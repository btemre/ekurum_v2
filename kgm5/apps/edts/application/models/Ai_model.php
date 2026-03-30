<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ai_model extends RT_Model
{
    private $logTable;
    private $quotaTable;
    private $rateLimitPerUser;
    private $rateLimitPerTenant;
    private $logEnabled;

    public function __construct()
    {
        parent::__construct();
        $this->config->load('ai_config', TRUE);

        $this->logTable           = $this->config->item('ai_log_table', 'ai_config');
        $this->tableName          = $this->logTable;
        $this->quotaTable         = $this->config->item('ai_user_quota_table', 'ai_config');
        $this->rateLimitPerUser   = (int) $this->config->item('ai_rate_limit_per_user', 'ai_config');
        $this->rateLimitPerTenant = (int) $this->config->item('ai_rate_limit_per_tenant', 'ai_config');
        $this->logEnabled         = $this->config->item('ai_log_enabled', 'ai_config');
        if ($this->rateLimitPerUser < 1) {
            $this->rateLimitPerUser = 100;
        }
    }

    /**
     * Kullanıcının günlük kota limitini döndürür.
     * r8t_edts_ai_user_quota tablosunda kayıt varsa o değer, yoksa config'deki ai_rate_limit_per_user kullanılır.
     *
     * @param int $userId
     * @return int
     */
    public function getDailyQuotaForUser($userId)
    {
        $userId = (int) $userId;
        if ($userId <= 0) {
            return $this->rateLimitPerUser;
        }
        $table = trim((string) $this->quotaTable);
        if ($table === '') {
            $table = 'r8t_edts_ai_user_quota';
        }
        try {
            $row = $this->db->select('daily_quota')->where('user_id', $userId)->limit(1)->get($table)->row();
            if ($row && isset($row->daily_quota)) {
                $q = (int) $row->daily_quota;
                return $q > 0 ? $q : $this->rateLimitPerUser;
            }
        } catch (Exception $e) {
            if (function_exists('log_message')) {
                log_message('error', 'Ai_model getDailyQuotaForUser: ' . $e->getMessage());
            }
        }
        return $this->rateLimitPerUser;
    }

    /**
     * AI isteğini loglar.
     *
     * @param array $logData  Log verisi
     * @return bool
     */
    public function addLog($logData = array())
    {
        if (!$this->logEnabled) {
            return true;
        }

        $defaults = array(
            'al_user_id'      => 0,
            'al_app'          => '',
            'al_request_type' => '',
            'al_prompt_hash'  => '',
            'al_response_len' => 0,
            'al_status'       => 'success',
            'al_error_msg'    => '',
            'al_ip'           => $this->input->ip_address(),
            'al_adddate'      => time(),
        );

        $data = array_merge($defaults, $logData);

        return $this->db->insert($this->logTable, $data);
    }

    /**
     * Kota hesabına dahil edilmeyen istek türleri (tahmine dayalı uyarı vb.).
     */
    private function _quotaExcludedRequestTypes()
    {
        return array('capacity_forecast');
    }

    /**
     * Kullanıcının bugünkü istek sayısını kontrol eder.
     * AI Asistan tahmine dayalı uyarıları (capacity_forecast) kota hesabına dahil edilmez.
     *
     * @param int $userId
     * @return bool  Limit aşıldıysa TRUE
     */
    public function isUserRateLimited($userId)
    {
        $todayStart = strtotime('today midnight');

        $this->db
            ->where('al_user_id', $userId)
            ->where('al_adddate >=', $todayStart)
            ->where('al_status', 'success');
        $excluded = $this->_quotaExcludedRequestTypes();
        if (!empty($excluded)) {
            $this->db->where_not_in('al_request_type', $excluded);
        }
        $count = $this->db->count_all_results($this->logTable);
        $limit = $this->getDailyQuotaForUser($userId);

        return ($count >= $limit);
    }

    /**
     * Kullanıcının bugünkü kalan istek sayısını döndürür.
     * AI Asistan tahmine dayalı uyarıları (capacity_forecast) kota hesabına dahil edilmez.
     *
     * @param int $userId
     * @return int
     */
    public function getUserRemainingQuota($userId)
    {
        $todayStart = strtotime('today midnight');

        $this->db
            ->where('al_user_id', $userId)
            ->where('al_adddate >=', $todayStart)
            ->where('al_status', 'success');
        $excluded = $this->_quotaExcludedRequestTypes();
        if (!empty($excluded)) {
            $this->db->where_not_in('al_request_type', $excluded);
        }
        $count = $this->db->count_all_results($this->logTable);
        $limit = $this->getDailyQuotaForUser($userId);
        $remaining = $limit - $count;

        return ($remaining > 0) ? $remaining : 0;
    }

    /**
     * Kullanıcının AI kullanım istatistiklerini döndürür.
     *
     * @param int    $userId
     * @param string $period  'today', 'week', 'month'
     * @return object
     */
    public function getUserStats($userId, $period = 'today')
    {
        switch ($period) {
            case 'week':
                $startTime = strtotime('monday this week midnight');
                break;
            case 'month':
                $startTime = strtotime('first day of this month midnight');
                break;
            default:
                $startTime = strtotime('today midnight');
                break;
        }

        $this->db->select('COUNT(*) as total_requests, SUM(al_response_len) as total_response_len');
        $this->db->where('al_user_id', $userId);
        $this->db->where('al_adddate >=', $startTime);
        $this->db->where('al_status', 'success');
        $result = $this->db->get($this->logTable)->row();

        if (!$result) {
            $result = new stdClass();
            $result->total_requests = 0;
            $result->total_response_len = 0;
        }

        return $result;
    }

    /**
     * Belirli türdeki AI loglarını listeler.
     *
     * @param array $filters  Filtre parametreleri
     * @param int   $limit
     * @param int   $offset
     * @return array
     */
    public function getLogs($filters = array(), $limit = 50, $offset = 0)
    {
        if (!empty($filters['user_id'])) {
            $this->db->where('al_user_id', $filters['user_id']);
        }
        if (!empty($filters['app'])) {
            $this->db->where('al_app', $filters['app']);
        }
        if (!empty($filters['request_type'])) {
            $this->db->where('al_request_type', $filters['request_type']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('al_status', $filters['status']);
        }
        if (!empty($filters['date_start'])) {
            $this->db->where('al_adddate >=', $filters['date_start']);
        }
        if (!empty($filters['date_end'])) {
            $this->db->where('al_adddate <=', $filters['date_end']);
        }

        $this->db->order_by('al_adddate', 'DESC');
        $this->db->limit($limit, $offset);

        return $this->db->get($this->logTable)->result();
    }

    /**
     * AI log sayısını döndürür (sayfalama için).
     *
     * @param array $filters
     * @return int
     */
    public function getLogsCount($filters = array())
    {
        if (!empty($filters['user_id'])) {
            $this->db->where('al_user_id', $filters['user_id']);
        }
        if (!empty($filters['app'])) {
            $this->db->where('al_app', $filters['app']);
        }
        if (!empty($filters['request_type'])) {
            $this->db->where('al_request_type', $filters['request_type']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('al_status', $filters['status']);
        }

        return $this->db->count_all_results($this->logTable);
    }
}
