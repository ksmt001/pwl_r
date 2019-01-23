<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_Order extends CI_Model {
  //td diganti ada as
  
  public function get_order_all($tgl_skrng){ 
    $query = $this->db->query("select t_order.id_transaksi, t_order.status, t_order.kode_tiket, t_order.tanggal, t_order.total from t_order INNER JOIN t_menu INNER JOIN t_order_detail WHERE t_menu.id_menu=t_order_detail.id_menu AND t_order.id_transaksi=t_order_detail.id_transaksi AND t_order.status='Belum dilayani' AND t_order.tanggal='".$tgl_skrng."' GROUP BY t_order.id_transaksi");
    return $query->result_array();

  }

  public function get_order_all_status($tgl_skrng){
    $query = $this->db->query("select o.id_transaksi, o.status, o.kode_tiket, o.tanggal, o.total from t_order o INNER JOIN t_menu m INNER JOIN t_order_detail od WHERE m.id_menu=od.id_menu AND o.id_transaksi=od.id_transaksi AND o.status='Sedang dilayani' AND o.tanggal='".$tgl_skrng."' GROUP BY o.id_transaksi");
    return $query->result_array();
  }

  public function get_order_all_cancel($tgl_skrng){
    $query = $this->db->query("select o.id_transaksi, o.status, o.kode_tiket, o.tanggal, o.total from t_order o INNER JOIN t_menu m INNER JOIN t_order_detail od WHERE m.id_menu=od.id_menu AND o.id_transaksi=od.id_transaksi AND o.status='Dibatalkan' AND o.tanggal='".$tgl_skrng."' GROUP BY o.id_transaksi");
    return $query->result_array();
  }
  public function get_order_all_done($tgl_skrng){
      $query = $this->db->query("select o.id_transaksi, o.status, o.kode_tiket, o.tanggal, o.total from t_order o INNER JOIN t_menu m INNER JOIN t_order_detail od WHERE m.id_menu=od.id_menu AND o.id_transaksi=od.id_transaksi AND o.status='Selesai' AND o.tanggal='".$tgl_skrng."' GROUP BY o.id_transaksi");
      return $query->result_array();

  }

  public  function get_order_detail($id_transaksi){
    $query = $this->db->query("select t_order.id_transaksi, t_menu.id_menu, t_menu.nama_menu, t_order_detail.jumlah, t_menu.harga from t_menu JOIN t_order JOIN t_order_detail WHERE t_order.id_transaksi=t_order_detail.id_transaksi AND t_menu.id_menu=t_order_detail.id_menu AND t_order.id_transaksi='".$id_transaksi."'");
    return $query->result_array();
  } 

    public  function get_print_out($id_transaksi, $kode_tiket){
    $query = $this->db->query("select * from t_order INNER JOIN t_menu INNER JOIN t_penumpang INNER JOIN t_order_detail WHERE t_order.id_transaksi=t_order_detail.id_transaksi AND t_order.kode_tiket=t_penumpang.kode_tiket AND t_menu.id_menu=t_order_detail.id_menu AND t_order.id_transaksi='".$id_transaksi."' AND t_penumpang.kode_tiket='".$kode_tiket."' GROUP BY t_order.id_transaksi");

    return $query->result_array();
  } 

  public function update_pesanan($data,$id_transaksi){
      $this->db->set($data); 
      $this->db->where("id_transaksi", $id_transaksi); 
      $this->db->update("t_order", $data);       
  }
  
  public function tambah_order($data)
  {
    $this->db->insert('t_order', $data);
    $id = $this->db->insert_id();
    return (isset($id)) ? $id : FALSE;
  }
  
  public function tambah_detail_order($data)
  {
    $this->db->insert('t_order_detail', $data);
  }

    public function batal_pesanan_order($data,$id_transaksi){
      $this->db->set($data); 
      $this->db->where("id_transaksi", $id_transaksi); 
      $this->db->update("t_order", $data);       
  }

   public function lapor_order_all(){ 
    $query = $this->db->query("select t_order.id_transaksi, t_order.status, t_order.kode_tiket, t_order.tanggal, t_order.total from t_order INNER JOIN t_menu INNER JOIN t_order_detail WHERE t_menu.id_menu=t_order_detail.id_menu AND t_order.id_transaksi=t_order_detail.id_transaksi GROUP BY t_order.id_transaksi");
    return $query->result_array();

  }
}
?>