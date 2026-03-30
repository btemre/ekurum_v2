<?php
defined('BASEPATH') OR exit('No direct script access allowed');

    function sessionDecodeCheck($_session = array()){
        $_sessionCode = $_session["_session"];
        $_sessionTime = $_session["_key"];
        $_sessionUid  = $_session["_udata"];

        $sessionX = md5($_sessionTime.'R571T'.$_sessionUid);
        if($sessionX==$_sessionCode){
            return TRUE;
        }else{
            return FALSE;
        }
    }


    ### <begin::LoginUser Bilgilerini Alma> ###
    function getUser(){
        $t = &get_instance();
        $t->load->model("auth_model");

        return $t->auth_model;
    }
    ### <end::LoginUser Bilgilerini Alma> ###


?>
