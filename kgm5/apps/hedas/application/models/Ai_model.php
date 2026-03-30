<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ai_model extends RT_Model
{
    private $logTable;
    private $rateLimitPerUser;
    private $rateLimitPerTenant;
    private $logEnabled;

    public function __construct()
    {
        parent::__construct();
        $this->config->load('ai_config', TRUE);

        $this->logTable           = $this->config->item('ai_log_table', 'ai_config');
        $this->tableName          = $this->logTable;
        $this->rateLimitPerUser   = $this->config->item('ai_rate_limit_per_user', 'ai_config');
        $this->rateLimitPerTenant = $this->config->item('ai_rate_limit_per_tenant', 'ai_config');
        $this->logEnabled         = $this->config->item('ai_log_enabled', 'ai_config');
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
     * Kullanıcının bugünkü istek sayısını kontrol eder.
     *
     * @param int $userId
     * @return bool  Limit aşıldıysa TRUE
     */
    public function isUserRateLimited($userId)
    {
        $todayStart = strtotime('today midnight');

        $count = $this->db
            ->where('al_user_id', $userId)
            ->where('al_adddate >=', $todayStart)
            ->where('al_status', 'success')
            ->count_all_results($this->logTable);

        return ($count >= $this->rateLimitPerUser);
    }

    /**
     * Kullanıcının bugünkü kalan istek sayısını döndürür.
     *
     * @param int $userId
     * @return int
     */
    public function getUserRemainingQuota($userId)
    {
        $todayStart = strtotime('today midnight');

        $count = $this->db
            ->where('al_user_id', $userId)
            ->where('al_adddate >=', $todayStart)
            ->where('al_status', 'success')
            ->count_all_results($this->logTable);

        $remaining = $this->rateLimitPerUser - $count;
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
