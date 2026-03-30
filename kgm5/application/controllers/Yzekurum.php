<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Yzekurum extends CI_Controller
{
    /** Bağlam için son kaç mesaj gönderilecek (token tasarrufu) */
    const CONTEXT_LAST_MESSAGES = 20;

    public $viewFolder = '';
    public $userData  = false;

    public function __construct()
    {
        ini_set('display_errors', 0);
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING & ~E_STRICT);
        set_time_limit(120);

        parent::__construct();
        $this->viewFolder = 'yzekurum_v';
        $this->load->model('auth_model');
        $this->load->model('ai_chat_model');
        $this->load->model('ai_model');
        $this->load->model('durusmalar_model');
        $this->load->library('gemini_service');
        $this->load->helper('durusmalar');
        $this->load->helper('tools');
        $this->load->helper('ai');

        $this->userData = $this->auth_model->userData;
        $this->db->db_debug = false;
    }

    private function _getUserId()
    {
        return $this->userData && isset($this->userData->userInfo->u_id)
            ? (int) $this->userData->userInfo->u_id
            : 0;
    }

    private function _checkAuth()
    {
        if ($this->userData === false) {
            $this->_err(401, 'Oturum bulunamadı. Lütfen giriş yapınız.');
        }
        if (!isAllowedViewApp('edts')) {
            $this->_err(403, 'EDTS uygulamasına erişim yetkiniz bulunmuyor.');
        }
    }

    private function _err($code, $message)
    {
        SetHeader($code);
        echo json_encode(aiResponse(false, $code, $message));
        exit;
    }

    /**
     * Eşzamanlı sohbet slotu alır. Limit aşılıyorsa false döner (process limiti aşımını önler).
     * @return string|false  Kilidin dosya yolu veya slot alınamazsa false
     */
    private function _acquireChatSlot()
    {
        $this->config->load('ai_config', true);
        $max = (int) $this->config->item('ai_max_concurrent_chat', 'ai_config');
        if ($max < 1) {
            $max = 5;
        }
        $lockDir = rtrim(APPPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'ai_chat_locks' . DIRECTORY_SEPARATOR;
        if (!is_dir($lockDir)) {
            @mkdir($lockDir, 0755, true);
        }
        if (!is_dir($lockDir) || !is_writable($lockDir)) {
            return true;
        }
        $lockId = uniqid('chat_', true);
        $lockFile = $lockDir . $lockId;
        if (@file_put_contents($lockFile, (string) time(), LOCK_EX) === false) {
            return true;
        }
        $cutoff = time() - 300;
        $count = 0;
        $files = @glob($lockDir . 'chat_*');
        if (is_array($files)) {
            foreach ($files as $f) {
                if (is_file($f) && filemtime($f) >= $cutoff) {
                    $count++;
                }
            }
        }
        if ($count > $max) {
            @unlink($lockFile);
            return false;
        }
        return $lockFile;
    }

    /**
     * Sohbet slotunu serbest bırakır.
     * @param string $lockFile  _acquireChatSlot() ile alınan dosya yolu
     */
    private function _releaseChatSlot($lockFile)
    {
        if ($lockFile !== null && $lockFile !== '' && is_file($lockFile)) {
            @unlink($lockFile);
        }
    }

    private function _out($obj)
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($obj);
        exit;
    }

    /**
     * Sayfa: YZ Ekürüm sohbet arayüzü
     */
    public function index()
    {
        if ($this->userData === false) {
            redirect(sys_url('login'));
            exit;
        }
        if (!isAllowedViewApp('edts')) {
            $alert = array(
                'title' => 'Hata!',
                'text'  => 'EDTS uygulaması için görüntüleme yetkiniz bulunmuyor.',
                'type'  => 'error',
            );
            $this->session->set_flashdata('alertToastr', $alert);
            redirect(sys_url('home'));
            exit;
        }

        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder  = 'list';
        $viewData->userData       = $this->userData;
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    /**
     * API: Kullanıcının sohbet oturumlarını listele
     */
    public function api_sessions()
    {
        $this->_checkAuth();
        $userId = $this->_getUserId();
        $sessions = $this->ai_chat_model->getSessionsForUser($userId, 50);
        $list = array();
        foreach ($sessions as $s) {
            $list[] = array(
                'session_id'  => (int) $s->session_id,
                'title'       => $s->title,
                'created_at'  => (int) $s->created_at,
                'updated_at'  => (int) $s->updated_at,
            );
        }
        $r = aiResponse(true, 200, 'Oturumlar listelendi.');
        $r->data = array('sessions' => $list);
        $this->_out($r);
    }

    /**
     * API: Oturumdaki mesajları getir
     */
    public function api_session_messages($sessionId)
    {
        $this->_checkAuth();
        $userId = $this->_getUserId();
        $sessionId = (int) $sessionId;
        if (!$this->ai_chat_model->isSessionOwnedByUser($sessionId, $userId)) {
            $this->_err(404, 'Oturum bulunamadı.');
        }
        $messages = $this->ai_chat_model->getMessagesForSession($sessionId);
        $list = array();
        foreach ($messages as $m) {
            $list[] = array(
                'id'       => (int) $m->id,
                'role'     => $m->role,
                'content'  => $m->content,
                'created_at' => (int) $m->created_at,
            );
        }
        $r = aiResponse(true, 200, 'Mesajlar listelendi.');
        $r->data = array('messages' => $list, 'session_id' => $sessionId);
        $this->_out($r);
    }

    /**
     * Birincil analiz türünü quick_action veya mesajdan tespit eder.
     */
    private function _getPrimaryAnalysisType($message, $quickAction = '')
    {
        if ($quickAction !== '') {
            return $quickAction;
        }
        $text = mb_strtolower(trim($message));
        if (preg_match('/haftalık|bu hafta|hafta özet/i', $text)) return 'haftalik';
        if (preg_match('/aylık|ay bazında|aylık dağılım/i', $text)) return 'aylik';
        if (preg_match('/avukat bazında|avukata göre/i', $text)) return 'avukat';
        if (preg_match('/memur bazında|memura göre/i', $text)) return 'memur';
        if (preg_match('/mahkeme bazında|mahkemeye göre/i', $text)) return 'mahkeme';
        if (preg_match('/taraf bazında|tarafa göre/i', $text)) return 'taraf';
        if (preg_match('/işlem bazında|işleme göre/i', $text)) return 'islem';
        if (preg_match('/geçmiş|önceki dönem|geçen (ay|yıl|hafta)/u', $text)) return 'gecmis';
        if (preg_match('/esas no|esas numarası/i', $text)) return 'esas_no';
        if (preg_match('/dosya no|dosya numarası/i', $text)) return 'dosya_no';
        if (preg_match('/duruşma tarih|tarihe göre durum/i', $text)) return 'durusma_tarih';
        return '';
    }

    /**
     * Son yıl (bulunduğumuz yıl) için tarih filtresi döndürür.
     */
    private function _getSonYilFiltre()
    {
        $yil = (int) date('Y');
        return array(
            'current_durusma_start' => '01-01-' . $yil,
            'current_durusma_end'   => '31-12-' . $yil,
            'search' => '',
            'filterMahkemeSelect' => '',
            'filterMemurSelect'  => '',
            'filterAvukatSelect' => '',
            'filterIslemSelect'  => '',
            'dlara_taraf'        => '',
            'dlara_dtakip'       => '',
        );
    }

    /**
     * Son 12 ay için tarih filtresi döndürür.
     */
    private function _getSon12AyFiltre()
    {
        return array(
            'current_durusma_start' => date('d-m-Y', strtotime('-12 months')),
            'current_durusma_end'   => date('d-m-Y'),
            'search' => '',
            'filterMahkemeSelect' => '',
            'filterMemurSelect'  => '',
            'filterAvukatSelect' => '',
            'filterIslemSelect'  => '',
            'dlara_taraf'        => '',
            'dlara_dtakip'       => '',
        );
    }

    /**
     * Niye/niyet tespiti: birincil analiz türüne göre EDTS veri metni ve analiz talimatı üretir.
     * Tarih işlemleri (aylık, geçmiş, avukat/memur/mahkeme/taraf/işlem) sadece son yıla ait veri kullanır.
     */
    private function _gatherEdtsDataForIntent($message, $quickAction = '')
    {
        $primary = $this->_getPrimaryAnalysisType($message, $quickAction);
        $sdata = '';

        if ($primary === 'gecmis') {
            $sdata = $this->durusmalar_model->getAutoFiltre((object) $this->_getSon12AyFiltre());
        } elseif (in_array($primary, array('avukat', 'memur', 'mahkeme', 'taraf', 'islem'), true)) {
            $sdata = $this->durusmalar_model->getAutoFiltre((object) $this->_getSonYilFiltre());
        }

        $dataText = '';
        $analysisType = $primary;

        switch ($primary) {
            case 'haftalik':
                $dataText = $this->_buildHaftalikAnalizData();
                break;
            case 'aylik':
                $dataText = $this->_buildAylikAnalizData($sdata);
                break;
            case 'avukat':
                $rows = $this->durusmalar_model->durusmaAvukatBazli($sdata);
                $toplam = array_sum(array_map(function ($r) { return (int) $r->sayi; }, $rows));
                $yil = (int) date('Y');
                $dataText = "DÖNEM: {$yil} yılı (sadece son yıl).\nTOPLAM DURUŞMA (dağılıma giren): {$toplam}\n\n=== AVUKAT BAZLI DURUŞMA DAĞILIMI ===\n";
                $dataText .= "Avukat | Duruşma Sayısı | Oran (%)\n";
                foreach ($rows as $item) {
                    $dataText .= $item->d_avukat . " | " . $item->sayi . " | " . $item->yuzde . "\n";
                }
                break;
            case 'memur':
                $rows = $this->durusmalar_model->durusmaMemurBazli($sdata);
                $toplam = array_sum(array_map(function ($r) { return (int) $r->sayi; }, $rows));
                $yil = (int) date('Y');
                $dataText = "DÖNEM: {$yil} yılı (sadece son yıl).\nTOPLAM DURUŞMA (dağılıma giren): {$toplam}\n\n=== MEMUR BAZLI DURUŞMA DAĞILIMI ===\n";
                $dataText .= "Memur | Duruşma Sayısı | Oran (%)\n";
                foreach ($rows as $item) {
                    $dataText .= $item->d_memur . " | " . $item->sayi . " | " . $item->yuzde . "\n";
                }
                break;
            case 'mahkeme':
                $rows = $this->durusmalar_model->durusmaMahkemeBazli($sdata);
                $toplam = array_sum(array_map(function ($r) { return (int) $r->sayi; }, $rows));
                $yil = (int) date('Y');
                $dataText = "DÖNEM: {$yil} yılı (sadece son yıl).\nTOPLAM DURUŞMA (dağılıma giren): {$toplam}\n\n=== MAHKEME BAZLI DURUŞMA DAĞILIMI (İlk 5 mahkeme - en çok duruşması olanlar) ===\n";
                $dataText .= "Mahkeme | Duruşma Sayısı | Oran (%)\n";
                foreach ($rows as $item) {
                    $dataText .= $item->d_mahkeme . " | " . $item->sayi . " | " . $item->yuzde . "\n";
                }
                break;
            case 'taraf':
                $rows = $this->durusmalar_model->durusmaTarafBazli($sdata);
                $toplam = array_sum(array_map(function ($r) { return (int) $r->sayi; }, $rows));
                $yil = (int) date('Y');
                $dataText = "DÖNEM: {$yil} yılı (sadece son yıl).\nTOPLAM DURUŞMA (taraf bilgisi olan): {$toplam}\n\n=== TARAF BAZLI DURUŞMA DAĞILIMI ===\n";
                $dataText .= "Taraf | Duruşma Sayısı | Oran (%)\n";
                foreach ($rows as $item) {
                    $dataText .= $item->d_taraf . " | " . $item->sayi . " | " . $item->yuzde . "\n";
                }
                break;
            case 'islem':
                $rows = $this->durusmalar_model->durusmaIslemBazli($sdata);
                $toplam = array_sum(array_map(function ($r) { return (int) $r->sayi; }, $rows));
                $yil = (int) date('Y');
                $dataText = "DÖNEM: {$yil} yılı (sadece son yıl).\nTOPLAM DURUŞMA (işlem bilgisi olan): {$toplam}\n\n=== İŞLEM BAZLI DURUŞMA DAĞILIMI ===\n";
                $dataText .= "İşlem | Duruşma Sayısı | Oran (%)\n";
                foreach ($rows as $item) {
                    $dataText .= $item->d_islem . " | " . $item->sayi . " | " . $item->yuzde . "\n";
                }
                break;
            case 'gecmis':
                $dataText = $this->_buildGecmisDonemAnalizData($sdata);
                break;
            case 'ara':
            case 'esas_no':
            case 'dosya_no':
            case 'durusma_tarih':
                $dataText = '';
                break;
            default:
                if ($primary === '') {
                    $text = mb_strtolower(trim($message . ' ' . $quickAction));
                    if (strpos($text, 'haftalık') !== false || strpos($text, 'bu hafta') !== false) {
                        $dataText = $this->_buildHaftalikAnalizData();
                        $analysisType = 'haftalik';
                    } elseif (strpos($text, 'aylık') !== false) {
                        $dataText = $this->_buildAylikAnalizData($sdata);
                        $analysisType = 'aylik';
                    } else {
                        $avukat = $this->durusmalar_model->durusmaAvukatBazli($sdata);
                        $mahkeme = $this->durusmalar_model->durusmaMahkemeBazli($sdata);
                        $islem = $this->durusmalar_model->durusmaIslemBazli($sdata);
                        $dataText = "=== AVUKAT BAZLI (ilk 5) ===\n";
                        foreach (array_slice($avukat, 0, 5) as $r) {
                            $dataText .= $r->d_avukat . " | " . $r->sayi . " | %" . $r->yuzde . "\n";
                        }
                        $dataText .= "\n=== MAHKEME BAZLI (ilk 5) ===\n";
                        foreach ($mahkeme as $r) {
                            $dataText .= $r->d_mahkeme . " | " . $r->sayi . " | %" . $r->yuzde . "\n";
                        }
                        $dataText .= "\n=== İŞLEM BAZLI ===\n";
                        foreach ($islem as $r) {
                            $dataText .= $r->d_islem . " | " . $r->sayi . " | %" . $r->yuzde . "\n";
                        }
                    }
                }
                break;
        }

        return array('dataText' => $dataText, 'analysis_type' => $analysisType);
    }

    /**
     * Haftalık analiz: günlere/avukatlara/mahkemelere göre özet (Ai controller ile uyumlu).
     */
    private function _buildHaftalikAnalizData()
    {
        $haftalik = $this->durusmalar_model->durusmaListesiHaftalik('');
        if (empty($haftalik)) {
            return "BU HAFTA DURUŞMA: 0\nBu hafta için planlanmış duruşma bulunmamaktadır.";
        }
        $toplam = count($haftalik);
        $gunlereGore = array();
        foreach ($haftalik as $item) {
            $t = isset($item->tarih) ? trim($item->tarih) : '';
            if ($t !== '') {
                $gunlereGore[$t] = isset($gunlereGore[$t]) ? $gunlereGore[$t] + 1 : 1;
            }
        }
        ksort($gunlereGore);
        $avukatlaraGore = array();
        foreach ($haftalik as $item) {
            $a = isset($item->d_avukat) && trim($item->d_avukat) !== '' ? trim($item->d_avukat) : 'Belirtilmemiş';
            $avukatlaraGore[$a] = isset($avukatlaraGore[$a]) ? $avukatlaraGore[$a] + 1 : 1;
        }
        arsort($avukatlaraGore);
        $mahkemelereGore = array();
        foreach ($haftalik as $item) {
            $m = isset($item->d_mahkeme) && trim($item->d_mahkeme) !== '' ? trim($item->d_mahkeme) : 'Belirtilmemiş';
            $mahkemelereGore[$m] = isset($mahkemelereGore[$m]) ? $mahkemelereGore[$m] + 1 : 1;
        }
        arsort($mahkemelereGore);

        $gunAdlari = array('Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi');
        $tarihFormatla = function ($tarih) use ($gunAdlari) {
            $ts = strtotime($tarih);
            return ($ts === false) ? $tarih : date('d/m/Y', $ts) . ' - ' . $gunAdlari[(int) date('w', $ts)];
        };
        $aylar = array(1 => 'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık');
        $haftaTarihMetni = '';
        if (!empty($gunlereGore)) {
            $b = min(array_keys($gunlereGore));
            $s = max(array_keys($gunlereGore));
            $haftaTarihMetni = (int) date('j', strtotime($b)) . ' ' . $aylar[(int) date('n', strtotime($b))] . ' ' . date('Y', strtotime($b)) . ' - ' . (int) date('j', strtotime($s)) . ' ' . $aylar[(int) date('n', strtotime($s))] . ' ' . date('Y', strtotime($s));
        }
        $enYogunGun = ''; $enYogunSayi = 0;
        foreach ($gunlereGore as $tarih => $adet) {
            if ($adet > $enYogunSayi) { $enYogunSayi = $adet; $enYogunGun = $tarihFormatla($tarih); }
        }
        $enAvukat = $avukatlaraGore ? array_key_first($avukatlaraGore) : ''; $enAvukatSayi = $avukatlaraGore ? reset($avukatlaraGore) : 0;
        $enMahkeme = $mahkemelereGore ? array_key_first($mahkemelereGore) : ''; $enMahkemeSayi = $mahkemelereGore ? reset($mahkemelereGore) : 0;

        $dataText = "TOPLAM DURUŞMA: {$toplam}\n\n";
        $dataText .= "=== GİRİŞ BİLGİSİ (yanıtında önce bu hafta tarihini yaz, sonra özet) ===\n";
        $dataText .= "Hafta tarihi: {$haftaTarihMetni}\n";
        $dataText .= "En yoğun gün: {$enYogunGun} ({$enYogunSayi} duruşma)\n";
        $dataText .= "En çok duruşması olan avukat: {$enAvukat} ({$enAvukatSayi} duruşma)\n";
        $dataText .= "En çok duruşması olan mahkeme: {$enMahkeme} ({$enMahkemeSayi} duruşma)\n\n";
        $dataText .= "=== GÜNLERE GÖRE DURUŞMA SAYILARI ===\nTarih | Sayı\n";
        foreach ($gunlereGore as $tarih => $adet) {
            $dataText .= $tarihFormatla($tarih) . " | {$adet}\n";
        }
        $dataText .= "\n=== AVUKATLARA GÖRE DURUŞMA SAYILARI ===\nAvukat | Sayı\n";
        foreach ($avukatlaraGore as $av => $adet) {
            $dataText .= $av . " | {$adet}\n";
        }
        $dataText .= "\n=== MAHKEMELERE GÖRE DURUŞMA SAYILARI (İlk 10) ===\nMahkeme | Sayı\n";
        $ilk10 = array_slice($mahkemelereGore, 0, 10, true);
        foreach ($ilk10 as $mh => $adet) {
            $dataText .= $mh . " | {$adet}\n";
        }
        return $dataText;
    }

    /**
     * Aylık analiz: sadece bulunduğumuz yıla ait duruşma ve karar dağılımı.
     */
    private function _buildAylikAnalizData($sdata)
    {
        $yil = (int) date('Y');
        if ($sdata === '') {
            $start = '01-01-' . $yil;
            $end   = '31-12-' . $yil;
            $filtre = array(
                'current_durusma_start' => $start,
                'current_durusma_end'   => $end,
                'search' => '',
                'filterMahkemeSelect' => '',
                'filterMemurSelect'  => '',
                'filterAvukatSelect' => '',
                'filterIslemSelect'  => '',
                'dlara_taraf'        => '',
                'dlara_dtakip'       => '',
            );
            $sdata = $this->durusmalar_model->getAutoFiltre((object) $filtre);
        }

        $aylik = $this->durusmalar_model->durusmaListesiAylik($sdata);
        $karar = $this->durusmalar_model->kararListesiAylik($sdata);
        $toplamDurusma = 0;
        $toplamKarar = 0;
        foreach ($aylik as $r) { $toplamDurusma += (int) $r->sayi; }
        foreach ($karar as $r) { $toplamKarar += (int) $r->sayi; }

        $dataText = "DÖNEM: {$yil} yılı (sadece bu yıla ait veriler).\n";
        $dataText .= "{$yil} YILI TOPLAM DURUŞMA: {$toplamDurusma}\n";
        $dataText .= "{$yil} YILI TOPLAM KARAR: {$toplamKarar}\n\n";
        $dataText .= "=== AYLIK DURUŞMA DAĞILIMI ({$yil}) ===\nAy | Sayı\n";
        foreach ($aylik as $item) {
            $dataText .= $item->ay . " | " . $item->sayi . "\n";
        }
        $dataText .= "\n=== AYLIK KARAR DAĞILIMI ({$yil}) ===\nAy | Sayı\n";
        foreach ($karar as $item) {
            $dataText .= $item->ay . " | " . $item->sayi . "\n";
        }
        return $dataText;
    }

    /**
     * Geçmiş dönem (son 3 ay): avukat, mahkeme, işlem dağılımı.
     */
    private function _buildGecmisDonemAnalizData($sdata)
    {
        $avukat = $this->durusmalar_model->durusmaAvukatBazli($sdata);
        $mahkeme = $this->durusmalar_model->durusmaMahkemeBazli($sdata);
        $islem = $this->durusmalar_model->durusmaIslemBazli($sdata);
        $toplam = 0;
        foreach ($avukat as $r) { $toplam += (int) $r->sayi; }

        $dataText = "DÖNEM: Son 12 ay (geçmiş dönem – son yıl). TOPLAM DURUŞMA: {$toplam}\n\n";
        $dataText .= "=== AVUKAT BAZLI (en çok duruşması olanlar) ===\nAvukat | Sayı | Oran (%)\n";
        foreach ($avukat as $item) {
            $dataText .= $item->d_avukat . " | " . $item->sayi . " | " . $item->yuzde . "\n";
        }
        $dataText .= "\n=== MAHKEME BAZLI (İlk 5) ===\nMahkeme | Sayı | Oran (%)\n";
        foreach ($mahkeme as $item) {
            $dataText .= $item->d_mahkeme . " | " . $item->sayi . " | " . $item->yuzde . "\n";
        }
        $dataText .= "\n=== İŞLEM BAZLI ===\nİşlem | Sayı | Oran (%)\n";
        foreach ($islem as $item) {
            $dataText .= $item->d_islem . " | " . $item->sayi . " | " . $item->yuzde . "\n";
        }
        return $dataText;
    }

    /**
     * Analiz türüne göre model için ek talimat: ne tür çıktı üretmesi gerektiği.
     */
    private function _getAnalysisTypeInstruction($analysisType)
    {
        $tasks = array(
            'haftalik' => " Görev: Kullanıcı BU HAFTA analizi istiyor. 1) Önce hafta tarihini belirt, toplam duruşma sayısını, en yoğun günü, en çok duruşması olan avukat ve mahkemeyi (sayılarıyla) kısaca özetle. 2) Sonra üç tablo ver: 'Günlere Göre Duruşma Dağılımı', 'Avukatlara Göre Duruşma Dağılımı', 'Mahkemelere Göre Duruşma Dağılımı (İlk 10)'. Sayı ve isimleri verideki gibi aynen kullan. Tabloları <table class='table table-sm'> ile oluştur.",
            'aylik'    => " Görev: Kullanıcı AYLIK analiz istiyor. Veriler sadece bulunduğumuz yıla aittir. Önce yılı ve o yıla ait toplam duruşma ve karar sayılarını yaz; ardından aylık duruşma ve aylık karar dağılımını iki ayrı tablo ile ver (Ay | Sayı). Ay adları ve sayıları verideki gibi aynen kullan.",
            'avukat'   => " Görev: Kullanıcı AVUKAT BAZINDA analiz istiyor. Veriler sadece son yıla (bulunduğumuz yıl) aittir. Önce dönemi (yılı) ve toplam duruşma sayısını belirt; ardından avukat bazında dağılımı tablo ile ver (Avukat | Duruşma Sayısı | Oran). Sayı ve yüzdeleri verideki gibi aynen kullan.",
            'memur'    => " Görev: Kullanıcı MEMUR BAZINDA analiz istiyor. Veriler sadece son yıla aittir. Toplam duruşma sayısını belirt; ardından memur bazında dağılımı tablo ile ver. Sayı ve yüzdeleri aynen kullan.",
            'mahkeme'  => " Görev: Kullanıcı MAHKEME BAZINDA analiz istiyor. Veriler sadece son yıla aittir. Toplam duruşma sayısını belirt, ardından mahkeme dağılımını tablo ile ver (ilk 5). Sayı ve yüzdeleri aynen kullan.",
            'taraf'    => " Görev: Kullanıcı TARAF BAZINDA analiz istiyor. Veriler sadece son yıla aittir. Toplam (taraf bilgisi olan) duruşma sayısını belirt; ardından taraf bazında dağılımı tablo ile ver. Sayı ve yüzdeleri aynen kullan.",
            'islem'    => " Görev: Kullanıcı İŞLEM BAZINDA analiz istiyor. Veriler sadece son yıla aittir. Toplam duruşma sayısını belirt; ardından işlem türüne göre dağılımı tablo ile ver. Sayı ve yüzdeleri aynen kullan.",
            'gecmis'   => " Görev: Kullanıcı GEÇMİŞ DÖNEM (son 12 ay / son yıl) analizi istiyor. Dönemi ve toplam duruşma sayısını belirt; ardından avukat, mahkeme ve işlem bazında dağılımları tablolar halinde ver. Sayıları aynen kullan.",
            'esas_no'  => " Görev: Kullanıcı esas numarasına göre durum/sorgu istiyor. Aşağıdaki sorgu sonucuna göre net bir özet ver; varsa tablo ile göster.",
            'dosya_no' => " Görev: Kullanıcı dosya numarasına göre durum/sorgu istiyor. Aşağıdaki sorgu sonucuna göre net bir özet ver; varsa tablo ile göster.",
            'durusma_tarih' => " Görev: Kullanıcı duruşma tarihlerine göre durum analizi istiyor. Aşağıdaki verilere göre tarih bazlı özet veya tablo ver.",
            'ara'      => " Görev: Kullanıcı doğal dil ile arama/sorgu yaptı. Aşağıdaki sorgu sonucuna göre net ve anlaşılır bir yanıt ver; gerekirse tablo kullan.",
        );
        $instruction = isset($tasks[$analysisType]) ? $tasks[$analysisType] : '';
        if ($instruction !== '') {
            return " " . trim($instruction) . " Eğer aşağıda 'Veriler (EDTS)' veya 'Sorgu sonucu' varsa, önce o verilere göre yanıtla.";
        }
        return " Eğer aşağıda 'Veriler (EDTS)' veya 'Sorgu sonucu' ile başlayan bir blok varsa, önce o verilere göre yanıtla.";
    }

    /**
     * Esas No / Dosya No / Duruşma tarihi sorgusu: Text-to-SQL ile çalıştırıp sonucu metne dönüştürür.
     */
    private function _runTextToSqlAndGetSummary($userQuery, $userId)
    {
        $schema = aiGetSchemaEDTS();
        $maskedQuery = aiMaskSensitiveData($userQuery);
        $generatedSQL = $this->gemini_service->textToSQL($maskedQuery, $schema);
        if ($generatedSQL === false) {
            return '';
        }
        $validation = aiValidateSQL($generatedSQL);
        if (!$validation['valid']) {
            return '';
        }
        $results = $this->durusmalar_model->ek_query_all($validation['sql']);
        if (!is_array($results) || empty($results)) {
            return "Sorgu sonucu: Kayıt bulunamadı.\n";
        }
        $firstRow = (array) $results[0];
        $columns = array_keys($firstRow);
        $dataText = "Sorgu sonucu (ilk " . count($results) . " kayıt):\n";
        $dataText .= implode("\t", $columns) . "\n";
        $limit = min(count($results), 50);
        for ($i = 0; $i < $limit; $i++) {
            $row = (array) $results[$i];
            $dataText .= implode("\t", array_map(function ($v) {
                return is_string($v) ? $v : (string) $v;
            }, $row)) . "\n";
        }
        return $dataText;
    }

    /**
     * API: Sohbet mesajı gönder, yanıt al
     */
    public function api_chat()
    {
        set_time_limit(180);
        @ini_set('memory_limit', '256M');

        $lockFile = null;
        try {
            $this->_checkAuth();
            $userId = $this->_getUserId();

            if ($this->ai_model->isUserRateLimited($userId)) {
                $this->_err(429, 'Günlük AI kullanım limitiniz dolmuştur. Yarın tekrar deneyiniz.');
            }

            $lockFile = $this->_acquireChatSlot();
            if ($lockFile === false) {
                $this->_err(503, 'Çok fazla eşzamanlı sohbet isteği. Lütfen kısa süre sonra tekrar deneyin.');
            }
            if ($lockFile !== null && $lockFile !== '') {
                register_shutdown_function(function () use ($lockFile) {
                    if (is_string($lockFile) && is_file($lockFile)) {
                        @unlink($lockFile);
                    }
                });
            }

            $input = json_decode(file_get_contents('php://input'));
            $message = isset($input->message) ? trim($input->message) : '';
            $sessionId = isset($input->session_id) ? (int) $input->session_id : 0;
            $quickAction = isset($input->quick_action) ? trim($input->quick_action) : '';

            if ($message === '' && $quickAction === '') {
                $this->_err(400, 'Mesaj boş olamaz.');
            }
            if (mb_strlen($message) > 4000) {
                $this->_err(400, 'Mesaj en fazla 4000 karakter olabilir.');
            }

            if ($message === '' && $quickAction !== '') {
                $message = $this->_quickActionToPrompt($quickAction);
            }

            $isNewSession = false;
            if ($sessionId <= 0) {
                $title = mb_strlen($message) > 50 ? mb_substr($message, 0, 47) . '...' : $message;
                $sessionId = $this->ai_chat_model->createSession($userId, $title ?: 'Yeni sohbet');
                if (!$sessionId) {
                    $this->_err(500, 'Oturum oluşturulamadı.');
                }
                $isNewSession = true;
            } else {
                if (!$this->ai_chat_model->isSessionOwnedByUser($sessionId, $userId)) {
                    $this->_err(404, 'Oturum bulunamadı.');
                }
            }

            $this->ai_chat_model->addMessage($sessionId, 'user', $message);

            $gathered = $this->_gatherEdtsDataForIntent($message, $quickAction);
            $edtsData = is_array($gathered) ? $gathered['dataText'] : $gathered;
            $analysisType = is_array($gathered) ? $gathered['analysis_type'] : '';

            $textLower = mb_strtolower($message . ' ' . $quickAction);
            $needsSql = (strpos($textLower, 'esas no') !== false || strpos($textLower, 'esas no\'ya') !== false
                || strpos($textLower, 'dosya no') !== false || strpos($textLower, 'dosya no\'ya') !== false
                || strpos($textLower, 'duruşma tarih') !== false || strpos($textLower, 'durum analizi') !== false
                || (strpos($textLower, 'ara') !== false && strlen($message) > 10));
            if ($needsSql && $message !== '') {
                $sqlSummary = $this->_runTextToSqlAndGetSummary($message, $userId);
                if ($sqlSummary !== '') {
                    $edtsData .= "\n" . $sqlSummary;
                }
            }

            $systemInstruction = "Sen YZ Ekürüm adında bir hukuk ve duruşma takip asistanısın. EDTS (Elektronik Duruşma Takip Sistemi) verilerine dayanarak yanıt veriyorsun. "
                . "SADECE verilen verilerdeki bilgileri kullan; veride olmayan bilgi uydurma. Sayı ve isimleri verideki gibi aynen kullan. "
                . "Yanıtını Türkçe, net ve profesyonel ver. Gerekirse HTML kullan: <table class='table table-sm'>, <ul><li>, <strong>. Markdown kullanma, sadece düz metin veya HTML.";
            $systemInstruction .= $this->_getAnalysisTypeInstruction($analysisType);
            if ($edtsData !== '') {
                $systemInstruction .= "\n\nVeriler (EDTS):\n" . aiMaskSensitiveData($edtsData);
            }

            $historyMessages = $this->ai_chat_model->getLastMessagesForContext($sessionId, self::CONTEXT_LAST_MESSAGES);
            $contentsForGemini = array();
            foreach ($historyMessages as $m) {
                $contentsForGemini[] = array(
                    'role' => $m->role === 'model' ? 'model' : 'user',
                    'text' => $m->content,
                );
            }

            $reply = $this->gemini_service->chatWithHistory($contentsForGemini, $systemInstruction);
            $this->db->close();
            $this->db->initialize();

            if ($reply === false) {
                $err = $this->gemini_service->getLastError();
                $this->ai_model->addLog(aiBuildLogData($userId, 'edts', 'yz_chat', aiMaskSensitiveData($message), '', 'error', $err));
                $this->_err(500, 'Yanıt oluşturulamadı: ' . $err);
            }

            $messageId = $this->ai_chat_model->addMessage($sessionId, 'model', $reply);
            if ($isNewSession && $messageId) {
                $title = mb_strlen($message) > 50 ? mb_substr($message, 0, 47) . '...' : $message;
                $this->ai_chat_model->updateSessionTitle($sessionId, $title ?: 'Yeni sohbet');
            }

            $this->ai_model->addLog(aiBuildLogData($userId, 'edts', 'yz_chat', aiMaskSensitiveData($message), mb_substr($reply, 0, 500), 'success'));

            $r = aiResponse(true, 200, 'Yanıt oluşturuldu.');
            $r->data = new stdClass();
            $r->data->message = $reply;
            $r->data->session_id = $sessionId;
            $r->data->message_id = $messageId;
            $r->data->remaining_quota = $this->ai_model->getUserRemainingQuota($userId);
            $this->_out($r);

        } catch (Exception $e) {
            if (function_exists('log_message')) {
                log_message('error', 'Yzekurum api_chat Exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            }
            $this->_out(aiResponse(false, 500, 'Beklenmeyen hata. Lütfen kısa bir mesajla tekrar deneyin veya daha sonra tekrar deneyin.'));
        } catch (Throwable $e) {
            if (function_exists('log_message')) {
                log_message('error', 'Yzekurum api_chat Throwable: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            }
            $this->_out(aiResponse(false, 500, 'Beklenmeyen hata. Lütfen kısa bir mesajla tekrar deneyin veya daha sonra tekrar deneyin.'));
        } finally {
            $this->_releaseChatSlot($lockFile);
        }
    }

    private function _quickActionToPrompt($quickAction)
    {
        $map = array(
            'haftalik'   => 'Bu haftanın duruşma özetini ver: toplam sayı, günlere göre dağılım, en yoğun gün, avukat ve mahkeme dağılımları.',
            'aylik'      => 'Bu yılın aylık duruşma ve karar dağılımı analizini yap: aylara göre sayılar ve toplamlar.',
            'avukat'     => 'Avukat bazında duruşma dağılımı: hangi avukatın kaç duruşması var, oranlarıyla birlikte.',
            'memur'      => 'Memur bazında duruşma dağılımı: hangi memurun kaç duruşması var, oranlarıyla.',
            'mahkeme'    => 'Mahkeme bazında duruşma dağılımı: hangi mahkemede kaç duruşma var (en çok duruşması olanlar).',
            'taraf'      => 'Taraf bazında duruşma dağılımı: davacı/davalı vb. taraflara göre sayı ve oranlar.',
            'islem'      => 'İşlem bazında duruşma dağılımı: DURUŞMA, KARAR vb. işlem türlerine göre sayı ve oranlar.',
            'gecmis'     => 'Son 3 aylık geçmiş dönem analizi: avukat, mahkeme ve işlem bazında dağılımlar.',
            'esas_no'    => 'Belirli bir esas numarasına göre duruşma/dosya durumu sorgulayacağım. Esas numarasını yazın.',
            'dosya_no'   => 'Belirli bir dosya numarasına göre duruşma/dosya durumu sorgulayacağım. Dosya numarasını yazın.',
            'durusma_tarih' => 'Duruşma tarihlerine göre durum: belirli bir tarih veya tarih aralığındaki duruşmaları listele.',
            'ara'        => 'Duruşmalarda doğal dil ile arama: Örn. "Bu ay en çok duruşması olan avukat", "X mahkemesindeki duruşmalar". Sorunuzu yazın.',
        );
        return isset($map[$quickAction]) ? $map[$quickAction] : $quickAction;
    }

    /**
     * API: Kota bilgisi (opsiyonel, UI için)
     */
    public function api_quota()
    {
        $this->_checkAuth();
        $userId = $this->_getUserId();
        $remaining = $this->ai_model->getUserRemainingQuota($userId);
        $r = aiResponse(true, 200, 'Kota bilgisi.');
        $r->data = new stdClass();
        $r->data->remaining_quota = $remaining;
        $this->_out($r);
    }
}
