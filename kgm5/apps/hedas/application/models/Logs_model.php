<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Logs_model extends RT_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->tableName  = "r8t_sys_userlogs";
    }
}
