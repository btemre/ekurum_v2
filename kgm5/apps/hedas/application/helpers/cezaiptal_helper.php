<?php
defined('BASEPATH') or exit('No direct script access allowed');

function ciDurumYazdir($id)
{
    $_tur = "Bilinmiyor";
    switch ($id) {
        case "0":
            $_tur = "Yetkisizlik";
            break;
        case "1":
            $_tur = "Kabul";
            break;
        case "2":
            $_tur = "Red";
            break;
        case "3":
            $_tur = "Kısmi Kabul";
            break;
        case "4":
            $_tur = "Kısmi Red";
            break;
        case "5":
            $_tur = "Kısmen Kabul Kısmen Red";
            break;
        case "6":
            $_tur = "Birleştirilmiş";
            break;
        case "7":
            $_tur = "Belirlenmemiş";
            break;
        default:
            $_tur = "Bilinmiyor";
            break;
    }
    return $_tur;
}

function ciTagYazdir($item = false)
{
    //return $item;
    $t = &get_instance();
    $t->load->model("cezaiptal_model");
    $_tagText = "";
    if ($item != false) {
        $_texts = explode("@", (string)($item ?? ''));
        foreach ($_texts as $text) {
            $isTag = $t->cezaiptal_model->ek_get(
                "r8t_edys_tags",
                array(
                    "tag_isim"  => $text
                )
            );
            if ($isTag) {
                $_tagText .= '<div class="badge badge-' . $isTag->tag_color . ' fw-bolder m-1">' . $isTag->tag_isim . '</div>';
            }
        }
    }
    return $_tagText;
}


function ciEditTagYazdir($item = false)
{
    //return $item;
    $t = &get_instance();
    $t->load->model("cezaiptal_model");
    $_tagText = "";
    if ($item != false) {
        $_texts = explode("@", (string)($item ?? ''));
        foreach ($_texts as $text) {
            $isTag = $t->cezaiptal_model->ek_get(
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
    return $_tagText;
}


function ciTagListAllPrint()
{
    $t = &get_instance();
    $t->load->model("cezaiptal_model");

    $items = $t->cezaiptal_model->ek_get_all(
        "r8t_edys_tags",
        array(
            "tag_ispublic"  => 1
        )
    );
    $tagText = "";
    if ($items) {
        foreach ($items as $item) {
            $tagText .= $item->tag_isim . ",";
        }
    }
    return $tagText;
}

function ciAyracsizYazdir($item = false)
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
    return $_text;
}
function ciEditAyracliYazdir($item = false)
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
    return $_text;
}


function tagifyToText($data = false)
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

function tagifyToTextToUpper($data = false)
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
