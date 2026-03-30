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
		$this->load->model("auth_model");
		$this->load->model("cezaiptal_model");
		$this->load->model("dosya_model");
		$this->load->model("gelengiden_model");
		$this->userData = $this->auth_model->userData;
	}

	public function index()
	{
		if ($this->userData === false) {
			redirect(sys_url("login"));
			exit;
		}
		//Kullanıcının Birimine Yada Kendi Şahsına Atanmış Uygulamayı Görüntüleme Yetkisi Varmı Kontrolü
		if (!isAllowedViewApp("hedas")) {
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
				"text"  => "HEDAS | Dashboard Modülünü Görüntüleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.",
				"type"  => "error"
			);
			$this->session->set_flashdata("alertToastr", $alert);
			redirect(sys_url("home"));
			exit;
		}


		$dosyanootomatik = $this->cezaiptal_model->dosyaNoOtomatik();
		$dosyatoplam = $this->dosya_model->dosyaToplam();
		$gelengidentoplam = $this->gelengiden_model->gelenGidenToplam();
		$cezaiptaltoplam = $this->cezaiptal_model->cezaIptalToplam();

		$dosyasonkayit = $this->dosya_model->dosyaSonKayit();
		$ggsonkayit = $this->gelengiden_model->ggSonKayit();
		$cezasonkayit = $this->cezaiptal_model->cezaSonKayit();

		$viewData = new stdClass();
		$viewData->viewFolder     = $this->viewFolder;
		$viewData->subViewFolder	= "view";
		$viewData->userData 			= $this->userData;

		$viewData->dosyanootomatik = $dosyanootomatik;
		$viewData->dosyatoplam = $dosyatoplam;
		$viewData->gelengidentoplam = $gelengidentoplam;
		$viewData->cezaiptaltoplam = $cezaiptaltoplam;

		$viewData->dosyasonkayit = $dosyasonkayit;
		$viewData->ggsonkayit = $ggsonkayit;
		$viewData->cezasonkayit = $cezasonkayit;
		$this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
	}
}
