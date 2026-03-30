<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends CI_Controller
{
    public $viewFolder     = "";
    public $userData        = false;

    public function __construct()
    {
        parent::__construct();
        $this->viewFolder = "users_v";
        $this->load->model("users_model");

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

        $userList = $this->users_model->ek_join_get_all(
            "r8t_users",
            array(
                "r8t_sys_grouplist" => "r8t_sys_grouplist.ug_id = r8t_users.u_group",
                "r8t_sys_statulist"    => "r8t_sys_statulist.us_id = r8t_users.u_statu",
                "r8t_sys_unitlist"  => "r8t_sys_unitlist.ub_id = r8t_users.u_unit"
            ),
            "INNER",
            array(
                "r8t_sys_grouplist.ug_yetkisira >=" => $this->userData->userB->ug_yetkisira,
                "r8t_sys_statulist.us_yetkisira >=" => $this->userData->userB->us_yetkisira
            ),
            "u_id,u_status DESC"
        );

        $groupList = $this->users_model->ek_get_all(
            "r8t_sys_grouplist",
            array(
                "ug_yetkisira >=" => $this->userData->userB->ug_yetkisira,
                "ug_status !="    => -1
            ),
            "ug_yetkisira ASC"
        );

        $statuList = $this->users_model->ek_get_all(
            "r8t_sys_statulist",
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
        $viewData->userList       = $userList;
        $viewData->groupList      = $groupList;
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
                    if ($_id == $this->userData->userB->u_id) {
                        return false;
                    } else {
                        $isActive = ($this->input->post("data") === "true") ? 1 : 0;
                        $isUpdate = $this->users_model->ek_join_get(
                            "r8t_users",
                            array(
                                "r8t_sys_grouplist" => "r8t_sys_grouplist.ug_id = r8t_users.u_group",
                                "r8t_sys_statulist"    => "r8t_sys_statulist.us_id = r8t_users.u_statu"
                            ),
                            "INNER",
                            array(
                                "u_id"  => $_id,
                                "r8t_sys_grouplist.ug_yetkisira >=" => $this->userData->userB->ug_yetkisira,
                                "r8t_sys_statulist.us_yetkisira >=" => $this->userData->userB->us_yetkisira,
                                "u_status !=" => -1
                            ),
                            false
                        );
                        if ($isUpdate) {
                            $this->users_model->update(
                                array(
                                    "u_id"  => $_id,
                                    "u_status !=" => -1
                                ),
                                array(
                                    "u_status" => $isActive
                                )
                            );
                            return true;
                        } else {
                            return false;
                        }
                    }
                }
            }
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


        $unitList = $this->users_model->ek_get_all(
            "r8t_sys_unitlist",
            array(
                "ub_status !="    => -1
            ),
            "ub_title ASC"
        );


        $groupList = $this->users_model->ek_get_all(
            "r8t_sys_grouplist",
            array(
                "ug_yetkisira >=" => $this->userData->userB->ug_yetkisira,
                "ug_status !="    => -1
            ),
            "ug_yetkisira ASC"
        );

        $statuList = $this->users_model->ek_get_all(
            "r8t_sys_statulist",
            array(
                "us_yetkisira >=" => $this->userData->userB->us_yetkisira,
                "us_status !="       => -1
            ),
            "us_yetkisira ASC"
        );

        $istasyonList = $this->users_model->ek_get_all(
            "r8t_sys_istasyonlar",
            array(),
            "kdi_id ASC"
        );

        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "add";
        $viewData->userData             = $this->userData;
        $viewData->unitList       = $unitList;
        $viewData->groupList      = $groupList;
        $viewData->statuList      = $statuList;
        $viewData->istasyonList   = $istasyonList;
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
        $this->form_validation->set_rules("units", "Çalıştığı Birim", "required|trim|numeric|callback_valid_unit");
        $this->form_validation->set_rules("group", "Kullanıcı Grubu", "required|trim|numeric|callback_valid_group");
        $this->form_validation->set_rules("status", "Kullanıcı Statüsü", "required|trim|numeric|callback_valid_statu");
        $this->form_validation->set_rules("istasyon", "İstasyon", "required|trim|numeric|callback_valid_istasyon");
        $this->form_validation->set_rules("name", "Adı", "required|trim|min_length[2]|max_length[30]|callback_valid_stringnospace");
        $this->form_validation->set_rules("lastname", "Diğer Adı", "trim|min_length[2]|max_length[30]|callback_valid_stringnospace");
        $this->form_validation->set_rules("surname", "Soyadı", "required|trim|min_length[2]|max_length[30]|callback_valid_stringnospace");
        $this->form_validation->set_rules("email", "E-mail", "required|trim|valid_email");
        $this->form_validation->set_rules("username", "Kullanıcı Adı", "required|trim|min_length[3]|max_length[25]|callback_valid_username");
        $this->form_validation->set_rules("password", "Parola", "required|trim|min_length[12]|max_length[30]");
        $this->form_validation->set_rules("repassword", "Parola Tekrarı", "required|trim|matches[password]");

        $this->form_validation->set_message(
            array(
                "required"    => "<b>{field}</b> alanı doldurulmalıdır.",
                "min_length"  => "<b>{field}</b> en az %s karakterden oluşmalıdır.",
                "max_length"  => "<b>{field}</b> en fazla %s karakterden oluşmalıdır.",
                "alpha"       => "<b>{field}</b> alanı sadece harf değerleri almalıdır.",
                "numeric"     => "<b>{field}</b> alanı geçerli bir değerler içermiyor.",
                "valid_email" => "Lütfen Geçerli Bir Mail Adresi Giriniz.",
                "matches"     => "Parola ve Tekrarı Birbiri İle Uyuşmuyor."
            )
        );

        if ($this->form_validation->run() == FALSE) {

            $unitList = $this->users_model->ek_get_all(
                "r8t_sys_unitlist",
                array(
                    "ub_status !="    => -1
                ),
                "ub_title ASC"
            );
            $groupList = $this->users_model->ek_get_all(
                "r8t_sys_grouplist",
                array(
                    "ug_yetkisira >=" => $this->userData->userB->ug_yetkisira,
                    "ug_status !="    => -1
                ),
                "ug_yetkisira ASC"
            );
            $statuList = $this->users_model->ek_get_all(
                "r8t_sys_statulist",
                array(
                    "us_yetkisira >=" => $this->userData->userB->us_yetkisira,
                    "us_status !="       => -1
                ),
                "us_yetkisira ASC"
            );

            $viewData = new stdClass();
            $viewData->viewFolder     = $this->viewFolder;
            $viewData->subViewFolder    = "add";
            $viewData->userData             = $this->userData;
            $viewData->form_error     = true;
            $viewData->unitList       = $unitList;
            $viewData->groupList      = $groupList;
            $viewData->statuList      = $statuList;
            $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        } else {

            $_units           = (int)trim(replacePost($this->input->post("units")));
            $_group           = (int)trim(replacePost($this->input->post("group")));
            $_status          = (int)trim(replacePost($this->input->post("status")));
            $_istasyon        = (int)trim(replacePost($this->input->post("istasyon")));
            $_name            = trim(replacePost($this->input->post("name")));
            $_lastname        = trim(replacePost($this->input->post("lastname")));
            $_surname         = trim(replacePost($this->input->post("surname")));
            $_cinsiyet         = trim(replacePost($this->input->post("cinsiyet")));
            $_email           = trim(replacePost($this->input->post("email")));
            $_username        = trim(replacePost($this->input->post("username")));
            $_password        = trim(replacePost($this->input->post("password")));

            $save = $this->users_model->add(
                array(
                    "u_name"          => $_name,
                    "u_lastname"      => $_lastname,
                    "u_surname"       => $_surname,
                    "u_cinsiyet"       => $_cinsiyet,
                    "u_username"      => $_username,
                    "u_password"      => sha1($_password),
                    "u_mail"          => $_email,
                    "u_unit"          => $_units,
                    "u_group"         => $_group,
                    "u_statu"         => $_status,
                    "u_istasyon"      => $_istasyon,
                    "u_repassword"    => 1,
                    "u_addtime"       => dateToTime(date("Y-m-d H:i:s")),
                    "u_adduser"       => $this->userData->userB->u_id
                )
            );
            if ($save) {
                $alert = array(
                    "title" => "Tebrikler!",
                    "text"  => "{$_name} {$_lastname} {$_surname} İsimli Kullanıcı Başarıyla Kaydedildi.",
                    "type"  => "success"
                );
                $this->session->set_flashdata("alertToastr", $alert);
                redirect(base_url('users'));
            } else {
                $alert = array(
                    "title" => "Hata!",
                    "text"  => "{$_name} {$_lastname} {$_surname} İsimli Kullanıcı Kaydı Yapılamadı.",
                    "type"  => "error"
                );
                $this->session->set_flashdata("alertToastr", $alert);
                redirect(base_url('users'));
            }
        }
    }

    public function valid_unit($unitid = false)
    {
        $_unitid = (int)trim(replacePost($unitid));

        $getUnit = $this->users_model->ek_get(
            "r8t_sys_unitlist",
            array(
                "ub_id"         => $_unitid,
                "ub_status !="  => -1
            )
        );
        if ($getUnit) {
            return true;
        } else {
            $this->form_validation->set_message('valid_unit', '<b>{field}</b> alanı geçerli değil');
            return false;
        }
    }

    public function valid_group($groupid = false)
    {
        $_groupid = (int)trim(replacePost($groupid));

        $getGroup = $this->users_model->ek_get(
            "r8t_sys_grouplist",
            array(
                "ug_id"             => $_groupid,
                "ug_status !="      => -1,
                "ug_yetkisira >=" => $this->userData->userB->ug_yetkisira
            )
        );
        if ($getGroup) {
            return true;
        } else {
            $this->form_validation->set_message('valid_group', '<b>{field}</b> alanı geçerli değil');
            return false;
        }
    }

    public function valid_statu($statuid = false)
    {
        $_statuid = (int)trim(replacePost($statuid));

        $getStatu = $this->users_model->ek_get(
            "r8t_sys_statulist",
            array(
                "us_id"             => $_statuid,
                "us_status !="      => -1,
                "us_yetkisira >=" => $this->userData->userB->us_yetkisira
            )
        );
        if ($getStatu) {
            return true;
        } else {
            $this->form_validation->set_message('valid_statu', '<b>{field}</b> alanı geçerli değil');
            return false;
        }
    }

    public function valid_istasyon($istasyonid = false)
    {
        $_istasyonid = (int)trim(replacePost($istasyonid));

        $getIstasyon = $this->users_model->ek_get(
            "r8t_sys_istasyonlar",
            array(
                "kdi_id"             => $_istasyonid
            )
        );
        if ($getIstasyon) {
            return true;
        } else {
            $this->form_validation->set_message('valid_istasyon', '<b>{field}</b> alanı geçerli değil');
            return false;
        }
    }

    public function valid_username($username = '')
    {
        $username = trim(replacePost($username));
        if (preg_match('/^[a-z]\w{4,25}[^_]$/i', $username) == FALSE) {
            $this->form_validation->set_message('valid_username', '<b>{field}</b> alanı sadece harf, rakam ve _ içerebilir.');
            return FALSE;
        }

        $getUsername = $this->users_model->get(
            array(
                "u_username"      => $username
            )
        );
        if ($getUsername) {
            $this->form_validation->set_message('valid_username', 'Bu kullanıcı adı başka kullanıcı tarafından kullanımda.');
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

        if (!isDbAllowedWriteModule()) {
            redirect(base_url($this->router->fetch_class()));
            exit;
        }


        $_id = (int)trim(replacePost($id));
        if ($_id <= 0) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Düzenlenecek Kullanıcı Bulunamadı. İlgili Kullanıcıyı Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("users"));
            exit;
        }

        $item = $this->users_model->ek_join_get(
            "r8t_users",
            array(
                "r8t_sys_grouplist" => "r8t_sys_grouplist.ug_id = r8t_users.u_group",
                "r8t_sys_statulist"    => "r8t_sys_statulist.us_id = r8t_users.u_statu",
                "r8t_sys_unitlist"  => "r8t_sys_unitlist.ub_id = r8t_users.u_unit"
            ),
            "INNER",
            array(
                "u_id"                              => $_id,
                "r8t_sys_grouplist.ug_yetkisira >=" => $this->userData->userB->ug_yetkisira,
                "r8t_sys_statulist.us_yetkisira >=" => $this->userData->userB->us_yetkisira
            ),
            false
        );


        if (!$item) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Düzenlenecek Kullanıcı Bulunamadı. İlgili Kullanıcıyı Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('users'));
            exit;
        }


        $unitList = $this->users_model->ek_get_all(
            "r8t_sys_unitlist",
            array(
                "ub_status !="    => -1
            ),
            "ub_title ASC"
        );


        $groupList = $this->users_model->ek_get_all(
            "r8t_sys_grouplist",
            array(
                "ug_yetkisira >=" => $this->userData->userB->ug_yetkisira,
                "ug_status !="    => -1
            ),
            "ug_yetkisira ASC"
        );

        $statuList = $this->users_model->ek_get_all(
            "r8t_sys_statulist",
            array(
                "us_yetkisira >=" => $this->userData->userB->us_yetkisira,
                "us_status !="       => -1
            ),
            "us_yetkisira ASC"
        );

        $istasyonList = $this->users_model->ek_get_all(
            "r8t_sys_istasyonlar",
            array(),
            "kdi_id ASC"
        );

        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "update";
        $viewData->userData             = $this->userData;
        $viewData->item           = $item;
        $viewData->unitList       = $unitList;
        $viewData->groupList      = $groupList;
        $viewData->statuList      = $statuList;
        $viewData->istasyonList      = $istasyonList;

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
                "text"  => "Düzenlenecek Kullanıcı Bulunamadı. İlgili Kullanıcıyı Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("users"));
            exit;
        }

        $item = $this->users_model->ek_join_get(
            "r8t_users",
            array(
                "r8t_sys_grouplist" => "r8t_sys_grouplist.ug_id = r8t_users.u_group",
                "r8t_sys_statulist"    => "r8t_sys_statulist.us_id = r8t_users.u_statu",
                "r8t_sys_unitlist"  => "r8t_sys_unitlist.ub_id = r8t_users.u_unit"
            ),
            "INNER",
            array(
                "u_id"                              => $_id,
                "r8t_sys_grouplist.ug_yetkisira >=" => $this->userData->userB->ug_yetkisira,
                "r8t_sys_statulist.us_yetkisira >=" => $this->userData->userB->us_yetkisira
            ),
            false
        );


        if (!$item) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Düzenlenecek Kullanıcı Bulunamadı. İlgili Kullanıcıyı Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('users'));
            exit;
        }



        $this->load->library("form_validation");

        // Kurallar Yazılır..
        $this->form_validation->set_rules("units", "Çalıştığı Birim", "required|trim|numeric|callback_valid_unit");
        $this->form_validation->set_rules("group", "Kullanıcı Grubu", "required|trim|numeric|callback_valid_group");
        $this->form_validation->set_rules("status", "Kullanıcı Statüsü", "required|trim|numeric|callback_valid_statu");
        $this->form_validation->set_rules("istasyon", "İstasyon", "required|trim|numeric|callback_valid_istasyon");
        $this->form_validation->set_rules("name", "Adı", "required|trim|min_length[2]|max_length[30]|callback_valid_stringnospace");
        $this->form_validation->set_rules("lastname", "Diğer Adı", "trim|min_length[2]|max_length[30]|callback_valid_stringnospace");
        $this->form_validation->set_rules("surname", "Soyadı", "required|trim|min_length[2]|max_length[30]|callback_valid_stringnospace");
        $this->form_validation->set_rules("email", "E-mail", "required|trim|valid_email");
        $this->form_validation->set_rules("username", "Kullanıcı Adı", "required|trim|min_length[3]|max_length[25]|callback_update_valid_username[$_id]");
        $this->form_validation->set_rules("password", "Parola", "trim|min_length[12]|max_length[30]");
        $this->form_validation->set_rules("repassword", "Parola Tekrarı", "trim|matches[password]");

        $this->form_validation->set_message(
            array(
                "required"    => "<b>{field}</b> alanı doldurulmalıdır.",
                "min_length"  => "<b>{field}</b> en az %s karakterden oluşmalıdır.",
                "max_length"  => "<b>{field}</b> en fazla %s karakterden oluşmalıdır.",
                "alpha"       => "<b>{field}</b> alanı sadece harf değerleri almalıdır.",
                "numeric"     => "<b>{field}</b> alanı geçerli bir değerler içermiyor.",
                "valid_email" => "Lütfen Geçerli Bir Mail Adresi Giriniz.",
                "matches"     => "Parola ve Tekrarı Birbiri İle Uyuşmuyor."
            )
        );

        if ($this->form_validation->run() == FALSE) {

            $unitList = $this->users_model->ek_get_all(
                "r8t_sys_unitlist",
                array(
                    "ub_status !="    => -1
                ),
                "ub_title ASC"
            );
            $groupList = $this->users_model->ek_get_all(
                "r8t_sys_grouplist",
                array(
                    "ug_yetkisira >=" => $this->userData->userB->ug_yetkisira,
                    "ug_status !="    => -1
                ),
                "ug_yetkisira ASC"
            );
            $statuList = $this->users_model->ek_get_all(
                "r8t_sys_statulist",
                array(
                    "us_yetkisira >=" => $this->userData->userB->us_yetkisira,
                    "us_status !="       => -1
                ),
                "us_yetkisira ASC"
            );

            $viewData = new stdClass();
            $viewData->viewFolder     = $this->viewFolder;
            $viewData->subViewFolder    = "update";
            $viewData->userData             = $this->userData;
            $viewData->form_error     = true;
            $viewData->item           = $item;
            $viewData->unitList       = $unitList;
            $viewData->groupList      = $groupList;
            $viewData->statuList      = $statuList;

            
            $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        } else {

            $_units           = (int)trim(replacePost($this->input->post("units")));
            $_group           = (int)trim(replacePost($this->input->post("group")));
            $_status          = (int)trim(replacePost($this->input->post("status")));
            $_istasyon        = (int)trim(replacePost($this->input->post("istasyon")));
            $_name            = trim(replacePost($this->input->post("name")));
            $_lastname        = trim(replacePost($this->input->post("lastname")));
            $_surname         = trim(replacePost($this->input->post("surname")));
            $_cinsiyet         = trim(replacePost($this->input->post("cinsiyet")));
            $_email           = trim(replacePost($this->input->post("email")));
            $_username        = trim(replacePost($this->input->post("username")));
            $_password        = trim(replacePost($this->input->post("password")));

            if (strlen($_password) > 0) {
                $_password = sha1($_password);
                $_repassword = 1;
            } else {
                $_password = $item->u_password;
                $_repassword = 0;
            }

            $update = $this->users_model->update(
                array(
                    "u_id"            => $_id,
                ),
                array(
                    "u_name"          => $_name,
                    "u_lastname"      => $_lastname,
                    "u_surname"       => $_surname,
                    "u_cinsiyet"       => $_cinsiyet,
                    "u_username"      => $_username,
                    "u_password"      => $_password,
                    "u_mail"          => $_email,
                    "u_unit"          => $_units,
                    "u_group"         => $_group,
                    "u_statu"         => $_status,
                    "u_istasyon"      => $_istasyon,
                    "u_repassword"    => $_repassword,
                    "u_updatetime"    => dateToTime(date("Y-m-d H:i:s")),
                    "u_updateuser"    => $this->userData->userB->u_id
                )
            );
            if ($update) {
                $alert = array(
                    "title" => "Tebrikler!",
                    "text"  => "{$_name} {$_lastname} {$_surname} İsimli Kullanıcı Başarıyla Güncellendi.",
                    "type"  => "success"
                );
                $this->session->set_flashdata("alertToastr", $alert);
                redirect(base_url('users'));
            } else {
                $alert = array(
                    "title" => "Hata!",
                    "text"  => "{$_name} {$_lastname} {$_surname} İsimli Kullanıcı Bilgileri Güncellenemedi.",
                    "type"  => "error"
                );
                $this->session->set_flashdata("alertToastr", $alert);
                redirect(base_url('users'));
            }
        }
    }



    public function update_valid_username($username = '', $id = 0)
    {
        $username = trim(replacePost($username));
        $_id = (int)trim(replacePost($id));

        //die("Username:" . $username . " ID:" . $_id);
        if (preg_match('/^[a-z]\w{3,25}[^_]$/i', $username) == FALSE) {
            $this->form_validation->set_message('update_valid_username', '<b>{field}</b> alanı sadece harf, rakam ve _ içerebilir.');
            return FALSE;
        }

        $getUsername = $this->users_model->get(
            array(
                "u_id !="       => $_id,
                "u_username"      => $username
            )
        );
        if ($getUsername) {
            $this->form_validation->set_message('update_valid_username', 'Bu kullanıcı adı başka kullanıcı tarafından kullanımda.');
            return FALSE;
        }
        return TRUE;
    }


    public function update_valid_password($password = '', $id = 0)
    {
        $password = trim(replacePass($password));
        $_id = (int)trim(replacePost($id));


        $getUsername = $this->users_model->get(
            array(
                "u_id"          => $_id,
                "u_password"    => sha1($password)
            )
        );
        if (!$getUsername) {
            $this->form_validation->set_message('update_valid_password', 'Mevcuttaki parolanızı doğru giriniz.');
            return FALSE;
        }
        return TRUE;
    }



    public function valid_stringnospace($text = '')
    {
        $_text = trim(replacePost($text));

        //die("Username:" . $username . " ID:" . $_id); /^[a-zığüşiöçİĞÜŞÖÇ]+$/i
        if (strlen($_text) > 0 && preg_match('/^[a-zığüşiöçİĞÜŞÖÇ]+$/i', $_text) == FALSE) {
            $this->form_validation->set_message('valid_stringnospace', '<b>{field}</b> alanı sadece harf içerebilir.');
            return FALSE;
        }
        return TRUE;
    }




    public function ejectOn($id)
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
                "text"  => "Arşivlenecek Kullanıcı Bulunamadı. İlgili Kullanıcıyı Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("users"));
            exit;
        }

        if ($_id == $this->userData->userB->u_id) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Arşivlenecek Kullanıcı Bulunamadı. İlgili Kullanıcıyı Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("users"));
            exit;
        }


        $item = $this->users_model->ek_join_get(
            "r8t_users",
            array(
                "r8t_sys_grouplist" => "r8t_sys_grouplist.ug_id = r8t_users.u_group",
                "r8t_sys_statulist"    => "r8t_sys_statulist.us_id = r8t_users.u_statu",
                "r8t_sys_unitlist"  => "r8t_sys_unitlist.ub_id = r8t_users.u_unit"
            ),
            "INNER",
            array(
                "u_id"                              => $_id,
                "r8t_sys_grouplist.ug_yetkisira >=" => $this->userData->userB->ug_yetkisira,
                "r8t_sys_statulist.us_yetkisira >=" => $this->userData->userB->us_yetkisira,
                "u_status !="                       => -1
            ),
            false
        );


        if (!$item) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Arşivlenecek Kullanıcı Bulunamadı. İlgili Kullanıcıyı Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('users'));
            exit;
        }

        $eject = $this->users_model->update(
            array(
                "u_id"      => $_id
            ),
            array(
                "u_status"  => -1
            )
        );

        if ($eject) {
            $alert = array(
                "title" => "Tebrikler!",
                "text"  => "{$item->u_name} {$item->u_lastname} {$item->u_surname} İsimli Kullanıcı Arşivlendi.",
                "type"  => "success"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('users'));
        } else {
            $alert = array(
                "title" => "Hata!",
                "text"  => "{$item->u_name} {$item->u_lastname} {$item->u_surname} İsimli Kullanıcı Arşivlenemedi.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('users'));
        }
    }


    public function ejectOff($id)
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
                "text"  => "Arşivdeki Kullanıcı Bulunamadı. İlgili Kullanıcıyı Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("users"));
            exit;
        }

        if ($_id == $this->userData->userB->u_id) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Arşivlenecek Kullanıcı Bulunamadı. İlgili Kullanıcıyı Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("users"));
            exit;
        }



        $item = $this->users_model->ek_join_get(
            "r8t_users",
            array(
                "r8t_sys_grouplist" => "r8t_sys_grouplist.ug_id = r8t_users.u_group",
                "r8t_sys_statulist"    => "r8t_sys_statulist.us_id = r8t_users.u_statu",
                "r8t_sys_unitlist"  => "r8t_sys_unitlist.ub_id = r8t_users.u_unit"
            ),
            "INNER",
            array(
                "u_id"                              => $_id,
                "r8t_sys_grouplist.ug_yetkisira >=" => $this->userData->userB->ug_yetkisira,
                "r8t_sys_statulist.us_yetkisira >=" => $this->userData->userB->us_yetkisira,
                "u_status"                       => -1
            ),
            false
        );


        if (!$item) {
            $alert = array(
                "title" => "Hata!",
                "text"  => "Arşivdeki Kullanıcı Bulunamadı. İlgili Kullanıcıyı Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('users'));
            exit;
        }

        $eject = $this->users_model->update(
            array(
                "u_id"      => $_id
            ),
            array(
                "u_status"  => 0
            )
        );

        if ($eject) {
            $alert = array(
                "title" => "Tebrikler!",
                "text"  => "{$item->u_name} {$item->u_lastname} {$item->u_surname} İsimli Kullanıcı Arşivden Çıkarılmıştır.",
                "type"  => "success"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('users'));
        } else {
            $alert = array(
                "title" => "Hata!",
                "text"  => "{$item->u_name} {$item->u_lastname} {$item->u_surname} İsimli Kullanıcı Arşivden Çıkarılamadı.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('users'));
        }
    }
}
