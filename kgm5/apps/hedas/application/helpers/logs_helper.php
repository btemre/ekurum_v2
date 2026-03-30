<?php
defined('BASEPATH') or exit('No direct script access allowed');


function logEkleYeni($data = false)
{
    $t = &get_instance();
    $t->load->model("logs_model");
    $sonucid = false;

    if (isset($data->kullanici) && isset($data->aciklama) && isset($data->uygulama) && isset($data->modul) && isset($data->icerikid)) {
        $sonucid = $t->logs_model->ek_add_lastid(
            "r8t_sys_userlogs",
            array(
                "ul_ustid"      => $data->ustid,
                "ul_userid"     => $data->kullanici,
                "ul_aciklama"   => $data->aciklama,
                "ul_tur"        => 0,
                "ul_app"        => $data->uygulama,
                "ul_modul"      => $data->modul,
                "ul_icerikid"   => $data->icerikid,
                "ul_olddata"    => json_encode($data->olddata),
                "ul_newdata"    => json_encode($data->newdata),
                "ul_adddate"    => dateToTimeFormat(date('Y-m-d H:i:s'), "Y-m-d H:i:s"),
                "ul_adduser"    => $data->kullanici
            )
        );
    }
    return $sonucid;
}


function logEkleSilme($data = false)
{
    $t = &get_instance();
    $t->load->model("logs_model");
    $sonucid = false;

    if (isset($data->kullanici) && isset($data->aciklama) && isset($data->uygulama) && isset($data->modul) && isset($data->icerikid)) {
        $sonucid = $t->logs_model->ek_add_lastid(
            "r8t_sys_userlogs",
            array(
                "ul_ustid"      => $data->ustid,
                "ul_userid"     => $data->kullanici,
                "ul_aciklama"   => $data->aciklama,
                "ul_tur"        => 1,
                "ul_app"        => $data->uygulama,
                "ul_modul"      => $data->modul,
                "ul_icerikid"   => $data->icerikid,
                "ul_olddata"    => json_encode($data->olddata),
                "ul_newdata"    => json_encode($data->newdata),
                "ul_adddate"    => dateToTimeFormat(date('Y-m-d H:i:s'), "Y-m-d H:i:s"),
                "ul_adduser"    => $data->kullanici
            )
        );
    }
    return $sonucid;
}


function logEkleGuncelleme($data = false)
{
    $t = &get_instance();
    $t->load->model("logs_model");
    $sonucid = false;

    if (isset($data->kullanici) && isset($data->aciklama) && isset($data->uygulama) && isset($data->modul) && isset($data->icerikid)) {
        $sonucid = $t->logs_model->ek_add_lastid(
            "r8t_sys_userlogs",
            array(
                "ul_ustid"      => $data->ustid,
                "ul_userid"     => $data->kullanici,
                "ul_aciklama"   => $data->aciklama,
                "ul_tur"        => 2,
                "ul_app"        => $data->uygulama,
                "ul_modul"      => $data->modul,
                "ul_icerikid"   => $data->icerikid,
                "ul_olddata"    => json_encode($data->olddata),
                "ul_newdata"    => json_encode($data->newdata),
                "ul_adddate"    => dateToTimeFormat(date('Y-m-d H:i:s'), "Y-m-d H:i:s"),
                "ul_adduser"    => $data->kullanici
            )
        );
    }
    return $sonucid;
}


function logEkleTasima($data = false)
{
    $t = &get_instance();
    $t->load->model("logs_model");
    $sonucid = false;

    if (isset($data->kullanici) && isset($data->aciklama) && isset($data->uygulama) && isset($data->modul) && isset($data->icerikid)) {
        $sonucid = $t->logs_model->ek_add_lastid(
            "r8t_sys_userlogs",
            array(
                "ul_ustid"      => $data->ustid,
                "ul_userid"     => $data->kullanici,
                "ul_aciklama"   => $data->aciklama,
                "ul_tur"        => 3,
                "ul_app"        => $data->uygulama,
                "ul_modul"      => $data->modul,
                "ul_icerikid"   => $data->icerikid,
                "ul_olddata"    => json_encode($data->olddata),
                "ul_newdata"    => json_encode($data->newdata),
                "ul_adddate"    => dateToTimeFormat(date('Y-m-d H:i:s'), "Y-m-d H:i:s"),
                "ul_adduser"    => $data->kullanici
            )
        );
    }
    return $sonucid;
}
