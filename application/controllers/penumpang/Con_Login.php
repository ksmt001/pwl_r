<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Con_login extends CI_Controller {
	function __construct(){
		parent::__construct();		
		$this->load->model('M_Login');
		$this->load->library('calendar');
		
 
	}

	public function index(){
		 $this->load->helper('url'); 
		 $query = $this->db->query("Select * from t_menu");
		 $data['menu'] = $query->result();
		 
		$this->load->view('index',$data);

	}
	public function menu(){
		 $this->load->helper('url'); 
		 $query = $this->db->query("Select * from t_menu");
	     $data['menu'] = $query->result();

		 $this->load->view('menu',$data);
	}

	public function validate(){
		$this->load->helper('date');

		
		$kode_tiket = $this->input->post('kode_tiket');
		$tgl_berangkat = $this->db->query("Select tgl_berangkat from t_penumpang where kode_tiket = '$kode_tiket'")->result();
		//$nuhun = new DateTime($this->input->post('tgl_sekarang'));
		//date_format($nuhun, 'Y-m-d');
		//$nuhun = date_parse($this->input->post('tgl_sekarang'));
		$nuhun = $this->input->post('tgl_sekarang');
	
		$dateStart = new DateTime($nuhun);
		$dateEnd = new DateTime($tgl_berangkat[0]->tgl_berangkat);

		$diffDay = (int) date_diff($dateStart, $dateEnd)->format("%r%a");
			
			// var_dump($diffDay);
			// die();

		//$query = $this->db->query("Select * from t_penumpang where tgl_sekarang beetwen tgl_berangkat");
		//$range = date_range($nuhun, 'tgl_berangkat');
		
		$where = array(
			'kode_tiket' => $kode_tiket
			// 'tgl_berangkat' => $dateEnd
			
			);
		
		$cek = $this->M_Login->cek_login("t_penumpang",$where)->num_rows();
		if($cek > 0 && $diffDay > 0){
	 
			$data_session = array(
				'nama' => $kode_tiket,
				'status' => "login"
				);
	 
			$this->session->set_userdata($data_session);

			redirect(base_url("penumpang/Pemesanan/tiket"));
	
		}else{
			$this->session->set_flashdata('pesan','Kode Tiket Salah!');
			redirect('Penumpang/Con_login','refresh');
		}
	}
	
	function logout(){
		$this->session->sess_destroy();
		redirect(base_url('penumpang/Con_login/index'));
	}

}
