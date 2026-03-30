<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Usergroup extends CI_Controller
{
    public $viewFolder     = "";
    public $userData        = false;

    public function __construct()
    {
        parent::__construct();
        $this->viewFolder = "usergroup_v";
        $this->load->model("usergroup_model");

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

        $groupList = $this->usergroup_model->get_all(
            array(
                "ug_yetkisira >=" => $this->userData->userB->ug_yetkisira,
                "ug_status !="    => -1
            ),
            "ug_yetkisira ASC"
        );

        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "list";
        $viewData->userData             = $this->userData;
        $viewData->groupList      = $groupList;
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
                    if ($_id == $this->userData->userB->ug_id) {
                        return false;
                    } else {
                        $isActive = ($this->input->post("data") === "true") ? 1 : 0;
                        $this->usergroup_model->update(
                            array(
                                "ug_id"  => $_id,
                                "ug_yetkisira >=" => $this->userData->userB->ug_yetkisira
                            ),
                            array(
                                "ug_status" => $isActive
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

        $groupList = $this->usergroup_model->get_all(
            array(
                "ug_yetkisira <=" => $this->userData->userB->ug_yetkisira,
                "ug_status !="    => -1,
                "ug_id !="        => 1
            ),
            "ug_yetkisira ASC"
        );

        $_count = count($groupList) - 1;

        $_items[] = array();
        for ($i = 0; $i <= $_count; $i++) {
            $_items[$i] = $groupList[$i]->ug_id;
        }
        for ($_x = 0; $_x <= count($items) - 1; $_x++) {
            $_items[$i] = $items[$_x];
            $i++;
        }

        foreach ($_items as $rank => $id) {
            $_rank = (int)trim(replacePost($rank));
            $_id = (int)trim(replacePost($id));
            $this->usergroup_model->update(
                array(
                    "ug_id"              => $_id,
                    "ug_yetkisira !="    => $_rank
                ),
                array(
                    "ug_yetkisira"    => $_rank
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

            $save = $this->usergroup_model->add(
                array(
                    "ug_name"         => $_name,
                    "ug_code"         => $_code,
                    "ug_color"        => $_color,
                    "ug_description"  => $_description,
                    "ug_yetkisira"    => 9999,
                    "ug_status"       => 0,
                    "ug_adddate"      => dateToTime(date("Y-m-d H:i:s")),
                    "ug_adduser"      => $this->userData->userB->u_id
                )
            );

            if ($save) {
                $alert = array(
                    "title" => "Tebrikler!",
                    "text"  => "$_name İsimli Kullanıcı Grubu Başarıyla Kaydedildi.",
                    "type"  => "success"
                );
                $this->session->set_flashdata("alertToastr", $alert);
                redirect(base_url('usergroup'));
            } else {
                $alert = array(
                    "title" => "Hata!",
                    "text"  => "$_name İsimli Kullanıcı Grup Kaydı Yapılamadı.",
                    "type"  => "error"
                );
                $this->session->set_flashdata("alertToastr", $alert);
                redirect(base_url('usergroup'));
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
                "text"  => "Düzenlenecek Kullanıcı Grubu Bulunamadı. İlgili Kullanıcı Grubunu Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("usergroup"));
            exit;
        }

        $item = $this->usergroup_model->get(
            array(
                "ug_id"             => $_id,
                "ug_yetkisira >="   => $this->userData->userB->ug_yetkisira,
                "ug_status !="      => -1
            )
        );

        if (!$item) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Düzenlenecek Kullanıcı Grubu Bulunamadı. İlgili Kullanıcı Grubunu Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('usergroup'));
            exit;
        }

        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "update";
        $viewData->userData             = $this->userData;
        $viewData->item           = $item;
        $viewData->colorVal             = $item->ug_color;
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function update($id)
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
                "text"  => "Düzenlenecek Kullanıcı Grubu Bulunamadı. İlgili Kullanıcı Grubunu Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("usergroup"));
            exit;
        }

        $item = $this->usergroup_model->get(
            array(
                "ug_id"             => $_id,
                "ug_yetkisira >="   => $this->userData->userB->ug_yetkisira,
                "ug_status !="      => -1
            )
        );

        if (!$item) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Düzenlenecek Kullanıcı Grubu Bulunamadı. İlgili Kullanıcı Grubunu Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('usergroup'));
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

            $_update = $this->usergroup_model->update(
                array(
                    "ug_id"             => $_id,
                    "ug_yetkisira >="   => $this->userData->userB->ug_yetkisira,
                    "ug_status !="      => -1
                ),
                array(
                    "ug_name"         => $_name,
                    "ug_code"         => $_code,
                    "ug_color"        => $_color,
                    "ug_description"  => $_description,
                    "ug_editdate"     => dateToTime(date("Y-m-d H:i:s")),
                    "ug_edituser"     => $this->userData->userB->u_id
                )
            );

            if ($_update) {
                $alert = array(
                    "title" => "Tebrikler!",
                    "text"  => "$_name İsimli Kullanıcı Grubu Başarıyla Güncellendi.",
                    "type"  => "success"
                );
                $this->session->set_flashdata("alertToastr", $alert);
                redirect(base_url('usergroup'));
            } else {
                $alert = array(
                    "title" => "Hata!",
                    "text"  => "$_name İsimli Kullanıcı Grup Güncellenemedi.",
                    "type"  => "error"
                );
                $this->session->set_flashdata("alertToastr", $alert);
                redirect(base_url('usergroup'));
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
                "text"  => "Silinecek Kullanıcı Grubu Bulunamadı. İlgili Kullanıcı Grubunu Silme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("usergroup"));
            exit;
        }

        $_isGroupInUser = $this->usergroup_model->ek_get(
            "r8t_users",
            array(
                "u_group" => $_id
            )
        );

        if ($_isGroupInUser) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Silinecek Gruba Bağlı Kullanıcı Kaydı Bulunmaktadır. Lütfen Önce Gruba Bağlı Kullanıcıları Güncelleyiniz.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alert", $alert);
            redirect(base_url("usergroup"));
            exit;
        }

        $_delete = $this->usergroup_model->update(
            array(
                "ug_id"  => $_id,
                "ug_yetkisira >=" => $this->userData->userB->ug_yetkisira
            ),
            array(
                "ug_status" => -1
            )
        );

        if ($_delete) {
            $alert = array(
                "title" => "Tebrikler!",
                "text"  => "Kullanıcı Grubu Başarıyla Silindi.",
                "type"  => "success"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('usergroup'));
        } else {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Kullanıcı Grup Silinemedi.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('usergroup'));
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
                "text"  => "Yetkilendirme Yapılacak Kullanıcı Grubu Bulunamadı. İlgili Kullanıcı Grubunu Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("usergroup"));
            exit;
        }


        $item = $this->usergroup_model->get(
            array(
                "ug_id"             => $_id,
                "ug_yetkisira >="   => $this->userData->userB->ug_yetkisira,
                "ug_status !="      => -1
            )
        );

        if (!$item) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirme Yapılacak Kullanıcı Grubu Bulunamadı. İlgili Kullanıcı Grubunu Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('usergroup'));
            exit;
        }

        $controllerList = getSysControllerList();


        if ($controllerList) {
            foreach ($controllerList as $controller) {
                $getDbController  = $this->usergroup_model->ek_get(
                    "r8t_sys_group_permissions",
                    array(
                        "gp_app"        => "sys",
                        "gp_groupid"    => $item->ug_id,
                        "gp_controller" => $controller->code
                    )
                );
                if ($getDbController) {
                    //Controller Bilgisini Güncelle
                    $controllerUpdate = $this->usergroup_model->ek_update(
                        "r8t_sys_group_permissions",
                        array(
                            "gp_app"        => "sys",
                            "gp_groupid"    => $item->ug_id,
                            "gp_controller" => $controller->code
                        ),
                        array(
                            "gp_json" => json_encode($controller)
                        )
                    );
                } else {
                    //Controller Bilgisini Ekle
                    $controllerAdd = $this->usergroup_model->ek_add(
                        "r8t_sys_group_permissions",
                        array(
                            "gp_app"        => "sys",
                            "gp_groupid"    => $item->ug_id,
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
            $controllers = $this->usergroup_model->ek_get_all(
                "r8t_sys_group_permissions",
                array(
                    "gp_app"        => "sys",
                    "gp_groupid"    => $item->ug_id
                )
            );
        } else {
            //Login Olan Kullanıcı Grubunun Admin Yetkisi Olduğu Controllerları Çek
            $queryX = "SELECT * FROM r8t_sys_group_permissions WHERE gp_app='sys' AND gp_groupid={$item->ug_id}";
            $queryText = "";
            foreach ($controllerList as $controller) {
                if (isDbAdminViewModule($controller->code) == false) {
                    $queryText .= " AND gp_controller!='{$controller->code}'";
                }
            }
            if ($queryText != "") {
                $queryX .= " AND (gp_controller!='xyz'{$queryText})";
            }
            $controllers = $this->usergroup_model->ek_query_all($queryX);
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
                "text"  => "Yetkilendirme Yapılacak Kullanıcı Grubu Bulunamadı. İlgili Kullanıcı Grubunu Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("usergroup"));
            exit;
        }

        if (!is_array($this->input->post("permissions"))) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirme Yapılacak Kullanıcı Grubu Bulunamadı. İlgili Kullanıcı Grubunu Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('usergroup'));
            exit;
        }

        $item = $this->usergroup_model->get(
            array(
                "ug_id"             => $_id,
                "ug_yetkisira >="   => $this->userData->userB->ug_yetkisira,
                "ug_status !="      => -1
            )
        );

        if (!$item) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirme Yapılacak Kullanıcı Grubu Bulunamadı. İlgili Kullanıcı Grubunu Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('usergroup'));
            exit;
        }

        // Login Olan Kullanıcı Grubu Root İse
        if ($this->userData->userB->ug_id == 1) {
            //db Deki Controllerları Çek
            $controllers = $this->usergroup_model->ek_get_all(
                "r8t_sys_group_permissions",
                array(
                    "gp_app"        => "sys",
                    "gp_groupid"    => $item->ug_id
                )
            );
        } else {
            $controllerList = getSysControllerList();
            //Login Olan Kullanıcı Grubunun Admin Yetkisi Olduğu Controllerları Çek
            $queryX = "SELECT * FROM r8t_sys_group_permissions WHERE gp_app='sys' AND gp_groupid={$item->ug_id}";
            $queryText = "";
            foreach ($controllerList as $controller) {
                if (isDbAdminViewModule($controller->code) == false) {
                    $queryText .= " AND gp_controller!='{$controller->code}'";
                }
            }
            if ($queryText != "") {
                $queryX .= " AND (gp_controller!='xyz'{$queryText})";
            }
            $controllers = $this->usergroup_model->ek_query_all($queryX);
        }


        //$_permissions = json_encode($this->input->post("permissions"));
        $_permissions = $this->input->post("permissions");

        foreach ($controllers as $controller) {
            $_update = $this->usergroup_model->ek_update(
                "r8t_sys_group_permissions",
                array(
                    "gp_app"        => "sys",
                    "gp_groupid"    => $item->ug_id,
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
                "text"  => $item->ug_name . " İsimli Kullanıcı Grubu Yetkilendirmesi Başarıyla Güncellendi.",
                "type"  => "success"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('usergroup'));
        } else {
            $alert = array(
                "title" => "Hata!",
                "text"  => $item->ug_name . " İsimli Kullanıcı Grup Yetkilendirmesi Güncellenemedi.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('usergroup'));
        }
    }



    public function app_permissions($id)
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
                "text"  => "Yetkilendirme Yapılacak Kullanıcı Grubu Kaydı Bulunamadı. İlgili Kullanıcı Grubunu Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("usergroup"));
            exit;
        }


        $item = $this->usergroup_model->get(
            array(
                "ug_id"             => $_id,
                "ug_yetkisira >="   => $this->userData->userB->ug_yetkisira,
                "ug_status !="      => -1
            )
        );

        if (!$item) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirme Yapılacak Kullanıcı Grubu Bulunamadı. İlgili Kullanıcı Grubunu Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('usergroup'));
            exit;
        }

        $getApps = getAppList();

        $appList = $this->usergroup_model->appUpdate($getApps);

        if ($appList) {
            foreach ($appList as $app) {
                $getDbPermissions = $this->usergroup_model->ek_get(
                    "r8t_sys_group_app_permissions",
                    array(
                        "gap_appzone"    => "sys",
                        "gap_groupid"     => $item->ug_id,
                        "gap_appcode"    => $app->a_appcode
                    )
                );
                if ($getDbPermissions) {
                    //Birim-Uygulama Düzeyinde Permission Kaydı Varsa Başlıkları Güncelle
                    $update = $this->usergroup_model->ek_update(
                        "r8t_sys_group_app_permissions",
                        array(
                            "gap_appzone"    => "sys",
                            "gap_groupid"     => $item->ug_id,
                            "gap_appcode"    => $app->a_appcode
                        ),
                        array(
                            "gap_json"       => json_encode($app)
                        )
                    );
                } else {
                    $add = $this->usergroup_model->ek_add(
                        "r8t_sys_group_app_permissions",
                        array(
                            "gap_appzone"      => "sys",
                            "gap_groupid"       => $item->ug_id,
                            "gap_appcode"      => $app->a_appcode,
                            "gap_json"         => json_encode($app)
                        )
                    );
                }
            }
        }

        // Login Olan Kullanıcı Grubu Root İse
        if ($this->userData->userB->ug_id == 1) {
            //DB Deki Controllerları Çek
            $apps = $this->usergroup_model->ek_join_get_all(
                "r8t_sys_group_app_permissions",
                array(
                    "r8t_sys_apps" => "r8t_sys_apps.a_appcode = r8t_sys_group_app_permissions.gap_appcode"
                ),
                "INNER",
                array(
                    "gap_appzone"  => "sys",
                    "gap_groupid"  => $item->ug_id,
                    "r8t_sys_apps.a_status !="    => -1
                ),
                false
            );
        } else {
            //Login Olan Kullanıcı Grubunun Admin Yetkisi Olduğu Controllerları Çek
            $queryX = "SELECT * FROM r8t_sys_group_app_permissions AS GAP INNER JOIN r8t_sys_apps AS APP ON APP.a_appcode= GAP.gap_appcode WHERE APP.a_status!=-1 AND GAP.gap_appzone='sys' AND GAP.gap_groupid={$item->ug_id}";
            $queryText = "";
            foreach ($appList as $app) {
                if (isAdminViewApp($app->a_appcode) == false) {
                    $queryText .= " AND GAP.gap_appcode!='{$app->a_appcode}'";
                }
            }
            if ($queryText != "") {
                $queryX .= " AND (GAP.gap_appcode!='xyz'{$queryText})";
            }
            $apps = $this->usergroup_model->ek_query_all($queryX);
        }

        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "app_permissions";
        $viewData->userData             = $this->userData;
        $viewData->item           = $item;
        $viewData->getApps        = $apps;
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function update_app_permissions($id)
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
                "text"  => "Yetkilendirme Yapılacak Kullanıcı Grubu Kaydı Bulunamadı. İlgili Kullanıcı Grubunu Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("usergroup"));
            exit;
        }


        $item = $this->usergroup_model->get(
            array(
                "ug_id"             => $_id,
                "ug_yetkisira >="   => $this->userData->userB->ug_yetkisira,
                "ug_status !="      => -1
            )
        );

        if (!$item) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Yetkilendirme Yapılacak Kullanıcı Grubu Bulunamadı. İlgili Kullanıcı Grubunu Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('usergroup'));
            exit;
        }

        $getApps = getAppList();

        $appList = $this->usergroup_model->appUpdate($getApps);



        // Login Olan Kullanıcı Grubu Root İse
        if ($this->userData->userB->ug_id == 1) {
            //DB Deki Controllerları Çek
            $apps = $this->usergroup_model->ek_join_get_all(
                "r8t_sys_group_app_permissions",
                array(
                    "r8t_sys_apps" => "r8t_sys_apps.a_appcode = r8t_sys_group_app_permissions.gap_appcode"
                ),
                "INNER",
                array(
                    "gap_appzone"  => "sys",
                    "gap_groupid"  => $item->ug_id,
                    "r8t_sys_apps.a_status !="    => -1
                ),
                false
            );
        } else {

            //Login Olan Kullanıcı Grubunun Admin Yetkisi Olduğu Controllerları Çek
            $queryX = "SELECT * FROM r8t_sys_group_app_permissions AS GAP INNER JOIN r8t_sys_apps AS APP ON APP.a_appcode= GAP.gap_appcode WHERE APP.a_status!=-1 AND GAP.gap_appzone='sys' AND GAP.gap_groupid={$item->ug_id}";
            $queryText = "";
            foreach ($appList as $app) {
                if (isAdminViewApp($app->a_appcode) == false) {
                    $queryText .= " AND GAP.gap_appcode!='{$app->a_appcode}'";
                }
            }
            if ($queryText != "") {
                $queryX .= " AND (GAP.gap_appcode!='xyz'{$queryText})";
            }

            $apps = $this->usergroup_model->ek_query_all($queryX);
        }


        $_permissions = $this->input->post("permissions");


        foreach ($apps as $app) {
            $_update = $this->usergroup_model->ek_update(
                "r8t_sys_group_app_permissions",
                array(
                    "gap_appzone"    => "sys",
                    "gap_groupid"    => $item->ug_id,
                    "gap_appcode"    => $app->gap_appcode
                ),
                array(
                    "gap_adminr"     => (isset($_permissions[$app->gap_appcode]['adminr'])) ? 1 : null,
                    "gap_list"       => (isset($_permissions[$app->gap_appcode]['list'])) ? 1 : null,
                    "gap_write"      => (isset($_permissions[$app->gap_appcode]['write'])) ? 1 : null,
                    "gap_update"     => (isset($_permissions[$app->gap_appcode]['update'])) ? 1 : null,
                    "gap_delete"     => (isset($_permissions[$app->gap_appcode]['delete'])) ? 1 : null,
                    "gap_read"       => (isset($_permissions[$app->gap_appcode]['read'])) ? 1 : null,
                )
            );
        }


        if ($_update) {
            $alert = array(
                "title" => "Tebrikler!",
                "text"  => $item->ug_name . " - Uygulama Yetkilendirmesi Başarıyla Güncellendi.",
                "type"  => "success"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('usergroup'));
        } else {
            $alert = array(
                "title" => "Hata!",
                "text"  => $item->ug_name . " - Uygulama Yetkilendirmesi Güncellenemedi.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('usergroup'));
        }
    }
}
