<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Notesreminders extends CI_Controller
{
    public $viewFolder = "";
    public $userData  = false;

    public function __construct()
    {
        parent::__construct();
        $this->viewFolder = "notesreminders_v";
        $this->load->model("notes_model");
        $this->load->model("auth_model");
        $this->load->helper("notes");
        $this->userData = $this->auth_model->userData;
    }

    private function getUserId()
    {
        return isset($this->userData->userB->u_id) ? (int) $this->userData->userB->u_id : 0;
    }

    private function checkAuth()
    {
        if ($this->userData === false) {
            redirect(sys_url("login"));
            exit;
        }
        if (!isAllowedViewApp("edts")) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden EDTS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(sys_url("home"));
            exit;
        }
    }

    public function index()
    {
        $this->checkAuth();

        $userId = $this->getUserId();
        $viewData = new stdClass();
        $viewData->viewFolder    = $this->viewFolder;
        $viewData->subViewFolder = "list";
        $viewData->userData      = $this->userData;
        $viewData->notes         = $this->notes_model->getAllForUser($userId);
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function save()
    {
        $this->checkAuth();
        if (!isAllowedViewApp("edts")) {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(array('success' => false, 'message' => 'Yetkiniz yok.'));
                return;
            }
            $this->session->set_flashdata("alertToastr", array("title" => "Hata!", "text" => "Yazma yetkiniz yok.", "type" => "error"));
            redirect(base_url('notesreminders'));
            return;
        }

        $userId = $this->getUserId();
        if (!$userId) {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(array('success' => false, 'message' => 'Oturum hatası.'));
                return;
            }
            redirect(base_url('notesreminders'));
            return;
        }

        $title       = $this->input->post('n_title');
        $content     = $this->input->post('n_content');
        $reminder_at = $this->input->post('n_reminder_at');
        $tag         = $this->input->post('n_tag');

        if (trim($title) === '') {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(array('success' => false, 'message' => 'Başlık zorunludur.'));
                return;
            }
            $this->session->set_flashdata("alertToastr", array("title" => "Hata!", "text" => "Başlık zorunludur.", "type" => "error"));
            redirect(base_url('notesreminders'));
            return;
        }

        $data = array(
            'n_user_id'     => $userId,
            'n_app'         => 'edts',
            'n_title'       => trim($title),
            'n_content'     => trim($content),
            'n_tag'         => ($tag && trim($tag) !== '') ? trim($tag) : null,
            'n_reminder_at' => ($reminder_at !== null && $reminder_at !== '') ? $reminder_at : null
        );

        $id = $this->notes_model->addNote($data);
        if ($id) {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(array('success' => true, 'id' => $id, 'message' => 'Not eklendi.'));
                return;
            }
            $this->session->set_flashdata("alertToastr", array("title" => "Başarılı", "text" => "Not eklendi.", "type" => "success"));
        } else {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(array('success' => false, 'message' => 'Kayıt eklenemedi.'));
                return;
            }
            $this->session->set_flashdata("alertToastr", array("title" => "Hata!", "text" => "Kayıt eklenemedi.", "type" => "error"));
        }
        redirect(base_url('notesreminders'));
    }

    public function update()
    {
        $this->checkAuth();
        if (!isAllowedViewApp("edts")) {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(array('success' => false, 'message' => 'Yetkiniz yok.'));
                return;
            }
            $this->session->set_flashdata("alertToastr", array("title" => "Hata!", "text" => "Yazma yetkiniz yok.", "type" => "error"));
            redirect(base_url('notesreminders'));
            return;
        }

        $userId = $this->getUserId();
        $id     = (int) $this->input->post('n_id');
        if (!$userId || !$id) {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(array('success' => false, 'message' => 'Geçersiz istek.'));
                return;
            }
            redirect(base_url('notesreminders'));
            return;
        }

        $note = $this->notes_model->getById($id, $userId);
        if (!$note) {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(array('success' => false, 'message' => 'Not bulunamadı.'));
                return;
            }
            $this->session->set_flashdata("alertToastr", array("title" => "Hata!", "text" => "Not bulunamadı.", "type" => "error"));
            redirect(base_url('notesreminders'));
            return;
        }

        $title       = $this->input->post('n_title');
        $content     = $this->input->post('n_content');
        $reminder_at = $this->input->post('n_reminder_at');
        $tag         = $this->input->post('n_tag');

        if (trim($title) === '') {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(array('success' => false, 'message' => 'Başlık zorunludur.'));
                return;
            }
            $this->session->set_flashdata("alertToastr", array("title" => "Hata!", "text" => "Başlık zorunludur.", "type" => "error"));
            redirect(base_url('notesreminders'));
            return;
        }

        $data = array(
            'n_title'       => trim($title),
            'n_content'     => trim($content),
            'n_tag'         => ($tag && trim($tag) !== '') ? trim($tag) : null,
            'n_reminder_at' => ($reminder_at !== null && $reminder_at !== '') ? $reminder_at : null
        );

        $ok = $this->notes_model->updateNote($id, $userId, $data);
        if ($ok) {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(array('success' => true, 'message' => 'Not güncellendi.'));
                return;
            }
            $this->session->set_flashdata("alertToastr", array("title" => "Başarılı", "text" => "Not güncellendi.", "type" => "success"));
        } else {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(array('success' => false, 'message' => 'Güncellenemedi.'));
                return;
            }
            $this->session->set_flashdata("alertToastr", array("title" => "Hata!", "text" => "Güncellenemedi.", "type" => "error"));
        }
        redirect(base_url('notesreminders'));
    }

    public function delete($id = 0)
    {
        $this->checkAuth();
        if (!isAllowedViewApp("edts")) {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(array('success' => false, 'message' => 'Yetkiniz yok.'));
                return;
            }
            $this->session->set_flashdata("alertToastr", array("title" => "Hata!", "text" => "Silme yetkiniz yok.", "type" => "error"));
            redirect(base_url('notesreminders'));
            return;
        }

        $userId = $this->getUserId();
        $id     = (int) $id;
        if (!$userId || !$id) {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(array('success' => false, 'message' => 'Geçersiz istek.'));
                return;
            }
            redirect(base_url('notesreminders'));
            return;
        }

        $ok = $this->notes_model->deleteNote($id, $userId);
        if ($ok) {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(array('success' => true, 'message' => 'Not silindi.'));
                return;
            }
            $this->session->set_flashdata("alertToastr", array("title" => "Başarılı", "text" => "Not silindi.", "type" => "success"));
        } else {
            if ($this->input->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode(array('success' => false, 'message' => 'Silinemedi.'));
                return;
            }
            $this->session->set_flashdata("alertToastr", array("title" => "Hata!", "text" => "Silinemedi.", "type" => "error"));
        }
        redirect(base_url('notesreminders'));
    }

    /**
     * Hatırlatma modalında "Tekrar Hatırlatma" tıklandığında - bir daha modal açılmasın
     */
    public function dismiss_reminder()
    {
        $this->checkAuth();
        if (!$this->input->post('n_id')) {
            header('Content-Type: application/json');
            echo json_encode(array('success' => false, 'message' => 'Geçersiz istek.'));
            return;
        }
        $userId = $this->getUserId();
        $id     = (int) $this->input->post('n_id');
        if (!$userId || !$id) {
            header('Content-Type: application/json');
            echo json_encode(array('success' => false, 'message' => 'Geçersiz istek.'));
            return;
        }
        $note = $this->notes_model->getById($id, $userId);
        if (!$note) {
            header('Content-Type: application/json');
            echo json_encode(array('success' => false, 'message' => 'Not bulunamadı.'));
            return;
        }
        $ok = $this->notes_model->dismissReminder($id, $userId);
        header('Content-Type: application/json');
        echo json_encode(array('success' => (bool) $ok));
    }
}
