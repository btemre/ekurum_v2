<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * AI ayarları örnek dosyası.
 * Bu dosyayı ai_config.php olarak kopyalayıp API anahtarınızı ve limitleri girin.
 * ai_config.php .gitignore'da olduğu için versiyon kontrolüne eklenmez.
 */

$config['ai_gemini_api_key']     = '';           // Gemini API key
$config['ai_gemini_model']      = 'gemini-2.5-flash'; // veya gemini-2.5
$config['ai_gemini_endpoint']   = 'https://generativelanguage.googleapis.com/v1beta/models/';
$config['ai_max_tokens']        = 8000;
$config['ai_temperature']       = 0.7;
$config['ai_request_timeout']   = 120;  // YZ Ekürüm uzun yanıtlar için 120 önerilir; 503 alıyorsanız artırın

$config['ai_log_table']             = 'r8t_edts_ai_log';  // AI istek log tablosu
$config['ai_log_enabled']           = true;

// Kullanıcı başına günlük AI istek limiti (özet, haftalık özet, AI ile ara, YZ Ekürüm sohbet hepsi bu kotadan düşer)
// Limit dolunca "Günlük AI kullanım limitiniz dolmuştur" hatası alınır. Değeri artırarak günlük kullanımı yükseltebilirsiniz.
$config['ai_rate_limit_per_user']   = 100;
$config['ai_rate_limit_per_tenant'] = 5000;

// Aynı anda kaç sohbet isteği işlenebilir (process limiti aşımını önler; sunucu kapasitesine göre 3–10 arası önerilir)
$config['ai_max_concurrent_chat']   = 5;

// Text-to-SQL izinli / yasaklı tablolar (ai_helper.php içinde kullanılır)
$config['ai_allowed_tables']    = array('r8t_edts_durusmalar');
$config['ai_blocked_tables']    = array();
