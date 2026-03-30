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
}
