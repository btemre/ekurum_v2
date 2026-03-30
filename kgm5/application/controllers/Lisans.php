<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Lisans / Abonelik süre takibi – platform yönetim paneli.
 * Birim bazında subscription_start_date, subscription_end_date, subscription_status listesi ve aksiyonlar.
 */
class Lisans extends CI_Controller {

    public $viewFolder = "";
    public $userData = false;

    public function __construct() {
        parent::__construct();
        $this->viewFolder = "lisans_v";
        $this->load->model("auth_model");
        $this->load->model("subscription_model");
        $this->userData = $this->auth_model->userData;
    }

    public function index() {
        if ($this->userData === false) {
            redirect(base_url("login"));
            exit;
        }
        if (!isDbAllowedViewModule('lisans')) {
            redirect(base_url("dashboard"));
            exit;
        }

        $filter = $this->input->get('filter') ?: 'all';
        if (!in_array($filter, array('all', 'expiring', 'expired', 'grace'))) {
            $filter = 'all';
        }

        $itemList = $this->subscription_model->get_units_for_license_admin(array(), $filter);

        $viewData = new stdClass();
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "list";
        $viewData->userData = $this->userData;
        $viewData->itemList = $itemList;
        $viewData->currentFilter = $filter;
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    /**
     * Lisans yenile (AJAX veya form POST): bitiş tarihini bir dönem ileri al.
     */
    public function renew() {
        if ($this->userData === false || !isDbAllowedUpdateModule('lisans')) {
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode(array('success' => false, 'message' => 'Yetkisiz')));
                return;
            }
            redirect(base_url('lisans'));
            return;
        }
        $ub_id = (int) $this->input->post('ub_id');
        $period = $this->input->post('period') ?: 'yearly';
        if (!in_array($period, array('monthly', 'yearly'))) $period = 'yearly';
        if (!$ub_id) {
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode(array('success' => false, 'message' => 'Birim seçiniz')));
                return;
            }
            redirect(base_url('lisans'));
            return;
        }
        $ok = $this->subscription_model->renew_subscription($ub_id, $period);
        if ($this->input->is_ajax_request()) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('success' => (bool) $ok, 'message' => $ok ? 'Lisans yenilendi' : 'Güncelleme yapılamadı')));
            return;
        }
        $this->session->set_flashdata('alert', array('title' => $ok ? 'Başarılı' : 'Hata', 'text' => $ok ? 'Lisans yenilendi.' : 'Güncelleme yapılamadı.', 'type' => $ok ? 'success' : 'error'));
        redirect(base_url('lisans'));
    }

    /**
     * Abonelik durumu değiştir: grace, active, suspended, expired, cancelled.
     */
    public function set_status() {
        if ($this->userData === false || !isDbAllowedUpdateModule('lisans')) {
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode(array('success' => false, 'message' => 'Yetkisiz')));
                return;
            }
            redirect(base_url('lisans'));
            return;
        }
        $ub_id = (int) $this->input->post('ub_id');
        $status = $this->input->post('status') ?: '';
        if (!$ub_id || !$status) {
            if ($this->input->is_ajax_request()) {
                $this->output->set_content_type('application/json')->set_output(json_encode(array('success' => false, 'message' => 'Parametre eksik')));
                return;
            }
            redirect(base_url('lisans'));
            return;
        }
        $ok = $this->subscription_model->set_subscription_status($ub_id, $status);
        if ($this->input->is_ajax_request()) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array('success' => (bool) $ok, 'message' => $ok ? 'Durum güncellendi' : 'Güncelleme yapılamadı')));
            return;
        }
        $this->session->set_flashdata('alert', array('title' => $ok ? 'Başarılı' : 'Hata', 'text' => $ok ? 'Durum güncellendi.' : 'Güncelleme yapılamadı.', 'type' => $ok ? 'success' : 'error'));
        redirect(base_url('lisans'));
    }
}
