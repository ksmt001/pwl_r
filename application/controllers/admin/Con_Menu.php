<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Con_Menu extends CI_Controller {

  public function __construct()
  { 
    parent::__construct();
     $this->load->library('pdf');
    $this->load->library('cart');
    $this->load->model('M_Menu');
    $this->load->model('M_order');
    $this->load->model('M_Message');
    $this->load->library('session');
  }

	public function index(){	 
    $this->load->helper('form');
    $username = $this->session->userdata("nama");
    $this->load->view('admin/header');
    $this->load->view('admin/pages/tentang'); 
    

	}

	public function view_menu(){
      $tgl_skrng=date('Y-m-d');
      $username= $this->session->userdata("nama");
      $data['menu'] = $this->M_Menu->get_menu_all();
      $this->load->view('admin/header');
      $this->load->view('admin/pages/v_menu',$data);
      
	}

	public function add_view_menu(){	 
    $username= $this->session->userdata("nama");
    $data['kategori'] = $this->M_Menu->get_kategori();
    $this->load->view('admin/header');
    $this->load->view('admin/pages/v_menu_add',$data);
    
	}
	
	public function tambah() {
       $username= $this->session->userdata("nama");
		   $data = array(
			'nama_menu' => $this->input->post('nama_menu'),
			'deskripsi' => $this->input->post('deskripsi'),
			'harga' => $this->input->post('harga'),
			'kategori' => $this->input->post('kategori'),
		);
		 
		 $this->M_Menu->insert_menu($data); 
		 redirect('admin/Con_menu/view_menu');		
		}

  public function edit_view_menu(){   
    $id_menu= $this->uri->segment('4'); 
    
    $data['old_id'] = $id_menu;
    $data['menu'] = $this->M_Menu->get_menu_id($id_menu);
    $data['kategori'] = $this->M_Menu->get_kategori();
    $this->load->view('admin/header');
    $this->load->view('admin/pages/v_menu_edit',$data, $id_menu);
    
  }

  public function update(){
      $data = array(
        'nama_menu' => $this->input->post('nama_menu'),
        'deskripsi' => $this->input->post('deskripsi'),
        'harga' => $this->input->post('harga'),
        'kategori' => $this->input->post('kategori'),
       );

      $old_id = $this->input->post('old_id');
      $this->M_Menu->update($data,$old_id);
   
      redirect('admin/Con_menu/view_menu');    
     }
    public function delete(){
      $id_menu= $this->uri->segment('4'); 
      $this->M_Menu->delete($id_menu);

      redirect('admin/Con_menu/view_menu');    
    }

  public function upload_gambar(){
    $id_menu= $this->uri->segment('4'); 
    
    $data['old_id'] = $id_menu;
    $data['menu'] = $this->M_Menu->get_menu_id($id_menu);
    $this->load->view('admin/header');
    $this->load->view('admin/pages/v_upload_menu',$data, $id_menu);
    
  }

    function upload(){
    
        //set preferences
    $id = $this->input->post('old_id');

    $config['upload_path'] = './uploads/menu/';
    $config['upload_url'] = base_url() . "uploads/menu/";
    $config['allowed_types'] = 'jpg|png|jpeg';
    $config['max_size']    = '262144';  
    $config['overwrite'] = FALSE;
    $filename= $id.'.jpg';
    //$namafile=$data2['upload_data']['file_name'];
        $config['file_name'] = $filename;

        //load upload class library
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('filename'))
        {
            // Jika upload gagal
            $upload_error = array('error' => $this->upload->display_errors());
            $this->load->view('admin/pages/v_upload_menu', $upload_error);
        }
        else
        { 

            // Perintah utama mengupload file
      $upload_data = $this->upload->data();
      
      //Mengambil data nama file yg barusan diupload
      $foto = $upload_data['file_name'];
      
      //Membuat Thumbnail
      $type = array('image/png'=>'png','image/jpg'=>'jpg','image/jpeg'=>'jpg');
      $config['image_library'] = 'gd2';     
      $config['overwrite'] = TRUE;
      $config['source_image'] = './uploads/menu/'.$foto;
      $config['create_thumb'] = TRUE;
      $config['maintain_ratio'] = TRUE;
      $config['width']         = 75;
      $config['height']       = 33;
      $config['new_image'] = './uploads/thumb/'.$foto;
      $this->load->library('image_lib', $config);
      $this->image_lib->clear();
      $this->image_lib->initialize($config);
          if(!$this->image_lib->resize())
          { 
              echo $this->image_lib->display_errors();
          } 
      
            //Pesan bila sukses
            $data['success_msg'] = '<div class="alert alert-success text-center">Your file <strong>' . $upload_data['file_name'] . '</strong> was successfully uploaded!</div>';
      
      
      //Update data mahasiswa khususnya field foto
      $data2['gambar']=$foto;
      $old_id = $this->input->post('old_id');       
            $this->M_Menu->update($data2,$old_id);
      
          redirect('admin/Con_menu/view_menu');
        }
    }

	public function notif(){
		$tgl_skrng = date('Y-m-d');

		$query = $this->db->query('select * from t_message INNER JOIN t_penumpang INNER JOIN t_order WHERE t_message.kode_tiket=t_penumpang.kode_tiket AND t_message.id_order=t_order.id_order AND t_message.tanggal="'.$tgl_skrng.'"');
		echo                
							"<thead>
                              <tr>
                                <th>ID Message</th>
                                <th>Kode Tiket</th>
                                <th>Nama </th>
                                <th>ID Order</th>
                                <th>Message</th>
                              </tr>
                              </thead>
                              <tbody>";
                                foreach ($query->result() as $i ) { 
                              echo"<tr>
                                  <td>".$i->id_message."</td>
                                  <td>".$i->kode_tiket."</td>
                                  <td>".$i->nama_penumpang."</td>
                                  <td>".$i->id_order."</td>
                                  <td>".$i->isi_message."</td>
                              </tr>";
                              }
                            echo "</tbody>";
	}
//
//

  public function view_order(){
      $tgl_skrng=date('Y-m-d');
      
      $data['order']   = $this->M_order->get_order_all($tgl_skrng);
      $data2['status'] = $this->M_order->get_order_all_status($tgl_skrng);
      $data3['cancel'] = $this->M_order->get_order_all_cancel($tgl_skrng);
      $data4['done']   = $this->M_order->get_order_all_done($tgl_skrng);

      $this->load->view('admin/header');
      $this->load->view('admin/pages2/v_order',$data);
      $this->load->view('admin/pages2/v_order_acc',$data2);
      $this->load->view('admin/pages2/v_order_done',$data4);
      $this->load->view('admin/pages2/v_order_cancel',$data3);
      
      
  }
    public function detail_pesanan(){
    $this->load->helper('form');
    $tgl_skrng= date('Y-m-d');
    $id_transaksi = $this->uri->segment('4'); 

    $data['order_detail'] = $this->M_order->get_order_detail($id_transaksi);
    

      $this->load->view('admin/header');
      $this->load->view('admin/pages2/v_order_detail',$data);
    
  }

  public function acc_pesanan(){
    $id_transaksi= $this->uri->segment('4'); 

      $data = array(
      'status' => 'Sedang dilayani'
      );

    $this->M_order->update_pesanan($data,$id_transaksi);
    
    redirect('admin/Con_Menu/view_order');
  }

  public function v_printout (){  
      $id_transaksi= $this->uri->segment('4'); 
      $kode_tiket= $this->uri->segment('5'); 
    
      $data['order_detail'] = $this->M_order->get_order_detail($id_transaksi);
      $data['print'] = $this->M_order->get_print_out($id_transaksi,$kode_tiket);

      $this->load->view('admin/header');
      $this->load->view('admin/pages2/v_printout',$data);
     
  }

  public function print_out(){
    $id_transaksi= $this->uri->segment('4'); 

      $data = array(
      'status' => 'Selesai'
      );

    $this->M_order->update_pesanan($data,$id_transaksi);
    
    redirect('admin/Con_Menu/view_order');
  }


  public function view_inbox(){
    $tgl_skrng = date('Y-m-d');
    
    $data['inbox'] = $this->M_Message->inbox_from_penumpang($tgl_skrng);

    $this->load->view('admin/header');
    $this->load->view('admin/pages2/v_inbox',$data);
  
  } 
  public function batal_pesan(){
    $id_transaksi= $this->uri->segment('4'); 

      $data = array(
      'status' => 'Dibatalkan'
      );

    $this->M_order->batal_pesanan_order($data,$id_transaksi);
    
    redirect('admin/Con_Menu/view_order');
  }



  //CETAK PDF
  public function cetakpdf()
  {
     // /echo "coba";
        $pdf = new FPDF();
        // membuat halaman baru
        $pdf->AddPage();
        // setting jenis font yang akan digunakan
        $pdf->SetFont('Arial','B',16);
        // mencetak string 
        $pdf->Cell(190,7,'Laporan',0,1,'C');
        $pdf->SetFont('Arial','B',12);
        // Memberikan space kebawah agar tidak terlalu rapat
        $pdf->Cell(10,7,'',0,1);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(20,6,'id transaksi',1,0);
        $pdf->Cell(40,6,'status',1,0);
        $pdf->Cell(27,6,'kode_tiket',1,0);
        $pdf->Cell(22,6,'tanggal',1,0);
        $pdf->Cell(24,6,'total',1,1);
        // $pdf->Cell(20,6,'harga',1,1);
        
        $pdf->SetFont('Arial','',10);
        $t_order_detail =  $query = $this->db->query("select t_order.id_transaksi, t_order.status, t_order.kode_tiket, t_order.tanggal, t_order.total from t_order INNER JOIN t_menu INNER JOIN t_order_detail WHERE t_menu.id_menu=t_order_detail.id_menu AND t_order.id_transaksi=t_order_detail.id_transaksi GROUP BY t_order.id_transaksi")->result();
        foreach ($t_order_detail as $row){
            $pdf->Cell(20,6,$row->id_transaksi,1,0);
            $pdf->Cell(40,6,$row->status,1,0);
            $pdf->Cell(27,6,$row->kode_tiket,1,0);
            $pdf->Cell(22,6,$row->tanggal,1,0);
            $pdf->Cell(24,6,$row->total,1,1);
            // $pdf->Cell(20,6,$row->harga,1,1); 
        }
        $pdf->Output();
  }

}
