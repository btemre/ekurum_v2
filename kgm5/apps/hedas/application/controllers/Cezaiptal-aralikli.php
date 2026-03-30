<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cezaiptal extends CI_Controller
{


    public $viewFolder     = "";
    public $userData        = false;

    public function __construct()
    {
        parent::__construct();

        $this->viewFolder = "cezaiptal_v";
        $this->load->model("cezaiptal_model");

        $this->load->model("auth_model");
        $this->userData = $this->auth_model->userData;
        $this->load->helper("cezaiptal");
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
                "text"  => "HEDAS | Ceza İptal Modülünü Görüntüleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(sys_url("home"));
            exit;
        }

        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "view";
        $viewData->userData             = $this->userData;
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
            $_sonuc->description            = "HEDAS | Ceza İptal Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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
        if (isset($_searchValue->ciAralik) === false || in_array($_searchValue->ciEvrakDurum, array(-1, 0, 1, 2, 3, 4, 5, 6, 7)) === false) {
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

        $tarihData = explode(" & ", (string)($_searchValue->ciAralik ?? ''));
        if (validDate($tarihData[0], "d-m-Y") == false || validDate($tarihData[1], "d-m-Y") == false) {
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
        $_tarihStart    = dateToTimeFormat($tarihData[0] . " 00:00:00", "d-m-Y H:i:s");
        $_tarihStop     = dateToTimeFormat($tarihData[1] . " 23:59:59", "d-m-Y H:i:s");




        $sql = "";
        $sqlEk = " ci_status=1 AND (ci_adddate >= " . trim(replaceHtml($_tarihStart)) . " AND ci_adddate <= " . trim(replaceHtml($_tarihStop)) . ")";

        if (strlen(trim(replaceHtml($_searchValue->ciText))) > 0) {
            $sqlEk .= " AND (ci_plaka LIKE '%" . trim(replaceHtml($_searchValue->ciText)) . "%'";
            $sqlEk .= " OR ci_esasno LIKE '%" . trim(replaceHtml($_searchValue->ciText)) . "%'";
            $sqlEk .= " OR ci_kararno LIKE '%" . trim(replaceHtml($_searchValue->ciText)) . "%'";
            $sqlEk .= " OR ci_cezaserino LIKE '%" . trim(replaceHtml($_searchValue->ciText)) . "%'";
            $sqlEk .= " OR ci_kurumdosyano LIKE '%" . trim(replaceHtml($_searchValue->ciText)) . "%'";
            $sqlEk .= " OR ci_itirazeden LIKE '%" . trim(replaceHtml($_searchValue->ciText)) . "%'";
            $sqlEk .= " OR ci_icra LIKE '%" . trim(replaceHtml($_searchValue->ciText)) . "%'";
            $sqlEk .= " OR ci_mahkeme LIKE '%" . trim(replaceHtml($_searchValue->ciText)) . "%'";
            $sqlEk .= " OR ci_cezakonu LIKE '%" . trim(replaceHtml($_searchValue->ciText)) . "%'";
            $sqlEk .= " OR ci_davakonu LIKE '%" . trim(replaceHtml($_searchValue->ciText)) . "%'";
            $sqlEk .= " OR ci_aciklama LIKE '%" . trim(replaceHtml($_searchValue->ciText)) . "%'";
            $sqlEk .= " OR ci_tags LIKE '%" . trim(replaceHtml($_searchValue->ciText)) . "%')";
        }

        if (in_array($_searchValue->ciEvrakDurum, array(0, 1, 2, 3, 4, 5, 6, 7)) === true) {
            $sqlEk .= " AND ci_evrakdurum LIKE '%" . (int)trim(replaceHtml($_searchValue->ciEvrakDurum)) . "%'";
        }

        if (trim(replaceHtml($_searchValue->ciFilter)) == true){
            $acilisDate = explode(" & ", (string)($_searchValue->ciAcilisTarih ?? ''));
            if(validDate($acilisDate[0], "d-m-Y") != false && validDate($acilisDate[1], "d-m-Y") != false) {
                $_acilisStart    = dateToTimeFormat($acilisDate[0] . " 00:00:00", "d-m-Y H:i:s");
                $_acilisStop     = dateToTimeFormat($acilisDate[1] . " 23:59:59", "d-m-Y H:i:s");
                $sqlEk .= " AND (ci_acilistarih >= " . trim(replaceHtml($_acilisStart)) . " AND ci_acilistarih <= " . trim(replaceHtml($_acilisStop)) . ")";
            }
        }

        if (trim(replaceHtml($_searchValue->ciFilter)) == true){
            $kararDate = explode(" & ", (string)($_searchValue->ciKararTarih ?? ''));
            if(validDate($kararDate[0], "d-m-Y") != false && validDate($kararDate[1], "d-m-Y") != false) {
                $_kararStart    = dateToTimeFormat($kararDate[0] . " 00:00:00", "d-m-Y H:i:s");
                $_kararStop     = dateToTimeFormat($kararDate[1] . " 23:59:59", "d-m-Y H:i:s");
                $sqlEk .= " AND (ci_karartarih >= " . trim(replaceHtml($_kararStart)) . " AND ci_karartarih <= " . trim(replaceHtml($_kararStop)) . ")";
            }
        }    


        $totalSql = "SELECT COUNT(ci_id) AS total FROM r8t_edys_cezaiptal WHERE" . $sqlEk;

        $totalRecordS = $this->cezaiptal_model->ek_query_all($totalSql);

        $totalRecord = (int)$totalRecordS[0]->total;
        $sql = "SELECT ci_id, ci_acilistarih, ci_cezakonu, ci_kurumdosyano, ci_itirazeden, ci_davakonu, ci_mahkeme, ci_esasno, ci_kararno, ci_karartarih, ci_plaka, ci_cezaserino, ci_evrakdurum, ci_icra, ci_tags, ci_aciklama FROM r8t_edys_cezaiptal WHERE" . $sqlEk;
        $sql .= " ORDER BY " . $_orderColumn . " " . strtoupper($_orderType);
        $sql .= " LIMIT " . $_kactan . ", " . $_kacar;

        $filterRecord = $this->cezaiptal_model->ek_query_all($sql);

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
                "_id"           => $item->ci_id,
                "_acilis"       => timeToDateFormat($item->ci_acilistarih, "d.m.Y"),
                "_cezakonu"     => ciAyracsizYazdir($item->ci_cezakonu),
                "_kurumdosyano" => ($item->ci_kurumdosyano),
                "_itirazeden"   => ciAyracsizYazdir($item->ci_itirazeden),
                "_davakonu"     => ciAyracsizYazdir($item->ci_davakonu),
                "_mahkeme"      => ciAyracsizYazdir($item->ci_mahkeme),
                "_esasno"       => ($item->ci_esasno),
                "_kararno"      => $item->ci_kararno,
                "_karartarih"   => timeToDateFormat($item->ci_karartarih, "d.m.Y"),
                "_plaka"        => ciAyracsizYazdir($item->ci_plaka),
                "_cezaserino"   => $item->ci_cezaserino,
                "_durum"        => ciDurumYazdir($item->ci_evrakdurum),
                "_icra"         => ciAyracsizYazdir($item->ci_icra),
                "_tags"         => ciTagYazdir($item->ci_tags),
                "_aciklama"     => html_entity_decode($item->ci_aciklama, ENT_QUOTES)
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

    private function getTableColumnName($data = 0)
    {
        $columnName = "ci_id";
        switch ($data) {

            case 0:
                $columnName = "ci_acilistarih";
                break;
            case 1:
                $columnName = "ci_cezakonu";
                break;
            case 2:
                $columnName = "ci_kurumdosyano";
                break;
            case 3:
                $columnName = "ci_itirazeden";
                break;
            case 4:
                $columnName = "ci_davakonu";
                break;
            case 5:
                $columnName = "ci_mahkeme";
                break;
            case 6:
                $columnName = "ci_esasno";
                break;
            case 7:
                $columnName = "ci_kararno";
                break;
            case 8:
                $columnName = "ci_karartarih";
                break;
            case 9:
                $columnName = "ci_plaka";
                break;
            case 10:
                $columnName = "ci_cezaserino";
                break;
            case 11:
                $columnName = "ci_evrakdurum";
                break;
            case 12:
                $columnName = "ci_icra";
                break;
            case 13:
                $columnName = "ci_tags";
                break;
            case 14:
                $columnName = "ci_aciklama";
                break;
            default:
                $columnName = "ci_id";
                break;
        }
        return $columnName;
    }

    public function api_mahkemeler()
    {

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

        if (!isDbAllowedListModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "HEDAS | Ceza İptal Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $items = $this->cezaiptal_model->ek_get_all(
            "r8t_sys_mahkemeler",
            array()
        );

        if (!$items) {
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

        $_item = "";
        foreach ($items as $item) {
            $_item .= $item->mh_name . ",";
        }

        $_sonuc =  new stdClass();
        $_sonuc->data                   = $_item;
        $_sonuc->success                = true;
        $_sonuc->code                   = 200;
        $_sonuc->description            = "Kayıt Bulundu.";
        $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
        SetHeader($_sonuc->code);
        echo $sonuc;
        exit;
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
            $_sonuc->description        = "HEDAS | Ceza İptal Modülünde Ekleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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
        if (isset($this->JSON_DATA->route) == false || isset($this->JSON_DATA->ciDosyaNo) == false) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if ($this->JSON_DATA->route != "addcezaiptal") {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Yönlendirilen Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $_ciDosyaNo             = trim(replaceHtml_Slash($this->JSON_DATA->ciDosyaNo));
        $_ciItirazEden          = str_replace("[{", "", $this->JSON_DATA->ciItirazEden);
        $_ciItirazEden          = str_replace("}]", "", $_ciItirazEden);
        $_ciItirazEden          = str_replace("},{", "", $_ciItirazEden);
        $_ciItirazEden          = str_replace('"', "", $_ciItirazEden);
        $_ciAcilisTarihi        = trim(replaceHtml($this->JSON_DATA->ciAcilisTarih));
        $_ciMahkeme             = str_replace("[{", "", $this->JSON_DATA->ciMahkeme);
        $_ciMahkeme             = str_replace("}]", "", $_ciMahkeme);
        $_ciMahkeme             = str_replace("},{", "", $_ciMahkeme);
        $_ciMahkeme             = str_replace('"', "", $_ciMahkeme);
        $_ciDavaKonusu          = str_replace("[{", "", $this->JSON_DATA->ciDavaKonusu);
        $_ciDavaKonusu          = str_replace("}]", "", $_ciDavaKonusu);
        $_ciDavaKonusu          = str_replace("},{", "", $_ciDavaKonusu);
        $_ciDavaKonusu          = str_replace('"', "", $_ciDavaKonusu);
        $_ciCezaKonusu          = str_replace("[{", "", $this->JSON_DATA->ciCezaKonusu);
        $_ciCezaKonusu          = str_replace("}]", "", $_ciCezaKonusu);
        $_ciCezaKonusu          = str_replace("},{", "", $_ciCezaKonusu);
        $_ciCezaKonusu          = str_replace('"', "", $_ciCezaKonusu);
        $_ciEsasNo             = trim(replaceHtml_Slash($this->JSON_DATA->ciEsasNo));
        $_ciKararNo             = trim(replaceHtml_Slash($this->JSON_DATA->ciKararNo));
        $_ciKararTarihi        = trim(replaceHtml($this->JSON_DATA->ciKararTarihi));
        $_ciPlaka             = str_replace("[{", "", $this->JSON_DATA->ciPlaka);
        $_ciPlaka          = str_replace("}]", "", $_ciPlaka);
        $_ciPlaka          = str_replace("},{", "", $_ciPlaka);
        $_ciPlaka          = str_replace('"', "", $_ciPlaka);
        $_ciSeriNo             = trim(replaceHtml_Slash($this->JSON_DATA->ciSeriNo));
        $_ciEvrakDurum             = (int)$this->JSON_DATA->ciEvrakDurum;
        $_ciIcra          = str_replace("[{", "", $this->JSON_DATA->ciIcra);
        $_ciIcra          = str_replace("}]", "", $_ciIcra);
        $_ciIcra          = str_replace("},{", "", $_ciIcra);
        $_ciIcra          = str_replace('"', "", $_ciIcra);
        $_ciAciklama             = trim(replaceHtml_Slash($this->JSON_DATA->ciAciklama));
        $_ciTags            = str_replace("[{", "", $this->JSON_DATA->ciTags);
        $_ciTags          = str_replace("}]", "", $_ciTags);
        $_ciTags          = str_replace("},{", "", $_ciTags);
        $_ciTags          = str_replace('"', "", $_ciTags);

        $itirazeden = explode("value:", (string)($_ciItirazEden ?? ''));
        $mahkeme = explode("value:", (string)($_ciMahkeme ?? ''));
        $davakonusu = explode("value:", (string)($_ciDavaKonusu ?? ''));
        $cezakonusu = explode("value:", (string)($_ciCezaKonusu ?? ''));
        $cezaplaka = explode("value:", (string)($_ciPlaka ?? ''));
        $icra = explode("value:", (string)($_ciIcra ?? ''));
        $etiket = explode("value:", (string)($_ciTags ?? ''));
        $itirazedenText = tagifyToText($itirazeden);
        $mahkemeText = tagifyToText($mahkeme);
        $davakonusuText = tagifyToText($davakonusu);
        $cezakonusuText = tagifyToText($cezakonusu);
        $plakaText = tagifyToTextToUpper($cezaplaka);
        $icraText = tagifyToText($icra);
        $etiketText = tagifyToText($etiket);

        if (strlen($_ciKararTarihi) > 5) {
            $_ciKararTarihi1 = dateToTimeFormat($_ciKararTarihi . " 00:00:00", "d-m-Y H:i:s");
        } else {
            $_ciKararTarihi1 = "";
        }



        $add = $this->cezaiptal_model->add(
            array(
                "ci_plaka"        => $plakaText,
                "ci_esasno"     => $_ciEsasNo,
                "ci_kararno"   => $_ciKararNo,
                "ci_cezaserino"      => $_ciSeriNo,
                "ci_kurumdosyano"       => $_ciDosyaNo,
                "ci_evrakdurum"    => $_ciEvrakDurum,
                "ci_itirazeden"   => $itirazedenText,
                "ci_icra"       => $icraText,
                "ci_mahkeme"     => $mahkemeText,
                "ci_cezakonu"    => $cezakonusuText,
                "ci_davakonu"    => $davakonusuText,
                "ci_aciklama"   => $_ciAciklama,
                "ci_acilistarih"       => dateToTimeFormat($_ciAcilisTarihi . " 00:00:00", "d-m-Y H:i:s"),
                "ci_karartarih"     => $_ciKararTarihi1, //dateToTimeFormat($_ciKararTarihi . " 00:00:00", "d-m-Y H:i:s"),
                "ci_tags"    => $etiketText,
                "ci_status"    => 1,
                "ci_adddate"    => dateToTimeFormat(date('Y-m-d H:i:s'), "Y-m-d H:i:s"),
                "ci_adduser"    => $this->userData->userB->u_id

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
            $_sonuc->description        = "HEDAS | Ceza İptal Modülünde Güncelleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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
        if (isset($this->JSON_DATA->route) == false || isset($this->JSON_DATA->ciDosyaNo) == false) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if ($this->JSON_DATA->route != "editcezaiptal") {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Yönlendirilen Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $_ciId                  = (int)$this->JSON_DATA->ciId;
        $_ciDosyaNo             = trim(replaceHtml_Slash($this->JSON_DATA->ciDosyaNo));
        $_ciItirazEden          = str_replace("[{", "", $this->JSON_DATA->ciItirazEden);
        $_ciItirazEden          = str_replace("}]", "", $_ciItirazEden);
        $_ciItirazEden          = str_replace("},{", "", $_ciItirazEden);
        $_ciItirazEden          = str_replace('"', "", $_ciItirazEden);
        $_ciAcilisTarihi        = trim(replaceHtml($this->JSON_DATA->ciAcilisTarih));
        $_ciMahkeme             = str_replace("[{", "", $this->JSON_DATA->ciMahkeme);
        $_ciMahkeme             = str_replace("}]", "", $_ciMahkeme);
        $_ciMahkeme             = str_replace("},{", "", $_ciMahkeme);
        $_ciMahkeme             = str_replace('"', "", $_ciMahkeme);
        $_ciDavaKonusu          = str_replace("[{", "", $this->JSON_DATA->ciDavaKonusu);
        $_ciDavaKonusu          = str_replace("}]", "", $_ciDavaKonusu);
        $_ciDavaKonusu          = str_replace("},{", "", $_ciDavaKonusu);
        $_ciDavaKonusu          = str_replace('"', "", $_ciDavaKonusu);
        $_ciCezaKonusu          = str_replace("[{", "", $this->JSON_DATA->ciCezaKonusu);
        $_ciCezaKonusu          = str_replace("}]", "", $_ciCezaKonusu);
        $_ciCezaKonusu          = str_replace("},{", "", $_ciCezaKonusu);
        $_ciCezaKonusu          = str_replace('"', "", $_ciCezaKonusu);
        $_ciEsasNo              = trim(replaceHtml_Slash($this->JSON_DATA->ciEsasNo));
        $_ciKararNo             = trim(replaceHtml_Slash($this->JSON_DATA->ciKararNo));
        $_ciKararTarihi         = trim(replaceHtml($this->JSON_DATA->ciKararTarihi));
        $_ciPlaka               = str_replace("[{", "", $this->JSON_DATA->ciPlaka);
        $_ciPlaka               = str_replace("}]", "", $_ciPlaka);
        $_ciPlaka               = str_replace("},{", "", $_ciPlaka);
        $_ciPlaka               = str_replace('"', "", $_ciPlaka);
        $_ciSeriNo              = trim(replaceHtml_Slash($this->JSON_DATA->ciSeriNo));
        $_ciEvrakDurum          = (int)$this->JSON_DATA->ciEvrakDurum;
        $_ciIcra                = str_replace("[{", "", $this->JSON_DATA->ciIcra);
        $_ciIcra                = str_replace("}]", "", $_ciIcra);
        $_ciIcra                = str_replace("},{", "", $_ciIcra);
        $_ciIcra                = str_replace('"', "", $_ciIcra);
        $_ciAciklama            = trim(replaceHtml_Slash($this->JSON_DATA->ciAciklama));
        $_ciTags                = str_replace("[{", "", $this->JSON_DATA->ciTags);
        $_ciTags                = str_replace("}]", "", $_ciTags);
        $_ciTags                = str_replace("},{", "", $_ciTags);
        $_ciTags                = str_replace('"', "", $_ciTags);

        $itirazeden     = explode("value:", (string)($_ciItirazEden ?? ''));
        $mahkeme        = explode("value:", (string)($_ciMahkeme ?? ''));
        $davakonusu     = explode("value:", (string)($_ciDavaKonusu ?? ''));
        $cezakonusu     = explode("value:", (string)($_ciCezaKonusu ?? ''));
        $cezaplaka      = explode("value:", (string)($_ciPlaka ?? ''));
        $icra           = explode("value:", (string)($_ciIcra ?? ''));
        $etiket         = explode("value:", (string)($_ciTags ?? ''));
        $itirazedenText = tagifyToText($itirazeden);
        $mahkemeText    = tagifyToText($mahkeme);
        $davakonusuText = tagifyToText($davakonusu);
        $cezakonusuText = tagifyToText($cezakonusu);
        $plakaText      = tagifyToTextToUpper($cezaplaka);
        $icraText       = tagifyToText($icra);
        $etiketText     = tagifyToText($etiket);


        if (strlen($_ciKararTarihi) > 5) {
            $_ciKararTarihi1 = dateToTimeFormat($_ciKararTarihi . " 00:00:00", "d-m-Y H:i:s");
        } else {
            $_ciKararTarihi1 = "";
        }


        $update = $this->cezaiptal_model->update(
            array(
                "ci_id"         => $_ciId
            ),
            array(
                "ci_plaka"        => $plakaText,
                "ci_esasno"     => $_ciEsasNo,
                "ci_kararno"   => $_ciKararNo,
                "ci_cezaserino"      => $_ciSeriNo,
                "ci_kurumdosyano"       => $_ciDosyaNo,
                "ci_evrakdurum"    => $_ciEvrakDurum,
                "ci_itirazeden"   => $itirazedenText,
                "ci_icra"       => $icraText,
                "ci_mahkeme"     => $mahkemeText,
                "ci_cezakonu"    => $cezakonusuText,
                "ci_davakonu"    => $davakonusuText,
                "ci_aciklama"   => $_ciAciklama,
                "ci_acilistarih"       => dateToTimeFormat($_ciAcilisTarihi . " 00:00:00", "d-m-Y H:i:s"),
                "ci_karartarih"     => $_ciKararTarihi1, //dateToTimeFormat($_ciKararTarihi . " 00:00:00", "d-m-Y H:i:s"),
                "ci_tags"    => $etiketText,
                "ci_editdate"    => dateToTimeFormat(date('Y-m-d H:i:s'), "Y-m-d H:i:s"),
                "ci_edituser"    => $this->userData->userB->u_id
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


    public function api_getBasvuru()
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
            $_sonuc->description            = "HEDAS | Ceza İptal Modülünde Görüntüleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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

        $getRecord = $this->cezaiptal_model->ek_get(
            "r8t_edys_cezaiptal",
            array(
                "ci_id" => (int)$postData->id
            )
        );

        if ($getRecord) {
            $itemArray = array(
                "_id"        => $getRecord->ci_id,
                "_acilistarihi"     => timeToDateFormat($getRecord->ci_acilistarih, "d-m-Y"),
                "_karartarihi"     => timeToDateFormat($getRecord->ci_karartarih, "d-m-Y"),
                "_itirazeden"    => ciEditAyracliYazdir($getRecord->ci_itirazeden),
                "_evrakdurum"       => $getRecord->ci_evrakdurum,
                "_mahkeme"      => ciEditAyracliYazdir($getRecord->ci_mahkeme),
                "_dosyano"   => $getRecord->ci_kurumdosyano,
                "_davakonusu"  => ciEditAyracliYazdir($getRecord->ci_davakonu),
                "_cezakonusu"  => ciEditAyracliYazdir($getRecord->ci_cezakonu),
                "_esasno"  => $getRecord->ci_esasno,
                "_kararno"  => $getRecord->ci_kararno,
                "_plaka"  => ciEditAyracliYazdir($getRecord->ci_plaka),
                "_serino"  => $getRecord->ci_cezaserino,
                "_icra"  => ciEditAyracliYazdir($getRecord->ci_icra),
                "_tags"      => ciEditTagYazdir($getRecord->ci_tags),
                "_aciklama"  => $getRecord->ci_aciklama
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
            $_sonuc->description            = "HEDAS | Ceza İptal Modülünde Güncelleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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

        $getUpdate = $this->cezaiptal_model->update(
            array(
                "ci_id" => (int)$postData->id
            ),
            array(
                "ci_status" => -1
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

    #### ARCHIVE FONKSİYONLARI #######
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

        if (!isDbAllowedListModule()) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "HEDAS | Ceza İptal Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.",
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
            $_sonuc->description            = "HEDAS | Ceza İptal Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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
        if (isset($_searchValue->ciAralik) === false || in_array($_searchValue->ciEvrakDurum, array(-1, 0, 1, 2, 3, 4, 5, 6, 7)) === false) {
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

        $tarihData = explode(" & ", (string)($_searchValue->ciAralik ?? ''));
        if (validDate($tarihData[0], "d-m-Y") == false || validDate($tarihData[1], "d-m-Y") == false) {
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
        $_tarihStart    = dateToTimeFormat($tarihData[0] . " 00:00:00", "d-m-Y H:i:s");
        $_tarihStop     = dateToTimeFormat($tarihData[1] . " 23:59:59", "d-m-Y H:i:s");




        $sql = "";
        $sqlEk = " ci_status=-1 AND (ci_adddate >= " . trim(replaceHtml($_tarihStart)) . " AND ci_adddate <= " . trim(replaceHtml($_tarihStop)) . ")";

        if (strlen(trim(replaceHtml($_searchValue->ciText))) > 0) {
            $sqlEk .= " AND (ci_plaka LIKE '%" . trim(replaceHtml($_searchValue->ciText)) . "%'";
            $sqlEk .= " OR ci_esasno LIKE '%" . trim(replaceHtml($_searchValue->ciText)) . "%'";
            $sqlEk .= " OR ci_kararno LIKE '%" . trim(replaceHtml($_searchValue->ciText)) . "%'";
            $sqlEk .= " OR ci_cezaserino LIKE '%" . trim(replaceHtml($_searchValue->ciText)) . "%'";
            $sqlEk .= " OR ci_kurumdosyano LIKE '%" . trim(replaceHtml($_searchValue->ciText)) . "%'";
            $sqlEk .= " OR ci_itirazeden LIKE '%" . trim(replaceHtml($_searchValue->ciText)) . "%'";
            $sqlEk .= " OR ci_icra LIKE '%" . trim(replaceHtml($_searchValue->ciText)) . "%'";
            $sqlEk .= " OR ci_mahkeme LIKE '%" . trim(replaceHtml($_searchValue->ciText)) . "%'";
            $sqlEk .= " OR ci_cezakonu LIKE '%" . trim(replaceHtml($_searchValue->ciText)) . "%'";
            $sqlEk .= " OR ci_davakonu LIKE '%" . trim(replaceHtml($_searchValue->ciText)) . "%'";
            $sqlEk .= " OR ci_aciklama LIKE '%" . trim(replaceHtml($_searchValue->ciText)) . "%'";
            $sqlEk .= " OR ci_tags LIKE '%" . trim(replaceHtml($_searchValue->ciText)) . "%')";
        }

        if (in_array($_searchValue->ciEvrakDurum, array(0, 1, 2, 3, 4, 5, 6, 7)) === true) {
            $sqlEk .= " AND ci_evrakdurum LIKE '%" . (int)trim(replaceHtml($_searchValue->ciEvrakDurum)) . "%'";
        }

        $acilisDate = explode(" & ", (string)($_searchValue->ciAcilisTarih ?? ''));
        if (validDate($acilisDate[0], "d-m-Y") != false && validDate($acilisDate[1], "d-m-Y") != false) {
            $_acilisStart    = dateToTimeFormat($acilisDate[0] . " 00:00:00", "d-m-Y H:i:s");
            $_acilisStop     = dateToTimeFormat($acilisDate[1] . " 23:59:59", "d-m-Y H:i:s");
            $sqlEk .= " AND (ci_acilistarih >= " . trim(replaceHtml($_acilisStart)) . " AND ci_acilistarih <= " . trim(replaceHtml($_acilisStop)) . ")";
        }

        $kararDate = explode(" & ", (string)($_searchValue->ciKararTarih ?? ''));
        if (validDate($kararDate[0], "d-m-Y") != false && validDate($kararDate[1], "d-m-Y") != false) {
            $_kararStart    = dateToTimeFormat($kararDate[0] . " 00:00:00", "d-m-Y H:i:s");
            $_kararStop     = dateToTimeFormat($kararDate[1] . " 23:59:59", "d-m-Y H:i:s");
            $sqlEk .= " AND (ci_karartarih >= " . trim(replaceHtml($_kararStart)) . " AND ci_karartarih <= " . trim(replaceHtml($_kararStop)) . ")";
        }


        $totalSql = "SELECT COUNT(ci_id) AS total FROM r8t_edys_cezaiptal WHERE" . $sqlEk;

        $totalRecordS = $this->cezaiptal_model->ek_query_all($totalSql);

        $totalRecord = (int)$totalRecordS[0]->total;
        $sql = "SELECT ci_id, ci_acilistarih, ci_cezakonu, ci_kurumdosyano, ci_itirazeden, ci_davakonu, ci_mahkeme, ci_esasno, ci_kararno, ci_karartarih, ci_plaka, ci_cezaserino, ci_evrakdurum, ci_icra, ci_tags, ci_aciklama FROM r8t_edys_cezaiptal WHERE" . $sqlEk;
        $sql .= " ORDER BY " . $_orderColumn . " " . strtoupper($_orderType);
        $sql .= " LIMIT " . $_kactan . ", " . $_kacar;

        $filterRecord = $this->cezaiptal_model->ek_query_all($sql);

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
                "_id"           => $item->ci_id,
                "_acilis"       => timeToDateFormat($item->ci_acilistarih, "d.m.Y"),
                "_cezakonu"     => ciAyracsizYazdir($item->ci_cezakonu),
                "_kurumdosyano" => ($item->ci_kurumdosyano),
                "_itirazeden"   => ciAyracsizYazdir($item->ci_itirazeden),
                "_davakonu"     => ciAyracsizYazdir($item->ci_davakonu),
                "_mahkeme"      => ciAyracsizYazdir($item->ci_mahkeme),
                "_esasno"       => ($item->ci_esasno),
                "_kararno"      => $item->ci_kararno,
                "_karartarih"   => timeToDateFormat($item->ci_karartarih, "d.m.Y"),
                "_plaka"        => ciAyracsizYazdir($item->ci_plaka),
                "_cezaserino"   => $item->ci_cezaserino,
                "_durum"        => ciDurumYazdir($item->ci_evrakdurum),
                "_icra"         => ciAyracsizYazdir($item->ci_icra),
                "_tags"         => ciTagYazdir($item->ci_tags),
                "_aciklama"     => $item->ci_aciklama
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
            $_sonuc->description            = "HEDAS | Ceza İptal Modülünde Güncelleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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

        $getUpdate = $this->cezaiptal_model->update(
            array(
                "ci_id" => (int)$postData->id,
                "ci_status" => -1
            ),
            array(
                "ci_status" => 1
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
    #### ARCHIVE FONKSİYONLARI #######


    ### TAGIFY SEARCH FONKSIYONLARI BASLANGIC #######
    public function api_FormItirazciSearch()
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
            $_sonuc->description        = "HEDAS | Cezaiptal Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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

        $sql = "SELECT ci_itirazeden FROM r8t_edys_cezaiptal WHERE ci_itirazeden LIKE '%" . $searchText . "%' GROUP BY ci_itirazeden";
        $items = $this->cezaiptal_model->ek_query_all($sql);
        $itemText = "";
        if ($items) {
            foreach ($items as $item) {
                $kaynakX = explode("@", $item->ci_itirazeden);
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
    public function api_FormDavaKonusuSearch()
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
            $_sonuc->description        = "HEDAS | Cezaiptal Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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

        $sql = "SELECT ci_davakonu FROM r8t_edys_cezaiptal WHERE ci_davakonu LIKE '%" . $searchText . "%' GROUP BY ci_davakonu";
        $items = $this->cezaiptal_model->ek_query_all($sql);
        $itemText = "";
        if ($items) {
            foreach ($items as $item) {
                $kaynakX = explode("@", $item->ci_davakonu);
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
    public function api_FormCezaKonusuSearch()
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
            $_sonuc->description        = "HEDAS | Cezaiptal Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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

        $sql = "SELECT ci_cezakonu FROM r8t_edys_cezaiptal WHERE ci_cezakonu LIKE '%" . $searchText . "%' GROUP BY ci_cezakonu";
        $items = $this->cezaiptal_model->ek_query_all($sql);
        $itemText = "";
        if ($items) {
            foreach ($items as $item) {
                $kaynakX = explode("@", $item->ci_cezakonu);
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
    public function api_FormPlakaSearch()
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
            $_sonuc->description        = "HEDAS | Cezaiptal Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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

        $sql = "SELECT ci_plaka FROM r8t_edys_cezaiptal WHERE ci_plaka LIKE '%" . $searchText . "%' GROUP BY ci_plaka";
        $items = $this->cezaiptal_model->ek_query_all($sql);
        $itemText = "";
        if ($items) {
            foreach ($items as $item) {
                $kaynakX = explode("@", $item->ci_plaka);
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

        if (!isDbAllowedListModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "HEDAS | Cezaiptal Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        $searchText         = trim(replaceHtml_Slash($this->JSON_DATA->searchText));


        $sql = "SELECT mh_name FROM r8t_sys_mahkemeler WHERE mh_name LIKE '%" . $searchText . "%' GROUP BY mh_name";
        $items = $this->cezaiptal_model->ek_query_all($sql);
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
            $_sonuc->description        = "HEDAS | Cezaiptal Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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
        $items = $this->cezaiptal_model->ek_query_all($sql);
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
