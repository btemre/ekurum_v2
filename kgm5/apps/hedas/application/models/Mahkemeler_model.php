<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mahkemeler_model extends RT_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->tableName  = "r8t_sys_mahkemeler";
    }

    public function checkMahkemeAdi($mhName,$mhId) {
        $query="select mh_id from ".$this->tableName." where 
        mh_name like '%$mhName%' and mh_id<>$mhId
        ";
        $mahkemeVar = $this->ek_query_all($query);

        return $mahkemeVar;
    }


}


