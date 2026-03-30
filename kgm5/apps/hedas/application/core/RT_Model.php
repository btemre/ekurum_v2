<?php
class RT_Model extends CI_Model
{
    public $tableName;

    public function __construct()
    {
        parent::__construct();
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

    public function ek_add_lastid($table = "", $data = array())
    {
        if ($this->db->insert($table, $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    public function ek_get($table = "", $where = array())
    {
        return $this->db->where($where)->get($table)->row();
    }


    public function ek_get_orderby($table = "", $where = array(), $order = false)
    {   
        if ($order == false) {
            return $this->db->where($where)->get($table)->row();
        }else{
            return $this->db->where($where)->order_by($order)->get($table)->row();
        }
    }


    public function ek_get_all($table = "", $where = array(), $order = false)
    {
        if ($order == false) {
            return $this->db->where($where)->get($table)->result();
        } else {
            return $this->db->where($where)->order_by($order)->get($table)->result();
        }
    }

    public function ek_get_all_wherein($table = "", $where = array(), $wherein = array())
    {
        return $this->db->where($where)->where_in($wherein)->get($table)->result();
    }


    public function ek_delete($table = "", $where = array())
    {
        return $this->db->where($where)->delete($table);
    }


    public function ek_query($query = "")
    {
        return $this->db->query($query)->row();
    }


    public function ek_query_all($query = "")
    {
        return $this->db->query($query)->result();
    }

    public function ek_get_all_w_ow_win_like_orlike_order_limit($table = "", $where = array(), $orwhere = array(), $wherein = array(), $like = array(), $orlike = array(), $order = false, $limit = false)
    {
        $this->db->select('*');
        //$this->db->from($table);
        if (count($where) !== 0) {
            $this->db->where($where);
        }
        if (count($orwhere) !== 0) {
            $this->db->or_where($orwhere);
        }
        if (count($wherein) !== 0) {
            $this->db->wherein($wherein);
        }
        if (count($like) !== 0) {
            $this->db->like($like);
        }
        if (count($orlike) !== 0) {
            $this->db->or_like($orlike);
        }
        if ($order !== false) {
            $this->db->order_by($order);
        }
        if ($limit !== false) {
            $this->db->limit($limit);
        }
        /* if (count($limit) !== 0) {
            $this->db->limit($limit);
        } */
        return $this->db->get($table)->result();
    }

    public function ek_count_all_w_ow_win_like_olike($table = "", $where = array(), $orwhere = array(), $wherein = array(), $like = array(), $orlike = array())
    {
        $this->db->select('*');
        $this->db->from($table);
        if (count($where) !== 0) {
            $this->db->where($where);
        }
        if (count($orwhere) !== 0) {
            $this->db->or_where($orwhere);
        }
        if (count($wherein) !== 0) {
            $this->db->wherein($wherein);
        }
        if (count($like) !== 0) {
            $this->db->like($like);
        }
        if (count($orlike) !== 0) {
            $this->db->or_like($orlike);
        }
        return $this->db->count_all_results();
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




    public function ek_join_get_all($table = "", $joinX = array(), $tur = "INNER", $where = array(), $order = false, $orwhere = array())
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
        return $this->db->get()->result();
    }

    ###begin::apps klasörü altındaki uygulamaların listesini alıp veritabanına ekleme ve güncelleme
    public function appUpdate($appList = false)
    {
        if ($appList == false) {
            return false;
        } else {
            foreach ($appList as $app) {
                $getDbApp = $this->ek_get(
                    "r8t_sys_apps",
                    array(
                        "a_appcode"     => $app->code
                    )
                );
                if ($getDbApp) {
                    //Uygulama Kaydı Varsa Bilgisini Güncelle
                    $update = $this->ek_update(
                        "r8t_sys_apps",
                        array(
                            "a_appcode"     => $app->code
                        ),
                        array(
                            "a_title"       => $app->name,
                            "a_description" => $app->description,
                            "a_shortcode"   => $app->shortcode,
                            "a_json"        => json_encode($app)
                        )
                    );
                } else {
                    // Uygulamayı Veritabanına Kaydediyoruz
                    $add = $this->ek_add(
                        "r8t_sys_apps",
                        array(
                            "a_appcode"     => $app->code,
                            "a_title"       => $app->name,
                            "a_description" => $app->description,
                            "a_shortcode"   => $app->shortcode,
                            "a_json"        => json_encode($app),
                            "a_status"      => 1
                        )
                    );
                }
            } ### FOREACH END

            $appList = $this->ek_get_all(
                "r8t_sys_apps",
                array(
                    "a_status >=" => 1
                )
            );
            return $appList;
        }
    }
    ###begin::apps klasörü altındaki uygulamaların listesini alıp veritabanına ekleme ve güncelleme

}
