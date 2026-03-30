<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public $viewFolder 	= "";
	public $userData		= false;

	public function __construct(){
			parent::__construct();

			$this->viewFolder = "dashboard_v";
			$this->load->model("auth_model");
			$this->userData = $this->auth_model->userData;
	}

	public function index(){
		if($this->userData === false){
				redirect(base_url("login"));
				exit;
		}

		if(!isDbAllowedViewModule()){
				redirect(base_url("home"));
				exit;
		}


		$viewData = new stdClass();
		$viewData->viewFolder     = $this->viewFolder;
		$viewData->subViewFolder	= "view";
		$viewData->userData 			= $this->userData;
		$this->load->view("{$this->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
	}

}
