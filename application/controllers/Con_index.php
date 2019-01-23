<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Con_Index extends CI_Controller {
	
	 public function __construct()
  { 
    parent::__construct();
    $this->load->library('cart');
    $this->load->model('M_Menu');
  }

	public function index(){	 

    $this->load->view('index');

	}
}
