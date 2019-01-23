<?php 
     class M_Message extends CI_Model {
        function __construct() { 
           parent::__construct(); 
        } 
        public function tambah_message($data){
          $this->db->insert('t_message', $data);
          return;
        }
 
         public function update($data,$old_id) { 
         	$this->db->set($data); 
         	$this->db->where("kd_food", $old_id); 
         	$this->db->update("t_food", $data); 
      }      
        public function delete($where,$table){
         $this->db->where($where);
         $this->db->delete($table);
       }

    public function get_inbox_all($tgl_skrng){
    $query = $this->db->query("Select * from t_message INNER JOIN t_order INNER JOIN t_penumpang WHERE t_message.id_pengirim=t_penumpang.kode_tiket AND t_message.id_transaksi=t_order.id_transaksi AND t_message.tanggal='".$tgl_skrng."'");
    return $query->result_array();
    }

    public function inbox_from_petugas($tgl_skrng){
    $query = $this->db->query("Select * from t_message INNER JOIN t_order INNER JOIN t_penumpang INNER JOIN t_petugas WHERE t_message.id_pengirim=t_penumpang.kode_tiket AND t_message.id_transaksi=t_order.id_transaksi AND t_message.id_pengirim=t_petugas.email AND t_message.tanggal='".$tgl_skrng."'");
    return $query->result_array();
    }

    public function inbox_from_penumpang($tgl_skrng){
    $query = $this->db->query("Select * from t_message INNER JOIN t_order INNER JOIN t_penumpang WHERE t_message.kode_tiket=t_penumpang.kode_tiket AND t_message.id_transaksi=t_order.id_transaksi AND t_message.tanggal='".$tgl_skrng."'");
    return $query->result_array();
    }

    public function get_inbox_reply($tgl_skrng,$kode_tiket){
    $query = $this->db->query("Select * from t_message INNER JOIN t_order INNER JOIN t_penumpang WHERE t_message.id_transaksi=t_order.id_transaksi AND t_message.id_transaksi=t_order.id_transaksi AND t_message.tanggal='".$tgl_skrng."'  AND t_message.kode_tiket='".$kode_tiket."' GROUP BY t_message.kode_tiket");
    return $query->result_array();
    }

}