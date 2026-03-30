<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gemini_service
{
    protected $CI;
    private $apiKey;
    private $model;
    private $endpoint;
    private $maxTokens;
    private $temperature;
    private $timeout;
    private $lastError;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->config->load('ai_config', TRUE);

        $this->apiKey     = $this->CI->config->item('ai_gemini_api_key', 'ai_config');
        $this->model      = $this->CI->config->item('ai_gemini_model', 'ai_config');
        $this->endpoint   = $this->CI->config->item('ai_gemini_endpoint', 'ai_config');
        $this->maxTokens  = $this->CI->config->item('ai_max_tokens', 'ai_config');
        $this->temperature = $this->CI->config->item('ai_temperature', 'ai_config');
        $this->timeout    = $this->CI->config->item('ai_request_timeout', 'ai_config');
        $this->lastError  = '';
    }

    /**
     * Gemini API'ye metin üretim isteği gönderir.
     *
     * @param string $prompt   Kullanıcı promptu
     * @param string $systemInstruction  Sistem talimatı (opsiyonel)
     * @param float  $temperature  Yaratıcılık seviyesi 0-1 (opsiyonel)
     * @return object|false  Başarılıysa yanıt objesi, başarısızsa false
     */
    public function generateContent($prompt, $systemInstruction = '', $temperature = null)
    {
        if (empty($prompt)) {
            $this->lastError = 'Prompt boş olamaz.';
            return false;
        }

        $temp = ($temperature !== null) ? $temperature : $this->temperature;

        $requestBody = array(
            'contents' => array(
                array(
                    'parts' => array(
                        array('text' => $prompt)
                    )
                )
            ),
            'generationConfig' => array(
                'temperature'    => $temp,
                'maxOutputTokens' => $this->maxTokens,
            )
        );

        if (!empty($systemInstruction)) {
            $requestBody['systemInstruction'] = array(
                'parts' => array(
                    array('text' => $systemInstruction)
                )
            );
        }

        $url = $this->endpoint . $this->model . ':generateContent?key=' . $this->apiKey;

        $response = $this->_httpPost($url, $requestBody);

        if ($response === false) {
            return false;
        }

        $decoded = json_decode($response);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->lastError = 'API yanıtı JSON olarak çözümlenemedi.';
            return false;
        }

        if (isset($decoded->error)) {
            $this->lastError = isset($decoded->error->message)
                ? $decoded->error->message
                : 'Bilinmeyen API hatası.';
            return false;
        }

        return $decoded;
    }

    /**
     * Gemini yanıtından metin kısmını çıkarır.
     *
     * @param object $response  generateContent yanıtı
     * @return string  Metin yanıtı
     */
    public function extractText($response)
    {
        if (
            isset($response->candidates[0]->content->parts[0]->text)
        ) {
            return $response->candidates[0]->content->parts[0]->text;
        }

        return '';
    }

    /**
     * Kısa yol: prompt gönder, metin al.
     *
     * @param string $prompt
     * @param string $systemInstruction
     * @return string|false
     */
    public function ask($prompt, $systemInstruction = '')
    {
        $response = $this->generateContent($prompt, $systemInstruction);
        if ($response === false) {
            return false;
        }
        return $this->extractText($response);
    }

    /**
     * Özet üretir.
     *
     * @param string $data        Özetlenecek veri (metin veya JSON)
     * @param string $context     Bağlam bilgisi (örn: "EDTS duruşma verileri")
     * @param string $language    Dil kodu
     * @return string|false
     */
    public function summarize($data, $context = '', $language = 'tr')
    {
        $langText = ($language === 'tr') ? 'Türkçe' : 'English';

        $systemInstruction = "Sen bir kurumsal veri analiz asistanısın. "
            . "Verilen verileri {$langText} olarak özetle. "
            . "Kısa, net ve profesyonel bir dil kullan. "
            . "Sayısal verileri vurgula, önemli trendleri belirt.";

        $prompt = "";
        if (!empty($context)) {
            $prompt .= "Bağlam: {$context}\n\n";
        }
        $prompt .= "Veriler:\n{$data}\n\n";
        $prompt .= "Lütfen bu verilerin kısa bir özetini oluştur.";

        return $this->ask($prompt, $systemInstruction);
    }

    /**
     * Doğal dil sorgusunu SQL'e çevirir (güvenli katman ile).
     *
     * @param string $userQuery    Kullanıcının doğal dil sorgusu
     * @param string $schemaInfo   Tablo/kolon bilgisi
     * @return string|false        Üretilen SQL veya false
     */
    public function textToSQL($userQuery, $schemaInfo)
    {
        $systemInstruction = "Sen bir SQL uzmanısın. "
            . "Sadece SELECT sorguları üret. INSERT, UPDATE, DELETE, DROP, ALTER, TRUNCATE gibi değiştirici komutlar ASLA üretme. "
            . "Sadece aşağıda belirtilen tablo ve kolonları kullan. "
            . "Sonuç olarak SADECE SQL sorgusunu döndür, başka hiçbir açıklama ekleme. "
            . "SQL sorgusunu ``` işaretleri OLMADAN düz metin olarak döndür. "
            . "Tarih alanları UNIX timestamp formatındadır, FROM_UNIXTIME() kullan.";

        $prompt = "Veritabanı şeması:\n{$schemaInfo}\n\n"
            . "Kullanıcı sorusu: {$userQuery}\n\n"
            . "Bu soruyu karşılayan SELECT SQL sorgusunu yaz.";

        return $this->ask($prompt, $systemInstruction);
    }

    /**
     * Son hata mesajını döndürür.
     *
     * @return string
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * API ayarlarını runtime'da değiştirmek için.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function setConfig($key, $value)
    {
        if (property_exists($this, $key)) {
            $this->$key = $value;
        }
    }

    /**
     * cURL ile HTTP POST isteği gönderir.
     *
     * @param string $url
     * @param array  $data
     * @return string|false
     */
    private function _httpPost($url, $data)
    {
        $jsonData = json_encode($data);

        if (function_exists('curl_init')) {
            return $this->_curlPost($url, $jsonData);
        }

        return $this->_streamPost($url, $jsonData);
    }

    /**
     * cURL tabanlı POST.
     */
    private function _curlPost($url, $jsonData)
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $jsonData,
            CURLOPT_HTTPHEADER     => array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData)
            ),
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            $this->lastError = 'HTTP isteği başarısız: ' . $error;
            return false;
        }

        if ($httpCode >= 400) {
            $decoded = json_decode($response);
            $this->lastError = isset($decoded->error->message)
                ? "HTTP {$httpCode}: " . $decoded->error->message
                : "HTTP {$httpCode}: Bilinmeyen hata.";
            return false;
        }

        return $response;
    }

    /**
     * file_get_contents tabanlı POST (cURL yoksa fallback).
     */
    private function _streamPost($url, $jsonData)
    {
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\n"
                    . "Content-Length: " . strlen($jsonData) . "\r\n",
                'content' => $jsonData,
                'timeout' => $this->timeout,
            ),
            'ssl' => array(
                'verify_peer' => true,
            ),
        );

        $context  = stream_context_create($options);
        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            $this->lastError = 'HTTP isteği başarısız (stream).';
            return false;
        }

        return $response;
    }
}
