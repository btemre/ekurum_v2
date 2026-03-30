<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Abonelik / lisans süre takibi – birim (unit) bazında.
 * r8t_sys_unitlist içindeki subscription_* alanları ile çalışır.
 */
class Subscription_model extends CI_Model {

    /** Erişim verilmemesi gereken durumlar */
    const STATUS_BLOCKED = array('expired', 'suspended', 'cancelled');

    public function __construct() {
        parent::__construct();
    }

    /**
     * Birim abonelik bilgisini getirir (subscription_* ve is_demo, demo_end_date).
     * @param int $ub_id Birim id (r8t_sys_unitlist.ub_id)
     * @return object|null
     */
    public function get_unit_subscription($ub_id) {
        if (empty($ub_id)) return null;
        $this->db->select('ub_id, subscription_start_date, subscription_end_date, subscription_period, subscription_status, is_demo, demo_end_date');
        $row = $this->db->get_where('r8t_sys_unitlist', array('ub_id' => (int)$ub_id))->row();
        return $row;
    }

    /**
     * Birim için erişim izni var mı? (Demo süresi veya ücretli abonelik geçerli mi?)
     * @param int $ub_id
     * @return bool true = erişim verilmeli, false = lisans/demo bitmiş veya askıda
     */
    public function unit_has_access($ub_id) {
        $sub = $this->get_unit_subscription($ub_id);
        if (!$sub) return false;

        // Sütunlar henüz yoksa (migration çalışmamış) erişim ver
        if (!isset($sub->subscription_status) && !isset($sub->subscription_end_date)) {
            return true;
        }

        $today = date('Y-m-d');

        // Demo birim: demo_end_date kontrolü
        if (!empty($sub->is_demo)) {
            if (!empty($sub->demo_end_date) && $sub->demo_end_date < $today) {
                return false;
            }
            return true;
        }

        // Ücretli: subscription_status ve subscription_end_date (null = sınırsız kabul)
        if ($sub->subscription_status !== null && $sub->subscription_status !== '' && in_array($sub->subscription_status, self::STATUS_BLOCKED)) {
            return false;
        }
        if (!empty($sub->subscription_end_date) && $sub->subscription_end_date < $today) {
            return false;
        }
        return true;
    }

    /**
     * Kalan gün sayısı (bitiş tarihine göre). Demo için demo_end_date, ücretli için subscription_end_date.
     * @param int $ub_id
     * @return int|null null = tarih yok, negatif = süre geçmiş
     */
    public function days_remaining($ub_id) {
        $sub = $this->get_unit_subscription($ub_id);
        if (!$sub) return null;

        $end_date = null;
        if (!empty($sub->is_demo) && !empty($sub->demo_end_date)) {
            $end_date = $sub->demo_end_date;
        } elseif (!empty($sub->subscription_end_date)) {
            $end_date = $sub->subscription_end_date;
        }
        if (!$end_date) return null;

        $today = new DateTime(date('Y-m-d'));
        $end = new DateTime($end_date);
        $diff = $today->diff($end);
        $days = (int) $diff->format('%r%a');
        return $days;
    }

    /**
     * Lisans yönetim listesi: tüm birimler (abonelik alanları + kalan gün).
     * @param array $where ek filtre (örn. ub_status != -1)
     * @param string $filter 'all' | 'expiring' | 'expired' | 'grace'
     * @return array
     */
    public function get_units_for_license_admin($where = array(), $filter = 'all') {
        $this->db->select('ub_id, ub_title, ub_status, subscription_start_date, subscription_end_date, subscription_period, subscription_status, is_demo, demo_end_date');
        $this->db->from('r8t_sys_unitlist');
        $this->db->where('ub_status !=', -1);
        foreach ($where as $k => $v) {
            $this->db->where($k, $v);
        }
        $rows = $this->db->get()->result();
        if (!$rows) return array();

        $today = date('Y-m-d');
        $in30 = date('Y-m-d', strtotime('+30 days'));

        if ($filter !== 'all') {
            $rows = array_filter($rows, function($row) use ($filter, $today, $in30) {
                $end = !empty($row->is_demo) ? $row->demo_end_date : $row->subscription_end_date;
                if ($filter === 'expiring') {
                    return $row->subscription_status === 'active' && $end && $end >= $today && $end <= $in30
                        || (!empty($row->is_demo) && $row->demo_end_date && $row->demo_end_date >= $today && $row->demo_end_date <= $in30);
                }
                if ($filter === 'expired') {
                    return $row->subscription_status === 'expired'
                        || ($end && $end < $today)
                        || (!empty($row->is_demo) && $row->demo_end_date && $row->demo_end_date < $today);
                }
                if ($filter === 'grace') {
                    return $row->subscription_status === 'grace';
                }
                return true;
            });
            $rows = array_values($rows);
        }

        foreach ($rows as $row) {
            $row->days_remaining = $this->days_remaining($row->ub_id);
        }
        return $rows;
    }

    /**
     * Basit liste: where ile filtre, sadece abonelik sütunları.
     */
    public function get_all_units_subscription($where = array()) {
        $default = array('ub_status !=' => -1);
        $where = array_merge($default, $where);
        return $this->db->select('ub_id, ub_title, subscription_start_date, subscription_end_date, subscription_period, subscription_status, is_demo, demo_end_date')
            ->where($where)
            ->get('r8t_sys_unitlist')
            ->result();
    }

    /**
     * Lisans yenile: bitiş tarihini bir dönem ileri al.
     * @param int $ub_id
     * @param string $period 'monthly' | 'yearly'
     * @param string|null $from_date başlangıç (null ise bugün veya mevcut bitiş)
     */
    public function renew_subscription($ub_id, $period = 'yearly', $from_date = null) {
        $sub = $this->get_unit_subscription($ub_id);
        if (!$sub) return false;
        $start = $from_date ?: ($sub->subscription_end_date && $sub->subscription_end_date >= date('Y-m-d') ? $sub->subscription_end_date : date('Y-m-d'));
        $end = $period === 'monthly'
            ? date('Y-m-d', strtotime($start . ' +1 month'))
            : date('Y-m-d', strtotime($start . ' +1 year'));
        return $this->db->where('ub_id', (int)$ub_id)->update('r8t_sys_unitlist', array(
            'subscription_start_date' => $start,
            'subscription_end_date'  => $end,
            'subscription_period'   => $period,
            'subscription_status'   => 'active',
        ));
    }

    /**
     * Grace uzat veya Askıya al / Askıdan kaldır.
     */
    public function set_subscription_status($ub_id, $status) {
        $allowed = array('active', 'expired', 'grace', 'suspended', 'cancelled');
        if (!in_array($status, $allowed)) return false;
        return $this->db->where('ub_id', (int)$ub_id)->update('r8t_sys_unitlist', array('subscription_status' => $status));
    }
}
