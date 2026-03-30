<?php
/**
 * Dosya modülü (HEDAS) - PHP 8.3 uyumlu.
 */
ini_set("memory_limit", "256M");
defined('BASEPATH') or exit('No direct script access allowed');

class Dosya extends CI_Controller
{


    public $viewFolder     = "";
    public $userData        = false;

    public function __construct()
    {
        parent::__construct();

        $this->viewFolder = "dosya_v";
        $this->load->model("dosya_model");

        $this->load->model("auth_model");
        $this->userData = $this->auth_model->userData;
        $this->load->helper("dosya");
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
                "text"  => "HEDAS | Dosya Modülünü Görüntüleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(sys_url("home"));
            exit;
        }


        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "list";
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
                "text"  => "HEDAS | HEDAS Modülünde Görüntüleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(sys_url("home"));
            exit;
        }

        $searchFilter=$this->input->get("api_search");
        if ($searchFilter) {
            $searchKey=$this->input->get("name");
            $fromId=$this->input->get("from");
            $outputArr=dosyaIstatistikComboList($searchKey,$fromId);
            echo json_encode($outputArr);
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
        //$viewData->dosyaStats=array("d_davali"=>"DAVALI","d_davaci"=>"DAVACI"        ,"d_davakonusu"=>"DAVA KONUSU","d_icra"=>"İCRA","d_projebilgisi"=>"PROJE","d_istinafred"=>"İSTİNAF RED","d_istinafkabul"=>"İSTİNAF KABUL","d_onamailami"=>"ONAMA","d_bozmailami"=>"BOZMA");
        $viewData->dosyaStats=array("d_davali"=>"DAVALI","d_davaci"=>"DAVACI","d_davakonusu"=>"DAVA KONUSU","d_projebilgisi"=>"PROJE","d_mevkiplaka"=>"MEVKİ");


    
        $tarihDataDurusma = explode(" / ", (string)($viewData->durusmaAralik ?? ''));
        $viewData->current_durusma_start=trim($tarihDataDurusma[0]);
        $viewData->current_durusma_end=trim($tarihDataDurusma[1]);
        
    
        $viewData->filterMahkemeSelect=$this->input->get("filterMahkemeSelect");
        foreach ($viewData->dosyaStats as $kz=>$vz) {
            $viewData->$kz=$this->input->get("filtre_".$kz);
        }


        $filtreSql=$this->dosya_model->getAutoFiltre($viewData);
        $dataList=array();
        
        $dosyadavaci = $this->dosya_model->dosyaDavaci($filtreSql);
        $dosyadavali = $this->dosya_model->dosyaDavali($filtreSql);
        $dosyamahkeme = $this->dosya_model->dosyaMahkeme($filtreSql);
        $dosyamahkemeTotal = $this->dosya_model->dosyaMahkemeTotal($filtreSql);
        $dosyadavaaciklama = $this->dosya_model->dosyaDavaAciklama($filtreSql);

        $dosyaProje = $this->dosya_model->dosyaProje($filtreSql);
        $totx=$dosyaProje[0]->toplam;
        $dosyaMevki = $this->dosya_model->dosyaMevki($filtreSql);
        $totx2=$dosyaProje[0]->toplam;

        $loopData=array(array("mainTitle"=>"Proje Sayısı","subTitle"=>"Toplam Proje Sayısı","davalar"=>$dosyaProje,"toplamDava"=>$totx)
        ,array("mainTitle"=>"Mevki Sayısı","subTitle"=>"Toplam Mevki Sayısı","davalar"=>$dosyaMevki,"toplamDava"=>$totx2)
        

        );

        $viewData->loopData=$loopData;

    
            
        $viewData->dosyadavaci = $dosyadavaci;
        $viewData->dosyadavali = $dosyadavali;
        $viewData->dosyamahkeme = $dosyamahkeme;
        $viewData->dosyamahkemeTotal = $dosyamahkemeTotal;

        $viewData->dosyadavaaciklama = $dosyadavaaciklama;

 
        
        
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "istatistik";
        $viewData->userData             = $this->userData;
    
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        

    }

    ##DATATABLE LIST KOLON SIRALAMASI
    private function getTableColumnName($data = 0)
    {
        $columnName = "d_id";
        switch ($data) {
            case 0:
                $columnName = "d_id";
                break;    
            case 1:
                $columnName = "dm_acilistarihi";
                break;
            case 2:
                $columnName = "d_kurumdosyano";
                break;
            case 3:
                $columnName = "d_davaci";
                break;
            case 4:
                $columnName = "d_davali";
                break;
            case 5:
                $columnName = "d_davakonuaciklama";
                break;
            case 6:
                $columnName = "dm_mahkeme";
                break;
            case 7:
                $columnName = "dm_esasno";
                break;
            case 8:
                $columnName = "dm_kararno";
                break;
            case 9:
                $columnName = "d_mevkiplaka";
                break;    
            case 10:
                $columnName = "d_tags";
                break;
            default:
                $columnName = "d_id";
                break;
        }
        return $columnName;
    }

    /**
     * Tüm string değerleri geçerli UTF-8 yapar; json_encode Invalid JSON hatasını önler.
     * Özellikle Esas No ile "2015/12" gibi kısa aramalarda dönen bazı kayıtlardaki bozuk karakterler hataya yol açabiliyor.
     */
    private function ensureUtf8Recursive($data)
    {
        if (is_string($data)) {
            return function_exists('iconv') ? iconv('UTF-8', 'UTF-8//IGNORE', $data) : $data;
        }
        if (is_array($data)) {
            return array_map(array($this, 'ensureUtf8Recursive'), $data);
        }
        if (is_object($data)) {
            $out = new stdClass();
            foreach ($data as $k => $v) {
                $out->$k = $this->ensureUtf8Recursive($v);
            }
            return $out;
        }
        return $data;
    }

    public function api_list()
    {
        ob_start();

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
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Kullanıcı Yetkilendirme Hatası. Lütfen Giriş Bilgilerinizi Kontrol Ediniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            if (ob_get_level()) { ob_end_clean(); }
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
            if (ob_get_level()) { ob_end_clean(); }
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
            $_sonuc->description            = "HEDAS | Dosya Modülünü Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            if (ob_get_level()) { ob_end_clean(); }
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $_post          = json_encode($this->input->post());
        $postData       = json_decode($_post);

        // DataTables POST yapısı kontrolü - search/order/start/length gerekli; eksikse erken 400 (ağır sorgu çalışmasın)
        if (!is_object($postData)
            || !isset($postData->search) || !is_object($postData->search) || !isset($postData->search->value)
            || !isset($postData->order[0]->column)
            || !isset($postData->order[0]->dir)
            || !isset($postData->start)
            || !isset($postData->length)) {
            $_sonuc =  new stdClass();
            $_sonuc->draw                     = isset($postData->draw) ? (int) $postData->draw : (int) $this->input->post('draw');
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 400;
            $_sonuc->description            = "Sorgu parametreleri eksik veya geçersiz (search/order/start/length).";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            if (ob_get_level()) { ob_end_clean(); }
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        try {

        $searchValueRaw = $postData->search->value;
        $_searchValue   = is_string($searchValueRaw) ? json_decode($searchValueRaw) : (is_object($searchValueRaw) ? $searchValueRaw : null);
        if (!is_object($_searchValue)) {
            $_searchValue = new stdClass();
        }
        if (!isset($_searchValue->dTapuBilgisi) || in_array($_searchValue->dTapuBilgisi, array(-1, 0, 1, 2)) === false) {
            $_searchValue->dTapuBilgisi = 2;
        }
        $defaultStart   = date('d-m-Y', strtotime('-5 years'));
        $defaultEnd     = date('d-m-Y');
        if (empty($_searchValue->dAralik) || strpos($_searchValue->dAralik, ' & ') === false) {
            $_searchValue->dAralik = $defaultStart . ' & ' . $defaultEnd;
        }

        $_orderColumn   = $this->getTableColumnName($postData->order[0]->column);
        $_orderType     = $postData->order[0]->dir;
        $_kactan        = (int) $postData->start;
        $_kacar         = (int) $postData->length;
        $_kactan        = max(0, $_kactan);
        $_kacar         = min(500, max(1, $_kacar));

        $tarihData = explode(" & ", (string)($_searchValue->dAralik ?? ''));
        if (!isset($tarihData[1]) || validDate($tarihData[0], "d-m-Y") == false || validDate($tarihData[1], "d-m-Y") == false) {
            $tarihData = array($defaultStart, $defaultEnd);
        }
        $_tarihStart    = dateToTimeFormat($tarihData[0] . " 00:00:00", "d-m-Y H:i:s");
        $_tarihStop     = dateToTimeFormat($tarihData[1] . " 23:59:59", "d-m-Y H:i:s");


        $_dTags                 = trim(replaceHtml($_searchValue->dTags ?? ''));
        $_dIcraNo               = trim(replaceHtml_Slash($_searchValue->dIcraNo ?? ''));
        $_dKurumDosyaNo         = (strlen(trim(replaceHtml_Slash($_searchValue->dKurumDosyaNo ?? '')))>0)? (int)trim(replaceHtml_Slash($_searchValue->dKurumDosyaNo)) : trim(replaceHtml_Slash($_searchValue->dKurumDosyaNo ?? ''));
        $_dKurumDosyaNoT         = (int)trim(replaceHtml_Slash($_searchValue->dText ?? ''));
        $_dDavaci               = trim(replaceHtml_Slash($_searchValue->dDavaci ?? ''));
        $_dDavali               = trim(replaceHtml_Slash($_searchValue->dDavali ?? ''));
        $_dDavaKonusu           = trim(replaceHtml_Slash($_searchValue->dDavaKonusu ?? ''));
        $_dDavaKonuAciklama     = trim(replaceHtml_Slash($_searchValue->dDavaKonuAciklama ?? ''));
        $_dMevkiPlaka           = trim(replaceHtml_Slash($_searchValue->dMevkiPlaka ?? ''));
        $_dIcra                 = trim(replaceHtml_Slash($_searchValue->dIcra ?? ''));
        $_dTemyiz               = trim(replaceHtml_Slash($_searchValue->dTemyiz ?? ''));
        $_dIstinafTemyiz        = trim(replaceHtml_Slash($_searchValue->dIstinafTemyiz ?? ''));
        $_dIstinafKabul         = trim(replaceHtml_Slash($_searchValue->dIstinafKabul ?? ''));
        $_dIstinafRed           = trim(replaceHtml_Slash($_searchValue->dIstinafRed ?? ''));
        $_dBozmaIlami           = trim(replaceHtml_Slash($_searchValue->dBozmaIlami ?? ''));
        $_dOnamaIlami           = trim(replaceHtml_Slash($_searchValue->dOnamaIlami ?? ''));
        $_dMirascilik           = trim(replaceHtml_Slash($_searchValue->dMirascilik ?? ''));
        $_dTapuBilgisi          = (int)trim(replaceHtml_Slash($_searchValue->dTapuBilgisi ?? 2));
        $dMahkemeData           = isset($_searchValue->dMahkemeData) && is_object($_searchValue->dMahkemeData) ? $_searchValue->dMahkemeData : null;
        $_dmMahkeme             = ($dMahkemeData && isset($dMahkemeData->dmMahkeme)) ? trim(replaceHtml_Slash($dMahkemeData->dmMahkeme)) : '';
        $_dmEsasNo              = ($dMahkemeData && isset($dMahkemeData->dmEsasNo)) ? trim(replaceHtml_Slash($dMahkemeData->dmEsasNo)) : '';

        $_dArsivNo               = isset($_searchValue->dArsivNo) ? trim(replaceHtml_Slash($_searchValue->dArsivNo)) : '';

        $sql = "";
        $isFilter = false;
        $sqlEk = "";
        $isIntegerFilter = false;
        $isIntegerEk = "";

        $likeEk = "";
        $_likeIsEk = false;

        // if (strlen($_dTags) > 0) {
        //     if ($isFilter == true) {
        //         $sqlEk .= " AND d_tags LIKE '%" . $_dTags . "%'";
        //     } else {
        //         $sqlEk .= "d_tags LIKE '%" . $_dTags . "%'";
        //     }
        //     $isFilter = true;
        // }

        if (strlen($_dIcraNo) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_projebilgisi LIKE '%" . $_dIcraNo . "%'";
            } else {
                $sqlEk .= "d_projebilgisi LIKE '%" . $_dIcraNo . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dKurumDosyaNo) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_kurumdosyano='" . $_dKurumDosyaNo."'";
            } else {
                $sqlEk .= "d_kurumdosyano='" . $_dKurumDosyaNo."'";
            }
            $isFilter = true;
        }else{
            if($_dKurumDosyaNoT!=0){
                $isIntegerEk .= "OR d_kurumdosyano='" . $_dKurumDosyaNoT."'";  
                $isIntegerFilter = true;
            }
        }

        if (strlen($_dDavaci) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_davaci LIKE '%" . $_dDavaci . "%'";
            } else {
                $sqlEk .= "d_davaci LIKE '%" . $_dDavaci . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dDavali) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_davali LIKE '%" . $_dDavali . "%'";
            } else {
                $sqlEk .= "d_davali LIKE '%" . $_dDavali . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dDavaKonusu) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_davakonusu LIKE '%" . $_dDavaKonusu . "%'";
            } else {
                $sqlEk .= "d_davakonusu LIKE '%" . $_dDavaKonusu . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dDavaKonuAciklama) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_davakonuaciklama LIKE '%" . $_dDavaKonuAciklama . "%'";
            } else {
                $sqlEk .= "d_davakonuaciklama LIKE '%" . $_dDavaKonuAciklama . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dMevkiPlaka) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_mevkiplaka LIKE '%" . $_dMevkiPlaka . "%'";
            } else {
                $sqlEk .= "d_mevkiplaka LIKE '%" . $_dMevkiPlaka . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dIcra) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_icra LIKE '%" . $_dIcra . "%'";
            } else {
                $sqlEk .= "d_icra LIKE '%" . $_dIcra . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dTemyiz) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_aciklama LIKE '%" . $_dTemyiz . "%'";
            } else {
                $sqlEk .= "d_aciklama LIKE '%" . $_dTemyiz . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dIstinafTemyiz) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_istinaftemyiz LIKE '%" . $_dIstinafTemyiz . "%'";
            } else {
                $sqlEk .= "d_istinaftemyiz LIKE '%" . $_dIstinafTemyiz . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dIstinafKabul) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_istinafkabul LIKE '%" . $_dIstinafKabul . "%'";
            } else {
                $sqlEk .= "d_istinafkabul LIKE '%" . $_dIstinafKabul . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dIstinafRed) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_istinafred LIKE '%" . $_dIstinafRed . "%'";
            } else {
                $sqlEk .= "d_istinafred LIKE '%" . $_dIstinafRed . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dBozmaIlami) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_bozmailami LIKE '%" . $_dBozmaIlami . "%'";
            } else {
                $sqlEk .= "d_bozmailami LIKE '%" . $_dBozmaIlami . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dOnamaIlami) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_onamailami LIKE '%" . $_dOnamaIlami . "%'";
            } else {
                $sqlEk .= "d_onamailami LIKE '%" . $_dOnamaIlami . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dMirascilik) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_mirascilik LIKE '%" . $_dMirascilik . "%'";
            } else {
                $sqlEk .= "d_mirascilik LIKE '%" . $_dMirascilik . "%'";
            }
            $isFilter = true;
        }

        if (in_array($_dTapuBilgisi, array(-1, 0, 1)) === true) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_tapubilgisi = (" . $_dTapuBilgisi . ")";
            } else {
                $sqlEk .= "d_tapubilgisi = (" . $_dTapuBilgisi . ")";
            }
            $isFilter = true;
        }

        $needsMahkemeJoin = false;
        if (strlen($_dmMahkeme) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND dm_mahkeme LIKE '%" . $_dmMahkeme . "%'";
            } else {
                $sqlEk .= "dm_mahkeme LIKE '%" . $_dmMahkeme . "%'";
            }
            $isFilter = true;
            $needsMahkemeJoin = true;
        }

        if (strlen($_dmEsasNo) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND dm_esasno LIKE '%" . $_dmEsasNo . "%'";
            } else {
                $sqlEk .= "dm_esasno LIKE '%" . $_dmEsasNo . "%'";
            }
            $isFilter = true;
            $needsMahkemeJoin = true;
        }

        if (strlen($_dArsivNo) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_arsivno LIKE '%" . $_dArsivNo . "%'";
            } else {
                $sqlEk .= "d_arsivno LIKE '%" . $_dArsivNo . "%'";
            }
            $isFilter = true;
        }

        if (strlen(trim(replaceHtml_Slash($_searchValue->dText ?? ''))) > 0) {

            $gelenText = trim(replaceHtml_Slash($_searchValue->dText ?? ''));

            $likeEk .= "d_arsivno LIKE '%".$gelenText."%'";
            if($_dKurumDosyaNo==0 && $_dKurumDosyaNoT!=0){

                $likeEk .= " OR d_kurumdosyano LIKE '%".$_dKurumDosyaNoT."%'";
            }
            if($_dKurumDosyaNoT==0){
                $likeEk .= " OR d_davaci LIKE '%".$gelenText."%'";
                $likeEk .= " OR d_davali LIKE '%".$gelenText."%'";
                $likeEk .= " OR d_davakonusu LIKE '%".$gelenText."%'";
                $likeEk .= " OR d_icra LIKE '%".$gelenText."%'";
                $likeEk .= " OR d_arsivno LIKE '%".$gelenText."%'";
            }
            $likeEk .= " OR d_davakonuaciklama LIKE '%".$gelenText."%'";
            $likeEk .= " OR d_projebilgisi LIKE '%".$gelenText."%'";
            $likeEk .= " OR d_mevkiplaka LIKE '%".$gelenText."%'";
            $likeEk .= " OR d_istinaftemyiz LIKE '%".$gelenText."%'";
            $likeEk .= " OR d_temyiz LIKE '%".$gelenText."%'";
            $likeEk .= " OR d_istinafkabul LIKE '%".$gelenText."%'";
            $likeEk .= " OR d_istinafred LIKE '%".$gelenText."%'";
            $likeEk .= " OR d_onamailami LIKE '%".$gelenText."%'";
            $likeEk .= " OR d_bozmailami LIKE '%".$gelenText."%'";
            $likeEk .= " OR d_icrakayitno LIKE '%".$gelenText."%'";
            $likeEk .= " OR d_mirascilik LIKE '%".$gelenText."%'";
            $likeEk .= " OR d_kararkesinlestirme LIKE '%".$gelenText."%'";
            // $likeEk .= " OR d_idarialacagi LIKE '%".$gelenText."%'";
            // $likeEk .= " OR d_vekaletalacagi LIKE '%".$gelenText."%'";
            // $likeEk .= " OR d_yargilamagideri LIKE '%".$gelenText."%'";
            $likeEk .= " OR d_aciklama LIKE '%".$gelenText."%'";
            $likeEk .= " OR d_tags LIKE '%".$gelenText."%'";
            $likeEk .= " OR dm_esasno LIKE '%".$gelenText."%'";
            $likeEk .= " OR dm_kararno LIKE '%".$gelenText."%'";
            $likeEk .= " OR dm_mahkeme LIKE '%".$gelenText."%'";
            $likeEk .= " OR dm_aciklama LIKE '%".$gelenText."%'";
            $_likeIsEk = true;
        }



        if ($_likeIsEk == true) {
            $sql .= " (" . $likeEk . " " . $isIntegerEk . ")";
        }


        if ($isFilter == true) {
            if($_likeIsEk == true){
                $sql .= " AND";
            }
            $sql .= " (" . $sqlEk . ")";
        }

        if ($isFilter == true || $_likeIsEk==true) {
                $sql .= " AND";
        }
        $sql .= " d_status=1";
        
        if($isFilter==false && $_likeIsEk==false){
            $sql .= " AND (d_adddate >= " . trim(replaceHtml($_tarihStart)) . " AND d_adddate <= " . trim(replaceHtml($_tarihStop)) . ")";
        }




        $_needsDmColumns = in_array($_orderColumn, array('dm_acilistarihi', 'dm_mahkeme', 'dm_esasno', 'dm_kararno'));
        $useJoin = $needsMahkemeJoin || $_likeIsEk || $_needsDmColumns;

        $joinClause = $useJoin ? " LEFT JOIN r8t_edys_dosya_mahkemeler ON dm_dosyaid=d_id" : "";
        $groupClause = $useJoin ? " GROUP BY d_id" : "";
        $countExpr = $useJoin ? "COUNT(DISTINCT d_id)" : "COUNT(*)";

        $totalSql = "SELECT " . $countExpr . " AS total FROM r8t_edys_dosya" . $joinClause . " WHERE" . $sql;
        $totalRecordS = $this->dosya_model->ek_query_all($totalSql);
        $totalRecord = (int)$totalRecordS[0]->total;

        $selectCols = "d_id, d_arsivno, d_icrakayitno, d_kurumdosyano, d_davaci, d_davali, d_davakonusu, d_davakonuaciklama, d_mevkiplaka, d_projebilgisi, d_icra, d_temyiz, d_istinaftemyiz, d_istinafkabul, d_istinafred, d_bozmailami, d_onamailami, d_kararkesinlestirme, d_mirascilik, d_idarialacagi, d_vekaletalacagi, d_yargilamagideri, d_tapubilgisi, d_tags, d_aciklama";
        if ($_needsDmColumns) {
            $selectCols .= ", dm_acilistarihi, dm_mahkeme, dm_esasno, dm_kararno";
        }

        $dataSql = "SELECT " . $selectCols . " FROM r8t_edys_dosya" . $joinClause . " WHERE" . $sql;
        $dataSql .= $groupClause;
        $dataSql .= " ORDER BY " . $_orderColumn . " " . strtoupper($_orderType);
        $dataSql .= " LIMIT " . $_kactan . ", " . $_kacar;

        $filterRecord = $this->dosya_model->ek_query_all($dataSql);
        if (!$filterRecord) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Filtrelemeye Uygun Kayıt Bulunamadı.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            if (ob_get_level()) { ob_end_clean(); }
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        // Toplu mahkeme yükleme (tek sorgu ile)
        $d_ids = array_map(function($item) { return (int)$item->d_id; }, $filterRecord);
        $mahkemelerByDosya = array();
        if (!empty($d_ids)) {
            $idsStr = implode(',', $d_ids);
            $mahkemelerAll = $this->dosya_model->ek_query_all("SELECT dm_id, dm_dosyaid, dm_acilistarihi, dm_esasno, dm_karartarihi, dm_kararno, dm_mahkeme, dm_aciklama, dm_adddate, dm_adduser FROM r8t_edys_dosya_mahkemeler WHERE dm_dosyaid IN (" . $idsStr . ")");
            if ($mahkemelerAll) {
                foreach ($mahkemelerAll as $mahkeme) {
                    $mahkemelerByDosya[$mahkeme->dm_dosyaid][] = $mahkeme;
                }
            }
        }

        $itemsData = array();
        foreach ($filterRecord as $item) {

            $mahkemeData = array();
            $itemMahkemeler = isset($mahkemelerByDosya[$item->d_id]) ? $mahkemelerByDosya[$item->d_id] : array();
            $firstMahkeme = !empty($itemMahkemeler) ? $itemMahkemeler[0] : null;
            if ($itemMahkemeler) {
                foreach ($itemMahkemeler as $mahkeme) {
                    $mahkemeArray = array(
                        "_id"   => $mahkeme->dm_id,
                        "_acilistarihi" => timeToDateFormat($mahkeme->dm_acilistarihi, "d.m.Y"),
                        "_esasno"       => $mahkeme->dm_esasno,
                        "_karartarihi"  => timeToDateFormat($mahkeme->dm_karartarihi, "d.m.Y"),
                        "_kararno"  => $mahkeme->dm_kararno,
                        "_mahkeme"  => dAyracsizYazdir($mahkeme->dm_mahkeme),
                        "_maciklama"    => $mahkeme->dm_aciklama,
                        "_kayittarihi"  => timeToDateFormat($mahkeme->dm_adddate, "d.m.Y"),
                        "_kayituserid"    => $mahkeme->dm_adduser
                    );
                    array_push($mahkemeData, $mahkemeArray);
                }
            }

            $itemArray = array(
                "_id"           => $item->d_id,
                "_arsivno"       => ($item->d_arsivno),
                "_icrano"     => ($item->d_icrakayitno),
                "_kurumdosyano" => ($item->d_kurumdosyano),
                "_davaci"   => dAyracsizYazdir($item->d_davaci),
                "_davali"     => dAyracsizYazdir($item->d_davali),
                "_davakonu"      => dAyracsizYazdir($item->d_davakonusu),
                "_davakonuaciklama" => dAyracsizYazdir($item->d_davakonuaciklama),
                "_mevkiplaka"      => html_entity_decode($item->d_mevkiplaka, ENT_QUOTES),
                "_proje"   => html_entity_decode($item->d_projebilgisi, ENT_QUOTES),
                "_icra"        => dAyracsizYazdir($item->d_icra),
                "_temyiz"   => ($item->d_temyiz),
                "_istinaftemyiz"        => ($item->d_istinaftemyiz),
                "_istinafkabul"         => dAyracsizYazdir($item->d_istinafkabul),
                "_istinafred"         => dAyracsizYazdir($item->d_istinafred),
                "_bozmailami"         => dAyracsizYazdir($item->d_bozmailami),
                "_onamailami"         => dAyracsizYazdir($item->d_onamailami),
                "_kesinlestirme"         => dAyracsizYazdir($item->d_kararkesinlestirme),
                "_mirascilik"         => dAyracsizYazdir($item->d_mirascilik),
                "_idarialacagi"         => number_format((float)($item->d_idarialacagi ?? 0), 2, ',', '.') . "₺",
                "_vekaletalacagi"         => number_format((float)($item->d_vekaletalacagi ?? 0), 2, ',', '.') . "₺",
                "_yargilamagideri"         => number_format((float)($item->d_yargilamagideri ?? 0), 2, ',', '.') . "₺",
                "_tapubilgisi"         => dTapuBilgiYazdir($item->d_tapubilgisi),
                "_tags"         => dTagYazdir($item->d_tags),
                "_aciklama"     => html_entity_decode($item->d_aciklama, ENT_QUOTES),
                "_acilistarihi" => $firstMahkeme ? timeToDateFormat($firstMahkeme->dm_acilistarihi, "d.m.Y") : "",
                "_mahkeme"      => $firstMahkeme ? dAyracsizYazdir($firstMahkeme->dm_mahkeme) : "",
                "_esasno"       => $firstMahkeme ? $firstMahkeme->dm_esasno : "",
                "_kararno"       => $firstMahkeme ? $firstMahkeme->dm_kararno : "",
                "_mahkemeler"     => $mahkemeData
            );

            array_push($itemsData, $itemArray);
        }

        $draw = isset($postData->draw) ? (int) $postData->draw : 0;

        $jsonFlags = JSON_UNESCAPED_UNICODE;
        if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
            $jsonFlags |= JSON_INVALID_UTF8_SUBSTITUTE;
        }

        if (count($itemsData) > 0) {
            $_sonuc =  new stdClass();
            $_sonuc->draw                     = $draw;
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = $totalRecord;
            $_sonuc->data                   = $this->ensureUtf8Recursive($itemsData);
            $_sonuc->success                = true;
            $_sonuc->code                   = 200;
            $_sonuc->description            = $totalRecord . " Adet Kayıt Bulundu.";
            $sonuc = json_encode($_sonuc, $jsonFlags);
            if ($sonuc === false) {
                $_sonuc->data = array();
                $_sonuc->description = "Kayitlar gosterilemedi (kodlama hatasi).";
                $sonuc = json_encode($_sonuc, $jsonFlags);
            }
            if (ob_get_level()) { ob_end_clean(); }
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->draw                     = $draw;
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Filtrelemeye Uygun Kayıt Bulunamadı.";
            $sonuc = json_encode($_sonuc, $jsonFlags);
            if (ob_get_level()) { ob_end_clean(); }
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        } catch (Throwable $e) {
            log_message('error', 'Dosya::api_list 5xx: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            if (ob_get_level()) { ob_end_clean(); }
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(503);
            echo json_encode(array(
                'success' => false,
                'code' => 503,
                'description' => 'Geçici bir sunucu hatası oluştu. Lütfen kısa süre sonra tekrar deneyin.',
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => array()
            ), JSON_UNESCAPED_UNICODE);
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

        if (!isDbAllowedListModule()) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "HEDAS | Bu Modülü Görüntüleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.",
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
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Kullanıcı Yetkilendirme Hatası. Lütfen Giriş Bilgilerinizi Kontrol Ediniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
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
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Uygulama Yetkilendirme Hatası. Sistem Yöneticinizden EDTS Uygulaması İçin Görüntüleme Yetkisi İsteyiniz.";
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
            $_sonuc->description            = "HEDAS | Bu modülde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $_post          = json_encode($this->input->post());
        $postData       = json_decode($_post);
        $_searchValue   = json_decode($postData->search->value);
        $_orderColumn   = $this->getHareketlerTableColumnName($postData->order[0]->column);
        $_orderType     = $postData->order[0]->dir;
        $_kactan        = $postData->start;
        $_kacar         = $postData->length;
        $_kactan        = max(0, (int) $_kactan);
        $_kacar         = min(500, max(1, (int) $_kacar));

        //GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
        if (isset($_orderType) === false || in_array($_orderType, array("desc", "asc")) === false) {
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

        $tarihData = explode(" & ", (string)($_searchValue->chKayitTarih ?? ''));
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
                $sqlEk .= " OR ul_newdata LIKE '%" . $_arananText . "%'";
                $sqlEk .= " OR ul_olddata LIKE '%" . $_arananText . "%'";
                $sqlEk .= " OR d_kurumdosyano LIKE '%" . $_arananText . "%'";
                $sqlEk .= " OR d_davaci LIKE '%" . $_arananText . "%'";
                $sqlEk .= " OR u_name LIKE '%" . $_arananText . "%'";
                $sqlEk .= " OR u_surname LIKE '%" . $_arananText . "%'";
            } else {
                $sqlEk .= "ul_newdata LIKE '%" . $_arananText . "%'";
                $sqlEk .= " OR ul_olddata LIKE '%" . $_arananText . "%'";
                $sqlEk .= " OR d_kurumdosyano LIKE '%" . $_arananText . "%'";
                $sqlEk .= " OR d_davaci LIKE '%" . $_arananText . "%'";

                $sqlEk .= " OR u_name LIKE '%" . $_arananText . "%'";
                $sqlEk .= " OR u_surname LIKE '%" . $_arananText . "%'";
            }
            $_ekVar = true;
            $_say++;
        }

        if ($_ekVar == true) {
            $sql .= " AND (" . $sqlEk . ")";
        }

        $totalSql = "SELECT COUNT(ul_id) AS total FROM r8t_sys_userlogs LEFT JOIN r8t_edys_dosya ON d_id=ul_icerikid LEFT JOIN r8t_users ON u_id=ul_userid WHERE ul_app='hedas' AND ul_modul IN ('dosya') AND (ul_adddate >= " . trim(replaceHtml($_tarihStart)) . " AND ul_adddate <= " . trim(replaceHtml($_tarihStop)) . ")" . $sql;
        
        $totalRecordS = $this->dosya_model->ek_query_all($totalSql);

        $totalRecord = (int)$totalRecordS[0]->total;



        $sql .= " ORDER BY " . $_orderColumn . " " . strtoupper($_orderType);
        $sql .= " LIMIT " . $_kactan . ", " . $_kacar;

        $filterSql = "SELECT ul_id, ul_adddate, ul_tur, ul_aciklama, ul_olddata, ul_newdata, d_kurumdosyano, d_davaci, u_name, u_lastname, u_surname FROM r8t_sys_userlogs LEFT JOIN r8t_edys_dosya ON d_id=ul_icerikid LEFT JOIN r8t_users ON u_id=ul_userid WHERE ul_app='hedas' AND ul_modul IN ('dosya')  AND (ul_adddate >= " . trim(replaceHtml($_tarihStart)) . " AND ul_adddate <= " . trim(replaceHtml($_tarihStop)) . ")" . $sql;
          //die($filterSql);

        $filterRecord = $this->dosya_model->ek_query_all($filterSql);

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
            $islemYapan = $item->u_name . ' ' . $item->u_lastname;
            $islemYapan = trim($islemYapan) . ' ' . $item->u_surname;

            $itemArray = array(
                "_id"           => $item->ul_id,
                "_kayittarihi"   => timeToDateFormat($item->ul_adddate, "d.m.Y H:i"),
                "_islemturu"     => ckmHareketTurYazdir($item->ul_tur),
                "_islemyapan"     => $islemYapan,
                "_dosyano"        => $item->d_kurumdosyano,
                "_avukat"       => $item->d_davaci,
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
                $columnName = "ul_aciklama";
                break;
            default:
                $columnName = "ul_id";
                break;
        }
        return $columnName;
    }



    public function api_listDashboard()
    {
        $_post          = json_encode($this->input->post());
        $postData       = json_decode($_post);

        if (!is_object($postData) || !isset($postData->search->value) || !isset($postData->order[0]->column) || !isset($postData->order[0]->dir) || !isset($postData->start) || !isset($postData->length)) {
            $_sonuc =  new stdClass();
            $_sonuc->draw                     = (int) $this->input->post('draw');
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 400;
            $_sonuc->description            = "Sorgu parametreleri eksik veya geçersiz (api_listDashboard).";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader(400);
            echo $sonuc;
            exit;
        }

        $_searchValue   = json_decode($postData->search->value);
        $_orderColumn   = $this->getTableColumnName($postData->order[0]->column);
        $_orderType     = $postData->order[0]->dir;
        $_kactan        = (int) $postData->start;
        $_kacar         = (int) $postData->length;
        $_kactan        = max(0, $_kactan);
        $_kacar         = min(500, max(1, $_kacar));

        //GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
        if (!is_object($_searchValue) || isset($_searchValue->dAralik) === false || in_array($_searchValue->dTapuBilgisi ?? -999, array(-1, 0, 1, 2)) === false) {
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

        $tarihData = explode(" & ", (string)($_searchValue->dAralik ?? ''));
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
        $sqlEk = " dos.d_status=1 AND (dos.d_adddate >= " . trim(replaceHtml($_tarihStart)) . " AND dos.d_adddate <= " . trim(replaceHtml($_tarihStop)) . ")";

        $_dTextDash = trim(replaceHtml($_searchValue->dText ?? ''));
        if (strlen($_dTextDash) > 0) {
            $sqlEk .= " AND (dos.d_arsivno LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dos.d_id LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dos.d_kurumdosyano LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dos.d_davaci LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dos.d_davali LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dos.d_davakonusu LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dos.d_davakonuaciklama LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dos.d_projebilgisi LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dos.d_mevkiplaka LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dos.d_istinaftemyiz LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dos.d_temyiz LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dos.d_istinafkabul LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dos.d_istinafred LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dos.d_onamailami LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dos.d_bozmailami LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dos.d_icrakayitno LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dos.d_icra LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dos.d_mirascilik LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dos.d_kararkesinlestirme LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dos.d_idarialacagi LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dos.d_vekaletalacagi LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dos.d_yargilamagideri LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dos.d_aciklama LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dos.d_tags LIKE '%" . $_dTextDash . "%'";

            $sqlEk .= " OR dm.dm_dosyaid LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dm.dm_esasno LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dm.dm_kararno LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dm.dm_mahkeme LIKE '%" . $_dTextDash . "%'";
            $sqlEk .= " OR dm.dm_aciklama LIKE '%" . $_dTextDash . "%')";
        }
        $_dTapuDash = (int) trim(replaceHtml($_searchValue->dTapuBilgisi ?? 2));
        if (in_array($_dTapuDash, array(0, 1)) === true) {
            $sqlEk .= " AND dos.d_tapubilgisi LIKE '%" . $_dTapuDash . "%'";
        }

        $totalSql = "SELECT COUNT(d_id) AS total
        FROM (
            SELECT d_id
            FROM r8t_edys_dosya
            WHERE d_status = 1
            ORDER BY d_id DESC
            LIMIT 5
        ) AS subquery";
        $totalRecordS = $this->dosya_model->ek_query_all($totalSql);
        $totalRecord = (int)$totalRecordS[0]->total;

        //die($totalSql);

        $sql = "SELECT dos.d_id, dos.d_arsivno, dos.d_icrakayitno, dos.d_kurumdosyano, dos.d_davaci, dos.d_davali, dos.d_davakonusu, dos.d_davakonuaciklama, dos.d_mevkiplaka, dos.d_projebilgisi, dos.d_icra, dos.d_temyiz, dos.d_istinaftemyiz, dos.d_istinafkabul, dos.d_istinafred, dos.d_bozmailami, dos.d_onamailami, dos.d_kararkesinlestirme, dos.d_mirascilik, dos.d_idarialacagi, dos.d_vekaletalacagi, dos.d_yargilamagideri, dos.d_tapubilgisi, dos.d_tags, dos.d_aciklama FROM r8t_edys_dosya AS dos LEFT JOIN r8t_edys_dosya_mahkemeler AS dm ON dm.dm_dosyaid=dos.d_id WHERE" . $sqlEk;
        $sql .= " ORDER BY d_id DESC";
        $sql .= " LIMIT 5 ";

        $filterRecord = $this->dosya_model->ek_query_all($sql);
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
        $discardSayac = 0;
        foreach ($filterRecord as $item) {

            $mahkemeler = $this->dosya_model->ek_get_all(
                "r8t_edys_dosya_mahkemeler",
                array(
                    "dm_dosyaid"    => $item->d_id
                )
            );
            $mahkemeData = array();
            if ($mahkemeler) {
                foreach ($mahkemeler as $mahkeme) {
                    $mahkemeArray = array(
                        "_id"   => $mahkeme->dm_id,
                        "_acilistarihi" => timeToDateFormat($mahkeme->dm_acilistarihi, "d.m.Y"),
                        "_esasno"       => $mahkeme->dm_esasno,
                        "_karartarihi"  => timeToDateFormat($mahkeme->dm_karartarihi, "d.m.Y"),
                        "_kararno"  => $mahkeme->dm_kararno,
                        "_mahkeme"  => dAyracsizYazdir($mahkeme->dm_mahkeme),
                        "_maciklama"    => $mahkeme->dm_aciklama,
                        "_kayittarihi"  => timeToDateFormat($mahkeme->dm_adddate, "d.m.Y"),
                        "_kayituserid"    => $mahkeme->dm_adduser
                    );
                    array_push($mahkemeData, $mahkemeArray);
                }
            }
            $itemArray = array(
                "_id"           => $item->d_id,
                "_arsivno"       => ($item->d_arsivno),
                "_icrano"     => ($item->d_icrakayitno),
                "_kurumdosyano" => ($item->d_kurumdosyano),
                "_davaci"   => dAyracsizYazdir($item->d_davaci),
                "_davali"     => dAyracsizYazdir($item->d_davali),
                "_davakonu"      => dAyracsizYazdir($item->d_davakonusu),
                "_davakonuaciklama"       => dAyracsizYazdir($item->d_davakonuaciklama),
                "_mevkiplaka"      => html_entity_decode($item->d_mevkiplaka, ENT_QUOTES),
                "_proje"   => html_entity_decode($item->d_projebilgisi, ENT_QUOTES),
                "_icra"        => dAyracsizYazdir($item->d_icra),
                "_temyiz"   => ($item->d_temyiz),
                "_istinaftemyiz"        => ($item->d_istinaftemyiz),
                "_istinafkabul"         => dAyracsizYazdir($item->d_istinafkabul),
                "_istinafred"         => dAyracsizYazdir($item->d_istinafred),
                "_bozmailami"         => dAyracsizYazdir($item->d_bozmailami),
                "_onamailami"         => dAyracsizYazdir($item->d_onamailami),
                "_kesinlestirme"         => dAyracsizYazdir($item->d_kararkesinlestirme),
                "_mirascilik"         => dAyracsizYazdir($item->d_mirascilik),
                "_idarialacagi"         => number_format((float)($item->d_idarialacagi ?? 0), 2, ',', '.') . "₺",
                "_vekaletalacagi"         => number_format((float)($item->d_vekaletalacagi ?? 0), 2, ',', '.') . "₺",
                "_yargilamagideri"         => number_format((float)($item->d_yargilamagideri ?? 0), 2, ',', '.') . "₺",
                "_tapubilgisi"         => dTapuBilgiYazdir($item->d_tapubilgisi),
                "_tags"         => dTagYazdir($item->d_tags),
                "_aciklama"     => html_entity_decode($item->d_aciklama, ENT_QUOTES),
                "_mahkemeler"     => $mahkemeData
            );
            if (in_array($itemArray, $itemsData)) {
                $discardSayac++;
            } else {
                array_push($itemsData, $itemArray);
            }
        }


        $draw = isset($postData->draw) ? (int) $postData->draw : 0;
        $jsonFlags = JSON_UNESCAPED_UNICODE;
        if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
            $jsonFlags |= JSON_INVALID_UTF8_SUBSTITUTE;
        }

        if (count($itemsData) > 0) {
            $_sonuc =  new stdClass();
            $_sonuc->draw                     = $draw;
            $_sonuc->recordsTotal           = $totalRecord - $discardSayac;
            $_sonuc->recordsFiltered        = $totalRecord - $discardSayac;
            $_sonuc->data                   = $this->ensureUtf8Recursive($itemsData);
            $_sonuc->success                = true;
            $_sonuc->code                   = 200;
            $_sonuc->description            = ($totalRecord - $discardSayac) . " Adet Kayıt Bulundu.";
            $sonuc = json_encode($_sonuc, $jsonFlags);
            if ($sonuc === false) {
                $_sonuc->data = array();
                $_sonuc->description = "Kayitlar gosterilemedi (kodlama hatasi).";
                $sonuc = json_encode($_sonuc, $jsonFlags);
            }
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->draw                     = $draw;
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Filtrelemeye Uygun Kayıt Bulunamadı.";
            $sonuc = json_encode($_sonuc, $jsonFlags);
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
            $_sonuc->description        = "HEDAS | Dosya Modülünde Ekleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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
        if (isset($this->JSON_DATA->route) == false || isset($this->JSON_DATA->dDosyaNo) == false) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if ($this->JSON_DATA->route != "adddavadosya") {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Yönlendirilen Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $_dKlasorNo         = trim(replaceHtml_Slash($this->JSON_DATA->dKlasorNo));
        $_dDosyaNo          = trim(replaceHtml_Slash($this->JSON_DATA->dDosyaNo));
        $_dDavaci           = str_replace("[{", "", $this->JSON_DATA->dDavaci);
        $_dDavaci           = str_replace("}]", "", $_dDavaci);
        $_dDavaci           = str_replace("},{", "", $_dDavaci);
        $_dDavaci           = str_replace('"', "", $_dDavaci);
        $_dDavali           = str_replace("[{", "", $this->JSON_DATA->dDavali);
        $_dDavali           = str_replace("}]", "", $_dDavali);
        $_dDavali           = str_replace("},{", "", $_dDavali);
        $_dDavali           = str_replace('"', "", $_dDavali);
        $_dDavaKonusu           = str_replace("[{", "", $this->JSON_DATA->dDavaKonusu);
        $_dDavaKonusu           = str_replace("}]", "", $_dDavaKonusu);
        $_dDavaKonusu           = str_replace("},{", "", $_dDavaKonusu);
        $_dDavaKonusu           = str_replace('"', "", $_dDavaKonusu);
        $_dKonuAciklama           = str_replace("[{", "", $this->JSON_DATA->dKonuAciklamasi);
        $_dKonuAciklama           = str_replace("}]", "", $_dKonuAciklama);
        $_dKonuAciklama           = str_replace("},{", "", $_dKonuAciklama);
        $_dKonuAciklama           = str_replace('"', "", $_dKonuAciklama);
        $_dProjeBilgi         = htmlentities($this->JSON_DATA->dProjeBilgi, ENT_QUOTES); //trim(replaceHtml_Slash($this->JSON_DATA->dProjeBilgi));
        $_dMevkiBilgi         = htmlentities($this->JSON_DATA->dMevkiBilgi, ENT_QUOTES); //trim(replaceHtml_Slash($this->JSON_DATA->dMevkiBilgi));
        $_dIstinafBilgi         = trim(replaceHtml_Slash($this->JSON_DATA->dIstinafBilgi));
        $_dTemyizBilgi         = trim(replaceHtml_Slash($this->JSON_DATA->dTemyizBilgi));
        $_dOnamaIlami           = str_replace("[{", "", $this->JSON_DATA->dOnamaIlami);
        $_dOnamaIlami           = str_replace("}]", "", $_dOnamaIlami);
        $_dOnamaIlami           = str_replace("},{", "", $_dOnamaIlami);
        $_dOnamaIlami           = str_replace('"', "", $_dOnamaIlami);
        $_dBozmaIlami           = str_replace("[{", "", $this->JSON_DATA->dBozmaIlami);
        $_dBozmaIlami           = str_replace("}]", "", $_dBozmaIlami);
        $_dBozmaIlami           = str_replace("},{", "", $_dBozmaIlami);
        $_dBozmaIlami           = str_replace('"', "", $_dBozmaIlami);
        $_dIstinafKabul           = str_replace("[{", "", $this->JSON_DATA->dIstinafKabul);
        $_dIstinafKabul           = str_replace("}]", "", $_dIstinafKabul);
        $_dIstinafKabul           = str_replace("},{", "", $_dIstinafKabul);
        $_dIstinafKabul           = str_replace('"', "", $_dIstinafKabul);
        $_dIstinafRed           = str_replace("[{", "", $this->JSON_DATA->dIstinafRed);
        $_dIstinafRed           = str_replace("}]", "", $_dIstinafRed);
        $_dIstinafRed           = str_replace("},{", "", $_dIstinafRed);
        $_dIstinafRed           = str_replace('"', "", $_dIstinafRed);
        $_dIcraNo          = trim(replaceHtml_Slash($this->JSON_DATA->dIcraNo));
        $_dIcra           = str_replace("[{", "", $this->JSON_DATA->dIcra);
        $_dIcra           = str_replace("}]", "", $_dIcra);
        $_dIcra           = str_replace("},{", "", $_dIcra);
        $_dIcra           = str_replace('"', "", $_dIcra);
        $_dKesinlestirme           = str_replace("[{", "", $this->JSON_DATA->dKesinlestirme);
        $_dKesinlestirme           = str_replace("}]", "", $_dKesinlestirme);
        $_dKesinlestirme           = str_replace("},{", "", $_dKesinlestirme);
        $_dKesinlestirme           = str_replace('"', "", $_dKesinlestirme);
        $_dMirascilik           = str_replace("[{", "", $this->JSON_DATA->dMirascilik);
        $_dMirascilik           = str_replace("}]", "", $_dMirascilik);
        $_dMirascilik           = str_replace("},{", "", $_dMirascilik);
        $_dMirascilik           = str_replace('"', "", $_dMirascilik);
        $_dIdariAlacagi           = str_replace("_", "", $this->JSON_DATA->dIdariAlacagi);
        $_dIdariAlacagi           = str_replace(".", "", $_dIdariAlacagi);
        $_dIdariAlacagi           = str_replace(",", ".", $_dIdariAlacagi);
        $_dVekaletAlacagi           = str_replace("_", "", $this->JSON_DATA->dVekaletAlacagi);
        $_dVekaletAlacagi           = str_replace(".", "", $_dVekaletAlacagi);
        $_dVekaletAlacagi           = str_replace(",", ".", $_dVekaletAlacagi);
        $_dYargilamaGideri           = str_replace("_", "", $this->JSON_DATA->dYargilamaGideri);
        $_dYargilamaGideri           = str_replace(".", "", $_dYargilamaGideri);
        $_dYargilamaGideri           = str_replace(",", ".", $_dYargilamaGideri);
        $_dTapuBilgi                = (int)trim(replaceHtml_Slash($this->JSON_DATA->dTapuBilgi));
        $_dAciklama                = htmlentities($this->JSON_DATA->dAciklama, ENT_QUOTES); //trim(replaceHtml_Slash($this->JSON_DATA->dAciklama));
        $_dTags             = str_replace("[{", "", $this->JSON_DATA->dTags);
        $_dTags             = str_replace("}]", "", $_dTags);
        $_dTags             = str_replace("},{", "", $_dTags);
        $_dTags             = str_replace('"', "", $_dTags);


        $_dSecilenDosyaNo           = str_replace("[{", "", $this->JSON_DATA->dDosyaNo);
        $_dSecilenDosyaNo           = str_replace("}]", "", $_dSecilenDosyaNo);
        $_dSecilenDosyaNo           = str_replace("},{", "", $_dSecilenDosyaNo);
        $_dSecilenDosyaNo           = str_replace('"', "", $_dSecilenDosyaNo);
        $_dSecilenDosyaNo=trim($_dSecilenDosyaNo);
        


        $dDavaci = explode("value:", (string)($_dDavaci ?? ''));
        $dDavali = explode("value:", (string)($_dDavali ?? ''));
        $dDavaKonusu = explode("value:", (string)($_dDavaKonusu ?? ''));
        $dKonuAciklama = explode("value:", (string)($_dKonuAciklama ?? ''));
        $dOnamaIlami = explode("value:", (string)($_dOnamaIlami ?? ''));
        $dBozmaIlami = explode("value:", (string)($_dBozmaIlami ?? ''));
        $dIstinafKabul = explode("value:", (string)($_dIstinafKabul ?? ''));
        $dIstinafRed = explode("value:", (string)($_dIstinafRed ?? ''));
        $dIcra = explode("value:", (string)($_dIcra ?? ''));
        $dKesinlestirme = explode("value:", (string)($_dKesinlestirme ?? ''));
        $dMirascilik = explode("value:", (string)($_dMirascilik ?? ''));
        $dTags = explode("value:", (string)($_dTags ?? ''));

        $dDavaciText = dTagifyToTextToUpper($dDavaci);
        $dDavaliText = dTagifyToTextToUpper($dDavali);
        $dDavaKonusuText = dTagifyToText($dDavaKonusu);
        $dKonuAciklamaText = dTagifyToText($dKonuAciklama);
        $dOnamaIlamiText = dTagifyToText($dOnamaIlami);
        $dBozmaIlamiText = dTagifyToText($dBozmaIlami);
        $dIstinafKabulText = dTagifyToText($dIstinafKabul);
        $dIstinafRedText = dTagifyToText($dIstinafRed);
        $dIcraText = dTagifyToText($dIcra);
        $dKesinlestirmeText = dTagifyToText($dKesinlestirme);
        $dMirascilikText = dTagifyToText($dMirascilik);
        $dTagsText = dTagifyToText($dTags);

        $sql = "SELECT max(d_kurumdosyano) d_kurumdosyano FROM r8t_edys_dosya ORDER BY d_id DESC LIMIT 1";
        $sonNumara = $this->dosya_model->ek_query($sql);
        $maxSonNumara=($sonNumara->d_kurumdosyano + 1);

        if (!empty($_dSecilenDosyaNo)) {
            $dosyaExist=checkDosyaKurumNoVarmi($_dSecilenDosyaNo);
            if (!is_numeric($_dSecilenDosyaNo)) {
                $_sonuc =  new stdClass();
                $_sonuc->success            = false;
                $_sonuc->code                 = 203;
                $_sonuc->description        = "$_dSecilenDosyaNo Girdiğiniz Dosya no geçerli değil. Lütfen bir sayı girin. Yada otomatik atama için boş bırakın.";
                $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
                SetHeader($_sonuc->code);
                echo $sonuc;
                exit;
            }            
            else if ($dosyaExist) {
                $_sonuc =  new stdClass();
                $_sonuc->success            = false;
                $_sonuc->code                 = 203;
                $_sonuc->description        = "Girdiğiniz Dosya no mevcut. Lütfen başka girin. Yada otomatik atama için boş bırakın.";
                $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
                SetHeader($_sonuc->code);
                echo $sonuc;
                exit;
            }
            else if ($_dSecilenDosyaNo>$maxSonNumara) {
                $_sonuc =  new stdClass();
                $_sonuc->success            = false;
                $_sonuc->code                 = 203;
                $_sonuc->description        = "Girdiğiniz Dosya no otomatik numaradan büyük olamaz. Lütfen başka girin. Yada otomatik atama için boş bırakın.";
                $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
                SetHeader($_sonuc->code);
                echo $sonuc;
                exit;
            }

        }
        else {

            $_dSecilenDosyaNo=$maxSonNumara;

        }

        $addData = array(
            "d_arsivno"        => $_dKlasorNo,
            "d_kurumdosyano"     => $_dSecilenDosyaNo,
            "d_davaci"   => $dDavaciText,
            "d_davali"      => $dDavaliText,
            "d_davakonusu"       => $dDavaKonusuText,
            "d_davakonuaciklama"    => $dKonuAciklamaText,
            "d_projebilgisi"   => $_dProjeBilgi,
            "d_mevkiplaka"       => $_dMevkiBilgi,
            "d_istinaftemyiz"     => $_dIstinafBilgi,
            "d_temyiz"    => $_dTemyizBilgi,
            "d_istinafkabul"    => $dIstinafKabulText,
            "d_istinafred"   => $dIstinafRedText,
            "d_onamailami"       => $dOnamaIlamiText,
            "d_bozmailami"     => $dBozmaIlamiText,
            "d_icrakayitno"     => $_dIcraNo,
            "d_icra"     => $dIcraText,
            "d_mirascilik"     => $dMirascilikText,
            "d_kararkesinlestirme"     => $dKesinlestirmeText,
            "d_idarialacagi"     => $_dIdariAlacagi,
            "d_vekaletalacagi"     => $_dVekaletAlacagi,
            "d_yargilamagideri"     => $_dYargilamaGideri,
            "d_tapubilgisi"     => $_dTapuBilgi,
            "d_aciklama"     => $_dAciklama,
            "d_tags"    => $dTagsText,
            "d_status"    => 1,
            "d_adddate"    => dateToTimeFormat(date('Y-m-d H:i:s'), "Y-m-d H:i:s"),
            "d_adduser"    => $this->userData->userB->u_id

        );

        $add = $this->dosya_model->ek_add_lastid(
            "r8t_edys_dosya",
            $addData
        );

        if ($add) {
            $logData = new stdClass();
            $logData->ustid         = "";
            $logData->kullanici     = $this->userData->userB->u_id;
            $logData->aciklama      = $add . " Id numaralı dosya evrakı eklendi.";
            $logData->uygulama      = "hedas";
            $logData->modul         = "dosya";
            $logData->icerikid      = $add;
            $logData->olddata       = array();
            $logData->newdata       = $addData;
            $logyaz = logEkleYeni($logData);

            $_sonuc =  new stdClass();
            $_sonuc->success            = true;
            $_sonuc->code                 = 200;
            $_sonuc->description        = "Tebrikler! Ekleme İşlemi Başarılı.";
            $_sonuc->dosyaid            = $add;
            $_sonuc->kurumdosyano       = $_dSecilenDosyaNo;
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Kayıt İşleminde Hata Oluştu. Lütfen Sistem Yöneticinize Başvurunuz!";
            $_sonuc->dosyaid            = -1;
            $_sonuc->kurumdosyano       = -1;
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }



    ## BEGIN::TAGIFY SEARCH FONKSIYONLARI

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
            $_sonuc->description        = "HEDAS | Dosya Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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
        $items = $this->dosya_model->ek_query_all($sql);
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

    public function api_FormDavaciSearch()
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
            $_sonuc->description        = "HEDAS | Dosya Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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


        $sql = "SELECT d_davaci FROM r8t_edys_dosya WHERE d_davaci LIKE '" . $searchText . "%' GROUP BY d_davaci";
        $items = $this->dosya_model->ek_query_all($sql);
        $itemText = "";
        if ($items) {
            foreach ($items as $item) {
                $kaynakX = explode("@", $item->d_davaci);
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
            $_sonuc->description        = count($items) . " Adet Etiket Bulundu.";
            $_sonuc->data               = $itemText;
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

    public function api_FormDavaliSearch()
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
            $_sonuc->description        = "HEDAS | Dosya Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $searchText         = trim(replaceHtml_Slash($this->JSON_DATA->searchText));


        $sql = "SELECT d_davali FROM r8t_edys_dosya WHERE d_davali LIKE '" . $searchText . "%' GROUP BY d_davali";
        $items = $this->dosya_model->ek_query_all($sql);
        $itemText = "";
        if ($items) {
            foreach ($items as $item) {
                $kaynakX = explode("@", $item->d_davali);
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
            $_sonuc->description        = "Kayıtlı Bulunamadı.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }

    public function api_FormDavaKonuSearch()
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
            $_sonuc->description        = "HEDAS | Dosya Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $searchText         = trim(replaceHtml_Slash($this->JSON_DATA->searchText));

        $sql = "SELECT d_davakonusu FROM r8t_edys_dosya WHERE d_davakonusu LIKE '%" . $searchText . "%' GROUP BY d_davakonusu";
        $items = $this->dosya_model->ek_query_all($sql);
        $itemText = "";
        if ($items) {
            foreach ($items as $item) {
                $kaynakX = explode("@", $item->d_davakonusu);
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

    public function api_FormKonuAciklamaSearch()
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
            $_sonuc->description        = "HEDAS | Dosya Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $searchText         = trim(replaceHtml_Slash($this->JSON_DATA->searchText));

        $sql = "SELECT d_davakonuaciklama FROM r8t_edys_dosya WHERE d_davakonuaciklama LIKE '%" . $searchText . "%' GROUP BY d_davakonuaciklama";
        $items = $this->dosya_model->ek_query_all($sql);
        $itemText = "";
        if ($items) {
            foreach ($items as $item) {
                $kaynakX = explode("@", $item->d_davakonuaciklama);
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
            $_sonuc->description        = count($items) . " Adet Etiket Bulundu.";
            $_sonuc->data               = $itemText;
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

    public function api_FormOnamaIlamiSearch()
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
            $_sonuc->description        = "HEDAS | Dosya Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
        $searchText         = trim(replaceHtml_Slash($this->JSON_DATA->searchText));

        $sql = "SELECT d_onamailami FROM r8t_edys_dosya WHERE d_onamailami LIKE '%" . $searchText . "%' GROUP BY d_onamailami";
        $items = $this->dosya_model->ek_query_all($sql);
        $itemText = "";
        if ($items) {
            foreach ($items as $item) {
                $kaynakX = explode("@", $item->d_onamailami);
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
            $_sonuc->description        = count($items) . " Adet Etiket Bulundu.";
            $_sonuc->data               = $itemText;
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

    public function api_FormBozmaIlamiSearch()
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
            $_sonuc->description        = "HEDAS | Dosya Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
        $searchText         = trim(replaceHtml_Slash($this->JSON_DATA->searchText));

        $sql = "SELECT d_bozmailami FROM r8t_edys_dosya WHERE d_bozmailami LIKE '%" . $searchText . "%' GROUP BY d_bozmailami";
        $items = $this->dosya_model->ek_query_all($sql);
        $itemText = "";
        if ($items) {
            foreach ($items as $item) {
                $kaynakX = explode("@", $item->d_bozmailami);
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
            $_sonuc->description        = count($items) . " Adet Etiket Bulundu.";
            $_sonuc->data               = $itemText;
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

    public function api_FormIstinafKabulSearch()
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
            $_sonuc->description        = "HEDAS | Dosya Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
        $searchText         = trim(replaceHtml_Slash($this->JSON_DATA->searchText));

        $sql = "SELECT d_istinafkabul FROM r8t_edys_dosya WHERE d_istinafkabul LIKE '%" . $searchText . "%' GROUP BY d_istinafkabul";
        $items = $this->dosya_model->ek_query_all($sql);
        $itemText = "";
        if ($items) {
            foreach ($items as $item) {
                $kaynakX = explode("@", $item->d_istinafkabul);
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

    public function api_FormIstinafRedSearch()
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
            $_sonuc->description        = "HEDAS | Dosya Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
        $searchText         = trim(replaceHtml_Slash($this->JSON_DATA->searchText));

        $sql = "SELECT d_istinafred FROM r8t_edys_dosya WHERE d_istinafred LIKE '%" . $searchText . "%' GROUP BY d_istinafred";
        $items = $this->dosya_model->ek_query_all($sql);
        $itemText = "";
        if ($items) {
            foreach ($items as $item) {
                $kaynakX = explode("@", $item->d_istinafred);
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
            $_sonuc->description        = count($items) . " Adet Etiket Bulundu.";
            $_sonuc->data               = $itemText;
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
            $_sonuc->description            = "HEDAS | Dosya Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        $searchText         = trim(replaceHtml_Slash($this->JSON_DATA->searchText));


        $sql = "SELECT mh_name FROM r8t_sys_mahkemeler WHERE mh_name LIKE '%" . $searchText . "%' GROUP BY mh_name";
        $items = $this->dosya_model->ek_query_all($sql);
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

    public function api_FormKesinlestirmeSearch()
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
            $_sonuc->description        = "HEDAS | Dosya Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
        $searchText         = trim(replaceHtml_Slash($this->JSON_DATA->searchText));

        $sql = "SELECT d_kararkesinlestirme FROM r8t_edys_dosya WHERE d_kararkesinlestirme LIKE '%" . $searchText . "%' GROUP BY d_kararkesinlestirme";
        $items = $this->dosya_model->ek_query_all($sql);
        $itemText = "";
        if ($items) {
            foreach ($items as $item) {
                $kaynakX = explode("@", $item->d_kararkesinlestirme);
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
    }

    public function api_FormMirascilikSearch()
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
            $_sonuc->description        = "HEDAS | Dosya Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $_sonuc->data               = array();
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
        $searchText         = trim(replaceHtml_Slash($this->JSON_DATA->searchText));

        $sql = "SELECT d_mirascilik FROM r8t_edys_dosya WHERE d_mirascilik LIKE '%" . $searchText . "%' GROUP BY d_mirascilik";
        $items = $this->dosya_model->ek_query_all($sql);
        $itemText = "";
        if ($items) {
            foreach ($items as $item) {
                $kaynakX = explode("@", $item->d_mirascilik);
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
    }
    
    ## END::TAGIFY SEARCH FONKSIYONLARI




    ## BEGIN::UPDATE FONKSIYONLASI


    public function api_getDosya()
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

        if (!isDbAllowedListModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 203;
            $_sonuc->description            = "HEDAS | Dosya Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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

        $getRecord = $this->dosya_model->ek_get(
            "r8t_edys_dosya",
            array(
                "d_id" => (int)$postData->id
            )
        );

        if ($getRecord) {
            $itemArray = array(
                "_id"           => $getRecord->d_id,
                "_arsivno"      => $getRecord->d_arsivno,
                "_kurumdosyano"      => $getRecord->d_kurumdosyano,
                "_davaci"      => dEditAyracliYazdir($getRecord->d_davaci),
                "_davali"      => dEditAyracliYazdir($getRecord->d_davali),
                "_davakonusu"      => dEditAyracliYazdir($getRecord->d_davakonusu),
                "_davakonuaciklama"      => dEditAyracliYazdir($getRecord->d_davakonuaciklama),
                "_projebilgisi"      => html_entity_decode($getRecord->d_projebilgisi, ENT_QUOTES),
                "_mevkiplaka"      => html_entity_decode($getRecord->d_mevkiplaka, ENT_QUOTES),
                "_istinaftemyiz"      => $getRecord->d_istinaftemyiz,
                "_temyiz"      => $getRecord->d_temyiz,
                "_istinafkabul"      => dEditAyracliYazdir($getRecord->d_istinafkabul),
                "_istinafred"      => dEditAyracliYazdir($getRecord->d_istinafred),
                "_onamailami"      => dEditAyracliYazdir($getRecord->d_onamailami),
                "_bozmailami"      => dEditAyracliYazdir($getRecord->d_bozmailami),
                "_icrakayitno"      => $getRecord->d_icrakayitno,
                "_icra"      => dEditAyracliYazdir($getRecord->d_icra),
                "_mirascilik"      => dEditAyracliYazdir($getRecord->d_mirascilik),
                "_kararkesinlestirme"      => dEditAyracliYazdir($getRecord->d_kararkesinlestirme),
                "_idarialacagi"      => number_format((float)($getRecord->d_idarialacagi ?? 0), 2, '.', ''),
                "_vekaletalacagi"      => number_format((float)($getRecord->d_vekaletalacagi ?? 0), 2, '.', ''),
                "_yargilamagideri"      => number_format((float)($getRecord->d_yargilamagideri ?? 0), 2, '.', ''),
                "_tapubilgisi"      => $getRecord->d_tapubilgisi,
                "_aciklama"      => html_entity_decode($getRecord->d_aciklama, ENT_QUOTES),
                "_tags"      => dEditTagYazdir($getRecord->d_tags)
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

    private function getTableMahkemeColumnName($data = 0)
    {
        $columnName = "dm_mahkeme";
        switch ($data) {
            case 0:
                $columnName = "dm_mahkeme";
                break;
            case 1:
                $columnName = "dm_acilistarihi";
                break;
            case 2:
                $columnName = "dm_esasno";
                break;
            case 3:
                $columnName = "dm_kararno";
                break;
            case 4:
                $columnName = "dm_karartarihi";
                break;
            case 5:
                $columnName = "dm_aciklama";
                break;
            default:
                $columnName = "dm_mahkeme";
                break;
        }
        return $columnName;
    }


    public function api_newmahkemerecord()
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
            $_sonuc->description        = "HEDAS | Dosya Modülünde Ekleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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
        if (isset($this->JSON_DATA->route) == false || isset($this->JSON_DATA->dmMahkeme) == false) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if ($this->JSON_DATA->route != "adddosyamahkeme") {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Yönlendirilen Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $_dmDosyaId         = trim(replaceHtml_Slash($this->JSON_DATA->dmDosyaId));
        $_dmEsasNo          = trim(replaceHtml_Slash($this->JSON_DATA->dmEsasNo));
        $_dmKararNo          = trim(replaceHtml_Slash($this->JSON_DATA->dmKararNo));
        $_dmAciklama          = trim(replaceHtml_Slash($this->JSON_DATA->dmAciklama));
        $_dmAcilisTarihi          = trim(replaceHtml($this->JSON_DATA->dmAcilisTarihi));
        $_dmKararTarihi          = trim(replaceHtml($this->JSON_DATA->dmKararTarihi));
        $_dmMahkeme           = str_replace("[{", "", $this->JSON_DATA->dmMahkeme);
        $_dmMahkeme           = str_replace("}]", "", $_dmMahkeme);
        $_dmMahkeme           = str_replace("},{", "", $_dmMahkeme);
        $_dmMahkeme           = str_replace('"', "", $_dmMahkeme);


        $dmMahkemeArr=explode(",",$_dmMahkeme);
        $dmMahkemeList=array();
        foreach (is_array($dmMahkemeArr) ? $dmMahkemeArr : array() as $km=>$vm) {
            if (empty($vm)) continue;
            $vm=trim($vm);
           
            $mahkemeList[]=FormSelectMahkemeList($vm);

        }
        
        $dmMahkemeText = dTagifyToText($mahkemeList);


        if (strlen($_dmKararTarihi) > 5) {
            $_dmKararTarihi1 = dateToTimeFormat($_dmKararTarihi . " 00:00:00", "d-m-Y H:i:s");
        } else {
            $_dmKararTarihi1 = "";
        }

        $addData = array(
            "dm_dosyaid"        => $_dmDosyaId,
            "dm_acilistarihi"     => dateToTimeFormat($_dmAcilisTarihi . " 00:00:00", "d-m-Y H:i:s"),
            "dm_esasno"   => $_dmEsasNo,
            "dm_karartarihi"      => $_dmKararTarihi1,
            "dm_kararno"       => $_dmKararNo,
            "dm_mahkeme"    => $dmMahkemeText,
            "dm_aciklama"   => $_dmAciklama,
            "dm_adddate"    => dateToTimeFormat(date('Y-m-d H:i:s'), "Y-m-d H:i:s"),
            "dm_adduser"    => $this->userData->userB->u_id

        );

        

        $add = $this->dosya_model->ek_add_lastid(
            "r8t_edys_dosya_mahkemeler",
            $addData
        );

        if ($add) {
            $logData = new stdClass();
            $logData->ustid         = "";
            $logData->kullanici     = $this->userData->userB->u_id;
            $logData->aciklama      = $_dmDosyaId . "Id Numaralı Dosyaya Evrakına " . $add . " Id numaralı mahkeme eklendi.";
            $logData->uygulama      = "hedas";
            $logData->modul         = "dosya";
            $logData->icerikid      = $add;
            $logData->olddata       = array(" ");
            $logData->newdata       = $addData;
            $logyaz = logEkleYeni($logData);

            $_sonuc =  new stdClass();
            $_sonuc->success            = true;
            $_sonuc->code                 = 200;
            $_sonuc->description        = "Tebrikler! Mahkeme Ekleme İşlemi Başarılı.";
            $_sonuc->mahkemeid            = $add;
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Kayıt İşleminde Hata Oluştu. Lütfen Sistem Yöneticinize Başvurunuz!";
            $_sonuc->mahkemeid            = -1;
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }

    public function api_mahkemelist()
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
            $_sonuc->description            = "HEDAS | Dosya Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $_post          = json_encode($this->input->post());
        $postData       = json_decode($_post);
        $_searchValue   = json_decode($postData->search->value);
        $_orderColumn   = $this->getTableMahkemeColumnName($postData->order[0]->column);
        $_orderType     = $postData->order[0]->dir;
        $_kactan        = $postData->start;
        $_kacar         = $postData->length;
        $_kactan        = max(0, (int) $_kactan);
        $_kacar         = min(500, max(1, (int) $_kacar));

        //GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
        if (isset($_searchValue->dmDosyaId) === false || is_numeric($_searchValue->dmDosyaId) === false) {
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

        $sql = "";
        $sqlEk = " dm_dosyaid = " . trim(replaceHtml($_searchValue->dmDosyaId));

        $totalSql = "SELECT COUNT(dm_id) AS total FROM r8t_edys_dosya_mahkemeler WHERE" . $sqlEk;
        $totalRecordS = $this->dosya_model->ek_query_all($totalSql);
        $totalRecord = (int)$totalRecordS[0]->total;

        //die($totalSql);

        $sql = "SELECT dm_id, dm_mahkeme, dm_acilistarihi, dm_esasno, dm_kararno, dm_karartarihi, dm_aciklama FROM r8t_edys_dosya_mahkemeler WHERE" . $sqlEk;
        $sql .= " ORDER BY " . $_orderColumn . " " . strtoupper($_orderType);
        $sql .= " LIMIT " . $_kactan . ", " . $_kacar;

        $filterRecord = $this->dosya_model->ek_query_all($sql);
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
        $discardSayac = 0;
        foreach ($filterRecord as $item) {
            $itemArray = array(
                "_id"           => $item->dm_id,
                "_mahkeme"       => dAyracsizYazdir($item->dm_mahkeme),
                "_mahkemeEdit"  => dEditAyracliYazdir($item->dm_mahkeme),
                "_mahkemeEditId"  => convertMahkemeIds($item->dm_mahkeme),
                "_acilistarihi"   => timeToDateFormat($item->dm_acilistarihi, "d-m-Y"),
                "_esasno"     => ($item->dm_esasno),
                "_kararno" => ($item->dm_kararno),
                "_karartarihi"   => timeToDateFormat($item->dm_karartarihi, "d-m-Y"),
                "_aciklama"     => ($item->dm_aciklama),
            );
            if (in_array($itemArray, $itemsData)) {
                $discardSayac++;
            } else {
                array_push($itemsData, $itemArray);
            }
        }


        if (count($itemsData) > 0) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = $totalRecord;
            $_sonuc->data                   = $itemsData;
            $_sonuc->success                = true;
            $_sonuc->code                   = 200;
            $_sonuc->description            = ($totalRecord) . " Adet Kayıt Bulundu.";
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


    public function api_updaterecord()
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
            $_sonuc->description        = "HEDAS | Dosya Modülünde Güncelleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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
        if (isset($this->JSON_DATA->route) == false || isset($this->JSON_DATA->dDosyaId) == false) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if ($this->JSON_DATA->route != "updatedavadosya") {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Yönlendirilen Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $_dDosyaId          = (int)trim(replaceHtml_Slash($this->JSON_DATA->dDosyaId));
        $_dKlasorNo         = trim(replaceHtml_Slash($this->JSON_DATA->dKlasorNo));
        $_dDavaci           = str_replace("[{", "", $this->JSON_DATA->dDavaci);
        $_dDavaci           = str_replace("}]", "", $_dDavaci);
        $_dDavaci           = str_replace("},{", "", $_dDavaci);
        $_dDavaci           = str_replace('"', "", $_dDavaci);
        $_dDavali           = str_replace("[{", "", $this->JSON_DATA->dDavali);
        $_dDavali           = str_replace("}]", "", $_dDavali);
        $_dDavali           = str_replace("},{", "", $_dDavali);
        $_dDavali           = str_replace('"', "", $_dDavali);
        $_dDavaKonusu           = str_replace("[{", "", $this->JSON_DATA->dDavaKonusu);
        $_dDavaKonusu           = str_replace("}]", "", $_dDavaKonusu);
        $_dDavaKonusu           = str_replace("},{", "", $_dDavaKonusu);
        $_dDavaKonusu           = str_replace('"', "", $_dDavaKonusu);
        $_dKonuAciklama           = str_replace("[{", "", $this->JSON_DATA->dKonuAciklamasi);
        $_dKonuAciklama           = str_replace("}]", "", $_dKonuAciklama);
        $_dKonuAciklama           = str_replace("},{", "", $_dKonuAciklama);
        $_dKonuAciklama           = str_replace('"', "", $_dKonuAciklama);
        $_dProjeBilgi         = htmlentities($this->JSON_DATA->dProjeBilgi, ENT_QUOTES); //trim(replaceHtml_Slash($this->JSON_DATA->dProjeBilgi));
        $_dMevkiBilgi         = htmlentities($this->JSON_DATA->dMevkiBilgi, ENT_QUOTES); //trim(replaceHtml_Slash($this->JSON_DATA->dMevkiBilgi));
        $_dIstinafBilgi         = trim(replaceHtml_Slash($this->JSON_DATA->dIstinafBilgi));
        $_dTemyizBilgi         = trim(replaceHtml_Slash($this->JSON_DATA->dTemyizBilgi));
        $_dOnamaIlami           = str_replace("[{", "", $this->JSON_DATA->dOnamaIlami);
        $_dOnamaIlami           = str_replace("}]", "", $_dOnamaIlami);
        $_dOnamaIlami           = str_replace("},{", "", $_dOnamaIlami);
        $_dOnamaIlami           = str_replace('"', "", $_dOnamaIlami);
        $_dBozmaIlami           = str_replace("[{", "", $this->JSON_DATA->dBozmaIlami);
        $_dBozmaIlami           = str_replace("}]", "", $_dBozmaIlami);
        $_dBozmaIlami           = str_replace("},{", "", $_dBozmaIlami);
        $_dBozmaIlami           = str_replace('"', "", $_dBozmaIlami);
        $_dIstinafKabul           = str_replace("[{", "", $this->JSON_DATA->dIstinafKabul);
        $_dIstinafKabul           = str_replace("}]", "", $_dIstinafKabul);
        $_dIstinafKabul           = str_replace("},{", "", $_dIstinafKabul);
        $_dIstinafKabul           = str_replace('"', "", $_dIstinafKabul);
        $_dIstinafRed           = str_replace("[{", "", $this->JSON_DATA->dIstinafRed);
        $_dIstinafRed           = str_replace("}]", "", $_dIstinafRed);
        $_dIstinafRed           = str_replace("},{", "", $_dIstinafRed);
        $_dIstinafRed           = str_replace('"', "", $_dIstinafRed);
        $_dIcraNo          = trim(replaceHtml_Slash($this->JSON_DATA->dIcraNo));
        $_dIcra           = str_replace("[{", "", $this->JSON_DATA->dIcra);
        $_dIcra           = str_replace("}]", "", $_dIcra);
        $_dIcra           = str_replace("},{", "", $_dIcra);
        $_dIcra           = str_replace('"', "", $_dIcra);
        $_dKesinlestirme           = str_replace("[{", "", $this->JSON_DATA->dKesinlestirme);
        $_dKesinlestirme           = str_replace("}]", "", $_dKesinlestirme);
        $_dKesinlestirme           = str_replace("},{", "", $_dKesinlestirme);
        $_dKesinlestirme           = str_replace('"', "", $_dKesinlestirme);
        $_dMirascilik           = str_replace("[{", "", $this->JSON_DATA->dMirascilik);
        $_dMirascilik           = str_replace("}]", "", $_dMirascilik);
        $_dMirascilik           = str_replace("},{", "", $_dMirascilik);
        $_dMirascilik           = str_replace('"', "", $_dMirascilik);
        $_dIdariAlacagi           = str_replace("_", "", $this->JSON_DATA->dIdariAlacagi);
        $_dIdariAlacagi           = str_replace(".", "", $_dIdariAlacagi);
        $_dIdariAlacagi           = str_replace(",", ".", $_dIdariAlacagi);
        $_dVekaletAlacagi           = str_replace("_", "", $this->JSON_DATA->dVekaletAlacagi);
        $_dVekaletAlacagi           = str_replace(".", "", $_dVekaletAlacagi);
        $_dVekaletAlacagi           = str_replace(",", ".", $_dVekaletAlacagi);
        $_dYargilamaGideri           = str_replace("_", "", $this->JSON_DATA->dYargilamaGideri);
        $_dYargilamaGideri           = str_replace(".", "", $_dYargilamaGideri);
        $_dYargilamaGideri           = str_replace(",", ".", $_dYargilamaGideri);
        $_dTapuBilgi                = (int)trim(replaceHtml_Slash($this->JSON_DATA->dTapuBilgi));
        $_dAciklama                = htmlentities($this->JSON_DATA->dAciklama, ENT_QUOTES); //trim(replaceHtml_Slash($this->JSON_DATA->dAciklama));
        $_dTags             = str_replace("[{", "", $this->JSON_DATA->dTags);
        $_dTags             = str_replace("}]", "", $_dTags);
        $_dTags             = str_replace("},{", "", $_dTags);
        $_dTags             = str_replace('"', "", $_dTags);

        $_dSecilenDosyaNo           = str_replace("[{", "", $this->JSON_DATA->dDosyaNo);
        $_dSecilenDosyaNo           = str_replace("}]", "", $_dSecilenDosyaNo);
        $_dSecilenDosyaNo           = str_replace("},{", "", $_dSecilenDosyaNo);
        $_dSecilenDosyaNo           = str_replace('"', "", $_dSecilenDosyaNo);
        $_dSecilenDosyaNo=trim($_dSecilenDosyaNo);
        

        $dDavaci = explode("value:", (string)($_dDavaci ?? ''));
        $dDavali = explode("value:", (string)($_dDavali ?? ''));
        $dDavaKonusu = explode("value:", (string)($_dDavaKonusu ?? ''));
        $dKonuAciklama = explode("value:", (string)($_dKonuAciklama ?? ''));
        $dOnamaIlami = explode("value:", (string)($_dOnamaIlami ?? ''));
        $dBozmaIlami = explode("value:", (string)($_dBozmaIlami ?? ''));
        $dIstinafKabul = explode("value:", (string)($_dIstinafKabul ?? ''));
        $dIstinafRed = explode("value:", (string)($_dIstinafRed ?? ''));
        $dIcra = explode("value:", (string)($_dIcra ?? ''));
        $dKesinlestirme = explode("value:", (string)($_dKesinlestirme ?? ''));
        $dMirascilik = explode("value:", (string)($_dMirascilik ?? ''));
        $dTags = explode("value:", (string)($_dTags ?? ''));

        $dDavaciText = dTagifyToTextToUpper($dDavaci);
        $dDavaliText = dTagifyToTextToUpper($dDavali);
        $dDavaKonusuText = dTagifyToText($dDavaKonusu);
        $dKonuAciklamaText = dTagifyToText($dKonuAciklama);
        $dOnamaIlamiText = dTagifyToText($dOnamaIlami);
        $dBozmaIlamiText = dTagifyToText($dBozmaIlami);
        $dIstinafKabulText = dTagifyToText($dIstinafKabul);
        $dIstinafRedText = dTagifyToText($dIstinafRed);
        $dIcraText = dTagifyToText($dIcra);
        $dKesinlestirmeText = dTagifyToText($dKesinlestirme);
        $dMirascilikText = dTagifyToText($dMirascilik);
        $dTagsText = dTagifyToText($dTags);

        $oldData = $this->dosya_model->ek_get(
            "r8t_edys_dosya",
            array(
                "d_id"      => $_dDosyaId
            )
        );

        $oldDosyaNo=(!empty($oldData->d_kurumdosyano))?$oldData->d_kurumdosyano:0;
        
 


        $sql = "SELECT max(d_kurumdosyano) d_kurumdosyano FROM r8t_edys_dosya ORDER BY d_id DESC LIMIT 1";
        $sonNumara = $this->dosya_model->ek_query($sql);
        $maxSonNumara=($sonNumara->d_kurumdosyano + 1);

        if ($_dSecilenDosyaNo and $oldDosyaNo!=$_dSecilenDosyaNo) {

            $dosyaExist=checkDosyaKurumNoVarmi($_dSecilenDosyaNo);
            if (!is_numeric($_dSecilenDosyaNo)) {
                $_sonuc =  new stdClass();
                $_sonuc->success            = false;
                $_sonuc->code                 = 203;
                $_sonuc->description        = "$_dSecilenDosyaNo Girdiğiniz Dosya no geçerli değil. Lütfen bir sayı girin. Yada otomatik atama için boş bırakın.";
                $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
                SetHeader($_sonuc->code);
                echo $sonuc;
                exit;
            }            
            else if ($dosyaExist) {
                $_sonuc =  new stdClass();
                $_sonuc->success            = false;
                $_sonuc->code                 = 203;
                $_sonuc->description        = "Girdiğiniz Dosya no mevcut. Lütfen başka girin. Yada otomatik atama için boş bırakın.";
                $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
                SetHeader($_sonuc->code);
                echo $sonuc;
                exit;
            }
            else if ($_dSecilenDosyaNo>$maxSonNumara) {
                $_sonuc =  new stdClass();
                $_sonuc->success            = false;
                $_sonuc->code                 = 203;
                $_sonuc->description        = "Girdiğiniz Dosya no otomatik numaradan büyük olamaz. Lütfen başka girin. Yada otomatik atama için boş bırakın.";
                $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
                SetHeader($_sonuc->code);
                echo $sonuc;
                exit;
            }


        }
        else {
            $_dSecilenDosyaNo=$oldDosyaNo;
        }
     


        $updateData = array(
            "d_arsivno"        => $_dKlasorNo,
            
            "d_davaci"   => $dDavaciText,
            "d_davali"      => $dDavaliText,
            "d_davakonusu"       => $dDavaKonusuText,
            "d_davakonuaciklama"    => $dKonuAciklamaText,
            "d_projebilgisi"   => $_dProjeBilgi,
            "d_mevkiplaka"       => $_dMevkiBilgi,
            "d_istinaftemyiz"     => $_dIstinafBilgi,
            "d_temyiz"    => $_dTemyizBilgi,
            "d_istinafkabul"    => $dIstinafKabulText,
            "d_istinafred"   => $dIstinafRedText,
            "d_onamailami"       => $dOnamaIlamiText,
            "d_bozmailami"     => $dBozmaIlamiText,
            "d_icrakayitno"     => $_dIcraNo,
            "d_icra"     => $dIcraText,
            "d_mirascilik"     => $dMirascilikText,
            "d_kararkesinlestirme"     => $dKesinlestirmeText,
            "d_idarialacagi"     => $_dIdariAlacagi,
            "d_vekaletalacagi"     => $_dVekaletAlacagi,
            "d_yargilamagideri"     => $_dYargilamaGideri,
            "d_tapubilgisi"     => $_dTapuBilgi,
            "d_aciklama"     => $_dAciklama,
            "d_tags"    => $dTagsText,
            "d_editdate"    => dateToTimeFormat(date('Y-m-d H:i:s'), "Y-m-d H:i:s"),
            "d_edituser"    => $this->userData->userB->u_id

        );

        if ($_dSecilenDosyaNo>0) {
            $updateData["d_kurumdosyano"]=$_dSecilenDosyaNo;
        }


        $update = $this->dosya_model->ek_update(
            "r8t_edys_dosya",
            array(
                "d_id"  => $_dDosyaId
            ),
            $updateData
        );

        if ($update !== false) {
            $logData = new stdClass();
            $logData->ustid         = "";
            $logData->kullanici     = $this->userData->userB->u_id;
            $logData->aciklama      = $_dDosyaId . " Id Numaralı Dosya Evrakı Güncellendi.";
            $logData->uygulama      = "hedas";
            $logData->modul         = "dosya";
            $logData->icerikid      = $_dDosyaId;
            $logData->olddata       = $oldData;
            $logData->newdata       = $updateData;
            $logyaz = logEkleGuncelleme($logData);

            $_sonuc =  new stdClass();
            $_sonuc->success            = true;
            $_sonuc->code                 = 200;
            $_sonuc->description        = "Tebrikler! Güncelleme İşlemi Başarılı.";
            $_sonuc->dosyaid            = $_dDosyaId;
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Güncelleme İşleminde Hata Oluştu. Lütfen Sistem Yöneticinize Başvurunuz!";
            $_sonuc->dosyaid            = -1;
            $_sonuc->kurumdosyano       = -1;
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }


    public function api_updatemahkemerecord()
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
            $_sonuc->description        = "HEDAS | Dosya Modülünde Güncelleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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
        if (isset($this->JSON_DATA->route) == false || isset($this->JSON_DATA->dmMahkemeId) == false || isset($this->JSON_DATA->dmDosyaId) == false) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if ($this->JSON_DATA->route != "updatedosyamahkeme") {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Yönlendirilen Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $_dmMahkemeId         = (int)trim(replaceHtml_Slash($this->JSON_DATA->dmMahkemeId));
        $_dmDosyaId         = (int)trim(replaceHtml_Slash($this->JSON_DATA->dmDosyaId));
        $_dmEsasNo          = trim(replaceHtml_Slash($this->JSON_DATA->dmEsasNo));
        $_dmKararNo          = trim(replaceHtml_Slash($this->JSON_DATA->dmKararNo));
        $_dmAciklama          = trim(replaceHtml_Slash($this->JSON_DATA->dmAciklama));
        $_dmAcilisTarihi          = trim(replaceHtml($this->JSON_DATA->dmAcilisTarihi));
        $_dmKararTarihi          = trim(replaceHtml($this->JSON_DATA->dmKararTarihi));
        $_dmMahkeme           = str_replace("[{", "", $this->JSON_DATA->dmMahkeme);
        $_dmMahkeme           = str_replace("}]", "", $_dmMahkeme);
        $_dmMahkeme           = str_replace("},{", "", $_dmMahkeme);
        $_dmMahkeme           = str_replace('"', "", $_dmMahkeme);


        
        $dmMahkemeArr=explode(",",$_dmMahkeme);
        $dmMahkemeList=array();
        foreach (is_array($dmMahkemeArr) ? $dmMahkemeArr : array() as $km=>$vm) {
            if (empty($vm)) continue;
            $vm=trim($vm);
           
            $mahkemeList[]=FormSelectMahkemeList($vm);

        }
        
        $dmMahkemeText = dTagifyToText($mahkemeList);

        if (strlen($_dmKararTarihi) > 5) {
            $_dmKararTarihi1 = dateToTimeFormat($_dmKararTarihi . " 00:00:00", "d-m-Y H:i:s");
        } else {
            $_dmKararTarihi1 = "";
        }


        $oldData = $this->dosya_model->ek_get(
            "r8t_edys_dosya_mahkemeler",
            array(
                "dm_id"      => $_dmMahkemeId
            )
        );

        $updateData = array(
            "dm_acilistarihi"     => dateToTimeFormat($_dmAcilisTarihi . " 00:00:00", "d-m-Y H:i:s"),
            "dm_esasno"   => $_dmEsasNo,
            "dm_karartarihi"      => $_dmKararTarihi1,
            "dm_kararno"       => $_dmKararNo,
            "dm_mahkeme"    => $dmMahkemeText,
            "dm_aciklama"   => $_dmAciklama,
            "dm_editdate"    => dateToTimeFormat(date('Y-m-d H:i:s'), "Y-m-d H:i:s"),
            "dm_edituser"    => $this->userData->userB->u_id

        );




        $update = $this->dosya_model->ek_update(
            "r8t_edys_dosya_mahkemeler",
            array(
                "dm_id" => $_dmMahkemeId,
                "dm_dosyaid"    => $_dmDosyaId
            ),
            $updateData
        );

        if ($update !== false) {
            $logData = new stdClass();
            $logData->ustid         = "";
            $logData->kullanici     = $this->userData->userB->u_id;
            $logData->aciklama      = $_dmDosyaId . " Id Numaralı Dosya Evrakında " . $_dmMahkemeId . " Id Numaralı Mahkeme Bilgisi Güncellendi.";
            $logData->uygulama      = "hedas";
            $logData->modul         = "dosya";
            $logData->icerikid      = $_dmMahkemeId;
            $logData->olddata       = $oldData;
            $logData->newdata       = $updateData;
            $logyaz = logEkleGuncelleme($logData);

            $_sonuc =  new stdClass();
            $_sonuc->success            = true;
            $_sonuc->code                 = 200;
            $_sonuc->description        = "Tebrikler! Güncelleme İşlemi Başarılı.";
            $_sonuc->mahkemeid            = $_dmMahkemeId;
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Güncelleme İşleminde Hata Oluştu. Lütfen Sistem Yöneticinize Başvurunuz!";
            $_sonuc->mahkemeid            = -1;
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
    }


    public function api_deletemahkemerecord()
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

        if (!isDbAllowedDeleteModule()) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "HEDAS | Dosya Modülünde Silme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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
        if (isset($this->JSON_DATA->route) == false || isset($this->JSON_DATA->dmMahkemeId) == false || isset($this->JSON_DATA->dmDosyaId) == false) {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        if ($this->JSON_DATA->route != "deletedosyamahkeme") {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Yönlendirilen Sorgu Parametreleri Eksik. Lutfen Api Bilgilerini Dogru Giriniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $_dmMahkemeId         = (int)trim(replaceHtml_Slash($this->JSON_DATA->dmMahkemeId));
        $_dmDosyaId         = (int)trim(replaceHtml_Slash($this->JSON_DATA->dmDosyaId));


        $oldData = $this->dosya_model->ek_get(
            "r8t_edys_dosya_mahkemeler",
            array(
                "dm_id"      => $_dmMahkemeId
            )
        );


        $delete = $this->dosya_model->ek_delete(
            "r8t_edys_dosya_mahkemeler",
            array(
                "dm_id" => $_dmMahkemeId,
                "dm_dosyaid"    => $_dmDosyaId
            )
        );

        if ($delete) {
            $logData = new stdClass();
            $logData->ustid         = "";
            $logData->kullanici     = $this->userData->userB->u_id;
            $logData->aciklama      = $_dmDosyaId . " Id Numaralı Dosya Evrakında " . $_dmMahkemeId . " Id Numaralı Mahkeme Bilgisi Silindi.";
            $logData->uygulama      = "hedas";
            $logData->modul         = "dosya";
            $logData->icerikid      = $_dmMahkemeId;
            $logData->olddata       = $oldData;
            $logData->newdata       = array();
            $logyaz = logEkleSilme($logData);

            $_sonuc =  new stdClass();
            $_sonuc->success            = true;
            $_sonuc->code                 = 200;
            $_sonuc->description        = "Tebrikler! Mahkeme bilgisi başarıyla silindi.";
            $_sonuc->mahkemeid            = $_dmMahkemeId;
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Silme İşleminde Hata Oluştu. Lütfen Sistem Yöneticinize Başvurunuz!";
            $_sonuc->mahkemeid            = -1;
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
            $_sonuc->description            = "HEDAS | Dosya Modülünde Güncelleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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

        $getUpdate = $this->dosya_model->update(
            array(
                "d_id" => (int)$postData->id,
                "d_status"  => 1
            ),
            array(
                "d_status" => 0
            )
        );


        if ($getUpdate) {
            $logData = new stdClass();
            $logData->ustid         = "";
            $logData->kullanici     = $this->userData->userB->u_id;
            $logData->aciklama      = (int)$postData->id . " Id Numaralı Dosya Evrakı Çöp Kutusuna Taşındı.";
            $logData->uygulama      = "hedas";
            $logData->modul         = "dosya";
            $logData->icerikid      = (int)$postData->id;
            $logData->olddata       = array("status" => 1);
            $logData->newdata       = array("status" => 0);
            $logyaz = logEkleTasima($logData);

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

    public function api_saveasdata()
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
            $_sonuc->description            = "HEDAS | Dosya Modülünde Güncelleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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

        $_dDosyaId = (int)trim(replaceHtml_Slash($this->JSON_DATA->id));

        $oldData = $this->dosya_model->ek_get(
            "r8t_edys_dosya",
            array(
                "d_id"      => $_dDosyaId
            )
        );

        $oldData0=(array)$oldData;

               


  
        $sql = "SELECT max(d_kurumdosyano) d_kurumdosyano FROM r8t_edys_dosya ORDER BY d_id DESC LIMIT 1";
        $sonNumara = $this->dosya_model->ek_query($sql);
        $addData = array(
            "d_arsivno"        => $oldData0["d_arsivno"],
            "d_kurumdosyano"     => ($sonNumara->d_kurumdosyano + 1),
            "d_davaci"   => $oldData0["d_davaci"],
            "d_davali"      => $oldData0["d_davali"],
            "d_davakonusu"       => $oldData0["d_davakonusu"],
            "d_davakonuaciklama"    => $oldData0["d_davakonuaciklama"],
            "d_projebilgisi"   => $oldData0["d_projebilgisi"],
            "d_mevkiplaka"       => $oldData0["d_mevkiplaka"],
            "d_istinaftemyiz"     => $oldData0["d_arsivno"],
            "d_temyiz"    => $oldData0["d_temyiz"],
            "d_istinafkabul"    => $oldData0["d_istinafkabul"],
            "d_istinafred"   => $oldData0["d_istinafred"],
            "d_onamailami"       => $oldData0["d_onamailami"],
            "d_bozmailami"     => $oldData0["d_bozmailami"],
            "d_icrakayitno"     => $oldData0["d_icrakayitno"],
            "d_icra"     => $oldData0["d_icra"],
            "d_mirascilik"     => $oldData0["d_mirascilik"],
            "d_kararkesinlestirme"     => $oldData0["d_kararkesinlestirme"],
            "d_idarialacagi"     => $oldData0["d_idarialacagi"],
            "d_vekaletalacagi"     => $oldData0["d_vekaletalacagi"],
            "d_yargilamagideri"     => $oldData0["d_yargilamagideri"],
            "d_tapubilgisi"     => $oldData0["d_tapubilgisi"],
            "d_aciklama"     => $oldData0["d_aciklama"],
            "d_tags"    => $oldData0["d_tags"],
            "d_status"    => 1,
            "d_adddate"    => dateToTimeFormat(date('Y-m-d H:i:s'), "Y-m-d H:i:s"),
            "d_adduser"    => $this->userData->userB->u_id

        );
  
        $add = $this->dosya_model->ek_add_lastid(
            "r8t_edys_dosya",
            $addData
        );

        if ($add) {

            $mahkemeler = $this->dosya_model->ek_get_all(
                "r8t_edys_dosya_mahkemeler",
                array(
                    "dm_dosyaid"    => $_dDosyaId
                )
            );
       
            foreach (is_array($mahkemeler) ? $mahkemeler : array() as $kx=>$vx) {
                $vx=(array)$vx;

                $addData2 = array(
                    "dm_dosyaid"        => $add,
                    "dm_acilistarihi"     => $vx["dm_acilistarihi"],
                    "dm_esasno"   => $vx["dm_esasno"],
                    "dm_karartarihi"      => $vx["dm_karartarihi"],
                    "dm_kararno"       => $vx["dm_kararno"],
                    "dm_mahkeme"    => $vx["dm_mahkeme"],
                    "dm_aciklama"   => $vx["dm_aciklama"],
                    "dm_adddate"    => dateToTimeFormat(date('Y-m-d H:i:s'), "Y-m-d H:i:s"),
                    "dm_adduser"    => $this->userData->userB->u_id
        
                );
        
            
                $add2 = $this->dosya_model->ek_add_lastid(
                    "r8t_edys_dosya_mahkemeler",
                    $addData2
                );            
            
            }



            
            $logData = new stdClass();
            $logData->ustid         = "";
            $logData->kullanici     = $this->userData->userB->u_id;
            $logData->aciklama      = $add . " Id numaralı dosya evrakından farklı kaydedildi.";
            $logData->uygulama      = "hedas";
            $logData->modul         = "dosya";
            $logData->icerikid      = $add;
            $logData->olddata       = array();
            $logData->newdata       = $addData;
            $logyaz = logEkleYeni($logData);

            $_sonuc =  new stdClass();
            $_sonuc->success            = true;
            $_sonuc->code                 = 200;
            $_sonuc->description        = "Tebrikler! Farklı kaydet İşlemi Başarılı.";
            $_sonuc->dosyaid            = $add;
            $_sonuc->kurumdosyano       = ($sonNumara->d_kurumdosyano + 1);
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->success            = false;
            $_sonuc->code                 = 203;
            $_sonuc->description        = "Kayıt İşleminde Hata Oluştu. Lütfen Sistem Yöneticinize Başvurunuz!";
            $_sonuc->dosyaid            = -1;
            $_sonuc->kurumdosyano       = -1;
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
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
                "text"  => "HEDAS | Dosya Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.",
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
            $_sonuc->description            = "HEDAS | Dosya Modülünde Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $_post          = json_encode($this->input->post());
        $postData       = json_decode($_post);

        if (!is_object($postData) || !isset($postData->search->value) || !isset($postData->order[0]->column) || !isset($postData->order[0]->dir) || !isset($postData->start) || !isset($postData->length)) {
            $_sonuc =  new stdClass();
            $_sonuc->draw                     = (int) $this->input->post('draw');
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 400;
            $_sonuc->description            = "Sorgu parametreleri eksik veya geçersiz (api_ejectlist).";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $_searchValue   = json_decode($postData->search->value);
        $_orderColumn   = $this->getTableColumnName($postData->order[0]->column);
        $_orderType     = $postData->order[0]->dir;
        $_kactan        = (int) $postData->start;
        $_kacar         = (int) $postData->length;
        $_kactan        = max(0, $_kactan);
        $_kacar         = min(500, max(1, $_kacar));

        //GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
        if (!is_object($_searchValue) || isset($_searchValue->dAralik) === false || in_array($_searchValue->dTapuBilgisi ?? -999, array(-1, 0, 1, 2)) === false) {
            $_sonuc =  new stdClass();
            $_sonuc->draw                     = isset($postData->draw) ? (int) $postData->draw : 0;
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Sorgu Parametreleri Eksik. Lütfen Sistem Yöneticinize Başvurunuz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            if (ob_get_level()) { ob_end_clean(); }
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        $tarihData = explode(" & ", (string)($_searchValue->dAralik ?? ''));
        if (validDate($tarihData[0], "d-m-Y") == false || validDate($tarihData[1], "d-m-Y") == false) {
            $_sonuc =  new stdClass();
            $_sonuc->draw                     = isset($postData->draw) ? (int) $postData->draw : 0;
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Sorgu Parametreleri Eksik. Lütfen Sistem Yöneticinize Başvurunuz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            if (ob_get_level()) { ob_end_clean(); }
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }
        $_tarihStart    = dateToTimeFormat($tarihData[0] . " 00:00:00", "d-m-Y H:i:s");
        $_tarihStop     = dateToTimeFormat($tarihData[1] . " 23:59:59", "d-m-Y H:i:s");


        $sql = "";
        $sqlEk = " dos.d_status=0 AND (dos.d_adddate >= " . trim(replaceHtml($_tarihStart)) . " AND dos.d_adddate <= " . trim(replaceHtml($_tarihStop)) . ")";

        $_dTextEject = trim(replaceHtml($_searchValue->dText ?? ''));
        if (strlen($_dTextEject) > 0) {
            $sqlEk .= " AND (dos.d_arsivno LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dos.d_id LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dos.d_kurumdosyano LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dos.d_davaci LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dos.d_davali LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dos.d_davakonusu LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dos.d_davakonuaciklama LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dos.d_projebilgisi LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dos.d_mevkiplaka LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dos.d_istinaftemyiz LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dos.d_temyiz LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dos.d_istinafkabul LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dos.d_istinafred LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dos.d_onamailami LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dos.d_bozmailami LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dos.d_icrakayitno LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dos.d_icra LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dos.d_mirascilik LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dos.d_kararkesinlestirme LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dos.d_idarialacagi LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dos.d_vekaletalacagi LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dos.d_yargilamagideri LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dos.d_aciklama LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dos.d_tags LIKE '%" . $_dTextEject . "%'";

            $sqlEk .= " OR dm.dm_dosyaid LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dm.dm_esasno LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dm.dm_kararno LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dm.dm_mahkeme LIKE '%" . $_dTextEject . "%'";
            $sqlEk .= " OR dm.dm_aciklama LIKE '%" . $_dTextEject . "%')";
        }
        $_dTapuEject = (int) trim(replaceHtml($_searchValue->dTapuBilgisi ?? 2));
        if (in_array($_dTapuEject, array(0, 1)) === true) {
            $sqlEk .= " AND dos.d_tapubilgisi LIKE '%" . $_dTapuEject . "%'";
        }

        $useJoinEject = (strlen($_dTextEject) > 0);
        $joinClauseEject = $useJoinEject ? " LEFT JOIN r8t_edys_dosya_mahkemeler AS dm ON dm.dm_dosyaid=dos.d_id" : "";
        $groupClauseEject = $useJoinEject ? " GROUP BY dos.d_id" : "";
        $countExprEject = $useJoinEject ? "COUNT(DISTINCT dos.d_id)" : "COUNT(*)";

        $totalSql = "SELECT " . $countExprEject . " AS total FROM r8t_edys_dosya AS dos" . $joinClauseEject . " WHERE" . $sqlEk;
        $totalRecordS = $this->dosya_model->ek_query_all($totalSql);
        $totalRecord = (int)$totalRecordS[0]->total;

        $selectCols = "dos.d_id, dos.d_arsivno, dos.d_icrakayitno, dos.d_kurumdosyano, dos.d_davaci, dos.d_davali, dos.d_davakonusu, dos.d_davakonuaciklama, dos.d_mevkiplaka, dos.d_projebilgisi, dos.d_icra, dos.d_temyiz, dos.d_istinaftemyiz, dos.d_istinafkabul, dos.d_istinafred, dos.d_bozmailami, dos.d_onamailami, dos.d_kararkesinlestirme, dos.d_mirascilik, dos.d_idarialacagi, dos.d_vekaletalacagi, dos.d_yargilamagideri, dos.d_tapubilgisi, dos.d_tags, dos.d_aciklama";
        $sql = "SELECT " . $selectCols . " FROM r8t_edys_dosya AS dos" . $joinClauseEject . " WHERE" . $sqlEk;
        $sql .= $groupClauseEject;
        $sql .= " ORDER BY " . $_orderColumn . " " . strtoupper($_orderType);
        $sql .= " LIMIT " . $_kactan . ", " . $_kacar;

        $filterRecord = $this->dosya_model->ek_query_all($sql);
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
        $discardSayac = 0;
        foreach ($filterRecord as $item) {

            $mahkemeler = $this->dosya_model->ek_get_all(
                "r8t_edys_dosya_mahkemeler",
                array(
                    "dm_dosyaid"    => $item->d_id
                )
            );
            $mahkemeData = array();
            if ($mahkemeler) {
                foreach ($mahkemeler as $mahkeme) {
                    $mahkemeArray = array(
                        "_id"   => $mahkeme->dm_id,
                        "_acilistarihi" => timeToDateFormat($mahkeme->dm_acilistarihi, "d.m.Y"),
                        "_esasno"       => $mahkeme->dm_esasno,
                        "_karartarihi"  => timeToDateFormat($mahkeme->dm_karartarihi, "d.m.Y"),
                        "_kararno"  => $mahkeme->dm_kararno,
                        "_mahkeme"  => dAyracsizYazdir($mahkeme->dm_mahkeme),
                        "_maciklama"    => $mahkeme->dm_aciklama,
                        "_kayittarihi"  => timeToDateFormat($mahkeme->dm_adddate, "d.m.Y"),
                        "_kayituserid"    => $mahkeme->dm_adduser
                    );
                    array_push($mahkemeData, $mahkemeArray);
                }
            }
            $itemArray = array(
                "_id"           => $item->d_id,
                "_arsivno"       => ($item->d_arsivno),
                "_icrano"     => ($item->d_icrakayitno),
                "_kurumdosyano" => ($item->d_kurumdosyano),
                "_davaci"   => dAyracsizYazdir($item->d_davaci),
                "_davali"     => dAyracsizYazdir($item->d_davali),
                "_davakonu"      => dAyracsizYazdir($item->d_davakonusu),
                "_davakonuaciklama"       => dAyracsizYazdir($item->d_davakonuaciklama),
                "_mevkiplaka"      => html_entity_decode($item->d_mevkiplaka, ENT_QUOTES),
                "_proje"   => html_entity_decode($item->d_projebilgisi, ENT_QUOTES),
                "_icra"        => dAyracsizYazdir($item->d_icra),
                "_temyiz"   => ($item->d_temyiz),
                "_istinaftemyiz"        => ($item->d_istinaftemyiz),
                "_istinafkabul"         => dAyracsizYazdir($item->d_istinafkabul),
                "_istinafred"         => dAyracsizYazdir($item->d_istinafred),
                "_bozmailami"         => dAyracsizYazdir($item->d_bozmailami),
                "_onamailami"         => dAyracsizYazdir($item->d_onamailami),
                "_kesinlestirme"         => dAyracsizYazdir($item->d_kararkesinlestirme),
                "_mirascilik"         => dAyracsizYazdir($item->d_mirascilik),
                "_idarialacagi"         => number_format((float)($item->d_idarialacagi ?? 0), 2, ',', '.') . "₺",
                "_vekaletalacagi"         => number_format((float)($item->d_vekaletalacagi ?? 0), 2, ',', '.') . "₺",
                "_yargilamagideri"         => number_format((float)($item->d_yargilamagideri ?? 0), 2, ',', '.') . "₺",
                "_tapubilgisi"         => dTapuBilgiYazdir($item->d_tapubilgisi),
                "_tags"         => dTagYazdir($item->d_tags),
                "_aciklama"     => html_entity_decode($item->d_aciklama, ENT_QUOTES),
                "_mahkemeler"     => $mahkemeData
            );
            if (in_array($itemArray, $itemsData)) {
                $discardSayac++;
            } else {
                array_push($itemsData, $itemArray);
            }
        }

        $draw = isset($postData->draw) ? (int) $postData->draw : 0;
        $jsonFlags = JSON_UNESCAPED_UNICODE;
        if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
            $jsonFlags |= JSON_INVALID_UTF8_SUBSTITUTE;
        }

        if (count($itemsData) > 0) {
            $_sonuc =  new stdClass();
            $_sonuc->draw                     = $draw;
            $_sonuc->recordsTotal           = $totalRecord - $discardSayac;
            $_sonuc->recordsFiltered        = $totalRecord - $discardSayac;
            $_sonuc->data                   = $this->ensureUtf8Recursive($itemsData);
            $_sonuc->success                = true;
            $_sonuc->code                   = 200;
            $_sonuc->description            = ($totalRecord - $discardSayac) . " Adet Kayıt Bulundu.";
            $sonuc = json_encode($_sonuc, $jsonFlags);
            if ($sonuc === false) {
                $_sonuc->data = array();
                $_sonuc->description = "Kayitlar gosterilemedi (kodlama hatasi).";
                $sonuc = json_encode($_sonuc, $jsonFlags);
            }
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        } else {
            $_sonuc =  new stdClass();
            $_sonuc->draw                     = $draw;
            $_sonuc->recordsTotal           = $totalRecord;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 200;
            $_sonuc->description            = "Filtrelemeye Uygun Kayıt Bulunamadı.";
            $sonuc = json_encode($_sonuc, $jsonFlags);
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
            $_sonuc->description            = "HEDAS | Dosya Modülünde Güncelleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
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

        $getUpdate = $this->dosya_model->update(
            array(
                "d_id" => (int)$postData->id,
                "d_status" => 0
            ),
            array(
                "d_status" => 1
            )
        );

        if ($getUpdate) {
            $logData = new stdClass();
            $logData->ustid         = "";
            $logData->kullanici     = $this->userData->userB->u_id;
            $logData->aciklama      = (int)$postData->id . " Id Numaralı Dosya Evrakı Çöp Kutusundan Geri Alındı.";
            $logData->uygulama      = "hedas";
            $logData->modul         = "dosya";
            $logData->icerikid      = (int)$postData->id;
            $logData->olddata       = array("status" => 1);
            $logData->newdata       = array("status" => 0);
            $logyaz = logEkleTasima($logData);

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

    

    
    ## END::UPDATE FONKSIYONLASI



    public function api_excelexport()
    {

        //POSTTAN GELEN JSON VERI ALMA
        // $this->output->set_content_type("application/json");
        // $this->output->set_header("Access-Control-Allow-Origin: *");
        // $this->output->set_header("Access-Control-Allow-Methods: GET, OPTIONS");
        // $this->output->set_header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");

        // $this->JSON_DATA = json_decode(trim(file_get_contents("php://input")));
        //$this->JSON_DATA = json_encode(trim(file_get_contents("php://input")), true);
        //$this->JSON_DATA = json_decode($this->JSON_DATA);
        ini_set('display_errors', 0);
        error_reporting(E_ERROR | E_PARSE);


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
            $_sonuc->description            = "HEDAS | Dosya Modülünü Listeleme Yetkiniz Bulunmuyor. Lütfen Sistem Yöneticinizden Yetki İsteyiniz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }


        $_post          = (string)$this->input->post("data");//json_encode($this->input->post("data"));
        $_searchValue   = json_decode($_post);//$this->JSON_DATA->data;
        // print_r($_searchValue->dMahkemeData);
        // die();
        // print_r($_searchValue);
        // die();
        // $_searchValue   = json_decode($postData->search->value);
        $_orderColumn   = "d_id";//$this->getTableColumnName($postData->order[0]->column);
        $_orderType     = "DESC";//$postData->order[0]->dir;
        // $_kactan        = $postData->start;
        // $_kacar         = $postData->length;

        //GELEN VERI ISTENILEN DEĞERLERİ İÇERİYORMU KONTROLU
        if (!is_object($_searchValue) || isset($_searchValue->dAralik) === false || strpos((string)$_searchValue->dAralik, " & ") === false) {
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

        $tarihData = explode(" & ", (string)($_searchValue->dAralik ?? ''), 2);
        if (count($tarihData) < 2) {
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



        
        // if (($_tarihStop-$_tarihStart)>7948799) {
        //     $_sonuc =  new stdClass();
        //     $_sonuc->recordsTotal           = 0;
        //     $_sonuc->recordsFiltered        = 0;
        //     $_sonuc->data                   = array();
        //     $_sonuc->success                = false;
        //     $_sonuc->code                   = 203;
        //     $_sonuc->description            = "Tarih Aralığı Maximum 3 Ay Olabilir.";
        //     $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
        //     SetHeader($_sonuc->code);
        //     echo $sonuc;
        //     exit;
        // }
        


        $_dTags                 = trim(replaceHtml($_searchValue->dTags));
        $_dIcraNo               = trim(replaceHtml_Slash($_searchValue->dIcraNo));
        $_dKurumDosyaNo         = trim(replaceHtml_Slash($_searchValue->dKurumDosyaNo));
        $_dDavaci               = trim(replaceHtml_Slash($_searchValue->dDavaci));
        $_dDavali               = trim(replaceHtml_Slash($_searchValue->dDavali));
        $_dDavaKonusu           = trim(replaceHtml_Slash($_searchValue->dDavaKonusu));
        $_dDavaKonuAciklama     = trim(replaceHtml_Slash($_searchValue->dDavaKonuAciklama));
        $_dMevkiPlaka           = trim(replaceHtml_Slash($_searchValue->dMevkiPlaka));
        $_dIcra                 = trim(replaceHtml_Slash($_searchValue->dIcra));
        $_dTemyiz               = trim(replaceHtml_Slash($_searchValue->dTemyiz));
        $_dIstinafTemyiz        = trim(replaceHtml_Slash($_searchValue->dIstinafTemyiz));
        $_dIstinafKabul         = trim(replaceHtml_Slash($_searchValue->dIstinafKabul));
        $_dIstinafRed           = trim(replaceHtml_Slash($_searchValue->dIstinafRed));
        $_dBozmaIlami           = trim(replaceHtml_Slash($_searchValue->dBozmaIlami));
        $_dOnamaIlami           = trim(replaceHtml_Slash($_searchValue->dOnamaIlami));
        $_dMirascilik           = trim(replaceHtml_Slash($_searchValue->dMirascilik));
        $_dTapuBilgisi          = (int)trim(replaceHtml_Slash($_searchValue->dTapuBilgisi));
        $_dmMahkeme             = trim(replaceHtml_Slash($_searchValue->dMahkemeData->dmMahkeme));
        $_dmEsasNo              = trim(replaceHtml_Slash($_searchValue->dMahkemeData->dmEsasNo));

        $isFilter = false;
        $sqlEk = "";

        if (strlen($_dTags) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_tags LIKE '%" . $_dTags . "%'";
            } else {
                $sqlEk .= "d_tags LIKE '%" . $_dTags . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dIcraNo) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_icrakayitno LIKE '%" . $_dIcraNo . "%'";
            } else {
                $sqlEk .= "d_icrakayitno LIKE '%" . $_dIcraNo . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dKurumDosyaNo) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_kurumdosyano LIKE '%" . $_dKurumDosyaNo . "%'";
            } else {
                $sqlEk .= "d_kurumdosyano LIKE '%" . $_dKurumDosyaNo . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dDavaci) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_davaci LIKE '%" . $_dDavaci . "%'";
            } else {
                $sqlEk .= "d_davaci LIKE '%" . $_dDavaci . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dDavali) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_davali LIKE '%" . $_dDavali . "%'";
            } else {
                $sqlEk .= "d_davali LIKE '%" . $_dDavali . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dDavaKonusu) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_davakonusu LIKE '%" . $_dDavaKonusu . "%'";
            } else {
                $sqlEk .= "d_davakonusu LIKE '%" . $_dDavaKonusu . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dDavaKonuAciklama) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_davakonuaciklama LIKE '%" . $_dDavaKonuAciklama . "%'";
            } else {
                $sqlEk .= "d_davakonuaciklama LIKE '%" . $_dDavaKonuAciklama . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dMevkiPlaka) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_mevkiplaka LIKE '%" . $_dMevkiPlaka . "%'";
            } else {
                $sqlEk .= "d_mevkiplaka LIKE '%" . $_dMevkiPlaka . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dIcra) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_icra LIKE '%" . $_dIcra . "%'";
            } else {
                $sqlEk .= "d_icra LIKE '%" . $_dIcra . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dTemyiz) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_temyiz LIKE '%" . $_dTemyiz . "%'";
            } else {
                $sqlEk .= "d_temyiz LIKE '%" . $_dTemyiz . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dIstinafTemyiz) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_istinaftemyiz LIKE '%" . $_dIstinafTemyiz . "%'";
            } else {
                $sqlEk .= "d_istinaftemyiz LIKE '%" . $_dIstinafTemyiz . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dIstinafKabul) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_istinafkabul LIKE '%" . $_dIstinafKabul . "%'";
            } else {
                $sqlEk .= "d_istinafkabul LIKE '%" . $_dIstinafKabul . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dIstinafRed) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_istinafred LIKE '%" . $_dIstinafRed . "%'";
            } else {
                $sqlEk .= "d_istinafred LIKE '%" . $_dIstinafRed . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dBozmaIlami) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_bozmailami LIKE '%" . $_dBozmaIlami . "%'";
            } else {
                $sqlEk .= "d_bozmailami LIKE '%" . $_dBozmaIlami . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dOnamaIlami) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_onamailami LIKE '%" . $_dOnamaIlami . "%'";
            } else {
                $sqlEk .= "d_onamailami LIKE '%" . $_dOnamaIlami . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dMirascilik) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_mirascilik LIKE '%" . $_dMirascilik . "%'";
            } else {
                $sqlEk .= "d_mirascilik LIKE '%" . $_dMirascilik . "%'";
            }
            $isFilter = true;
        }

        if (in_array($_dTapuBilgisi, array(-1, 0, 1)) === true) {
            if ($isFilter == true) {
                $sqlEk .= " AND d_tapubilgisi = (" . $_dTapuBilgisi . ")";
            } else {
                $sqlEk .= "d_tapubilgisi = (" . $_dTapuBilgisi . ")";
            }
            $isFilter = true;
        }

        if (strlen($_dmMahkeme) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND dm_mahkeme LIKE '%" . $_dmMahkeme . "%'";
            } else {
                $sqlEk .= "dm_mahkeme LIKE '%" . $_dmMahkeme . "%'";
            }
            $isFilter = true;
        }

        if (strlen($_dmEsasNo) > 0) {
            if ($isFilter == true) {
                $sqlEk .= " AND dm_esasno LIKE '%" . $_dmEsasNo . "%'";
            } else {
                $sqlEk .= "dm_esasno LIKE '%" . $_dmEsasNo . "%'";
            }
            $isFilter = true;
        }



        if ($isFilter == true) {
            $sqlEk = " d_status=1 AND (" . $sqlEk . ")";
        } else {
            $sqlEk .= " d_status=1 AND (d_adddate >= " . trim(replaceHtml($_tarihStart)) . " AND d_adddate <= " . trim(replaceHtml($_tarihStop)) . ")";
        }


        // $totalSql = "SELECT COUNT(d_id) AS total FROM r8t_edys_dosya LEFT JOIN r8t_edys_dosya_mahkemeler ON dm_dosyaid=d_id WHERE" . $sqlEk;
        // $totalRecordS = $this->dosya_model->ek_query_all($totalSql);
        // $totalRecord = (int)$totalRecordS[0]->total;

        // die($totalSql);

        $sql = "SELECT d_adddate, d_kurumdosyano, d_arsivno, d_icrakayitno, d_davaci, d_davali, d_davakonusu, d_davakonuaciklama, dm_mahkeme, dm_acilistarihi, dm_esasno, dm_kararno, d_mevkiplaka, d_projebilgisi, d_icra, d_temyiz, d_istinaftemyiz, d_istinafkabul, d_istinafred, d_bozmailami, d_onamailami, d_kararkesinlestirme, d_mirascilik, d_tapubilgisi, d_idarialacagi, d_vekaletalacagi, d_yargilamagideri, d_aciklama, dm_aciklama FROM r8t_edys_dosya LEFT JOIN r8t_edys_dosya_mahkemeler ON dm_dosyaid=d_id WHERE" . $sqlEk;
        $sql .= " ORDER BY " . $_orderColumn . " " . strtoupper($_orderType);
        // $sql .= " LIMIT " . $_kactan . ", " . $_kacar;

        // die($sql);

        $totalRecord = 0;
        $filterRecord = $this->dosya_model->ek_query_all($sql);
        if (!$filterRecord) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = 0;
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

        $table_column = array(
            "Kayıt Tarihi",
            "Kurum Dosya No",
            "Arşiv Klasör No",
            "İcra Kayıt No",
            "Davacı",
            "Davalı",
            "Dava Konusu",
            "Konu Açıklaması",
            "Mahkemesi",
            "M.Aç. Tarihi",
            "Esas No",
            "Karar No",
            "Mevki/Plaka",
            "Proje Bilgisi",
            "İcra Bilgisi",
            "Temyiz Başvuru/Tarih",
            "İstinaf Başvuru/Tarih",
            "İstinaf Kabul Karar",
            "İstinaf Red Karar",
            "Bozma İlamı",
            "Onama İlamı",
            "Kesinleştirme Şerhi",
            "Mirasçılık",
            "Tapu Bilgisi",
            "İdari Alacağımız",
            "Vekalet Alacağı",
            "Yargılama Gideri",
            "Dosya Açıklama",
            "Mahkeme Açıklama"
        );

        $filename = str_replace(" & ", "_ile_", $_searchValue->dAralik) . '-Dosyalar-' . date("d-m-Y-H-i-s") . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        if ($output === false) {
            $_sonuc =  new stdClass();
            $_sonuc->recordsTotal           = 0;
            $_sonuc->recordsFiltered        = 0;
            $_sonuc->data                   = array();
            $_sonuc->success                = false;
            $_sonuc->code                   = 500;
            $_sonuc->description            = "Dosya çıktısı oluşturulamadı. Lütfen sistem yöneticinize başvurunuz.";
            $sonuc = json_encode($_sonuc, JSON_UNESCAPED_UNICODE);
            SetHeader($_sonuc->code);
            echo $sonuc;
            exit;
        }

        // Excel UTF-8 BOM
        fwrite($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($output, $table_column, ';');

        foreach ($filterRecord as $item) {
            $row = array(
                timeToDateFormat($item->d_adddate, "d-m-Y H:i:s"),
                $item->d_kurumdosyano,
                $item->d_arsivno,
                $item->d_icrakayitno,
                dAyracsizYazdir($item->d_davaci),
                dAyracsizYazdir($item->d_davali),
                dAyracsizYazdir($item->d_davakonusu),
                dAyracsizYazdir($item->d_davakonuaciklama),
                dAyracsizYazdir($item->dm_mahkeme),
                timeToDateFormat($item->dm_acilistarihi, "d-m-Y"),
                $item->dm_esasno,
                $item->dm_kararno,
                strip_tags(html_entity_decode((string)$item->d_mevkiplaka, ENT_QUOTES)),
                strip_tags(html_entity_decode((string)$item->d_projebilgisi, ENT_QUOTES)),
                dAyracsizYazdir($item->d_icra),
                $item->d_temyiz,
                $item->d_istinaftemyiz,
                dAyracsizYazdir($item->d_istinafkabul),
                dAyracsizYazdir($item->d_istinafred),
                dAyracsizYazdir($item->d_bozmailami),
                dAyracsizYazdir($item->d_onamailami),
                dAyracsizYazdir($item->d_kararkesinlestirme),
                dAyracsizYazdir($item->d_mirascilik),
                dTapuBilgiYazdir($item->d_tapubilgisi),
                number_format((float)($item->d_idarialacagi ?? 0), 2, ',', '.'),
                number_format((float)($item->d_vekaletalacagi ?? 0), 2, ',', '.'),
                number_format((float)($item->d_yargilamagideri ?? 0), 2, ',', '.'),
                strip_tags(html_entity_decode((string)$item->d_aciklama, ENT_QUOTES)),
                strip_tags(html_entity_decode((string)$item->dm_aciklama, ENT_QUOTES)),
            );
            fputcsv($output, $row, ';');
        }

        fclose($output);
        exit;

    }



}
?>