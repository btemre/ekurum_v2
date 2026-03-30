<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Önbellek ve rate limit: api_list / api_mahkemelist gibi ağır endpoint'lerde
 * PHP işlem sayısını ve 503 hatalarını azaltmak için kullanılır.
 */

/** Varsayılan cache TTL (saniye). Aynı parametrelerle tekrar istek cache'den döner. */
if (!function_exists('api_list_cache_ttl')) {
    function api_list_cache_ttl() {
        return defined('API_LIST_CACHE_TTL') ? (int) API_LIST_CACHE_TTL : 45;
    }
}

/** Dakika başına maksimum istek (kullanıcı başına, endpoint başına). */
if (!function_exists('api_list_rate_limit_max')) {
    function api_list_rate_limit_max() {
        return defined('API_LIST_RATE_LIMIT_MAX') ? (int) API_LIST_RATE_LIMIT_MAX : 40;
    }
}

/** Cache dosyalarının yazılacağı dizin. */
if (!function_exists('api_list_cache_dir')) {
    function api_list_cache_dir() {
        $base = defined('APPPATH') && APPPATH ? rtrim(APPPATH, DIRECTORY_SEPARATOR) : sys_get_temp_dir();
        $dir = $base . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'api_list';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        return $dir;
    }
}

/**
 * İstek için cache anahtarı üretir (kullanıcı + POST gövdesi).
 * @param string $prefix Örn. 'edts_list', 'hedas_list', 'hedas_mahkemelist'
 * @param int $userId
 * @param array|object $postData
 * @return string
 */
if (!function_exists('api_list_cache_key')) {
    function api_list_cache_key($prefix, $userId, $postData) {
        $arr = is_object($postData) ? (array) $postData : $postData;
        ksort($arr);
        $raw = $prefix . '_' . (int) $userId . '_' . json_encode($arr);
        return md5($raw);
    }
}

/**
 * Cache'den yanıt getirir. Süre dolmuşsa false döner.
 * @param string $key api_list_cache_key() çıktısı
 * @param int $ttlSec saniye
 * @return string|false JSON yanıt veya false
 */
if (!function_exists('api_list_cache_get')) {
    function api_list_cache_get($key, $ttlSec = null) {
        $ttlSec = $ttlSec !== null ? (int) $ttlSec : api_list_cache_ttl();
        $dir = api_list_cache_dir();
        $file = $dir . DIRECTORY_SEPARATOR . 'c_' . $key;
        if (!is_file($file)) {
            return false;
        }
        if (filemtime($file) + $ttlSec < time()) {
            @unlink($file);
            return false;
        }
        $body = @file_get_contents($file);
        return $body !== false ? $body : false;
    }
}

/**
 * Yanıtı cache'e yazar.
 * @param string $key
 * @param string $body JSON yanıt
 * @param int|null $ttlSec
 */
if (!function_exists('api_list_cache_set')) {
    function api_list_cache_set($key, $body, $ttlSec = null) {
        $dir = api_list_cache_dir();
        $file = $dir . DIRECTORY_SEPARATOR . 'c_' . $key;
        @file_put_contents($file, $body, LOCK_EX);
    }
}

/**
 * Rate limit kontrolü. Dakika başına istek sayısı aşılıyorsa false döner.
 * @param string $prefix Örn. 'edts_list'
 * @param int $userId
 * @param int|null $maxPerMinute
 * @param int $windowSec pencere süresi (saniye)
 * @return bool true = istek kabul, false = limit aşıldı
 */
if (!function_exists('api_list_rate_limit_allowed')) {
    function api_list_rate_limit_allowed($prefix, $userId, $maxPerMinute = null, $windowSec = 60) {
        $maxPerMinute = $maxPerMinute !== null ? (int) $maxPerMinute : api_list_rate_limit_max();
        $dir = api_list_cache_dir();
        $file = $dir . DIRECTORY_SEPARATOR . 'rl_' . $prefix . '_' . (int) $userId;
        $now = time();
        $data = array('ts' => $now, 'count' => 1);
        if (is_file($file)) {
            $raw = @file_get_contents($file);
            if ($raw !== false) {
                $dec = @json_decode($raw, true);
                if (is_array($dec) && isset($dec['ts'], $dec['count'])) {
                    if ($now - $dec['ts'] >= $windowSec) {
                        $data = array('ts' => $now, 'count' => 1);
                    } else {
                        $data['ts'] = $dec['ts'];
                        $data['count'] = $dec['count'] + 1;
                    }
                }
            }
        }
        if ($data['count'] > $maxPerMinute) {
            return false;
        }
        @file_put_contents($file, json_encode($data), LOCK_EX);
        return true;
    }
}

/**
 * Rate limit aşıldığında 429 döndürür; Retry-After header ile.
 */
if (!function_exists('api_list_send_rate_limit_response')) {
    function api_list_send_rate_limit_response() {
        if (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        header('Retry-After: 60');
        http_response_code(429);
        echo json_encode(array(
            'success' => false,
            'code' => 429,
            'description' => 'Çok fazla istek gönderildi. Lütfen bir dakika bekleyip tekrar deneyin.',
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => array()
        ), JSON_UNESCAPED_UNICODE);
        exit;
    }
}
