<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Notes_model extends RT_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->tableName = 'r8t_edts_notes';
    }

    /**
     * Kullanıcının tüm notları (liste sayfası için)
     */
    public function getAllForUser($userId, $app = 'edts')
    {
        $where = array(
            'n_user_id' => (int) $userId,
            'n_app'     => $app
        );
        return $this->ek_get_all($this->tableName, $where, 'n_updated_at DESC, n_created_at DESC');
    }

    /**
     * Dashboard widget için: bugün/gelecek hatırlatmalar + son 1 not
     */
    public function getForDashboard($userId, $app = 'edts', $reminderLimit = 5)
    {
        $todayStart = date('Y-m-d 00:00:00');
        $remindersSql = "SELECT * FROM " . $this->tableName . "
            WHERE n_user_id = " . (int) $userId . "
            AND n_app = " . $this->db->escape($app) . "
            AND n_reminder_at IS NOT NULL
            AND n_reminder_at >= " . $this->db->escape($todayStart) . "
            ORDER BY n_reminder_at ASC
            LIMIT " . (int) $reminderLimit;
        $reminders = $this->ek_query_all($remindersSql);

        $lastNoteSql = "SELECT * FROM " . $this->tableName . "
            WHERE n_user_id = " . (int) $userId . "
            AND n_app = " . $this->db->escape($app) . "
            ORDER BY COALESCE(n_updated_at, n_created_at) DESC
            LIMIT 1";
        $lastNote = $this->ek_query($lastNoteSql);

        return array(
            'reminders' => $reminders ? $reminders : array(),
            'last_note' => $lastNote
        );
    }

    /**
     * Vadesi geçmiş ve henüz "Tekrar Hatırlatma" ile kapatılmamış hatırlatmalar
     * (Modal göstermek için)
     */
    public function getDueReminders($userId, $app = 'edts', $limit = 10)
    {
        try {
            $now = date('Y-m-d H:i:s');
            $sql = "SELECT * FROM " . $this->tableName . "
                WHERE n_user_id = " . (int) $userId . "
                AND n_app = " . $this->db->escape($app) . "
                AND n_reminder_at IS NOT NULL
                AND n_reminder_at <= " . $this->db->escape($now) . "
                AND n_reminder_dismissed_at IS NULL
                ORDER BY n_reminder_at ASC
                LIMIT " . (int) $limit;
            $result = $this->db->query($sql);
            if ($result === false) {
                return array();
            }
            return $result->result() ?: array();
        } catch (Throwable $e) {
            return array();
        }
    }

    /**
     * Hatırlatmayı "Tekrar Hatırlatma" ile kapat - bir daha modal açılmasın
     */
    public function dismissReminder($id, $userId, $app = 'edts')
    {
        return $this->ek_update($this->tableName, array(
            'n_id'      => (int) $id,
            'n_user_id' => (int) $userId,
            'n_app'     => $app
        ), array('n_reminder_dismissed_at' => date('Y-m-d H:i:s')));
    }

    /**
     * Tek not (yetki için user_id kontrolü)
     */
    public function getById($id, $userId, $app = 'edts')
    {
        return $this->ek_get($this->tableName, array(
            'n_id'      => (int) $id,
            'n_user_id' => (int) $userId,
            'n_app'     => $app
        ));
    }

    /**
     * Not ekle; eklenen kaydın id'sini döner
     */
    public function addNote($data)
    {
        $data['n_created_at'] = date('Y-m-d H:i:s');
        $data['n_updated_at'] = $data['n_created_at'];
        if (isset($data['n_reminder_at']) && $data['n_reminder_at'] === '') {
            $data['n_reminder_at'] = null;
        }
        return $this->ek_add_lastid($this->tableName, $data);
    }

    /**
     * Not güncelle
     */
    public function updateNote($id, $userId, $data, $app = 'edts')
    {
        $data['n_updated_at'] = date('Y-m-d H:i:s');
        if (array_key_exists('n_reminder_at', $data)) {
            if ($data['n_reminder_at'] === '') {
                $data['n_reminder_at'] = null;
            }
            $data['n_reminder_dismissed_at'] = null;
        }
        return $this->ek_update($this->tableName, array(
            'n_id'      => (int) $id,
            'n_user_id' => (int) $userId,
            'n_app'     => $app
        ), $data);
    }

    /**
     * Not sil
     */
    public function deleteNote($id, $userId, $app = 'edts')
    {
        return $this->ek_delete($this->tableName, array(
            'n_id'      => (int) $id,
            'n_user_id' => (int) $userId,
            'n_app'     => $app
        ));
    }
}
