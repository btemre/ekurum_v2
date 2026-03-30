<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{

	public $viewFolder 	= "";
	public $userData		= false;

	public function __construct()
	{
		parent::__construct();

		$this->viewFolder = "home_v";
		$this->load->model("auth_model");
		$this->userData = $this->auth_model->userData;
	}

	public function index()
	{

		if ($this->userData === false) {
			redirect(base_url("login"));
			exit;
		}

		if (!isDbAllowedViewModule()) {
			redirect(base_url("dashboard"));
			exit;
		}

		$this->load->model("units_model");
		$this->load->model("duyurular_model");

		if (isDbAllowedListModule()) {
			$appList = $this->units_model->ek_get_all(
				"r8t_sys_apps",
				array(
					"a_status"			=> 1
				)
			);

			$duyuruList = $this->duyurular_model->get_all(
				array(
					"us_status !="       => -1
				),
				"us_adddate DESC"
			);

		} else {
			$appList = array();
			$duyuruList = array();
		}

		$detail=$this->input->get("detail");
		if ($detail>0) {

			$duyuruArr = $this->duyurular_model->get_all(
				array(
					"us_id"       => $detail
				)
				
			);
			if (!empty($duyuruArr[0]))
				$duyuruArr=(array)$duyuruArr[0];
			else $duyuruArr=array();

					
			$baslik=$duyuruArr["us_name"];
			$detailTxt=$duyuruArr["us_description"];
			$alert = array(
				"title" => "$baslik",
				"text"  => "$detailTxt",
				"type"  => "success"
			);
			$this->session->set_flashdata("alertToastr", $alert);
		}

		$viewData = new stdClass();
		$viewData->viewFolder     = $this->viewFolder;
		$viewData->subViewFolder	= "view";
		$viewData->userData 			= $this->userData;
		$viewData->appList 				= $appList;
		$viewData->duyuruList 				= $duyuruList;

		$this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
	}

	/**
	 * Header bildirimler dropdown için JSON API.
	 * Döner: total, alerts (uyarı/hatırlatma), updates (duyuru), logs (işlem logu).
	 */
	public function api_header_notifications()
	{
		header('Content-Type: application/json; charset=utf-8');
		$out = array(
			'total'   => 0,
			'alerts'  => array(),
			'updates' => array(),
			'logs'    => array(),
			'license' => null
		);
		if ($this->userData === false) {
			echo json_encode($out);
			return;
		}
		if (function_exists('isAllowedViewApp') && !isAllowedViewApp('edts')) {
			echo json_encode($out);
			return;
		}
		$userId = isset($this->userData->userB->u_id) ? (int) $this->userData->userB->u_id : 0;
		if ($userId <= 0) {
			echo json_encode($out);
			return;
		}

		// Uyarı: vadesi gelen hatırlatmalar (notes)
		$this->load->model('notes_model');
		$dueReminders = $this->notes_model->getDueReminders($userId, 'edts', 10);
		foreach ($dueReminders as $r) {
			$title = isset($r->n_title) && trim($r->n_title) !== '' ? $r->n_title : 'Hatırlatma';
			$desc = isset($r->n_content) ? mb_substr(strip_tags($r->n_content), 0, 80) : '';
			if (mb_strlen($desc) > 80) {
				$desc .= '...';
			}
			$date = isset($r->n_reminder_at) ? $r->n_reminder_at : (isset($r->n_created_at) ? $r->n_created_at : '');
			$out['alerts'][] = array(
				'id'          => (int) $r->n_id,
				'title'       => $title,
				'description' => $desc,
				'date'        => $date,
				'time_ago'    => $this->_time_ago($date)
			);
		}

		// Güncelleme: son duyurular
		$this->load->model('duyurular_model');
		$this->db->from($this->duyurular_model->tableName);
		$this->db->where('us_status !=', -1);
		$this->db->order_by('us_adddate', 'DESC');
		$this->db->limit(10);
		$duyurular = $this->db->get()->result();
		foreach ($duyurular as $d) {
			$title = isset($d->us_name) ? $d->us_name : '';
			$desc = isset($d->us_description) ? mb_substr(strip_tags($d->us_description), 0, 100) : '';
			if (mb_strlen($desc) > 100) {
				$desc .= '...';
			}
			$out['updates'][] = array(
				'id'          => (int) $d->us_id,
				'title'       => $title,
				'description' => $desc,
				'date'        => isset($d->us_adddate) ? $d->us_adddate : ''
			);
		}

		// Log: kullanıcının son işlem logları
		$this->db->from('r8t_sys_userlogs');
		$this->db->where('ul_userid', $userId);
		$this->db->order_by('ul_adddate', 'DESC');
		$this->db->limit(10);
		$logs = $this->db->get()->result();
		$turLabels = array(0 => 'Ekleme', 1 => 'Silme', 2 => 'Güncelleme', 3 => 'Taşıma');
		foreach ($logs as $l) {
			$tur = isset($l->ul_tur) ? (int) $l->ul_tur : 0;
			$out['logs'][] = array(
				'id'        => (int) $l->ul_id,
				'aciklama'  => isset($l->ul_aciklama) ? $l->ul_aciklama : '',
				'tur'       => $tur,
				'tur_label' => isset($turLabels[$tur]) ? $turLabels[$tur] : 'İşlem',
				'date'      => isset($l->ul_adddate) ? $l->ul_adddate : ''
			);
		}

		// Lisans: giriş yapan kullanıcının birimine ait abonelik süre bilgisi (u_unit veya join'den gelen ub_id)
		$u_unit = isset($this->userData->userB->u_unit) ? (int) $this->userData->userB->u_unit : (isset($this->userData->userB->ub_id) ? (int) $this->userData->userB->ub_id : 0);
		if ($u_unit > 0) {
			$row = $this->db->get_where('r8t_sys_unitlist', array('ub_id' => $u_unit))->row();
			if ($row) {
				$endDate = null;
				if (!empty($row->is_demo) && !empty($row->demo_end_date)) {
					$endDate = $row->demo_end_date;
				} elseif (!empty($row->subscription_end_date)) {
					$endDate = $row->subscription_end_date;
				}
				$daysRemaining = null;
				if ($endDate) {
					$today = new \DateTime(date('Y-m-d'));
					$end = new \DateTime($endDate);
					$diff = $today->diff($end);
					$daysRemaining = (int) $diff->format('%r%a');
				}
				$out['license'] = array(
					'unit_title'              => isset($row->ub_title) ? $row->ub_title : '',
					'subscription_start_date' => isset($row->subscription_start_date) ? $row->subscription_start_date : null,
					'subscription_end_date'   => isset($row->subscription_end_date) ? $row->subscription_end_date : null,
					'demo_end_date'           => isset($row->demo_end_date) ? $row->demo_end_date : null,
					'subscription_period'     => isset($row->subscription_period) ? $row->subscription_period : null,
					'subscription_status'     => isset($row->subscription_status) ? $row->subscription_status : null,
					'is_demo'                 => !empty($row->is_demo),
					'days_remaining'          => $daysRemaining
				);
			}
		}

		$out['total'] = count($out['alerts']) + count($out['updates']) + count($out['logs']);
		echo json_encode($out);
	}

	private function _time_ago($datetime)
	{
		if (empty($datetime)) {
			return '';
		}
		$ts = is_numeric($datetime) ? $datetime : strtotime($datetime);
		if ($ts === false) {
			return $datetime;
		}
		$diff = time() - $ts;
		if ($diff < 60) {
			return 'Az önce';
		}
		if ($diff < 3600) {
			$m = floor($diff / 60);
			return $m . ' dakika';
		}
		if ($diff < 86400) {
			$h = floor($diff / 3600);
			return $h . ' saat';
		}
		if ($diff < 604800) {
			$d = floor($diff / 86400);
			return $d . ' gün';
		}
		return date('d.m.Y', $ts);
	}
}
