<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dosya_model extends RT_Model
{
    private static $_indexesChecked = false;

    public function __construct()
    {
        parent::__construct();
        $this->tableName  = "r8t_edys_dosya";
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
                "SELECT COUNT(*) AS cnt FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'r8t_edys_dosya' AND INDEX_NAME = 'idx_dosya_status_adddate'",
                array($dbName)
            );
            if ($res && ($row = $res->row()) && (int)$row->cnt === 0) {
                $this->db->query("ALTER TABLE r8t_edys_dosya ADD INDEX idx_dosya_status_adddate (d_status, d_adddate, d_id)");
            }

            $res = $this->db->query(
                "SELECT COUNT(*) AS cnt FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'r8t_edys_dosya_mahkemeler' AND INDEX_NAME = 'idx_mahkeme_dosyaid'",
                array($dbName)
            );
            if ($res && ($row = $res->row()) && (int)$row->cnt === 0) {
                $this->db->query("ALTER TABLE r8t_edys_dosya_mahkemeler ADD INDEX idx_mahkeme_dosyaid (dm_dosyaid)");
            }

            $res = $this->db->query(
                "SELECT COUNT(*) AS cnt FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'r8t_edys_dosya' AND INDEX_NAME = 'idx_dosya_kurumdosyano'",
                array($dbName)
            );
            if ($res && ($row = $res->row()) && (int)$row->cnt === 0) {
                $this->db->query("ALTER TABLE r8t_edys_dosya ADD INDEX idx_dosya_kurumdosyano (d_kurumdosyano)");
            }
        } catch (\Exception $e) {
        }

        $this->db->db_debug = $origDebug;
    }
    public function dosyaToplam()
    {
        $sqlx = "SELECT
            SUM(CASE WHEN d_davaci LIKE '%KARAYOLLARI%' THEN 1 ELSE 0 END) AS kgm_davaci,
            SUM(CASE WHEN d_davali LIKE '%KARAYOLLARI%' THEN 1 ELSE 0 END) AS kgm_davali,
            SUM(CASE WHEN d_davaci NOT LIKE '%KARAYOLLARI%' THEN 1 ELSE 0 END) AS diger_davaci,
            SUM(CASE WHEN d_davali NOT LIKE '%KARAYOLLARI%' THEN 1 ELSE 0 END) AS diger_davali,
            COUNT(*) AS toplam
            FROM r8t_edys_dosya WHERE d_status = '1'";

        $row = $this->ek_query_all($sqlx);
        if (!$row) return array();

        $r = $row[0];
        $result = array();
        if ((int)$r->kgm_davaci > 0) {
            $obj = new stdClass();
            $obj->durum = 'KGM Davacı';
            $obj->sayi = $r->kgm_davaci;
            $obj->toplam = $r->toplam;
            $result[] = $obj;
        }
        if ((int)$r->kgm_davali > 0) {
            $obj = new stdClass();
            $obj->durum = 'KGM Davalı';
            $obj->sayi = $r->kgm_davali;
            $obj->toplam = $r->toplam;
            $result[] = $obj;
        }
        if ((int)$r->diger_davaci > 0) {
            $obj = new stdClass();
            $obj->durum = 'Diğer Davacı';
            $obj->sayi = $r->diger_davaci;
            $obj->toplam = $r->toplam;
            $result[] = $obj;
        }
        if ((int)$r->diger_davali > 0) {
            $obj = new stdClass();
            $obj->durum = 'Diğer Davalı';
            $obj->sayi = $r->diger_davali;
            $obj->toplam = $r->toplam;
            $result[] = $obj;
        }
        return $result;
    }
    public function dosyaSonKayit()
    {
        $sqlx="SELECT d_id, d_kurumdosyano, d_arsivno, d_davaci, d_davali, d_davakonusu, d_adddate FROM r8t_edys_dosya 
        WHERE d_status='1' 
        ORDER BY d_id desc
        LIMIT 5;";
        $dosyasonkayit = $this->ek_query_all($sqlx);
        return $dosyasonkayit;
    }

    public function dosyaDavaci($sdata="")
    {
        $mahkemeSql=$sdata[0];
        $otherSql=$sdata[1];
        $sqlx="select d_davaci davaci,count(*) sayi,(select count(*) from r8t_edys_dosya where d_status='1' $mahkemeSql) toplam from r8t_edys_dosya d 
				
        where d.d_status='1'
        $mahkemeSql
        $otherSql
        
        group by d_davaci
        order by sayi desc 
        limit 100";
        
        $dosyadavaci = $this->ek_query_all($sqlx);
        return $dosyadavaci;
    }

    public function dosyaDavali($sdata="")
    {
        $mahkemeSql=$sdata[0];
        $otherSql=$sdata[1];

        $sqlx="select d_davali davali,count(*) sayi,(select count(*) from r8t_edys_dosya where d_status='1' $mahkemeSql) toplam from r8t_edys_dosya d 
				
        where d.d_status='1'
        $mahkemeSql
        $otherSql
        
        group by d_davali
        order by sayi desc 
        limit 100";

        $dosyadavali = $this->ek_query_all($sqlx);
        return $dosyadavali;
    }
    public function dosyaMahkeme($sdata="")
    {
        $mahkemeSql=$sdata[0];
        $otherSql=$sdata[1];


        $sqlx="select dm_mahkeme mahkeme,count(*) sayi from r8t_edys_dosya d 
        left join r8t_edys_dosya_mahkemeler dm on d.d_id=dm.dm_dosyaid
                                
        where d.d_status='1' and dm_mahkeme<>''
        $mahkemeSql
        $otherSql

        group by dm_mahkeme
        order by sayi desc 
        limit 100";

        $dosyamahkeme = $this->ek_query_all($sqlx);
        
        return $dosyamahkeme;
    } 
    
    public function dosyaMahkemeTotal($sdata="")
    {
        $mahkemeSql=$sdata[0];
        $otherSql=$sdata[1];


        $sqlx="select dm_mahkeme mahkeme,count(*) sayi from r8t_edys_dosya d 
        left join r8t_edys_dosya_mahkemeler dm on d.d_id=dm.dm_dosyaid
                                
        where d.d_status='1'
        $mahkemeSql
        $otherSql

        group by dm_mahkeme
        order by sayi desc 
        ";
        
        $dosyamahkeme = $this->ek_query_all($sqlx);
        $totx=0;
        foreach (is_array($dosyamahkeme) ? $dosyamahkeme : array() as $kx=>$vx) {
            $totx+=$vx->sayi;
        }
        
        
        return $totx;
    }     
    

    
    public function dosyaDavaAciklama($sdata="")
    {
        $mahkemeSql=$sdata[0];
        $otherSql=$sdata[1];



        $sqlx="select d_davakonuaciklama aciklama,count(*) sayi,(select count(*) from r8t_edys_dosya where d_status='1' $mahkemeSql) toplam from r8t_edys_dosya d 
                        
        where d.d_status='1'
        $mahkemeSql
        $otherSql

        group by d_davakonuaciklama
        order by sayi desc 
        limit 100";
        $dosyadavaaciklama = $this->ek_query_all($sqlx);
        return $dosyadavaaciklama;
    }
    


    public function dosyaProje($sdata="")
    {
        $mahkemeSql=$sdata[0];
        $otherSql=$sdata[1];



        $sqlx="select d_projebilgisi aciklama,count(*) sayi,(select count(*) from r8t_edys_dosya where d_status='1' $mahkemeSql) toplam from r8t_edys_dosya d 
                        
        where d.d_status='1'
        $mahkemeSql
        $otherSql

        group by d_projebilgisi
        order by sayi desc 
        limit 100";
        $dosyadavaaciklama = $this->ek_query_all($sqlx);
        return $dosyadavaaciklama;
    }


    public function dosyaMevki($sdata="")
    {
        $mahkemeSql=$sdata[0];
        $otherSql=$sdata[1];



        $sqlx="select d_mevkiplaka aciklama,count(*) sayi,(select count(*) from r8t_edys_dosya where d_status='1' $mahkemeSql) toplam from r8t_edys_dosya d 
                        
        where d.d_status='1'
        $mahkemeSql
        $otherSql

        group by d_mevkiplaka
        order by sayi desc 
        limit 100";
        $dosyadavaaciklama = $this->ek_query_all($sqlx);
        return $dosyadavaaciklama;
    }

    public function getAutoFiltre($sdata) {
        $viewData=(array)$sdata;
        $search=$viewData["search"];
        $tarihStart=$viewData["current_durusma_start"];
        $tarihEnd=$viewData["current_durusma_end"];
        $dosyaStats=$viewData["dosyaStats"];
      
        
        $startD    = dateToTimeFormat($tarihStart." 00:00:00", "d-m-Y H:i:s");
        $endD     = dateToTimeFormat($tarihEnd." 23:59:59", "d-m-Y H:i:s");

        
        $filterMahkemeSelect=trim($viewData["filterMahkemeSelect"]);


        $sql0=" and (d_adddate between '$startD' and '$endD') ";
        if ($filterMahkemeSelect!="") {

            $filterArrList=explode(",",$filterMahkemeSelect);
            
            $totMahx=0;
            $dmMahkemeTxt="";
            foreach (is_array($filterArrList) ? $filterArrList : array() as $fk=>$fv) {

                $mahkeme=FormSelectMahkemeList($fv);
                if ($mahkeme) {
                    $orx=($totMahx>0)?" or ":"";
                    $mahkeme=str_replace("Mahkemesi","",$mahkeme);
                    $mahkeme=trim($mahkeme);
                    $dmMahkemeTxt.=$orx." dm_mahkeme like '%$mahkeme%' ";
                    $totMahx++;
                    
                }
            }            
            $sql0.="and d_id in (select dm_dosyaid from r8t_edys_dosya_mahkemeler where $dmMahkemeTxt)";
        }
                                      
        $sql="";
        foreach (is_array($dosyaStats) ? $dosyaStats : array() as $kDosya=>$vDosya) {

            $filterVal=$this->input->get("filtre_".$kDosya);

            $filterVal=(!empty($filterVal) and $filterVal!="null" and $filterVal!="-1" and $filterVal!="Hepsi")?$filterVal:"";
            
            if ($filterVal) {
                $sql.=" and ($kDosya like '%$filterVal%') ";
            }        

        }

        $sqlArr=array($sql0,$sql);
        

        return $sqlArr;
    }

    
    /* ETDS FONKSİYONLARI SİLİNECEK */

    



}
