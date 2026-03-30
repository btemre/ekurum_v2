<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ai extends CI_Controller
{
    public $userData = false;

    public function __construct()
    {
        ini_set('display_errors', 0);
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING & ~E_STRICT);
        set_time_limit(90);

        parent::__construct();
        $this->load->model("durusmalar_model");
        $this->load->model("ai_model");
        $this->load->library("gemini_service");
        $this->load->helper("durusmalar");

        $this->load->model("auth_model");
        $this->userData = $this->auth_model->userData;

        $this->db->db_debug = FALSE;

        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=utf-8");
    }

    /**
     * Uzun API cagrilarindan sonra MySQL baglantisini yeniden kurar.
     * CI mysqli driver reconnect() yeni baglanti olusturmadigi icin close + initialize kullanilir.
     */
    private function _dbReconnect()
    {
        $this->db->close();
        $this->db->initialize();
    }

    private function _out($obj)
    {
        echo json_encode($obj);
        exit;
    }

    private function _err($code, $message)
    {
        SetHeader($code);
        $this->_out(aiResponse(false, $code, $message));
    }

    public function api_test()
    {
        $r = new stdClass();
        $r->success = true;
        $r->code = 200;
        $r->description = "AI controller calisiyor.";
        $r->data = new stdClass();
        $r->data->php_version = phpversion();
        $r->data->user_logged = ($this->userData !== false);
        $dtNow = new DateTime('now', new DateTimeZone('Europe/Istanbul'));
        $r->data->timestamp = $dtNow->format('Y-m-d H:i:s');
        $this->_out($r);
    }

    private function _checkAuth()
    {
        if ($this->userData === false) {
            $this->_err(401, "Oturum bulunamadı. Lütfen giriş yapınız.");
        }
        if (!isAllowedViewApp("edts")) {
            $this->_err(403, "EDTS uygulamasına erişim yetkiniz bulunmuyor.");
        }
    }

    private function _checkRateLimit()
    {
        $userId = $this->_getUserId();
        if ($this->ai_model->isUserRateLimited($userId)) {
            $this->_err(429, "Günlük AI kullanım limitiniz dolmuştur. Yarın tekrar deneyiniz.");
        }
    }

    private function _getUserId()
    {
        if (!$this->userData) {
            return 0;
        }
        $id = 0;
        if (isset($this->userData->userInfo->u_id) && (int) $this->userData->userInfo->u_id > 0) {
            $id = (int) $this->userData->userInfo->u_id;
        }
        if ($id <= 0 && isset($this->userData->userB->u_id) && (int) $this->userData->userB->u_id > 0) {
            $id = (int) $this->userData->userB->u_id;
        }
        return $id;
    }

    public function api_summary()
    {
        try {
            $this->_checkAuth();
            $this->_checkRateLimit();

            $input = json_decode(file_get_contents('php://input'));
            $period = isset($input->period) ? $input->period : 'month';

            $sdata = "";
            if (isset($input->filters)) {
                $sdata = $this->durusmalar_model->getAutoFiltre($input->filters);
            }

            $avukatBazli  = $this->durusmalar_model->durusmaAvukatBazli($sdata);
            $memurBazli   = $this->durusmalar_model->durusmaMemurBazli($sdata);
            $tarafBazli   = $this->durusmalar_model->durusmaTarafBazli($sdata);
            $mahkemeBazli = $this->durusmalar_model->durusmaMahkemeBazli($sdata);
            $islemBazli   = $this->durusmalar_model->durusmaIslemBazli($sdata);
            $haftalik     = $this->durusmalar_model->durusmaListesiHaftalik($sdata);

            $dataText = "=== AVUKAT BAZLI DURUŞMA DAĞILIMI ===\n";
            foreach ($avukatBazli as $item) {
                $dataText .= "Avukat: {$item->d_avukat}, Duruşma Sayısı: {$item->sayi}, Oran: %{$item->yuzde}\n";
            }
            $dataText .= "\n=== MEMUR BAZLI DURUŞMA DAĞILIMI ===\n";
            foreach ($memurBazli as $item) {
                $dataText .= "Memur: {$item->d_memur}, Duruşma Sayısı: {$item->sayi}, Oran: %{$item->yuzde}\n";
            }
            $dataText .= "\n=== TARAF BAZLI DURUŞMA DAĞILIMI ===\n";
            foreach ($tarafBazli as $item) {
                $dataText .= "Taraf: {$item->d_taraf}, Duruşma Sayısı: {$item->sayi}, Oran: %{$item->yuzde}\n";
            }
            $dataText .= "\n=== MAHKEME BAZLI DURUŞMA DAĞILIMI (İlk 5) ===\n";
            foreach ($mahkemeBazli as $item) {
                $dataText .= "Mahkeme: {$item->d_mahkeme}, Duruşma Sayısı: {$item->sayi}, Oran: %{$item->yuzde}\n";
            }
            $dataText .= "\n=== İŞLEM BAZLI DURUŞMA DAĞILIMI ===\n";
            foreach ($islemBazli as $item) {
                $dataText .= "İşlem: {$item->d_islem}, Sayı: {$item->sayi}, Oran: %{$item->yuzde}\n";
            }
            $dataText .= "\n=== BU HAFTA DURUŞMALAR ===\n";
            foreach ($haftalik as $item) {
                $dataText .= "Tarih: {$item->tarih}, Mahkeme: {$item->d_mahkeme}, Esas No: {$item->d_esasno}, "
                    . "Avukat: {$item->d_avukat}, Taraf: {$item->d_taraf}, İşlem: {$item->d_islem}\n";
            }

            $maskedData = aiMaskSensitiveData($dataText);
            $context = "EDTS (Elektronik Duruşma Takip Sistemi) duruşma istatistikleri";
            $result = $this->gemini_service->summarize($maskedData, $context);
            $this->_dbReconnect();
            $userId = $this->_getUserId();

            if ($result === false) {
                $error = $this->gemini_service->getLastError();
                $this->ai_model->addLog(aiBuildLogData($userId, 'edts', 'summary', $maskedData, '', 'error', $error));
                $this->_err(500, "AI özet oluşturulamadı: " . $error);
            }

            $this->ai_model->addLog(aiBuildLogData($userId, 'edts', 'summary', $maskedData, $result, 'success'));

            $r = aiResponse(true, 200, "Özet başarıyla oluşturuldu.");
            $r->data = new stdClass();
            $r->data->summary = $result;
            $r->data->remaining_quota = $this->ai_model->getUserRemainingQuota($userId);
            $this->_out($r);

        } catch (Exception $e) {
            $this->_err(500, "Beklenmeyen hata: " . $e->getMessage());
        } catch (Error $e) {
            $this->_err(500, "Sistem hatası: " . $e->getMessage());
        }
    }

    public function api_weekSummary()
    {
        try {
            $this->_checkAuth();
            $this->_checkRateLimit();

            $haftalik = $this->durusmalar_model->durusmaListesiHaftalik();

            if (empty($haftalik)) {
                $r = aiResponse(true, 200, "Bu hafta duruşma bulunmuyor.");
                $r->data = new stdClass();
                $r->data->summary = "Bu hafta için planlanmış duruşma bulunmamaktadır.";
                $r->data->count = 0;
                $this->_out($r);
            }

            $toplam = count($haftalik);

            // Günlere göre say (tarih => adet)
            $gunlereGore = [];
            foreach ($haftalik as $item) {
                $t = isset($item->tarih) ? trim($item->tarih) : '';
                if ($t === '') continue;
                $gunlereGore[$t] = isset($gunlereGore[$t]) ? $gunlereGore[$t] + 1 : 1;
            }
            ksort($gunlereGore);

            // Avukatlara göre say
            $avukatlaraGore = [];
            foreach ($haftalik as $item) {
                $a = isset($item->d_avukat) && trim($item->d_avukat) !== '' ? trim($item->d_avukat) : 'Belirtilmemiş';
                $avukatlaraGore[$a] = isset($avukatlaraGore[$a]) ? $avukatlaraGore[$a] + 1 : 1;
            }
            arsort($avukatlaraGore);

            // Mahkemelere göre say
            $mahkemelereGore = [];
            foreach ($haftalik as $item) {
                $m = isset($item->d_mahkeme) && trim($item->d_mahkeme) !== '' ? trim($item->d_mahkeme) : 'Belirtilmemiş';
                $mahkemelereGore[$m] = isset($mahkemelereGore[$m]) ? $mahkemelereGore[$m] + 1 : 1;
            }
            arsort($mahkemelereGore);

            $gunAdlari = ['Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi'];
            $_tzIst = new DateTimeZone('Europe/Istanbul');
            $tarihFormatla = function ($tarih) use ($gunAdlari, $_tzIst) {
                $dt = DateTime::createFromFormat('Y-m-d', trim($tarih), $_tzIst);
                if (!$dt) return $tarih;
                return $dt->format('d/m/Y') . ' - ' . $gunAdlari[(int) $dt->format('w')];
            };

            // En yoğun gün, en çok duruşması olan avukat ve mahkeme (giriş özeti için)
            $enYogunGun = '';
            $enYogunGunSayi = 0;
            foreach ($gunlereGore as $tarih => $adet) {
                if ($adet > $enYogunGunSayi) {
                    $enYogunGunSayi = $adet;
                    $enYogunGun = $tarihFormatla($tarih);
                }
            }
            $enCokAvukat = $avukatlaraGore ? (string) array_key_first($avukatlaraGore) : '';
            $enCokAvukatSayi = $avukatlaraGore ? (int) reset($avukatlaraGore) : 0;
            $enCokMahkeme = $mahkemelereGore ? (string) array_key_first($mahkemelereGore) : '';
            $enCokMahkemeSayi = $mahkemelereGore ? (int) reset($mahkemelereGore) : 0;

            $aylar = [1 => 'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
            $tarihHaftaFormatla = function ($tarih) use ($aylar, $_tzIst) {
                $dt = DateTime::createFromFormat('Y-m-d', trim($tarih), $_tzIst);
                if (!$dt) return $tarih;
                return (int) $dt->format('j') . ' ' . $aylar[(int) $dt->format('n')] . ' ' . $dt->format('Y');
            };
            $haftaTarihMetni = '';
            if (!empty($gunlereGore)) {
                $haftaBaslangic = min(array_keys($gunlereGore));
                $haftaBitis = max(array_keys($gunlereGore));
                $haftaTarihMetni = $tarihHaftaFormatla($haftaBaslangic) . ' - ' . $tarihHaftaFormatla($haftaBitis);
            }

            $dataText = "TOPLAM DURUŞMA SAYISI: {$toplam}\n\n";
            $dataText .= "=== GİRİŞ ÖZETİ (giriş paragrafının EN BAŞINDA hafta tarihini belirt, sonra bunları kullan; en sonda 'Detaylı bilgileri aşağı kaydırarak inceleyebilirsiniz.' yaz) ===\n";
            $dataText .= "Hafta tarihi: {$haftaTarihMetni}\n";
            $dataText .= "En yoğun gün: {$enYogunGun} ({$enYogunGunSayi} duruşma)\n";
            $dataText .= "En çok duruşması olan avukat: {$enCokAvukat} ({$enCokAvukatSayi} duruşma)\n";
            $dataText .= "En çok duruşması olan mahkeme: {$enCokMahkeme} ({$enCokMahkemeSayi} duruşma)\n\n";
            $dataText .= "=== GÜNLERE GÖRE DURUŞMA SAYILARI (Tarih formatı: Gün/Ay/Yıl - Gün Adı; sayıları aynen kullan) ===\n";
            $dataText .= "Tarih | Duruşma Sayısı\n";
            foreach ($gunlereGore as $tarih => $adet) {
                $tarihGoster = $tarihFormatla($tarih);
                $dataText .= "{$tarihGoster} | {$adet}\n";
            }
            $dataText .= "\n=== AVUKATLARA GÖRE DURUŞMA SAYILARI (bu sayıları aynen kullan) ===\n";
            $dataText .= "Avukat | Duruşma Sayısı\n";
            foreach ($avukatlaraGore as $avukat => $adet) {
                $dataText .= $avukat . " | " . $adet . "\n";
            }
            $mahkemelereGoreIlk10 = array_slice($mahkemelereGore, 0, 10, true);
            $dataText .= "\n=== MAHKEMELERE GÖRE DURUŞMA SAYILARI - İLK 10 MAHKEME (bu sayıları aynen kullan) ===\n";
            $dataText .= "Mahkeme | Duruşma Sayısı\n";
            foreach ($mahkemelereGoreIlk10 as $mahkeme => $adet) {
                $dataText .= $mahkeme . " | " . $adet . "\n";
            }

            $maskedData = aiMaskSensitiveData($dataText);

            $systemInstruction = "Sen bir hukuk asistanısın. Aşağıda önceden hesaplanmış dağılım verileri var. "
                . "Görevin: 1) Önce bir giriş paragrafı yaz. Giriş paragrafının EN BAŞINDA mutlaka hafta tarih bilgisini belirt (verideki 'Hafta tarihi' satırındaki aralığı aynen kullan, örn: '17 Şubat 2026 - 23 Şubat 2026 tarihleri arasında...'). "
                . "Ardından toplam duruşma sayısını, en yoğun günü, en çok duruşması olan avukatı ve mahkemeyi (sayılarıyla) kısaca belirt. "
                . "Paragrafın sonunda mutlaka şu cümleyi yaz: 'Detaylı bilgileri aşağı kaydırarak inceleyebilirsiniz.' "
                . "2) Sonra aşağıda üç tablo olarak ver: 'Günlere Göre Duruşma Dağılımı', 'Avukatlara Göre Duruşma Dağılımı', 'Mahkemelere Göre Duruşma Dağılımı (İlk 10 Mahkeme)' başlıklarıyla. Üçüncü tablonun başlığı tam olarak 'Mahkemelere Göre Duruşma Dağılımı (İlk 10 Mahkeme)' olsun. Sayı ve isimleri VERİDEKİ GİBİ AYNEN kullan. "
                . "Yanıtını mutlaka TAMAMLA; tablo veya cümle ortasında kesme. "
                . "HTML formatında döndür: başlıklar <h5>/<h6>, tablolar <table class='table table-sm'>. Sadece HTML, markdown kullanma.";

            $result = $this->gemini_service->ask($maskedData, $systemInstruction);
            $this->_dbReconnect();
            $userId = $this->_getUserId();

            if ($result === false) {
                $error = $this->gemini_service->getLastError();
                $this->ai_model->addLog(aiBuildLogData($userId, 'edts', 'week_summary', $maskedData, '', 'error', $error));
                $this->_err(500, "Haftalık özet oluşturulamadı: " . $error);
            }

            $this->ai_model->addLog(aiBuildLogData($userId, 'edts', 'week_summary', $maskedData, $result, 'success'));

            $r = aiResponse(true, 200, "Haftalık özet oluşturuldu.");
            $r->data = new stdClass();
            $r->data->summary = $result;
            $r->data->count = count($haftalik);
            $r->data->remaining_quota = $this->ai_model->getUserRemainingQuota($userId);
            $this->_out($r);

        } catch (Exception $e) {
            $this->_err(500, "Beklenmeyen hata: " . $e->getMessage());
        } catch (Error $e) {
            $this->_err(500, "Sistem hatası: " . $e->getMessage());
        }
    }

    /**
     * Kapasite planlaması için tahmine dayalı tek cümlelik uyarı döndürür.
     * scope=daily ise sadece bugünün verisiyle günlük analiz; yoksa haftalık.
     */
    public function api_capacityForecast()
    {
        try {
            $this->_checkAuth();
            $this->_checkRateLimit();

            $input = json_decode(file_get_contents('php://input'), true);
            $scope = isset($input['scope']) && $input['scope'] === 'daily' ? 'daily' : 'week';

            if ($scope === 'daily') {
                $tzIst = new DateTimeZone('Europe/Istanbul');
                $nowIst = new DateTime('now', $tzIst);
                $todayStart = (new DateTime($nowIst->format('Y-m-d') . ' 00:00:00', $tzIst))->getTimestamp();
                $todayEnd = (new DateTime($nowIst->format('Y-m-d') . ' 23:59:59', $tzIst))->getTimestamp();
                $bugunListe = $this->db->query("SELECT d_avukat, d_mahkeme FROM r8t_edts_durusmalar WHERE d_status=1 AND d_durusmatarihi >= ? AND d_durusmatarihi <= ?", array($todayStart, $todayEnd))->result();
                $bugunToplam = count($bugunListe);
                $avukatlaraGore = array();
                $mahkemelereGore = array();
                foreach ($bugunListe as $item) {
                    $a = isset($item->d_avukat) && trim($item->d_avukat) !== '' ? trim($item->d_avukat) : 'Belirtilmemiş';
                    $avukatlaraGore[$a] = isset($avukatlaraGore[$a]) ? $avukatlaraGore[$a] + 1 : 1;
                    $m = isset($item->d_mahkeme) && trim($item->d_mahkeme) !== '' ? trim($item->d_mahkeme) : 'Belirtilmemiş';
                    $mahkemelereGore[$m] = isset($mahkemelereGore[$m]) ? $mahkemelereGore[$m] + 1 : 1;
                }
                arsort($avukatlaraGore);
                arsort($mahkemelereGore);
                $enYogunAvukat = $avukatlaraGore ? array_key_first($avukatlaraGore) : '';
                $enYogunAvukatSayi = $avukatlaraGore ? reset($avukatlaraGore) : 0;
                $enYogunMahkeme = $mahkemelereGore ? array_key_first($mahkemelereGore) : '';
                $enYogunMahkemeSayi = $mahkemelereGore ? reset($mahkemelereGore) : 0;
                $bugunTarih = $nowIst->format('d.m.Y');

                $dataText = "Bugün ({$bugunTarih}) toplam duruşma: {$bugunToplam}. "
                    . "Bugün en çok duruşması olan avukat: {$enYogunAvukat} ({$enYogunAvukatSayi} duruşma). "
                    . "Bugün en çok duruşması olan mahkeme: {$enYogunMahkeme} ({$enYogunMahkemeSayi} duruşma).";
                $maskedData = aiMaskSensitiveData($dataText);
                $systemInstruction = "Sen bir günlük duruşma analiz asistanısın. Verilen BUGÜNE AİT istatistiklere göre TEK CÜMLELİK günlük analiz yaz. Türkçe, kısa, net. "
                    . "Sadece bugünün verisini özetle (avukat adı, mahkeme adı veya toplam sayı vurgulayabilirsin). Sadece uyarı/özet cümlesi, HTML/markdown yok.";
                $prompt = $maskedData . "\n\nBu veriye göre bugün için tek cümlelik günlük analiz yaz. Verideki sayı ve isimleri aynen kullan.";
            } else {
                $haftalik = $this->durusmalar_model->durusmaListesiHaftalik();
                $buHaftaToplam = count($haftalik);

                $nextTwoWeeks = $this->db->query("
                    SELECT COUNT(*) as cnt FROM r8t_edts_durusmalar du
                    WHERE du.d_status = '1'
                    AND FROM_UNIXTIME(du.d_durusmatarihi) >= CURDATE() + INTERVAL 1 DAY
                    AND FROM_UNIXTIME(du.d_durusmatarihi) < CURDATE() + INTERVAL 15 DAY
                ")->row();
                $gelecekIkiHaftaToplam = $nextTwoWeeks ? (int) $nextTwoWeeks->cnt : 0;

                $avukatlaraGore = [];
                foreach ($haftalik as $item) {
                    $a = isset($item->d_avukat) && trim($item->d_avukat) !== '' ? trim($item->d_avukat) : 'Belirtilmemiş';
                    $avukatlaraGore[$a] = isset($avukatlaraGore[$a]) ? $avukatlaraGore[$a] + 1 : 1;
                }
                arsort($avukatlaraGore);
                $enYogunAvukat = $avukatlaraGore ? array_key_first($avukatlaraGore) : '';
                $enYogunAvukatSayi = $avukatlaraGore ? reset($avukatlaraGore) : 0;

                $gunlereGore = [];
                foreach ($haftalik as $item) {
                    $t = isset($item->tarih) ? trim($item->tarih) : '';
                    if ($t !== '') $gunlereGore[$t] = isset($gunlereGore[$t]) ? $gunlereGore[$t] + 1 : 1;
                }
                arsort($gunlereGore);
                $enYogunGun = $gunlereGore ? array_key_first($gunlereGore) : '';
                $enYogunGunSayi = $gunlereGore ? reset($gunlereGore) : 0;

                $tipTurleri = [
                    'avukat_odakli' => "Sadece en çok duruşması olan avukatı (isim ve sayı) vurgulayan tek cümle yaz.",
                    'gun_odakli'     => "Sadece en yoğun günü (tarih/gün adı ve sayı) vurgulayan tek cümle yaz.",
                    'donem_odakli'  => "Önümüzdeki 14 günün sayısını vurgulayan, 'yoğun' kelimesi kullanmadan tek cümle yaz.",
                    'sayi_odakli'   => "Bu hafta toplam sayıyı ve kapasite planlaması önerisi veren tek cümle yaz (örn. dikkat edilmesi gereken gün).",
                ];
                $secimKey = array_rand($tipTurleri);
                $secimTalimat = $tipTurleri[$secimKey];

                $dataText = "Bu hafta toplam duruşma: {$buHaftaToplam}. "
                    . "Önümüzdeki 14 günde planlanan duruşma: {$gelecekIkiHaftaToplam}. "
                    . "Bu hafta en çok duruşması olan avukat: {$enYogunAvukat} ({$enYogunAvukatSayi} duruşma). "
                    . "Bu hafta en yoğun gün: {$enYogunGun} ({$enYogunGunSayi} duruşma).";
                $maskedData = aiMaskSensitiveData($dataText);
                $systemInstruction = "Sen bir kapasite planlama asistanısın. Verilen istatistiklere göre TEK CÜMLELİK uyarı yaz. Türkçe, kısa, net. "
                    . "ÖNEMLİ: 'Bu hafta yoğun', 'önümüzdeki günler yoğun', 'duruşma yoğun', 'yoğun geçecek' gibi aynı anlama gelen ifadeleri KULLANMA. "
                    . "Her seferinde farklı bir açı (avukat adı, belirli gün, sayı, öneri) kullan. Sadece uyarı cümlesi, HTML/markdown yok.";
                $prompt = $maskedData . "\n\nKural: " . $secimTalimat . " Verideki sayı ve isimleri aynen kullan.";
            }

            $result = $this->gemini_service->ask($prompt, $systemInstruction);
            $this->_dbReconnect();
            $userId = $this->_getUserId();

            if ($result === false || trim($result) === '') {
                $error = $this->gemini_service->getLastError();
                $this->ai_model->addLog(aiBuildLogData($userId, 'edts', 'capacity_forecast', $maskedData, '', 'error', $error ?: 'Boş yanıt'));
                $this->_err(500, $error ?: "Tahmin oluşturulamadı.");
            }

            $this->ai_model->addLog(aiBuildLogData($userId, 'edts', 'capacity_forecast', $maskedData, $result, 'success'));

            $r = aiResponse(true, 200, "Tahmin oluşturuldu.");
            $r->data = new stdClass();
            $r->data->forecast = trim(preg_replace('/<[^>]+>/', '', $result));
            $r->data->remaining_quota = $this->ai_model->getUserRemainingQuota($userId);
            $this->_out($r);

        } catch (Exception $e) {
            $this->_err(500, "Beklenmeyen hata: " . $e->getMessage());
        } catch (Error $e) {
            $this->_err(500, "Sistem hatası: " . $e->getMessage());
        }
    }

    public function api_textToSQL()
    {
        try {
            $this->_checkAuth();
            $this->_checkRateLimit();

            $input = json_decode(file_get_contents('php://input'));
            $userQuery = isset($input->query) ? trim($input->query) : '';

            if (empty($userQuery) || mb_strlen($userQuery) < 5) {
                $this->_err(400, "Lütfen en az 5 karakterlik bir soru giriniz.");
            }
            if (mb_strlen($userQuery) > 500) {
                $this->_err(400, "Soru en fazla 500 karakter olabilir.");
            }

            $schema = aiGetSchemaEDTS();
            $maskedQuery = aiMaskSensitiveData($userQuery);
            $generatedSQL = $this->gemini_service->textToSQL($maskedQuery, $schema);
            $this->_dbReconnect();
            $userId = $this->_getUserId();

            if ($generatedSQL === false) {
                $error = $this->gemini_service->getLastError();
                $this->ai_model->addLog(aiBuildLogData($userId, 'edts', 'text_to_sql', $maskedQuery, '', 'error', $error));
                $this->_err(500, "SQL oluşturulamadı: " . $error);
            }

            $validation = aiValidateSQL($generatedSQL);
            if (!$validation['valid']) {
                $this->ai_model->addLog(aiBuildLogData($userId, 'edts', 'text_to_sql', $maskedQuery, $generatedSQL, 'error', $validation['message']));
                $this->_err(403, $validation['message']);
            }

            $safeSQL = $validation['sql'];
            $results = $this->durusmalar_model->ek_query_all($safeSQL);

            if ($results === false || $results === null) {
                $dbError = $this->db->error();
                $errMsg = isset($dbError['message']) ? $dbError['message'] : 'Bilinmeyen veritabanı hatası';
                $this->ai_model->addLog(aiBuildLogData($userId, 'edts', 'text_to_sql', $maskedQuery, $safeSQL, 'error', $errMsg));
                $this->_err(500, "Sorgu çalıştırılırken hata oluştu.");
            }

            $resultCount = is_array($results) ? count($results) : 0;
            $columns = array();
            if ($resultCount > 0) {
                $firstRow = (array) $results[0];
                $columns = array_keys($firstRow);
            }

            $maskedResults = array();
            foreach ($results as $row) {
                $maskedRow = new stdClass();
                foreach ((array) $row as $key => $val) {
                    $maskedRow->$key = aiMaskSensitiveData((string) $val);
                }
                $maskedResults[] = $maskedRow;
            }

            $this->ai_model->addLog(aiBuildLogData($userId, 'edts', 'text_to_sql', $maskedQuery, $safeSQL . ' => ' . $resultCount . ' rows', 'success'));

            $r = aiResponse(true, 200, "Sorgu başarıyla çalıştırıldı.");
            $r->data = new stdClass();
            $r->data->query = $userQuery;
            $r->data->sql = $safeSQL;
            $r->data->columns = $columns;
            $r->data->rows = $maskedResults;
            $r->data->total = $resultCount;
            $r->data->remaining_quota = $this->ai_model->getUserRemainingQuota($userId);
            $this->_out($r);

        } catch (Exception $e) {
            $this->_err(500, "Beklenmeyen hata: " . $e->getMessage());
        } catch (Error $e) {
            $this->_err(500, "Sistem hatası: " . $e->getMessage());
        }
    }

    public function api_quota()
    {
        try {
            $this->_checkAuth();

            $userId = $this->_getUserId();
            $remaining = $this->ai_model->getUserRemainingQuota($userId);
            $statsToday = $this->ai_model->getUserStats($userId, 'today');
            $statsMonth = $this->ai_model->getUserStats($userId, 'month');

            $r = aiResponse(true, 200, "Kota bilgisi alındı.");
            $r->data = new stdClass();
            $r->data->remaining_today = $remaining;
            $r->data->used_today = (int) $statsToday->total_requests;
            $r->data->used_month = (int) $statsMonth->total_requests;
            $this->_out($r);

        } catch (Exception $e) {
            $this->_err(500, "Beklenmeyen hata: " . $e->getMessage());
        } catch (Error $e) {
            $this->_err(500, "Sistem hatası: " . $e->getMessage());
        }
    }
}
