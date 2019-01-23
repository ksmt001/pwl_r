<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Con_Penumpang extends CI_Controller {
	
	public function index(){
			$this->load->helper('form');
			$kode_tiket= $this->session->userdata("nama");
			$query = $this->db->query('select * from t_penumpang WHERE kode_tiket="'.$kode_tiket.'"');
			$data['records'] = $query->result();
			$data['old_id'] = $kode_tiket;

			$this->load->view('penumpang/v_penumpang',$data);
	}

// ORDER FOOD
	public function Menu_food(){
		 $this->load->helper('url'); 
		 $query = $this->db->query("Select * from t_menu INNER JOiN t_jenismenu WHERE t_menu.id_jenis=t_jenismenu.id_jenis");
	     $data['m_food'] = $query->result();
		
	     $query2 = $this->db->query("Select * from t_temp INNER JOiN t_penumpang WHERE t_temp.kode_tiket=t_penumpang.kode_tiket");
	     $data['temp'] = $query2->result();
		
		 $this->load->view('penumpang/v_menu_food',$data);
	}

	public function add_list(){
		 $this->load->model('M_Food_Order');		
		 $data = array(
			'id_order' => $this->input->post('id_order'),
			'kode_tiket'=> $this->input->post('kode_tiket'),
			'id_menu' => $this->input->post('id_menu'),
			'jumlah' => $this->input->post('jumlah'),
			'harga' => $this->input->post('harga')

		  ); 		

		 $this->M_Food_Order->insert_temp($data); 
		 $this->M_Food_Order->insert_orderdetail($data); 

		 $query = $this->db->get("t_temp"); 
		 $data['records'] = $query->result(); 

		 //$this->load->view('penumpang/v_menu_food',$data);

		 redirect('penumpang/Con_Penumpang/Menu_food');
	}
	public function detail_pesanan(){
		 $kode_tiket= $this->session->userdata("nama");		
	     $query2 = $this->db->query("Select t_temp.id_order, t_temp.kode_tiket,t_temp.id_menu, t_temp.jumlah,t_menu.nama_menu, t_menu.harga from t_temp INNER JOIN t_penumpang INNER JOIN t_menu INNER JOIN t_jenismenu WHERE t_menu.id_menu=t_temp.id_menu AND t_temp.kode_tiket='".$kode_tiket."' GROUP BY t_temp.id ");
		 
	     $data['temp'] = $query2->result();

 		 $this->load->view('penumpang/v_detail_order',$data);

	}

	public function add_pesanan(){
		 $this->load->model('M_Food_Order');		
		 $old_id= $this->session->userdata("nama");		 

		 $data = array(
			'id_order' => $this->input->post('id_order'),
			'kode_tiket'=> $this->input->post('kode_tiket'),
			'total'=> $this->input->post('total')
			
		  ); 		

		 $this->M_Food_Order->insert_order($data);
	
			 $query = $this->db->query("select * from t_order"); 
			 $data['order'] = $query->result(); 

			$data2 = array(
			'tanggal'=> date('Y-m-d'),	
			'harga'=> $this->input->post('harga'),
			'sub_total'=> $this->input->post('sub_total')
			);

			$this->M_Food_Order->update_detailorder($data2,$old_id);

			
			$where = array('kode_tiket' => $old_id);
			$this->M_Food_Order->delete($where,'t_temp');


		 //$this->load->view('penumpang/v_menu_food',$data);
		 redirect('penumpang/Con_Penumpang/Menu_food');
	}




	public function View_food_order(){
		 $this->load->helper('url'); 
		 $kode_tiket= $this->session->userdata("nama");		
		 
		 $query = $this->db->query("Select * from t_food INNER JOIN t_order WHERE t_order.status='Belum Dilayani' AND t_order.kode_tiket='".$kode_tiket."' AND t_food.kode_food=t_order.kode_order");
		 $data['order_food'] = $query->result();

		 $query2 = $this->db->query("Select * from t_order INNER JOIN t_food WHERE t_order.status='Sedang dilayani' AND t_order.kode_tiket='".$kode_tiket."' AND t_food.kode_food=t_order.kode_order");
	     $data['order_served'] = $query2->result();

		
		$this->load->view('penumpang/v_order_food_view',$data);
	}

    public function update_order_food(){
 			$this->load->model('M_Food_Order');
	         $old_id= $this->uri->segment('4'); 
		

			$data = array(
			'status' => 'Cancel'
			);

			$this->M_Food_Order->update($data,$old_id);
			$query = $this->db->get('t_order');
	
//			echo '$this->M_Food_Order->update($data,$old_id)';
	
			redirect('penumpang/Con_Penumpang/View_food_order');
    }


//ORDER DRINK

	public function Menu_drink(){
		 $this->load->helper('url'); 
		 $query = $this->db->query("Select * from t_drink");
	     $data['m_drink'] = $query->result();
		
		$this->load->view('penumpang/v_menu_drink',$data);
	}

	public function drink_order(){
		 $this->load->model('M_Drink_Order');		
		 $data = array(
			'id_order' => $this->input->post('id_order'),
			'kode_order' => $this->input->post('kode_order'),
			'kode_tiket'=> $this->input->post('kode_tiket'),
			'jumlah' => $this->input->post('jumlah'),
			'note' => $this->input->post('note')
		  ); 		
		 $this->M_Drink_Order->insert($data); 
		 $query = $this->db->get("t_order"); 
		 $data['records'] = $query->result(); 
		 redirect('penumpang/Con_Penumpang/notif_sukses');
	}



	public function View_drink_order(){
		 $this->load->helper('url'); 
		 $kode_tiket= $this->session->userdata("nama");		
		 $query = $this->db->query("Select * from t_drink INNER JOIN t_order WHERE t_order.status='Belum Dilayani' AND t_order.kode_tiket='".$kode_tiket."' AND t_drink.kode_drink=t_order.kode_order");
	     $data['order_drink'] = $query->result();

	    $query2 = $this->db->query("Select * from t_order INNER JOIN t_drink WHERE t_order.status='Sedang dilayani' AND t_order.kode_tiket='".$kode_tiket."' AND t_drink.kode_drink=t_order.kode_order");
	     $data['order_served'] = $query2->result();
		
		$this->load->view('penumpang/v_order_drink_view',$data);
	}


	public function update_order_drink(){
 		$this->load->model('M_Drink_Order');
	    $old_id= $this->uri->segment('4'); 

		$data = array(
		'status' => 'Cancel'
		);

		$this->M_Drink_Order->update($data,$old_id);
		$query = $this->db->get('t_order');
	
//			echo '$this->M_Food_Order->update($data,$old_id)';
	
		redirect('penumpang/Con_Penumpang/View_drink_order');
    }



	public function view_message(){
		$this->load->helper('form');
		$kode_tiket = $this->uri->segment('4');			
		$id_order = $this->uri->segment('5');			

		 $query = $this->db->query("Select * from t_order INNER JOIN t_penumpang WHERE t_penumpang.kode_tiket='".$kode_tiket."' AND t_order.id_order='".$id_order."' ");
	     $data['message'] = $query->result();
 		 $data['old_id'] = $kode_tiket;
	
 		 $this->load->view('penumpang/v_message',$data);	
	}

	public function message(){
		$this->load->model('M_Message');		

		$kode_tiket= $this->uri->segment('4'); 
		$id_order= $this->uri->segment('5');
	
		 $data = array(
			'kode_tiket' => $this->input->post('kode_tiket'),
			'id_order'=> $this->input->post('id_order'),
			'isi_message' => $this->input->post('isi_pesan'),
			'status' => "Pesanan belum dilayani",
			'tanggal' => $this->input->post('tanggal')


		  ); 		
		 $this->M_Message->insert($data); 
		 $query = $this->db->get("t_message"); 
		 $data['message'] = $query->result(); 
		
		redirect('penumpang/Con_Penumpang/');		
 		 //$this->load->view('penumpang/v_message',$data);	

	}

	public function notif_sukses(){
		$this->load->view('penumpang/v_notif_sukses');	
	}

//-----------------------------------------REAL TIME ORDER ----------------------------------
		public function n_order_food_acc(){
		 $kode_tiket= $this->session->userdata("nama");		
		 
		 $query2 = $this->db->query("Select * from t_order INNER JOIN t_food WHERE t_order.status='Sedang dilayani' AND t_order.kode_tiket='".$kode_tiket."' AND t_food.kode_food=t_order.kode_order");
		 	echo"						<tr>
											<th>ID Order</th>
											<th>Kode Order</th>
											<th>Nama Makanan</th>
											<th>Jumlah</th>
											<th>Note</th>
											<th>Status</th>
											<th>Total Pembayaran</th>
										</tr>";	
											foreach($query2->result() as $ofs) { 
												$total =$ofs->harga*$ofs->jumlah; 
			echo"						<tr>
											<td>$ofs->id_order; </td>
											<td>$ofs->kode_order; </td>
											<td> $ofs->nama_food; </td>
											<td> $ofs->jumlah;</td>
											<td> $ofs->note; </td>
											<td> $ofs->status;</td>
											<td> $total;   </td>
										</tr>";
											 } 
									echo"</table>";

	}
}