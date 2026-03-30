<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Units extends CI_Controller {
    public $viewFolder 	= "";
  	public $userData		= false;

    public function __construct(){
        parent::__construct();
        $this->viewFolder = "units_v";
        $this->load->model("units_model");

        $this->load->model("auth_model");
        $this->userData = $this->auth_model->userData;
    }


  	public function index(){
  		if($this->userData === false){
  				redirect(base_url("login"));
  				exit;
  		}

      if(!isDbAllowedViewModule()){
          redirect(base_url("dashboard"));
          exit;
      }

      $itemList = $this->units_model->get_all(
          array(
              "ub_status !="  => -1
          )
      );

  		$viewData = new stdClass();
  		$viewData->viewFolder     = $this->viewFolder;
  		$viewData->subViewFolder	= "list";
      $viewData->userData 			= $this->userData;
  		$viewData->itemList 			= $itemList;
  		$this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
  	}

    public function isActiveSetter($id){
        if($this->userData === false){
            return false;
            exit;
        }else{
            if(!isDbAllowedUpdateModule()){
                return false;
                exit;
            }else{
                $_id = (int)trim(replacePost($id));
                  if($_id){
                      if($_id==$this->userData->userB->ub_id){
                        return false;
                      }else{
                        $isActive = ($this->input->post("data") === "true") ? 1 : 0;
                        $this->units_model->update(
                          array(
                              "ub_id"  => $_id
                          ),
                          array(
                              "ub_status" => $isActive
                          )
                        );
                        return true;
                      }
                  }
            }
        }
    }

    public function remove($id){
        if($this->userData === false){
    				redirect(base_url("login"));
    				exit;
    		}

        if(!isDbAllowedDeleteModule()){
            redirect(base_url($this->router->fetch_class()));
            exit;
        }


        $_id = (int)trim(replacePost($id));
        if($_id<=0){
          $alert = array(
              "title" => "Hata!",
              "text"  => "Silinecek Birim Bulunamadı. İlgili Kullanıcının Birimi Silme Yetkiniz Bulunmuyor Olabilir.",
              "type"  => "error"
          );
          $this->session->set_flashdata("alertToastr", $alert);
          redirect(base_url("units")); exit;
        }

        $_isUnitInUser = $this->units_model->ek_get(
            "r8t_users",
            array(
                "u_unit" => $_id
            )
        );

        if($_isUnitInUser){
            $alert = array(
                "title" => "Hata!",
                "text"  => "Silinecek Birime Bağlı Kullanıcı Kaydı Bulunmaktadır. Lütfen Önce Birime Bağlı Kullanıcıları Güncelleyiniz.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alert", $alert);
            redirect(base_url("units")); exit;
        }

        $_delete = $this->units_model->update(
          array(
              "ub_id"  => $_id,
              "ub_id >=" => $this->userData->userB->ub_id
          ),
          array(
              "ub_status" => -1
          )
        );

        if($_delete){
            $alert = array(
                "title" => "Tebrikler!",
                "text"  => "Birim Başarıyla Silindi.",
                "type"  => "success"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('units'));
        }else{
            $alert = array(
                "title" => "Hata!",
                "text"  => "Birim Silinemedi.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('units'));
        }

    }


    public function new_form(){
      if($this->userData === false){
  				redirect(base_url("login"));
  				exit;
  		}

      if(!isDbAllowedWriteModule()){
          redirect(base_url($this->router->fetch_class()));
          exit;
      }


      $colorVal = "success";
  		$viewData = new stdClass();
  		$viewData->viewFolder     = $this->viewFolder;
  		$viewData->subViewFolder	= "add";
  		$viewData->userData 			= $this->userData;
      $viewData->colorVal 			= $colorVal;
  		$this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);

    }

    public function save(){
      if($this->userData === false){
          redirect(base_url("login"));
          exit;
      }

      if(!isDbAllowedWriteModule()){
          redirect(base_url($this->router->fetch_class()));
          exit;
      }

      $this->load->library("form_validation");

      // Kurallar Yazılır..
      $this->form_validation->set_rules("name", "Birim Adı", "required|trim|min_length[3]|max_length[30]");
      $this->form_validation->set_rules("short_name", "Kısa Adı", "required|trim|min_length[2]|max_length[10]");
      $this->form_validation->set_rules("description", "Birim Açıklaması", "required|trim|min_length[5]|max_length[255]");

      $this->form_validation->set_message(
        array(
            "required"    => "<b>{field}</b> alanı doldurulmalıdır.",
            "min_length"  => "<b>{field}</b> en az %s karakterden oluşmalıdır.",
            "max_length"  => "<b>{field}</b> en fazla %s karakterden oluşmalıdır.",
            "alpha"       => "<b>{field}</b> alanı sadece harf değerleri almalıdır.",
        )
      );
      if($this->form_validation->run() == FALSE){
          $viewData = new stdClass();
          $viewData->viewFolder     = $this->viewFolder;
          $viewData->subViewFolder	= "add";
          $viewData->userData 			= $this->userData;
          $viewData->form_error     = true;

          $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);

      }else{

          $_name        = trim(replacePost($this->input->post("name")));
          $_shor_name   = trim(replacePost($this->input->post("short_name")));
          $_description = trim(replacePost($this->input->post("description")));

          $save = $this->units_model->add(
              array(
                  "ub_title"          => $_name,
                  "ub_short_title"    => $_shor_name,
                  "ub_description"    => $_description,
                  "ub_status"         => 0,
                  "ub_adddate"        => dateToTime(date("Y-m-d H:i:s")),
                  "ub_adduser"        => $this->userData->userB->u_id
              )
          );

          if($save){
              $alert = array(
                  "title" => "Tebrikler!",
                  "text"  => "$_name İsimli Birim Başarıyla Kaydedildi.",
                  "type"  => "success"
              );
              $this->session->set_flashdata("alertToastr", $alert);
              redirect(base_url('units'));
          }else{
              $alert = array(
                  "title" => "Hata!",
                  "text"  => "$_name İsimli Birim Kaydı Yapılamadı.",
                  "type"  => "error"
              );
              $this->session->set_flashdata("alertToastr", $alert);
              redirect(base_url('units'));
          }
      }


    }



    public function update_form($id){
      if($this->userData === false){
  				redirect(base_url("login"));
  				exit;
  		}

      if(!isDbAllowedUpdateModule()){
          redirect(base_url($this->router->fetch_class()));
          exit;
      }

      $_id = (int)trim(replacePost($id));
      if($_id<=0){
        $alert = array(
            "title" => "Hata!",
            "text"  => "Düzenlenecek Birim Kaydı Bulunamadı. İlgili Birimi Düzenleme Yetkiniz Bulunmuyor Olabilir.",
            "type"  => "error"
        );
        $this->session->set_flashdata("alertToastr", $alert);
        redirect(base_url("units")); exit;
      }

      $item = $this->units_model->get(
          array(
              "ub_id"             => $_id,
              "ub_status !="      => -1
          )
      );

      if(!$item){
        $alert = array(
            "title" => "Hata!",
            "text"  => "Düzenlenecek Birim Kaydı Bulunamadı. İlgili Birimi Düzenleme Yetkiniz Bulunmuyor Olabilir.",
            "type"  => "error"
        );
        $this->session->set_flashdata("alertToastr", $alert);
        redirect(base_url('units'));
        exit;
      }

  		$viewData = new stdClass();
  		$viewData->viewFolder     = $this->viewFolder;
  		$viewData->subViewFolder	= "update";
  		$viewData->userData 			= $this->userData;
      $viewData->item           = $item;
  		$this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);

    }

    public function update($id){
      if($this->userData === false){
  				redirect(base_url("login"));
  				exit;
  		}

      if(!isDbAllowedUpdateModule()){
          redirect(base_url($this->router->fetch_class()));
          exit;
      }

      $_id = (int)trim(replacePost($id));
      if($_id<=0){
        $alert = array(
            "title" => "Hata!",
            "text"  => "Düzenlenecek Birim Kaydı Bulunamadı. İlgili Birimi Düzenleme Yetkiniz Bulunmuyor Olabilir.",
            "type"  => "error"
        );
        $this->session->set_flashdata("alertToastr", $alert);
        redirect(base_url("units")); exit;
      }

      $item = $this->units_model->get(
          array(
              "ub_id"             => $_id,
              "ub_status !="      => -1
          )
      );

      if(!$item){
        $alert = array(
            "title" => "Hata!",
            "text"  => "Düzenlenecek Birim Kaydı Bulunamadı. İlgili Birimi Düzenleme Yetkiniz Bulunmuyor Olabilir.",
            "type"  => "error"
        );
        $this->session->set_flashdata("alertToastr", $alert);
        redirect(base_url('units'));
        exit;
      }


      $this->load->library("form_validation");

      // Kurallar Yazılır..
      $this->form_validation->set_rules("name", "Birim Adı", "required|trim|min_length[3]|max_length[30]");
      $this->form_validation->set_rules("short_name", "Kısa Adı", "required|trim|min_length[2]|max_length[10]");
      $this->form_validation->set_rules("description", "Grup Açıklaması", "required|trim|min_length[5]|max_length[255]");

      $this->form_validation->set_message(
        array(
            "required"    => "<b>{field}</b> alanı doldurulmalıdır.",
            "min_length"  => "<b>{field}</b> en az %s karakterden oluşmalıdır.",
            "max_length"  => "<b>{field}</b> en fazla %s karakterden oluşmalıdır.",
            "alpha"       => "<b>{field}</b> alanı sadece harf değerleri almalıdır.",
        )
      );
      if($this->form_validation->run() == FALSE){
          $viewData = new stdClass();
          $viewData->viewFolder     = $this->viewFolder;
          $viewData->subViewFolder	= "update";
          $viewData->userData 			= $this->userData;
          $viewData->form_error     = true;

          $this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);

      }else{

          $_name        = trim(replacePost($this->input->post("name")));
          $_short_name  = trim(replacePost($this->input->post("short_name")));
          $_description = trim(replacePost($this->input->post("description")));

          $_update = $this->units_model->update(
              array(
                  "ub_id"             => $_id,
                  "ub_status !="      => -1
              ),
              array(
                  "ub_title"        => $_name,
                  "ub_short_title"  => $_short_name,
                  "ub_description"  => $_description,
                  "ub_editdate"     => dateToTime(date("Y-m-d H:i:s")),
                  "ub_edituser"     => $this->userData->userB->u_id
              )
          );

          if($_update){
              $alert = array(
                  "title" => "Tebrikler!",
                  "text"  => "$_name İsimli Birim Başarıyla Güncellendi.",
                  "type"  => "success"
              );
              $this->session->set_flashdata("alertToastr", $alert);
              redirect(base_url('units'));
          }else{
              $alert = array(
                  "title" => "Hata!",
                  "text"  => "$_name İsimli Birim Güncellenemedi.",
                  "type"  => "error"
              );
              $this->session->set_flashdata("alertToastr", $alert);
              redirect(base_url('units'));
          }
      }


    }



  	public function app_permissions($id){
  		if($this->userData === false){
  				redirect(base_url("login"));
  				exit;
  		}

      if(!isDbAllowedUpdateModule() || !isDbAdminViewModule()){
          redirect(base_url($this->router->fetch_class()));
          exit;
      }

      $_id = (int)trim(replacePost($id));
      if($_id<=0){
        $alert = array(
            "title" => "Hata!",
            "text"  => "Yetkilendirme Yapılacak Birim Kaydı Bulunamadı. İlgili Birimi Düzenleme Yetkiniz Bulunmuyor Olabilir.",
            "type"  => "error"
        );
        $this->session->set_flashdata("alertToastr", $alert);
        redirect(base_url("units")); exit;
      }


      $item = $this->units_model->get(
          array(
              "ub_id"             => $_id,
              "ub_status !="      => -1
          )
      );

      if(!$item){
        $alert = array(
            "title" => "Hata!",
            "text"  => "Yetkilendirme Yapılacak Birim Kaydı Bulunamadı. İlgili Birimi Düzenleme Yetkiniz Bulunmuyor Olabilir.",
            "type"  => "error"
        );
        $this->session->set_flashdata("alertToastr", $alert);
        redirect(base_url('units'));
        exit;
      }

      $getApps = getAppList();

      $appList = $this->units_model->appUpdate($getApps);

      if($appList){
          foreach($appList as $app){
                $getDbPermissions = $this->units_model->ek_get(
                      "r8t_sys_unit_app_permissions",
                      array(
                          "up_appzone"    => "sys",
                          "up_unitid"     => $item->ub_id,
                          "up_appcode"    => $app->a_appcode
                      )
                );
                if($getDbPermissions){
                    //Birim-Uygulama Düzeyinde Permission Kaydı Varsa Başlıkları Güncelle
                    $update = $this->units_model->ek_update(
                          "r8t_sys_unit_app_permissions",
                          array(
                            "up_appzone"    => "sys",
                            "up_unitid"     => $item->ub_id,
                            "up_appcode"    => $app->a_appcode
                          ),
                          array(
                            "up_json"       => json_encode($app)
                          )
                    );
                }else{
                    $add = $this->units_model->ek_add(
                          "r8t_sys_unit_app_permissions",
                          array(
                              "up_appzone"      => "sys",
                              "up_unitid"       => $item->ub_id,
                              "up_appcode"      => $app->a_appcode,
                              "up_json"         => json_encode($app)
                          )
                    );
                }
          }
      }

      // Login Olan Kullanıcı Grubu Root İse
      if($this->userData->userB->ug_id==1){
          //DB Deki Controllerları Çek
          $apps = $this->units_model->ek_join_get_all(
                "r8t_sys_unit_app_permissions",
                array(
          				"r8t_sys_apps" => "r8t_sys_apps.a_appcode = r8t_sys_unit_app_permissions.up_appcode"
          			),
          			"INNER",
                array(
                  "up_appzone"  => "sys",
                  "up_unitid"  => $item->ub_id,
                  "r8t_sys_apps.a_status !="    => -1
          			),
          			false
          );
      }else{
          //Login Olan Kullanıcı Grubunun Admin Yetkisi Olduğu Controllerları Çek
          $queryX = "SELECT * FROM r8t_sys_unit_app_permissions AS UP INNER JOIN r8t_sys_apps AS APP ON APP.a_appcode= UP.up_appcode WHERE APP.a_status!=-1 AND UP.up_appzone='sys' AND UP.up_unitid={$item->ub_id}";
          $queryText = "";
          foreach($appList as $app){
              if(isAdminViewApp($app->a_appcode)==false){
                  $queryText .= " AND UP.up_appcode!='{$app->a_appcode}'";
              }
          }
          if($queryText!=""){ $queryX .= " AND (UP.up_appcode!='xyz'{$queryText})"; }
          $apps = $this->units_model->ek_query_all($queryX);

      }

  		$viewData = new stdClass();
  		$viewData->viewFolder     = $this->viewFolder;
  		$viewData->subViewFolder	= "app_permissions";
  		$viewData->userData 			= $this->userData;
      $viewData->item           = $item;
      $viewData->getApps        = $apps;
  		$this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
  	}

    public function update_app_permissions($id){
        if($this->userData === false){
    				redirect(base_url("login"));
    				exit;
    		}

        if(!isDbAllowedUpdateModule() || !isDbAdminViewModule()){
            redirect(base_url($this->router->fetch_class()));
            exit;
        }

        $_id = (int)trim(replacePost($id));
        if($_id<=0){
          $alert = array(
              "title" => "Hata!",
              "text"  => "Yetkilendirme Yapılacak Birim Kaydı Bulunamadı. İlgili Birimi Düzenleme Yetkiniz Bulunmuyor Olabilir.",
              "type"  => "error"
          );
          $this->session->set_flashdata("alertToastr", $alert);
          redirect(base_url("units")); exit;
        }

        if(!is_array($this->input->post("permissions"))){
          $alert = array(
            "title" => "Hata!",
            "text"  => "Yetkilendirme Yapılacak Birim Kaydı Bulunamadı. İlgili Birimi Düzenleme Yetkiniz Bulunmuyor Olabilir.",
            "type"  => "error"
          );
          $this->session->set_flashdata("alertToastr", $alert);
          redirect(base_url('units'));
          exit;
        }

        $item = $this->units_model->get(
            array(
                "ub_id"             => $_id,
                "ub_status !="      => -1
            )
        );

        if(!$item){
          $alert = array(
              "title" => "Hata!",
              "text"  => "Yetkilendirme Yapılacak Birim Kaydı Bulunamadı. İlgili Birimi Düzenleme Yetkiniz Bulunmuyor Olabilir.",
              "type"  => "error"
          );
          $this->session->set_flashdata("alertToastr", $alert);
          redirect(base_url('units'));
          exit;
        }


        // Login Olan Kullanıcı Grubu Root İse
        if($this->userData->userB->ug_id==1){
            //DB Deki Controllerları Çek
            $apps = $this->units_model->ek_join_get_all(
                  "r8t_sys_unit_app_permissions",
                  array(
            				"r8t_sys_apps" => "r8t_sys_apps.a_appcode = r8t_sys_unit_app_permissions.up_appcode"
            			),
            			"INNER",
                  array(
                    "up_appzone"  => "sys",
                    "up_unitid"  => $item->ub_id,
                    "r8t_sys_apps.a_status !="    => -1
            			),
            			false
            );
        }else{
            //Login Olan Kullanıcı Grubunun Admin Yetkisi Olduğu Controllerları Çek
            $queryX = "SELECT * FROM r8t_sys_unit_app_permissions AS UP INNER JOIN r8t_sys_apps AS APP ON APP.a_appcode= UP.up_appcode WHERE APP.a_status!=-1 AND UP.up_appzone='sys' AND UP.up_unitid={$item->ub_id}";
            $queryText = "";
            foreach($appList as $app){
                if(isAdminViewApp($app->a_appcode)==false){
                    $queryText .= " AND UP.up_appcode!='{$app->a_appcode}'";
                }
            }
            if($queryText!=""){ $queryX .= " AND (UP.up_appcode!='xyz'{$queryText})"; }
            $apps = $this->units_model->ek_query_all($queryX);

        }


        $_permissions = $this->input->post("permissions");


        foreach($apps as $app){
            $_update = $this->units_model->ek_update(
                "r8t_sys_unit_app_permissions",
                array(
                    "up_appzone"    => "sys",
                    "up_unitid"    => $item->ub_id,
                    "up_appcode"    => $app->up_appcode
                ),
                array(
                    "up_adminr"     => (isset($_permissions[$app->up_appcode]['adminr'])) ? 1 : null,
                    "up_list"       => (isset($_permissions[$app->up_appcode]['list'])) ? 1 : null,
                    "up_write"      => (isset($_permissions[$app->up_appcode]['write'])) ? 1 : null,
                    "up_update"     => (isset($_permissions[$app->up_appcode]['update'])) ? 1 : null,
                    "up_delete"     => (isset($_permissions[$app->up_appcode]['delete'])) ? 1 : null,
                    "up_read"       => (isset($_permissions[$app->up_appcode]['read'])) ? 1 : null,
                )
            );
        }


        if($_update){
            $alert = array(
                "title" => "Tebrikler!",
                "text"  => $item->ub_title." - Uygulama Yetkilendirmesi Başarıyla Güncellendi.",
                "type"  => "success"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('units'));
        }else{
            $alert = array(
                "title" => "Hata!",
                "text"  => $item->ub_title." - Uygulama Yetkilendirmesi Güncellenemedi.",
                "type"  => "error"
            );
            $this->session->set_flashdata("alertToastr", $alert);
            redirect(base_url('units'));
        }

    }


}
?>
