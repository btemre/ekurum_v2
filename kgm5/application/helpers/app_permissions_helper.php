<?php
defined('BASEPATH') or exit('No direct script access allowed');


### <begin::Apps Dosyasındaki Uygulamaların Listesini Ceker> ###
function getAppList()
{
    $t = &get_instance();
    $_cl = array();
    $t->load->helper("file");

    $files = get_dir_file_info(FCPATH . "apps", TRUE);
    if ($files == false) {
        return false;
    }
    $sayac = -1;

    foreach (array_keys($files) as $file) {
        if (is_dir(FCPATH . "apps/" . $file . "/")) {
            $sayac++;
            $_name = $file;
            $_info = read_file(FCPATH . "apps/{$_name}/info.json");
            if ($_info) {
                $infoFile = json_decode($_info);
                $_cl[$sayac]['name'] = $infoFile->name;
                $_cl[$sayac]['shortcode'] = $infoFile->shortcode;
                $_cl[$sayac]['code'] = $_name;
                $_cl[$sayac]['description'] = $infoFile->description;
                $_cl[$sayac]['menutext'] = $infoFile->menutext;
                $_cl[$sayac]['url'] = FCPATH . "apps/" . $file . "/";
                $_cl[$sayac]['color'] = $infoFile->color;
                $_cl[$sayac]['image'] = $infoFile->image;
            } else {
                $sayac--;
            }
        }
    }
    if ($sayac <= -1) {
        return false;
    }

    $moduls = json_encode($_cl);
    $controllers = new stdClass();
    $controllers = json_decode($moduls);
    return $controllers;
}
### <end::Apps Dosyasındaki Uygulamaların Listesini Ceker> ###

### <begin::Uygulamada Admin Yetkisinin Kontrolü> ##
function isAdminViewApp($appName = "")
{
    $t = &get_instance();
    //$appName = ($appName == "") ? $t->router->fetch_class() : $appName;
    $userB    = getUser()->userData->userB;
    $t->load->model("RT_model");

    $_isPermissions = false;
    $isUnitsAdminPermissions = $t->RT_model->ek_get(
        "r8t_sys_unit_app_permissions",
        array(
            "up_appzone"        => "sys",
            "up_unitid"         => $userB->ub_id,
            "up_appcode"        => $appName,
            "up_adminr"         => 1
        )
    );
    if ($isUnitsAdminPermissions) {
        $_isPermissions = true;
    }

    $isGroupAdminPermissions = $t->RT_model->ek_get(
        "r8t_sys_group_app_permissions",
        array(
            "gap_appzone"        => "sys",
            "gap_groupid"         => $userB->ug_id,
            "gap_appcode"        => $appName,
            "gap_adminr"         => 1
        )
    );
    if ($isGroupAdminPermissions) {
        $_isPermissions = true;
    }



    ### KULLANICI YETKILERI KONTROL EDILECEK

    return $_isPermissions;
}
### <end::Uygulamada Admin Yetkisinin Kontrolü> ##
### <begin::Uygulamada Wiew Yetkisinin Kontrolü> ##
function isAllowedViewApp($appName = "")
{
    $t = &get_instance();
    //$moduleName = ($moduleName == "") ? $t->router->fetch_class() : $moduleName;
    $userB    = getUser()->userData->userB;
    $t->load->model("RT_model");

    $_isPermissions = false;

    $isGroupPermissions = $t->RT_model->ek_get(
        "r8t_sys_group_app_permissions",
        array(
            "gap_appzone"        => "sys",
            "gap_groupid"         => $userB->ug_id,
            "gap_appcode"        => $appName,
            "gap_read"         => 1
        )
    );
    if ($isGroupPermissions) {
        $_isPermissions = true;
    }

    $isUnitsPermissions = $t->RT_model->ek_get(
        "r8t_sys_unit_app_permissions",
        array(
            "up_appzone"        => "sys",
            "up_unitid"         => $userB->ub_id,
            "up_appcode"        => $appName,
            "up_read"           => 1
        )
    );

    if ($isUnitsPermissions) {
        $_isPermissions = true;
    } else {
        $_isPermissions = false;
    }


    ### KULLANICI YETKILERI KONTROL EDILECEK

    return $_isPermissions;
}
### <end::Uygulamada Wiew Yetkisinin Kontrolü> ##
### <begin::Uygulamada List Yetkisinin Kontrolü> ##
function isAllowedListApp($appName = "")
{
    $t = &get_instance();
    //$moduleName = ($moduleName == "") ? $t->router->fetch_class() : $moduleName;
    $userB    = getUser()->userData->userB;
    $t->load->model("RT_model");

    $_isPermissions = false;

    $isGroupPermissions = $t->RT_model->ek_get(
        "r8t_sys_group_app_permissions",
        array(
            "gap_appzone"        => "sys",
            "gap_groupid"         => $userB->ug_id,
            "gap_appcode"        => $appName,
            "gap_list"         => 1
        )
    );
    if ($isGroupPermissions) {
        $_isPermissions = true;
    }

    $isUnitsPermissions = $t->RT_model->ek_get(
        "r8t_sys_unit_app_permissions",
        array(
            "up_appzone"        => "sys",
            "up_unitid"         => $userB->ub_id,
            "up_appcode"        => $appName,
            "up_list"           => 1
        )
    );

    if ($isUnitsPermissions) {
        $_isPermissions = true;
    } else {
        $_isPermissions = false;
    }

    ### KULLANICI YETKILERI KONTROL EDILECEK

    return $_isPermissions;
}
### <end::Uygulamada List Yetkisinin Kontrolü> ##
### <begin::Uygulamada Write Yetkisinin Kontrolü> ##
function isAllowedWriteApp($appName = "")
{
    $t = &get_instance();
    //$moduleName = ($moduleName == "") ? $t->router->fetch_class() : $moduleName;
    $userB    = getUser()->userData->userB;
    $t->load->model("RT_model");

    $_isPermissions = false;
    $isUnitsPermissions = $t->RT_model->ek_get(
        "r8t_sys_unit_app_permissions",
        array(
            "up_appzone"        => "sys",
            "up_unitid"         => $userB->ub_id,
            "up_appcode"        => $appName,
            "up_write"           => 1
        )
    );

    if ($isUnitsPermissions) {
        $_isPermissions = true;
    }

    $isGroupPermissions = $t->RT_model->ek_get(
        "r8t_sys_group_app_permissions",
        array(
            "gap_appzone"        => "sys",
            "gap_groupid"         => $userB->ug_id,
            "gap_appcode"        => $appName,
            "gap_write"         => 1
        )
    );
    if ($isGroupPermissions) {
        $_isPermissions = true;
    }


    ### KULLANICI YETKILERI KONTROL EDILECEK

    return $_isPermissions;
}
### <end::Uygulamada Write Yetkisinin Kontrolü> ##
### <begin::Uygulamada Update Yetkisinin Kontrolü> ##
function isAllowedUpdateApp($appName = "")
{
    $t = &get_instance();
    //$moduleName = ($moduleName == "") ? $t->router->fetch_class() : $moduleName;
    $userB    = getUser()->userData->userB;
    $t->load->model("RT_model");

    $_isPermissions = false;
    $isUnitsPermissions = $t->RT_model->ek_get(
        "r8t_sys_unit_app_permissions",
        array(
            "up_appzone"        => "sys",
            "up_unitid"         => $userB->ub_id,
            "up_appcode"        => $appName,
            "up_update"           => 1
        )
    );

    if ($isUnitsPermissions) {
        $_isPermissions = true;
    }

    $isGroupPermissions = $t->RT_model->ek_get(
        "r8t_sys_group_app_permissions",
        array(
            "gap_appzone"        => "sys",
            "gap_groupid"         => $userB->ug_id,
            "gap_appcode"        => $appName,
            "gap_update"         => 1
        )
    );
    if ($isGroupPermissions) {
        $_isPermissions = true;
    }


    ### KULLANICI YETKILERI KONTROL EDILECEK

    return $_isPermissions;
}
### <end::Uygulamada Update Yetkisinin Kontrolü> ##
### <begin::Uygulamada Delete Yetkisinin Kontrolü> ##
function isAllowedDeleteApp($appName = "")
{
    $t = &get_instance();
    //$moduleName = ($moduleName == "") ? $t->router->fetch_class() : $moduleName;
    $userB    = getUser()->userData->userB;
    $t->load->model("RT_model");

    $_isPermissions = false;
    $isUnitsPermissions = $t->RT_model->ek_get(
        "r8t_sys_unit_app_permissions",
        array(
            "up_appzone"        => "sys",
            "up_unitid"         => $userB->ub_id,
            "up_appcode"        => $appName,
            "up_delete"         => 1
        )
    );

    if ($isUnitsPermissions) {
        $_isPermissions = true;
    }

    $isGroupPermissions = $t->RT_model->ek_get(
        "r8t_sys_group_app_permissions",
        array(
            "gap_appzone"        => "sys",
            "gap_groupid"         => $userB->ug_id,
            "gap_appcode"        => $appName,
            "gap_delete"         => 1
        )
    );
    if ($isGroupPermissions) {
        $_isPermissions = true;
    }


    ### KULLANICI YETKILERI KONTROL EDILECEK

    return $_isPermissions;
}
### <end::Uygulamada Delete Yetkisinin Kontrolü> ##


### <begin::Uygulama İçerisindeki Controller Dosyasındaki Controller Dosya Listesini Ceker> ###
function getAppControllerList($appcode = "")
{
    $t = &get_instance();
    $_cl = array();
    $t->load->helper("file");

    $_pathC   = FCPATH . "apps/{$appcode}/application/controllers";
    $_pathW   = FCPATH . "apps/{$appcode}/application/views";

    $files = get_dir_file_info($_pathC, FALSE);
    if ($files == false) {
        return false;
    }
    $sayac = -1;
    $infoFile = new stdClass();
    foreach (array_keys($files) as $file) {
        if (strstr($file, ".php") == true) {
            $sayac++;
            $_name = strtolower(str_replace(".php", "", $file));
            $_info = read_file($_pathW . "/{$_name}_v/info.json");
            if ($_info) {
                $infoFile = json_decode($_info);
            } else {
                $infoFile->name         = $_name;
                $infoFile->description  = ucwords($_name) . " Modülü";
            }
            $_cl[$sayac]['app']         = $appcode;
            $_cl[$sayac]['name']        = $infoFile->name;
            $_cl[$sayac]['code']        = $_name;
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
### <end::Uygulama İçerisindeki Controller Dosyasındaki Controller Dosya Listesini Ceker> ###

### <begin::Sistem İçerisinde Uygulamaların Birimler Bazında Yetkilerinin Kontrolü> ###
function getPermissionAppList()
{
    $t = &get_instance();
    $t->load->model("RT_model");
    $userB    = getUser()->userData->userB;
    $apps     = false;
    $getApps  = getAppList();

    $appList  = $t->RT_model->appUpdate($getApps);
    // Login Olan Kullanıcı Grubu Root İse
    if ($userB->ug_id == 1) {
        //DB Deki Controllerları Çek
        $apps = $t->RT_model->ek_get_all(
            "r8t_sys_apps",
            array(
                "a_status !="    => -1
            ),
            false
        );
    } else {
        //Login Olan Kullanıcı Grubunun Admin Yetkisi Olduğu Controllerları Çek
        $queryX = "SELECT * FROM r8t_sys_apps WHERE a_status!=-1";
        $queryText = "";
        foreach ($appList as $app) {
            if (isAdminViewApp($app->a_appcode) == false) {
                $queryText .= " AND a_appcode != '{$app->a_appcode}'";
            }
        }
        if ($queryText != "") {
            $queryX .= $queryText;
        }

        $apps = $t->RT_model->ek_query_all($queryX);
    }

    return $apps;
}
### <end::Sistem İçerisinde Uygulamaların Birimler Bazında Yetkilerinin Kontrolü> ###

### <begin::Uygulama İçindeki Modüllerin Kullanıcı Grubu Ve Statüleri Bazında Yetkilerinin Kontrolü> ###
### <begin::Uygulamadaki Modülde Wiew Yetkisinin Kontrolü> ##
function isAllowedViewAppModule($moduleName = "", $appcode = "")
{
    $t = &get_instance();
    if ($moduleName == "" || $appcode == "") {
        return false;
    }
    //  $moduleName = ($moduleName == "") ? $t->router->fetch_class() : $moduleName;
    $moduleName   = trim(replacePost($moduleName));
    $appcode      = trim(replacePost($appcode));

    $userB    = getUser()->userData->userB;
    $t->load->model("usergroup_model");
    $t->load->model("userstatus_model");

    $_isPermissions = false;
    $isGroupPermissions = $t->usergroup_model->ek_get(
        "r8t_sys_group_permissions",
        array(
            "gp_app"        => $appcode,
            "gp_groupid"    => $userB->ug_id,
            "gp_controller" => $moduleName,
            "gp_read"     => 1
        )
    );

    if ($isGroupPermissions) {
        $_isPermissions = true;
    }

    $isStatusPermissions = $t->userstatus_model->ek_get(
        "r8t_sys_statu_permissions",
        array(
            "sp_app"        => $appcode,
            "sp_statuid"    => $userB->us_id,
            "sp_controller" => $moduleName,
            "sp_read"     => 1
        )
    );

    if ($isStatusPermissions) {
        $_isPermissions = true;
    }

    ### KULLANICI YETKILERI KONTROL EDILECEK

    return $_isPermissions;
}
### <end::Uygulamadaki Modülde Wiew Yetkisinin Kontrolü> ##
### <begin::Uygulamadaki Modülde List Yetkisinin Kontrolü> ##
function isAllowedListAppModule($moduleName = "", $appcode = "")
{
    $t = &get_instance();
    if ($moduleName == "" || $appcode == "") {
        return false;
    }
    //  $moduleName = ($moduleName == "") ? $t->router->fetch_class() : $moduleName;
    $moduleName   = trim(replacePost($moduleName));
    $appcode      = trim(replacePost($appcode));

    $userB    = getUser()->userData->userB;
    $t->load->model("usergroup_model");
    $t->load->model("userstatus_model");

    $_isPermissions = false;
    $isGroupPermissions = $t->usergroup_model->ek_get(
        "r8t_sys_group_permissions",
        array(
            "gp_app"        => $appcode,
            "gp_groupid"    => $userB->ug_id,
            "gp_controller" => $moduleName,
            "gp_list"     => 1
        )
    );

    if ($isGroupPermissions) {
        $_isPermissions = true;
    }

    $isStatusPermissions = $t->userstatus_model->ek_get(
        "r8t_sys_statu_permissions",
        array(
            "sp_app"        => $appcode,
            "sp_statuid"    => $userB->us_id,
            "sp_controller" => $moduleName,
            "sp_list"     => 1
        )
    );

    if ($isStatusPermissions) {
        $_isPermissions = true;
    }

    ### KULLANICI YETKILERI KONTROL EDILECEK

    return $_isPermissions;
}
### <end::Uygulamadaki Modülde List Yetkisinin Kontrolü> ##
### <begin::Uygulamadaki Modülde Write Yetkisinin Kontrolü> ##
function isAllowedWriteAppModule($moduleName = "", $appcode = "")
{
    $t = &get_instance();
    if ($moduleName == "" || $appcode == "") {
        return false;
    }
    //  $moduleName = ($moduleName == "") ? $t->router->fetch_class() : $moduleName;
    $moduleName   = trim(replacePost($moduleName));
    $appcode      = trim(replacePost($appcode));

    $userB    = getUser()->userData->userB;
    $t->load->model("usergroup_model");
    $t->load->model("userstatus_model");

    $_isPermissions = false;
    $isGroupPermissions = $t->usergroup_model->ek_get(
        "r8t_sys_group_permissions",
        array(
            "gp_app"        => $appcode,
            "gp_groupid"    => $userB->ug_id,
            "gp_controller" => $moduleName,
            "gp_write"     => 1
        )
    );

    if ($isGroupPermissions) {
        $_isPermissions = true;
    }

    $isStatusPermissions = $t->userstatus_model->ek_get(
        "r8t_sys_statu_permissions",
        array(
            "sp_app"        => $appcode,
            "sp_statuid"    => $userB->us_id,
            "sp_controller" => $moduleName,
            "sp_write"     => 1
        )
    );

    if ($isStatusPermissions) {
        $_isPermissions = true;
    }

    ### KULLANICI YETKILERI KONTROL EDILECEK

    return $_isPermissions;
}
### <end::Uygulamadaki Modülde Write Yetkisinin Kontrolü> ##
### <begin::Uygulamadaki Modülde Update Yetkisinin Kontrolü> ##
function isAllowedUpdateAppModule($moduleName = "", $appcode = "")
{
    $t = &get_instance();
    if ($moduleName == "" || $appcode == "") {
        return false;
    }
    //  $moduleName = ($moduleName == "") ? $t->router->fetch_class() : $moduleName;
    $moduleName   = trim(replacePost($moduleName));
    $appcode      = trim(replacePost($appcode));

    $userB    = getUser()->userData->userB;
    $t->load->model("usergroup_model");
    $t->load->model("userstatus_model");

    $_isPermissions = false;
    $isGroupPermissions = $t->usergroup_model->ek_get(
        "r8t_sys_group_permissions",
        array(
            "gp_app"        => $appcode,
            "gp_groupid"    => $userB->ug_id,
            "gp_controller" => $moduleName,
            "gp_update"     => 1
        )
    );

    if ($isGroupPermissions) {
        $_isPermissions = true;
    }

    $isStatusPermissions = $t->userstatus_model->ek_get(
        "r8t_sys_statu_permissions",
        array(
            "sp_app"        => $appcode,
            "sp_statuid"    => $userB->us_id,
            "sp_controller" => $moduleName,
            "sp_update"     => 1
        )
    );

    if ($isStatusPermissions) {
        $_isPermissions = true;
    }

    ### KULLANICI YETKILERI KONTROL EDILECEK

    return $_isPermissions;
}
### <end::Uygulamadaki Modülde Update Yetkisinin Kontrolü> ##
### <begin::Uygulamadaki Modülde Delete Yetkisinin Kontrolü> ##
function isAllowedDeleteAppModule($moduleName = "", $appcode = "")
{
    $t = &get_instance();
    if ($moduleName == "" || $appcode == "") {
        return false;
    }
    //  $moduleName = ($moduleName == "") ? $t->router->fetch_class() : $moduleName;
    $moduleName   = trim(replacePost($moduleName));
    $appcode      = trim(replacePost($appcode));

    $userB    = getUser()->userData->userB;
    $t->load->model("usergroup_model");
    $t->load->model("userstatus_model");

    $_isPermissions = false;
    $isGroupPermissions = $t->usergroup_model->ek_get(
        "r8t_sys_group_permissions",
        array(
            "gp_app"        => $appcode,
            "gp_groupid"    => $userB->ug_id,
            "gp_controller" => $moduleName,
            "gp_delete"     => 1
        )
    );

    if ($isGroupPermissions) {
        $_isPermissions = true;
    }

    $isStatusPermissions = $t->userstatus_model->ek_get(
        "r8t_sys_statu_permissions",
        array(
            "sp_app"        => $appcode,
            "sp_statuid"    => $userB->us_id,
            "sp_controller" => $moduleName,
            "sp_delete"     => 1
        )
    );

    if ($isStatusPermissions) {
        $_isPermissions = true;
    }

    ### KULLANICI YETKILERI KONTROL EDILECEK

    return $_isPermissions;
}
### <end::Uygulamadaki Modülde Delete Yetkisinin Kontrolü> ##
### <begin::Uygulamadaki Modülde AdminView Yetkisinin Kontrolü> ##
function isAllowedAdminViewAppModule($moduleName = "", $appcode = "")
{
    $t = &get_instance();
    if ($moduleName == "" || $appcode == "") {
        return false;
    }
    //  $moduleName = ($moduleName == "") ? $t->router->fetch_class() : $moduleName;
    $moduleName   = trim(replacePost($moduleName));
    $appcode      = trim(replacePost($appcode));

    $userB    = getUser()->userData->userB;
    $t->load->model("usergroup_model");
    $t->load->model("userstatus_model");

    $_isPermissions = false;
    $isGroupPermissions = $t->usergroup_model->ek_get(
        "r8t_sys_group_permissions",
        array(
            "gp_app"        => $appcode,
            "gp_groupid"    => $userB->ug_id,
            "gp_controller" => $moduleName,
            "gp_adminr"     => 1
        )
    );

    if ($isGroupPermissions) {
        $_isPermissions = true;
    }

    $isStatusPermissions = $t->userstatus_model->ek_get(
        "r8t_sys_statu_permissions",
        array(
            "sp_app"        => $appcode,
            "sp_statuid"    => $userB->us_id,
            "sp_controller" => $moduleName,
            "sp_adminr"     => 1
        )
    );

    if ($isStatusPermissions) {
        $_isPermissions = true;
    }

    ### KULLANICI YETKILERI KONTROL EDILECEK

    return $_isPermissions;
}
  ### <end::Uygulamadaki Modülde AdminView Yetkisinin Kontrolü> ##


  ### <end::Uygulama İçindeki Modüllerin Kullanıcı Grubu Ve Statüleri Bazında Yetkilerinin Kontrolü> ###
