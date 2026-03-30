<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cezaiptal_model extends RT_Model
{
    private static $_indexesChecked = false;

    public function __construct()
    {
        parent::__construct();
        $this->tableName  = "r8t_edys_cezaiptal";
        $this->_ensureDbIndexes();
    }

    private function _ensureDbIndexes()
    {
        if (self::$_indexesChecked) return;
        self::$_indexesChecked = true;
        $origDebug = $this->db->db_debug;
        $this->db->db_debug = false;
        try {
            $dbName = $this->db->database;
            $res = $this->db->query(
                "SELECT COUNT(*) AS cnt FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'r8t_edys_cezaiptal' AND INDEX_NAME = 'idx_cezaiptal_kurumdosyano'",
                array($dbName)
            );
            if ($res && ($row = $res->row()) && (int)$row->cnt === 0) {
                $this->db->query("ALTER TABLE r8t_edys_cezaiptal ADD INDEX idx_cezaiptal_kurumdosyano (ci_kurumdosyano)");
            }
        } catch (\Exception $e) {
        }
        $this->db->db_debug = $origDebug;
    }
    public function dosyaNoOtomatik()
    {
        $dosyanootomatik = $this->ek_query_all("select max(ec.ci_kurumdosyano)+1 as dosyasira  from r8t_edys_cezaiptal ec;");
        return $dosyanootomatik;
    }
    public function cezaIptalToplam()
    {
        $cezaiptaltoplam = $this->ek_query_all("SELECT durum, sayi, (SELECT  COUNT(*)  FROM r8t_edys_cezaiptal WHERE ci_status = '1') AS toplam
        FROM (
          SELECT 'Yetkisizlik' AS durum, COUNT(*) AS sayi FROM r8t_edys_cezaiptal WHERE ci_status = '1' and ci_evrakdurum='0'
          UNION ALL
          SELECT 'Kabul' AS durum, COUNT(*) AS sayi FROM r8t_edys_cezaiptal WHERE ci_status = '1' and ci_evrakdurum='1'
          UNION ALL
          SELECT 'Red' AS durum, COUNT(*) AS sayi FROM r8t_edys_cezaiptal WHERE ci_status = '1' and ci_evrakdurum='2'
          UNION ALL
          SELECT 'Kısmi Kabul' AS durum, COUNT(*) AS sayi FROM r8t_edys_cezaiptal WHERE ci_status = '1' and ci_evrakdurum='3'
          UNION ALL
          SELECT 'Kısmi Red' AS durum, COUNT(*) AS sayi FROM r8t_edys_cezaiptal WHERE ci_status = '1' and ci_evrakdurum='4'
          UNION ALL
          SELECT 'Kısmi Kabul/Kısmi Red' AS durum, COUNT(*) AS sayi FROM r8t_edys_cezaiptal WHERE ci_status = '1' and ci_evrakdurum='5'
          UNION ALL
          SELECT 'Birleştirilmiş' AS durum, COUNT(*) AS sayi FROM r8t_edys_cezaiptal WHERE ci_status = '1' and ci_evrakdurum='6'
          UNION ALL
          SELECT 'Belirlenmemiş' AS durum, COUNT(*) AS sayi FROM r8t_edys_cezaiptal WHERE ci_status = '1' and ci_evrakdurum='7'
          ) AS t
            WHERE sayi != '0';");
        return $cezaiptaltoplam;
    }
    public function cezaSonKayit()
    {
        $cezasonkayit = $this->ek_query_all("SELECT * FROM r8t_edys_cezaiptal 
        WHERE ci_status='1' 
        ORDER BY ci_id desc
        LIMIT 5;");
        return $cezasonkayit;
    }
    public function CezaIptalTum()
    {
        $cezaiptaltum = $this->ek_query_all("
        SELECT
        ci.ci_acilistarih,
        ci.ci_cezakonu,
        ci.ci_kurumdosyano,
        ci.ci_itirazeden,
        ci.ci_davakonu,
        ci.ci_mahkeme,
        ci.ci_esasno,
        ci.ci_karartarih,
        ci.ci_plaka,
        ci.ci_cezaserino,
        ci.ci_evrakdurum
        FROM r8t_edys_cezaiptal ci 
        where ci.ci_status='1'"
        );
        return $cezaiptaltum;
    }
}