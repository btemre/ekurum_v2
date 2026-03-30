<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth_model extends CI_Model
{
    public $tableName   = "r8t_users";
    public $userData    = false;
    public $oturumSaat  = 5; ### 5 SAATLIK OTURUM SURESI TANIMLANIR

    public function __construct()
    {
        parent::__construct();
        ##LOGIN KONTROLU ICIN SESSION CEKIYORUZ
        $_session = $this->session->userdata("_session");
        if ($_session == NULL) {
            $this->userData = false;
        } else { ## SESSION VERISI VAR ISE DOGRULAMA ADIMLARINI CALISTIR
            if (sessionDecodeCheck($_session) == FALSE) { ## SessionId Değerinin Şifrelemesinin Kontrolü
                $this->userData = false;
            } else {
                $userLogin = $this->checkAuthLogin($_session); ## LOGIN OLUNMUŞMU KONTROLU
                if ($userLogin == FALSE) {
                    $this->userData = false;
                } else {
                    if ($this->logoutTimeUpdate($_session) == FALSE) { ## LOGOUT ZAMANI GÜNCELLEMESI
                        $this->userData = false;
                    } else {
                        $this->userData = $this->loginGetUser($_session["_udata"]);
                        //print_r($this->userData);
                        if ($this->userData != FALSE) {
                            $sessionX = array(
                                "_session"    => trim(replacePost($_session["_session"])),
                                "_key"        => trim(replacePost($_session["_key"])),
                                "_udata"      => trim(replacePost($_session["_udata"]))
                            );
                            $this->session->set_userdata("_session", $sessionX);
                            // die("Model:" . $this->router->fetch_class());

                            if ($this->userData->userB->u_repassword == 1 && ($this->router->fetch_class() != "auth")) {
                                redirect(base_url("auth/repassword_form"));
                            }
                        }
                    }
                }
            }
        }
    }

    private function checkAuthLogin($_session = array())
    {
        $_outTime = dateToTime(date("Y-m-d H:i:s"));
        $this->db->select('*');
        $this->db->from('r8t_sys_loginhistory');
        $this->db->join('r8t_users', 'r8t_users.u_id = r8t_sys_loginhistory.l_userid');
        $this->db->join('r8t_sys_grouplist', 'r8t_sys_grouplist.ug_id = r8t_users.u_group');
        $this->db->join('r8t_sys_statulist', 'r8t_sys_statulist.us_id = r8t_users.u_statu');
        $this->db->join('r8t_sys_unitlist', 'r8t_sys_unitlist.ub_id = r8t_users.u_unit');
        $this->db->join('r8t_sys_istasyonlar', 'r8t_sys_istasyonlar.kdi_id = r8t_users.u_istasyon');

        $this->db->where(
            array(
                'r8t_users.u_status'                  => 1,
                'r8t_sys_loginhistory.l_sessionid'    => trim(replacePost($_session["_session"])),
                'r8t_sys_loginhistory.l_userid'       => trim(replacePost($_session["_udata"])),
                'r8t_sys_loginhistory.l_logouttime >' => $_outTime,
                'r8t_sys_loginhistory.l_status'       => 1,
                'r8t_sys_loginhistory.l_type'         => 0,    ## WEB OTURUMU OLANLARI SORGULA
                'r8t_sys_grouplist.ug_status'         => 1,
                'r8t_sys_statulist.us_status'         => 1,
                'r8t_sys_unitlist.ub_status'          => 1
            )
        );
        $_userlogin = $this->db->get()->row();
        if ($_userlogin) {
            return $_userlogin;
        } else {
            return FALSE;
        }
    }

    private function logoutTimeUpdate($_session = array())
    {
        $_outLine     = ((60 * 60 * $this->oturumSaat) + time());
        $outTime = dateToTime(date("Y-m-d H:i:s", $_outLine));
        $loginUpdate = $this->ek_update(
            "r8t_sys_loginhistory",
            array(
                "l_sessionid"   => trim(replacePost($_session["_session"]))
            ),
            array(
                "l_logouttime"     => $outTime
            )
        );
        if ($loginUpdate) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function loginGetUser($userid = null)
    {
        $userData = new stdClass();
        $userData->userB = FALSE;

        $sonucX = TRUE;
        ### KULLANICI BILGILERINI AL
        $userB = $this->ek_join_get(
            "r8t_users",
            array(
                "r8t_sys_grouplist"     => "r8t_sys_grouplist.ug_id = r8t_users.u_group",
                "r8t_sys_statulist"     => "r8t_sys_statulist.us_id = r8t_users.u_statu",
                "r8t_sys_unitlist"      => "r8t_sys_unitlist.ub_id = r8t_users.u_unit",
                "r8t_sys_istasyonlar"   => "r8t_sys_istasyonlar.kdi_id = r8t_users.u_istasyon"
            ),
            "INNER",
            array(
                "u_id"    => (int)trim(replacePost($userid))
            ),
            false
        );
        if (!$userB) {
            $sonucX = FALSE;
        } else {
            $userData->userB  = $userB;
        }

        if ($sonucX == TRUE) {
            return $userData;
        } else {
            return FALSE;
        }
    }

    public function get($where = array())
    {
        return $this->db->where($where)->get($this->tableName)->row();
    }

    public function get_all($where = array(), $order = "")
    {
        if ($order == "") {
            return $this->db->where($where)->get($this->tableName)->result();
        } else {
            return $this->db->where($where)->order_by($order)->get($this->tableName)->result();
        }
    }

    public function add($data = array())
    {
        return $this->db->insert($this->tableName, $data);
    }

    public function update($where = array(), $data = array())
    {
        return $this->db->where($where)->update($this->tableName, $data);
    }

    public function ek_update($table = "", $where = array(), $data = array())
    {
        return $this->db->where($where)->update($table, $data);
    }

    public function ek_add($table = "", $data = array())
    {
        return $this->db->insert($table, $data);
    }

    public function ek_get($table = "", $where = array())
    {
        return $this->db->where($where)->get($table)->row();
    }

    public function ek_join_get($table = "", $joinX = array(), $tur = "INNER", $where = array(), $order = false, $orwhere = array())
    {
        $this->db->select('*');
        $this->db->from($table);
        foreach ($joinX as $coll => $value) {
            $this->db->join($coll, $value, $tur);
        }
        $this->db->where($where);
        $this->db->or_where($orwhere);
        if ($order != false) {
            $this->db->order_by($order);
        }
        //return $this->db->join($joinX)->where($where)->get()->row();
        return $this->db->get()->row();
    }
}
