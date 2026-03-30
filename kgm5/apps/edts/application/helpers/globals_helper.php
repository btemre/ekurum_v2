<?php
##begin::Durusmalar Modulu Global Fonksiyonları

function FormSelectSorumluAvukatList(){
    $t = &get_instance();
    $t->load->model("durusmalar_model");
    
    $sql = "SELECT u_id,u_name,u_lastname,u_surname FROM r8t_users WHERE u_unit=2 AND u_statu=9 ORDER BY u_name,u_lastname,u_surname ASC";
    $items = $t->durusmalar_model->ek_query_all($sql);
    
    return $items;
}

function FormSelectMahkemeList($mahkemeId=0,$idIndex=0){
    $t = &get_instance();
    $t->load->model("mahkemeler_model");
    $where=($mahkemeId>0)?" and mh_id=$mahkemeId ":"";
    $sql = "SELECT mh_id,mh_name FROM r8t_sys_mahkemeler WHERE mh_status=1 $where ORDER BY mh_name ASC";
    $items = $t->mahkemeler_model->ek_query_all($sql);

    $arr=array();
    $slcMahkeme="";
    foreach ($items as $k=>$v) {
        $v=(array)$v;
        $namex=trim($v["mh_name"]);
        $arr[$namex]=array("mh_id"=>$v["mh_id"],"mh_name"=>$namex);
        if ($mahkemeId==$v["mh_id"]) $slcMahkeme=$namex;
    }

    if ($mahkemeId>0) {
        return $slcMahkeme;
    }

    if ($idIndex) {
        $newArr=array();
        foreach ($arr as $k=>$v) {
            $newArr[$v["mh_id"]]=$v["mh_name"];
        }
        return $newArr;
    }
    
    return $arr;
}

function FormSelectSorumluMemurList(){
    $t = &get_instance();
    $t->load->model("durusmalar_model");
    
    $sql = "SELECT u_id,u_name,u_lastname,u_surname FROM r8t_users WHERE u_unit=2 AND u_statu=7 ORDER BY u_name,u_lastname,u_surname ASC";
    $items = $t->durusmalar_model->ek_query_all($sql);
    
    return $items;
}


function FormSelectSorumluMemurName(){
    $t = &get_instance();
    $t->load->model("durusmalar_model");
    $userB    = getUser()->userData->userB;
    
    $item = trim($userB->u_name . ' ' . $userB->u_lastname).' '.$userB->u_surname;
    return $item;
}
function getTrAy($ay="",$keyOnly="") {
    
    $trAy=array("January"=>"Ocak","February"=>"Şub.","March"=>"Mart"
    ,"April"=>"Nis.","May"=>"May.","June"=>"Haz."
    ,"July"=>"Tem.","August"=>"Ağu.","September"=>"Eyl."
    ,"October"=>"Ekim","November"=>"Kas.","December"=>"Ara.");
    $output=(isset($trAy[$ay]))?$trAy[$ay]:$ay;
    $keys0=array_values($trAy);
    if ($keyOnly) {
        $ay0=($keyOnly-1);
        $output=(isset($keys0[$ay0]))?$keys0[$ay0]:$ay;
    }

    return $output;

}
function prp($data="") {
    echo "<pre>";
    echo print_r($data,1);
    echo "</pre>";
}


/** Cache busting: JS/CSS URL'lerine ?v=X eklemek için. Deploy'da güncellenen dosyaların mtime'ına göre otomatik sürüm (her deploy sonrası yeni değer). */
function asset_ver() {
    $v = config_item('asset_version');
    if ($v !== null && $v !== false && $v !== '') {
        return $v;
    }
    $files = array(
        FCPATH . 'assets/js/moduls/ai/ai_service.js',
        APPPATH . 'views/dashboard_v/view/content.php',
        APPPATH . 'views/durusmalar_v/list/content.php',
        APPPATH . 'config/config.php',
    );
    $max = 0;
    foreach ($files as $f) {
        if (is_file($f)) {
            $m = @filemtime($f);
            if ($m > $max) $max = $m;
        }
    }
    return $max > 0 ? (string) $max : '1';
}

##end::Durusmalar Modulu Global Fonksiyonları 



?>
