<?php
defined('BASEPATH') or exit('No direct script access allowed');


/**
 * KVKK uyumluluğu için hassas alanları maskeler.
 *
 * @param string $text     Maskelenecek metin
 * @return string          Maskelenmiş metin
 */
function aiMaskSensitiveData($text)
{
    $masked = $text;

    // TC Kimlik (11 haneli rakam)
    $masked = preg_replace('/\b[1-9]\d{10}\b/', '***TC_MASKED***', $masked);

    // Telefon numaraları
    $masked = preg_replace('/(\+?9?0?\s?)?(\(?\d{3}\)?\s?\d{3}\s?\d{2}\s?\d{2})/', '***TEL_MASKED***', $masked);

    // E-posta adresleri
    $masked = preg_replace('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', '***EMAIL_MASKED***', $masked);

    // IBAN
    $masked = preg_replace('/TR\d{2}\s?\d{4}\s?\d{4}\s?\d{4}\s?\d{4}\s?\d{4}\s?\d{2}/', '***IBAN_MASKED***', $masked);

    return $masked;
}


/**
 * Veritabanı şema bilgisini EDTS için döndürür (Text-to-SQL için).
 *
 * @return string
 */
function aiGetSchemaEDTS()
{
    $schema = "Tablo: r8t_edts_durusmalar (Duruşma Takip Sistemi)\n"
        . "Kolonlar:\n"
        . "  d_id          - INT, Primary Key, benzersiz kayıt numarası\n"
        . "  d_esasno      - VARCHAR, esas numarası (mahkeme dosya numarası)\n"
        . "  d_mahkeme     - VARCHAR, mahkeme adı\n"
        . "  d_dosyano     - VARCHAR, kurum dosya numarası\n"
        . "  d_durusmatarihi - INT, duruşma tarihi (UNIX timestamp, FROM_UNIXTIME() ile tarih formatına çevir)\n"
        . "  d_avukat      - VARCHAR, avukat adı soyadı\n"
        . "  d_avukatid    - INT, avukat ID\n"
        . "  d_memur       - VARCHAR, memur adı soyadı\n"
        . "  d_memurid     - INT, memur ID\n"
        . "  d_dosyaturu   - VARCHAR, dosya türü\n"
        . "  d_taraf       - VARCHAR, taraf bilgisi (davacı/davalı)\n"
        . "  d_islem       - VARCHAR, işlem türü (DURUŞMA, KARAR vb.)\n"
        . "  d_tarafbilgisi - TEXT, detaylı taraf bilgisi\n"
        . "  d_takip       - VARCHAR, takip bilgisi\n"
        . "  d_tutanak     - TEXT, tutanak bilgisi\n"
        . "  d_tags        - VARCHAR, etiketler\n"
        . "  d_status      - INT, durum (1=aktif, 0=pasif, -1=arşiv)\n"
        . "  d_adddate     - INT, kayıt tarihi (UNIX timestamp)\n"
        . "\nÖnemli: d_status=1 olan kayıtlar aktif kayıtlardır. Tarih alanları UNIX timestamp formatındadır.\n"
        . "Zaman dilimi: Tüm tarihler Türkiye saatine (Europe/Istanbul, UTC+3) göre değerlendirilmelidir. "
        . "MySQL oturumu +03:00 timezone ile çalışmaktadır, FROM_UNIXTIME() zaten Türkiye saatini döndürür.\n";

    return $schema;
}


/**
 * Veritabanı şema bilgisini HEDAS için döndürür (Text-to-SQL için).
 *
 * @return string
 */
function aiGetSchemaHEDAS()
{
    $schema = "Tablo: r8t_edys_dosya (Dosya/Evrak Yönetim Sistemi)\n"
        . "Kolonlar:\n"
        . "  d_id              - INT, Primary Key\n"
        . "  d_kurumdosyano    - VARCHAR, kurum dosya numarası\n"
        . "  d_davaci          - VARCHAR, davacı\n"
        . "  d_davali          - VARCHAR, davalı\n"
        . "  d_davakonusu      - VARCHAR, dava konusu\n"
        . "  d_davakonuaciklama - TEXT, dava konusu açıklaması\n"
        . "  d_mevkiplaka      - VARCHAR, mevki/plaka bilgisi\n"
        . "  d_projebilgisi    - VARCHAR, proje bilgisi\n"
        . "  d_icra            - VARCHAR, icra bilgisi\n"
        . "  d_temyiz          - VARCHAR, temyiz durumu\n"
        . "  d_istinaftemyiz   - VARCHAR, istinaf temyiz\n"
        . "  d_istinafkabul    - VARCHAR, istinaf kabul\n"
        . "  d_istinafred      - VARCHAR, istinaf red\n"
        . "  d_bozmailami      - VARCHAR, bozma ilamı\n"
        . "  d_onamailami      - VARCHAR, onama ilamı\n"
        . "  d_tags            - VARCHAR, etiketler\n"
        . "  d_status          - INT, durum (1=aktif, 0=pasif, -1=arşiv)\n"
        . "  d_adddate         - INT, kayıt tarihi (UNIX timestamp)\n"
        . "\nTablo: r8t_edys_dosya_mahkemeler (Dosya ile ilişkili mahkeme bilgileri)\n"
        . "Kolonlar:\n"
        . "  dm_id           - INT, Primary Key\n"
        . "  dm_dosyaid      - INT, r8t_edys_dosya tablosundaki d_id ile ilişkili (Foreign Key)\n"
        . "  dm_acilistarihi - VARCHAR, açılış tarihi\n"
        . "  dm_esasno       - VARCHAR, esas numarası\n"
        . "  dm_karartarihi  - VARCHAR, karar tarihi\n"
        . "  dm_kararno      - VARCHAR, karar numarası\n"
        . "  dm_mahkeme      - VARCHAR, mahkeme adı\n"
        . "  dm_aciklama     - TEXT, açıklama\n"
        . "\nÖnemli: d_status=1 olan kayıtlar aktif kayıtlardır.\n"
        . "JOIN örneği: r8t_edys_dosya d INNER JOIN r8t_edys_dosya_mahkemeler dm ON d.d_id = dm.dm_dosyaid\n";

    return $schema;
}


/**
 * AI Text-to-SQL çıktısını güvenlik için doğrular.
 * Ekürüm ve Yapay Zeka'ya sor kapsamında kesinlikle toplu/tekil oluşturma, silme, güncelleme yasaktır.
 * Sadece SELECT sorgularına izin verilir.
 *
 * @param string $sql  Doğrulanacak SQL
 * @return array       ['valid' => bool, 'message' => string, 'sql' => string]
 */
function aiValidateSQL($sql)
{
    $result = array(
        'valid'   => false,
        'message' => '',
        'sql'     => ''
    );

    $cleanSQL = trim($sql);

    // ``` işaretlerini temizle
    $cleanSQL = preg_replace('/^```(sql)?/i', '', $cleanSQL);
    $cleanSQL = preg_replace('/```$/', '', $cleanSQL);
    $cleanSQL = trim($cleanSQL);

    if (empty($cleanSQL)) {
        $result['message'] = 'SQL sorgusu boş.';
        return $result;
    }

    $upperSQL = strtoupper($cleanSQL);

    // Ekürüm / Yapay Zeka: Oluşturma, Silme, Güncelleme (toplu veya tekil) kesinlikle yasak
    $writeKeywords = array('INSERT', 'UPDATE', 'DELETE', 'CREATE', 'DROP', 'ALTER', 'TRUNCATE', 'REPLACE', 'GRANT', 'REVOKE', 'EXEC', 'EXECUTE', 'CALL', 'LOCK', 'UNLOCK');
    foreach ($writeKeywords as $kw) {
        if (preg_match('/\b' . preg_quote($kw, '/') . '\b/', $upperSQL)) {
            $result['message'] = 'Ekürüm ve Yapay Zeka\'ya sor kapsamında oluşturma, silme veya güncelleme işlemlerine (toplu veya tekil) izin verilmemektedir. Yalnızca okuma (SELECT) sorguları çalıştırılabilir.';
            return $result;
        }
    }

    // SELECT ile başlamalı
    if (strpos($upperSQL, 'SELECT') !== 0) {
        $result['message'] = 'Yalnızca SELECT sorguları çalıştırılabilir.';
        return $result;
    }

    $blocked = array(
        'INSERT ', 'UPDATE ', 'DELETE ', 'DROP ', 'ALTER ', 'TRUNCATE ',
        'CREATE ', 'REPLACE ', 'GRANT ', 'REVOKE ', 'EXEC ', 'EXECUTE ',
        'INTO OUTFILE', 'INTO DUMPFILE', 'LOAD_FILE', 'BENCHMARK(',
        'SLEEP(', 'INFORMATION_SCHEMA', 'MYSQL.', 'PERFORMANCE_SCHEMA'
    );

    foreach ($blocked as $keyword) {
        if (strpos($upperSQL, $keyword) !== false) {
            $result['message'] = 'Ekürüm ve Yapay Zeka\'ya sor kapsamında oluşturma, silme veya güncelleme işlemlerine izin verilmemektedir.';
            return $result;
        }
    }

    // Birden fazla sorgu engelle
    $noStrings = preg_replace("/'[^']*'/", '', $cleanSQL);
    $noStrings = preg_replace('/"[^"]*"/', '', $noStrings);
    if (substr_count($noStrings, ';') > 1) {
        $result['message'] = 'Birden fazla sorgu çalıştırılamaz.';
        return $result;
    }
    $cleanSQL = rtrim($cleanSQL, ';');

    // İzin verilen tablo kontrolü
    $t = &get_instance();
    $t->config->load('ai_config', TRUE);
    $allowedTables = $t->config->item('ai_allowed_tables', 'ai_config');
    $blockedTables = $t->config->item('ai_blocked_tables', 'ai_config');

    foreach ($blockedTables as $table) {
        if (stripos($cleanSQL, $table) !== false) {
            $result['message'] = 'Bu tabloya erişim yetkisi bulunmuyor.';
            return $result;
        }
    }

    // LIMIT yoksa ekle (maks 500 satır)
    if (stripos($upperSQL, 'LIMIT') === false) {
        $cleanSQL .= ' LIMIT 500';
    }

    $result['valid']   = true;
    $result['message'] = 'SQL doğrulandı.';
    $result['sql']     = $cleanSQL;

    return $result;
}


/**
 * AI isteğini loglamak için standart log verisi oluşturur.
 *
 * @param int    $userId       Kullanıcı ID
 * @param string $app          Uygulama kodu (edts, hedas)
 * @param string $requestType  İstek türü (summary, text_to_sql, document_summary, prediction)
 * @param string $prompt       Prompt metni (hash'lenecek)
 * @param string $response     Yanıt metni
 * @param string $status       Durum (success, error)
 * @param string $errorMsg     Hata mesajı (opsiyonel)
 * @return array
 */
function aiBuildLogData($userId, $app, $requestType, $prompt, $response = '', $status = 'success', $errorMsg = '')
{
    return array(
        'al_user_id'      => (int) $userId,
        'al_app'          => $app,
        'al_request_type' => $requestType,
        'al_prompt_hash'  => md5($prompt),
        'al_response_len' => strlen($response),
        'al_status'       => $status,
        'al_error_msg'    => $errorMsg,
        'al_adddate'      => time(),
    );
}


/**
 * AI API yanıt formatını oluşturur (mevcut projenin standart response yapısına uygun).
 *
 * @param bool   $success
 * @param int    $code
 * @param string $description
 * @param mixed  $data
 * @return object
 */
function aiResponse($success, $code, $description, $data = null)
{
    $_sonuc = new stdClass();
    $_sonuc->success     = $success;
    $_sonuc->code        = $code;
    $_sonuc->description = $description;
    $_sonuc->data        = $data;
    return $_sonuc;
}
