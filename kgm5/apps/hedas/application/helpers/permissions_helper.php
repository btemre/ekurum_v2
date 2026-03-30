<?php
defined('BASEPATH') or exit('No direct script access allowed');

### <begin::Controller Dosyasındaki Controller Dosya Listesini Ceker> ###
function getSysControllerList()
{
    $t = &get_instance();
    $_cl = array();
    $t->load->helper("file");

    $files = get_dir_file_info(APPPATH . "controllers", FALSE);
    if ($files == false) {
        return false;
    }
    $sayac = -1;
    $infoFile = new stdClass();
    foreach (array_keys($files) as $file) {
        if (strstr($file, ".php") == true) {
            $sayac++;
            $_name = strtolower(str_replace(".php", "", $file));
            $_info = read_file(APPPATH . "views/{$_name}_v/info.json");
            if ($_info) {
                $infoFile = json_decode($_info);
            } else {
                $infoFile->name         = $_name;
                $infoFile->description  = ucwords($_name) . " Modülü";
            }
            $_cl[$sayac]['name'] = $infoFile->name;
            $_cl[$sayac]['code'] = $_name;
            $_cl[$sayac]['description'] = $infoFile->description;
        }
    }
    if ($sayac == -1) {
        return false;
    }

    $moduls = json_encode($_cl);
    $controllers = new stdClass();
    $controllers = json_decode($moduls);
    return $controllers;
}
### <end::Controller Dosyasındaki Controller Dosya Listesini Ceker> ###

### <begin::Controller Ic Yetkilendirme Kontrol Fonksiyonları> ###


### <begin::Modülde Wiew Yetkisinin Kontrolü> ##
function isDbAllowedViewModule($moduleName = "")
{
    $t = &get_instance();
    $moduleName = ($moduleName == "") ? $t->router->fetch_class() : $moduleName;
    $moduleName   = trim(replacePost($moduleName));

    $userB    = getUser()->userData->userB;
    $t->load->model("RT_model");

    $_isPermissions = false;
    $isGroupAdminPermissions = $t->RT_model->ek_get(
        "r8t_sys_group_permissions",
        array(
            "gp_app"        => "hedas",
            "gp_groupid"    => $userB->ug_id,
            "gp_controller" => $moduleName,
            "gp_read"     => 1
        )
    );

    if ($isGroupAdminPermissions) {
        $_isPermissions = true;
    }

    $isStatusAdminPermissions = $t->RT_model->ek_get(
        "r8t_sys_statu_permissions",
        array(
            "sp_app"        => "hedas",
            "sp_statuid"    => $userB->us_id,
            "sp_controller" => $moduleName,
            "sp_read"     => 1
        )
    );

    if ($isStatusAdminPermissions) {
        $_isPermissions = true;
    }

    ### KULLANICI YETKILERI KONTROL EDILECEK

    return $_isPermissions;
}
### <end::Modülde Wiew Yetkisinin Kontrolü> ##
### <begin::Modülde List Yetkisinin Kontrolü> ##
function isDbAllowedListModule($moduleName = "")
{
    $t = &get_instance();
    $moduleName = ($moduleName == "") ? $t->router->fetch_class() : $moduleName;
    $moduleName   = trim(replacePost($moduleName));

    $userB    = getUser()->userData->userB;
    $t->load->model("RT_model");

    $_isPermissions = false;
    $isGroupAdminPermissions = $t->RT_model->ek_get(
        "r8t_sys_group_permissions",
        array(
            "gp_app"        => "hedas",
            "gp_groupid"    => $userB->ug_id,
            "gp_controller" => $moduleName,
            "gp_list"     => 1
        )
    );

    if ($isGroupAdminPermissions) {
        $_isPermissions = true;
    }

    $isStatusAdminPermissions = $t->RT_model->ek_get(
        "r8t_sys_statu_permissions",
        array(
            "sp_app"        => "hedas",
            "sp_statuid"    => $userB->us_id,
            "sp_controller" => $moduleName,
            "sp_list"     => 1
        )
    );

    if ($isStatusAdminPermissions) {
        $_isPermissions = true;
    }

    ### KULLANICI YETKILERI KONTROL EDILECEK

    return $_isPermissions;
}
### <end::Modülde List Yetkisinin Kontrolü> ##
### <begin::Modülde Write Yetkisinin Kontrolü> ##
function isDbAllowedWriteModule($moduleName = "")
{
    $t = &get_instance();
    $moduleName     = ($moduleName == "") ? $t->router->fetch_class() : $moduleName;
    $moduleName     = trim(replacePost($moduleName));

    $userB    = getUser()->userData->userB;
    $t->load->model("RT_model");

    $_isPermissions = false;
    $isGroupAdminPermissions = $t->RT_model->ek_get(
        "r8t_sys_group_permissions",
        array(
            "gp_app"        => "hedas",
            "gp_groupid"    => $userB->ug_id,
            "gp_controller" => $moduleName,
            "gp_write"     => 1
        )
    );

    if ($isGroupAdminPermissions) {
        $_isPermissions = true;
    }

    $isStatusAdminPermissions = $t->RT_model->ek_get(
        "r8t_sys_statu_permissions",
        array(
            "sp_app"        => "hedas",
            "sp_statuid"    => $userB->us_id,
            "sp_controller" => $moduleName,
            "sp_write"     => 1
        )
    );

    if ($isStatusAdminPermissions) {
        $_isPermissions = true;
    }

    ### KULLANICI YETKILERI KONTROL EDILECEK

    return $_isPermissions;
}
### <end::Modülde Write Yetkisinin Kontrolü> ##
### <begin::Modülde Update Yetkisinin Kontrolü> ##
function isDbAllowedUpdateModule($moduleName = "")
{
    $t = &get_instance();
    $moduleName = ($moduleName == "") ? $t->router->fetch_class() : $moduleName;
    $moduleName   = trim(replacePost($moduleName));

    $userB    = getUser()->userData->userB;
    $t->load->model("RT_model");

    $_isPermissions = false;
    $isGroupAdminPermissions = $t->RT_model->ek_get(
        "r8t_sys_group_permissions",
        array(
            "gp_app"        => "hedas",
            "gp_groupid"    => $userB->ug_id,
            "gp_controller" => $moduleName,
            "gp_update"     => 1
        )
    );

    if ($isGroupAdminPermissions) {
        $_isPermissions = true;
    }

    $isStatusAdminPermissions = $t->RT_model->ek_get(
        "r8t_sys_statu_permissions",
        array(
            "sp_app"        => "hedas",
            "sp_statuid"    => $userB->us_id,
            "sp_controller" => $moduleName,
            "sp_update"     => 1
        )
    );

    if ($isStatusAdminPermissions) {
        $_isPermissions = true;
    }

    ### KULLANICI YETKILERI KONTROL EDILECEK

    return $_isPermissions;
}
### <end::Modülde Update Yetkisinin Kontrolü> ##
### <begin::Modülde Silme Yetkisinin Kontrolü> ##
function isDbAllowedDeleteModule($moduleName = "")
{
    $t = &get_instance();
    $moduleName = ($moduleName == "") ? $t->router->fetch_class() : $moduleName;
    $moduleName   = trim(replacePost($moduleName));

    $userB    = getUser()->userData->userB;
    $t->load->model("RT_model");

    $_isPermissions = false;
    $isGroupAdminPermissions = $t->RT_model->ek_get(
        "r8t_sys_group_permissions",
        array(
            "gp_app"        => "hedas",
            "gp_groupid"    => $userB->ug_id,
            "gp_controller" => $moduleName,
            "gp_delete"     => 1
        )
    );

    if ($isGroupAdminPermissions) {
        $_isPermissions = true;
    }

    $isStatusAdminPermissions = $t->RT_model->ek_get(
        "r8t_sys_statu_permissions",
        array(
            "sp_app"        => "hedas",
            "sp_statuid"    => $userB->us_id,
            "sp_controller" => $moduleName,
            "sp_delete"     => 1
        )
    );

    if ($isStatusAdminPermissions) {
        $_isPermissions = true;
    }

    ### KULLANICI YETKILERI KONTROL EDILECEK

    return $_isPermissions;
}
### <end::Modülde Silme Yetkisinin Kontrolü> ##
### <begin::Modülde Admin Yetkisinin Kontrolü> ##
function isDbAdminViewModule($moduleName = "")
{
    $t = &get_instance();
    $moduleName = ($moduleName == "") ? $t->router->fetch_class() : $moduleName;
    $moduleName   = trim(replacePost($moduleName));

    $userB    = getUser()->userData->userB;
    $t->load->model("RT_model");

    $_isPermissions = false;
    $isGroupAdminPermissions = $t->RT_model->ek_get(
        "r8t_sys_group_permissions",
        array(
            "gp_app"        => "hedas",
            "gp_groupid"    => $userB->ug_id,
            "gp_controller" => $moduleName,
            "gp_adminr"     => 1
        )
    );

    if ($isGroupAdminPermissions) {
        $_isPermissions = true;
    }

    $isStatusAdminPermissions = $t->RT_model->ek_get(
        "r8t_sys_statu_permissions",
        array(
            "sp_app"        => "hedas",
            "sp_statuid"    => $userB->us_id,
            "sp_controller" => $moduleName,
            "sp_adminr"     => 1
        )
    );

    if ($isStatusAdminPermissions) {
        $_isPermissions = true;
    }

    ### KULLANICI YETKILERI KONTROL EDILECEK

    return $_isPermissions;
}
### <end::Modülde Admin Yetkisinin Kontrolü> ##

### <end::Controller Ic Yetkilendirme Kontrol Fonksiyonları> ###

### <begin::Ana Admin Aside Menu Yetkilendirme Kontrol Fonksiyonları> ###
function isDbAsidePermissions($moduleName = "")
{
    $_isPermissions = false;
    if (isDbAllowedViewModule($moduleName)) {
        $_isPermissions = true;
    }
    if (isDbAllowedListModule($moduleName)) {
        $_isPermissions = true;
    }
    if (isDbAllowedWriteModule($moduleName)) {
        $_isPermissions = true;
    }
    if (isDbAllowedUpdateModule($moduleName)) {
        $_isPermissions = true;
    }
    if (isDbAllowedDeleteModule($moduleName)) {
        $_isPermissions = true;
    }
    if (isDbAdminViewModule($moduleName)) {
        $_isPermissions = true;
    }
    return $_isPermissions;
}
### <end::Ana Admin Aside Menu Yetkilendirme Kontrol Fonksiyonları> ###

### <begin::Ana Admin Aside Menu Yetkilendirme Kontrol Fonksiyonları> ###
function isAsidePermissions($moduleName = "")
{
    $_isPermissions = false;
    if (isDbAllowedViewModule($moduleName)) {
        $_isPermissions = true;
    }
    if (isDbAllowedListModule($moduleName)) {
        $_isPermissions = true;
    }
    if (isDbAllowedWriteModule($moduleName)) {
        $_isPermissions = true;
    }
    if (isDbAllowedUpdateModule($moduleName)) {
        $_isPermissions = true;
    }
    if (isDbAllowedDeleteModule($moduleName)) {
        $_isPermissions = true;
    }
    if (isDbAdminViewModule($moduleName)) {
        $_isPermissions = true;
    }
    return $_isPermissions;
}
    ### <end::Ana Admin Aside Menu Yetkilendirme Kontrol Fonksiyonları> ###
