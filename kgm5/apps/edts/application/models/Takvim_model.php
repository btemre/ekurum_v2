<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Takvim_model extends RT_Model
{
    protected $prefsTable   = 'r8t_edts_calendar_notification_prefs';
    protected $feedTable   = 'r8t_edts_calendar_feed_tokens';
    protected $logTable    = 'r8t_edts_calendar_notification_log';
    protected $pushTable   = 'r8t_edts_push_subscriptions';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Kullanıcının bildirim tercihlerini getirir.
     */
    public function getNotificationPrefs($userId)
    {
        $userId = (int) $userId;
        if ($userId <= 0) {
            return null;
        }
        $row = $this->ek_query("SELECT * FROM " . $this->prefsTable . " WHERE user_id = " . $userId);
        return $row;
    }

    /**
     * Bildirim tercihlerini kaydeder (insert veya update).
     */
    public function saveNotificationPrefs($userId, $data)
    {
        $userId = (int) $userId;
        if ($userId <= 0) {
            return false;
        }
        $now = date('Y-m-d H:i:s');
        $allowed = array('email_enabled', 'push_enabled', 'reminder_minutes', 'quiet_hours_start', 'quiet_hours_end');
        $set = array();
        foreach ($allowed as $k) {
            if (array_key_exists($k, $data)) {
                if ($k === 'email_enabled' || $k === 'push_enabled') {
                    $set[$k] = (int) $data[$k] ? 1 : 0;
                } elseif ($k === 'reminder_minutes') {
                    $set[$k] = (int) $data[$k];
                    if ($set[$k] < 0) {
                        $set[$k] = 1440;
                    }
                } else {
                    $set[$k] = $data[$k];
                }
            }
        }
        $existing = $this->getNotificationPrefs($userId);
        if ($existing) {
            $set['updated_at'] = $now;
            return $this->db->where('user_id', $userId)->update($this->prefsTable, $set);
        }
        $set['user_id']    = $userId;
        $set['created_at'] = $now;
        $set['updated_at'] = $now;
        if (!isset($set['email_enabled'])) {
            $set['email_enabled'] = 1;
        }
        if (!isset($set['push_enabled'])) {
            $set['push_enabled'] = 0;
        }
        if (!isset($set['reminder_minutes'])) {
            $set['reminder_minutes'] = 1440;
        }
        return $this->db->insert($this->prefsTable, $set);
    }

    /**
     * Kullanıcı için feed token döndürür; yoksa oluşturur.
     */
    public function getOrCreateFeedToken($userId)
    {
        $userId = (int) $userId;
        if ($userId <= 0) {
            return null;
        }
        $row = $this->ek_query("SELECT token FROM " . $this->feedTable . " WHERE user_id = " . $userId);
        if ($row && !empty($row->token)) {
            return $row->token;
        }
        $token = bin2hex(random_bytes(32));
        $now   = date('Y-m-d H:i:s');
        $this->db->insert($this->feedTable, array(
            'user_id'    => $userId,
            'token'     => $token,
            'created_at' => $now,
        ));
        return $token;
    }

    /**
     * Token ile user_id bulur.
     */
    public function getUserIdByFeedToken($token)
    {
        if (empty($token) || strlen($token) !== 64) {
            return null;
        }
        $row = $this->ek_query("SELECT user_id FROM " . $this->feedTable . " WHERE token = " . $this->db->escape($token));
        return $row ? (int) $row->user_id : null;
    }

    /**
     * Belirli reminder_minutes değerine sahip ve email veya push açık kullanıcıları getirir.
     * @param int $reminderMinutes
     * @return array [{ user_id, email, email_enabled, push_enabled }, ...]
     */
    public function getUsersWithReminderPrefs($reminderMinutes)
    {
        $reminderMinutes = (int) $reminderMinutes;
        $sql = "SELECT p.user_id, p.email_enabled, p.push_enabled,
                u.u_email AS email
                FROM " . $this->prefsTable . " p
                INNER JOIN r8t_users u ON u.u_id = p.user_id
                WHERE p.reminder_minutes = " . $reminderMinutes . "
                AND (p.email_enabled = 1 OR p.push_enabled = 1)
                AND u.u_status = 1";
        $rows = $this->ek_query_all($sql);
        return $rows ? $rows : array();
    }

    /**
     * Bu (user_id, d_id, channel) daha önce gönderilmiş mi?
     */
    public function alreadySent($userId, $dId, $channel)
    {
        $n = $this->ek_query("SELECT id FROM " . $this->logTable . "
            WHERE user_id = " . (int) $userId . " AND d_id = " . (int) $dId . " AND channel = " . $this->db->escape($channel) . " LIMIT 1");
        return $n && !empty($n->id);
    }

    /**
     * Bildirim gönderildi olarak logla.
     */
    public function logNotification($userId, $dId, $channel)
    {
        return $this->db->insert($this->logTable, array(
            'user_id' => (int) $userId,
            'd_id'    => (int) $dId,
            'channel' => $channel,
            'sent_at' => date('Y-m-d H:i:s'),
        ));
    }

    /**
     * Kullanıcıların push aboneliklerini getirir.
     */
    public function getPushSubscriptionsByUserIds($userIds)
    {
        if (empty($userIds)) {
            return array();
        }
        $ids = array_map('intval', (array) $userIds);
        $ids = array_filter($ids);
        if (empty($ids)) {
            return array();
        }
        $sql = "SELECT user_id, endpoint, p256dh, auth FROM " . $this->pushTable . " WHERE user_id IN (" . implode(',', $ids) . ")";
        $rows = $this->ek_query_all($sql);
        return $rows ? $rows : array();
    }

    /**
     * Push aboneliği kaydet (endpoint, p256dh, auth).
     */
    public function savePushSubscription($userId, $endpoint, $p256dh, $auth, $userAgent = '')
    {
        $userId = (int) $userId;
        if ($userId <= 0 || empty($endpoint)) {
            return false;
        }
        $existing = $this->ek_query("SELECT id FROM " . $this->pushTable . " WHERE endpoint = " . $this->db->escape(substr($endpoint, 0, 255)) . " LIMIT 1");
        $data = array(
            'user_id'    => $userId,
            'endpoint'   => $endpoint,
            'p256dh'     => $p256dh,
            'auth'       => $auth,
            'user_agent' => $userAgent,
            'created_at' => date('Y-m-d H:i:s'),
        );
        if ($existing && !empty($existing->id)) {
            return $this->db->where('endpoint', $data['endpoint'])->update($this->pushTable, array(
                'user_id' => $userId,
                'p256dh'  => $data['p256dh'],
                'auth'    => $data['auth'],
                'user_agent' => $data['user_agent'],
            ));
        }
        return $this->db->insert($this->pushTable, $data);
    }
}
