<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gelengiden_model extends RT_Model{
    private static $_indexesChecked = false;

    public function __construct(){
        parent::__construct();
        $this->tableName  = "r8t_edys_ggevrak";
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
                "SELECT COUNT(*) AS cnt FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'r8t_edys_ggevrak' AND INDEX_NAME = 'idx_ggevrak_status_adddate'",
                array($dbName)
            );
            if ($res && ($row = $res->row()) && (int)$row->cnt === 0) {
                $this->db->query("ALTER TABLE r8t_edys_ggevrak ADD INDEX idx_ggevrak_status_adddate (gg_status, gg_adddate)");
            }
        } catch (\Exception $e) {
        }
        $this->db->db_debug = $origDebug;
    }
    public function gelenGidenToplam()
    {
        $sqlx = "SELECT
            SUM(CASE WHEN gg_tur='1' THEN 1 ELSE 0 END) AS giden,
            SUM(CASE WHEN gg_tur='0' THEN 1 ELSE 0 END) AS gelen,
            SUM(CASE WHEN gg_tur='2' THEN 1 ELSE 0 END) AS genel,
            COUNT(*) AS toplam
            FROM r8t_edys_ggevrak WHERE gg_status = '1'";
        $row = $this->ek_query_all($sqlx);
        if (!$row) return array();

        $r = $row[0];
        $result = array();
        if ((int)$r->giden > 0) {
            $obj = new stdClass();
            $obj->durum = 'Giden Evrak';
            $obj->sayi = $r->giden;
            $obj->toplam = $r->toplam;
            $result[] = $obj;
        }
        if ((int)$r->gelen > 0) {
            $obj = new stdClass();
            $obj->durum = 'Gelen Evrak';
            $obj->sayi = $r->gelen;
            $obj->toplam = $r->toplam;
            $result[] = $obj;
        }
        if ((int)$r->genel > 0) {
            $obj = new stdClass();
            $obj->durum = 'Genel Evrak';
            $obj->sayi = $r->genel;
            $obj->toplam = $r->toplam;
            $result[] = $obj;
        }
        return $result;
    }
    public function ggSonKayit()
    {
        $ggsonkayit = $this->ek_query_all("SELECT gg_id, gg_tarih, gg_kaynak, gg_tur, gg_sayi, gg_dosyano, gg_kategori, gg_tags, gg_aciklama FROM r8t_edys_ggevrak 
        WHERE gg_status='1' 
        ORDER BY gg_id desc
        LIMIT 5;");
        return $ggsonkayit;
    }
    public function gelenGidenTum()
    {
        $gelengidentum = $this->ek_query_all("
        SELECT gg.gg_tur,
        gg.gg_kaynak,
        gg.gg_aciklama,
        gg.gg_sayi,
        gg.gg_dosyano,
        gg.gg_kategori,
        date(FROM_UNIXTIME(gg.gg_tarih)) as tarih,
        gg.gg_tarih as tarih2
        from r8t_edys_ggevrak gg 
        where gg.gg_status='1'
        AND gg.gg_tarih is not null
        order by MONTH(FROM_UNIXTIME(gg.gg_tarih))"
        );
        return $gelengidentum;
    }
}
