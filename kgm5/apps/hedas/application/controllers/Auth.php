<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public $viewFolder     = "";

    public function __construct()
    {
        parent::__construct();
        $this->viewFolder = "auth_v";
        $this->load->model("auth_model");
    }

    public function login()
    {
        if ($this->auth_model->userData !== false) {
            redirect(base_url());
            exit;
        }
        $viewData = new stdClass();
        $viewData->viewFolder     = $this->viewFolder;
        $viewData->subViewFolder    = "login";

        $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function do_login()
    {
        if ($this->auth_model->userData !== false) {
            redirect(base_url());
            exit;
        }
        $this->load->library("form_validation");
        // Kurallar Yazılır..
        $this->form_validation->set_rules("username", "Kullanıcı Adı", "required|trim|min_length[4]|max_length[25]|callback_valid_username");
        $this->form_validation->set_rules("password", "Parola", "required|trim|min_length[12]|max_length[30]");
        //  $this->form_validation->set_rules("password", "Parola", "required|trim|min_length[12]|max_length[30]|callback_valid_password"); ##Şifre Politikası Oluşturulunca Kullanılacak

        $this->form_validation->set_message(
            array(
                "required"    => "<b>{field}</b> alanı doldurulmalıdır.",
                "valid_email" => "Lütfen Geçerli Bir Mail Adresi Giriniz.",
                "is_unique"   => "<b>{field}</b> alanı daha önce kullanılmış",
                "numeric"     => "<b>{field}</b> alanı geçerli değil",
                "matches"     => "Şifreler birbiri ile uyuşmuyor.",
                "min_length"  => "<b>{field}</b> en az %s karakterden oluşmalıdır.",
                "max_length"  => "<b>{field}</b> en fazla %s karakterden oluşmalıdır.",
                "alpha"       => "<b>{field}</b> alanı sadece harf değerleri alır.",
                "regex_match" => "<b>{field}</b> alanı kabul edilebilir değerler içermiyor."
            )
        );

        if ($this->form_validation->run() == FALSE) {
            $viewData = new stdClass();
            $viewData->viewFolder     = $this->viewFolder;
            $viewData->subViewFolder    = "login";
            $viewData->form_error     = true;

            $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        } else {
            $_username = trim(replacePost($this->input->post("username")));
            $_password = trim(replacePass($this->input->post("password")));

            ### VERITABANINDAKI KULLANICI TABLOSUNDA KULLANICI BILGILERINI KONTROL EDIYORUZ
            $getUser = $this->auth_model->ek_join_get(
                "r8t_users",
                array(
                    "r8t_sys_grouplist" => "r8t_sys_grouplist.ug_id = r8t_users.u_group",
                    "r8t_sys_statulist"    => "r8t_sys_statulist.us_id = r8t_users.u_statu"
                ),
                "INNER",
                array(
                    "u_username"  => $_username,
                    "u_password"  => sha1($_password),
                    "u_status"    => 1,
                    "r8t_sys_grouplist.ug_status" => 1,
                    "r8t_sys_statulist.us_status" => 1
                ),
                false
            );

            if ($getUser) {

                ### AKTIF WEB OTURUMUNU KAPATIYORUZ
                $this->auth_model->ek_update(
                    "r8t_sys_loginhistory",
                    array(
                        "l_userid"    => $getUser->u_id,
                        "l_type"      => 0,
                        "l_status"    => 1
                    ),
                    array(
                        "l_status"    => 0
                    )
                );

                $outLine    = (60 * 60 * 5) + time(); # 5 SAATLIK OTURUM SURESI VERIYORUZ
                $inTime     = dateToTime(date("Y-m-d H:i:s")); #OTURUM BAŞLANGIÇ SAATİ - TIME() DEGERI YERINE BU FONKSIYONU OZELLIKLE KULLANDIK
                $outTime    = dateToTime(date("Y-m-d H:i:s", $outLine)); #OTURUM BİTİŞ SAATİ - TIME() DEGERI YERINE BU FONKSIYONU OZELLIKLE KULLANDIK
                $sessionId  = md5($inTime . 'R571T' . $getUser->u_id); ### GIRIS ZAMANI VE USER ID DEN SESSION OLUSTURDUK. DOGRULAMASINI SONUNA EKLEDIK
                $userIp     = $_SERVER["REMOTE_ADDR"];
                $browser        = $_SERVER['HTTP_USER_AGENT'];
                $region          = _ipLookup($userIp);

                ## LOGIN KAYDINI VERITABANINA EKLIYORUZ - HER SAYFADA KONTROL EDILIP LOGIN SURESI (60*60*60*5)+time() = 5 SAAT UZATILIYOR
                $loginInsert = $this->auth_model->ek_add(
                    "r8t_sys_loginhistory",
                    array(
                        "l_sessionid"   => $sessionId,
                        "l_userid"      => $getUser->u_id,
                        "l_mail"           => $getUser->u_mail,
                        "l_ipaddress"   => $userIp,
                        "l_logintime"   => $inTime,
                        "l_logouttime"  => $outTime,
                        "l_browser"            => $browser,
                        "l_region"            => $region,
                        "l_status"      => 1,
                        "l_type"                => 0
                    )
                );

                if ($loginInsert) {
                    $_sessionX = array(
                        "_session"  => $sessionId,
                        "_key"      => $inTime,
                        "_udata"    => $getUser->u_id
                    );
                    $alert = array(
                        "title" => "Tebrikler! Giriş Başarılı.",
                        "text"  => "$getUser->u_name $getUser->u_lastname , Hoşgeldiniz.",
                        "type"  => "success"
                    );


                    $_loginRedirect = "";
                    $this->session->set_userdata("_session", $_sessionX);
                    $this->session->set_flashdata("alert", $alert);
                    redirect(base_url($_loginRedirect));
                } else {
                    $alert = array(
                        "title" => "Hata! Giriş Yapılamadı.",
                        "text"  => "Lütfen Kullanıcı Bilgilerinizi Kontrol Ediniz.",
                        "type"  => "error"
                    );
                    $this->session->set_flashdata("alert", $alert);
                    redirect(base_url("login"));
                }
            } else {
                $alert = array(
                    "title" => "Hata! Giriş Yapılamadı.",
                    "text"  => "Lütfen Kullanıcı Bilgilerinizi Kontrol Ediniz.",
                    "type"  => "error"
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
                    "l_userid"   => trim(replacePost($_session["_udata"]))
                ),
                array(
                    "l_status"     => 0
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
        if (preg_match('/^[a-z]\w{4,25}[^_]$/i', $username) == FALSE) {
            $this->form_validation->set_message('valid_username', '<b>{field}</b> alanı sadece harf, rakam ve _ içerebilir.');
            return FALSE;
        }
        return TRUE;
    }
}
