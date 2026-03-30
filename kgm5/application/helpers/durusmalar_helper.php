<?php
defined('BASEPATH') or exit('No direct script access allowed');


function _drsGetAllTagsCache()
{
    static $cache = null;
    if ($cache === null) {
        $t = &get_instance();
        $t->load->model("durusmalar_model");
        $allTags = $t->durusmalar_model->ek_query_all("SELECT tag_isim, tag_color FROM r8t_edts_tags");
        $cache = array();
        if ($allTags) {
            foreach ($allTags as $tag) {
                $cache[$tag->tag_isim] = $tag->tag_color;
            }
        }
    }
    return $cache;
}

function drsTagYazdir($item = false)
{
    $_tagText = "";
    if ($item != false) {
        $tagCache = _drsGetAllTagsCache();
        $_texts = @explode("@", $item);
        foreach ($_texts as $text) {
            $text = trim($text);
            if (strlen($text) > 0 && isset($tagCache[$text])) {
                $_tagText .= '<div class="badge badge-' . $tagCache[$text] . ' fw-bolder m-1">' . $text . '</div>';
            }
        }
    }
    return $_tagText;
}


function drsEditTagYazdir($item = false)
{
    //return $item;
    $t = &get_instance();
    $t->load->model("durusmalar_model");
    $_tagText = "";
    if ($item != false) {
        $_texts = @explode("@", $item);
        foreach ($_texts as $text) {
            $isTag = $t->durusmalar_model->ek_get(
                "r8t_edts_tags",
                array(
                    "tag_isim"  => $text
                )
            );
            if ($isTag) {
                $_tagText .= $isTag->tag_isim . ',';
            }
        }
    }
    return $_tagText;
}


function drsAyracsizYazdir($item = false)
{
    $_text = "";
    if ($item != false) {
        $_texts = @explode("@", $item);
        for ($i = 0; $i <= count($_texts) - 2; $i++) {
            if (stripos($_text, trim($_texts[$i])) < 1) {
                if ($i == 0) {
                    $_text .= trim($_texts[$i]);
                } elseif ($i > 0 && $i != (count($_texts) - 2)) {
                    $_text .= " " . trim($_texts[$i]);
                } else {
                    $_text .= " " . trim($_texts[$i]);
                }
            }
        }
    }
    $_text = str_replace(',,', ',', $_text);
    return $_text;
}

function drsEditAyracliYazdir($item = false)
{
    $_text = "";
    if ($item != false) {
        $_texts = @explode("@", $item);
        for ($i = 0; $i <= count($_texts) - 2; $i++) {
            if (stripos($_text, trim($_texts[$i])) < 1) {
                if ($i == 0) {
                    $_text .= $_texts[$i];
                } elseif ($i > 0 && $i != (count($_texts) - 2)) {
                    $_text .= "," . $_texts[$i];
                } else {
                    $_text .= "," . $_texts[$i];
                }
            }
        }
    }
    $_text = str_replace(',,', ',', $_text);
    return $_text;
}


function convertMahkemeTextToId($data = false)
{
    $mahkemeList=FormSelectMahkemeList(0);
	$data=explode("@",$data);
    $output=array();
    foreach ($data?$data:array() as $k=>$v) {
		if (empty($v)) continue;   
		
        $mahkemeArr=(!empty($mahkemeList[$v]))?$mahkemeList[$v]:"";
        $mahkeme=(!empty($mahkemeArr["mh_id"]))?$mahkemeArr["mh_id"]:"";
		
        if ($mahkeme)
            $output[]=$mahkeme;
    }
    $outputTxt=(is_array($output))?implode(",",$output):"";
    
    
    return $outputTxt;
    
}

function convertMahkemeIdToText($data = false)
{
    $mahkemeList=FormSelectMahkemeList(0,1);
    $output=array();
    
    foreach ($data?$data:array() as $k=>$v) {
        $mahkeme=(!empty($mahkemeList[$v]))?$mahkemeList[$v]:"";
        if ($mahkeme)
            $output[]=$mahkeme;
        else {
            $newMahkeme=(stristr($v,"yeni_"))?str_replace("yeni_","",$v):"";
            
            if ($newMahkeme)
                $output[]=addNewMahkeme($newMahkeme);
        }
    }

    $dataLast=drsTagifyToText($output);


    return $dataLast;
    
}

function drsTagifyToText($data = false)
{
    $_text = "";
    if (is_array($data)) {
        foreach ($data as $veri) {
            if (strlen($veri) > 0 && $veri != "") {
                $_text .= $veri . "@";
            }
        }
    }
    return $_text;
}

function addNewMahkeme($data) {
    $t = &get_instance();
    $t->load->model("mahkemeler_model");
    $data=trim($data);
    $data=ucwords($data);
    $mahkemeVar=$t->mahkemeler_model->checkMahkemeAdi($data,0);
    if (empty($mahkemeVar)) {
        $update = $t->mahkemeler_model->ek_add('r8t_sys_mahkemeler',
            array("mh_name"=>$data)
        );
    }

    return $data;
}

function drsTagifyToTextToUpper($data = false)
{
    $_text = "";
    $search = array("ç", "i", "ı", "ğ", "ö", "ş", "ü");
    $replace = array("Ç", "İ", "I", "Ğ", "Ö", "Ş", "Ü");

    if (is_array($data)) {
        foreach ($data as $veri) {
            if (strlen($veri) > 0 && $veri != "") {
                $text = str_replace($search, $replace, $veri);
                $_text .= strtoupper($text) . "@";
            }
        }
    }
    return $_text;
}

function drsTakipBilgisiYaz($id=false){
    $_tur = "Bilinmiyor";
    switch ($id) {
        case false:
            $_tur = "";
            break;
        case "0":
            $_tur = '';
            break;
        case "1":
            $_tur = '<div class="badge badge-info fw-bolder m-1">Duruşmaya Gidildi</div>';
            break;
        case "2":
            $_tur = '<div class="badge badge-warning fw-bolder m-1">Mazeret Çekildi</div>';
            break;
        default:
            $_tur = "";
            break;
    }
    return $_tur;

}


function drsTutanakBilgisiYaz($id=false){
    $_tur = "Bilinmiyor";
    switch ($id) {
        case false:
            $_tur = "";
            break;
        case "0":
            $_tur = '<div class="badge badge-warning fw-bolder m-1">Alınmadı</div>';
            break;
        case "1":
            $_tur = '<div class="badge badge-success fw-bolder m-1">Alındı</div>';
            break;
        default:
            $_tur = "";
            break;
    }
    return $_tur;

}

function drsSorumluAvukatCek($id=false){
    $t = &get_instance();
    $items = false;
    if($id!==false){
        $t->load->model("durusmalar_model");
        
        $sql = "SELECT u_id,u_name,u_lastname,u_surname FROM r8t_users WHERE u_id=".$id." AND u_status=1";
        $items = $t->durusmalar_model->ek_query($sql);
    }
    
    return $items;
}

function ckmAyracsizYazdir($item = false)
{
    $_text = "";
    if ($item != false) {
        $_texts = @explode("@", $item);
        for ($i = 0; $i <= count($_texts) - 2; $i++) {
            if (stripos($_text, trim($_texts[$i])) < 1) {
                if ($i == 0) {
                    $_text .= trim($_texts[$i]);
                } elseif ($i > 0 && $i != (count($_texts) - 2)) {
                    $_text .= " " . trim($_texts[$i]);
                } else {
                    $_text .= " " . trim($_texts[$i]);
                }
            }
        }
    }
    $_text = str_replace(',,', ',', $_text);
    return $_text;
}

function ckmHareketTurYazdir($id)
{
    $_tur = "Bilinmiyor";
    switch ($id) {
        case "-1":
            $_tur = "Hepsi";
            break;
        case "0":
            $_tur = '<div class="badge badge-success fw-bolder m-1">Eklendi</div>';
            break;
        case "1":
            $_tur = '<div class="badge badge-info fw-bolder m-1">Silmindi</div>';
            break;
        case "2":
            $_tur = '<div class="badge badge-warning fw-bolder m-1">Güncellendi</div>';
            break;
        case "3":
            $_tur = '<div class="badge badge-danger fw-bolder m-1">Çöpe Atıldı</div>';
            break;
        default:
            $_tur = "Bilinmiyor";
            break;
    }
    return $_tur;
}
