<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gelengiden extends CI_Controller
{
    public $viewFolder     = "";
    public $userData        = false;

    public function __construct()
    {
        parent::__construct();

        $this->viewFolder = "gelengiden_v";
        $this->load->model("gelengiden_model");

        $this->load->model("auth_model");
        $this->userData = $this->auth_model->userData;
        $this->load->helper("gelengiden");
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
                "text"  => "HEDAS | Gelen-Giden Evrak Modülünü Görüntüleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(sys_url("home"));
            exit;
        }

        $items = $this->gelengiden_model->ek_get_all(
            "r8t_edys_ggevrak",
            array(
                "gg_id >" => -1
            ),
            "gg_id DESC"
        );

        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "view";
        $viewData->userData             = $this->userData;
        $viewData->items          = $items;
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }
    public function ara()
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

        if (!isDbAllowedListModule()) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "HEDAS | Evrak Listesi Modülünü Görüntüleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(sys_url("home"));
            exit;
        }
        $gelengidentum = $this->gelengiden_model->gelenGidenTum();

        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "ara";
        $viewData->userData             = $this->userData;
        $viewData->gelengidentum = $gelengidentum
        ;
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }
    public function api_list()
    {

        if ($this->userData === false) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Kullanıcı Yetkilendirme Hatası. Lütfen Giriş Bilgilerinizi Kontrol Ediniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isAllowedViewApp("hedas")) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden HEDAS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isDbAllowedListModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "HEDAS | Gelen-Giden Evrak Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $_post          = json_encode($this->input->post());
        $postData       = json_decode($_post);
        $_searchValue   = json_decode($postData->search->value);
        $_orderColumn   = $this->getTableColumnName($postData->order[0]->column);
        $_orderType     = $postData->order[0]->dir;
        $_kactan        = $postData->start;
        $_kacar         = $postData->length;
        $_kactan        = max(0, (int) $_kactan);
        $_kacar         = min(500, max(1, (int) $_kacar));

        //GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
        if (isset($_searchValue->ggTarih) === false || in_array($_searchValue->ggTur, array(-1, 0, 1, 2)) === false || in_array($_searchValue->ggKategori, array(-1, 0, 1, 2)) === false) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Sorgu Parametreleri Eksik. Lütfen Sistem Yöneticinize Başvurunuz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $tarihData = explode(" & ", (string)($_searchValue->ggTarih ?? ''));
        $_tarihStart    = dateToTimeFormat($tarihData[0] . " 00:00:00", "d-m-Y H:i:s");
        $_tarihStop     = dateToTimeFormat($tarihData[1] . " 23:59:59", "d-m-Y H:i:s");


        $sql = "";

        if ($_searchValue->ggTur == 0 || $_searchValue->ggTur == 1 || $_searchValue->ggTur == 2) {
            $sql .= " AND gg_tur = " . trim(replaceHtml($_searchValue->ggTur));
        }

        if ($_searchValue->ggKategori == 0 || $_searchValue->ggKategori == 1 || $_searchValue->ggKategori == 2) {
            $sql .= " AND gg_kategori = " . trim(replaceHtml($_searchValue->ggKategori));
        }

        $sql .= ")";
        $_ekVar = false;
        $_say = 0;
        $sqlEk = "";
        if (strlen(trim(replaceHtml_Slash($_searchValue->ggIlgili))) > 0) {
            $_ekVar = true;
            $_say++;
            $sqlEk .= "gg_kaynak LIKE '%" . trim(replaceHtml_Slash($_searchValue->ggIlgili)) . "%'";
        }
        if (strlen(trim(replaceHtml($_searchValue->ggSayi))) > 0) {
            $_ekVar = true;
            $_say++;
            if ($_say > 0) {
                $sqlEk .= " OR gg_sayi LIKE '%" . trim(replaceHtml($_searchValue->ggSayi)) . "%'";
            } else {
                $sqlEk .= "gg_sayi LIKE '%" . trim(replaceHtml($_searchValue->ggSayi)) . "%'";
            }
        }
        if (strlen(trim(replaceHtml_Slash($_searchValue->ggDosyaNo))) > 0) {
            $_ekVar = true;
            $_say++;
            if ($_say > 0) {
                $sqlEk .= " OR gg_dosyano LIKE '%" . trim(replaceHtml_Slash($_searchValue->ggDosyaNo)) . "%'";
            } else {
                $sqlEk .= "gg_dosyano LIKE '%" . trim(replaceHtml_Slash($_searchValue->ggDosyaNo)) . "%'";
            }
        }
        if (strlen(trim(replaceHtml_Slash_Karakter($_searchValue->ggAciklama))) > 0) {
            $_ekVar = true;
            $_say++;
            if ($_say > 0) {
                $sqlEk .= " OR gg_aciklama LIKE '%" . trim(replaceHtml_Slash_Karakter($_searchValue->ggAciklama)) . "%'";
            } else {
                $sqlEk .= "gg_aciklama LIKE '%" . trim(replaceHtml_Slash_Karakter($_searchValue->ggAciklama)) . "%'";
            }
        }
        if (strlen(trim(replaceHtml($_searchValue->ggEtiket))) > 0) {
            $_ekVar = true;
            $_say++;
            if ($_say > 0) {
                $sqlEk .= " OR gg_tags LIKE '%" . trim(replaceHtml($_searchValue->ggEtiket)) . "%'";
            } else {
                $sqlEk .= "gg_tags LIKE '%" . trim(replaceHtml($_searchValue->ggEtiket)) . "%'";
            }
        }
        if (strlen(trim(replaceHtml_Slash($_searchValue->ggText))) > 0) {
            $_ekVar = true;
            $_say++;
            if ($_say > 0) {
                $sqlEk .= " OR gg_tarih LIKE '%" . trim(replaceHtml_Slash($_searchValue->ggText)) . "%' OR gg_id LIKE '%" . trim(replaceHtml_Slash($_searchValue->ggText)) . "%'";
            } else {
                $sqlEk .= "gg_tarih LIKE '%" . trim(replaceHtml_Slash($_searchValue->ggText)) . "%' OR gg_id LIKE '%" . trim(replaceHtml_Slash($_searchValue->ggText)) . "%'";
            }
        }

        if ($_ekVar == true) {
            $sql .= " AND (" . $sqlEk . ")";
        }

        $totalSql = "SELECT COUNT(gg_id) AS total FROM r8t_edys_ggevrak WHERE gg_status=1 AND (gg_adddate >= " . trim(replaceHtml($_tarihStart)) . " AND gg_adddate <= " . trim(replaceHtml($_tarihStop)) . $sql;

        $totalRecordS = $this->gelengiden_model->ek_query_all($totalSql);

        $totalRecord = (int)$totalRecordS[0]->total;



        $sql .= " ORDER BY " . $_orderColumn . " " . strtoupper($_orderType);
        $sql .= " LIMIT " . $_kactan . ", " . $_kacar;

        $filterSql = "SELECT gg_id, gg_tarih, gg_kaynak, gg_tur, gg_sayi, gg_dosyano, gg_kategori, gg_tags, gg_aciklama FROM r8t_edys_ggevrak WHERE gg_status=1 AND (gg_adddate >= " . trim(replaceHtml($_tarihStart)) . " AND gg_adddate <= " . trim(replaceHtml($_tarihStop)) . $sql;
        //die($filterSql);
        $filterRecord = $this->gelengiden_model->ek_query_all($filterSql);


        if (!$filterRecord) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Filtrelemeye Uygun Kayıt Bulunamadı.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $itemsData = array();
        foreach ($filterRecord as $item) {
            $itemArray = array(
                "_id"        => $item->gg_id,
                "_tarih"     => timeToDateFormat($item->gg_tarih, "d.m.Y"),
                "_tarihtime" => $item->gg_tarih,
                "_ilgili"    => ggIlgiliYazdir($item->gg_kaynak),
                "_tur"       => ggTurYazdir($item->gg_tur),
                "_sayi"      => $item->gg_sayi,
                "_dosyano"   => $item->gg_dosyano,
                "_kategori"  => ggKategoriYazdir($item->gg_kategori),
                "_tags"      => ggTagYazdir($item->gg_tags),
                "_aciklama"  => html_entity_decode($item->gg_aciklama, ENT_QUOTES)
            );
            array_push($itemsData, $itemArray);
        }

        if (count($itemsData) > 0) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = $totalRecord;
            $_sonuc->data                   = $itemsData;
            $_sonuc->success                = true;
            $_sonuc->code                   = 200;
            $_sonuc->description            = $totalRecord . " Adet Kayıt Bulundu.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Filtrelemeye Uygun Kayıt Bulunamadı.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }
    public function api_listDashboard()
    {
        $_post          = json_encode($this->input->post());
        $postData       = json_decode($_post);
        $_searchValue   = json_decode($postData->search->value);
        $_orderColumn   = $this->getTableColumnName($postData->order[0]->column);
        $_orderType     = $postData->order[0]->dir;
        $_kactan        = $postData->start;
        $_kacar         = $postData->length;

         

        $tarihData = explode(" & ", (string)($_searchValue->ggTarih ?? ''));
        $_tarihStart    = dateToTimeFormat($tarihData[0] . " 00:00:00", "d-m-Y H:i:s");
        $_tarihStop     = dateToTimeFormat($tarihData[1] . " 23:59:59", "d-m-Y H:i:s");


        $sql = "";

        if ($_searchValue->ggTur == 0 || $_searchValue->ggTur == 1 || $_searchValue->ggTur == 2) {
            $sql .= " AND gg_tur = " . trim(replaceHtml($_searchValue->ggTur));
        }

        if ($_searchValue->ggKategori == 0 || $_searchValue->ggKategori == 1 || $_searchValue->ggKategori == 2) {
            $sql .= " AND gg_kategori = " . trim(replaceHtml($_searchValue->ggKategori));
        }

        $sql .= ")";
        $_ekVar = false;
        $_say = 0;
        $sqlEk = "";
        if (strlen(trim(replaceHtml_Slash($_searchValue->ggIlgili))) > 0) {
            $_ekVar = true;
            $_say++;
            $sqlEk .= "gg_kaynak LIKE '%" . trim(replaceHtml_Slash($_searchValue->ggIlgili)) . "%'";
        }
        if (strlen(trim(replaceHtml($_searchValue->ggSayi))) > 0) {
            $_ekVar = true;
            $_say++;
            if ($_say > 0) {
                $sqlEk .= " OR gg_sayi LIKE '%" . trim(replaceHtml($_searchValue->ggSayi)) . "%'";
            } else {
                $sqlEk .= "gg_sayi LIKE '%" . trim(replaceHtml($_searchValue->ggSayi)) . "%'";
            }
        }
        if (strlen(trim(replaceHtml_Slash($_searchValue->ggDosyaNo))) > 0) {
            $_ekVar = true;
            $_say++;
            if ($_say > 0) {
                $sqlEk .= " OR gg_dosyano LIKE '%" . trim(replaceHtml_Slash($_searchValue->ggDosyaNo)) . "%'";
            } else {
                $sqlEk .= "gg_dosyano LIKE '%" . trim(replaceHtml_Slash($_searchValue->ggDosyaNo)) . "%'";
            }
        }
        if (strlen(trim(replaceHtml_Slash($_searchValue->ggAciklama))) > 0) {
            $_ekVar = true;
            $_say++;
            if ($_say > 0) {
                $sqlEk .= " OR gg_aciklama LIKE '%" . trim(replaceHtml_Slash($_searchValue->ggAciklama)) . "%'";
            } else {
                $sqlEk .= "gg_aciklama LIKE '%" . trim(replaceHtml_Slash($_searchValue->ggAciklama)) . "%'";
            }
        }
        if (strlen(trim(replaceHtml($_searchValue->ggEtiket))) > 0) {
            $_ekVar = true;
            $_say++;
            if ($_say > 0) {
                $sqlEk .= " OR gg_tags LIKE '%" . trim(replaceHtml($_searchValue->ggEtiket)) . "%'";
            } else {
                $sqlEk .= "gg_tags LIKE '%" . trim(replaceHtml($_searchValue->ggEtiket)) . "%'";
            }
        }
        if (strlen(trim(replaceHtml_Slash($_searchValue->ggText))) > 0) {
            $_ekVar = true;
            $_say++;
            if ($_say > 0) {
                $sqlEk .= " OR gg_tarih LIKE '%" . trim(replaceHtml_Slash($_searchValue->ggText)) . "%' OR gg_id LIKE '%" . trim(replaceHtml_Slash($_searchValue->ggText)) . "%'";
            } else {
                $sqlEk .= "gg_tarih LIKE '%" . trim(replaceHtml_Slash($_searchValue->ggText)) . "%' OR gg_id LIKE '%" . trim(replaceHtml_Slash($_searchValue->ggText)) . "%'";
            }
        }

        if ($_ekVar == true) {
            $sql .= " AND (" . $sqlEk . ") ";
            
        }

        $totalSql = "SELECT COUNT(gg_id) AS total
        FROM (
            SELECT gg_id
            FROM r8t_edys_ggevrak
            WHERE gg_status = 1
            ORDER BY gg_id DESC
            LIMIT 5
        ) AS subquery
        " ;

        $totalRecordS = $this->gelengiden_model->ek_query_all($totalSql);

        $totalRecord = (int)$totalRecordS[0]->total;



        $sql .= " ORDER BY gg_id DESC ";
        $sql .= " LIMIT 5 " ;

        $filterSql = "SELECT gg_id, gg_tarih, gg_kaynak, gg_tur, gg_sayi, gg_dosyano, gg_kategori, gg_tags, gg_aciklama FROM r8t_edys_ggevrak WHERE gg_status=1 AND (gg_adddate >= " . trim(replaceHtml($_tarihStart)) . " AND gg_adddate <= " . trim(replaceHtml($_tarihStop)) . $sql;
        //die($filterSql);
        $filterRecord = $this->gelengiden_model->ek_query_all($filterSql);


        if (!$filterRecord) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Filtrelemeye Uygun Kayıt Bulunamadı.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $itemsData = array();
        foreach ($filterRecord as $item) {
            $itemArray = array(
                "_id"        => $item->gg_id,
                "_tarih"     => timeToDateFormat($item->gg_tarih, "d.m.Y"),
                "_tarihtime" => $item->gg_tarih,
                "_ilgili"    => ggIlgiliYazdir($item->gg_kaynak),
                "_tur"       => ggTurYazdir($item->gg_tur),
                "_sayi"      => $item->gg_sayi,
                "_dosyano"   => $item->gg_dosyano,
                "_kategori"  => ggKategoriYazdir($item->gg_kategori),
                "_tags"      => ggTagYazdir($item->gg_tags),
                "_aciklama"  => html_entity_decode($item->gg_aciklama, ENT_QUOTES)
            );
            array_push($itemsData, $itemArray);
        }

        if (count($itemsData) > 0) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = $totalRecord;
            $_sonuc->data                   = $itemsData;
            $_sonuc->success                = true;
            $_sonuc->code                   = 200;
            $_sonuc->description            = $totalRecord . " Adet Kayıt Bulundu.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Filtrelemeye Uygun Kayıt Bulunamadı.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }
    public function api_getEvrak()
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
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isAllowedViewApp("hedas")) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden HEDAS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isDbAllowedViewModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "HEDAS | Gelen-Giden Evrak Modülünde Görüntüleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
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
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Sorgu Parametreleri Eksik. Lütfen Sistem Yöneticinize Başvurunuz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $getRecord = $this->gelengiden_model->ek_get(
            "r8t_edys_ggevrak",
            array(
                "gg_id" => (int)$postData->id
            )
        );

        if ($getRecord) {

            $itemArray = array(
                "_id"        => $getRecord->gg_id,
                "_tarih"     => timeToDateFormat($getRecord->gg_tarih, "d-m-Y"),
                "_ilgili"    => ggEditIlgiliYazdir($getRecord->gg_kaynak),
                "_tur"       => $getRecord->gg_tur,
                "_sayi"      => $getRecord->gg_sayi,
                "_dosyano"   => $getRecord->gg_dosyano,
                "_kategori"  => $getRecord->gg_kategori,
                "_tags"      => ggEditTagYazdir($getRecord->gg_tags),
                "_aciklama"  => html_entity_decode($getRecord->gg_aciklama, ENT_QUOTES)
            );

            $_sonuc =  new stdClass();
            $_sonuc->data                   = $itemArray;
            $_sonuc->success                = true;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Kayıt Bulundu.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Uygun Kayıt Bulunamadı.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }

    private function getTableColumnName($data = 0)
    {
        $columnName = "gg_id";
        switch ($data) {
            case 0:
                $columnName = "gg_tarih";
                break;
            case 1:
                $columnName = "gg_kaynak";
                break;
            case 2:
                $columnName = "gg_tur";
                break;
            case 3:
                $columnName = "gg_sayi";
                break;
            case 4:
                $columnName = "gg_dosyano";
                break;
            case 5:
                $columnName = "gg_kategori";
                break;
            case 6:
                $columnName = "gg_tags";
                break;
            case 7:
                $columnName = "gg_aciklama";
                break;
            default:
                $columnName = "gg_tarih";
                break;
        }
        return $columnName;
    }

    public function api_newrecord()
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
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
        //Kullanıcının Birimine Yada Kendi Şahsına Atanmış Uygulamayı Görüntüleme Yetkisi Varmı Kontrolü
        if (!isAllowedViewApp("hedas")) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden HEDAS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isDbAllowedWriteModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "HEDAS | Gelen-Giden Evrak Modülünde Ekleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (json_last_error() !== 0) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Eksik Parametre. Lutfen Api Bilgilerini Dogru Paremetre İle Gönderiniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        //GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
        if (isset($this->JSON_DATA->route) == false || isset($this->JSON_DATA->ggTur) == false) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if ($this->JSON_DATA->route != "addgelengiden") {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Yönlendirilen Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $_ggTur             = (int)$this->JSON_DATA->ggTur;
        $_ggKategori        = (int)$this->JSON_DATA->ggKategori;
        $_ggDosyaNo         = (int)$this->JSON_DATA->ggDosyaNo;
        $_ggSayi            = trim(replaceHtml($this->JSON_DATA->ggSayi));
        $_ggEvrakTarih      = trim(replaceHtml($this->JSON_DATA->ggEvrakTarih));
        $_ggAciklama        = htmlentities($this->JSON_DATA->ggAciklama, ENT_QUOTES);
        $_ggKaynak          = str_replace("[{", "", $this->JSON_DATA->ggKaynak);
        $_ggKaynak          = str_replace("}]", "", $_ggKaynak);
        $_ggKaynak          = str_replace("},{", "", $_ggKaynak);
        $_ggKaynak          = str_replace('"', "", $_ggKaynak);
        $_ggTags            = str_replace("[{", "", $this->JSON_DATA->ggTags);
        $_ggTags          = str_replace("}]", "", $_ggTags);
        $_ggTags          = str_replace("},{", "", $_ggTags);
        $_ggTags          = str_replace('"', "", $_ggTags);


        $kaynak = explode("value:", (string)($_ggKaynak ?? ''));
        $etiket = explode("value:", (string)($_ggTags ?? ''));
        $kaynakText = "";
        $etiketText = "";
        if (is_array($kaynak)) {
            foreach ($kaynak as $veri) {
                if (strlen($veri) > 0 && $veri != "") {
                    $kaynakText .= $veri . "@";
                }
            }
            //echo $kaynakText;
        }
        if (is_array($etiket)) {
            foreach ($etiket as $veri) {
                if (strlen($veri) > 0 && $veri != "") {
                    $etiketText .= $veri . "@";
                }
            }
            //echo $etiketText;
        }

        $add = $this->gelengiden_model->add(
            array(
                "gg_tur"        => $_ggTur,
                "gg_kaynak"     => $kaynakText,
                "gg_aciklama"   => $_ggAciklama,
                "gg_tarih"      => dateToTimeFormat($_ggEvrakTarih . " 00:00:00", "d-m-Y H:i:s"),
                "gg_sayi"       => $_ggSayi,
                "gg_dosyano"    => $_ggDosyaNo,
                "gg_kategori"   => $_ggKategori,
                "gg_tags"       => $etiketText,
                "gg_status"     => 1,
                "gg_adddate"    => dateToTimeFormat(date('Y-m-d H:i:s'), "Y-m-d H:i:s"),
                "gg_adduser"    => $this->userData->userB->u_id
            )
        );

        if ($add) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = true;
            $_sonuc->code                 = 200;
            $_sonuc->description        = "Tebrikler! Ekleme İşlemi Başarılı.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Kayıt İşleminde Hata Oluştu. Lütfen Sistem Yöneticinize Başvurunuz!";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }

    public function api_taglist()
    {

        if ($this->userData === false) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Kullanıcı Yetkilendirme Hatası. Lütfen Giriş Bilgilerinizi Kontrol Ediniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
        //Kullanıcının Birimine Yada Kendi Şahsına Atanmış Uygulamayı Görüntüleme Yetkisi Varmı Kontrolü
        if (!isAllowedViewApp("hedas")) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden HEDAS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isDbAllowedListModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "HEDAS | Gelen-Giden Evrak Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        $items = $this->gelengiden_model->ek_get_all(
            "r8t_edys_tags",
            array(
                "tag_ispublic"  => 1
            )
        );

        if (!$items) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Kayıtlı Etiket Bulunamadı.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
        $itemData = array();
        foreach ($items as $item) {
            $itemArray = array(
                "id"    => $item->tag_id,
                "name"  => $item->tag_isim,
                "color" => $item->tag_color
            );
            array_push($itemData, $itemArray);
        }

        if (count($itemData) > 0) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = true;
            $_sonuc->code                 = 200;
            $_sonuc->description        = count($itemData) . " Adet Etiket Bulundu.";
            $_sonuc->data               = $itemData;
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Kayıtlı Etiket Bulunamadı.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }

    public function api_editrecord()
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
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
        //Kullanıcının Birimine Yada Kendi Şahsına Atanmış Uygulamayı Görüntüleme Yetkisi Varmı Kontrolü
        if (!isAllowedViewApp("hedas")) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden HEDAS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isDbAllowedUpdateModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "HEDAS | Gelen-Giden Evrak Modülünde Güncelleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (json_last_error() !== 0) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Eksik Parametre. Lutfen Api Bilgilerini Dogru Paremetre İle Gönderiniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        //GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
        if (isset($this->JSON_DATA->route) == false || isset($this->JSON_DATA->ggTur) == false) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if ($this->JSON_DATA->route != "editgelengiden") {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Yönlendirilen Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $_ggId              = (int)$this->JSON_DATA->ggId;
        $_ggTur             = (int)$this->JSON_DATA->ggTur;
        $_ggKategori        = (int)$this->JSON_DATA->ggKategori;
        $_ggDosyaNo         = (int)$this->JSON_DATA->ggDosyaNo;
        $_ggSayi            = trim(replaceHtml($this->JSON_DATA->ggSayi));
        $_ggEvrakTarih      = trim(replaceHtml($this->JSON_DATA->ggTarih));
        $_ggAciklama        = htmlentities($this->JSON_DATA->ggAciklama, ENT_QUOTES);
        $_ggKaynak          = str_replace("[{", "", $this->JSON_DATA->ggKaynak);
        $_ggKaynak          = str_replace("}]", "", $_ggKaynak);
        $_ggKaynak          = str_replace("},{", "", $_ggKaynak);
        $_ggKaynak          = str_replace('"', '', $_ggKaynak);
        $_ggTags            = str_replace("[{", "", $this->JSON_DATA->ggEtiket);
        $_ggTags          = str_replace("}]", "", $_ggTags);
        $_ggTags          = str_replace("},{", "", $_ggTags);
        $_ggTags          = str_replace('"', "", $_ggTags);

        // echo 'KAYNAK:' . $_ggKaynak . ' <br> ETIKET:' . $_ggTags;
        //die();


        $kaynak = explode("value:", (string)($_ggKaynak ?? ''));
        $etiket = explode("value:", (string)($_ggTags ?? ''));
        $kaynakText = "";
        $etiketText = "";
        if (is_array($kaynak)) {
            foreach ($kaynak as $veri) {
                if (strlen($veri) > 0 && $veri != "") {
                    $kaynakText .= $veri . "@";
                }
            }
            //echo $kaynakText;
        }
        if (is_array($etiket)) {
            foreach ($etiket as $veri) {
                if (strlen($veri) > 0 && $veri != "") {
                    $etiketText .= $veri . "@";
                }
            }
            // echo $etiketText;
        }

        $update = $this->gelengiden_model->update(
            array(
                "gg_id"         => $_ggId
            ),
            array(
                "gg_tur"        => $_ggTur,
                "gg_kaynak"     => $kaynakText,
                "gg_aciklama"   => $_ggAciklama,
                "gg_tarih"      => dateToTimeFormat($_ggEvrakTarih . " 00:00:00", "d-m-Y H:i:s"),
                "gg_sayi"       => $_ggSayi,
                "gg_dosyano"    => $_ggDosyaNo,
                "gg_kategori"   => $_ggKategori,
                "gg_tags"       => $etiketText,
                "gg_editdate"    => dateToTimeFormat(date('Y-m-d H:i:s'), "Y-m-d H:i:s"),
                "gg_edituser"    => $this->userData->userB->u_id
            )
        );

        if ($update !== false) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = true;
            $_sonuc->code                 = 200;
            $_sonuc->description        = "Tebrikler! Güncelleme İşlemi Başarılı.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Güncelleme İşleminde Hata Oluştu. Lütfen Sistem Yöneticinize Başvurunuz!";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }

    public function api_ejectdata()
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
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isAllowedViewApp("hedas")) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden HEDAS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isDbAllowedUpdateModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "HEDAS | Gelen-Giden Evrak Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
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
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Sorgu Parametreleri Eksik. Lütfen Sistem Yöneticinize Başvurunuz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $getUpdate = $this->gelengiden_model->update(
            array(
                "gg_id" => (int)$postData->id
            ),
            array(
                "gg_status" => -1
            )
        );

        if ($getUpdate) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = true;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Kayıt Çöp Kutusuna Taşındı.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Kayıt Çöp Kutusuna Taşınamadı.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }

    ##ÇÖP KUTUSU LİST VE MODULLER;

    public function archive()
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
                "text"  => "HEDAS | Gelen-Giden Evrak Modülünde Görüntüleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(sys_url("home"));
            exit;
        }


        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "archive";
        $viewData->userData             = $this->userData;
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function api_ejectlist()
    {

        if ($this->userData === false) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Kullanıcı Yetkilendirme Hatası. Lütfen Giriş Bilgilerinizi Kontrol Ediniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isAllowedViewApp("hedas")) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden HEDAS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isDbAllowedListModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "HEDAS | Gelen-Giden Evrak Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $_post          = json_encode($this->input->post());
        $postData       = json_decode($_post);
        $_searchValue   = json_decode($postData->search->value);
        $_orderColumn   = $this->getTableColumnName($postData->order[0]->column);
        $_orderType     = $postData->order[0]->dir;
        $_kactan        = $postData->start;
        $_kacar         = $postData->length;
        $_kactan        = max(0, (int) $_kactan);
        $_kacar         = min(500, max(1, (int) $_kacar));

        //GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
        if (isset($_searchValue->ggTarih) === false || in_array($_searchValue->ggTur, array(-1, 0, 1, 2)) === false || in_array($_searchValue->ggKategori, array(-1, 0, 1, 2)) === false) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Sorgu Parametreleri Eksik. Lütfen Sistem Yöneticinize Başvurunuz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $tarihData = explode(" & ", (string)($_searchValue->ggTarih ?? ''));
        $_tarihStart    = dateToTimeFormat($tarihData[0] . " 00:00:00", "d-m-Y H:i:s");
        $_tarihStop     = dateToTimeFormat($tarihData[1] . " 23:59:59", "d-m-Y H:i:s");

        /*         
        $totalRecord = $this->gelengiden_model->ek_count_all_w_ow_win_like_olike(
            "r8t_edys_ggevrak",
            array(
                "gg_tarih >="   => trim(replaceHtml($_tarihStart)),
                "gg_tarih <="   => trim(replaceHtml($_tarihStop))
            ),
            array(),
            array(),
            array(),
            array()
        );

        */

        $sql = "";

        if ($_searchValue->ggTur == 0 || $_searchValue->ggTur == 1 || $_searchValue->ggTur == 2) {
            $sql .= " AND gg_tur = " . trim(replaceHtml($_searchValue->ggTur));
        }

        if ($_searchValue->ggKategori == 0 || $_searchValue->ggKategori == 1 || $_searchValue->ggKategori == 2) {
            $sql .= " AND gg_kategori = " . trim(replaceHtml($_searchValue->ggKategori));
        }

        $sql .= ")";
        $_ekVar = false;
        $_say = 0;
        $sqlEk = "";
        if (strlen(trim(replaceHtml($_searchValue->ggIlgili))) > 0) {
            $_ekVar = true;
            $_say++;
            $sqlEk .= "gg_kaynak LIKE '%" . trim(replaceHtml($_searchValue->ggIlgili)) . "%'";
        }
        if (strlen(trim(replaceHtml($_searchValue->ggSayi))) > 0) {
            $_ekVar = true;
            $_say++;
            if ($_say > 0) {
                $sqlEk .= " OR gg_sayi LIKE '%" . trim(replaceHtml($_searchValue->ggSayi)) . "%'";
            } else {
                $sqlEk .= "gg_sayi LIKE '%" . trim(replaceHtml($_searchValue->ggSayi)) . "%'";
            }
        }
        if (strlen(trim(replaceHtml($_searchValue->ggDosyaNo))) > 0) {
            $_ekVar = true;
            $_say++;
            if ($_say > 0) {
                $sqlEk .= " OR gg_dosyano LIKE '%" . trim(replaceHtml($_searchValue->ggDosyaNo)) . "%'";
            } else {
                $sqlEk .= "gg_dosyano LIKE '%" . trim(replaceHtml($_searchValue->ggDosyaNo)) . "%'";
            }
        }
        if (strlen(trim(replaceHtml($_searchValue->ggAciklama))) > 0) {
            $_ekVar = true;
            $_say++;
            if ($_say > 0) {
                $sqlEk .= " OR gg_aciklama LIKE '%" . trim(replaceHtml($_searchValue->ggAciklama)) . "%'";
            } else {
                $sqlEk .= "gg_aciklama LIKE '%" . trim(replaceHtml($_searchValue->ggAciklama)) . "%'";
            }
        }
        if (strlen(trim(replaceHtml($_searchValue->ggEtiket))) > 0) {
            $_ekVar = true;
            $_say++;
            if ($_say > 0) {
                $sqlEk .= " OR gg_tags LIKE '%" . trim(replaceHtml($_searchValue->ggEtiket)) . "%'";
            } else {
                $sqlEk .= "gg_tags LIKE '%" . trim(replaceHtml($_searchValue->ggEtiket)) . "%'";
            }
        }
        if (strlen(trim(replaceHtml($_searchValue->ggText))) > 0) {
            $_ekVar = true;
            $_say++;
            if ($_say > 0) {
                $sqlEk .= " OR gg_tarih LIKE '%" . trim(replaceHtml($_searchValue->ggText)) . "%' OR gg_id LIKE '%" . trim(replaceHtml($_searchValue->ggText)) . "%'";
            } else {
                $sqlEk .= "gg_tarih LIKE '%" . trim(replaceHtml($_searchValue->ggText)) . "%' OR gg_id LIKE '%" . trim(replaceHtml($_searchValue->ggText)) . "%'";
            }
        }

        if ($_ekVar == true) {
            $sql .= " AND (" . $sqlEk . ")";
        }

        $totalSql = "SELECT COUNT(gg_id) AS total FROM r8t_edys_ggevrak WHERE gg_status=-1 AND (gg_tarih >= " . trim(replaceHtml($_tarihStart)) . " AND gg_tarih <= " . trim(replaceHtml($_tarihStop)) . $sql;

        $totalRecordS = $this->gelengiden_model->ek_query_all($totalSql);

        $totalRecord = (int)$totalRecordS[0]->total;



        $sql .= " ORDER BY " . $_orderColumn . " " . strtoupper($_orderType);
        $sql .= " LIMIT " . $_kactan . ", " . $_kacar;

        $filterSql = "SELECT gg_id, gg_tarih, gg_kaynak, gg_tur, gg_sayi, gg_dosyano, gg_kategori, gg_tags, gg_aciklama FROM r8t_edys_ggevrak WHERE gg_status=-1 AND (gg_tarih >= " . trim(replaceHtml($_tarihStart)) . " AND gg_tarih <= " . trim(replaceHtml($_tarihStop)) . $sql;

        $filterRecord = $this->gelengiden_model->ek_query_all($filterSql);

        if (!$filterRecord) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Filtrelemeye Uygun Kayıt Bulunamadı.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $itemsData = array();
        foreach ($filterRecord as $item) {
            $itemArray = array(
                "_id"        => $item->gg_id,
                "_tarih"     => timeToDateFormat($item->gg_tarih, "d.m.Y"),
                "_ilgili"    => ggIlgiliYazdir($item->gg_kaynak),
                "_tur"       => ggTurYazdir($item->gg_tur),
                "_sayi"      => $item->gg_sayi,
                "_dosyano"   => $item->gg_dosyano,
                "_kategori"  => ggKategoriYazdir($item->gg_kategori),
                "_tags"      => ggTagYazdir($item->gg_tags),
                "_aciklama"  => $item->gg_aciklama,
            );
            array_push($itemsData, $itemArray);
        }

        if (count($itemsData) > 0) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = $totalRecord;
            $_sonuc->data                   = $itemsData;
            $_sonuc->success                = true;
            $_sonuc->code                   = 200;
            $_sonuc->description            = $totalRecord . " Adet Kayıt Bulundu.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Filtrelemeye Uygun Kayıt Bulunamadı.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }


    public function api_reejectdata()
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
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isAllowedViewApp("hedas")) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden HEDAS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isDbAllowedUpdateModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "HEDAS | Gelen-Giden Evrak Modülünde Güncelleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
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
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Sorgu Parametreleri Eksik. Lütfen Sistem Yöneticinize Başvurunuz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $getUpdate = $this->gelengiden_model->update(
            array(
                "gg_id" => (int)$postData->id,
                "gg_status" => -1
            ),
            array(
                "gg_status" => 1
            )
        );

        if ($getUpdate) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = true;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Kayıt Çöp Kutusundan Geri Alındı.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Kayıt Çöp Kutusundan Geri Alınamadı.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }

    ### TAGIFY SEARCH FONKSIYONLARI BASLANGIC #######
    public function api_FormKaynakSearch()
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
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
        //Kullanıcının Birimine Yada Kendi Şahsına Atanmış Uygulamayı Görüntüleme Yetkisi Varmı Kontrolü
        if (!isAllowedViewApp("hedas")) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden HEDAS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isDbAllowedListModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "HEDAS | GelenGiden Evrak Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        if (json_last_error() !== 0) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Eksik Parametre. Lutfen Api Bilgilerini Dogru Paremetre İle Gönderiniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
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
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        $searchText         = trim(replaceHtml_Slash($this->JSON_DATA->searchText));


        $sql = "SELECT gg_kaynak FROM r8t_edys_ggevrak WHERE gg_kaynak LIKE '%" . $searchText . "%' GROUP BY gg_kaynak";
        $items = $this->gelengiden_model->ek_query_all($sql);
        $itemText = "";
        if ($items) {
            foreach ($items as $item) {
                $kaynakX = explode("@", $item->gg_kaynak);
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
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Kayıt Bulunamadı.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }

    public function api_FormTagSearch()
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
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
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
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if ($this->userData === false) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Kullanıcı Yetkilendirme Hatası. Lütfen Giriş Bilgilerinizi Kontrol Ediniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
        //Kullanıcının Birimine Yada Kendi Şahsına Atanmış Uygulamayı Görüntüleme Yetkisi Varmı Kontrolü
        if (!isAllowedViewApp("hedas")) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden HEDAS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isDbAllowedListModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "HEDAS | GelenGiden Evrak Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
        $searchText         = trim(replaceHtml_Slash($this->JSON_DATA->searchText));

        if (strlen($searchText) > 0) {
            $sqlEk = " WHERE tag_isim LIKE '%" . $searchText . "%'";
        } else {
            $sqlEk = "";
        }

        $sql = "SELECT tag_isim FROM r8t_edys_tags" . $sqlEk . " GROUP BY tag_isim";
        $items = $this->gelengiden_model->ek_query_all($sql);
        $itemText = "";
        if ($items) {
            foreach ($items as $item) {
                $kaynakX = explode("@", $item->tag_isim);
                foreach ($kaynakX as $kaynak) {
                    if (strlen($kaynak) > 0 && stripos($itemText, $kaynak) === false) {
                        $itemText .= $kaynak . ",";
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
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Kayıt Bulunamadı.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        if (!$items) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Kayıtlı Etiket Bulunamadı.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }


    ### TAGIFY SEARCH FONKSIYONLARI BITIS #######


}
