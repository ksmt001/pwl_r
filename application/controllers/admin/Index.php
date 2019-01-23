<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends CI_Controller {

	function __construct(){
		parent::__construct();
	
		if($this->session->userdata('status') != "login"){
			redirect(base_url("Admin/Con_login/index"));
		}
	}
 
	public function index(){
		base_url('Admin/Con_Login');

	}

}