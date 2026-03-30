<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ai extends CI_Controller
{
    public $userData = false;

    public function __construct()
    {
        ini_set('display_errors', 0);
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING & ~E_STRICT);
        set_time_limit(120);

        parent::__construct();
        $this->load->model("dosya_model");
        $this->load->model("ai_model");
        $this->load->library("gemini_service");
        $this->load->helper("dosya");

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

    private function _checkAuth()
    {
        if ($this->userData === false) {
            $this->_err(401, "Oturum bulunamadı. Lütfen giriş yapınız.");
        }
        if (!isAllowedViewApp("hedas")) {
            $this->_err(403, "HEDAS uygulamasına erişim yetkiniz bulunmuyor.");
        }
    }

    private function _checkRateLimit()
    {
        $userId = $this->userData->userInfo->u_id;
        if ($this->ai_model->isUserRateLimited($userId)) {
            $this->_err(429, "Günlük AI kullanım limitiniz dolmuştur. Yarın tekrar deneyiniz.");
        }
    }

    private function _getUserId()
    {
        return $this->userData->userInfo->u_id;
    }

    public function api_dosyaSummary()
    {
        try {
            $this->_checkAuth();
            $this->_checkRateLimit();

            $input = json_decode(file_get_contents('php://input'));
            $dosyaId = isset($input->dosya_id) ? (int) $input->dosya_id : 0;

            if ($dosyaId <= 0) {
                $this->_err(400, "Geçersiz dosya ID.");
            }

            $dosya = $this->dosya_model->get(array('d_id' => $dosyaId, 'd_status >=' => 0));
            if (!$dosya) {
                $this->_err(404, "Dosya bulunamadı.");
            }

            $mahkemeler = $this->dosya_model->ek_get_all(
                'r8t_edys_dosya_mahkemeler',
                array('dm_dosyaid' => $dosyaId)
            );

            $dataText = "=== DOSYA BİLGİLERİ ===\n";
            $dataText .= "Kurum Dosya No: {$dosya->d_kurumdosyano}\n";
            $dataText .= "Davacı: {$dosya->d_davaci}\n";
            $dataText .= "Davalı: {$dosya->d_davali}\n";
            $dataText .= "Dava Konusu: {$dosya->d_davakonusu}\n";

            if (!empty($dosya->d_davakonuaciklama)) {
                $dataText .= "Dava Konusu Açıklama: {$dosya->d_davakonuaciklama}\n";
            }
            if (!empty($dosya->d_mevkiplaka)) {
                $dataText .= "Mevki/Plaka: {$dosya->d_mevkiplaka}\n";
            }
            if (!empty($dosya->d_projebilgisi)) {
                $dataText .= "Proje Bilgisi: {$dosya->d_projebilgisi}\n";
            }

            $dataText .= "İcra: " . (!empty($dosya->d_icra) ? $dosya->d_icra : "Yok") . "\n";
            $dataText .= "Temyiz: " . (!empty($dosya->d_temyiz) ? $dosya->d_temyiz : "Yok") . "\n";
            $dataText .= "İstinaf Temyiz: " . (!empty($dosya->d_istinaftemyiz) ? $dosya->d_istinaftemyiz : "Yok") . "\n";
            $dataText .= "İstinaf Kabul: " . (!empty($dosya->d_istinafkabul) ? $dosya->d_istinafkabul : "Yok") . "\n";
            $dataText .= "İstinaf Red: " . (!empty($dosya->d_istinafred) ? $dosya->d_istinafred : "Yok") . "\n";
            $dataText .= "Bozma İlamı: " . (!empty($dosya->d_bozmailami) ? $dosya->d_bozmailami : "Yok") . "\n";
            $dataText .= "Onama İlamı: " . (!empty($dosya->d_onamailami) ? $dosya->d_onamailami : "Yok") . "\n";

            if (!empty($mahkemeler)) {
                $dataText .= "\n=== MAHKEME BİLGİLERİ ===\n";
                foreach ($mahkemeler as $mah) {
                    $dataText .= "Mahkeme: {$mah->dm_mahkeme}, Esas No: {$mah->dm_esasno}, "
                        . "Açılış Tarihi: {$mah->dm_acilistarihi}, "
                        . "Karar Tarihi: " . (!empty($mah->dm_karartarihi) ? $mah->dm_karartarihi : "Bekliyor") . ", "
                        . "Karar No: " . (!empty($mah->dm_kararno) ? $mah->dm_kararno : "-") . "\n";
                    if (!empty($mah->dm_aciklama)) {
                        $dataText .= "  Açıklama: {$mah->dm_aciklama}\n";
                    }
                }
            }

            $maskedData = aiMaskSensitiveData($dataText);

            $systemInstruction = "Sen bir hukuk dosya analiz asistanısın. "
                . "SADECE aşağıda verilen dosya verilerindeki bilgileri kullan. Veride olmayan hiçbir dosya no, tarih, karar, aşama veya olay uydurma. "
                . "Tüm bilgiler (dosya no, mahkeme, taraflar, tarihler, karar metni vb.) veridekiyle birebir aynı olmalı. "
                . "Veride belirtilmeyen bir aşama veya karar için 'veride bulunmuyor' de; asla tahmin etme. "
                . "Verilen dosya bilgilerini Türkçe özetle: genel durum, aşama, karar durumu gibi yalnızca veride geçenleri belirt. "
                . "Kısa ve profesyonel dil kullan. Yanıtını HTML formatında döndür. "
                . "Başlıklar için <h5>, <h6>, listeler için <ul><li>, önemli bilgiler için <strong>, tablolar için <table class='table table-sm'> kullan. Sadece HTML döndür.";

            $result = $this->gemini_service->ask($maskedData, $systemInstruction);
            $this->_dbReconnect();
            $userId = $this->_getUserId();

            if ($result === false) {
                $error = $this->gemini_service->getLastError();
                $this->ai_model->addLog(aiBuildLogData($userId, 'hedas', 'dosya_summary', $maskedData, '', 'error', $error));
                $this->_err(500, "Dosya özeti oluşturulamadı: " . $error);
            }

            $this->ai_model->addLog(aiBuildLogData($userId, 'hedas', 'dosya_summary', $maskedData, $result, 'success'));

            $r = aiResponse(true, 200, "Dosya özeti oluşturuldu.");
            $r->data = new stdClass();
            $r->data->summary = $result;
            $r->data->dosya_no = $dosya->d_kurumdosyano;
            $r->data->remaining_quota = $this->ai_model->getUserRemainingQuota($userId);
            $this->_out($r);

        } catch (Exception $e) {
            $this->_err(500, "Beklenmeyen hata: " . $e->getMessage());
        } catch (Error $e) {
            $this->_err(500, "Sistem hatası: " . $e->getMessage());
        }
    }

    public function api_genelSummary()
    {
        try {
            $this->_checkAuth();
            $this->_checkRateLimit();

            $totalAktif = $this->dosya_model->ek_query_all(
                "SELECT COUNT(*) as total FROM r8t_edys_dosya WHERE d_status = 1"
            );
            $totalArsiv = $this->dosya_model->ek_query_all(
                "SELECT COUNT(*) as total FROM r8t_edys_dosya WHERE d_status = -1"
            );
            $konuDagilim = $this->dosya_model->ek_query_all(
                "SELECT d_davakonusu, COUNT(*) as sayi FROM r8t_edys_dosya WHERE d_status = 1 GROUP BY d_davakonusu ORDER BY sayi DESC LIMIT 10"
            );
            $mahkemeDagilim = $this->dosya_model->ek_query_all(
                "SELECT dm.dm_mahkeme, COUNT(*) as sayi FROM r8t_edys_dosya_mahkemeler dm INNER JOIN r8t_edys_dosya d ON d.d_id = dm.dm_dosyaid WHERE d.d_status = 1 GROUP BY dm.dm_mahkeme ORDER BY sayi DESC LIMIT 10"
            );

            $dataText = "=== HEDAS GENEL İSTATİSTİKLER ===\n";
            $dataText .= "Aktif Dosya Sayısı: " . $totalAktif[0]->total . "\n";
            $dataText .= "Arşiv Dosya Sayısı: " . $totalArsiv[0]->total . "\n";

            $dataText .= "\n=== DAVA KONUSU DAĞILIMI (İlk 10) ===\n";
            foreach ($konuDagilim as $item) {
                $dataText .= "Konu: {$item->d_davakonusu}, Sayı: {$item->sayi}\n";
            }

            $dataText .= "\n=== MAHKEME DAĞILIMI (İlk 10) ===\n";
            foreach ($mahkemeDagilim as $item) {
                $dataText .= "Mahkeme: {$item->dm_mahkeme}, Dosya Sayısı: {$item->sayi}\n";
            }

            $maskedData = aiMaskSensitiveData($dataText);
            $context = "HEDAS (Hukuki Evrak ve Dosya Arşiv Sistemi) dosya istatistikleri";
            $result = $this->gemini_service->summarize($maskedData, $context);
            $this->_dbReconnect();
            $userId = $this->_getUserId();

            if ($result === false) {
                $error = $this->gemini_service->getLastError();
                $this->ai_model->addLog(aiBuildLogData($userId, 'hedas', 'genel_summary', $maskedData, '', 'error', $error));
                $this->_err(500, "Genel özet oluşturulamadı: " . $error);
            }

            $this->ai_model->addLog(aiBuildLogData($userId, 'hedas', 'genel_summary', $maskedData, $result, 'success'));

            $r = aiResponse(true, 200, "Genel özet oluşturuldu.");
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

            $schema = aiGetSchemaHEDAS();
            $maskedQuery = aiMaskSensitiveData($userQuery);
            $generatedSQL = $this->gemini_service->textToSQL($maskedQuery, $schema);
            $this->_dbReconnect();
            $userId = $this->_getUserId();

            if ($generatedSQL === false) {
                $error = $this->gemini_service->getLastError();
                $this->ai_model->addLog(aiBuildLogData($userId, 'hedas', 'text_to_sql', $maskedQuery, '', 'error', $error));
                $this->_err(500, "SQL oluşturulamadı: " . $error);
            }

            $validation = aiValidateSQL($generatedSQL);
            if (!$validation['valid']) {
                $this->ai_model->addLog(aiBuildLogData($userId, 'hedas', 'text_to_sql', $maskedQuery, $generatedSQL, 'error', $validation['message']));
                $this->_err(403, $validation['message']);
            }

            $safeSQL = $validation['sql'];
            $results = $this->dosya_model->ek_query_all($safeSQL);

            if ($results === false || $results === null) {
                $dbError = $this->db->error();
                $errMsg = isset($dbError['message']) ? $dbError['message'] : 'Bilinmeyen veritabanı hatası';
                $this->ai_model->addLog(aiBuildLogData($userId, 'hedas', 'text_to_sql', $maskedQuery, $safeSQL, 'error', $errMsg));
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

            $this->ai_model->addLog(aiBuildLogData($userId, 'hedas', 'text_to_sql', $maskedQuery, $safeSQL . ' => ' . $resultCount . ' rows', 'success'));

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
