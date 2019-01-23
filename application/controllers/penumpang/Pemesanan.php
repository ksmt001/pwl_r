<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pemesanan extends CI_Controller {

	public function __construct()
	{	
		parent::__construct();
		$this->load->library('cart');
		$this->load->model('M_pemesanan');
		$this->load->model('M_Message');
	}

	public function index()
	{
		$kategori=($this->uri->segment(4))?$this->uri->segment(4):0;
		$data['menu'] = $this->M_pemesanan->get_menu_kategori($kategori);
		$data['kategori'] = $this->M_pemesanan->get_kategori_all();
		$this->load->view('penumpang/header',$data);
		$this->load->view('penumpang/pemesanan/list_menu',$data);
	}

	public function tiket()
	{	
			$this->load->helper('form');
			$kode_tiket= $this->session->userdata("nama");
			$query = $this->db->query('select * from t_penumpang WHERE kode_tiket="'.$kode_tiket.'"');
			$data['records'] = $query->result();
			$data['old_id'] = $kode_tiket;
	
			$kategori=($this->uri->segment(3))?$this->uri->segment(3):0;
			$data['menu'] = $this->M_pemesanan->get_menu_kategori($kategori);
			$data['kategori'] = $this->M_pemesanan->get_kategori_all();


			$this->load->view('penumpang/header',$data);
			$this->load->view('penumpang/pages/tiket',$data);
			
	}
	public function tampil_cart()
	{
		$data['kategori'] = $this->M_pemesanan->get_kategori_all();
		$this->load->view('penumpang/header',$data);
		$this->load->view('penumpang/pemesanan/tampil_cart',$data);
	}
	
	public function check_out()
	{
		$kode_tiket = $this->session->userdata("nama");
		$query = $this->db->query("Select * from t_penumpang WHERE kode_tiket='".$kode_tiket."'");
		$data['penumpang'] = $query->result();

		$data['kategori'] = $this->M_pemesanan->get_kategori_all();
		$this->load->view('penumpang/header',$data);
		$this->load->view('penumpang/pemesanan/tampil_cart',$data);
		$this->load->view('penumpang/pemesanan/check_out',$data);
	}
	
	public function detail_menu()
	{
		$id=($this->uri->segment(4))?$this->uri->segment(4):0;
		$data['kategori'] = $this->M_pemesanan->get_kategori_all();
		$data['detail'] = $this->M_pemesanan->get_menu_id($id)->row_array();
		$this->load->view('penumpang/header',$data);
		$this->load->view('penumpang/pemesanan/detail_menu',$data);
	}

	
	
	function tambah()
	{
		$data_produk= array('id' => $this->input->post('id'),
							 'name' => $this->input->post('nama'),
							 'price' => $this->input->post('harga'),
							 'gambar' => $this->input->post('gambar'),
							 'qty' =>$this->input->post('qty')
							);
		$this->cart->insert($data_produk);
		redirect('penumpang/pemesanan');
	}

	function hapus($rowid) 
	{
		if ($rowid=="all")
			{
				$this->cart->destroy();
			}
		else
			{
				$data = array('rowid' => $rowid,
			  				  'qty' =>0);
				$this->cart->update($data);
			}
		redirect('penumpang/pemesanan/tampil_cart');
	}

	function ubah_cart()
	{
		$cart_info = $_POST['cart'] ;
		foreach( $cart_info as $id => $cart)
		{
			$rowid = $cart['rowid'];
			$price = $cart['price'];
			$gambar = $cart['gambar'];
			$amount = $price * $cart['qty'];
			$qty = $cart['qty'];
			$data = array('rowid' => $rowid,
							'price' => $price,
							'gambar' => $gambar,
							'amount' => $amount,
							'qty' => $qty);
			$this->cart->update($data);
		}
		redirect('penumpang/pemesanan/tampil_cart');
	}

	public function proses_order()
	{
	
		$data_order = array('tanggal' => date('Y-m-d'),
					   		'kode_tiket' => $this->input->post('kode_tiket'),
					   		'total' => $this->input->post('total')
					   	);
		$id_order = $this->M_pemesanan->tambah_order($data_order);
		//-------------------------Input data detail order-----------------------		
		if ($cart = $this->cart->contents())
			{
				foreach ($cart as $item)
					{
						$data_detail = array('id_transaksi' =>$id_order,
										'id_menu' => $item['id'],
										'jumlah' => $item['qty'],
										'tanggal'=> date('Y-m-d'),
										'harga' => $item['price']);			
						$proses = $this->M_pemesanan->tambah_detail_order($data_detail);
					}
			}
		//-------------------------Hapus shopping cart--------------------------		
		$this->cart->destroy();
		$data['kategori'] = $this->M_pemesanan->get_kategori_all();
		$this->load->view('penumpang/header',$data);
		$this->load->view('penumpang/pemesanan/sukses',$data);
		
	}
	
}
?>