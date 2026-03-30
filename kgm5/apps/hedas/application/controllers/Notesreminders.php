<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Notesreminders extends CI_Controller
{

	public $viewFolder 	= "";
	public $userData		= false;

	public function __construct()
	{
		parent::__construct();
		$this->viewFolder = "notesreminders_v";
		$this->load->model("notesreminders_model");

		$this->load->model("auth_model");
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
				"text"  => "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden HEDAS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.",
				"type"  => "error"
			);
			$this->session->set_flashdata("alertToastr", $alert);
			redirect(sys_url("home"));
			exit;
		}

		if (!isDbAllowedViewModule()) {
			$alert = array(
				"title" => "Hata!",
				"text"  => "HEDAS | Not-Hatırlatma Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.",
				"type"  => "error"
			);
			$this->session->set_flashdata("alertToastr", $alert);
			redirect(sys_url("home"));
			exit;
		}

		if (isDbAllowedListModule()) {
			$items = $this->notesreminders_model->ek_get_all(
				"r8t_edys_notes_reminders",
				array(
					"nr_userid" => $this->userData->userB->u_id
				),
				"nr_id DESC"
			);
		} else {
			$items = array();
		}

		$viewData = new stdClass();
		$viewData->viewFolder     = $this->viewFolder;
		$viewData->subViewFolder	= "list";
		$viewData->userData 			= $this->userData;
		$viewData->items          = $items;
		$this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
	}

	public function save_api()
	{
		//POSTTAN GELEN JSON VERI ALMA
		$this->output->set_content_type("application/json");
		$this->output->set_header("Access-Control-Allow-Origin: *");
		$this->output->set_header("Access-Control-Allow-Methods: GET, OPTIONS");
		$this->output->set_header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");

		$this->JSON_DATA = json_decode(trim(file_get_contents("php://input")));
		//$this->JSON_DATA = json_encode(trim(file_get_contents("php://input")), true);
		//$this->JSON_DATA = json_decode($this->JSON_DATA);




		if ($this->userData === false) {
			$_sonuc =  new stdClass();
			$_sonuc->success			= false;
			$_sonuc->code 				= 203;
			$_sonuc->description		= "Kullanıcı Yetkilendirme Hatası. Lütfen Giriş Bilgilerinizi Kontrol Ediniz.";
			$sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
			SetHeader($_sonuc->code);
			echo $sonuc;
			exit;
		}
		//Kullanıcının Birimine Yada Kendi Şahsına Atanmış Uygulamayı Görüntüleme Yetkisi Varmı Kontrolü
		if (!isAllowedViewApp("hedas")) {
			$_sonuc =  new stdClass();
			$_sonuc->success			= false;
			$_sonuc->code 				= 203;
			$_sonuc->description		= "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden HEDAS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
			$sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
			SetHeader($_sonuc->code);
			echo $sonuc;
			exit;
		}

		if (!isDbAllowedWriteModule()) {
			$_sonuc =  new stdClass();
			$_sonuc->success			= false;
			$_sonuc->code 				= 203;
			$_sonuc->description		= "HEDAS | Not-Hatırlatma Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
			$sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
			SetHeader($_sonuc->code);
			echo $sonuc;
			exit;
		}

		if (json_last_error() !== 0) {
			$_sonuc =  new stdClass();
			$_sonuc->success			= false;
			$_sonuc->code 				= 203;
			$_sonuc->description		= "Eksik Parametre. Lutfen Api Bilgilerini Dogru Paremetre İle Gönderiniz.";
			$sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
			SetHeader($_sonuc->code);
			echo $sonuc;
			exit;
		}


		//GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
		if (isset($this->JSON_DATA->route) == false || isset($this->JSON_DATA->title) == false) {
			$_sonuc =  new stdClass();
			$_sonuc->success			= false;
			$_sonuc->code 				= 203;
			$_sonuc->description		= "Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
			$sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
			SetHeader($_sonuc->code);
			echo $sonuc;
			exit;
		}

		if ($this->JSON_DATA->route != "addnotes") {
			$_sonuc =  new stdClass();
			$_sonuc->success			= false;
			$_sonuc->code 				= 203;
			$_sonuc->description		= "Yönlendirilen Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
			$sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
			SetHeader($_sonuc->code);
			echo $sonuc;
			exit;
		}

		$_title 		= htmlspecialchars(trim($this->JSON_DATA->title));
		$_subject 		= htmlspecialchars(trim($this->JSON_DATA->subject));
		$_description 	= htmlspecialchars(trim($this->JSON_DATA->description));
		$_noReminder 	= (bool)trim(replacePost($this->JSON_DATA->noReminder));
		$_noReplay 		= (bool)trim(replacePost($this->JSON_DATA->noReplay));
		$_start 		= trim(replaceHtml($this->JSON_DATA->start));
		$_end 			= trim(replaceHtml($this->JSON_DATA->end));



		//$errors = new stdClass();
		$_sonuc =  new stdClass();
		$_errors = new stdClass();
		$_sonuc->success			= false;
		$_sonuc->code 				= "";
		$_sonuc->description		= "";
		$_sonuc->error = false;
		//title validation
		if (valid_min_length($_title, 3) == false || valid_max_length($_title, 250) == false) {
			$_sonuc->error = true;
			$_errors->title = "Başlık alanı uygun kriterlerde değil";
		}

		if (valid_max_length($_subject, 1)) {
			if (valid_min_length($_subject, 3) == false || valid_max_length($_subject, 250) == false) {
				$_errors->subject = "Konu alanı uygun kriterlerde değil";
			}
		}

		if (valid_max_length($_description, 1)) {
			if (valid_min_length($_description, 2) == false || valid_max_length($_description, 500) == false) {
				$_errors->subject = "Konu alanı uygun kriterlerde değil";
			}
		}

		if ($_noReminder == true) {
			$_reminder = 0;
		} else {
			$_reminder = 1;
		}

		if ($_noReplay !== true) {
			$_noReplay = false;
		}

		if (valid_date($_start, "Y-m-d") == false) {
			$_start = date("Y-m-d");
		}
		if (valid_date($_start, "Y-m-d") == false) {
			$_end = date("Y-m-d");
		}


		if ($_sonuc->error == true) {
			$_sonuc->success			= false;
			$_sonuc->code 				= 406;
			$_sonuc->description		= "Form Verilerinde Hata Var.";
			$_sonuc->error = true;
			$_sonuc->errors = $_errors;
			$sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
			SetHeader($_sonuc->code);
			echo $sonuc;
			exit;
		}


		if ($_noReminder == true) {
			// Hatırlatma Yok İse Çalışacak
			$_start = date("Y-m-d") . " 00:00:00";
			$_end = date("Y-m-d") . " 00:00:00";
		} else {
			if ($_noReplay == true) {
				// Hatırlatma Tekrarlanmayacaksa Burası Çalışacak
				$_end = $_start . " 23:59:59";
			} else {
				//Tekrarlanan Hatırlatma Olacak İse Burası Çalışacak
				$_end .= " 23:59:59";
			}
			$_start .= " 00:00:00";
		}
		/*	$addX = array(
			"nr_userid"			=> $this->userData->userB->u_id,
			"nr_title"			=> $_title,
			"nr_subject"		=> $_subject,
			"nr_description"	=> $_description,
			"nr_remind"			=> $_reminder,
			"nr_remind_start"	=> $_start, //dateToTime($_start),
			"nr_remind_stop"	=> $_end, //dateToTime($_end),
			"nr_status"			=> 1,
			"nr_adddate"		=> dateToTime(date("Y-m-d H:i:s")),
			"nr_adduser"		=> $this->userData->userB->u_id
		);
		*/

		$_add = $this->notesreminders_model->ek_add(
			"r8t_edys_notes_reminders",
			array(
				"nr_userid"			=> $this->userData->userB->u_id,
				"nr_title"			=> $_title,
				"nr_subject"		=> $_subject,
				"nr_description"	=> $_description,
				"nr_remind"			=> $_reminder,
				"nr_remind_start"	=> dateToTime($_start),
				"nr_remind_stop"	=> dateToTime($_end),
				"nr_status"			=> 1,
				"nr_adddate"		=> dateToTime(date("Y-m-d H:i:s")),
				"nr_adduser"		=> $this->userData->userB->u_id
			)
		);

		if ($_add) {
			$_sonuc =  new stdClass();
			$_sonuc->success			= true;
			$_sonuc->code 				= 200;
			$_sonuc->description		= "Tebrikler! Ekleme İşlemi Başarılı.";
			$sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
			SetHeader($_sonuc->code);
			echo $sonuc;
			exit;
		} else {
			$_sonuc =  new stdClass();
			$_sonuc->success			= false;
			$_sonuc->code 				= 400;
			$_sonuc->description		= "Hata! Ekleme İşlemi Yapılamadı.";
			$sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
			SetHeader($_sonuc->code);
			echo $sonuc;
			exit;
		}
	}
}
