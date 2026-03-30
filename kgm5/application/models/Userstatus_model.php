<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Userstatus_model extends RT_Model{

    public function __construct(){
        parent::__construct();
        $this->tableName  = "r8t_sys_statulist";
    }


}
?>
