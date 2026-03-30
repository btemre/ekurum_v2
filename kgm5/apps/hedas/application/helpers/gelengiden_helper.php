<?php
defined('BASEPATH') or exit('No direct script access allowed');

function ggTurYazdir($id)
{
    $_tur = "Bilinmiyor";
    switch ($id) {
        case "0":
            $_tur = "Gelen Evrak";
            break;
        case "1":
            $_tur = "Giden Evrak";
            break;
        case "2":
            $_tur = "Genel Evrak";
            break;
        default:
            $_tur = "Bilinmiyor";
            break;
    }
    return $_tur;
}

function ggKategoriYazdir($id)
{
    $_tur = "Bilinmiyor";
    switch ($id) {
        case "0":
            $_tur = "Genel";
            break;
        case "1":
            $_tur = "Personel";
            break;
        case "2":
            $_tur = "Tamim Genelge";
            break;
        default:
            $_tur = "Bilinmiyor";
            break;
    }
    return $_tur;
}

function ggIlgiliYazdir($item = false)
{
    $_text = "";
    if ($item != false) {
        $_texts = explode("@", (string)($item ?? ''));
        for ($i = 0; $i <= count($_texts) - 2; $i++) {
            if ($i == 0) {
                $_text .= $_texts[$i];
            } elseif ($i > 0 && $i != (count($_texts) - 2)) {
                $_text .= "<br>" . $_texts[$i];
            } else {
                $_text .= "<br>" . $_texts[$i];
            }
        }
    }
    return $_text;
}

function ggTagYazdir($item = false)
{
    //return $item;
    $t = &get_instance();
    $t->load->model("gelengiden_model");
    $_tagText = "";
    if ($item != false) {
        $_texts = explode("@", (string)($item ?? ''));
        foreach ($_texts as $text) {
            $isTag = $t->gelengiden_model->ek_get(
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


function ggEditIlgiliYazdir($item = false)
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


function ggEditTagYazdir($item = false)
{
    //return $item;
    $t = &get_instance();
    $t->load->model("gelengiden_model");
    $_tagText = "";
    if ($item != false) {
        $_texts = explode("@", (string)($item ?? ''));
        foreach ($_texts as $text) {
            $isTag = $t->gelengiden_model->ek_get(
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


function ggTagListAllPrint()
{
    $t = &get_instance();
    $t->load->model("gelengiden_model");

    $items = $t->gelengiden_model->ek_get_all(
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

function ggKaynakListAllPrint()
{
    $t = &get_instance();
    $t->load->model("gelengiden_model");

    $sql = "SELECT gg_kaynak FROM r8t_edys_ggevrak WHERE gg_status!=-1 GROUP BY gg_kaynak";
    $items = $t->gelengiden_model->ek_query_all($sql);
    $itemText = "";
    if ($items) {
        foreach ($items as $item) {
            $kaynakX = explode("@", $item->gg_kaynak);
            foreach ($kaynakX as $kaynak) {
                if (stripos($itemText, $kaynak) == false) {
                    $itemText .= $kaynak . ",";
                }
            }
        }
    }
    return $itemText;
}
