<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Durusmalar_model extends RT_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->tableName  = "r8t_edts_durusmalar";
    }


    public function durusmaAvukatBazli($sdata="")
    {
        
        $sqlx="SELECT du.d_avukat,
        count(du.d_avukatid) as sayi,
        ROUND(count(du.d_avukatid)/(select count(d.d_avukatid) from r8t_edts_durusmalar d
        WHERE d.d_status='1' $sdata)*100,1) as yuzde 
        from r8t_edts_durusmalar du 
        where 
        du.d_status='1'
        and du.d_avukatid<>'' $sdata
        group by du.d_avukatid
        order by sayi desc";
        
        $durusmaavukatbazli = $this->ek_query_all($sqlx);
        return $durusmaavukatbazli;
    }

    public function durusmaMemurBazli($sdata="")
    {
        $sqlx="SELECT du.d_memur,
        count(du.d_memurid) as sayi,
        ROUND(count(du.d_memurid)/(select count(d.d_memurid) from r8t_edts_durusmalar d
        WHERE d.d_status='1'
        $sdata)*100,1) as yuzde 
        from r8t_edts_durusmalar du 
        where 
        du.d_status='1' $sdata 
        group by du.d_memurid
        order by sayi desc";
        $durusmamemurbazli = $this->ek_query_all($sqlx);

        return $durusmamemurbazli;
    }

    public function durusmaTarafBazli($sdata="")
    {
        $sqlx="SELECT du.d_taraf,
        count(du.d_taraf) as sayi,
        ROUND(count(du.d_taraf)/(select count(d.d_taraf) from r8t_edts_durusmalar d
        WHERE d.d_status='1'
        and d.d_taraf <> ''
        $sdata)*100,1) as yuzde 
        from r8t_edts_durusmalar du 
        where du.d_status='1'
        and du.d_taraf <> '' $sdata
        group by du.d_taraf
        order by sayi desc";
        $durusmatarafbazli = $this->ek_query_all($sqlx);
        //echo $sqlx;
        return $durusmatarafbazli;
    }

    public function durusmaMahkemeBazli($sdata="")
    {
        $sqlx="SELECT du.d_mahkeme,
        count(du.d_mahkeme) as sayi,
        ROUND(count(du.d_mahkeme)/(select count(d.d_mahkeme) from r8t_edts_durusmalar d
        WHERE d.d_status='1'
         $sdata)*100,1) as yuzde 
        from r8t_edts_durusmalar du 
        where du.d_status='1' $sdata
        group by du.d_mahkeme
        order by sayi desc
        limit 5";
        $durusmamahkemebazli = $this->ek_query_all($sqlx);
        //echo $sqlx;
        return $durusmamahkemebazli;
    }

    public function durusmaIslemBazli($sdata="")
    {
        $sqlx="SELECT du.d_islem,
        count(du.d_islem) as sayi,
        ROUND(count(du.d_islem)/(select count(d.d_islem) from r8t_edts_durusmalar d
        WHERE d.d_status='1'
        $sdata)*100,1) as yuzde 
        from r8t_edts_durusmalar du 
        where du.d_status='1'
        and du.d_islem <> '' $sdata
        group by du.d_islem
        order by sayi desc";
        //echo $sqlx;
        $durusmaislembazli = $this->ek_query_all($sqlx);
        
        return $durusmaislembazli;
    }

    public function durusmaListesiHaftalik($sdata="")
    {
        $sqlx="SELECT du.d_dosyano,du.d_dosyaturu,du.d_mahkeme,date(FROM_UNIXTIME(du.d_durusmatarihi)) as tarih,
        du.d_esasno,du.d_taraf,du.d_islem,du.d_memur,du.d_avukat
        from r8t_edts_durusmalar du 
        where YEAR(FROM_UNIXTIME(du.d_durusmatarihi))=YEAR(curdate())
        and du.d_status='1'
        and YEARWEEK(FROM_UNIXTIME(du.d_durusmatarihi))=YEARWEEK(CURRENT_DATE)
        order by tarih desc";
        
        $durusmalistesihaftalik = $this->ek_query_all($sqlx);
        return $durusmalistesihaftalik;
    }

    public function durusmaListesiAylik($sdata="")
    {
        $sqlx="SELECT du.d_islem as islem,
        count(du.d_islem) as sayi,
        MONTHNAME(FROM_UNIXTIME(du.d_durusmatarihi)) as ay,
        MONTH(FROM_UNIXTIME(du.d_durusmatarihi)) as ayid,
        YEAR(FROM_UNIXTIME(du.d_durusmatarihi)) as yil
        
        from r8t_edts_durusmalar du 
        where du.d_status='1'
        AND du.d_islem like '%DURUŞMA%' $sdata
        group by MONTH(FROM_UNIXTIME(du.d_durusmatarihi))
        order by MONTH(FROM_UNIXTIME(du.d_durusmatarihi))";
        //prp($sqlx);
        $durusmalistesiaylik = $this->ek_query_all($sqlx);
        return $durusmalistesiaylik;
    }

    public function kararListesiAylik($sdata="")
    {
        $sqlx="SELECT du.d_islem as islem,
        count(du.d_islem) as sayi,
        MONTHNAME(FROM_UNIXTIME(du.d_durusmatarihi)) as ay,
        MONTH(FROM_UNIXTIME(du.d_durusmatarihi)) as ayid,
        YEAR(FROM_UNIXTIME(du.d_durusmatarihi)) as yil
        from r8t_edts_durusmalar du 
        where du.d_status='1'
        AND du.d_islem like '%karar%' $sdata
        group by MONTH(FROM_UNIXTIME(du.d_durusmatarihi))
        order by MONTH(FROM_UNIXTIME(du.d_durusmatarihi))";
        $kararlistesiaylik = $this->ek_query_all($sqlx);
        //prp($sqlx);
        return $kararlistesiaylik;
    }
    public function durusmalarTum()
    {
        $durusmalartum = $this->ek_query_all("SELECT du.d_esasno,
        du.d_mahkeme,
        du.d_dosyano,
        du.d_durusmatarihi,
        du.d_avukat,
        du.d_memur,
        du.d_dosyaturu,
        du.d_taraf,
        du.d_islem,
        du.d_tarafbilgisi
        FROM r8t_edts_durusmalar du 
        WHERE du.d_status='1'
        ORDER BY du.d_durusmatarihi DESC"
        );
        return $durusmalartum;
    }

    public function getAutoFiltre($sdata) {
        $viewData=(array)$sdata;
        $search=$viewData["search"];
        $tarihStart=$viewData["current_durusma_start"];
        $tarihEnd=$viewData["current_durusma_end"];
      
        
        $startD    = dateToTimeFormat($tarihStart." 00:00:00", "d-m-Y H:i:s");
        $endD     = dateToTimeFormat($tarihEnd." 23:59:59", "d-m-Y H:i:s");

        
        $filterMahkemeSelect=trim($viewData["filterMahkemeSelect"] ?? '');
        $filterMemurSelect=trim($viewData["filterMemurSelect"] ?? '');
        $filterAvukatSelect=trim($viewData["filterAvukatSelect"] ?? '');
        $filterIslemSelect=trim($viewData["filterIslemSelect"] ?? '');
        $dlara_taraf=trim($viewData["dlara_taraf"] ?? '');
        $dlara_dtakip=trim($viewData["dlara_dtakip"] ?? '');

        $sql=" and (d_durusmatarihi between '$startD' and '$endD') ";
     

        if ($filterMahkemeSelect!="") {

            $filterArrList=explode(",",$filterMahkemeSelect);
            
            $totMahx=0;
            $dmMahkemeTxt="";
            foreach ($filterArrList?$filterArrList:array() as $fk=>$fv) {

                $mahkeme=FormSelectMahkemeList($fv);
                if ($mahkeme) {
                    $orx=($totMahx>0)?" or ":"";
                    $mahkeme=str_replace("Mahkemesi","",$mahkeme);
                    $mahkeme=trim($mahkeme);
                    $dmMahkemeTxt.=$orx." d_mahkeme like '%$mahkeme%' ";
                    $totMahx++;
                    
                }
            }            
            $sql.="and ($dmMahkemeTxt)";
        }

        if ($filterMemurSelect>0) {
            $sql.=" and (d_memurid in ($filterMemurSelect)) ";
        }
        if ($filterAvukatSelect>0) {
            $sql.=" and (d_avukatid in ($filterAvukatSelect)) ";
        }
        if ($filterIslemSelect!="" and $filterIslemSelect!="-1") {

            $filterIslemSelectArr=explode(",",$filterIslemSelect);
            $innerFilterIslem="";
            $orx="";
            $totx=0;
            foreach ($filterIslemSelectArr as $fx0=>$fv0) {
                $orx=($totx>0)?" or ":"";
                $innerFilterIslem.=$orx." d_islem like '%$fv0%'";
                $totx++;
            }
            $sql.=" and ($innerFilterIslem) ";
            
        }


        if ($dlara_taraf!="" and $dlara_taraf!="-1") {

            $dlara_tarafArr=explode(",",$dlara_taraf);
            $innerFilterTaraf="";
            $orx="";
            $totx=0;
            foreach ($dlara_tarafArr as $fx0=>$fv0) {
                $orx=($totx>0)?" or ":"";
                $innerFilterTaraf.=$orx." d_taraf like '%$fv0%'";
                $totx++;
            }
            $sql.=" and ($innerFilterTaraf) ";
            
        }
    
                                

        return $sql;
    }


}


