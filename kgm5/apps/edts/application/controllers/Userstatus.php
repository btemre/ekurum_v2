<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Userstatus extends CI_Controller
{
    public $viewFolder     = "";
    public $userData        = false;

    public function __construct()
    {
        parent::__construct();
        $this->viewFolder = "userstatus_v";
        $this->load->model("userstatus_model");

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


        $statuList = $this->userstatus_model->get_all(
            array(
                "us_yetkisira >=" => $this->userData->userB->us_yetkisira,
                "us_status !="       => -1
            ),
            "us_yetkisira ASC"
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
                        $this->userstatus_model->update(
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

        $statuList = $this->userstatus_model->get_all(
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
            $this->userstatus_model->update(
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
        $this->form_validation->set_rules("name", "Statü Adı", "required|trim|min_length[3]|max_length[30]");
        $this->form_validation->set_rules("code", "Statü Kodu", "required|trim|alpha|min_length[2]|max_length[2]|callback_code_validation");
        $this->form_validation->set_rules("color", "Statü Rengi", "required|trim|callback_color_validation");
        $this->form_validation->set_rules("description", "Statü Açıklaması", "required|trim|min_length[5]|max_length[255]");

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
            $viewData->subViewFolder    = "add";
            $viewData->userData             = $this->userData;
            $viewData->form_error     = true;
            $viewData->colorVal             = $colorVal;

            $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        } else {

            $_name        = trim(replacePost($this->input->post("name")));
            $_code        = trim(replacePost($this->input->post("code")));
            $_color       = trim(replacePost($this->input->post("color")));
            $_description = trim(replacePost($this->input->post("description")));

            $save = $this->userstatus_model->add(
                array(
                    "us_name"         => $_name,
                    "us_code"         => $_code,
                    "us_color"        => $_color,
                    "us_description"  => $_description,
                    "us_yetkisira"    => 9999,
                    "us_status"       => 0,
                    "us_adddate"      => dateToTime(date("Y-m-d H:i:s")),
                    "us_adduser"      => $this->userData->userB->u_id
                )
            );

            if ($save) {
                $alert = array(
                    "title" => "Tebrikler!",
                    "text"  => "$_name İsimli Kullanıcı Statüsü Başarıyla Kaydedildi.",
                    "type"  => "success"
                );
                $this->session->set_flashdata("alertToastr", $alert);
                redirect(base_url('userstatus'));
            } else {
                $alert = array(
                    "title" => "Hata!",
                    "text"  => "$_name İsimli Kullanıcı Statü Kaydı Yapılamadı.",
                    "type"  => "error"
                );
                $this->session->set_flashdata("alertToastr", $alert);
                redirect(base_url('userstatus'));
            }
        }
    }

    public function color_validation($color = false)
    {
        $_color = false;
        switch ($color) {
            case "success":
                $_color = true;
                break;
            case "secondary":
                $_color = true;
                break;
            case "primary":
                $_color = true;
                break;
            case "info":
                $_color = true;
                break;
            case "danger":
                $_color = true;
                break;
            case "warning":
                $_color = true;
                break;
            default:
                $_color = false;
        }
        if ($_color == false) {
            $this->form_validation->set_message('color_validation', '<b>{field}</b> geçerli değil');
            return false;
        } else {
            return true;
        }
    }

    public function code_validation($code = false)
    {
        $_code = trim($code);
        $regex_uppercase = '/[A-Z]/';
        $validX = true;
        $validText = "";
        if (preg_match_all($regex_uppercase, $_code) < 1) {
            $validX = FALSE;
            $validText .= "<b>{field}</b> alanı sadece büyük harf içermelidir.<br>";
        }
        if ($validX == FALSE) {
            $this->form_validation->set_message('code_validation', $validText);
            return FALSE;
        }
        return TRUE;
    }

    public function update_form($id)
    {
        if ($this->userData === false) {
            redirect(base_url("login"));
            exit;
        }

        if (!isDbAllowedUpdateModule()) {
            redirect(base_url($this->router->fetch_class()));
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
            redirect(base_url("userstatus"));
            exit;
        }

        $item = $this->userstatus_model->get(
            array(
                "us_id"             => $_id,
                "us_yetkisira >="   => $this->userData->userB->us_yetkisira,
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
            redirect(base_url('userstatus'));
            exit;
        }

        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "update";
        $viewData->userData             = $this->userData;
        $viewData->item           = $item;
        $viewData->colorVal             = $item->us_color;
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function update($id)
    {
        if ($this->userData === false) {
            return false;
            exit;
        }

        if (!isDbAllowedUpdateModule()) {
            redirect(base_url($this->router->fetch_class()));
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
            redirect(base_url("userstatus"));
            exit;
        }

        $item = $this->userstatus_model->get(
            array(
                "us_id"             => $_id,
                "us_yetkisira >="   => $this->userData->userB->us_yetkisira,
                "us_status !="      => -1
            )
        );

        if (!$item) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Düzenlenecek Kullanıcı Statüsü Bulunamadı. İlgili Kullanıcı Statüsünu Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('userstatus'));
            exit;
        }


        $this->load->library("form_validation");

        // Kurallar Yazılır..
        $this->form_validation->set_rules("name", "Grup Adı", "required|trim|min_length[3]|max_length[30]");
        $this->form_validation->set_rules("code", "Grup Kodu", "required|trim|alpha|min_length[2]|max_length[2]|callback_code_validation");
        $this->form_validation->set_rules("color", "Grup Rengi", "required|trim|callback_color_validation");
        $this->form_validation->set_rules("description", "Grup Açıklaması", "required|trim|min_length[5]|max_length[255]");

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
            $viewData->colorVal             = $colorVal;

            $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        } else {

            $_name        = trim(replacePost($this->input->post("name")));
            $_code        = trim(replacePost($this->input->post("code")));
            $_color       = trim(replacePost($this->input->post("color")));
            $_description = trim(replacePost($this->input->post("description")));

            $save = $this->userstatus_model->update(
                array(
                    "us_id"             => $_id,
                    "us_yetkisira >="   => $this->userData->userB->us_yetkisira,
                    "us_status !="      => -1
                ),
                array(
                    "us_name"         => $_name,
                    "us_code"         => $_code,
                    "us_color"        => $_color,
                    "us_description"  => $_description,
                    "us_editdate"     => dateToTime(date("Y-m-d H:i:s")),
                    "us_edituser"     => $this->userData->userB->u_id
                )
            );

            if ($save) {
                $alert = array(
                    "title" => "Tebrikler!",
                    "text"  => "$_name İsimli Kullanıcı Statüsü Başarıyla Güncellendi.",
                    "type"  => "success"
                );
                $this->session->set_flashdata("alertToastr", $alert);
                redirect(base_url('userstatus'));
            } else {
                $alert = array(
                    "title" => "Hata!",
                    "text"  => "$_name İsimli Kullanıcı Grup Güncellenemedi.",
                    "type"  => "error"
                );
                $this->session->set_flashdata("alertToastr", $alert);
                redirect(base_url('userstatus'));
            }
        }
    }



    public function remove($id)
    {
        if ($this->userData === false) {
            redirect(base_url("login"));
            exit;
        }

        if (!isDbAllowedDeleteModule()) {
            redirect(base_url($this->router->fetch_class()));
            exit;
        }


        $_id = (int)trim(replacePost($id));
        if ($_id <= 0) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Silinecek Kullanıcı Statüsü Bulunamadı. İlgili Kullanıcı Statüsünu Silme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("userstatus"));
            exit;
        }

        $_isStatusInUser = $this->userstatus_model->ek_get(
            "r8t_users",
            array(
                "u_group" => $_id
            )
        );

        if ($_isStatusInUser) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Silinecek Statüye Bağlı Kullanıcı Kaydı Bulunmaktadır. Lütfen Önce Statüye Bağlı Kullanıcıları Güncelleyiniz.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alert", $alert);
            redirect(base_url("userstatus"));
            exit;
        }

        $_delete = $this->userstatus_model->update(
            array(
                "us_id"  => $_id,
                "us_yetkisira >=" => $this->userData->userB->us_yetkisira
            ),
            array(
                "us_status" => -1
            )
        );

        if ($_delete) {
            $alert = array(
                "title" => "Tebrikler!",
                "text"  => "Kullanıcı Statüsü Başarıyla Silindi.",
                "type"  => "success"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('userstatus'));
        } else {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Kullanıcı Grup Silinemedi.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('userstatus'));
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
            redirect(base_url("userstatus"));
            exit;
        }


        $item = $this->userstatus_model->get(
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
            redirect(base_url("userstatus"));
            exit;
        }

        $controllerList = getSysControllerList();

        if ($controllerList) {
            foreach ($controllerList as $controller) {
                $getDbController  = $this->userstatus_model->ek_get(
                    "r8t_sys_statu_permissions",
                    array(
                        "sp_app"        => "sys",
                        "sp_statuid"    => $item->us_id,
                        "sp_controller" => $controller->code
                    )
                );
                if ($getDbController) {
                    //Controller Bilgisini Güncelle
                    $controllerUpdate = $this->userstatus_model->ek_update(
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
                    $controllerAdd = $this->userstatus_model->ek_add(
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
            $controllers = $this->userstatus_model->ek_get_all(
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
            $controllers = $this->userstatus_model->ek_query_all($queryX);
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
            redirect(base_url("userstatus"));
            exit;
        }

        if (!is_array($this->input->post("kt_docs_statu_repeater"))) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirme Yapılacak Kullanıcı Statüsü Bulunamadı. İlgili Kullanıcı Statüsünu Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("userstatus"));
            exit;
        }

        $item = $this->userstatus_model->get(
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
            redirect(base_url("userstatus"));
            exit;
        }

        // Login Olan Kullanıcı Grubu Root İse
        if ($this->userData->userB->us_id == 1) {
            //db Deki Controllerları Çek
            $controllers = $this->userstatus_model->ek_get_all(
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
            $controllers = $this->userstatus_model->ek_query_all($queryX);
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
            $_update = $this->userstatus_model->ek_update(
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
            redirect(base_url("userstatus"));
        } else {
            $alert = array(
                "title" => "Hata!",
                "text"  => $item->us_name . " İsimli Kullanıcı Statüsü Yetkilendirmesi Güncellenemedi.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("userstatus"));
        }
    }
}
