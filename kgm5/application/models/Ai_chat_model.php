<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ai_chat_model extends RT_Model
{
    private $sessionsTable = 'r8t_edts_ai_sessions';
    private $messagesTable = 'r8t_edts_ai_messages';

    public function __construct()
    {
        parent::__construct();
        $this->tableName = $this->sessionsTable;
    }

    /**
     * Yeni sohbet oturumu oluşturur.
     *
     * @param int    $userId
     * @param string $title
     * @return int|false session_id veya false
     */
    public function createSession($userId, $title = 'Yeni sohbet')
    {
        $now = time();
        $data = array(
            'user_id'    => (int) $userId,
            'title'      => $title,
            'created_at' => $now,
            'updated_at' => $now,
        );
        if ($this->db->insert($this->sessionsTable, $data)) {
            return (int) $this->db->insert_id();
        }
        return false;
    }

    /**
     * Kullanıcının oturumlarını son güncelleme tarihine göre listeler.
     *
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getSessionsForUser($userId, $limit = 50)
    {
        $this->db->select('session_id, title, created_at, updated_at');
        $this->db->from($this->sessionsTable);
        $this->db->where('user_id', (int) $userId);
        $this->db->order_by('updated_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Oturumun kullanıcıya ait olup olmadığını kontrol eder.
     *
     * @param int $sessionId
     * @param int $userId
     * @return bool
     */
    public function isSessionOwnedByUser($sessionId, $userId)
    {
        $row = $this->db->select('session_id')
            ->from($this->sessionsTable)
            ->where('session_id', (int) $sessionId)
            ->where('user_id', (int) $userId)
            ->get()
            ->row();
        return $row !== null;
    }

    /**
     * Oturumun mesajlarını kronolojik sırada döndürür.
     *
     * @param int $sessionId
     * @param int|null $limit  Son N mesaj (null = tümü)
     * @param int|null $offset Bağlam için son mesajlardan önce atlanacak sayı
     * @return array
     */
    public function getMessagesForSession($sessionId, $limit = null, $offset = null)
    {
        $this->db->select('id, session_id, role, content, token_estimate, created_at');
        $this->db->from($this->messagesTable);
        $this->db->where('session_id', (int) $sessionId);
        $this->db->order_by('created_at', 'ASC');
        if ($limit !== null) {
            if ($offset !== null) {
                $this->db->limit($limit, $offset);
            } else {
                $this->db->limit($limit);
            }
        }
        return $this->db->get()->result();
    }

    /**
     * Son N mesajı getirir (token tasarrufu için bağlam penceresi).
     *
     * @param int $sessionId
     * @param int $lastN
     * @return array
     */
    public function getLastMessagesForContext($sessionId, $lastN = 20)
    {
        $all = $this->getMessagesForSession($sessionId, null, null);
        if (count($all) <= $lastN) {
            return $all;
        }
        return array_slice($all, -$lastN);
    }

    /**
     * Mesaj ekler.
     *
     * @param int    $sessionId
     * @param string $role  'user' | 'model'
     * @param string $content
     * @param int|null $tokenEstimate
     * @return int|false message id veya false
     */
    public function addMessage($sessionId, $role, $content, $tokenEstimate = null)
    {
        $data = array(
            'session_id'     => (int) $sessionId,
            'role'           => $role === 'model' ? 'model' : 'user',
            'content'        => $content,
            'token_estimate' => $tokenEstimate,
            'created_at'     => time(),
        );
        if ($this->db->insert($this->messagesTable, $data)) {
            $this->touchSession($sessionId);
            return (int) $this->db->insert_id();
        }
        return false;
    }

    /**
     * Oturumun updated_at alanını günceller.
     *
     * @param int $sessionId
     * @return bool
     */
    public function touchSession($sessionId)
    {
        return $this->db->where('session_id', (int) $sessionId)
            ->update($this->sessionsTable, array('updated_at' => time()));
    }

    /**
     * Oturum başlığını günceller.
     *
     * @param int    $sessionId
     * @param string $title
     * @return bool
     */
    public function updateSessionTitle($sessionId, $title)
    {
        return $this->db->where('session_id', (int) $sessionId)
            ->update($this->sessionsTable, array('title' => $title, 'updated_at' => time()));
    }
}
