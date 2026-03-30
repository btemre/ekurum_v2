<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Importexcel extends CI_Controller
{


    public $viewFolder = "";
    public $userData = false;

    public function __construct()
    {
        parent::__construct();

        $this->viewFolder = "importexcel_v";
        $this->load->model("importexcel_model");
        $this->load->helper("importexcel");

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
        if (!isAllowedViewApp("edts")) {
            $alert = array(
                "title" => "Hata!",
                "text" => "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden EDTS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.",
                "type" => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(sys_url("home"));
            exit;
        }

        if (!isDbAllowedViewModule()) {
            $alert = array(
                "title" => "Hata!",
                "text" => "EDTS | Excel Aktarım Modülünü Görüntüleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.",
                "type" => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(sys_url("home"));
            exit;
        }

        $viewData = new stdClass();
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "view";
        $viewData->userData = $this->userData;
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }


    private function getTableColumnName($data = 0)
    {
        $columnName = "d_id";
        switch ($data) {
            case 0:
                $columnName = "d_mahkeme";
                break;
            case 1:
                $columnName = "d_esasno";
                break;
            case 2:
                $columnName = "d_dosyaturu";
                break;
            case 3:
                $columnName = "d_durusmatarihi";
                break;
            case 4:
                $columnName = "d_tarafbilgisi";
                break;
            case 5:
                $columnName = "d_islem";
                break;
            default:
                $columnName = "d_id";
                break;
        }
        return $columnName;
    }


    public function api_list()
    {

        if ($this->userData === false) {
            $_sonuc = new stdClass();
            $_sonuc->recordsTotal = 0;
            $_sonuc->recordsFiltered = 0;
            $_sonuc->data = array();
            $_sonuc->success = false;
            $_sonuc->code = 203;
            $_sonuc->description = "Kullanıcı Yetkilendirme Hatası. Lütfen Giriş Bilgilerinizi Kontrol Ediniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isAllowedViewApp("edts")) {
            $_sonuc = new stdClass();
            $_sonuc->recordsTotal = 0;
            $_sonuc->recordsFiltered = 0;
            $_sonuc->data = array();
            $_sonuc->success = false;
            $_sonuc->code = 203;
            $_sonuc->description = "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden EDTS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isDbAllowedListModule()) {
            $_sonuc = new stdClass();
            $_sonuc->recordsTotal = 0;
            $_sonuc->recordsFiltered = 0;
            $_sonuc->data = array();
            $_sonuc->success = false;
            $_sonuc->code = 203;
            $_sonuc->description = "EDTS | Dosyalar Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $_post = json_encode($this->input->post());
        $postData = json_decode($_post);
        
        // DataTables POST verisi kontrolü
        if (!$postData || !isset($postData->search) || !isset($postData->order) || !isset($postData->order[0])) {
            $_sonuc = new stdClass();
            $_sonuc->recordsTotal = 0;
            $_sonuc->recordsFiltered = 0;
            $_sonuc->data = array();
            $_sonuc->success = false;
            $_sonuc->code = 400;
            $_sonuc->description = "Geçersiz istek parametreleri.";
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($_sonuc);
            exit;
        }
        
        $_searchValue = isset($postData->search->value) ? json_decode($postData->search->value) : new stdClass();
        if (!$_searchValue) $_searchValue = new stdClass();
        $_orderColumn = $this->getTableColumnName($postData->order[0]->column);
        $_orderType = $postData->order[0]->dir;
        $_kactan = isset($postData->start) ? $postData->start : 0;
        $_kacar = isset($postData->length) ? $postData->length : 10;

        //GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
        if (isset($_orderType) === false || in_array($_orderType, array("desc", "asc")) === false) {
            $_sonuc = new stdClass();
            $_sonuc->recordsTotal = 0;
            $_sonuc->recordsFiltered = 0;
            $_sonuc->data = array();
            $_sonuc->success = false;
            $_sonuc->code = 203;
            $_sonuc->description = "Sorgu Parametreleri Eksik. Lütfen Sistem Yöneticinize Başvurunuz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }



        $sql = "";
        $loginUser = $this->userData->userB->u_id;

        $totalSql = "SELECT COUNT(d_id) AS total FROM r8t_edts_importexcel WHERE d_status=0 AND d_adduser=" . $loginUser . $sql;
        // die($totalSql);
        $totalRecordS = $this->importexcel_model->ek_query_all($totalSql);

        $totalRecord = (int) $totalRecordS[0]->total;



        $sql .= " ORDER BY " . $_orderColumn . " " . strtoupper($_orderType);
        $sql .= " LIMIT " . $_kactan . ", " . $_kacar;

        $filterSql = "SELECT * FROM r8t_edts_importexcel WHERE d_status=0 AND d_adduser=" . $loginUser . $sql;


        $filterRecord = $this->importexcel_model->ek_query_all($filterSql);

        if (!$filterRecord) {
            $_sonuc = new stdClass();
            $_sonuc->recordsTotal = $totalRecord;
            $_sonuc->recordsFiltered = 0;
            $_sonuc->data = array();
            $_sonuc->success = false;
            $_sonuc->code = 203;
            $_sonuc->description = "Filtrelemeye Uygun Kayıt Bulunamadı.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        $itemsData = array();
        foreach ($filterRecord as $item) {

            $itemArray = array(
                "_id" => $item->d_id,
                "_dosyaturu" => $item->d_dosyaturu,
                "_mahkeme" => $item->d_mahkeme,
                "_durusmatarihi" => timeToDateFormat($item->d_durusmatarihi, "d.m.Y H:i"),
                "_esasno" => $item->d_esasno,
                "_tarafbilgisi" => ($item->d_tarafbilgisi),
                "_islem" => $item->d_islem
            );
            array_push($itemsData, $itemArray);
        }

        if (count($itemsData) > 0) {
            $_sonuc = new stdClass();
            $_sonuc->recordsTotal = $totalRecord;
            $_sonuc->recordsFiltered = $totalRecord;
            $_sonuc->data = $itemsData;
            $_sonuc->success = true;
            $_sonuc->code = 200;
            $_sonuc->description = $totalRecord . " Adet Kayıt Bulundu.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc = new stdClass();
            $_sonuc->recordsTotal = $totalRecord;
            $_sonuc->recordsFiltered = 0;
            $_sonuc->data = array();
            $_sonuc->success = false;
            $_sonuc->code = 203;
            $_sonuc->description = "Filtrelemeye Uygun Kayıt Bulunamadı.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }


    public function api_deleteitem()
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
            $_sonuc = new stdClass();
            $_sonuc->recordsTotal = 0;
            $_sonuc->recordsFiltered = 0;
            $_sonuc->data = array();
            $_sonuc->success = false;
            $_sonuc->code = 203;
            $_sonuc->description = "Kullanıcı Yetkilendirme Hatası. Lütfen Giriş Bilgilerinizi Kontrol Ediniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isAllowedViewApp("edts")) {
            $_sonuc = new stdClass();
            $_sonuc->recordsTotal = 0;
            $_sonuc->recordsFiltered = 0;
            $_sonuc->data = array();
            $_sonuc->success = false;
            $_sonuc->code = 203;
            $_sonuc->description = "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden EDTS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isDbAllowedListModule()) {
            $_sonuc = new stdClass();
            $_sonuc->recordsTotal = 0;
            $_sonuc->recordsFiltered = 0;
            $_sonuc->data = array();
            $_sonuc->success = false;
            $_sonuc->code = 203;
            $_sonuc->description = "EDTS | Excelden Aktarım Modülünde Silme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        $postData = $this->JSON_DATA;
        //GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
        if (isset($postData->id) === false) {
            $_sonuc = new stdClass();
            $_sonuc->recordsTotal = 0;
            $_sonuc->recordsFiltered = 0;
            $_sonuc->data = array();
            $_sonuc->success = false;
            $_sonuc->code = 203;
            $_sonuc->description = "Sorgu Parametreleri Eksik. Lütfen Sistem Yöneticinize Başvurunuz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $getDelete = $this->importexcel_model->ek_delete(
            "r8t_edts_importexcel",
            array(
                "d_id" => (int) $postData->id,
                "d_adduser" => $this->userData->userB->u_id
            )
        );



        if ($getDelete) {
            $logData = new stdClass();
            $logData->ustid = "";
            $logData->kullanici = $this->userData->userB->u_id;
            $logData->aciklama = (int) $postData->id . " Id Numaralı Duruşma Kaydı Önbellekten Silindi.";
            $logData->uygulama = "edts";
            $logData->modul = "importexcel";
            $logData->icerikid = (int) $postData->id;
            $logData->olddata = array("status" => 1);
            $logData->newdata = array("status" => -1);
            $logyaz = logEkleSilme($logData);

            $_sonuc = new stdClass();
            $_sonuc->data = array();
            $_sonuc->success = true;
            $_sonuc->code = 200;
            $_sonuc->description = "Kayıt Silindi.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc = new stdClass();
            $_sonuc->data = array();
            $_sonuc->success = false;
            $_sonuc->code = 203;
            $_sonuc->description = "Kayıt Silinemedi.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

    }


    public function api_cleanall()
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
            $_sonuc = new stdClass();
            $_sonuc->recordsTotal = 0;
            $_sonuc->recordsFiltered = 0;
            $_sonuc->data = array();
            $_sonuc->success = false;
            $_sonuc->code = 203;
            $_sonuc->description = "Kullanıcı Yetkilendirme Hatası. Lütfen Giriş Bilgilerinizi Kontrol Ediniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isAllowedViewApp("edts")) {
            $_sonuc = new stdClass();
            $_sonuc->recordsTotal = 0;
            $_sonuc->recordsFiltered = 0;
            $_sonuc->data = array();
            $_sonuc->success = false;
            $_sonuc->code = 203;
            $_sonuc->description = "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden EDTS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isDbAllowedListModule()) {
            $_sonuc = new stdClass();
            $_sonuc->recordsTotal = 0;
            $_sonuc->recordsFiltered = 0;
            $_sonuc->data = array();
            $_sonuc->success = false;
            $_sonuc->code = 203;
            $_sonuc->description = "EDTS | Excelden Aktarım Modülünde Silme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }



        $getDelete = $this->importexcel_model->ek_delete(
            "r8t_edts_importexcel",
            array(
                "d_adduser" => $this->userData->userB->u_id
            )
        );



        if ($getDelete) {
            $logData = new stdClass();
            $logData->ustid = "";
            $logData->kullanici = $this->userData->userB->u_id;
            $logData->aciklama = "Önbellek Kayıtları Silindi.";
            $logData->uygulama = "edts";
            $logData->modul = "importexcel";
            $logData->icerikid = null;
            $logData->olddata = array("status" => 1);
            $logData->newdata = array("status" => -1);
            $logyaz = logEkleSilme($logData);

            $_sonuc = new stdClass();
            $_sonuc->data = array();
            $_sonuc->success = true;
            $_sonuc->code = 200;
            $_sonuc->description = "Kayıt Silindi.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc = new stdClass();
            $_sonuc->data = array();
            $_sonuc->success = false;
            $_sonuc->code = 203;
            $_sonuc->description = "Kayıt Silinemedi.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

    }



    public function doupload()
    {

        if ($this->userData === false) {
            redirect(sys_url("login"));
            exit;
        }
        //Kullanıcının Birimine Yada Kendi Şahsına Atanmış Uygulamayı Görüntüleme Yetkisi Varmı Kontrolü
        if (!isAllowedViewApp("edts")) {
            $alert = array(
                "title" => "Hata!",
                "text" => "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden EDTS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.",
                "type" => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(sys_url("home"));
            exit;
        }

        if (!isDbAllowedWriteModule()) {
            $alert = array(
                "title" => "Hata!",
                "text" => "EDTS | Excel Aktarım Modülünü Ekleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.",
                "type" => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("importexcel"));
            exit;
        }

        if(!empty($_FILES["dosya"]["name"])){
        
            $dosya = uploadFile("dosya","./upload/");
            if ( $xlsx = SimpleXLSX::parse('./upload/'.$dosya.'')) {
                $say=0;
                $sifirla = $this->importexcel_model->ek_delete(
                    "r8t_edts_importexcel",
                    array(
                        "d_adduser" => $this->userData->userB->u_id,
                    )
                );
                foreach ($xlsx->rows() as $sutun) {
                        $ekleIslem = $this->importexcel_model->ek_add(
                            "r8t_edts_importexcel",
                            array(
                                "d_mahkeme" => $sutun[0],
                                "d_esasno" => $sutun[1],
                                "d_dosyaturu" => $sutun[2],
                                "d_durusmatarihi" => dateToTimeFormat($sutun[3], "d.m.Y H:i:s"),
                                "d_tarafbilgisi" => $sutun[4],
                                "d_islem" => $sutun[5],
                                "d_status" => 0,
                                "d_adduser" => $this->userData->userB->u_id,
                                "d_adddate" => dateToTimeFormat(date('Y-m-d H:i:s'), "Y-m-d H:i:s"),                                
                            )
                        );
                        if($ekleIslem){
                            $say++;
                        }
                }

                unlink(FCPATH.'upload/'.$dosya);

                if ($say>0) {
                    $alert = array(
                        "title" => "Tebrikler!",
                        "text"  => "Dosya Yükleme işlemi Başarılı",
                        "type"  => "success"
                    );
                    $this->session->set_flashdata("alertToastr", $alert); 
                    redirect(base_url("importexcel"));
                } else {
                    $alert = array(
                        "title" => "Hata!",
                        "text" => "Dosya Yüklenemedi",
                        "type" => "error"
                    );
                    $this->session->set_flashdata("alertToastr", $alert);
                    redirect(sys_url("importexcel"));
                    exit;
                }
                // if ($say>1) {
                //     print '<div class="alert alert-danger alert-solid shadow" role="alert"><strong>İşlem Başarısız! Excel İçeriği Hatalı; Lütfen Satırları Kontrol Ettikten Sonra Tekrar Yükleme Yapınız!</strong></div>';
                // }else {
                //     echo SimpleXLSX::parseError();
                // }
            }
        }else{
            $alert = array(
                "title" => "Hata!",
                "text" => "Dosya Seçilmemiş",
                "type" => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("importexcel"));
            exit;
        }
        
        
    }

    

    public function api_getDurusma()
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
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Kullanıcı Yetkilendirme Hatası. Lütfen Giriş Bilgilerinizi Kontrol Ediniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isAllowedViewApp("edts")) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden CEKAS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isDbAllowedWriteModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "EDTS | Excelden Aktarım Modülünde Ekleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $postData       = $this->JSON_DATA;
        //GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
        if (isset($postData->id) === false) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Sorgu Parametreleri Eksik. Lütfen Sistem Yöneticinize Başvurunuz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $editMemur = $this->userData->userB->u_name . ' ' . $this->userData->userB->u_lastname;
        $editMemur = trim($editMemur) . ' ' . $this->userData->userB->u_surname;

        $getRecord = $this->importexcel_model->ek_get(
            "r8t_edts_importexcel",
            array(
                "d_id" => (int)$postData->id,
                "d_adduser" => $this->userData->userB->u_id
            )
        );


        if ($getRecord) {

            $itemArray = array(
                "_id"           => $getRecord->d_id,
                "_dosyaturu"       => ($getRecord->d_dosyaturu),
                "_mahkeme"    => ($getRecord->d_mahkeme),
                "_durusmatarihi"     => timeToDateFormat($getRecord->d_durusmatarihi, "d-m-Y H:i"),
                "_esasno"     => $getRecord->d_esasno,
                "_tarafbilgisi"       => ($getRecord->d_tarafbilgisi),
                "_islem"       => ($getRecord->d_islem),
            );

            $_sonuc =  new stdClass();
            $_sonuc->data                   = $itemArray;
            $_sonuc->success                = true;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Kayıt Bulundu.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Uygun Kayıt Bulunamadı.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }



    public function api_excelnewrecord() //EXCELLDEN MODAL SONRASI VERILERI EKLEME FONKSIYONU
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
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Kullanıcı Yetkilendirme Hatası. Lütfen Giriş Bilgilerinizi Kontrol Ediniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
        //Kullanıcının Birimine Yada Kendi Şahsına Atanmış Uygulamayı Görüntüleme Yetkisi Varmı Kontrolü
        if (!isAllowedViewApp("edts")) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden CEKAS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
        
        if (!isDbAllowedWriteModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "EDTS | Duruşmalar Modülünde Ekleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
        
        if (json_last_error() !== 0) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Eksik Parametre. Lutfen Api Bilgilerini Dogru Paremetre İle Gönderiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
        
        
        //GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
        if (isset($this->JSON_DATA->route) == false || isset($this->JSON_DATA->edtsDosyaNo) == false) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
        
        if ($this->JSON_DATA->route != "addexceldurusma") {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Yönlendirilen Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
        
        $_edtsId = (int)trim(replaceHtml_Slash($this->JSON_DATA->edtsId));

        $getRecord = $this->importexcel_model->ek_get(
            "r8t_edts_importexcel",
            array(
                "d_id" => (int)$_edtsId,
                "d_adduser" => $this->userData->userB->u_id
            )
        );

        if(!$getRecord){
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $edtsDosyaTurText       = (strlen(trim($getRecord->d_dosyaturu))>3) ? trim($getRecord->d_dosyaturu)."@" : "";
        $edtsMahkemeText        = (strlen(trim($getRecord->d_mahkeme))>3) ? trim($getRecord->d_mahkeme)."@" : "";
        $_edtsDurusmaTarihi     = (strlen(trim($getRecord->d_durusmatarihi))>3) ? trim($getRecord->d_durusmatarihi) : "";
        $_edtsEsasNo            = (strlen(trim($getRecord->d_esasno))>3) ? trim($getRecord->d_esasno) : "";
        $_edtsTarafBilgisi      = (strlen(trim($getRecord->d_tarafbilgisi))>3) ? htmlentities(trim($getRecord->d_tarafbilgisi), ENT_QUOTES) : "";
        $edtsIslemText          = (strlen(trim($getRecord->d_islem))>3) ? trim($getRecord->d_islem)."@" : "";
        
		$_edtsDosyaNo           = trim(replaceHtml_Slash($this->JSON_DATA->edtsDosyaNo));
        $_edtsAvukat            = (int)trim(replaceHtml_Slash($this->JSON_DATA->edtsAvukat));
        $_edtsTaraf             = trim(replaceHtml_Slash($this->JSON_DATA->edtsTaraf));
        $_edtsDurusmaIslemi            = (int)trim(replaceHtml_Slash($this->JSON_DATA->edtsDurusmaIslemi));
        $_edtsTutanakDurum      = (int)trim(replaceHtml_Slash($this->JSON_DATA->edtsTutanakDurum));
        $_edtsAciklama      =htmlentities($this->JSON_DATA->edtsAciklama, ENT_QUOTES);
        
        
        $_edtsIlgiliAvukat      =  str_replace("[{", "", $this->JSON_DATA->edtsIlgiliAvukat);
        $_edtsIlgiliAvukat      = str_replace("}]", "", $_edtsIlgiliAvukat);
        $_edtsIlgiliAvukat      = str_replace("},{", "", $_edtsIlgiliAvukat);
        $_edtsIlgiliAvukat      = str_replace('"', "", $_edtsIlgiliAvukat);
            
        $_edtsIlgiliMemur       =  str_replace("[{", "", $this->JSON_DATA->edtsIlgiliMemur);
        $_edtsIlgiliMemur       = str_replace("}]", "", $_edtsIlgiliMemur);
        $_edtsIlgiliMemur       = str_replace("},{", "", $_edtsIlgiliMemur);
        $_edtsIlgiliMemur       = str_replace('"', "", $_edtsIlgiliMemur);
            
        $_edtsEtiket           =  str_replace("[{", "", $this->JSON_DATA->edtsEtiket);
        $_edtsEtiket          = str_replace("}]", "", $_edtsEtiket);
        $_edtsEtiket          = str_replace("},{", "", $_edtsEtiket);
        $_edtsEtiket          = str_replace('"', "", $_edtsEtiket);    
        


        if ($_edtsTutanakDurum!=1){
            $_edtsTutanakDurum = 0;
        }

        switch($_edtsDurusmaIslemi){
            default:
            $_edtsDurusmaIslemi1 = 0;
            break;
            case 1:
                $_edtsDurusmaIslemi1 = 1;
                break;
            case 2:
                $_edtsDurusmaIslemi1 = 2;  
                break;  
        }

        


        $edtsIlgiliAvukat = @explode("value:", $_edtsIlgiliAvukat);
        $edtsIlgiliMemur = @explode("value:", $_edtsIlgiliMemur);
        $edtsEtiket = @explode("value:", $_edtsEtiket);
        $edtsIlgiliAvukatText = "";
        $edtsIlgiliMemurText = "";
        $edtsEtiketText = "";

        $edtsIlgiliAvukatText   = xlsTagifyToText($edtsIlgiliAvukat);
        $edtsIlgiliMemurText    = xlsTagifyToText($edtsIlgiliMemur);
        $edtsEtiketText         = xlsTagifyToText($edtsEtiket);

        $addMemur = $this->userData->userB->u_name . ' ' . $this->userData->userB->u_lastname;
        $addMemur = trim($addMemur) . ' ' . $this->userData->userB->u_surname;

        $avukatCek = xlsSorumluAvukatCek($_edtsAvukat);
        if($avukatCek!=false){
            $avukatAdi = $avukatCek->u_name . ' ' . $avukatCek->u_lastname;
            $avukatAdi = trim($avukatAdi) . ' ' . $avukatCek->u_surname;
            }else{
            $avukatAdi = "";
        }

        $addData = array(
            "d_dosyaturu"    => $edtsDosyaTurText,
            "d_mahkeme"  => $edtsMahkemeText,
            "d_memur" => $addMemur,
            "d_memurid" => $this->userData->userB->u_id,           
            "d_durusmatarihi"       => $_edtsDurusmaTarihi,
            "d_esasno"      => $_edtsEsasNo,
            "d_tarafbilgisi"      => $_edtsTarafBilgisi,
            "d_islem"   => $edtsIslemText,
            "d_avukat"    =>  $avukatAdi,
            "d_avukatid"    =>  $_edtsAvukat,
            "d_dosyano"    =>  $_edtsDosyaNo,
            "d_taraf"  => $_edtsTaraf,
            "d_aciklama"  => $_edtsAciklama,
            "d_ilgiliavukatlar"  => $edtsIlgiliAvukatText,
            "d_ilgilimemurlar"  => $edtsIlgiliMemurText,
            "d_status"  => 1,
            "d_takip"  => $_edtsDurusmaIslemi1,
            "d_tutanak"  => $_edtsTutanakDurum,
            "d_adddate"   => dateToTimeFormat(date('Y-m-d H:i:s'), "Y-m-d H:i:s"),
            "d_adduser"   => $this->userData->userB->u_id,
            "d_tags"      => $edtsEtiketText
        );

        $add = $this->importexcel_model->ek_add_lastid(
            "r8t_edts_durusmalar",
            $addData
        );

        if ($add) {
            $logData = new stdClass();
            $logData->ustid         = "";
            $logData->kullanici     = $this->userData->userB->u_id;
            $logData->aciklama      = $_edtsEsasNo . " Esas Nolu Duruşma " . $add . " Id numarası ile excelden aktarılarak eklendi.";
            $logData->uygulama      = "edts";
            $logData->modul         = "importexcel";
            $logData->icerikid      = $add;
            $logData->olddata       = array();
            $logData->newdata       = $addData;
            $logyaz = logEkleYeni($logData);

            $getDelete = $this->importexcel_model->ek_delete(
                "r8t_edts_importexcel",
                array(
                    "d_id" => (int) $_edtsId,
                    "d_adduser" => $this->userData->userB->u_id
                )
            );

            $_sonuc =  new stdClass();
            $_sonuc->success            = true;
            $_sonuc->code                 = 200;
            $_sonuc->description        = "Tebrikler! Ekleme İşlemi Başarılı.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Kayıt İşleminde Hata Oluştu. Lütfen Sistem Yöneticinize Başvurunuz!";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }




    ### TAGIFY SEARCH FONKSIYONLARI BASLANGIC #######
    public function api_FormDosyaTuruSearch()
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
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Kullanıcı Yetkilendirme Hatası. Lütfen Giriş Bilgilerinizi Kontrol Ediniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
        //Kullanıcının Birimine Yada Kendi Şahsına Atanmış Uygulamayı Görüntüleme Yetkisi Varmı Kontrolü
        if (!isAllowedViewApp("edts")) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden HEDAS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isDbAllowedListModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "EDTS | Excelden Aktarım Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        if (json_last_error() !== 0) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Eksik Parametre. Lutfen Api Bilgilerini Dogru Paremetre İle Gönderiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        //GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
        if (isset($this->JSON_DATA->searchText) == false) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        $searchText         = trim(replaceHtml_Slash($this->JSON_DATA->searchText));

        $sql = "SELECT d_dosyaturu  FROM r8t_edts_durusmalar WHERE d_dosyaturu  LIKE '%" . $searchText . "%' GROUP BY d_dosyaturu ";
        $items = $this->importexcel_model->ek_query_all($sql);
        $itemText = "";
        if ($items) {
            foreach ($items as $item) {
                $kaynakX = explode("@", $item->d_dosyaturu);
                foreach ($kaynakX as $kaynak) {
                    if (strlen($kaynak) > 0 && stripos($itemText, trim($kaynak)) === false) {
                        $itemText .= trim($kaynak) . ",";
                    }
                }
            }
        }

        if (count($items) > 0) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = true;
            $_sonuc->code                 = 200;
            $_sonuc->description        = count($items) . " Adet Kayıt Bulundu.";
            $_sonuc->data               = $itemText;
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Kayıt Bulunamadı.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }


    public function api_FormMahkemeSearch()
    {
        //POSTTAN GELEN JSON VERI ALMA
        $this->output->set_content_type("application/json");
        $this->output->set_header("Access-Control-Allow-Origin: *");
        $this->output->set_header("Access-Control-Allow-Methods: GET, OPTIONS");
        $this->output->set_header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");

        $this->JSON_DATA = json_decode(trim(file_get_contents("php://input")));
        //$this->JSON_DATA = json_encode(trim(file_get_contents("php://input")), true);
        //$this->JSON_DATA = json_decode($this->JSON_DATA);

        if (json_last_error() !== 0) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Eksik Parametre. Lutfen Api Bilgilerini Dogru Paremetre İle Gönderiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        //GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
        if (isset($this->JSON_DATA->searchText) == false) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        if ($this->userData === false) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Kullanıcı Yetkilendirme Hatası. Lütfen Giriş Bilgilerinizi Kontrol Ediniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isAllowedViewApp("edts")) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden HEDAS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isDbAllowedListModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "EDTS | Excelden Aktarım Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        $searchText         = trim(replaceHtml_Slash($this->JSON_DATA->searchText));


        $sql = "SELECT mh_name FROM r8t_sys_mahkemeler WHERE mh_name LIKE '%" . $searchText . "%' GROUP BY mh_name";
        $items = $this->importexcel_model->ek_query_all($sql);
        $itemText = "";
        if ($items) {
            foreach ($items as $item) {
                $kaynakX = explode("@", $item->mh_name);
                foreach ($kaynakX as $kaynak) {
                    if (strlen($kaynak) > 0 && stripos($itemText, trim($kaynak)) === false) {
                        $itemText .= trim($kaynak) . ",";
                    }
                }
            }
        }

        if (count($items) > 0) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = true;
            $_sonuc->code                 = 200;
            $_sonuc->description        = count($items) . " Adet Kayıt Bulundu.";
            $_sonuc->data               = $itemText;
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Kayıt Bulunamadı.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }


    public function api_FormIlgiliMemurSearch()
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
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Kullanıcı Yetkilendirme Hatası. Lütfen Giriş Bilgilerinizi Kontrol Ediniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
        //Kullanıcının Birimine Yada Kendi Şahsına Atanmış Uygulamayı Görüntüleme Yetkisi Varmı Kontrolü
        if (!isAllowedViewApp("edts")) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden HEDAS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isDbAllowedListModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "EDTS | Excelden Aktarım Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        if (json_last_error() !== 0) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Eksik Parametre. Lutfen Api Bilgilerini Dogru Paremetre İle Gönderiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        //GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
        if (isset($this->JSON_DATA->searchText) == false) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $searchText         = trim(replaceHtml_Slash($this->JSON_DATA->searchText));
        if(strlen($searchText)>0){
            $sqlEk = "";//" AND u_name LIKE '%" . $searchText . "%' OR  u_lastname LIKE '%" . $searchText . "%' OR u_surname LIKE '%" . $searchText . "%'";
        }else{
            $sqlEk = "";
        }
        

        $sql = "SELECT u_id,u_name,u_lastname,u_surname FROM r8t_users WHERE u_unit=2 AND u_statu=7".$sqlEk;
        $items = $this->importexcel_model->ek_query_all($sql);
        $itemText = "";
        if ($items) {
            foreach ($items as $item) {
                $itemText .= $item->u_id . "-".trim($item->u_name." ".$item->u_lastname)." ".$item->u_surname.",";
            }
        }

        if (count($items) > 0) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = true;
            $_sonuc->code                 = 200;
            $_sonuc->description        = count($items) . " Adet Kayıt Bulundu.";
            $_sonuc->data               = $itemText;
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Kayıt Bulunamadı.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }

    public function api_FormIlgiliAvukatSearch()
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
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Kullanıcı Yetkilendirme Hatası. Lütfen Giriş Bilgilerinizi Kontrol Ediniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
        //Kullanıcının Birimine Yada Kendi Şahsına Atanmış Uygulamayı Görüntüleme Yetkisi Varmı Kontrolü
        if (!isAllowedViewApp("edts")) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden HEDAS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isDbAllowedListModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "EDTS | Excelden Aktarım Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        if (json_last_error() !== 0) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Eksik Parametre. Lutfen Api Bilgilerini Dogru Paremetre İle Gönderiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        //GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
        if (isset($this->JSON_DATA->searchText) == false) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $searchText         = trim(replaceHtml_Slash($this->JSON_DATA->searchText));
        if(strlen($searchText)>0){
            $sqlEk = "";//" AND u_name LIKE '%" . $searchText . "%' OR  u_lastname LIKE '%" . $searchText . "%' OR u_surname LIKE '%" . $searchText . "%'";
        }else{
            $sqlEk = "";
        }
        

        $sql = "SELECT u_id,u_name,u_lastname,u_surname FROM r8t_users WHERE u_unit=2 AND u_statu=9".$sqlEk;
        $items = $this->importexcel_model->ek_query_all($sql);
        $itemText = "";
        if ($items) {
            foreach ($items as $item) {
                $itemText .= $item->u_id . "-".trim($item->u_name." ".$item->u_lastname)." ".$item->u_surname.",";
            }
        }

        if (count($items) > 0) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = true;
            $_sonuc->code                 = 200;
            $_sonuc->description        = count($items) . " Adet Kayıt Bulundu.";
            $_sonuc->data               = $itemText;
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Kayıt Bulunamadı.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }



    public function api_SelectListAvukat()
    {
        //POSTTAN GELEN JSON VERI ALMA
        $this->output->set_content_type("application/json");
        $this->output->set_header("Access-Control-Allow-Origin: *");
        $this->output->set_header("Access-Control-Allow-Methods: GET, OPTIONS");
        $this->output->set_header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");

        $this->JSON_DATA = json_decode(trim(file_get_contents("php://input")));
        //$this->JSON_DATA = json_encode(trim(file_get_contents("php://input")), true);
        //$this->JSON_DATA = json_decode($this->JSON_DATA);

        // if (json_last_error() !== 0) {
        //     $_sonuc =  new stdClass();
        //     $_sonuc->success            = false;
        //     $_sonuc->code                 = 203;
        //     $_sonuc->description        = "Eksik Parametre. Lutfen Api Bilgilerini Dogru Paremetre İle Gönderiniz.";
        //     $sonuc = json_encode($_sonuc);
        //     SetHeader($_sonuc->code);
        //     echo $sonuc;
        //     exit;
        // }


        // //GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
        // if (isset($this->JSON_DATA->type) == false) {
        //     $_sonuc =  new stdClass();
        //     $_sonuc->success            = false;
        //     $_sonuc->code                 = 203;
        //     $_sonuc->description        = "Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
        //     $sonuc = json_encode($_sonuc);
        //     SetHeader($_sonuc->code);
        //     echo $sonuc;
        //     exit;
        // }


        if ($this->userData === false) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Kullanıcı Yetkilendirme Hatası. Lütfen Giriş Bilgilerinizi Kontrol Ediniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isAllowedViewApp("edts")) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden EDTS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isDbAllowedListModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "EDTS | Excelden Aktarım Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        $searchText         = "";//trim(replaceHtml_Slash($this->JSON_DATA->searchText));

        if(strlen($searchText)>0){
            $sqlEk = " AND u_name LIKE '%" . $searchText . "%' OR  u_lastname LIKE '%" . $searchText . "%' OR u_surname LIKE '%" . $searchText . "%'";
        }else{
            $sqlEk = "";
        }

        $sql = "SELECT u_id AS id,CONCAT(u_name,' ',u_lastname,' ',u_surname) AS text FROM r8t_users WHERE u_unit=2 AND u_statu=9".$sqlEk;
        $items = $this->importexcel_model->ek_query_all($sql);
        // $itemText = "";
        // if ($items) {
        //     foreach ($items as $item) {
        //         $kaynakX = explode("@", $item->mh_name);
        //         foreach ($kaynakX as $kaynak) {
        //             if (strlen($kaynak) > 0 && stripos($itemText, trim($kaynak)) === false) {
        //                 $itemText .= trim($kaynak) . ",";
        //             }
        //         }
        //     }
        // }

        if (count($items) > 0) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = true;
            $_sonuc->code                 = 200;
            $_sonuc->description        = count($items) . " Adet Kayıt Bulundu.";
            $_sonuc->data               = $items;
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Kayıt Bulunamadı.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }



    public function api_SelectListMahkeme()
    {
        //POSTTAN GELEN JSON VERI ALMA
        $this->output->set_content_type("application/json");
        $this->output->set_header("Access-Control-Allow-Origin: *");
        $this->output->set_header("Access-Control-Allow-Methods: GET, OPTIONS");
        $this->output->set_header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");

        $this->JSON_DATA = json_decode(trim(file_get_contents("php://input")));
        //$this->JSON_DATA = json_encode(trim(file_get_contents("php://input")), true);
        //$this->JSON_DATA = json_decode($this->JSON_DATA);

        if (json_last_error() !== 0) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Eksik Parametre. Lutfen Api Bilgilerini Dogru Paremetre İle Gönderiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        //GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
        if (isset($this->JSON_DATA->status) == false) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        if ($this->userData === false) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Kullanıcı Yetkilendirme Hatası. Lütfen Giriş Bilgilerinizi Kontrol Ediniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isAllowedViewApp("edts")) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden EDTS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isDbAllowedListModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "EDTS | Excelden Aktarım Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        $searchText         = "";//trim(replaceHtml_Slash($this->JSON_DATA->searchText));


        $sql = "SELECT * FROM r8t_sys_mahkemeler WHERE mh_name LIKE '%" . $searchText . "%' GROUP BY mh_name";
        $items = $this->importexcel_model->ek_query_all($sql);
        // $itemText = "";
        // if ($items) {
        //     foreach ($items as $item) {
        //         $kaynakX = explode("@", $item->mh_name);
        //         foreach ($kaynakX as $kaynak) {
        //             if (strlen($kaynak) > 0 && stripos($itemText, trim($kaynak)) === false) {
        //                 $itemText .= trim($kaynak) . ",";
        //             }
        //         }
        //     }
        // }

        if (count($items) > 0) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = true;
            $_sonuc->code                 = 200;
            $_sonuc->description        = count($items) . " Adet Kayıt Bulundu.";
            $_sonuc->data               = $items;
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Kayıt Bulunamadı.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }



    ### TAGIFY SEARCH FONKSIYONLARI BITIS #######




}