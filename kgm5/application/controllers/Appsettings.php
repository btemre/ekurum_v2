<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Appsettings extends CI_Controller
{
    public $viewFolder     = "";
    public $userData        = false;

    public function __construct()
    {
        parent::__construct();
        $this->viewFolder = "appsettings_v";
        $this->load->model("appsettings_model");

        $this->load->model("auth_model");
        $this->userData = $this->auth_model->userData;
    }


    public function index()
    {
        if ($this->userData === false) {
            redirect(base_url("login"));
            exit;
        }

        if (!isDbAllowedViewModule()) {
            redirect(base_url("dashboard"));
            exit;
        }

        $apps = getPermissionAppList();

        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "list";
        $viewData->userData             = $this->userData;
        $viewData->getApps        = $apps;
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }


    public function isActiveSetter($appcode)
    {
        if ($this->userData === false) {
            return false;
            exit;
        } else {
            $_appcode = trim(replacePost($appcode));
            if (!isAllowedUpdateApp($_appcode)) {
                return false;
                exit;
            } else {
                if ($_appcode) {
                    $isActive = ($this->input->post("data") === "true") ? 1 : 0;
                    $this->appsettings_model->update(
                        array(
                            "a_appcode"  => $_appcode
                        ),
                        array(
                            "a_status" => $isActive
                        )
                    );
                    return true;
                }
            }
        }
    }

    public function grouplist($appcode)
    {
        if ($this->userData === false) {
            redirect(base_url("login"));
            exit;
        }

        if (!isDbAllowedUpdateModule()) {
            $alert = array(
                "title" => "Yetkisiz Erişim!",
                "text"  => "Modüle Erişim Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url($this->router->fetch_class()));
            exit;
        }

        $_appcode = trim(replacePost($appcode));
        if (!isAllowedUpdateApp($_appcode)) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Düzenlenecek Uygulama Bulunamadı. İlgili Uygulamayı Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("appsettings"));
            exit;
        }

        $app = $this->appsettings_model->get(
            array(
                "a_appcode"         => $_appcode,
                "a_status !="      => -1
            )
        );

        if (!$app) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Düzenlenecek Uygulama Bulunamadı. İlgili Uygulamayı Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('appsettings'));
            exit;
        }

        $groupList = $this->appsettings_model->ek_get_all(
            "r8t_sys_grouplist",
            array(
                "ug_yetkisira >=" => $this->userData->userB->ug_yetkisira,
                "ug_status !="    => -1
            ),
            "ug_yetkisira ASC"
        );


        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "grouplist";
        $viewData->userData             = $this->userData;
        $viewData->groupList      = $groupList;
        $viewData->item           = $app;
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function group_permissions($appcode, $groupid)
    {
        if ($this->userData === false) {
            redirect(base_url("login"));
            exit;
        }

        if (!isDbAllowedUpdateModule()) {
            redirect(base_url($this->router->fetch_class()));
            exit;
        }

        $_appcode   = trim(replacePost($appcode));
        $_groupid   = (int)trim(replacePost($groupid));

        if (!isAllowedUpdateApp($_appcode)) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirilecek Uygulama Bulunamadı. İlgili Uygulamayı Yetkilendirme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("appsettings"));
            exit;
        }

        $app = $this->appsettings_model->get(
            array(
                "a_appcode"         => $_appcode,
                "a_status !="      => -1
            )
        );

        if (!$app) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirilecek Uygulama Bulunamadı. İlgili Uygulamayı Yetkilendirme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('appsettings'));
            exit;
        }

        if ($_groupid <= 0) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirilecek Kullanıcı Grubu Bulunamadı. İlgili Kullanıcı Grubunu Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("appsettings"));
            exit;
        }

        $group = $this->appsettings_model->ek_get(
            "r8t_sys_grouplist",
            array(
                "ug_id"             => $_groupid,
                "ug_yetkisira >="   => $this->userData->userB->ug_yetkisira,
                "ug_status !="      => -1
            )
        );

        if (!$group) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirilecek Kullanıcı Grubu Bulunamadı. İlgili Kullanıcı Grubunu Yetkilendirme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('appsettings'));
            exit;
        }

        $controllerList = getAppControllerList($_appcode);


        if ($controllerList) {
            foreach ($controllerList as $controller) {
                $getDbController  = $this->appsettings_model->ek_get(
                    "r8t_sys_group_permissions",
                    array(
                        "gp_app"        => $_appcode,
                        "gp_groupid"    => $group->ug_id,
                        "gp_controller" => $controller->code
                    )
                );
                if ($getDbController) {
                    //Controller Bilgisini Güncelle
                    $controllerUpdate = $this->appsettings_model->ek_update(
                        "r8t_sys_group_permissions",
                        array(
                            "gp_app"        => $_appcode,
                            "gp_groupid"    => $group->ug_id,
                            "gp_controller" => $controller->code
                        ),
                        array(
                            "gp_json" => json_encode($controller)
                        )
                    );
                } else {
                    //Controller Bilgisini Ekle
                    $controllerAdd = $this->appsettings_model->ek_add(
                        "r8t_sys_group_permissions",
                        array(
                            "gp_app"        => $_appcode,
                            "gp_groupid"    => $group->ug_id,
                            "gp_controller" => $controller->code,
                            "gp_json" => json_encode($controller)
                        )
                    );
                }
            } //FOREACH END
        }


        // Login Olan Kullanıcı Grubu Root İse
        if ($this->userData->userB->ug_id == 1) {
            //db Deki Controllerları Çek
            $controllers = $this->appsettings_model->ek_get_all(
                "r8t_sys_group_permissions",
                array(
                    "gp_app"        => $_appcode,
                    "gp_groupid"    => $group->ug_id
                )
            );
        } else {
            //Login Olan Kullanıcı Grubunun Admin Yetkisi Olduğu Controllerları Çek
            $queryX = "SELECT * FROM r8t_sys_group_permissions WHERE gp_app='{$_appcode}' AND gp_groupid={$group->ug_id}";
            $queryText = "";
            foreach ($controllerList as $controller) {
                if (isAllowedListAppModule($controller->code, $_appcode) == false) {
                    $queryText .= " AND gp_controller!='{$controller->code}'";
                }
            }
            if ($queryText != "") {
                $queryX .= $queryText;
            }

            $controllers = $this->appsettings_model->ek_query_all($queryX);
        }


        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "group_permissions";
        $viewData->userData             = $this->userData;
        $viewData->group           = $group;
        $viewData->app           = $app;
        $viewData->controllerList = $controllers;
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function update_group_permissions($appcode, $groupid)
    {
        if ($this->userData === false) {
            redirect(base_url("login"));
            exit;
        }

        if (!isDbAllowedUpdateModule()) {
            redirect(base_url($this->router->fetch_class()));
            exit;
        }

        $_appcode   = trim(replacePost($appcode));
        $_groupid   = (int)trim(replacePost($groupid));

        if (!isAllowedUpdateApp($_appcode)) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirilecek Uygulama Bulunamadı. İlgili Uygulamayı Yetkilendirme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("appsettings"));
            exit;
        }

        $app = $this->appsettings_model->get(
            array(
                "a_appcode"         => $_appcode,
                "a_status !="      => -1
            )
        );

        if (!$app) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirilecek Uygulama Bulunamadı. İlgili Uygulamayı Yetkilendirme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('appsettings'));
            exit;
        }

        if ($_groupid <= 0) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirilecek Kullanıcı Grubu Bulunamadı. İlgili Kullanıcı Grubunu Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("appsettings"));
            exit;
        }

        $group = $this->appsettings_model->ek_get(
            "r8t_sys_grouplist",
            array(
                "ug_id"             => $_groupid,
                "ug_yetkisira >="   => $this->userData->userB->ug_yetkisira,
                "ug_status !="      => -1
            )
        );

        if (!$group) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirilecek Kullanıcı Grubu Bulunamadı. İlgili Kullanıcı Grubunu Yetkilendirme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('appsettings'));
            exit;
        }


        // Login Olan Kullanıcı Grubu Root İse
        if ($this->userData->userB->ug_id == 1) {
            //db Deki Controllerları Çek
            $controllers = $this->appsettings_model->ek_get_all(
                "r8t_sys_group_permissions",
                array(
                    "gp_app"        => $_appcode,
                    "gp_groupid"    => $group->ug_id
                )
            );
        } else {
            $controllerList = getAppControllerList($_appcode);
            //Login Olan Kullanıcı Grubunun Admin Yetkisi Olduğu Controllerları Çek
            $queryX = "SELECT * FROM r8t_sys_group_permissions WHERE gp_app='{$_apppcode}' AND gp_groupid={$group->ug_id}";
            $queryText = "";
            foreach ($controllerList as $controller) {
                if (isDbAdminViewModule($controller->code) == false) {
                    $queryText .= " AND gp_controller!='{$controller->code}'";
                }
            }
            if ($queryText != "") {
                $queryX .= " AND (gp_controller!='xyz'{$queryText})";
            }
            $controllers = $this->appsettings_model->ek_query_all($queryX);
        }

        //$_permissions = json_encode($this->input->post("permissions"));
        $_permissions = @$this->input->post("permissions");

        foreach ($controllers as $controller) {
            $_update = $this->usergroup_model->ek_update(
                "r8t_sys_group_permissions",
                array(
                    "gp_app"        => $_appcode,
                    "gp_groupid"    => $group->ug_id,
                    "gp_controller" => $controller->gp_controller
                ),
                array(
                    "gp_adminr"     => (isset($_permissions[$controller->gp_controller]['adminr'])) ? 1 : null,
                    "gp_list"       => (isset($_permissions[$controller->gp_controller]['list'])) ? 1 : null,
                    "gp_write"      => (isset($_permissions[$controller->gp_controller]['write'])) ? 1 : null,
                    "gp_update"     => (isset($_permissions[$controller->gp_controller]['update'])) ? 1 : null,
                    "gp_delete"     => (isset($_permissions[$controller->gp_controller]['delete'])) ? 1 : null,
                    "gp_read"       => (isset($_permissions[$controller->gp_controller]['read'])) ? 1 : null,
                )
            );
        }


        if ($_update) {
            $alert = array(
                "title" => "Tebrikler!",
                "text"  => $group->ug_name . " İsimli Kullanıcı Grubu Yetkilendirmesi Başarıyla Güncellendi.",
                "type"  => "success"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("appsettings/grouplist/{$_appcode}"));
        } else {
            $alert = array(
                "title" => "Hata!",
                "text"  => $group->ug_name . " İsimli Kullanıcı Grup Yetkilendirmesi Güncellenemedi.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("appsettings/grouplist/{$_appcode}"));
        }
    }


    public function statulist($appcode)
    {
        if ($this->userData === false) {
            redirect(base_url("login"));
            exit;
        }

        if (!isDbAllowedUpdateModule()) {
            redirect(base_url($this->router->fetch_class()));
            exit;
        }

        $_appcode = trim(replacePost($appcode));
        if (!isAllowedUpdateApp($_appcode)) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Düzenlenecek Uygulama Bulunamadı. İlgili Uygulamayı Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("appsettings"));
            exit;
        }

        $app = $this->appsettings_model->get(
            array(
                "a_appcode"         => $_appcode,
                "a_status !="      => -1
            )
        );

        if (!$app) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Düzenlenecek Uygulama Bulunamadı. İlgili Uygulamayı Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('appsettings'));
            exit;
        }


        $statuList = $this->appsettings_model->ek_get_all(
            "r8t_sys_statulist",
            array(
                "us_yetkisira >=" => $this->userData->userB->us_yetkisira,
                "us_status !="       => -1
            ),
            "us_yetkisira ASC"
        );



        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "statulist";
        $viewData->userData             = $this->userData;
        $viewData->statuList      = $statuList;
        $viewData->item           = $app;
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function statu_permissions($appcode, $statuid)
    {
        if ($this->userData === false) {
            redirect(base_url("login"));
            exit;
        }

        if (!isDbAllowedUpdateModule()) {
            redirect(base_url($this->router->fetch_class()));
            exit;
        }

        $_appcode   = trim(replacePost($appcode));
        $_statuid   = (int)trim(replacePost($statuid));

        if (!isAllowedUpdateApp($_appcode)) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirilecek Uygulama Bulunamadı. İlgili Uygulamayı Yetkilendirme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("appsettings"));
            exit;
        }

        $app = $this->appsettings_model->get(
            array(
                "a_appcode"         => $_appcode,
                "a_status !="      => -1
            )
        );

        if (!$app) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirilecek Uygulama Bulunamadı. İlgili Uygulamayı Yetkilendirme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('appsettings'));
            exit;
        }

        if ($_statuid <= 0) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirilecek Kullanıcı Statüsü Bulunamadı. İlgili Kullanıcı Statüsünü Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("appsettings"));
            exit;
        }

        $statu = $this->appsettings_model->ek_get(
            "r8t_sys_statulist",
            array(
                "us_id"             => $_statuid,
                "us_yetkisira >="   => $this->userData->userB->us_yetkisira,
                "us_status !="      => -1
            )
        );

        if (!$statu) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirilecek Kullanıcı Statüsü Bulunamadı. İlgili Kullanıcı Statüsünü Yetkilendirme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('appsettings'));
            exit;
        }

        $controllerList = getAppControllerList($_appcode);


        if ($controllerList) {
            foreach ($controllerList as $controller) {
                $getDbController  = $this->appsettings_model->ek_get(
                    "r8t_sys_statu_permissions",
                    array(
                        "sp_app"        => $_appcode,
                        "sp_statuid"    => $statu->us_id,
                        "sp_controller" => $controller->code
                    )
                );
                if ($getDbController) {
                    //Controller Bilgisini Güncelle
                    $controllerUpdate = $this->appsettings_model->ek_update(
                        "r8t_sys_statu_permissions",
                        array(
                            "sp_app"        => $_appcode,
                            "sp_statuid"    => $statu->us_id,
                            "sp_controller" => $controller->code
                        ),
                        array(
                            "sp_json" => json_encode($controller)
                        )
                    );
                } else {
                    //Controller Bilgisini Ekle
                    $controllerAdd = $this->appsettings_model->ek_add(
                        "r8t_sys_statu_permissions",
                        array(
                            "sp_app"        => $_appcode,
                            "sp_statuid"    => $statu->us_id,
                            "sp_controller" => $controller->code,
                            "sp_json" => json_encode($controller)
                        )
                    );
                }
            } //FOREACH END
        }


        // Login Olan Kullanıcı Grubu Root İse
        if ($this->userData->userB->ug_id == 1) {
            //db Deki Controllerları Çek
            $controllers = $this->appsettings_model->ek_get_all(
                "r8t_sys_statu_permissions",
                array(
                    "sp_app"        => $_appcode,
                    "sp_statuid"    => $statu->us_id
                )
            );
        } else {
            //Login Olan Kullanıcı Grubunun Admin Yetkisi Olduğu Controllerları Çek
            $queryX = "SELECT * FROM r8t_sys_statu_permissions WHERE sp_app='{$_appcode}' AND sp_statuid={$statu->us_id}";
            $queryText = "";
            foreach ($controllerList as $controller) {
                if (isAllowedListAppModule($controller->code, $_appcode) == false) {
                    $queryText .= " AND sp_controller!='{$controller->code}'";
                }
            }
            if ($queryText != "") {
                $queryX .= " AND (sp_controller!='xyz'{$queryText})";
            }
            $controllers = $this->appsettings_model->ek_query_all($queryX);
        }


        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "statu_permissions";
        $viewData->userData             = $this->userData;
        $viewData->statu           = $statu;
        $viewData->app           = $app;
        $viewData->controllerList = $controllers;
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function update_statu_permissions($appcode, $statuid)
    {
        if ($this->userData === false) {
            redirect(base_url("login"));
            exit;
        }

        if (!isDbAllowedUpdateModule()) {
            redirect(base_url($this->router->fetch_class()));
            exit;
        }

        $_appcode   = trim(replacePost($appcode));
        $_statuid   = (int)trim(replacePost($statuid));

        if (!isAllowedUpdateApp($_appcode)) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirilecek Uygulama Bulunamadı. İlgili Uygulamayı Yetkilendirme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("appsettings"));
            exit;
        }

        $app = $this->appsettings_model->get(
            array(
                "a_appcode"         => $_appcode,
                "a_status !="      => -1
            )
        );

        if (!$app) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirilecek Uygulama Bulunamadı. İlgili Uygulamayı Yetkilendirme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('appsettings'));
            exit;
        }

        if ($_statuid <= 0) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirilecek Kullanıcı Statüsü Bulunamadı. İlgili Kullanıcı Statüsünü Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("appsettings"));
            exit;
        }

        $statu = $this->appsettings_model->ek_get(
            "r8t_sys_statulist",
            array(
                "us_id"             => $_statuid,
                "us_yetkisira >="   => $this->userData->userB->us_yetkisira,
                "us_status !="      => -1
            )
        );

        if (!$statu) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirilecek Kullanıcı Statüsü Bulunamadı. İlgili Kullanıcı Statüsünü Yetkilendirme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('appsettings'));
            exit;
        }


        // Login Olan Kullanıcı Grubu Root İse
        if ($this->userData->userB->ug_id == 1) {
            //db Deki Controllerları Çek
            $controllers = $this->appsettings_model->ek_get_all(
                "r8t_sys_statu_permissions",
                array(
                    "sp_app"        => $_appcode,
                    "sp_statuid"    => $statu->us_id
                )
            );
        } else {
            $controllerList = getAppControllerList($_appcode);
            //Login Olan Kullanıcı Grubunun Admin Yetkisi Olduğu Controllerları Çek
            $queryX = "SELECT * FROM r8t_sys_statu_permissions WHERE sp_app='{$_apppcode}' AND sp_groupid={$statu->us_id}";
            $queryText = "";
            foreach ($controllerList as $controller) {
                if (isDbAdminViewModule($controller->code) == false) {
                    $queryText .= " AND sp_controller!='{$controller->code}'";
                }
            }
            if ($queryText != "") {
                $queryX .= " AND (sp_controller!='xyz'{$queryText})";
            }
            $controllers = $this->appsettings_model->ek_query_all($queryX);
        }

        //$_permissions = json_encode($this->input->post("permissions"));
        $_permissionsX = $this->input->post("kt_docs_statu_repeater");
        $_permissions = array();
        foreach ($_permissionsX as $permsX => $permsA) {
            if (isset($permsA['adminr'][0])) {
                $_permissions[$permsA['controller']]['adminr'] = "on";
            }
            if (isset($permsA['list'][0])) {
                $_permissions[$permsA['controller']]['list'] = "on";
            }
            if (isset($permsA['write'][0])) {
                $_permissions[$permsA['controller']]['write'] = "on";
            }
            if (isset($permsA['update'][0])) {
                $_permissions[$permsA['controller']]['update'] = "on";
            }
            if (isset($permsA['delete'][0])) {
                $_permissions[$permsA['controller']]['delete'] = "on";
            }
            if (isset($permsA['read'][0])) {
                $_permissions[$permsA['controller']]['read'] = "on";
            }
        }

        foreach ($controllers as $controller) {
            $_update = $this->appsettings_model->ek_update(
                "r8t_sys_statu_permissions",
                array(
                    "sp_app"        => $_appcode,
                    "sp_statuid"    => $statu->us_id,
                    "sp_controller" => $controller->sp_controller
                ),
                array(
                    "sp_adminr"     => (isset($_permissions[$controller->sp_controller]['adminr'])) ? 1 : null,
                    "sp_list"       => (isset($_permissions[$controller->sp_controller]['list'])) ? 1 : null,
                    "sp_write"      => (isset($_permissions[$controller->sp_controller]['write'])) ? 1 : null,
                    "sp_update"     => (isset($_permissions[$controller->sp_controller]['update'])) ? 1 : null,
                    "sp_delete"     => (isset($_permissions[$controller->sp_controller]['delete'])) ? 1 : null,
                    "sp_read"       => (isset($_permissions[$controller->sp_controller]['read'])) ? 1 : null,
                )
            );
        }


        if ($_update) {
            $alert = array(
                "title" => "Tebrikler!",
                "text"  => $statu->us_name . " İsimli Kullanıcı Statü Yetkilendirmesi Başarıyla Güncellendi.",
                "type"  => "success"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("appsettings/statulist/{$_appcode}"));
        } else {
            $alert = array(
                "title" => "Hata!",
                "text"  => $statu->us_name . " İsimli Kullanıcı Statü Yetkilendirmesi Güncellenemedi.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("appsettings/statulist/{$_appcode}"));
        }
    }
}
