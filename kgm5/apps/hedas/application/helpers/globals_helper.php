<?php

function FormSelectSorumluAvukatList(){
    $t = &get_instance();
    $t->load->model("dosya_model");
    
    $sql = "SELECT u_id,u_name,u_lastname,u_surname FROM r8t_users WHERE u_unit=2 AND u_statu=9 ORDER BY u_name,u_lastname,u_surname ASC";
    $items = $t->dosya_model->ek_query_all($sql);
    
    return $items;
}
function convertMahkemeIds($dmList,$txt=1) {
    $dmArr=explode("@",$dmList);
    $dtArr=array();
    foreach ($dmArr?$dmArr:array() as $kx=>$vx) {
        if (empty($vx)) continue;
        $dtArr[]=FormSelectMahkemeList("","",$vx);

    }
    if ($txt) {
        return implode(",",$dtArr);
    }

    return $dtArr;
}

function FormSelectMahkemeList($mahkemeId="",$idIndex=0,$mahkemeTxt=""){
    $t = &get_instance();
    $t->load->model("mahkemeler_model");
    if ($mahkemeTxt) {
        $mahkemeTxt0=str_replace("Mahkemesi","",$mahkemeTxt);
        $mahkemeTxt0=trim($mahkemeTxt0);
        $where=($mahkemeTxt)?" and (mh_name like '$mahkemeTxt' or mh_name like '$mahkemeTxt0%' ) ":"";
        $limit="limit 1";
    }
    else {
        $where=($mahkemeId)?" and mh_id in ($mahkemeId) ":"";
        $limit="";
    }
    
    $sql = "SELECT mh_id,mh_name FROM r8t_sys_mahkemeler WHERE mh_status=1 $where ORDER BY mh_name ASC $limit ";
    
    $items = $t->mahkemeler_model->ek_query_all($sql);

    if ($mahkemeTxt) {
        $mhNamex=(!empty($items[0]->mh_id))?$items[0]->mh_id:0;
    
        return $mhNamex;
    }

    $arr=array();
    $slcMahkeme="";
    foreach ($items as $k=>$v) {
        $v=(array)$v;
        $namex=trim($v["mh_name"]);
        $arr[$namex]=array("mh_id"=>$v["mh_id"],"mh_name"=>$namex);
        if ($mahkemeId==$v["mh_id"]) $slcMahkeme=$namex;
    }

    if ($mahkemeId) {
        return $slcMahkeme;
    }

    if ($idIndex) {
        $newArr=array();
        foreach ($arr as $k=>$v) {
            $newArr[$v["mh_id"]]=$v["mh_name"];
        }
        return $newArr;
    }
    
    return $arr;
}

function FormSelectSorumluMemurList(){
    $t = &get_instance();
    $t->load->model("dosya_model");
    
    $sql = "SELECT u_id,u_name,u_lastname,u_surname FROM r8t_users WHERE u_unit=2 AND u_statu=7 ORDER BY u_name,u_lastname,u_surname ASC";
    $items = $t->dosya_model->ek_query_all($sql);
    
    return $items;
}


function FormSelectSorumluMemurName(){
    $t = &get_instance();
    $t->load->model("dosya_model");
    $userB    = getUser()->userData->userB;
    
    $item = trim($userB->u_name . ' ' . $userB->u_lastname).' '.$userB->u_surname;
    return $item;
}

function dosyaIstatistikGetNamebyId($field,$dId) { 
    $t = get_instance();
    $t->load->model("dosya_model");
    
    $idArr=explode("|",$dId);
    $keyx=(!empty($idArr[1]))?$idArr[1]:0;
    
    $sql="select d_id,$field from r8t_edys_dosya 
    where d_id=$idArr[0]
       
    ";
    
    $data = $t->dosya_model->ek_query_all($sql);
    
    
    $outputX=(isset($data[0]->$field))?$data[0]->$field:"";
    
    $outputArr=explode("@",$outputX);
    
    $output=(isset($outputArr[$keyx]))?$outputArr[$keyx]:$outputX;
    $output=trim($output);
    return $output;

}

function dosyaIstatistikComboList($keyword,$field) {
    $t = get_instance();
    $t->load->model("dosya_model");

    if ($field=="dm_mahkeme")  {
        $sql="select $field from r8t_edys_dosya_mahkemeler
        where $field<>'' and $field<>'@' and $field like '%$keyword%'
        group by $field
        order by $field
        
        
        ";
        $data = $t->dosya_model->ek_query_all($sql);
    }
    else {
        $keyword1=strtolower($keyword);
        $keyword2=strtoupper($keyword);
        $sql="select d_id,$field from r8t_edys_dosya 
        where $field<>'' and $field<>'@' and ($field like '%$keyword%' or $field like '%$keyword1%' or $field like '%$keyword2%')
        group by $field
        order by $field
        
        
        ";
        
        //prp($sql);
        $data = $t->dosya_model->ek_query_all($sql);
    }

    $uniqArr=array();
    foreach ($data?$data:array() as $kx=>$vz) {
        $vx=(isset($vz->$field))?$vz->$field:"";
        $keyx=(isset($vz->d_id))?$vz->d_id:0;
        if (empty($vx) or empty($keyx)) continue;
        $delim="@";
        $vx=trim($vx);
        $subArr=explode($delim,$vx);
        foreach ($subArr as $subx=>$suby) {
            if (empty($suby)) continue;
            $suby=trim($suby);
            $suby=strip_tags(html_entity_decode($suby));
            $suby=trim($suby);
            $uniqArr[$suby]="$keyx|$subx";

        }
        
    }

    ksort($uniqArr);

    $output=array();
    foreach ($uniqArr?$uniqArr:array() as $kc=>$vc){
        if (!stristr($kc,$keyword)) continue;
        $output[]=array("id"=>$vc,"val"=>$kc);
    }
    
   
    return $output;

}

function prp($data="") {
    echo "<pre>";
    echo print_r($data,1);
    echo "</pre>";
}

function checkDosyaKurumNoVarmi($id) {
    $t = get_instance();
    $t->load->model("dosya_model");

    $sql = "SELECT d_id,d_kurumdosyano FROM r8t_edys_dosya where d_kurumdosyano='$id' ORDER BY d_id DESC LIMIT 1";
    $sonNumara = $t->dosya_model->ek_query($sql);
    
    
    if (!empty($sonNumara->d_kurumdosyano)) {
        return 1;
    }
    else
        return 0;
}