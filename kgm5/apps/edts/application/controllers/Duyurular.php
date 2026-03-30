<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Duyurular extends CI_Controller
{
    public $viewFolder     = "";
    public $userData        = false;

    public function __construct()
    {
        parent::__construct();
        $this->viewFolder = "duyurular_v";
        $this->load->model("duyurular_model");

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

        $statuList = $this->duyurular_model->get_all(
            array(
                "us_status !="       => -1
            ),
            "us_adddate DESC"
        );


        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "list";
        $viewData->userData             = $this->userData;
        $viewData->statuList      = $statuList;
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }


    public function isActiveSetter($id)
    {
        if ($this->userData === false) {
            return false;
            exit;
        } else {
            if (!isDbAllowedUpdateModule()) {
                return false;
                exit;
            } else {
                $_id = (int)trim(replacePost($id));
                if ($_id) {
                    if ($_id == $this->userData->userB->us_id) {
                        return false;
                    } else {
                        $isActive = ($this->input->post("data") === "true") ? 1 : 0;
                        $this->duyurular_model->update(
                            array(
                                "us_id"  => $_id,
                                "us_yetkisira >=" => $this->userData->userB->us_yetkisira
                            ),
                            array(
                                "us_status" => $isActive
                            )
                        );
                        return true;
                    }
                }
            }
        }
    }

    public function rankSetter()
    {
        if ($this->userData === false) {
            return false;
            exit;
        }

        if (!isDbAllowedUpdateModule()) {
            return false;
            exit;
        }

        $data = $this->input->post("data");

        parse_str($data, $order);

        $items = $order["ord"];

        $statuList = $this->duyurular_model->get_all(
            array(
                "us_yetkisira <=" => $this->userData->userB->us_yetkisira,
                "us_status !="       => -1
            ),
            "us_yetkisira ASC"
        );

        $_count = count($statuList) - 1;

        $_items[] = array();
        for ($i = 0; $i <= $_count; $i++) {
            $_items[$i] = $statuList[$i]->us_id;
        }
        for ($_x = 0; $_x <= count($items) - 1; $_x++) {
            $_items[$i] = $items[$_x];
            $i++;
        }

        foreach ($_items as $rank => $id) {
            $_rank = (int)trim(replacePost($rank));
            $_id = (int)trim(replacePost($id));
            $this->duyurular_model->update(
                array(
                    "us_id"              => $_id,
                    "us_yetkisira !="    => $_rank
                ),
                array(
                    "us_yetkisira"    => $_rank
                )
            );
        }
    }

    public function new_form()
    {
        if ($this->userData === false) {
            redirect(base_url("login"));
            exit;
        }

        if (!isDbAllowedWriteModule()) {
            redirect(base_url($this->router->fetch_class()));
            exit;
        }

        $colorVal = "success";
        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "add";
        $viewData->userData             = $this->userData;
        $viewData->colorVal             = $colorVal;
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function save()
    {
        if ($this->userData === false) {
            redirect(base_url("login"));
            exit;
        }

        if (!isDbAllowedWriteModule()) {
            redirect(base_url($this->router->fetch_class()));
            exit;
        }

        $this->load->library("form_validation");

        // Kurallar Yazılır..
        $this->form_validation->set_rules("name", "Duyuru Başlığı", "required|trim|min_length[3]");
        $this->form_validation->set_rules("code", "Duyuru Kodu", "required|trim|min_length[2]");

        $this->form_validation->set_message(
            array(
                "required"    => "<b>{field}</b> alanı doldurulmalıdır.",
                "min_length"  => "<b>{field}</b> en az %s karakterden oluşmalıdır.",
                "max_length"  => "<b>{field}</b> en fazla %s karakterden oluşmalıdır.",
                "alpha"       => "<b>{field}</b> alanı sadece harf değerleri almalıdır.",
            )
        );
        if ($this->form_validation->run() == FALSE) {
            $viewData = new stdClass();
            $viewData->viewFolder     = $this->viewFolder;
            $viewData->subViewFolder    = "add";
            $viewData->userData             = $this->userData;
            $viewData->form_error     = true;

            $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        } else {

            $_name        = trim(replacePost($this->input->post("name")));
            $_code        = trim(replacePost($this->input->post("code")));
            $_description = trim(replacePost($this->input->post("description")));

            $save = $this->duyurular_model->add(
                array(
                    "us_name"         => $_name,
                    "us_code"         => $_code,
                    "us_description"  => $_description,
                    "us_status"       => 1,
                    "us_adddate"      => dateToTime(date("Y-m-d H:i:s")),
                    "us_adduser"      => $this->userData->userB->u_id
                )
            );

            if ($save) {
                $alert = array(
                    "title" => "Tebrikler!",
                    "text"  => "$_name  Başarıyla Kaydedildi.",
                    "type"  => "success"
                );
                $this->session->set_flashdata("alertToastr", $alert);
                redirect(base_url('duyurular'));
            } else {
                $alert = array(
                    "title" => "Hata!",
                    "text"  => "$_name kaydı Yapılamadı.",
                    "type"  => "error"
                );
                $this->session->set_flashdata("alertToastr", $alert);
                redirect(base_url('duyurular'));
            }
        }
    }



    public function update_form($id)
    {
        if ($this->userData === false) {
            redirect(base_url("login"));
            exit;
        }



        $_id = (int)trim(replacePost($id));
        if ($_id <= 0) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Düzenlenecek Kullanıcı Statüsü Bulunamadı. İlgili Kullanıcı Statüsünu Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("duyurular"));
            exit;
        }

        $item = $this->duyurular_model->get(
            array(
                "us_id"             => $_id,
                "us_status !="      => -1
            )
        );

        if (!$item) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Düzenlenecek Kullanıcı Statüsü Bulunamadı. İlgili Kullanıcı Statüsünü Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('duyurular'));
            exit;
        }

        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "update";
        $viewData->userData             = $this->userData;
        $viewData->item           = $item;
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function update($id)
    {
        if ($this->userData === false) {
            return false;
            exit;
        }


        $_id = (int)trim(replacePost($id));
        if ($_id <= 0) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Düzenlenecek Kullanıcı Statüsü Bulunamadı. İlgili Kullanıcı Statüsünu Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("duyurular"));
            exit;
        }

        $item = $this->duyurular_model->get(
            array(
                "us_id"             => $_id,
                "us_status !="      => -1
            )
        );

        if (!$item) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Düzenlenecek kayıt Bulunamadı. İlgili Kullanıcı Statüsünu Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('duyurular'));
            exit;
        }


        $this->load->library("form_validation");

        // Kurallar Yazılır..
        $this->form_validation->set_rules("name", "Ad", "required|trim|min_length[3]");
        $this->form_validation->set_rules("code", "Kod", "required|trim|min_length[2]");
        

        $this->form_validation->set_message(
            array(
                "required"    => "<b>{field}</b> alanı doldurulmalıdır.",
                "min_length"  => "<b>{field}</b> en az %s karakterden oluşmalıdır.",
                "max_length"  => "<b>{field}</b> en fazla %s karakterden oluşmalıdır.",
                "alpha"       => "<b>{field}</b> alanı sadece harf değerleri almalıdır.",
            )
        );
        if ($this->form_validation->run() == FALSE) {
            $colorVal = trim(replacePost($this->input->post("color")));
            $viewData = new stdClass();
            $viewData->viewFolder     = $this->viewFolder;
            $viewData->subViewFolder    = "update";
            $viewData->userData             = $this->userData;
            $viewData->form_error     = true;


            $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        } else {

            $_name        = trim(replacePost($this->input->post("name")));
            $_code        = trim(replacePost($this->input->post("code")));
            $_description = trim(replacePost($this->input->post("description")));

            $save = $this->duyurular_model->update(
                array(
                    "us_id"             => $_id,
                ),
                array(
                    "us_name"         => $_name,
                    "us_code"         => $_code,
                    "us_description"  => $_description,
                    "us_editdate"     => dateToTime(date("Y-m-d H:i:s")),
                    "us_edituser"     => $this->userData->userB->u_id
                )
            );


            if ($save) {
                $alert = array(
                    "title" => "Tebrikler!",
                    "text"  => "$_name  Başarıyla Güncellendi.",
                    "type"  => "success"
                );
                $this->session->set_flashdata("alertToastr", $alert);
                redirect(base_url('duyurular'));
            } else {
                $alert = array(
                    "title" => "Hata!",
                    "text"  => "$_name  Güncellenemedi.",
                    "type"  => "error"
                );
                $this->session->set_flashdata("alertToastr", $alert);
                redirect(base_url('duyurular'));
            }
        }
    }



    public function remove($id)
    {
        if ($this->userData === false) {
            redirect(base_url("login"));
            exit;
        }

 

        $_id = (int)trim(replacePost($id));
        if ($_id <= 0) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Silinecek kayıt Bulunamadı. Silme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("duyurular"));
            exit;
        }



        $_delete = $this->duyurular_model->update(
            array(
                "us_id"  => $_id,
            ),
            array(
                "us_status" => -1
            )
        );

        if ($_delete) {
            $alert = array(
                "title" => "Tebrikler!",
                "text"  => "Kayıt Silindi.",
                "type"  => "success"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('duyurular'));
        } else {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Kayıt Silinemedi.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('duyurular'));
        }
    }



    public function permissions($id)
    {
        if ($this->userData === false) {
            redirect(base_url("login"));
            exit;
        }

        if (!isDbAllowedUpdateModule() || !isDbAdminViewModule()) {
            redirect(base_url($this->router->fetch_class()));
            exit;
        }

        $_id = (int)trim(replacePost($id));
        if ($_id <= 0) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirme Yapılacak Kullanıcı Statüsü Bulunamadı. İlgili Kullanıcı Statüsünu Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("duyurular"));
            exit;
        }


        $item = $this->duyurular_model->get(
            array(
                "us_id"             => $_id,
                "us_yetkisira >="   => $this->userData->userB->us_yetkisira,
                "us_status !="      => -1
            )
        );

        if (!$item) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirme Yapılacak Kullanıcı Statüsü Bulunamadı. İlgili Kullanıcı Statüsünu Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("duyurular"));
            exit;
        }

        $controllerList = getSysControllerList();

        if ($controllerList) {
            foreach ($controllerList as $controller) {
                $getDbController  = $this->duyurular_model->ek_get(
                    "r8t_sys_statu_permissions",
                    array(
                        "sp_app"        => "sys",
                        "sp_statuid"    => $item->us_id,
                        "sp_controller" => $controller->code
                    )
                );
                if ($getDbController) {
                    //Controller Bilgisini Güncelle
                    $controllerUpdate = $this->duyurular_model->ek_update(
                        "r8t_sys_statu_permissions",
                        array(
                            "sp_app"        => "sys",
                            "sp_statuid"    => $item->us_id,
                            "sp_controller" => $controller->code
                        ),
                        array(
                            "sp_json" => json_encode($controller)
                        )
                    );
                } else {
                    //Controller Bilgisini Ekle
                    $controllerAdd = $this->duyurular_model->ek_add(
                        "r8t_sys_statu_permissions",
                        array(
                            "sp_app"        => "sys",
                            "sp_statuid"    => $item->us_id,
                            "sp_controller" => $controller->code,
                            "sp_json" => json_encode($controller)
                        )
                    );
                }
            } //FOREACH END
        }

        // Login Olan Kullanıcı Grubu Root İse
        if ($this->userData->userB->us_id == 1) {
            //db Deki Controllerları Çek
            $controllers = $this->duyurular_model->ek_get_all(
                "r8t_sys_statu_permissions",
                array(
                    "sp_app"        => "sys",
                    "sp_statuid"    => $item->us_id
                )
            );
        } else {
            //Login Olan Kullanıcı Grubunun Admin Yetkisi Olduğu Controllerları Çek
            $queryX = "SELECT * FROM r8t_sys_statu_permissions WHERE sp_app='sys' AND sp_statuid={$item->us_id}";
            $queryText = "";
            foreach ($controllerList as $controller) {
                if (isDbAdminViewModule($controller->code) == false) {
                    $queryText .= " AND sp_controller!='{$controller->code}'";
                }
            }
            if ($queryText != "") {
                $queryX .= " AND (sp_controller!='xyz'{$queryText})";
            }
            $controllers = $this->duyurular_model->ek_query_all($queryX);
        }




        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "permissions";
        $viewData->userData             = $this->userData;
        $viewData->item           = $item;
        $viewData->controllerList = $controllers;
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function update_permissions($id)
    {
        if ($this->userData === false) {
            redirect(base_url("login"));
            exit;
        }

        if (!isDbAllowedUpdateModule() || !isDbAdminViewModule()) {
            redirect(base_url($this->router->fetch_class()));
            exit;
        }

        $_id = (int)trim(replacePost($id));
        if ($_id <= 0) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirme Yapılacak Kullanıcı Statüsü Bulunamadı. İlgili Kullanıcı Statüsünu Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("duyurular"));
            exit;
        }

        if (!is_array($this->input->post("kt_docs_statu_repeater"))) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirme Yapılacak Kullanıcı Statüsü Bulunamadı. İlgili Kullanıcı Statüsünu Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("duyurular"));
            exit;
        }

        $item = $this->duyurular_model->get(
            array(
                "us_id"             => $_id,
                "us_yetkisira >="   => $this->userData->userB->us_yetkisira,
                "us_status !="      => -1
            )
        );

        if (!$item) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirme Yapılacak Kullanıcı Statüsü Bulunamadı. İlgili Kullanıcı Statüsünu Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("duyurular"));
            exit;
        }

        // Login Olan Kullanıcı Grubu Root İse
        if ($this->userData->userB->us_id == 1) {
            //db Deki Controllerları Çek
            $controllers = $this->duyurular_model->ek_get_all(
                "r8t_sys_statu_permissions",
                array(
                    "sp_app"        => "sys",
                    "sp_statuid"    => $item->us_id
                )
            );
        } else {
            $controllerList = getSysControllerList();
            //Login Olan Kullanıcı Grubunun Admin Yetkisi Olduğu Controllerları Çek
            $queryX = "SELECT * FROM r8t_sys_statu_permissions WHERE sp_app='sys' AND sp_statuid={$item->us_id}";
            $queryText = "";
            foreach ($controllerList as $controller) {
                if (isDbAdminViewModule($controller->code) == false) {
                    $queryText .= " AND sp_controller!='{$controller->code}'";
                }
            }
            if ($queryText != "") {
                $queryX .= " AND (sp_controller!='xyz'{$queryText})";
            }
            $controllers = $this->duyurular_model->ek_query_all($queryX);
        }



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
            $_update = $this->duyurular_model->ek_update(
                "r8t_sys_statu_permissions",
                array(
                    "sp_app"        => "sys",
                    "sp_statuid"    => $item->us_id,
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
                "text"  => $item->us_name . " İsimli Kullanıcı Statüsü Yetkilendirmesi Başarıyla Güncellendi.",
                "type"  => "success"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("duyurular"));
        } else {
            $alert = array(
                "title" => "Hata!",
                "text"  => $item->us_name . " İsimli Kullanıcı Statüsü Yetkilendirmesi Güncellenemedi.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("duyurular"));
        }
    }
}
