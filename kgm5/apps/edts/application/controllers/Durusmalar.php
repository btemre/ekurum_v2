<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Durusmalar extends CI_Controller
{


    public $viewFolder     = "";
    public $userData        = false;

    public function __construct()
    {
        parent::__construct();

        $this->viewFolder = "durusmalar_v";
        $this->load->model("durusmalar_model");
        $this->load->model("mahkemeler_model");
        $this->load->helper("durusmalar");

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
                "text"  => "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden EDTS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(sys_url("home"));
            exit;
        }

        if (!isDbAllowedListModule()) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "EDTS | Duruşmalar Modülünü Görüntüleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(sys_url("home"));
            exit;
        }
        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->moduleName     = $this->uri->segment(1);
        $viewData->subViewFolder  = ($viewData->moduleName === 'dashboard') ? 'dashboard' : 'list';
        $viewData->userData       = $this->userData;

        if ($viewData->moduleName === 'dashboard' && isAllowedViewApp("edts")) {
            $this->load->model("notes_model");
            $userId = isset($this->userData->userB->u_id) ? (int) $this->userData->userB->u_id : 0;
            $viewData->dashboardNotes = $userId ? $this->notes_model->getForDashboard($userId) : array('reminders' => array(), 'last_note' => null);
            $viewData->dashboardReminderCount = isset($viewData->dashboardNotes['reminders']) ? count($viewData->dashboardNotes['reminders']) : 0;
            $tzIst = new DateTimeZone('Europe/Istanbul');
            $nowIst = new DateTime('now', $tzIst);
            $todayStart = (new DateTime($nowIst->format('Y-m-d') . ' 00:00:00', $tzIst))->getTimestamp();
            $todayEnd = (new DateTime($nowIst->format('Y-m-d') . ' 23:59:59', $tzIst))->getTimestamp();
            $todayRow = $this->durusmalar_model->ek_query("SELECT COUNT(*) AS cnt FROM r8t_edts_durusmalar WHERE d_status=1 AND d_durusmatarihi >= " . $todayStart . " AND d_durusmatarihi <= " . $todayEnd);
            $viewData->dashboardTodayCount = $todayRow && isset($todayRow->cnt) ? (int) $todayRow->cnt : 0;
            $lawyerCounts = $this->durusmalar_model->ek_query_all("SELECT d_avukat, COUNT(*) AS cnt FROM r8t_edts_durusmalar WHERE d_status=1 AND d_durusmatarihi >= " . $todayStart . " AND d_durusmatarihi <= " . $todayEnd . " AND d_avukatid IS NOT NULL AND d_avukatid != '' GROUP BY d_avukatid ORDER BY cnt DESC");
            $viewData->dashboardTodayByLawyer = is_array($lawyerCounts) ? $lawyerCounts : array();
            $courtCounts = $this->durusmalar_model->ek_query_all("SELECT d_mahkeme, COUNT(*) AS cnt FROM r8t_edts_durusmalar WHERE d_status=1 AND d_durusmatarihi >= " . $todayStart . " AND d_durusmatarihi <= " . $todayEnd . " GROUP BY d_mahkeme ORDER BY cnt DESC");
            $viewData->dashboardTodayByCourt = is_array($courtCounts) ? $courtCounts : array();
            $tarafCounts = $this->durusmalar_model->ek_query_all("SELECT d_taraf, COUNT(*) AS cnt FROM r8t_edts_durusmalar WHERE d_status=1 AND d_durusmatarihi >= " . $todayStart . " AND d_durusmatarihi <= " . $todayEnd . " AND TRIM(IFNULL(d_taraf,'')) != '' GROUP BY d_taraf ORDER BY cnt DESC");
            $viewData->dashboardTodayByTaraf = is_array($tarafCounts) ? $tarafCounts : array();
            $islemCounts = $this->durusmalar_model->ek_query_all("SELECT d_islem, COUNT(*) AS cnt FROM r8t_edts_durusmalar WHERE d_status=1 AND d_durusmatarihi >= " . $todayStart . " AND d_durusmatarihi <= " . $todayEnd . " AND TRIM(IFNULL(d_islem,'')) != '' GROUP BY d_islem ORDER BY cnt DESC");
            $viewData->dashboardTodayByIslem = is_array($islemCounts) ? $islemCounts : array();
        } else {
            $viewData->dashboardNotes = array('reminders' => array(), 'last_note' => null);
        }

        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }
    public function ara()
    {

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

        if (!isDbAllowedListModule()) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "EDTS | Duruşmalar Modülünü Görüntüleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(sys_url("home"));
            exit;
        }
        $durusmalartum = $this->durusmalar_model->durusmalarTum();

        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "ara";
        $viewData->userData             = $this->userData;
        $viewData->durusmalartum = $durusmalartum
        ;
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }
    private function getTableColumnName($data = 0)
    {
        $columnName = "d_id";
        switch ($data) {
            case 0:
                $columnName = "d_esasno";
                break;
            case 1:
                $columnName = "d_mahkeme";
                break;
            case 2:
                $columnName = "d_dosyano";
                break;
            case 3:
                $columnName = "d_durusmatarihi";
                break;
            case 4:
                $columnName = "d_avukat";
                break;
            case 5:
                $columnName = "d_memur";
                break;
            case 6:
                $columnName = "d_dosyaturu";
                break;
            case 7:
                $columnName = "d_taraf";
                break;
            case 8:
                $columnName = "d_islem";
                break;
            case 9:
                $columnName = "d_tarafbilgisi";
                break;
            case 10:
                $columnName = "d_takip";
                break;
            case 11:
                $columnName = "d_tutanak";
                break;
            case 12:
                    $columnName = "d_tags";
                    break;
            case 13:
                $columnName = "d_adddate";
                break;
            default:
                $columnName = "d_id";
                break;
        }
        return $columnName;
    }
    private static $_ft_ok = null;

    private function _buildTextSearch($searchText)
    {
        $escaped = $this->db->escape_str($searchText);
        $ftCols = 'd_dosyaturu, d_mahkeme, d_esasno, d_tarafbilgisi, d_memur, d_avukat, d_islem, d_dosyano, d_taraf, d_aciklama, d_ilgiliavukatlar, d_ilgilimemurlar, d_tags';
        if (self::$_ft_ok !== false) {
            return "MATCH(" . $ftCols . ") AGAINST('" . $escaped . "' IN BOOLEAN MODE)";
        }
        return "d_dosyaturu LIKE '%" . $escaped . "%' OR d_mahkeme LIKE '%" . $escaped . "%' OR d_esasno LIKE '%" . $escaped . "%' OR d_tarafbilgisi LIKE '%" . $escaped . "%' OR d_memur LIKE '%" . $escaped . "%' OR d_avukat LIKE '%" . $escaped . "%' OR d_islem LIKE '%" . $escaped . "%' OR d_dosyano LIKE '%" . $escaped . "%' OR d_taraf LIKE '%" . $escaped . "%' OR d_aciklama LIKE '%" . $escaped . "%' OR d_ilgiliavukatlar LIKE '%" . $escaped . "%' OR d_ilgilimemurlar LIKE '%" . $escaped . "%' OR d_tags LIKE '%" . $escaped . "%'";
    }

    private function _safeQueryAll($sql, $hasTextSearch = false)
    {
        $result = @$this->db->query($sql);
        if ($result === false && $hasTextSearch && self::$_ft_ok === null) {
            self::$_ft_ok = false;
            return null;
        }
        if ($result !== false && $hasTextSearch && self::$_ft_ok === null) {
            self::$_ft_ok = true;
        }
        return $result ? $result->result() : array();
    }

    public function api_list()
    {

        if ($this->userData === false) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
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
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
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
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "EDTS | Dosyalar Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $_post          = json_encode($this->input->post());
        $postData       = json_decode($_post);
        
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

        $this->load->helper('api_list_cache');
        $userId = isset($this->userData->userB->u_id) ? (int) $this->userData->userB->u_id : 0;
        if ($userId > 0 && !api_list_rate_limit_allowed('edts_list', $userId)) {
            api_list_send_rate_limit_response();
        }
        $cacheKey = api_list_cache_key('edts_list', $userId, $this->input->post());
        $cached = $userId > 0 ? api_list_cache_get($cacheKey) : false;
        if ($cached !== false) {
            header('Content-Type: application/json; charset=utf-8');
            SetHeader(200);
            echo $cached;
            exit;
        }

        try {

        $_searchValue   = isset($postData->search->value) ? json_decode($postData->search->value) : new stdClass();
        if (!$_searchValue) $_searchValue = new stdClass();
        if (!isset($_searchValue->dEklemeTarihi) || empty($_searchValue->dEklemeTarihi)) {
            $_searchValue->dEklemeTarihi = date('d-m-Y', strtotime('-6 years')) . ' & ' . date('d-m-Y');
        }
        if (!isset($_searchValue->dDurusmaAralik) || empty($_searchValue->dDurusmaAralik)) {
            $_searchValue->dDurusmaAralik = date('d-m-Y H:i', strtotime('-6 years')) . ' & ' . date('d-m-Y H:i', strtotime('+6 years'));
        }
        if (!isset($_searchValue->dListTur)) $_searchValue->dListTur = 'filter';
        if (!isset($_searchValue->dMemurId)) $_searchValue->dMemurId = -1;
        if (!isset($_searchValue->dAvukatId)) $_searchValue->dAvukatId = -1;
        if (!isset($_searchValue->dMahkemeId)) $_searchValue->dMahkemeId = -1;
        if (!isset($_searchValue->dIslem)) $_searchValue->dIslem = -1;
        if (!isset($_searchValue->dEsasNo)) $_searchValue->dEsasNo = '';
        if (!isset($_searchValue->dTarafBilgisi)) $_searchValue->dTarafBilgisi = '';
        if (!isset($_searchValue->dTaraf)) $_searchValue->dTaraf = -1;
        if (!isset($_searchValue->dTutanak)) $_searchValue->dTutanak = -1;
        if (!isset($_searchValue->dTakip)) $_searchValue->dTakip = -1;
        if (!isset($_searchValue->dText)) $_searchValue->dText = '';
        if (!isset($_searchValue->dDosyaNo)) $_searchValue->dDosyaNo = '';
        if (!isset($_searchValue->dTags)) $_searchValue->dTags = '';
        $_orderColumn   = $this->getTableColumnName($postData->order[0]->column);
        $_orderType     = $postData->order[0]->dir;
        $_kactan        = isset($postData->start) ? $postData->start : 0;
        $_kacar         = isset($postData->length) ? $postData->length : 10;
        $_kactan        = max(0, (int) $_kactan);
        $_kacar         = min(500, max(1, (int) $_kacar));


        //GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
        if (isset($_orderType) === false || in_array($_orderType, array("desc", "asc")) === false) {
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


        $mainModuleCustom=(!empty($_searchValue->mainModuleCustom))?$_searchValue->mainModuleCustom:"";
        $subModuleCustom=(!empty($_searchValue->subModuleCustom))?$_searchValue->subModuleCustom:"";


        $DurusmalarimActive=($mainModuleCustom=="durusmalar" and $subModuleCustom=="durusmalarim")?1:0;
        $DashboardActive=($mainModuleCustom=="dashboard")?1:0;
        
        
        if ($DurusmalarimActive) {
            $myId = $this->userData->userB->u_id;
            $keyWord=(empty($_searchValue->dText))?1:0;
            
            if ($keyWord)
                $_searchValue->dMemurId=$myId;
        }

        if ($DashboardActive) {
            $thisDate=date("d-m-Y H:i:s");
            $todayExact=$thisDate." 00:00 & ".$thisDate." 23:59";

//            $_searchValue->dDurusmaAralik=$todayExact;
            
            
            
            $keyWord=(empty($_searchValue->dText))?1:0;
            if ($keyWord)
                $_searchValue->dListTur="filter";
        }



        $tarihData = @explode(" & ", $_searchValue->dEklemeTarihi);
        $_tarihStart    = dateToTimeFormat($tarihData[0] . " 00:00:00", "d-m-Y H:i:s");
        $_tarihStop     = dateToTimeFormat($tarihData[1] . " 23:59:59", "d-m-Y H:i:s");

        $tarihDataDurusma = @explode(" & ", $_searchValue->dDurusmaAralik);
        $_tarihStartDurusma    = dateToTimeFormat($tarihDataDurusma[0] . ":00", "d-m-Y H:i:s");
        $_tarihStopDurusma     = dateToTimeFormat($tarihDataDurusma[1] . ":59", "d-m-Y H:i:s");
     
     

        if($_searchValue->dListTur=="ara"){
            $listTur = "ara";
        }else{
            $listTur = "filter";
        }
        

        $sql = "";
        $_isEk = false;
        $_say=0;
        $sqlEk = "";

        $_likeIsEk = false;
        $_sayLike = 0;
        $likeEk = "";

        if ($_searchValue->dMemurId != -1 && $_searchValue->dMemurId) {

            $andTxt=($_say>0)?" AND ":"";
            $sqlEk .= $andTxt." d_memurid in (" . trim(replaceHtml($_searchValue->dMemurId)).")";

            $_isEk = true;
            $_say++;
        }

        if ($_searchValue->dAvukatId != -1 && $_searchValue->dAvukatId) {
            $andTxt=($_say>0)?" AND ":"";
            $sqlEk .= $andTxt." d_avukatid in (" . trim(replaceHtml($_searchValue->dAvukatId)).")";

            $_isEk = true;
            $_say++;
        }

        
        if ($_searchValue->dMahkemeId != -1 && $_searchValue->dMahkemeId) {
            $andTxt=($_say>0)?" AND ":"";
            $mahkemeLists=explode(",",$_searchValue->dMahkemeId);
            $mahkemeExtraAdd="";
            $mhkIdx=0;
            foreach ($mahkemeLists as $km0=>$vm0) {
                $vm0=trim($vm0);
                if (empty($vm0)) continue;
                $dMahkemeText=FormSelectMahkemeList($vm0);
                $dMahkemeText=trim($dMahkemeText);
                $andMahkemeTxt=($mhkIdx>0)?" OR ":"";
                $mahkemeExtraAdd .= $andMahkemeTxt." d_mahkeme = '" . $this->db->escape_str(trim(replaceHtml_Slash($dMahkemeText)))."'";
                $mhkIdx++;
            }

            $sqlEk .= $andTxt."( $mahkemeExtraAdd )";

            $_isEk = true;
            $_say++;
        }



        if ($_searchValue->dIslem != -1 && strlen($_searchValue->dIslem) > 0) {
            $andTxt=($_say>0)?" AND ":"";
            $mahkemeLists=explode(",",$_searchValue->dIslem);
            $mahkemeExtraAdd="";
            $mhkIdx=0;
            foreach ($mahkemeLists as $km0=>$vm0) {
                $vm0=trim($vm0);
                if (empty($vm0)) continue;
                $andMahkemeTxt=($mhkIdx>0)?" OR ":"";
                $mahkemeExtraAdd .= $andMahkemeTxt." d_islem = '" . $this->db->escape_str(trim($vm0))."'";
                $mhkIdx++;
            }

            $sqlEk .= $andTxt."( $mahkemeExtraAdd )";

            $_isEk = true;
            $_say++;
        }


        if (strlen(trim(replaceHtml_Slash($_searchValue->dEsasNo))) > 0) {
            if ($_say > 0) {
                $sqlEk .= " AND d_esasno = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dEsasNo)))."'";
            } else {
                $sqlEk .= "d_esasno = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dEsasNo)))."'";
            }
            $_isEk = true;
            $_say++;
        }

        if (strlen(trim(replaceHtml_Slash($_searchValue->dDosyaNo))) > 0) {
            if ($_say > 0) {
                $sqlEk .= " AND d_dosyano = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dDosyaNo)))."'";
            } else {
                $sqlEk .= "d_dosyano = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dDosyaNo)))."'";
            }
            $_isEk = true;
            $_say++;
        }

        if (strlen(trim(replaceHtml_Slash($_searchValue->dTarafBilgisi))) > 0) {
            if ($_say > 0) {
                $sqlEk .= " AND d_tarafbilgisi = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dTarafBilgisi)))."'";
            } else {
                $sqlEk .= "d_tarafbilgisi = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dTarafBilgisi)))."'";
            }
            $_isEk = true;
            $_say++;
        }

        if (strlen(trim(replaceHtml_Slash($_searchValue->dTaraf))) > 0 AND $_searchValue->dTaraf!=-1) {
            if ($_say > 0) {
                $sqlEk .= " AND d_taraf = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dTaraf)))."'";
            } else {
                $sqlEk .= "d_taraf = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dTaraf)))."'";
            }
            $_isEk = true;
            $_say++;
        }

        if (strlen(trim(replaceHtml_Slash($_searchValue->dTutanak))) > 0 AND $_searchValue->dTutanak!=-1) {
            if ($_say > 0) {
                $sqlEk .= " AND d_tutanak = " . (int)trim(replaceHtml_Slash($_searchValue->dTutanak));
            } else {
                $sqlEk .= "d_tutanak =" . (int)trim(replaceHtml_Slash($_searchValue->dTutanak));
            }
            $_isEk = true;
            $_say++;
        }

        if (strlen(trim(replaceHtml_Slash($_searchValue->dTakip))) > 0 AND $_searchValue->dTakip!=-1) {
            if ($_say > 0) {
                $sqlEk .= " AND d_takip = " . (int)trim(replaceHtml_Slash($_searchValue->dTakip));
            } else {
                $sqlEk .= "d_takip =" . (int)trim(replaceHtml_Slash($_searchValue->dTakip));
            }
            $_isEk = true;
            $_say++;
        }




        if (strlen(trim(replaceHtml_Slash($_searchValue->dText))) > 0) {
            $gelenText = trim(replaceHtml_Slash($_searchValue->dText));
            $likeEk .= $this->_buildTextSearch($gelenText);
            $_likeIsEk = true;
            $_sayLike++;
        }

        if ($_likeIsEk == true) {
            $sql .= " AND (" . $likeEk . ")";
        }

        if ($_isEk == true) {
            $sql .= " AND (" . $sqlEk . ")";
        }

        $_selectCols = "d_id, d_dosyano, d_dosyaturu, d_mahkeme, d_durusmatarihi, d_esasno, d_memur, d_memurid, d_tarafbilgisi, d_islem, d_avukat, d_avukatid, d_taraf, d_tags, d_adddate, d_ilgilimemurlar, d_ilgiliavukatlar, d_takip, d_tutanak";

        if($listTur=="filter"){
            $_whereBase = "d_status=1";
            $_tStart = (int)trim(replaceHtml($_tarihStart));
            $_tStop = (int)trim(replaceHtml($_tarihStop));
            $_dStart = (int)trim(replaceHtml($_tarihStartDurusma));
            $_dStop = (int)trim(replaceHtml($_tarihStopDurusma));
            if (($_tStop - $_tStart) < 157680000) {
                $_whereBase .= " AND d_adddate >= " . $_tStart . " AND d_adddate <= " . $_tStop;
            }
            if (($_dStop - $_dStart) < 315360000) {
                $_whereBase .= " AND (d_durusmatarihi IS NULL OR (d_durusmatarihi >= " . $_dStart . " AND d_durusmatarihi <= " . $_dStop . "))";
            }
        }else{
            $_whereBase = "d_status=1";
        }

        $_t0 = microtime(true);

        $_hasText = ($_likeIsEk == true);
        $filterSql = "SELECT " . $_selectCols . " FROM r8t_edts_durusmalar WHERE " . $_whereBase . $sql . " ORDER BY " . $_orderColumn . " " . strtoupper($_orderType) . " LIMIT " . $_kactan . ", " . $_kacar;
        $filterRecord = $this->_safeQueryAll($filterSql, $_hasText);
        if ($filterRecord === null && $_hasText) {
            $sql = str_replace(
                $likeEk,
                $this->_buildTextSearch(trim(replaceHtml_Slash($_searchValue->dText))),
                $sql
            );
            $filterSql = "SELECT " . $_selectCols . " FROM r8t_edts_durusmalar WHERE " . $_whereBase . $sql . " ORDER BY " . $_orderColumn . " " . strtoupper($_orderType) . " LIMIT " . $_kactan . ", " . $_kacar;
            $filterRecord = $this->durusmalar_model->ek_query_all($filterSql);
        }

        $_t1 = microtime(true);

        $returnedCount = $filterRecord ? count($filterRecord) : 0;
        if ($returnedCount < (int)$_kacar) {
            $totalRecord = (int)$_kactan + $returnedCount;
        } else {
            $countSql = "SELECT COUNT(*) AS total FROM r8t_edts_durusmalar WHERE " . $_whereBase . $sql;
            $totalRecordS = $this->durusmalar_model->ek_query($countSql);
            $totalRecord = $totalRecordS ? (int)$totalRecordS->total : 0;
        }

        $_t2 = microtime(true);

        if (!$filterRecord) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Filtrelemeye Uygun Kayıt Bulunamadı.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        $countByEsasno = array();
        $esasnoList = array_filter(array_unique(array_map(function ($r) { return $r->d_esasno; }, $filterRecord)));
        if (!empty($esasnoList)) {
            $escaped = array_map(function ($e) { return "'" . $this->db->escape_str($e) . "'"; }, $esasnoList);
            $countSql = "SELECT d_esasno, COUNT(*) AS cnt FROM r8t_edts_durusmalar WHERE d_status=1 AND d_esasno IN (" . implode(',', $escaped) . ") GROUP BY d_esasno";
            $countRows = $this->durusmalar_model->ek_query_all($countSql);
            if ($countRows) {
                foreach ($countRows as $cr) {
                    $countByEsasno[$cr->d_esasno] = (int) $cr->cnt;
                }
            }
        }

        $countByDosyano = array();
        $dosyanoList = array_filter(array_unique(array_map(function ($r) { return $r->d_dosyano; }, $filterRecord)));
        if (!empty($dosyanoList)) {
            $escapedD = array_map(function ($e) { return "'" . $this->db->escape_str($e) . "'"; }, $dosyanoList);
            $countSqlD = "SELECT d_dosyano, COUNT(*) AS cnt FROM r8t_edts_durusmalar WHERE d_status=1 AND d_dosyano IN (" . implode(',', $escapedD) . ") GROUP BY d_dosyano";
            $countRowsD = $this->durusmalar_model->ek_query_all($countSqlD);
            if ($countRowsD) {
                foreach ($countRowsD as $cr) {
                    $countByDosyano[$cr->d_dosyano] = (int) $cr->cnt;
                }
            }
        }

        $itemsData = array();
        foreach ($filterRecord as $item) {

            $itemArray = array(
                "_id"           => $item->d_id,
                "_dosyano"      => $item->d_dosyano,
                "_dosyano_count" => isset($countByDosyano[$item->d_dosyano]) ? $countByDosyano[$item->d_dosyano] : 0,
                "_dosyaturu"    => etAyracsizYazdir($item->d_dosyaturu),
                "_mahkeme"      => etAyracsizYazdir($item->d_mahkeme),
                "_durusmatarihi" => timeToDateFormat($item->d_durusmatarihi, "d.m.Y H:i"),
                "_esasno"       => $item->d_esasno,
                "_esasno_count" => isset($countByEsasno[$item->d_esasno]) ? $countByEsasno[$item->d_esasno] : 0,
                "_memur"        => $item->d_memur,
                "_tarafbilgisi" => $item->d_tarafbilgisi,
                "_islem"        => etAyracsizYazdir($item->d_islem),
                "_memurid"      => $item->d_memurid,
                "_avukat"       => $item->d_avukat,
                "_avukatid"     => $item->d_avukatid,
                "_taraf"        => $item->d_taraf,
                "_tags"         => drsTagYazdir($item->d_tags),
                "_aciklama"     => '',
                "_eklenmetarihi" => timeToDateFormat($item->d_adddate, "d.m.Y H:i"),
                "_ilgilimemur"  => etAyracsizYazdir($item->d_ilgilimemurlar),
                "_ilgiliavukat" => etAyracsizYazdir($item->d_ilgiliavukatlar),
                "_takip"        => drsTakipBilgisiYaz($item->d_takip),
                "_tutanak"      => drsTutanakBilgisiYaz($item->d_tutanak)
            );
            $itemsData[] = $itemArray;
        }

        $_t3 = microtime(true);

        if (count($itemsData) > 0) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = $totalRecord;
            $_sonuc->data                   = $itemsData;
            $_sonuc->success                = true;
            $_sonuc->code                   = 200;
            $_sonuc->description            = $totalRecord . " Adet Kayıt Bulundu.";
            $_sonuc->_perf = array('select_ms' => round(($_t1-$_t0)*1000), 'count_ms' => round(($_t2-$_t1)*1000), 'php_ms' => round(($_t3-$_t2)*1000), 'total_ms' => round(($_t3-$_t0)*1000));
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            if ($userId > 0) {
                api_list_cache_set($cacheKey, $sonuc);
            }
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Filtrelemeye Uygun Kayıt Bulunamadı.";
            $_sonuc->_perf = array('select_ms' => round(($_t1-$_t0)*1000), 'count_ms' => round(($_t2-$_t1)*1000), 'php_ms' => round(($_t3-$_t2)*1000), 'total_ms' => round(($_t3-$_t0)*1000));
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            if ($userId > 0) {
                api_list_cache_set($cacheKey, $sonuc);
            }
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        } catch (Throwable $e) {
            log_message('error', 'Durusmalar::api_list 5xx: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            $err = new stdClass();
            $err->recordsTotal = 0;
            $err->recordsFiltered = 0;
            $err->data = array();
            $err->success = false;
            $err->code = 503;
            $err->description = 'Geçici bir sunucu hatası oluştu. Lütfen kısa süre sonra tekrar deneyin.';
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(503);
            echo json_encode($err, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    /**
     * Export list as CSV or Excel (CSV content).
     * POST: export_type = "filtered"|"full", format = "excel"|"csv", filters = JSON object (same as api_list search value).
     * No _esasno_count / _dosyano_count; full text in all columns.
     */
    public function api_list_export()
    {
        if ($this->userData === false) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(array('success' => false, 'description' => 'Kullanıcı Yetkilendirme Hatası.'));
            exit;
        }
        if (!isAllowedViewApp("edts")) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(array('success' => false, 'description' => 'Uygulama Yetkilendirme Hatası.'));
            exit;
        }
        if (!isDbAllowedListModule()) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(array('success' => false, 'description' => 'EDTS | Listeleme Yetkiniz Bulunmuyor.'));
            exit;
        }

        $export_type = $this->input->post('export_type');
        $format = $this->input->post('format');
        if (!in_array($export_type, array('filtered', 'full'), true) || !in_array($format, array('excel', 'csv', 'pdf', 'print'), true)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(array('success' => false, 'description' => 'Geçersiz export_type veya format.'));
            exit;
        }

        $_whereBase = "d_status=1";
        $sql = "";
        $_orderColumn = "d_adddate";
        $_orderType = "DESC";
        $_hasText = false;

        if ($export_type === 'filtered') {
            $filtersRaw = $this->input->post('filters');
            $_searchValue = is_string($filtersRaw) ? json_decode($filtersRaw) : $filtersRaw;
            if (!$_searchValue) $_searchValue = new stdClass();

            if (!isset($_searchValue->dEklemeTarihi) || empty($_searchValue->dEklemeTarihi)) {
                $_searchValue->dEklemeTarihi = date('d-m-Y', strtotime('-6 years')) . ' & ' . date('d-m-Y');
            }
            if (!isset($_searchValue->dDurusmaAralik) || empty($_searchValue->dDurusmaAralik)) {
                $_searchValue->dDurusmaAralik = date('d-m-Y H:i', strtotime('-6 years')) . ' & ' . date('d-m-Y H:i', strtotime('+6 years'));
            }
            if (!isset($_searchValue->dListTur)) $_searchValue->dListTur = 'filter';
            if (!isset($_searchValue->dMemurId)) $_searchValue->dMemurId = -1;
            if (!isset($_searchValue->dAvukatId)) $_searchValue->dAvukatId = -1;
            if (!isset($_searchValue->dMahkemeId)) $_searchValue->dMahkemeId = -1;
            if (!isset($_searchValue->dIslem)) $_searchValue->dIslem = -1;
            if (!isset($_searchValue->dEsasNo)) $_searchValue->dEsasNo = '';
            if (!isset($_searchValue->dTarafBilgisi)) $_searchValue->dTarafBilgisi = '';
            if (!isset($_searchValue->dTaraf)) $_searchValue->dTaraf = -1;
            if (!isset($_searchValue->dTutanak)) $_searchValue->dTutanak = -1;
            if (!isset($_searchValue->dTakip)) $_searchValue->dTakip = -1;
            if (!isset($_searchValue->dText)) $_searchValue->dText = '';
            if (!isset($_searchValue->dDosyaNo)) $_searchValue->dDosyaNo = '';
            if (!isset($_searchValue->dTags)) $_searchValue->dTags = '';

            $mainModuleCustom = !empty($_searchValue->mainModuleCustom) ? $_searchValue->mainModuleCustom : "";
            $subModuleCustom = !empty($_searchValue->subModuleCustom) ? $_searchValue->subModuleCustom : "";
            $DurusmalarimActive = ($mainModuleCustom === "durusmalar" && $subModuleCustom === "durusmalarim") ? 1 : 0;
            $DashboardActive = ($mainModuleCustom === "dashboard") ? 1 : 0;

            if ($DurusmalarimActive) {
                $myId = $this->userData->userB->u_id;
                if (empty($_searchValue->dText)) $_searchValue->dMemurId = $myId;
            }
            if ($DashboardActive && empty($_searchValue->dText)) {
                $_searchValue->dListTur = "filter";
            }

            $tarihData = @explode(" & ", $_searchValue->dEklemeTarihi);
            $_tarihStart = dateToTimeFormat($tarihData[0] . " 00:00:00", "d-m-Y H:i:s");
            $_tarihStop = dateToTimeFormat($tarihData[1] . " 23:59:59", "d-m-Y H:i:s");
            $tarihDataDurusma = @explode(" & ", $_searchValue->dDurusmaAralik);
            $_tarihStartDurusma = dateToTimeFormat($tarihDataDurusma[0] . ":00", "d-m-Y H:i:s");
            $_tarihStopDurusma = dateToTimeFormat($tarihDataDurusma[1] . ":59", "d-m-Y H:i:s");

            $listTur = (isset($_searchValue->dListTur) && $_searchValue->dListTur === "ara") ? "ara" : "filter";

            $_isEk = false;
            $_say = 0;
            $sqlEk = "";
            $_likeIsEk = false;
            $_sayLike = 0;
            $likeEk = "";

            if (isset($_searchValue->dMemurId) && $_searchValue->dMemurId != -1 && $_searchValue->dMemurId !== '') {
                $sqlEk .= " d_memurid in (" . trim(replaceHtml($_searchValue->dMemurId)) . ")";
                $_isEk = true;
                $_say++;
            }
            if (isset($_searchValue->dAvukatId) && $_searchValue->dAvukatId != -1 && $_searchValue->dAvukatId !== '') {
                $andTxt = $_say > 0 ? " AND " : "";
                $sqlEk .= $andTxt . " d_avukatid in (" . trim(replaceHtml($_searchValue->dAvukatId)) . ")";
                $_isEk = true;
                $_say++;
            }
            if (isset($_searchValue->dMahkemeId) && $_searchValue->dMahkemeId != -1 && $_searchValue->dMahkemeId !== '') {
                $andTxt = $_say > 0 ? " AND " : "";
                $mahkemeLists = explode(",", $_searchValue->dMahkemeId);
                $mahkemeExtraAdd = "";
                $mhkIdx = 0;
                foreach ($mahkemeLists as $vm0) {
                    $vm0 = trim($vm0);
                    if ($vm0 === '') continue;
                    $dMahkemeText = FormSelectMahkemeList($vm0);
                    $dMahkemeText = trim($dMahkemeText);
                    $andMahkemeTxt = $mhkIdx > 0 ? " OR " : "";
                    $mahkemeExtraAdd .= $andMahkemeTxt . " d_mahkeme = '" . $this->db->escape_str(trim(replaceHtml_Slash($dMahkemeText))) . "'";
                    $mhkIdx++;
                }
                if ($mahkemeExtraAdd !== '') {
                    $sqlEk .= $andTxt . "(" . $mahkemeExtraAdd . ")";
                    $_isEk = true;
                    $_say++;
                }
            }
            if (isset($_searchValue->dIslem) && $_searchValue->dIslem != -1 && strlen($_searchValue->dIslem) > 0) {
                $andTxt = $_say > 0 ? " AND " : "";
                $islemList = explode(",", $_searchValue->dIslem);
                $islemExtraAdd = "";
                $idx = 0;
                foreach ($islemList as $vm0) {
                    $vm0 = trim($vm0);
                    if ($vm0 === '') continue;
                    $islemExtraAdd .= ($idx > 0 ? " OR " : "") . " d_islem = '" . $this->db->escape_str(trim($vm0)) . "'";
                    $idx++;
                }
                if ($islemExtraAdd !== '') {
                    $sqlEk .= $andTxt . "(" . $islemExtraAdd . ")";
                    $_isEk = true;
                    $_say++;
                }
            }
            if (strlen(trim(replaceHtml_Slash($_searchValue->dEsasNo))) > 0) {
                $andTxt = $_say > 0 ? " AND " : "";
                $sqlEk .= $andTxt . " d_esasno = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dEsasNo))) . "'";
                $_isEk = true;
                $_say++;
            }
            if (strlen(trim(replaceHtml_Slash($_searchValue->dDosyaNo))) > 0) {
                $andTxt = $_say > 0 ? " AND " : "";
                $sqlEk .= $andTxt . " d_dosyano = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dDosyaNo))) . "'";
                $_isEk = true;
                $_say++;
            }
            if (strlen(trim(replaceHtml_Slash($_searchValue->dTarafBilgisi))) > 0) {
                $andTxt = $_say > 0 ? " AND " : "";
                $sqlEk .= $andTxt . " d_tarafbilgisi = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dTarafBilgisi))) . "'";
                $_isEk = true;
                $_say++;
            }
            if (isset($_searchValue->dTaraf) && strlen(trim(replaceHtml_Slash($_searchValue->dTaraf))) > 0 && $_searchValue->dTaraf != -1) {
                $andTxt = $_say > 0 ? " AND " : "";
                $sqlEk .= $andTxt . " d_taraf = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dTaraf))) . "'";
                $_isEk = true;
                $_say++;
            }
            if (isset($_searchValue->dTutanak) && $_searchValue->dTutanak != -1 && strlen(trim(replaceHtml_Slash($_searchValue->dTutanak))) > 0) {
                $andTxt = $_say > 0 ? " AND " : "";
                $sqlEk .= $andTxt . " d_tutanak = " . (int)trim(replaceHtml_Slash($_searchValue->dTutanak));
                $_isEk = true;
                $_say++;
            }
            if (isset($_searchValue->dTakip) && $_searchValue->dTakip != -1 && strlen(trim(replaceHtml_Slash($_searchValue->dTakip))) > 0) {
                $andTxt = $_say > 0 ? " AND " : "";
                $sqlEk .= $andTxt . " d_takip = " . (int)trim(replaceHtml_Slash($_searchValue->dTakip));
                $_isEk = true;
                $_say++;
            }
            if (strlen(trim(replaceHtml_Slash($_searchValue->dText))) > 0) {
                $gelenText = trim(replaceHtml_Slash($_searchValue->dText));
                $likeEk = $this->_buildTextSearch($gelenText);
                $_likeIsEk = true;
            }
            if ($_likeIsEk) $sql .= " AND (" . $likeEk . ")";
            if ($_isEk) $sql .= " AND (" . $sqlEk . ")";

            if ($listTur === "filter") {
                $_tStart = (int)trim(replaceHtml($_tarihStart));
                $_tStop = (int)trim(replaceHtml($_tarihStop));
                $_dStart = (int)trim(replaceHtml($_tarihStartDurusma));
                $_dStop = (int)trim(replaceHtml($_tarihStopDurusma));
                if (($_tStop - $_tStart) < 157680000) {
                    $_whereBase .= " AND d_adddate >= " . $_tStart . " AND d_adddate <= " . $_tStop;
                }
                if (($_dStop - $_dStart) < 315360000) {
                    $_whereBase .= " AND (d_durusmatarihi IS NULL OR (d_durusmatarihi >= " . $_dStart . " AND d_durusmatarihi <= " . $_dStop . "))";
                }
            }
            $_hasText = $_likeIsEk;
        }

        $_selectCols = "d_id, d_dosyano, d_dosyaturu, d_mahkeme, d_durusmatarihi, d_esasno, d_memur, d_memurid, d_tarafbilgisi, d_islem, d_avukat, d_avukatid, d_taraf, d_tags, d_adddate, d_ilgilimemurlar, d_ilgiliavukatlar, d_takip, d_tutanak";
        $exportSql = "SELECT " . $_selectCols . " FROM r8t_edts_durusmalar WHERE " . $_whereBase . $sql . " ORDER BY " . $_orderColumn . " " . $_orderType;
        $filterRecord = $this->_safeQueryAll($exportSql, $_hasText);
        if ($filterRecord === null && $_hasText && $export_type === 'filtered' && isset($_searchValue) && strlen(trim(replaceHtml_Slash($_searchValue->dText))) > 0) {
            $likeEkNew = $this->_buildTextSearch(trim(replaceHtml_Slash($_searchValue->dText)));
            $sql = str_replace($likeEk, $likeEkNew, $sql);
            $exportSql = "SELECT " . $_selectCols . " FROM r8t_edts_durusmalar WHERE " . $_whereBase . $sql . " ORDER BY " . $_orderColumn . " " . $_orderType;
            $filterRecord = $this->durusmalar_model->ek_query_all($exportSql);
        }
        if (!$filterRecord) $filterRecord = array();

        // Excel/CSV için HTML etiketlerini kaldır (sadece düz metin)
        $exportPlain = function ($v) {
            if ($v === null || $v === '') return '';
            $v = html_entity_decode((string)$v, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $v = strip_tags($v);
            return trim(preg_replace('/\s+/', ' ', $v));
        };

        $headers = array('Esas No', 'Mahkeme', 'Dosya No', 'Duruşma Tarihi', 'Avukat', 'Memur', 'Dosya Türü', 'Taraf', 'İşlem', 'Taraf Bilgisi', 'D.Takip', 'D.Tutanak', 'Etiketler', 'Eklenme Tarihi', 'İlgili Memur', 'İlgili Avukat');
        $rows = array();
        foreach ($filterRecord as $item) {
            $rows[] = array(
                $exportPlain($item->d_esasno !== null ? $item->d_esasno : ''),
                $exportPlain(etAyracsizYazdir($item->d_mahkeme)),
                $exportPlain($item->d_dosyano !== null ? $item->d_dosyano : ''),
                timeToDateFormat($item->d_durusmatarihi, "d.m.Y H:i"),
                $exportPlain($item->d_avukat !== null ? $item->d_avukat : ''),
                $exportPlain($item->d_memur !== null ? $item->d_memur : ''),
                $exportPlain(etAyracsizYazdir($item->d_dosyaturu)),
                $exportPlain($item->d_taraf !== null ? $item->d_taraf : ''),
                $exportPlain(etAyracsizYazdir($item->d_islem)),
                $exportPlain($item->d_tarafbilgisi !== null ? $item->d_tarafbilgisi : ''),
                $exportPlain(drsTakipBilgisiYaz($item->d_takip)),
                $exportPlain(drsTutanakBilgisiYaz($item->d_tutanak)),
                $exportPlain(drsTagYazdir($item->d_tags)),
                timeToDateFormat($item->d_adddate, "d.m.Y H:i"),
                $exportPlain(etAyracsizYazdir($item->d_ilgilimemurlar)),
                $exportPlain(etAyracsizYazdir($item->d_ilgiliavukatlar))
            );
        }

        if ($format === 'pdf' || $format === 'print') {
            $title = ($export_type === 'full' ? 'Tam Liste' : 'Filtreli Liste') . ' - Duruşmalar';
            $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>' . htmlspecialchars($title) . '</title>';
            $html .= '<style>body{font-family:DejaVu Sans,sans-serif;font-size:10px;margin:12px;}';
            $html .= 'table{border-collapse:collapse;width:100%;}th,td{border:1px solid #333;padding:4px 6px;text-align:left;}th{background:#eee;}';
            $html .= '@media print{body{margin:0;} table{page-break-inside:auto;} tr{page-break-inside:avoid;}}</style></head><body>';
            $html .= '<h1 style="font-size:14px;margin-bottom:8px;">' . htmlspecialchars($title) . '</h1>';
            $html .= '<p style="margin-bottom:8px;">Toplam: ' . count($rows) . ' kayıt. Tarih: ' . date('d.m.Y H:i') . '</p>';
            $html .= '<table><thead><tr>';
            foreach ($headers as $h) {
                $html .= '<th>' . htmlspecialchars($h) . '</th>';
            }
            $html .= '</tr></thead><tbody>';
            foreach ($rows as $row) {
                $html .= '<tr>';
                foreach ($row as $cell) {
                    $html .= '<td>' . htmlspecialchars((string)$cell) . '</td>';
                }
                $html .= '</tr>';
            }
            $html .= '</tbody></table></body></html>';
            header('Content-Type: text/html; charset=UTF-8');
            header('Cache-Control: max-age=0');
            echo $html;
            exit;
        }

        $sep = ';';
        $csv = "\xEF\xBB\xBF";
        $csv .= implode($sep, array_map(function ($h) use ($sep) {
            return '"' . str_replace('"', '""', $h) . '"';
        }, $headers)) . "\r\n";
        foreach ($rows as $row) {
            $csv .= implode($sep, array_map(function ($v) use ($sep) {
                $v = (string)$v;
                return '"' . str_replace('"', '""', $v) . '"';
            }, $row)) . "\r\n";
        }

        $ext = $format === 'excel' ? 'xlsx' : 'csv';
        $mime = $format === 'excel' ? 'application/vnd.ms-excel' : 'text/csv; charset=UTF-8';
        $filename = 'durusmalar_export_' . date('Ymd_Hi') . '.' . $ext;
        header('Content-Type: ' . $mime . '; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        echo $csv;
        exit;
    }

    public function api_listDashboard()
    {
        if ($this->userData === false) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
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
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
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
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "EDTS | Dosyalar Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $_post          = json_encode($this->input->post());
        $postData       = json_decode($_post);
        
        // DataTables POST verisi kontrolü (api_listDashboard)
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
        
        // Filtre: global search veya ilk sütunun search value'sundan (DataTables sütun araması columns[0][search][value] ile gelir)
        $_searchValue   = isset($postData->search->value) ? json_decode($postData->search->value) : null;
        if (!$_searchValue && isset($postData->columns[0]->search->value)) {
            $_searchValue = json_decode($postData->columns[0]->search->value);
        }
        if (!$_searchValue || !is_object($_searchValue)) $_searchValue = new stdClass();
        if (!isset($_searchValue->dEklemeTarihi) || empty($_searchValue->dEklemeTarihi)) {
            $_searchValue->dEklemeTarihi = date('d-m-Y', strtotime('-6 years')) . ' & ' . date('d-m-Y');
        }
        if (!isset($_searchValue->dDurusmaAralik) || empty($_searchValue->dDurusmaAralik)) {
            $_searchValue->dDurusmaAralik = date('d-m-Y H:i', strtotime('-6 years')) . ' & ' . date('d-m-Y H:i', strtotime('+6 years'));
        }
        if (!isset($_searchValue->dMemurId)) $_searchValue->dMemurId = -1;
        if (!isset($_searchValue->dAvukatId)) $_searchValue->dAvukatId = -1;
        if (!isset($_searchValue->dMahkemeId)) $_searchValue->dMahkemeId = -1;
        if (!isset($_searchValue->dIslem)) $_searchValue->dIslem = -1;
        if (!isset($_searchValue->dEsasNo)) $_searchValue->dEsasNo = '';
        if (!isset($_searchValue->dTarafBilgisi)) $_searchValue->dTarafBilgisi = '';
        if (!isset($_searchValue->dTaraf)) $_searchValue->dTaraf = -1;
        if (!isset($_searchValue->dTutanak)) $_searchValue->dTutanak = -1;
        if (!isset($_searchValue->dTakip)) $_searchValue->dTakip = -1;
        if (!isset($_searchValue->dText)) $_searchValue->dText = '';
        // api_listDashboard: dashboard filtresinde olmayan alanlar için varsayılan (Undefined property hatası önlemi)
        if (!isset($_searchValue->dDosyaNo)) $_searchValue->dDosyaNo = '';
        if (!isset($_searchValue->dDosyaTuru)) $_searchValue->dDosyaTuru = '';
        if (!isset($_searchValue->dMahkeme)) $_searchValue->dMahkeme = '';
        if (!isset($_searchValue->dDurusmaTarihi)) $_searchValue->dDurusmaTarihi = '';
        if (!isset($_searchValue->dIslem)) $_searchValue->dIslem = -1;
        if (!isset($_searchValue->dMemur)) $_searchValue->dMemur = '';
        if (!isset($_searchValue->dAvukat)) $_searchValue->dAvukat = '';
        
        $_orderColumn   = $this->getTableColumnName($postData->order[0]->column);
        $_orderType     = $postData->order[0]->dir;
        $_kactan        = isset($postData->start) ? $postData->start : 0;
        $_kacar         = isset($postData->length) ? $postData->length : 10;
        $_kactan        = max(0, (int) $_kactan);
        $_kacar         = min(500, max(1, (int) $_kacar));

        //GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
        if (isset($_orderType) === false || in_array($_orderType, array("desc", "asc")) === false) {
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

        $tarihData = @explode(" & ", $_searchValue->dEklemeTarihi);
        $_tarihStart    = dateToTimeFormat($tarihData[0] . " 00:00:00", "d-m-Y H:i:s");
        $_tarihStop     = dateToTimeFormat($tarihData[1] . " 23:59:59", "d-m-Y H:i:s");

        $tarihDataDurusma = @explode(" & ", $_searchValue->dDurusmaAralik);
        $_tarihStartDurusma    = dateToTimeFormat($tarihDataDurusma[0] . ":00", "d-m-Y H:i:s");
        $_tarihStopDurusma     = dateToTimeFormat($tarihDataDurusma[1] . ":59", "d-m-Y H:i:s");

        

        $sql = "";
        $_isEk = false;
        $_say=0;
        $sqlEk = "";

        $_likeIsEk = false;
        $_sayLike = 0;
        $likeEk = "";

        if ($_searchValue->dMemurId != -1 && $_searchValue->dMemurId > 0) {

            if ($_say > 0) {
                $sqlEk .= " AND d_memurid = " . trim(replaceHtml($_searchValue->dMemurId));
            } else {
                $sqlEk .= "d_memurid =" . trim(replaceHtml($_searchValue->dMemurId));
            }
            $_isEk = true;
            $_say++;
        }

        if ($_searchValue->dAvukatId != -1 && $_searchValue->dAvukatId > 0) {

            if ($_say > 0) {
                $sqlEk .= " AND d_avukatid = " . trim(replaceHtml($_searchValue->dAvukatId));
            } else {
                $sqlEk .= "d_avukatid =" . trim(replaceHtml($_searchValue->dAvukatId));
            }
            $_isEk = true;
            $_say++;
        }


        if ($_searchValue->dMahkemeId != -1 && $_searchValue->dMahkemeId > 0) {
            $dMahkemeText=FormSelectMahkemeList($_searchValue->dMahkemeId);
            if ($_say > 0) {
                $sqlEk .= " AND d_mahkeme = '" . $this->db->escape_str(trim(replaceHtml($dMahkemeText)))."'";
            } else {
                $sqlEk .= "d_mahkeme = '" . $this->db->escape_str(trim(replaceHtml($dMahkemeText)))."'";
            }
            $_isEk = true;
            $_say++;
        }

        if ($_searchValue->dIslem != -1 && strlen($_searchValue->dIslem) > 0) {

            if ($_say > 0) {
                $sqlEk .= " AND d_islem = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dIslem)))."'";
            } else {
                $sqlEk .= "d_islem = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dIslem)))."'";
            }
            $_isEk = true;
            $_say++;
        }


        if (strlen(trim(replaceHtml_Slash($_searchValue->dEsasNo))) > 0) {
            if ($_say > 0) {
                $sqlEk .= " AND d_esasno = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dEsasNo)))."'";
            } else {
                $sqlEk .= "d_esasno = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dEsasNo)))."'";
            }
            $_isEk = true;
            $_say++;
        }

        if (strlen(trim(replaceHtml_Slash($_searchValue->dDosyaNo))) > 0) {
            if ($_say > 0) {
                $sqlEk .= " AND d_dosyano = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dDosyaNo)))."'";
            } else {
                $sqlEk .= "d_dosyano = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dDosyaNo)))."'";
            }
            $_isEk = true;
            $_say++;
        }

        if (strlen(trim(replaceHtml_Slash($_searchValue->dTarafBilgisi))) > 0) {
            if ($_say > 0) {
                $sqlEk .= " AND d_tarafbilgisi = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dTarafBilgisi)))."'";
            } else {
                $sqlEk .= "d_tarafbilgisi = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dTarafBilgisi)))."'";
            }
            $_isEk = true;
            $_say++;
        }

        if (strlen(trim(replaceHtml_Slash($_searchValue->dTaraf))) > 0 AND $_searchValue->dTaraf!=-1) {
            if ($_say > 0) {
                $sqlEk .= " AND d_taraf = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dTaraf)))."'";
            } else {
                $sqlEk .= "d_taraf = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dTaraf)))."'";
            }
            $_isEk = true;
            $_say++;
        }

        if (strlen(trim(replaceHtml_Slash($_searchValue->dTutanak))) > 0 AND $_searchValue->dTutanak!=-1) {
            if ($_say > 0) {
                $sqlEk .= " AND d_tutanak = " . (int)trim(replaceHtml_Slash($_searchValue->dTutanak));
            } else {
                $sqlEk .= "d_tutanak =" . (int)trim(replaceHtml_Slash($_searchValue->dTutanak));
            }
            $_isEk = true;
            $_say++;
        }

        if (strlen(trim(replaceHtml_Slash($_searchValue->dTakip))) > 0 AND $_searchValue->dTakip!=-1) {
            if ($_say > 0) {
                $sqlEk .= " AND d_takip = " . (int)trim(replaceHtml_Slash($_searchValue->dTakip));
            } else {
                $sqlEk .= "d_takip =" . (int)trim(replaceHtml_Slash($_searchValue->dTakip));
            }
            $_isEk = true;
            $_say++;
        }




        if (strlen(trim(replaceHtml_Slash($_searchValue->dText))) > 0) {
            $gelenText = trim(replaceHtml_Slash($_searchValue->dText));
            $likeEk .= $this->_buildTextSearch($gelenText);
            $_likeIsEk = true;
            $_sayLike++;
        }

        if ($_likeIsEk == true) {
            $sql .= " AND (" . $likeEk . ")";
        }

        if ($_isEk == true) {
            $sql .= " AND (" . $sqlEk . ")";
        }

        $tzIst = new DateTimeZone('Europe/Istanbul');
        $nowIst = new DateTime('now', $tzIst);
        $_todayStart = (new DateTime($nowIst->format('Y-m-d') . ' 00:00:00', $tzIst))->getTimestamp();
        $_todayEnd = (new DateTime($nowIst->format('Y-m-d') . ' 23:59:59', $tzIst))->getTimestamp();

        $selectCols = "d_id, d_dosyano, d_dosyaturu, d_mahkeme, d_durusmatarihi, d_esasno, d_memur, d_memurid, d_tarafbilgisi, d_islem, d_avukat, d_avukatid, d_taraf, d_tags, d_adddate, d_ilgilimemurlar, d_ilgiliavukatlar, d_takip, d_tutanak";

        // Badge ile esas no / dosya no filtresi varsa sadece o numaraya ait tüm kayıtlar (tarih kısıtı yok)
        $filterByEsasOrDosya = (isset($_searchValue->dEsasNo) && trim($_searchValue->dEsasNo) !== '') || (isset($_searchValue->dDosyaNo) && trim($_searchValue->dDosyaNo) !== '');
        $_whereBase = $filterByEsasOrDosya
            ? "d_status=1"
            : "d_status=1 AND d_durusmatarihi >= " . $_todayStart . " AND d_durusmatarihi <= " . $_todayEnd;

        $_t0 = microtime(true);
        $_hasText = ($_likeIsEk == true);
        $filterSql = "SELECT " . $selectCols . " FROM r8t_edts_durusmalar WHERE " . $_whereBase . $sql . " ORDER BY " . $_orderColumn . " " . strtoupper($_orderType);
        $filterRecord = $this->_safeQueryAll($filterSql, $_hasText);
        if ($filterRecord === null && $_hasText) {
            $sql = str_replace(
                $likeEk,
                $this->_buildTextSearch(trim(replaceHtml_Slash($_searchValue->dText))),
                $sql
            );
            $filterSql = "SELECT " . $selectCols . " FROM r8t_edts_durusmalar WHERE " . $_whereBase . $sql . " ORDER BY " . $_orderColumn . " " . strtoupper($_orderType);
            $filterRecord = $this->durusmalar_model->ek_query_all($filterSql);
        }
        $_t1 = microtime(true);
        $totalRecord = $filterRecord ? count($filterRecord) : 0;
        $_t2 = $_t1;

        if (!$filterRecord) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Filtrelemeye Uygun Kayıt Bulunamadı.";
            $_sonuc->_perf = array('select_ms' => round(($_t1-$_t0)*1000), 'total_ms' => round(($_t1-$_t0)*1000), 'today' => $nowIst->format('d.m.Y'), 'filter_start' => $_todayStart, 'filter_end' => $_todayEnd);
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        // Aynı esas no / dosya no’ya ait kayıt sayıları (badge için – Tüm Duruşmalar ile aynı mantık)
        $countByEsasno = array();
        $esasnoList = array_filter(array_unique(array_map(function ($r) { return $r->d_esasno; }, $filterRecord)));
        if (!empty($esasnoList)) {
            $escaped = array_map(function ($e) { return "'" . $this->db->escape_str($e) . "'"; }, $esasnoList);
            $countSql = "SELECT d_esasno, COUNT(*) AS cnt FROM r8t_edts_durusmalar WHERE d_status=1 AND d_esasno IN (" . implode(',', $escaped) . ") GROUP BY d_esasno";
            $countRows = $this->durusmalar_model->ek_query_all($countSql);
            if ($countRows) {
                foreach ($countRows as $cr) {
                    $countByEsasno[$cr->d_esasno] = (int) $cr->cnt;
                }
            }
        }
        $countByDosyano = array();
        $dosyanoList = array_filter(array_unique(array_map(function ($r) { return $r->d_dosyano; }, $filterRecord)));
        if (!empty($dosyanoList)) {
            $escapedD = array_map(function ($e) { return "'" . $this->db->escape_str($e) . "'"; }, $dosyanoList);
            $countSqlD = "SELECT d_dosyano, COUNT(*) AS cnt FROM r8t_edts_durusmalar WHERE d_status=1 AND d_dosyano IN (" . implode(',', $escapedD) . ") GROUP BY d_dosyano";
            $countRowsD = $this->durusmalar_model->ek_query_all($countSqlD);
            if ($countRowsD) {
                foreach ($countRowsD as $cr) {
                    $countByDosyano[$cr->d_dosyano] = (int) $cr->cnt;
                }
            }
        }

        $itemsData = array();
        foreach ($filterRecord as $item) {
            $itemsData[] = array(
                "_id"           => $item->d_id,
                "_dosyano"      => $item->d_dosyano,
                "_dosyano_count" => isset($countByDosyano[$item->d_dosyano]) ? $countByDosyano[$item->d_dosyano] : 0,
                "_dosyaturu"    => etAyracsizYazdir($item->d_dosyaturu),
                "_mahkeme"      => etAyracsizYazdir($item->d_mahkeme),
                "_durusmatarihi" => timeToDateFormat($item->d_durusmatarihi, "d.m.Y H:i"),
                "_esasno"       => $item->d_esasno,
                "_esasno_count" => isset($countByEsasno[$item->d_esasno]) ? $countByEsasno[$item->d_esasno] : 0,
                "_memur"        => $item->d_memur,
                "_tarafbilgisi" => $item->d_tarafbilgisi,
                "_islem"        => etAyracsizYazdir($item->d_islem),
                "_memurid"      => $item->d_memurid,
                "_avukat"       => $item->d_avukat,
                "_avukatid"     => $item->d_avukatid,
                "_taraf"        => $item->d_taraf,
                "_tags"         => drsTagYazdir($item->d_tags),
                "_aciklama"     => '',
                "_eklenmetarihi" => timeToDateFormat($item->d_adddate, "d.m.Y H:i"),
                "_ilgilimemur"  => etAyracsizYazdir($item->d_ilgilimemurlar),
                "_ilgiliavukat" => etAyracsizYazdir($item->d_ilgiliavukatlar),
                "_takip"        => drsTakipBilgisiYaz($item->d_takip),
                "_tutanak"      => drsTutanakBilgisiYaz($item->d_tutanak)
            );
        }
        $_t3 = microtime(true);

        if (count($itemsData) > 0) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = $totalRecord;
            $_sonuc->data                   = $itemsData;
            $_sonuc->success                = true;
            $_sonuc->code                   = 200;
            $_sonuc->description            = $totalRecord . " Adet Kayıt Bulundu.";
            $_sonuc->_perf = array('select_ms' => round(($_t1-$_t0)*1000), 'php_ms' => round(($_t3-$_t1)*1000), 'total_ms' => round(($_t3-$_t0)*1000), 'today' => $nowIst->format('d.m.Y'), 'filter_start' => $_todayStart, 'filter_end' => $_todayEnd);
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
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Filtrelemeye Uygun Kayıt Bulunamadı.";
            $_sonuc->_perf = array('select_ms' => round(($_t1-$_t0)*1000), 'php_ms' => round(($_t3-$_t1)*1000), 'total_ms' => round(($_t3-$_t0)*1000), 'today' => $nowIst->format('d.m.Y'), 'filter_start' => $_todayStart, 'filter_end' => $_todayEnd);
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
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

        if ($this->JSON_DATA->route != "adddurusmamanuel") {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Yönlendirilen Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

		$_edtsTutanakDurum      = (int)trim(replaceHtml_Slash($this->JSON_DATA->edtsTutanakDurum));
		$_edtsDosyaNo           = trim(replaceHtml_Slash($this->JSON_DATA->edtsDosyaNo));
        $_edtsDurusmaTarihi     = trim(replaceHtml($this->JSON_DATA->edtsDurusmaTarihi));

		$_edtsDosyaTur          = str_replace("[{", "", $this->JSON_DATA->edtsDosyaTur);
        $_edtsDosyaTur          = str_replace("}]", "", $_edtsDosyaTur);
        $_edtsDosyaTur          = str_replace("},{", "", $_edtsDosyaTur);
        $_edtsDosyaTur          = str_replace('"', "", $_edtsDosyaTur);

		$_edtsMahkeme           = $this->JSON_DATA->edtsMahkeme;

        $_edtsIlgiliAvukat           =  str_replace("[{", "", $this->JSON_DATA->edtsIlgiliAvukat);
        $_edtsIlgiliAvukat          = str_replace("}]", "", $_edtsIlgiliAvukat);
        $_edtsIlgiliAvukat          = str_replace("},{", "", $_edtsIlgiliAvukat);
        $_edtsIlgiliAvukat          = str_replace('"', "", $_edtsIlgiliAvukat);

        $_edtsIslem           = str_replace("[{", "", $this->JSON_DATA->edtsIslem);
        $_edtsIslem          = str_replace("}]", "", $_edtsIslem);
        $_edtsIslem          = str_replace("},{", "", $_edtsIslem);
        $_edtsIslem          = str_replace('"', "", $_edtsIslem);
        

        $_edtsIlgiliMemur           =  str_replace("[{", "", $this->JSON_DATA->edtsIlgiliMemur);
        $_edtsIlgiliMemur          = str_replace("}]", "", $_edtsIlgiliMemur);
        $_edtsIlgiliMemur          = str_replace("},{", "", $_edtsIlgiliMemur);
        $_edtsIlgiliMemur          = str_replace('"', "", $_edtsIlgiliMemur);

        $_edtsEtiket           =  str_replace("[{", "", $this->JSON_DATA->edtsEtiket);
        $_edtsEtiket          = str_replace("}]", "", $_edtsEtiket);
        $_edtsEtiket          = str_replace("},{", "", $_edtsEtiket);
        $_edtsEtiket          = str_replace('"', "", $_edtsEtiket);    
        
        $_edtsEsasNo            = trim(replaceHtml_Slash($this->JSON_DATA->edtsEsasNo));
		$_edtsAvukat            = (int)trim(replaceHtml_Slash($this->JSON_DATA->edtsAvukat));
        $_edtsTaraf            = trim(replaceHtml_Slash($this->JSON_DATA->edtsTaraf));
        $_edtsDurusmaIslemi            = (int)trim(replaceHtml_Slash($this->JSON_DATA->edtsDurusmaIslemi));



		$_edtsTarafBilgisi      =htmlentities($this->JSON_DATA->edtsTarafBilgisi, ENT_QUOTES);
		$_edtsAciklama      =htmlentities($this->JSON_DATA->edtsAciklama, ENT_QUOTES);
	
        if (strlen($_edtsDurusmaTarihi) == 16) {
            $_edtsDurusmaTarihi1 = dateToTimeFormat($_edtsDurusmaTarihi.":00", "d-m-Y H:i:s");
        } else {
            $_edtsDurusmaTarihi1 = "";
        }

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

        


        $edtsDosyaTur = @explode("value:", $_edtsDosyaTur);
        $edtsMahkeme = @explode(",", $_edtsMahkeme);
        $edtsIlgiliAvukat = @explode("value:", $_edtsIlgiliAvukat);
        $edtsIslem = @explode("value:", $_edtsIslem);
        $edtsIlgiliMemur = @explode("value:", $_edtsIlgiliMemur);
        $edtsEtiket = @explode("value:", $_edtsEtiket);
        $edtsDosyaTurText = "";
        $edtsMahkemeText = "";
        $edtsIlgiliAvukatText = "";
        $edtsIslemText = "";
        $edtsIlgiliMemurText = "";
        $edtsEtiketText = "";

        $edtsDosyaTurText       = drsTagifyToText($edtsDosyaTur);
        $edtsMahkemeText        = convertMahkemeIdToText($edtsMahkeme);
        $edtsIlgiliAvukatText   = drsTagifyToText($edtsIlgiliAvukat);
        $edtsIslemText          = drsTagifyToText($edtsIslem);
        $edtsIlgiliMemurText    = drsTagifyToText($edtsIlgiliMemur);
        $edtsEtiketText         = drsTagifyToText($edtsEtiket);

        $addMemur = $this->userData->userB->u_name . ' ' . $this->userData->userB->u_lastname;
        $addMemur = trim($addMemur) . ' ' . $this->userData->userB->u_surname;

        $avukatCek = drsSorumluAvukatCek($_edtsAvukat);
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
            "d_durusmatarihi"       => $_edtsDurusmaTarihi1,
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

        $add = $this->durusmalar_model->ek_add_lastid(
            "r8t_edts_durusmalar",
            $addData
        );

        if ($add !== false) {
            $logId = ($add > 0) ? $add : $this->db->insert_id();
            $logData = new stdClass();
            $logData->ustid         = "";
            $logData->kullanici     = $this->userData->userB->u_id;
            $logData->aciklama      = ($logId ? $logId : 'Yeni') . " Id numaralı Duruşma kaydı eklendi.";
            $logData->uygulama      = "edts";
            $logData->modul         = "durusmalar";
            $logData->icerikid      = $logId ? $logId : 0;
            $logData->olddata       = array();
            $logData->newdata       = $addData;
            $logyaz = logEkleYeni($logData);

            $_sonuc =  new stdClass();
            $_sonuc->success            = true;
            $_sonuc->code                 = 200;
            $_sonuc->description        = "Tebrikler! Ekleme İşlemi Başarılı.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $dbError = $this->db->error();
            $lastQuery = $this->db->last_query();
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Kayıt İşleminde Hata Oluştu. Lütfen Sistem Yöneticinize Başvurunuz!";
            $_sonuc->debug              = array(
                'db_message' => isset($dbError['message']) ? $dbError['message'] : '',
                'db_code' => isset($dbError['code']) ? $dbError['code'] : ''
            );
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
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

        if (!isDbAllowedListModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "EDTS | Durusmalar Modülünde Görüntüleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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

        $getRecord = $this->durusmalar_model->ek_get(
            "r8t_edts_durusmalar",
            array(
                "d_id" => (int)$postData->id,
            )
        );


        if ($getRecord) {

            $itemArray = array(
                "_id"           => $getRecord->d_id,
                "_dosyano"      => $getRecord->d_dosyano, //ckmAyracsizYazdir($item->c_plaka),
                "_dosyaturu"       => drsEditAyracliYazdir($getRecord->d_dosyaturu),
                "_mahkeme"    => convertMahkemeTextToId($getRecord->d_mahkeme),
                "_durusmatarihi"     => timeToDateFormat($getRecord->d_durusmatarihi, "d-m-Y H:i"),
                "_esasno"     => $getRecord->d_esasno,
                "_memur"        => ($getRecord->d_memur),
                "_tarafbilgisi"       => html_entity_decode($getRecord->d_tarafbilgisi, ENT_QUOTES),
                "_islem"       => drsEditAyracliYazdir($getRecord->d_islem),
                "_memur"       => ($getRecord->d_memur),
                "_memurid"       => ($getRecord->d_memurid),
                "_avukat"       => ($getRecord->d_avukat),
                "_avukatid"       => ($getRecord->d_avukatid),
                "_taraf"       => ($getRecord->d_taraf),
                "_tags"         => drsEditTagYazdir($getRecord->d_tags),
                "_aciklama"     => html_entity_decode($getRecord->d_aciklama, ENT_QUOTES),
                "_eklenmetarihi"  => timeToDateFormat($getRecord->d_adddate, "d-m-Y H:i"),
                "_ilgilimemur"   => drsEditAyracliYazdir($getRecord->d_ilgilimemurlar),
                "_ilgiliavukat"   => drsEditAyracliYazdir($getRecord->d_ilgiliavukatlar),
                "_takip"   => $getRecord->d_takip,
                "_tutanak"   => $getRecord->d_tutanak
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

    public function api_getMahkemeList()
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

        if (!isDbAllowedListModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "EDTS | Durusmalar Modülünde Görüntüleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $postData       = $this->JSON_DATA;


        $mahkemeArr=FormSelectMahkemeList();
        
       

        if ($mahkemeArr) {

            $itemArray=array();
            foreach ($mahkemeArr?$mahkemeArr:array() as $k=>$v) {
                $itemArray[]=$v;
            }


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

        if (!isDbAllowedUpdateModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "EDTS | Duruşmalar Modülünde Güncelleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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
        if (isset($this->JSON_DATA->route) == false || isset($this->JSON_DATA->edtsId) == false) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if ($this->JSON_DATA->route != "editdurusmamanuel") {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Yönlendirilen Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
        $_edtsId              = (int)trim(replaceHtml_Slash($this->JSON_DATA->edtsId));
		$_edtsTutanakDurum      = (int)trim(replaceHtml_Slash($this->JSON_DATA->edtsTutanakDurum));
		$_edtsDosyaNo           = trim(replaceHtml_Slash($this->JSON_DATA->edtsDosyaNo));
        $_edtsDurusmaTarihi     = trim(replaceHtml($this->JSON_DATA->edtsDurusmaTarihi));

		$_edtsDosyaTur          = str_replace("[{", "", $this->JSON_DATA->edtsDosyaTur);
        $_edtsDosyaTur          = str_replace("}]", "", $_edtsDosyaTur);
        $_edtsDosyaTur          = str_replace("},{", "", $_edtsDosyaTur);
        $_edtsDosyaTur          = str_replace('"', "", $_edtsDosyaTur);

		$_edtsMahkeme           = str_replace("[{", "", $this->JSON_DATA->edtsMahkeme);
        $_edtsMahkeme          = str_replace("}]", "", $_edtsMahkeme);
        $_edtsMahkeme          = str_replace("},{", "", $_edtsMahkeme);
        $_edtsMahkeme          = str_replace('"', "", $_edtsMahkeme);

        $_edtsIlgiliAvukat           =  str_replace("[{", "", $this->JSON_DATA->edtsIlgiliAvukat);
        $_edtsIlgiliAvukat          = str_replace("}]", "", $_edtsIlgiliAvukat);
        $_edtsIlgiliAvukat          = str_replace("},{", "", $_edtsIlgiliAvukat);
        $_edtsIlgiliAvukat          = str_replace('"', "", $_edtsIlgiliAvukat);

        $_edtsIslem           = str_replace("[{", "", $this->JSON_DATA->edtsIslem);
        $_edtsIslem          = str_replace("}]", "", $_edtsIslem);
        $_edtsIslem          = str_replace("},{", "", $_edtsIslem);
        $_edtsIslem          = str_replace('"', "", $_edtsIslem);
        

        $_edtsIlgiliMemur           =  str_replace("[{", "", $this->JSON_DATA->edtsIlgiliMemur);
        $_edtsIlgiliMemur          = str_replace("}]", "", $_edtsIlgiliMemur);
        $_edtsIlgiliMemur          = str_replace("},{", "", $_edtsIlgiliMemur);
        $_edtsIlgiliMemur          = str_replace('"', "", $_edtsIlgiliMemur);

        $_edtsEtiket           =  str_replace("[{", "", $this->JSON_DATA->edtsEtiket);
        $_edtsEtiket          = str_replace("}]", "", $_edtsEtiket);
        $_edtsEtiket          = str_replace("},{", "", $_edtsEtiket);
        $_edtsEtiket          = str_replace('"', "", $_edtsEtiket);    
        
        $_edtsEsasNo            = trim(replaceHtml_Slash($this->JSON_DATA->edtsEsasNo));
		$_edtsAvukat            = (int)trim(replaceHtml_Slash($this->JSON_DATA->edtsAvukat));
        $_edtsTaraf            = trim(replaceHtml_Slash($this->JSON_DATA->edtsTaraf));
        $_edtsDurusmaIslemi            = (int)trim(replaceHtml_Slash($this->JSON_DATA->edtsDurusmaIslemi));

        $edtsMahkemeText = "";
        

		$_edtsTarafBilgisi      =htmlentities($this->JSON_DATA->edtsTarafBilgisi, ENT_QUOTES);
		$_edtsAciklama      =htmlentities($this->JSON_DATA->edtsAciklama, ENT_QUOTES);
	
        if (strlen($_edtsDurusmaTarihi) == 16) {
            $_edtsDurusmaTarihi1 = dateToTimeFormat($_edtsDurusmaTarihi.":00", "d-m-Y H:i:s");
        } else {
            $_edtsDurusmaTarihi1 = "";
        }

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

        


        $edtsDosyaTur = @explode("value:", $_edtsDosyaTur);
        $edtsMahkeme = @explode(",", $_edtsMahkeme);
        $edtsIlgiliAvukat = @explode("value:", $_edtsIlgiliAvukat);
        $edtsIslem = @explode("value:", $_edtsIslem);
        $edtsIlgiliMemur = @explode("value:", $_edtsIlgiliMemur);
        $edtsEtiket = @explode("value:", $_edtsEtiket);
        $edtsDosyaTurText = "";
        
        $edtsIlgiliAvukatText = "";
        $edtsIslemText = "";
        $edtsIlgiliMemurText = "";
        $edtsEtiketText = "";

        $edtsDosyaTurText       = drsTagifyToText($edtsDosyaTur);
        $edtsMahkemeText        = convertMahkemeIdToText($edtsMahkeme);
        $edtsIlgiliAvukatText   = drsTagifyToText($edtsIlgiliAvukat);
        $edtsIslemText          = drsTagifyToText($edtsIslem);
        $edtsIlgiliMemurText    = drsTagifyToText($edtsIlgiliMemur);
        $edtsEtiketText         = drsTagifyToText($edtsEtiket);

        $addMemur = $this->userData->userB->u_name . ' ' . $this->userData->userB->u_lastname;
        $addMemur = trim($addMemur) . ' ' . $this->userData->userB->u_surname;

        $avukatCek = drsSorumluAvukatCek($_edtsAvukat);
        if($avukatCek!=false){
            $avukatAdi = $avukatCek->u_name . ' ' . $avukatCek->u_lastname;
            $avukatAdi = trim($avukatAdi) . ' ' . $avukatCek->u_surname;
            }else{
            $avukatAdi = "";
        }

        $oldData = $this->durusmalar_model->ek_get(
            "r8t_edts_durusmalar",
            array(
                "d_id"      => $_edtsId
            )
        );

        $updateData = array(
            "d_dosyaturu"    => $edtsDosyaTurText,
            "d_mahkeme"  => $edtsMahkemeText,
            "d_memur" => $addMemur,
            "d_memurid" => $this->userData->userB->u_id,           
            "d_durusmatarihi"       => $_edtsDurusmaTarihi1,
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
            "d_editdate"   => dateToTimeFormat(date('Y-m-d H:i:s'), "Y-m-d H:i:s"),
            "d_edituser"   => $this->userData->userB->u_id,
            "d_tags"      => $edtsEtiketText
        );

        $update = $this->durusmalar_model->update(
            array(
                "d_id"          => $_edtsId,
            ),
            $updateData
        );

        if ($update !== false) {
            $logData = new stdClass();
            $logData->ustid         = "";
            $logData->kullanici     = $this->userData->userB->u_id;
            $logData->aciklama      = $_edtsId . " Id Numaralı Duruşma Kaydı Güncellendi.";
            $logData->uygulama      = "edts";
            $logData->modul         = "durusmalar";
            $logData->icerikid      = $_edtsId;
            $logData->olddata       = $oldData;
            $logData->newdata       = $updateData;
            $logyaz = logEkleGuncelleme($logData);

            $_sonuc =  new stdClass();
            $_sonuc->success            = true;
            $_sonuc->code                 = 200;
            $_sonuc->description        = "Tebrikler! Güncelleme İşlemi Başarılı.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Güncelleme İşleminde Hata Oluştu. Lütfen Sistem Yöneticinize Başvurunuz!";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }
    public function archive()
    {

        if ($this->userData === false) {
            redirect(sys_url("login"));
            exit;
        }
        //Kullanıcının Birimine Yada Kendi Şahsına Atanmış Uygulamayı Görüntüleme Yetkisi Varmı Kontrolü
        if (!isAllowedViewApp("edts")) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden CEKAS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(sys_url("home"));
            exit;
        }

        if (!isDbAllowedDeleteModule()) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "EDTS | Durusmalar Modülünde Silme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.",
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
    public function istatistik()
    {
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
                "text"  => "EDTS | EDTS Modülünde Görüntüleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(sys_url("home"));
            exit;
        }

    $viewData = new stdClass();
    $viewData->search=$this->input->get("search");
    $defStart=date("01-01-Y");
    $defEnd=date("d-m-Y");
    $defFull="$defStart / $defEnd";
    $durusmaAralik=$this->input->get("durusma_aralik");
    $durusmaAralik=(empty($durusmaAralik))?$defFull:$durusmaAralik;
    $viewData->durusmaAralik=$durusmaAralik;

    $tarihDataDurusma = @explode(" / ", $viewData->durusmaAralik);
    $viewData->current_durusma_start=trim($tarihDataDurusma[0]);
    $viewData->current_durusma_end=trim($tarihDataDurusma[1]);
    

    $viewData->filterMahkemeSelect=$this->input->get("filterMahkemeSelect");
    $viewData->filterMemurSelect=$this->input->get("filterMemurSelect");
    $viewData->filterAvukatSelect=$this->input->get("filterAvukatSelect");
    $viewData->filterIslemSelect=$this->input->get("filterIslemSelect");
    $viewData->dlara_taraf=$this->input->get("dlara_taraf");
    $viewData->dlara_dtakip=$this->input->get("dlara_dtakip");


    $filtreSql=$this->durusmalar_model->getAutoFiltre($viewData);
    $durusmaavukatbazli = $this->durusmalar_model->durusmaAvukatBazli($filtreSql);
    $durusmamemurbazli = $this->durusmalar_model->durusmaMemurBazli($filtreSql);
    $durusmatarafbazli = $this->durusmalar_model->durusmaTarafBazli($filtreSql);
    $durusmamahkemebazli = $this->durusmalar_model->durusmaMahkemeBazli($filtreSql);
    $durusmaislembazli = $this->durusmalar_model->durusmaIslemBazli($filtreSql);
    $durusmalistesiaylik = $this->durusmalar_model->durusmaListesiAylik($filtreSql);
    $kararlistesiaylik = $this->durusmalar_model->kararListesiAylik($filtreSql);
    
    
    $viewData->viewFolder     = $this->viewFolder;
    $viewData->subViewFolder    = "istatistik";
    $viewData->userData             = $this->userData;

    $viewData->durusmaavukatbazli = $durusmaavukatbazli;
    $viewData->durusmamemurbazli = $durusmamemurbazli;
    $viewData->durusmatarafbazli = $durusmatarafbazli;
    $viewData->durusmamahkemebazli = $durusmamahkemebazli;
    $viewData->durusmaislembazli = $durusmaislembazli;
    $viewData->durusmalistesiaylik = $durusmalistesiaylik;
    $viewData->kararlistesiaylik = $kararlistesiaylik;

    $viewData->durusmaavukatbazli2 = $durusmaavukatbazli;
    $viewData->durusmamemurbazli2 = $durusmamemurbazli;
    $viewData->durusmatarafbazli2 = $durusmatarafbazli;
    $viewData->durusmamahkemebazli2 = $durusmamahkemebazli;
    $viewData->durusmaislembazli2 = $durusmaislembazli;
    $viewData->durusmalistesiaylik2 = $durusmalistesiaylik;
    $viewData->kararlistesiaylik2 = $kararlistesiaylik;

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
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Kullanıcı Yetkilendirme Hatası. Lütfen Giriş Bilgilerinizi Kontrol Ediniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isAllowedViewApp("edts")) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden EDTS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isDbAllowedDeleteModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "EDTS | Dosyalar Modülünde Silme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $_post          = json_encode($this->input->post());
        $postData       = json_decode($_post);
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
        $_searchValue   = isset($postData->search->value) ? json_decode($postData->search->value) : new stdClass();
        if (!$_searchValue) $_searchValue = new stdClass();
        if (!isset($_searchValue->dEklemeTarihi) || empty($_searchValue->dEklemeTarihi)) {
            $_searchValue->dEklemeTarihi = date('d-m-Y', strtotime('-6 years')) . ' & ' . date('d-m-Y');
        }
        $_orderColumn   = $this->getTableColumnName($postData->order[0]->column);
        $_orderType     = $postData->order[0]->dir;
        $_kactan        = isset($postData->start) ? $postData->start : 0;
        $_kacar         = isset($postData->length) ? $postData->length : 10;
        $_kactan        = max(0, (int) $_kactan);
        $_kacar         = min(500, max(1, (int) $_kacar));

        //GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
        if (isset($_orderType) === false || in_array($_orderType, array("desc", "asc")) === false) {
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

        $tarihData = @explode(" & ", $_searchValue->dEklemeTarihi);
        $_tarihStart    = dateToTimeFormat($tarihData[0] . " 00:00:00", "d-m-Y H:i:s");
        $_tarihStop     = dateToTimeFormat($tarihData[1] . " 23:59:59", "d-m-Y H:i:s");


        $sql = "";


        $_ejectSelectCols = "d_id, d_dosyano, d_dosyaturu, d_mahkeme, d_durusmatarihi, d_esasno, d_memur, d_memurid, d_tarafbilgisi, d_islem, d_avukat, d_avukatid, d_taraf, d_tags, d_adddate, d_ilgilimemurlar, d_ilgiliavukatlar, d_takip, d_tutanak";
        $_whereBase = "d_status=-1 AND d_adddate >= " . trim(replaceHtml($_tarihStart)) . " AND d_adddate <= " . trim(replaceHtml($_tarihStop));

        $filterSql = "SELECT " . $_ejectSelectCols . " FROM r8t_edts_durusmalar WHERE " . $_whereBase . $sql . " ORDER BY " . $_orderColumn . " " . strtoupper($_orderType) . " LIMIT " . $_kactan . ", " . $_kacar;
        $filterRecord = $this->durusmalar_model->ek_query_all($filterSql);

        $returnedCount = $filterRecord ? count($filterRecord) : 0;
        if ($returnedCount < (int)$_kacar) {
            $totalRecord = (int)$_kactan + $returnedCount;
        } else {
            $totalRecordS = $this->durusmalar_model->ek_query("SELECT COUNT(*) AS total FROM r8t_edts_durusmalar WHERE " . $_whereBase . $sql);
            $totalRecord = $totalRecordS ? (int)$totalRecordS->total : 0;
        }

        if (!$filterRecord) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Filtrelemeye Uygun Kayıt Bulunamadı.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        $itemsData = array();
        foreach ($filterRecord as $item) {

            $itemArray = array(
                "_id"           => $item->d_id,
                "_dosyano"      => $item->d_dosyano, //ckmAyracsizYazdir($item->c_plaka),
                "_dosyaturu"       => etAyracsizYazdir($item->d_dosyaturu),
                "_mahkeme"    => etAyracsizYazdir($item->d_mahkeme),
                "_durusmatarihi"     => timeToDateFormat($item->d_durusmatarihi, "d.m.Y H:i"),
                "_esasno"     => $item->d_esasno,
                "_memur"        => ($item->d_memur),
                "_tarafbilgisi"       => ($item->d_tarafbilgisi),
                "_islem"       => etAyracsizYazdir($item->d_islem),
                "_memur"       => ($item->d_memur),
                "_memurid"       => ($item->d_memurid),
                "_avukat"       => ($item->d_avukat),
                "_avukatid"       => ($item->d_avukatid),
                "_taraf"       => ($item->d_taraf),
                "_tags"         => drsTagYazdir($item->d_tags),
                "_aciklama"     => '',
                "_eklenmetarihi" => timeToDateFormat($item->d_adddate, "d.m.Y H:i"),
                "_ilgilimemur"  => etAyracsizYazdir($item->d_ilgilimemurlar),
                "_ilgiliavukat" => etAyracsizYazdir($item->d_ilgiliavukatlar),
                "_takip"        => drsTakipBilgisiYaz($item->d_takip),
                "_tutanak"      => drsTutanakBilgisiYaz($item->d_tutanak)
            );
            $itemsData[] = $itemArray;
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
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Filtrelemeye Uygun Kayıt Bulunamadı.";
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

        if (!isDbAllowedDeleteModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "EDTS | Duruşmalar Modülünde Silme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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

        $getUpdate = $this->durusmalar_model->ek_update(
            "r8t_edts_durusmalar",
            array(
                "d_id" => (int)$postData->id,
                "d_status" => 1
            ),
            array(
                "d_status" => -1
            )
        );

        if ($getUpdate) {
            $logData = new stdClass();
            $logData->ustid         = "";
            $logData->kullanici     = $this->userData->userB->u_id;
            $logData->aciklama      = (int)$postData->id . " Id Numaralı Duruşma Çöp Kutusuna Taşındı.";
            $logData->uygulama      = "edts";
            $logData->modul         = "durusmalar";
            $logData->icerikid      = (int)$postData->id;
            $logData->olddata       = array("status" => 1);
            $logData->newdata       = array("status" => -1);
            $logyaz = logEkleTasima($logData);

            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = true;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Kayıt Çöp Kutusuna Taşındı.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Kayıt Çöp Kutusuna Taşınamadı.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

    }


    public function api_mahkemeejectdata()
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

        if (!isDbAllowedDeleteModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "EDTS | Duruşmalar Modülünde Silme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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

        $getUpdate = $this->durusmalar_model->ek_update(
            "r8t_sys_mahkemeler",
            array(
                "mh_id" => (int)$postData->id,
                "mh_status" => 1
            ),
            array(
                "mh_status" => -1
            )
        );

        if ($getUpdate) {
            $logData = new stdClass();
            $logData->ustid         = "";
            $logData->kullanici     = $this->userData->userB->u_id;
            $logData->aciklama      = (int)$postData->id . " Id Numaralı Mahkeme Çöp Kutusuna Taşındı.";
            $logData->uygulama      = "edts";
            $logData->modul         = "mahkemeler";
            $logData->icerikid      = (int)$postData->id;
            $logData->olddata       = array("status" => 1);
            $logData->newdata       = array("status" => -1);
            $logyaz = logEkleTasima($logData);

            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = true;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Kayıt Çöp Kutusuna Taşındı.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Kayıt Çöp Kutusuna Taşınamadı.";
            $sonuc = json_encode($_sonuc);
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

        if (!isDbAllowedUpdateModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "EDTS | Duruşmalar Modülünde Güncelleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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

        $getUpdate = $this->durusmalar_model->update(
            array(
                "d_id" => (int)$postData->id,
                "d_status" => -1
            ),
            array(
                "d_status" => 1
            )
        );

        if ($getUpdate) {
            $logData = new stdClass();
            $logData->ustid         = "";
            $logData->kullanici     = $this->userData->userB->u_id;
            $logData->aciklama      = (int)$postData->id . " Id Numaralı Duruşma Çöp Kutusundan Taşındı.";
            $logData->uygulama      = "edts";
            $logData->modul         = "durusmalar";
            $logData->icerikid      = (int)$postData->id;
            $logData->olddata       = array("status" => -1);
            $logData->newdata       = array("status" => 1);
            $logyaz = logEkleTasima($logData);

            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = true;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Kayıt Çöp Kutusundan Taşındı.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Kayıt Çöp Kutusundan Taşınamadı.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

    }

    ### HAREKETLER ----------------------

    public function hareketler()
    {

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

        if (!isDbAllowedListModule()) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "EDTS | Duruşma TAkip Modülünü Görüntüleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(sys_url("home"));
            exit;
        }

        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "hareketler";
        $viewData->userData             = $this->userData;
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function api_hareketlerlist()
    {

        if ($this->userData === false) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
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
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
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
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "EDTS | Duruşama Takip Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $_post          = json_encode($this->input->post());
        $postData       = json_decode($_post);
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
        $_searchValue   = isset($postData->search->value) ? json_decode($postData->search->value) : new stdClass();
        if (!$_searchValue) $_searchValue = new stdClass();
        if (!isset($_searchValue->chKayitTarih) || empty($_searchValue->chKayitTarih)) {
            $_searchValue->chKayitTarih = date('d-m-Y', strtotime('-6 years')) . ' & ' . date('d-m-Y');
        }
        if (!isset($_searchValue->chId)) $_searchValue->chId = '';
        if (!isset($_searchValue->chText)) $_searchValue->chText = '';
        $_orderColumn   = $this->getHareketlerTableColumnName($postData->order[0]->column);
        $_orderType     = $postData->order[0]->dir;
        $_kactan        = isset($postData->start) ? $postData->start : 0;
        $_kacar         = isset($postData->length) ? $postData->length : 10;
        $_kactan        = max(0, (int) $_kactan);
        $_kacar         = min(500, max(1, (int) $_kacar));

        //GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
        if (isset($_orderType) === false || in_array($_orderType, array("desc", "asc")) === false) {
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

        $tarihData = @explode(" & ", $_searchValue->chKayitTarih);
        $_tarihStart    = dateToTimeFormat($tarihData[0] . " 00:00:00", "d-m-Y H:i:s");
        $_tarihStop     = dateToTimeFormat($tarihData[1] . " 23:59:59", "d-m-Y H:i:s");


        $sql = "";

        $_ekVar = false;
        $_say = 0;
        $sqlEk = "";
        $isid = (int)$_searchValue->chId;
        // if (strlen(trim(replaceHtml($_searchValue->cId))) > 0 && $isid > 0 ) {
        //     $_ekVar = true;
        //     $_say++;
        //     $sqlEk .= "c_id LIKE = (" . trim(replaceHtml($_searchValue->cId)) . ")";
        // }
        // if ($_searchValue->cIstasyonid != -1 && $_searchValue->cIstasyonid > 0) {

        //     if ($_say > 0) {
        //         $sqlEk .= " OR c_istasyonid = " . trim(replaceHtml($_searchValue->cIstasyonid));
        //     } else {
        //         $sqlEk .= "c_istasyonid =" . trim(replaceHtml($_searchValue->cIstasyonid));
        //     }
        //     $_ekVar = true;
        //     $_say++;
        // }
        // if (strlen(trim(replaceHtml($_searchValue->cPlaka))) > 0) {

        //     if ($_say > 0) {
        //         $sqlEk .= " OR c_plaka LIKE '%" . trim(replaceHtml($_searchValue->cPlaka)) . "%'";
        //     } else {
        //         $sqlEk .= "c_plaka LIKE '%" . trim(replaceHtml($_searchValue->cPlaka)) . "%'";
        //     }
        //     $_ekVar = true;
        //     $_say++;
        // }
        if (strlen(trim(replaceHtml($_searchValue->chText))) > 0) {
            $_arananText = trim(replaceHtml($_searchValue->chText));
            if ($_say > 0) {
                $sqlEk .= " OR ul_newdata LIKE '%" . $_arananText . "%'";
                $sqlEk .= " OR ul_olddata LIKE '%" . $_arananText . "%'";
                $sqlEk .= " OR d_dosyano LIKE '%" . $_arananText . "%'";
                $sqlEk .= " OR d_avukat LIKE '%" . $_arananText . "%'";
                $sqlEk .= " OR d_dosyaturu LIKE '%" . $_arananText . "%'";
                $sqlEk .= " OR u_name LIKE '%" . $_arananText . "%'";
                $sqlEk .= " OR u_surname LIKE '%" . $_arananText . "%'";
            } else {
                $sqlEk .= "ul_newdata LIKE '%" . $_arananText . "%'";
                $sqlEk .= " OR ul_olddata LIKE '%" . $_arananText . "%'";
                $sqlEk .= " OR d_dosyano LIKE '%" . $_arananText . "%'";
                $sqlEk .= " OR d_avukat LIKE '%" . $_arananText . "%'";
                $sqlEk .= " OR d_dosyaturu LIKE '%" . $_arananText . "%'";
                $sqlEk .= " OR u_name LIKE '%" . $_arananText . "%'";
                $sqlEk .= " OR u_surname LIKE '%" . $_arananText . "%'";
            }
            $_ekVar = true;
            $_say++;
        }

        if ($_ekVar == true) {
            $sql .= " AND (" . $sqlEk . ")";
        }

        $_hrkWhereBase = "ul_app='edts' AND ul_modul IN ('durusmalar', 'durusmalar') AND ul_adddate >= " . trim(replaceHtml($_tarihStart)) . " AND ul_adddate <= " . trim(replaceHtml($_tarihStop));

        $filterSql = "SELECT ul_id, ul_adddate, ul_tur, ul_aciklama, ul_olddata, ul_newdata, d_dosyano, d_avukat, d_dosyaturu, u_name, u_lastname, u_surname FROM r8t_sys_userlogs LEFT JOIN r8t_edts_durusmalar ON d_id=ul_icerikid LEFT JOIN r8t_users ON u_id=ul_userid WHERE " . $_hrkWhereBase . $sql . " ORDER BY " . $_orderColumn . " " . strtoupper($_orderType) . " LIMIT " . $_kactan . ", " . $_kacar;
        $filterRecord = $this->durusmalar_model->ek_query_all($filterSql);

        $returnedCount = $filterRecord ? count($filterRecord) : 0;
        if ($returnedCount < (int)$_kacar) {
            $totalRecord = (int)$_kactan + $returnedCount;
        } else {
            $totalRecordS = $this->durusmalar_model->ek_query("SELECT COUNT(*) AS total FROM r8t_sys_userlogs LEFT JOIN r8t_edts_durusmalar ON d_id=ul_icerikid LEFT JOIN r8t_users ON u_id=ul_userid WHERE " . $_hrkWhereBase . $sql);
            $totalRecord = $totalRecordS ? (int)$totalRecordS->total : 0;
        }

        if (!$filterRecord) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Filtrelemeye Uygun Kayıt Bulunamadı.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        $itemsData = array();
        foreach ($filterRecord as $item) {
            $islemYapan = $item->u_name . ' ' . $item->u_lastname;
            $islemYapan = trim($islemYapan) . ' ' . $item->u_surname;

            $itemArray = array(
                "_id"           => $item->ul_id,
                "_kayittarihi"   => timeToDateFormat($item->ul_adddate, "d.m.Y H:i"),
                "_islemturu"     => ckmHareketTurYazdir($item->ul_tur),
                "_islemyapan"     => $islemYapan,
                "_dosyano"        => $item->d_dosyano,
                "_avukat"       => $item->d_avukat,
                "_dosyaturu"       => ckmAyracsizYazdir($item->d_dosyaturu),
                "_aciklama"     => $item->ul_aciklama,
                "_olddata"     => $item->ul_olddata,
                "_newdata"     => $item->ul_newdata,
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
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Filtrelemeye Uygun Kayıt Bulunamadı.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }

    ## END HAREKETLİST

    private function getHareketlerTableColumnName($data = 0)
    {
        $columnName = "ul_id";
        switch ($data) {
            case 0:
                $columnName = "ul_adddate";
                break;
            case 1:
                $columnName = "ul_tur";
                break;
            case 2:
                $columnName = "ul_userid";
                break;
            case 3:
                $columnName = "d_dosyano";
                break;
            case 4:
                $columnName = "d_avukat";
                break;
            case 5:
                $columnName = "d_dosyaturu";
                break;
            case 6:
                $columnName = "ul_aciklama";
                break;
            default:
                $columnName = "ul_id";
                break;
        }
        return $columnName;
    }

    ###BEGIN::DURUSMALARIM FONKSİYONLARI####



    public function mahkemeler()
    {

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

        if (!isDbAllowedListModule()) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "EDTS | Duruşma TAkip Modülünü Görüntüleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(sys_url("home"));
            exit;
        }
        $_mhId=$this->input->get("mh_id");
        $_mhName=$this->input->get("mh_name");

        if ($_mhName) {

            $_mhName=ucwords($_mhName);
            $_mhId=(empty($_mhId))?0:$_mhId;
    
            $mahkemeVar=$this->mahkemeler_model->checkMahkemeAdi($_mhName,$_mhId);
    
            if (!empty($mahkemeVar)) {
                header("location:/apps/edts/durusmalar/mahkemeler");
                exit;

            }    
            
            if (empty($_mhId)) {
    
                $update = $this->mahkemeler_model->ek_add('r8t_sys_mahkemeler',
                    array(
                        "mh_name"        => $_mhName
                    )
                );
    
        
                if ($update) {
                    header("location:/apps/edts/durusmalar/mahkemeler");
                    exit;
                } else {
                    header("location:/apps/edts/durusmalar/mahkemeler");
                    exit;
                }
            }
            else {
    
                $update = $this->mahkemeler_model->update(
                    array(
                        "mh_id"         => $_mhId
                    ),
                    array(
                        "mh_name"        => $_mhName
                    )
                );
        
                if ($update) {
                    header("location:/apps/edts/durusmalar/mahkemeler");
                    exit;
                } else {
                    header("location:/apps/edts/durusmalar/mahkemeler");
                    exit;
                }            
            }
                    
            

        }

        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "mahkemeler";
        $viewData->userData             = $this->userData;
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }


    public function api_getMahkeme()
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
            $_sonuc->description            = "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden HEDAS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isDbAllowedViewModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "EDTS |Bu modülde Görüntüleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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

        $getRecord = $this->durusmalar_model->ek_get(
            "r8t_sys_mahkemeler",
            array(
                "mh_id" => (int)$postData->id
            )
        );

        if ($getRecord) {

            $itemArray = array(
                "_id"        => $getRecord->mh_id,
                "_mhadi"       => $getRecord->mh_name

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

    public function api_mahkemelerlist()
    {

        if ($this->userData === false) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
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
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
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
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "EDTS | Duruşma Takip Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $_post          = json_encode($this->input->post());
        $postData       = json_decode($_post);
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
        $_searchValue   = isset($postData->search->value) ? json_decode($postData->search->value) : new stdClass();
        if (!$_searchValue) $_searchValue = new stdClass();
        if (!isset($_searchValue->chKayitTarih) || empty($_searchValue->chKayitTarih)) {
            $_searchValue->chKayitTarih = date('d-m-Y', strtotime('-6 years')) . ' & ' . date('d-m-Y');
        }
        if (!isset($_searchValue->chId)) $_searchValue->chId = '';
        if (!isset($_searchValue->chText)) $_searchValue->chText = '';
        $_orderColumn   = $this->getMahkemelerTableColumnName($postData->order[0]->column);
        $_orderType     = $postData->order[0]->dir;
        $_kactan        = isset($postData->start) ? $postData->start : 0;
        $_kacar         = isset($postData->length) ? $postData->length : 10;
        $_kactan        = max(0, (int) $_kactan);
        $_kacar         = min(500, max(1, (int) $_kacar));

        //GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
        if (isset($_orderType) === false || in_array($_orderType, array("desc", "asc")) === false) {
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

        $tarihData = @explode(" & ", $_searchValue->chKayitTarih);
        $_tarihStart    = dateToTimeFormat($tarihData[0] . " 00:00:00", "d-m-Y H:i:s");
        $_tarihStop     = dateToTimeFormat($tarihData[1] . " 23:59:59", "d-m-Y H:i:s");


        $sql = "";

        $_ekVar = false;
        $_say = 0;
        $sqlEk = "";
        $isid = (int)$_searchValue->chId;
        
        if (strlen(trim(replaceHtml($_searchValue->chText))) > 0) {
            $_arananText = trim(replaceHtml($_searchValue->chText));
            if ($_say > 0) {
                $sqlEk .= " OR mh_name LIKE '%" . $_arananText . "%'";
                $sqlEk .= " OR mh_id LIKE '%" . $_arananText . "%'";
            } else {
                $sqlEk .= "mh_name LIKE '%" . $_arananText . "%'";
                $sqlEk .= " OR mh_id LIKE '%" . $_arananText . "%'";
            }
            $_ekVar = true;
            $_say++;
        }

        if ($_ekVar == true) {
            $sql .= " AND (" . $sqlEk . ")";
        }

        $filterSql = "SELECT mh_id, mh_name FROM r8t_sys_mahkemeler WHERE mh_status=1" . $sql . " ORDER BY " . $_orderColumn . " " . strtoupper($_orderType) . " LIMIT " . $_kactan . ", " . $_kacar;
        $filterRecord = $this->durusmalar_model->ek_query_all($filterSql);

        $returnedCount = $filterRecord ? count($filterRecord) : 0;
        if ($returnedCount < (int)$_kacar) {
            $totalRecordS = (object)array('total' => (int)$_kactan + $returnedCount);
        } else {
            $totalRecordS = $this->durusmalar_model->ek_query("SELECT COUNT(*) AS total FROM r8t_sys_mahkemeler WHERE mh_status=1" . $sql);
        }
        $totalRecord = $totalRecordS ? (int)$totalRecordS->total : 0;

        if (!$filterRecord) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Filtrelemeye Uygun Kayıt Bulunamadı.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        $itemsData = array();
        foreach ($filterRecord as $item) {

            $itemArray = array(
                "_id"           => $item->mh_id,
                "_mhadi"        => $item->mh_name,
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
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Filtrelemeye Uygun Kayıt Bulunamadı.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }

    ## END MAHKEMELİST

    private function getMahkemelerTableColumnName($data = 0)
    {
        $columnName = "ul_id";
        switch ($data) {
            case 1:
                $columnName = "mh_name";
                break;
            
            default:
                $columnName = "mh_id";
                break;
        }
        return $columnName;
    }

    ###BEGIN::DURUSMALARIM FONKSİYONLARI####


    public function durusmalarim()
    {

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

        if (!isDbAllowedListModule()) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "EDTS | Duruşmalar Modülünü Görüntüleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(sys_url("home"));
            exit;
        }

        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "mylist";
        $viewData->userData             = $this->userData;
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }


    public function api_mylist()
    {

        if ($this->userData === false) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
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
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
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
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "EDTS | Dosyalar Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $_post          = json_encode($this->input->post());
        $postData       = json_decode($_post);
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
        $_searchValue   = isset($postData->search->value) ? json_decode($postData->search->value) : new stdClass();
        if (!$_searchValue) $_searchValue = new stdClass();
        if (!isset($_searchValue->dEklemeTarihi) || empty($_searchValue->dEklemeTarihi)) {
            $_searchValue->dEklemeTarihi = date('d-m-Y', strtotime('-6 years')) . ' & ' . date('d-m-Y');
        }
        if (!isset($_searchValue->dDurusmaAralik) || empty($_searchValue->dDurusmaAralik)) {
            $_searchValue->dDurusmaAralik = date('d-m-Y H:i', strtotime('-6 years')) . ' & ' . date('d-m-Y H:i', strtotime('+6 years'));
        }
        if (!isset($_searchValue->dListTur)) $_searchValue->dListTur = 'filter';
        if (!isset($_searchValue->dMemurId)) $_searchValue->dMemurId = -1;
        if (!isset($_searchValue->dAvukatId)) $_searchValue->dAvukatId = -1;
        if (!isset($_searchValue->dMahkemeId)) $_searchValue->dMahkemeId = -1;
        if (!isset($_searchValue->dIslem)) $_searchValue->dIslem = -1;
        if (!isset($_searchValue->dEsasNo)) $_searchValue->dEsasNo = '';
        if (!isset($_searchValue->dTarafBilgisi)) $_searchValue->dTarafBilgisi = '';
        if (!isset($_searchValue->dTaraf)) $_searchValue->dTaraf = -1;
        if (!isset($_searchValue->dTutanak)) $_searchValue->dTutanak = -1;
        if (!isset($_searchValue->dTakip)) $_searchValue->dTakip = -1;
        if (!isset($_searchValue->dText)) $_searchValue->dText = '';
        if (!isset($_searchValue->dDosyaNo)) $_searchValue->dDosyaNo = '';
        if (!isset($_searchValue->dTags)) $_searchValue->dTags = '';
        $_orderColumn   = $this->getTableColumnName($postData->order[0]->column);
        $_orderType     = $postData->order[0]->dir;
        $_kactan        = isset($postData->start) ? $postData->start : 0;
        $_kacar         = isset($postData->length) ? $postData->length : 10;
        $_kactan        = max(0, (int) $_kactan);
        $_kacar         = min(500, max(1, (int) $_kacar));

        //GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
        if (isset($_orderType) === false || in_array($_orderType, array("desc", "asc")) === false) {
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

        $tarihData = @explode(" & ", $_searchValue->dEklemeTarihi);
        $_tarihStart    = dateToTimeFormat($tarihData[0] . " 00:00:00", "d-m-Y H:i:s");
        $_tarihStop     = dateToTimeFormat($tarihData[1] . " 23:59:59", "d-m-Y H:i:s");

        $tarihDataDurusma = @explode(" & ", $_searchValue->dDurusmaAralik);
        $_tarihStartDurusma    = dateToTimeFormat($tarihDataDurusma[0] . ":00", "d-m-Y H:i:s");
        $_tarihStopDurusma     = dateToTimeFormat($tarihDataDurusma[1] . ":59", "d-m-Y H:i:s");

        

        $sql = "";
        $_isEk = false;
        $_say=0;
        $sqlEk = "";

        $_likeIsEk = false;
        $_sayLike = 0;
        $likeEk = "";

        $myId = $this->userData->userB->u_id;
        $sqlEk .= "d_memurid =" . $myId;
        $_isEk = true;
        $_say++;
        // if ($_searchValue->dMemurId != -1 && $_searchValue->dMemurId > 0) {

        //     if ($_say > 0) {
        //         $sqlEk .= " AND d_memurid = " . $myId;
        //     } else {
        //         $sqlEk .= "d_memurid =" . $myId;
        //     }
        //     $_isEk = true;
        //     $_say++;
        // }

        if ($_searchValue->dAvukatId != -1 && $_searchValue->dAvukatId > 0) {

            if ($_say > 0) {
                $sqlEk .= " AND d_avukatid = " . trim(replaceHtml($_searchValue->dAvukatId));
            } else {
                $sqlEk .= "d_avukatid =" . trim(replaceHtml($_searchValue->dAvukatId));
            }
            $_isEk = true;
            $_say++;
        }
        if ($_searchValue->dMahkemeId != -1 && $_searchValue->dMahkemeId > 0) {
            $dMahkemeText=FormSelectMahkemeList($_searchValue->dMahkemeId);
            if ($_say > 0) {
                $sqlEk .= " AND d_mahkeme = '" . $this->db->escape_str(trim(replaceHtml($dMahkemeText)))."'";
            } else {
                $sqlEk .= "d_mahkeme = '" . $this->db->escape_str(trim(replaceHtml($dMahkemeText)))."'";
            }
            $_isEk = true;
            $_say++;
        }



        if ($_searchValue->dIslem != -1 && strlen($_searchValue->dIslem) > 0) {

            if ($_say > 0) {
                $sqlEk .= " AND d_islem = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dIslem)))."'";
            } else {
                $sqlEk .= "d_islem = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dIslem)))."'";
            }
            $_isEk = true;
            $_say++;
        }


        if (strlen(trim(replaceHtml_Slash($_searchValue->dEsasNo))) > 0) {
            if ($_say > 0) {
                $sqlEk .= " AND d_esasno = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dEsasNo)))."'";
            } else {
                $sqlEk .= "d_esasno = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dEsasNo)))."'";
            }
            $_isEk = true;
            $_say++;
        }

        if (strlen(trim(replaceHtml_Slash($_searchValue->dDosyaNo))) > 0) {
            if ($_say > 0) {
                $sqlEk .= " AND d_dosyano = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dDosyaNo)))."'";
            } else {
                $sqlEk .= "d_dosyano = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dDosyaNo)))."'";
            }
            $_isEk = true;
            $_say++;
        }

        if (strlen(trim(replaceHtml_Slash($_searchValue->dTarafBilgisi))) > 0) {
            if ($_say > 0) {
                $sqlEk .= " AND d_tarafbilgisi = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dTarafBilgisi)))."'";
            } else {
                $sqlEk .= "d_tarafbilgisi = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dTarafBilgisi)))."'";
            }
            $_isEk = true;
            $_say++;
        }

        if (strlen(trim(replaceHtml_Slash($_searchValue->dTaraf))) > 0 AND $_searchValue->dTaraf!=-1) {
            if ($_say > 0) {
                $sqlEk .= " AND d_taraf = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dTaraf)))."'";
            } else {
                $sqlEk .= "d_taraf = '" . $this->db->escape_str(trim(replaceHtml_Slash($_searchValue->dTaraf)))."'";
            }
            $_isEk = true;
            $_say++;
        }

        if (strlen(trim(replaceHtml_Slash($_searchValue->dTutanak))) > 0 AND $_searchValue->dTutanak!=-1) {
            if ($_say > 0) {
                $sqlEk .= " AND d_tutanak = " . (int)trim(replaceHtml_Slash($_searchValue->dTutanak));
            } else {
                $sqlEk .= "d_tutanak =" . (int)trim(replaceHtml_Slash($_searchValue->dTutanak));
            }
            $_isEk = true;
            $_say++;
        }

        if (strlen(trim(replaceHtml_Slash($_searchValue->dTakip))) > 0 AND $_searchValue->dTakip!=-1) {
            if ($_say > 0) {
                $sqlEk .= " AND d_takip = " . (int)trim(replaceHtml_Slash($_searchValue->dTakip));
            } else {
                $sqlEk .= "d_takip =" . (int)trim(replaceHtml_Slash($_searchValue->dTakip));
            }
            $_isEk = true;
            $_say++;
        }




        if (strlen(trim(replaceHtml_Slash($_searchValue->dText))) > 0) {
            $gelenText = trim(replaceHtml_Slash($_searchValue->dText));
            $likeEk .= $this->_buildTextSearch($gelenText);
            $_likeIsEk = true;
            $_sayLike++;
        }

        if ($_likeIsEk == true) {
            $sql .= " AND (" . $likeEk . ")";
        }

        if ($_isEk == true) {
            $sql .= " AND (" . $sqlEk . ")";
        }

        $_mySelectCols = "d_id, d_dosyano, d_dosyaturu, d_mahkeme, d_durusmatarihi, d_esasno, d_memur, d_memurid, d_tarafbilgisi, d_islem, d_avukat, d_avukatid, d_taraf, d_tags, d_adddate, d_ilgilimemurlar, d_ilgiliavukatlar, d_takip, d_tutanak";
        $_whereBase = "d_status=1";
        $_tStart = (int)trim(replaceHtml($_tarihStart));
        $_tStop = (int)trim(replaceHtml($_tarihStop));
        $_dStart = (int)trim(replaceHtml($_tarihStartDurusma));
        $_dStop = (int)trim(replaceHtml($_tarihStopDurusma));
        if (($_tStop - $_tStart) < 157680000) {
            $_whereBase .= " AND d_adddate >= " . $_tStart . " AND d_adddate <= " . $_tStop;
        }
        if (($_dStop - $_dStart) < 315360000) {
            $_whereBase .= " AND (d_durusmatarihi IS NULL OR (d_durusmatarihi >= " . $_dStart . " AND d_durusmatarihi <= " . $_dStop . "))";
        }

        $_t0 = microtime(true);
        $_hasText = ($_likeIsEk == true);
        $filterSql = "SELECT " . $_mySelectCols . " FROM r8t_edts_durusmalar WHERE " . $_whereBase . $sql . " ORDER BY " . $_orderColumn . " " . strtoupper($_orderType) . " LIMIT " . $_kactan . ", " . $_kacar;
        $filterRecord = $this->_safeQueryAll($filterSql, $_hasText);
        if ($filterRecord === null && $_hasText) {
            $sql = str_replace(
                $likeEk,
                $this->_buildTextSearch(trim(replaceHtml_Slash($_searchValue->dText))),
                $sql
            );
            $filterSql = "SELECT " . $_mySelectCols . " FROM r8t_edts_durusmalar WHERE " . $_whereBase . $sql . " ORDER BY " . $_orderColumn . " " . strtoupper($_orderType) . " LIMIT " . $_kactan . ", " . $_kacar;
            $filterRecord = $this->durusmalar_model->ek_query_all($filterSql);
        }
        $_t1 = microtime(true);

        $returnedCount = $filterRecord ? count($filterRecord) : 0;
        if ($returnedCount < (int)$_kacar) {
            $totalRecord = (int)$_kactan + $returnedCount;
        } else {
            $countSql = "SELECT COUNT(*) AS total FROM r8t_edts_durusmalar WHERE " . $_whereBase . $sql;
            $totalRecordS = $this->durusmalar_model->ek_query($countSql);
            $totalRecord = $totalRecordS ? (int)$totalRecordS->total : 0;
        }
        $_t2 = microtime(true);

        if (!$filterRecord) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Filtrelemeye Uygun Kayıt Bulunamadı.";
            $_sonuc->_perf = array('select_ms' => round(($_t1-$_t0)*1000), 'count_ms' => round(($_t2-$_t1)*1000), 'total_ms' => round(($_t2-$_t0)*1000));
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $itemsData = array();
        foreach ($filterRecord as $item) {
            $itemsData[] = array(
                "_id"           => $item->d_id,
                "_dosyano"      => $item->d_dosyano,
                "_dosyaturu"    => etAyracsizYazdir($item->d_dosyaturu),
                "_mahkeme"      => etAyracsizYazdir($item->d_mahkeme),
                "_durusmatarihi" => timeToDateFormat($item->d_durusmatarihi, "d.m.Y H:i"),
                "_esasno"       => $item->d_esasno,
                "_memur"        => $item->d_memur,
                "_tarafbilgisi" => $item->d_tarafbilgisi,
                "_islem"        => etAyracsizYazdir($item->d_islem),
                "_memurid"      => $item->d_memurid,
                "_avukat"       => $item->d_avukat,
                "_avukatid"     => $item->d_avukatid,
                "_taraf"        => $item->d_taraf,
                "_tags"         => drsTagYazdir($item->d_tags),
                "_aciklama"     => '',
                "_eklenmetarihi" => timeToDateFormat($item->d_adddate, "d.m.Y H:i"),
                "_ilgilimemur"  => etAyracsizYazdir($item->d_ilgilimemurlar),
                "_ilgiliavukat" => etAyracsizYazdir($item->d_ilgiliavukatlar),
                "_takip"        => drsTakipBilgisiYaz($item->d_takip),
                "_tutanak"      => drsTutanakBilgisiYaz($item->d_tutanak)
            );
        }
        $_t3 = microtime(true);

        if (count($itemsData) > 0) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = $totalRecord;
            $_sonuc->data                   = $itemsData;
            $_sonuc->success                = true;
            $_sonuc->code                   = 200;
            $_sonuc->description            = $totalRecord . " Adet Kayıt Bulundu.";
            $_sonuc->_perf = array('select_ms' => round(($_t1-$_t0)*1000), 'count_ms' => round(($_t2-$_t1)*1000), 'php_ms' => round(($_t3-$_t2)*1000), 'total_ms' => round(($_t3-$_t0)*1000));
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
            $_sonuc->code                   = 203;
            $_sonuc->description            = "Filtrelemeye Uygun Kayıt Bulunamadı.";
            $_sonuc->_perf = array('select_ms' => round(($_t1-$_t0)*1000), 'count_ms' => round(($_t2-$_t1)*1000), 'php_ms' => round(($_t3-$_t2)*1000), 'total_ms' => round(($_t3-$_t0)*1000));
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }

    

    ###END::DURUSMALARIM FONKSİYONLARI####




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
            $_sonuc->description        = "EDTS | Duruşmalar Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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
        $items = $this->durusmalar_model->ek_query_all($sql);
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
            $_sonuc->description            = "EDTS | Durusmalar Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        $searchText         = trim(replaceHtml_Slash($this->JSON_DATA->searchText));


        $sql = "SELECT mh_name FROM r8t_sys_mahkemeler WHERE mh_name LIKE '%" . $searchText . "%' GROUP BY mh_name";
        $items = $this->durusmalar_model->ek_query_all($sql);
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
            $_sonuc->description        = "EDTS | Durusmalar Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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
        $items = $this->durusmalar_model->ek_query_all($sql);
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
            $_sonuc->description        = "EDTS | Durusmalar Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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
        $items = $this->durusmalar_model->ek_query_all($sql);
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
            $_sonuc->description            = "EDTS | Duruşmalar Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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
        $items = $this->durusmalar_model->ek_query_all($sql);
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
            $_sonuc->description            = "EDTS | Duruşmalar Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        $searchText         = "";//trim(replaceHtml_Slash($this->JSON_DATA->searchText));


        $sql = "SELECT * FROM r8t_sys_mahkemeler WHERE mh_name LIKE '%" . $searchText . "%' GROUP BY mh_name";
        $items = $this->durusmalar_model->ek_query_all($sql);
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


        
    public function api_mahkemeEditrecord()
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
            $_sonuc->description        = "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden EDTS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if (!isDbAllowedUpdateModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "EDTS | Bu modülde Güncelleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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
        if (isset($this->JSON_DATA->route) == false || isset($this->JSON_DATA->mh_name) == false) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if ($this->JSON_DATA->route != "editmahkeme") {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Yönlendirilen Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $_mhId              = trim($this->JSON_DATA->mh_id);
        $_mhName             = trim($this->JSON_DATA->mh_name);
        $_mhName=ucwords($_mhName);
        $_mhId=(empty($_mhId))?0:$_mhId;

        $mahkemeVar=$this->mahkemeler_model->checkMahkemeAdi($_mhName,$_mhId);

        if (!empty($mahkemeVar)) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Mahkeme Adı zaten mevcut. Lütfen değiştirip tekrar deneyiniz!";
            $sonuc = json_encode($_sonuc);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }    
        
        if (empty($_mhId)) {

            $update = $this->mahkemeler_model->ek_add('r8t_sys_mahkemeler',
                array(
                    "mh_name"        => $_mhName
                )
            );

    
            if ($update) {
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
                $_sonuc->description        = "Ekleme İşleminde Hata Oluştu. Lütfen Sistem Yöneticinize Başvurunuz!";
                $sonuc = json_encode($_sonuc);
                SetHeader($_sonuc->code);
                echo $sonuc;
                exit;
            }
        }
        else {

            $update = $this->mahkemeler_model->update(
                array(
                    "mh_id"         => $_mhId
                ),
                array(
                    "mh_name"        => $_mhName
                )
            );
    
            if ($update) {
                $_sonuc =  new stdClass();
                $_sonuc->success            = true;
                $_sonuc->code                 = 200;
                $_sonuc->description        = "Tebrikler! Güncelleme İşlemi Başarılı.";
                $sonuc = json_encode($_sonuc);
                SetHeader($_sonuc->code);
                echo $sonuc;
                exit;
            } else {
                $_sonuc =  new stdClass();
                $_sonuc->success            = false;
                $_sonuc->code                 = 203;
                $_sonuc->description        = "Güncelleme İşleminde Hata Oluştu. Lütfen Sistem Yöneticinize Başvurunuz!";
                $sonuc = json_encode($_sonuc);
                SetHeader($_sonuc->code);
                echo $sonuc;
                exit;
            }            
        }
    


        

    }


    ### TAGIFY SEARCH FONKSIYONLARI BITIS #######

}

