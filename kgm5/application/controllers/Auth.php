<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public $viewFolder = "";
    public function __construct()
    {
        parent::__construct();
        $this->viewFolder = "auth_v";
        $this->load->model("users_model");
        $this->load->model("auth_model");
        $this->userData = $this->auth_model->userData;
    }
    public function login()
    {
        if ($this->auth_model->userData !== false) {
            redirect(base_url());
            exit;
        }
        $viewData = new stdClass();
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "login";

        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }
    public function forgotpassword()
    {
        $viewData = new stdClass();
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "forgotpassword";

        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }
    public function do_forgotpassword()
    {
        if ($this->auth_model->userData !== false) {
            redirect(base_url());
            exit;
        }
        $this->load->library("form_validation");
        // Kurallar Yazılır..
        $this->form_validation->set_rules("email", "E Posta", "required|trim|valid_email");
        //$this->form_validation->set_rules("password", "Parola", "required|trim|min_length[12]|max_length[30]");
        //  $this->form_validation->set_rules("password", "Parola", "required|trim|min_length[12]|max_length[30]|callback_valid_password"); ##Şifre Politikası Oluşturulunca Kullanılacak

        $this->form_validation->set_message(
            array(
                "required" => "<b>{field}</b> alanı doldurulmalıdır.",
                "valid_email" => "Lütfen Geçerli Bir Mail Adresi Giriniz.",
                "is_unique" => "<b>{field}</b> alanı daha önce kullanılmış",
                "numeric" => "<b>{field}</b> alanı geçerli değil",
                "matches" => "Şifreler birbiri ile uyuşmuyor.",
                "min_length" => "<b>{field}</b> en az %s karakterden oluşmalıdır.",
                "max_length" => "<b>{field}</b> en fazla %s karakterden oluşmalıdır.",
                "alpha" => "<b>{field}</b> alanı sadece harf değerleri alır.",
                "regex_match" => "<b>{field}</b> alanı kabul edilebilir değerler içermiyor."
            )
        );

        if ($this->form_validation->run() == FALSE) {
            $viewData = new stdClass();
            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "forgotpassword";
            $viewData->form_error = true;

            $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        } else {
            $_mail = trim(replacePost($this->input->post("email")));
            //$_password = sha1(trim(replacePass("33kgm05.bt33")));
            $resetToken = generateResetToken();

            ### VERITABANINDAKI KULLANICI TABLOSUNDA KULLANICI BILGILERINI KONTROL EDIYORUZ
            $getUser = $this->auth_model->ek_join_get(
                "r8t_users",
                array(
                    "r8t_sys_grouplist" => "r8t_sys_grouplist.ug_id = r8t_users.u_group",
                    "r8t_sys_statulist" => "r8t_sys_statulist.us_id = r8t_users.u_statu",
                    "r8t_sys_unitlist" => "r8t_sys_unitlist.ub_id = r8t_users.u_unit",
                    "r8t_sys_istasyonlar" => "r8t_sys_istasyonlar.kdi_id = r8t_users.u_istasyon"
                ),
                "INNER",
                array(
                    "u_mail" => $_mail,
                    "u_status" => 1,
                    "r8t_sys_grouplist.ug_status" => 1,
                    "r8t_sys_statulist.us_status" => 1,
                    "r8t_sys_unitlist.ub_status" => 1,
                ),
                false
            );

            // echo '<pre>';
            // echo 'User:' . $_username . ' | Pass:' . sha1($_password);
            // print_r($getUser);
            // echo '</pre>';
            // die();

            if ($getUser) {

                ### AKTIF WEB OTURUMUNU KAPATIYORUZ
                // $this->auth_model->ek_update(
                //     "r8t_sys_loginhistory",
                //     array(
                //         "l_userid"    => $getUser->u_id,
                //         "l_type"      => 0,
                //         "l_status"    => 1
                //     ),
                //     array(
                //         "l_status"    => 0
                //     )
                // );

                // $outLine    = (60 * 60 * 5) + time(); # 5 SAATLIK OTURUM SURESI VERIYORUZ
                // $inTime     = dateToTime(date("Y-m-d H:i:s")); #OTURUM BAŞLANGIÇ SAATİ - TIME() DEGERI YERINE BU FONKSIYONU OZELLIKLE KULLANDIK
                // $outTime    = dateToTime(date("Y-m-d H:i:s", $outLine)); #OTURUM BİTİŞ SAATİ - TIME() DEGERI YERINE BU FONKSIYONU OZELLIKLE KULLANDIK
                // $sessionId  = md5($inTime . 'R571T' . $getUser->u_id); ### GIRIS ZAMANI VE USER ID DEN SESSION OLUSTURDUK. DOGRULAMASINI SONUNA EKLEDIK
                // $userIp     = $_SERVER["REMOTE_ADDR"];
                // $browser        = $_SERVER['HTTP_USER_AGENT'];
                // $region          = _ipLookup($userIp);

                ## LOGIN KAYDINI VERITABANINA EKLIYORUZ - HER SAYFADA KONTROL EDILIP LOGIN SURESI (60*60*60*5)+time() = 5 SAAT UZATILIYOR
                
                $loginInsert = $this->auth_model->ek_update(
                    "r8t_users",
                    array(
                        "u_mail" => $_mail
                    ),
                    array(
                        "u_repassword" => 0,
                        "u_token"      => $resetToken
                        //"u_password"   => $_password
                    )
                );

                if ($loginInsert) {
                    $posta_mail = $_mail; // E-posta alıcısı
                    $posta_konu = "Şifre Sıfırlama Talebi"; // E-posta konusu
                    $posta_mesaj = "Merhaba,\n\nŞifrenizi sıfırlamak için Doğrulama Kodunuz : \n\n"  . $resetToken;

                     MailGonder($posta_mail, $posta_konu, $posta_mesaj);

                    $alert = [
                        "title" => "Şifre Talebi Başarılı!",
                        "text" => "Lütfen E-Posta Adresinize Gelen Doğrulama Kodunu ilgili alana Girin.",
                        "type" => "success"
                    ];
                    $this->session->set_flashdata("alert", $alert);
                    redirect(base_url("auth/repassword_formmail"));
                } else {
                    $alert = array(
                        "title" => "Hata!",
                        "text" => "Lütfen E-Posta Bilgilerinizi Kontrol Ediniz.",
                        "type" => "error"
                    );
                    $this->session->set_flashdata("alert", $alert);
                    redirect(base_url("auth/forgotpassword"));
                }
            } else {
                $alert = array(
                    "title" => "Hata!",
                    "text" => "Lütfen E-Posta Bilgilerinizi Kontrol Ediniz.",
                    "type" => "error"
                );
                $this->session->set_flashdata("alert", $alert);
                redirect(base_url("auth/forgotpassword"));
            }
        }
    }
    public function do_login()
    {
        if ($this->auth_model->userData !== false) {
            redirect(base_url());
            exit;
        }
        $this->load->library("form_validation");
        // Kurallar Yazılır..
        $this->form_validation->set_rules("username", "Kullanıcı Adı", "required|trim|min_length[3]|max_length[25]|callback_valid_username");
        $this->form_validation->set_rules("password", "Parola", "required|trim|min_length[12]|max_length[30]");
        //  $this->form_validation->set_rules("password", "Parola", "required|trim|min_length[12]|max_length[30]|callback_valid_password"); ##Şifre Politikası Oluşturulunca Kullanılacak

        $this->form_validation->set_message(
            array(
                "required" => "<b>{field}</b> alanı doldurulmalıdır.",
                "valid_email" => "Lütfen Geçerli Bir Mail Adresi Giriniz.",
                "is_unique" => "<b>{field}</b> alanı daha önce kullanılmış",
                "numeric" => "<b>{field}</b> alanı geçerli değil",
                "matches" => "Şifreler birbiri ile uyuşmuyor.",
                "min_length" => "<b>{field}</b> en az %s karakterden oluşmalıdır.",
                "max_length" => "<b>{field}</b> en fazla %s karakterden oluşmalıdır.",
                "alpha" => "<b>{field}</b> alanı sadece harf değerleri alır.",
                "regex_match" => "<b>{field}</b> alanı kabul edilebilir değerler içermiyor."
            )
        );

        if ($this->form_validation->run() == FALSE) {
            $viewData = new stdClass();
            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "login";
            $viewData->form_error = true;

            $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        } else {
            $_username = trim(replacePost($this->input->post("username")));
            $_password = trim(replacePass($this->input->post("password")));

            ### VERITABANINDAKI KULLANICI TABLOSUNDA KULLANICI BILGILERINI KONTROL EDIYORUZ
            $getUser = $this->auth_model->ek_join_get(
                "r8t_users",
                array(
                    "r8t_sys_grouplist" => "r8t_sys_grouplist.ug_id = r8t_users.u_group",
                    "r8t_sys_statulist" => "r8t_sys_statulist.us_id = r8t_users.u_statu",
                    "r8t_sys_unitlist" => "r8t_sys_unitlist.ub_id = r8t_users.u_unit",
                    "r8t_sys_istasyonlar" => "r8t_sys_istasyonlar.kdi_id = r8t_users.u_istasyon"
                ),
                "INNER",
                array(
                    "u_username" => $_username,
                    "u_password" => sha1($_password),
                    "u_status" => 1,
                    "r8t_sys_grouplist.ug_status" => 1,
                    "r8t_sys_statulist.us_status" => 1,
                    "r8t_sys_unitlist.ub_status" => 1,
                ),
                false
            );

            // echo '<pre>';
            // echo 'User:' . $_username . ' | Pass:' . sha1($_password);
            // print_r($getUser);
            // echo '</pre>';
            // die();

            if ($getUser) {

                ### AKTIF WEB OTURUMUNU KAPATIYORUZ
                $this->auth_model->ek_update(
                    "r8t_sys_loginhistory",
                    array(
                        "l_userid" => $getUser->u_id,
                        "l_type" => 0,
                        "l_status" => 1
                    ),
                    array(
                        "l_status" => 0
                    )
                );

                $outLine = (60 * 60 * 5) + time(); # 5 SAATLIK OTURUM SURESI VERIYORUZ
                $inTime = dateToTime(date("Y-m-d H:i:s")); #OTURUM BAŞLANGIÇ SAATİ - TIME() DEGERI YERINE BU FONKSIYONU OZELLIKLE KULLANDIK
                $outTime = dateToTime(date("Y-m-d H:i:s", $outLine)); #OTURUM BİTİŞ SAATİ - TIME() DEGERI YERINE BU FONKSIYONU OZELLIKLE KULLANDIK
                $sessionId = md5($inTime . 'R571T' . $getUser->u_id); ### GIRIS ZAMANI VE USER ID DEN SESSION OLUSTURDUK. DOGRULAMASINI SONUNA EKLEDIK
                $userIp = $_SERVER["REMOTE_ADDR"];
                $browser = $_SERVER['HTTP_USER_AGENT'];
                $region = _ipLookup($userIp);

                ## LOGIN KAYDINI VERITABANINA EKLIYORUZ - HER SAYFADA KONTROL EDILIP LOGIN SURESI (60*60*60*5)+time() = 5 SAAT UZATILIYOR
                $loginInsert = $this->auth_model->ek_add(
                    "r8t_sys_loginhistory",
                    array(
                        "l_sessionid" => $sessionId,
                        "l_userid" => $getUser->u_id,
                        "l_username" => $getUser->u_username,
                        "l_mail" => $getUser->u_mail,
                        "l_ipaddress" => $userIp,
                        "l_logintime" => $inTime,
                        "l_logouttime" => $outTime,
                        "l_browser" => $browser,
                        "l_region" => $region,
                        "l_status" => 1,
                        "l_type" => 0
                    )
                );

                if ($loginInsert) {
                    $_sessionX = array(
                        "_session" => $sessionId,
                        "_key" => $inTime,
                        "_udata" => $getUser->u_id
                    );

                    $alert = array(
                        "title" => "Tebrikler! Giriş Başarılı.",
                        "text" => "$getUser->u_name $getUser->u_lastname",
                        "type" => "success"
                    );

                    $_loginRedirect = "";
                    $this->session->set_userdata("_session", $_sessionX);
                    $this->session->set_flashdata("alertlogin", $alert);
                    redirect(base_url($_loginRedirect));
                } else {
                    $alert = array(
                        "title" => "Hata! Giriş Yapılamadı.",
                        "text" => "Lütfen Kullanıcı Bilgilerinizi Kontrol Ediniz.",
                        "type" => "error"
                    );
                    $this->session->set_flashdata("alert", $alert);
                    redirect(base_url("login"));
                }
            } else {
                $alert = array(
                    "title" => "Hata! Giriş Yapılamadı.",
                    "text" => "Lütfen Kullanıcı Bilgilerinizi Kontrol Ediniz.",
                    "type" => "error"
                );
                $this->session->set_flashdata("alert", $alert);
                redirect(base_url("login"));
            }
        }
    }
    public function logout()
    {
        if ($this->auth_model->userData === false) {
            redirect(base_url("login"));
            exit;
        }
        ### SESSION BILGISINI CEKTIRIYORUZ
        $_session = $this->session->userdata("_session");
        if ($_session) { ## SESSION BILGISI VARSA BURAYA DEVAM ET
            ### VERITABANINDAN OTURUM DURUMUNU PASIFE ALIYORUZ
            $loginUpdate = $this->auth_model->ek_update(
                "r8t_sys_loginhistory",
                array(
                    "l_userid" => trim(replacePost($_session["_udata"]))
                ),
                array(
                    "l_status" => 0
                )
            );
            ### COOKIE DEKI SESSION KAYDINI SILIYORUZ
            $this->session->unset_userdata("_session");
            ### PHP SESSION KAYDINI SILIYORUZ VE SIFIRLIYORUZ
            session_destroy();
            redirect(base_url("login"));
        }
    }
    public function valid_password($password = '')
    {
        $password = trim($password);
        $regex_lowercase = '/[a-z]/';
        $regex_uppercase = '/[A-Z]/';
        $regex_number = '/[0-9]/';
        $regex_special = '/[!@#$%^&*()\-_=+{};:,.§~]/';
        $validX = TRUE;
        $validText = "";
        if (empty($password)) {
            $validX = FALSE;
            $validText .= "<b>{field}</b> alanı doldurulmalıdır.<br>";
        }
        if (preg_match_all($regex_lowercase, $password) < 1) {
            $validX = FALSE;
            $validText .= "<b>{field}</b> alanı en az bir küçük harf içermelidir.<br>";
        }
        if (preg_match_all($regex_uppercase, $password) < 1) {
            $validX = FALSE;
            $validText .= "<b>{field}</b> alanı en az bir büyük harf içermelidir.<br>";
        }
        if (preg_match_all($regex_number, $password) < 1) {
            $validX = FALSE;
            $validText .= "<b>{field}</b> alanı en az bir rakam içermelidir.<br>";
        }
        if (preg_match_all($regex_special, $password) < 1) {
            $validX = FALSE;
            $validText .= '<b>{field}</b> alanı en az bir özel karakter içermelidir.' . ' Örn:' . htmlentities(' ! @ . ?') . '<br>';
        }
        if ($validX == FALSE) {
            $this->form_validation->set_message('valid_password', $validText);
            return FALSE;
        }
        return TRUE;
    }
    public function valid_username($username = '')
    {
        $username = trim($username);
        if (preg_match('/^[a-z]\w{3,25}[^_]$/i', $username) == FALSE) {
            $this->form_validation->set_message('valid_username', '<b>{field}</b> alanı sadece harf, rakam ve _ içerebilir.');
            return FALSE;
        }
        return TRUE;
    }
    public function update_valid_password($password = '', $id = 0)
    {
        $password = trim(replacePass($password));
        $_id = (int) trim(replacePost($id));


        $getUsername = $this->users_model->get(
            array(
                "u_id" => $_id,
                "u_password" => sha1($password)
            )
        );
        if (!$getUsername) {
            $this->form_validation->set_message('update_valid_password', 'Mevcuttaki parolanızı doğru giriniz.');
            return FALSE;
        }
        return TRUE;
    }
    public function repassword_form()
    {
        if ($this->userData === false) {
            redirect(base_url("login"));
            exit;
        }

        $_id = $this->userData->userB->u_id;
        if ($_id <= 0) {
            $alert = array(
                "title" => "Hata!",
                "text" => "Düzenlenecek Kullanıcı Bulunamadı. İlgili Kullanıcıyı Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type" => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("home"));
            exit;
        }

        $item = $this->users_model->ek_join_get(
            "r8t_users",
            array(
                "r8t_sys_grouplist" => "r8t_sys_grouplist.ug_id = r8t_users.u_group",
                "r8t_sys_statulist" => "r8t_sys_statulist.us_id = r8t_users.u_statu",
                "r8t_sys_unitlist" => "r8t_sys_unitlist.ub_id = r8t_users.u_unit"
            ),
            "INNER",
            array(
                "u_id" => $_id,
                "r8t_sys_grouplist.ug_yetkisira >=" => $this->userData->userB->ug_yetkisira,
                "r8t_sys_statulist.us_yetkisira >=" => $this->userData->userB->us_yetkisira
            ),
            false
        );


        if (!$item) {
            $alert = array(
                "title" => "Hata!",
                "text" => "Düzenlenecek Kullanıcı Bulunamadı. İlgili Kullanıcıyı Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type" => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('home'));
            exit;
        }

        $viewData = new stdClass();
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "repassword";
        $viewData->userData = $this->userData;
        $viewData->item = $item;
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }
    public function repassword()
    {
        if ($this->userData === false) {
            redirect(base_url("login"));
            exit;
        }


        $_id = $this->userData->userB->u_id;
        if ($_id <= 0) {
            $alert = array(
                "title" => "Hata!",
                "text" => "Düzenlenecek Kullanıcı Bulunamadı. İlgili Kullanıcıyı Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type" => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url("repassword_form"));
            exit;
        }

        $item = $this->users_model->ek_join_get(
            "r8t_users",
            array(
                "r8t_sys_grouplist" => "r8t_sys_grouplist.ug_id = r8t_users.u_group",
                "r8t_sys_statulist" => "r8t_sys_statulist.us_id = r8t_users.u_statu",
                "r8t_sys_unitlist" => "r8t_sys_unitlist.ub_id = r8t_users.u_unit"
            ),
            "INNER",
            array(
                "u_id" => $_id,
                "r8t_sys_grouplist.ug_yetkisira >=" => $this->userData->userB->ug_yetkisira,
                "r8t_sys_statulist.us_yetkisira >=" => $this->userData->userB->us_yetkisira
            ),
            false
        );


        if (!$item) {
            $alert = array(
                "title" => "Hata!",
                "text" => "Düzenlenecek Kullanıcı Bulunamadı. İlgili Kullanıcıyı Düzenleme Yetkiniz Bulunmuyor Olabilir.",
                "type" => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('repassword_form'));
            exit;
        }



        $this->load->library("form_validation");

        // Kurallar Yazılır..
        $this->form_validation->set_rules("oldpassword", "Mevcut Parola", "required|trim|min_length[12]|max_length[30]|callback_update_valid_password[$_id]");
        $this->form_validation->set_rules("password", "Yeni Parola", "required|trim|min_length[12]|max_length[30]");
        $this->form_validation->set_rules("repassword", "Parola Tekrarı", "required|trim|matches[password]");

        $this->form_validation->set_message(
            array(
                "required" => "<b>{field}</b> alanı doldurulmalıdır.",
                "min_length" => "<b>{field}</b> en az %s karakterden oluşmalıdır.",
                "max_length" => "<b>{field}</b> en fazla %s karakterden oluşmalıdır.",
                "alpha" => "<b>{field}</b> alanı sadece harf değerleri almalıdır.",
                "numeric" => "<b>{field}</b> alanı geçerli bir değerler içermiyor.",
                "valid_email" => "Lütfen Geçerli Bir Mail Adresi Giriniz.",
                "matches" => "Parola ve Tekrarı Birbiri İle Uyuşmuyor."
            )
        );

        if ($this->form_validation->run() == FALSE) {

            $viewData = new stdClass();
            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "repassword";
            $viewData->userData = $this->userData;
            $viewData->form_error = true;
            $viewData->item = $item;
            $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        } else {

            $_oldpassword = trim(replacePost($this->input->post("oldpassword")));
            $_password = trim(replacePost($this->input->post("password")));

            if (strlen($_password) > 0) {
                $_password = sha1($_password);
                $_repassword = 1;
            } else {
                $_password = $item->u_password;
                $_repassword = 0;
            }

            $update = $this->users_model->update(
                array(
                    "u_id" => $_id,
                ),
                array(
                    "u_password" => $_password,
                    "u_repassword" => 0,
                    "u_updatetime" => dateToTime(date("Y-m-d H:i:s")),
                    "u_updateuser" => $this->userData->userB->u_id
                )
            );
            if ($update) {
                $alert = array(
                    "title" => "Tebrikler!",
                    "text" => "Parolanız Başarıyla Güncellendi.",
                    "type" => "success"
                );
                $this->session->set_flashdata("alertToastr", $alert);
                redirect(base_url('home'));
            } else {
                $alert = array(
                    "title" => "Hata!",
                    "text" => "Parola Bilgileri Güncellenemedi.",
                    "type" => "error"
                );
                $this->session->set_flashdata("alertToastr", $alert);
                redirect(base_url('repassword_form'));
            }
        }
    }

    public function update_valid_passwordmail( $id = 0)
    {
        //$password = trim(replacePass($password));
        $id = trim(replacePost($this->input->post("kodmail")));;


        $getUsername = $this->users_model->get(
            array(
                "u_token" => $id
                //"u_password" => sha1($password)
            )
        );
        if (!$getUsername) {
            $this->form_validation->set_message('update_valid_passwordmail', 'Doğrulama kodunu doğru giriniz.');
            return FALSE;
        }
        return TRUE;
    }
    public function repassword_formmail()
    {
       
        $viewData = new stdClass();
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "repasswordmail";
       
        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }




    public function repasswordmail()
    {
       

        $_id = trim(replacePost($this->input->post("kodmail")));





        $this->load->library("form_validation");

        // Kurallar Yazılır..
        $this->form_validation->set_rules("kodmail", "Mevcut Parola", "required|trim|min_length[6]|max_length[10]|callback_update_valid_passwordmail[$_id]");
        $this->form_validation->set_rules("passwordmail", "Yeni Parola", "required|trim|min_length[12]|max_length[30]");
        $this->form_validation->set_rules("repasswordmail", "Parola Tekrarı", "required|trim|matches[passwordmail]");

        $this->form_validation->set_message(
            array(
                "required" => "<b>{field}</b> alanı doldurulmalıdır.",
                "min_length" => "<b>{field}</b> en az %s karakterden oluşmalıdır.",
                "max_length" => "<b>{field}</b> en fazla %s karakterden oluşmalıdır.",
                "alpha" => "<b>{field}</b> alanı sadece harf değerleri almalıdır.",
                "numeric" => "<b>{field}</b> alanı geçerli bir değerler içermiyor.",
                "valid_email" => "Lütfen Geçerli Bir Mail Adresi Giriniz.",
                "matches" => "Parola ve Tekrarı Birbiri İle Uyuşmuyor."
            )
        );

        if ($this->form_validation->run() == FALSE) {

            $viewData = new stdClass();
            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "repasswordmail";
            //$viewData->userData = $this->userData;
            $viewData->form_error = true;
            //$viewData->item = $item;
            $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        } else {

            $_kodmail = trim(replacePost($this->input->post("kodmail")));
            $_password = trim(replacePost($this->input->post("passwordmail")));

            if (strlen($_password) > 0) {
                $_password = sha1($_password);
                $_repassword = 1;
            } else {
                $_password = sha1($_password);
                $_repassword = 0;
            }

            $update = $this->users_model->update(
                array(
                    "u_token" => $_id,
                ),
                array(
                    "u_password" => $_password,
                    "u_repassword" => 0,
                    "u_token"   =>'',
                    "u_updatetime" => dateToTime(date("Y-m-d H:i:s"))
                    //"u_updateuser" => $this->userData->userB->u_id
                )
            );
            if ($update) {
                $alert = array(
                    "title" => "Tebrikler!",
                    "text" => "Parolanız Başarıyla Güncellendi. Giriş Yapabilirsiniz.",
                    "type" => "success"
                );
                $this->session->set_flashdata("alert", $alert);
                redirect(base_url('login'));
            } else {
                $alert = array(
                    "title" => "Hata!",
                    "text" => "Parola Bilgileri Güncellenemedi.",
                    "type" => "error"
                );
                $this->session->set_flashdata("alertToastr", $alert);
                redirect(base_url('repassword_formmail'));
            }
        }
    }
}