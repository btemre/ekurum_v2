<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{

	public $viewFolder 	= "";
	public $userData		= false;

	public function __construct()
	{
		parent::__construct();

		$this->viewFolder = "dashboard_v";
		$this->load->model("durusmalar_model");
        $this->load->helper("durusmalar");

		$this->load->model("auth_model");
		$this->userData = $this->auth_model->userData;
	}

	public function index()
	{
		// Sayfa önbelleğini kısıtla; kullanıcı güncel HTML alsın (hard refresh gereksin)
		$this->output->set_header('Cache-Control: no-cache, must-revalidate, max-age=0');
		$this->output->set_header('Pragma: no-cache');

		if ($this->userData === false) {
			redirect(sys_url("login"));
			exit;
		}
		//Kullanıcının Birimine Yada Kendi Şahsına Atanmış Uygulamayı Görüntüleme Yetkisi Varmı Kontrolü
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

		if (!isDbAllowedViewModule()) {
			$alert = array(
				"title" => "Hata!",
				"text"  => "CEKAS | Dashboard Modülünü Görüntüleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.",
				"type"  => "error"
			);
			$this->session->set_flashdata("alertToastr", $alert);
			redirect(sys_url("home"));
			exit;
		}
		if ($this->uri->segment(1)=="") {
			header("location:/apps/edts/dashboard");
			exit;
		}
		$durusmaavukatbazli = $this->durusmalar_model->durusmaAvukatBazli();
		$durusmalistesihaftalik = $this->durusmalar_model->durusmaListesiHaftalik();



		$viewData = new stdClass();
		$viewData->viewFolder     = $this->viewFolder;
		$viewData->subViewFolder	= "view";
		$viewData->userData 			= $this->userData;

		$viewData->durusmaavukatbazli = $durusmaavukatbazli;
		$viewData->durusmalistesihaftalik = $durusmalistesihaftalik;
		$this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
	}
}
