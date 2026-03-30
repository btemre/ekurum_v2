<?php
defined('BASEPATH') or exit('No direct script access allowed');

function dTapuBilgiYazdir($id)
{
    $_tur = "Bilinmiyor";
    switch ($id) {
        case "-1":
            $_tur = "Belirsiz";
            break;
        case "0":
            $_tur = "Tapu Yok";
            break;
        case "1":
            $_tur = "Tapu Var";
            break;
        default:
            $_tur = "Bilinmiyor";
            break;
    }
    return $_tur;
}
function _dGetAllTagsCache()
{
    static $cache = null;
    if ($cache === null) {
        $t = &get_instance();
        $t->load->model("dosya_model");
        $allTags = $t->dosya_model->ek_query_all("SELECT tag_isim, tag_color FROM r8t_edys_tags");
        $cache = array();
        if ($allTags) {
            foreach ($allTags as $tag) {
                $cache[$tag->tag_isim] = $tag->tag_color;
            }
        }
    }
    return $cache;
}

function dTagYazdir($item = false)
{
    $_tagText = "";
    if ($item != false) {
        $tagCache = _dGetAllTagsCache();
        $_texts = explode("@", (string)($item ?? ''));
        foreach ($_texts as $text) {
            $text = trim($text);
            if (strlen($text) > 0 && isset($tagCache[$text])) {
                $_tagText .= '<div class="badge badge-' . $tagCache[$text] . ' fw-bolder m-1">' . $text . '</div>';
            }
        }
    }
    return $_tagText;
}

function dEditTagYazdir($item = false)
{
    //return $item;
    $t = &get_instance();
    $t->load->model("dosya_model");
    $_tagText = "";
    if ($item != false) {
        $_texts = explode("@", (string)($item ?? ''));
        foreach ($_texts as $text) {
            $isTag = $t->dosya_model->ek_get(
                "r8t_edys_tags",
                array(
                    "tag_isim"  => $text
                )
            );
            if ($isTag) {
                $_tagText .= $isTag->tag_isim . ',';
            }
        }
    }
    $_tagText = str_replace(',,', ',', $_tagText);
    return $_tagText;
}


function dAyracsizYazdir($item = false)
{
    $_text = "";
    if ($item != false) {
        $_texts = explode("@", (string)($item ?? ''));
        for ($i = 0; $i <= count($_texts) - 2; $i++) {
            if ($i == 0) {
                $_text .= $_texts[$i];
            } elseif ($i > 0 && $i != (count($_texts) - 2)) {
                $_text .= " " . $_texts[$i];
            } else {
                $_text .= " " . $_texts[$i];
            }
        }
    }
    $_text = str_replace(',,', ',', $_text);
    return $_text;
}
function dEditAyracliYazdir($item = false)
{
    $_text = "";
    if ($item != false) {
        $_texts = explode("@", (string)($item ?? ''));
        for ($i = 0; $i <= count($_texts) - 2; $i++) {
            if ($i == 0) {
                $_text .= $_texts[$i];
            } elseif ($i > 0 && $i != (count($_texts) - 2)) {
                $_text .= "," . $_texts[$i];
            } else {
                $_text .= "," . $_texts[$i];
            }
        }
    }
    $_text = str_replace(',,', ',', $_text);
    return $_text;
}

function dTagifyToText($data = false)
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

function dTagifyToTextToUpper($data = false)
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