<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class M_Menu extends CI_Model {

  public function get_menu_all(){ 
    $query = $this->db->query("Select * from t_menu INNER JOIN t_kategori WHERE t_menu.kategori=t_kategori.id");
    return $query->result_array();
  }

  public function get_kategori(){ 
    $query = $this->db->query("Select * from t_kategori");
    return $query;
  }

 public function get_menu_id($id_menu){ 
    $query = $this->db->query("Select * from t_menu INNER JOIN t_kategori WHERE t_menu.kategori=t_kategori.id AND t_menu.id_menu='".$id_menu."'");
    return $query->result_array();
  }
   public function insert_menu($data) {
        $username= $this->session->userdata("nama"); 
         if ($this->db->insert("t_menu", $data)) { 
            return true; 
         } 
  } 
    public function update($data,$old_id) { 
           $this->db->set($data); 
           $this->db->where("id_menu", $old_id); 
           $this->db->update("t_menu", $data); 
    } 
    
    public function delete($id_menu) { 
           if ($this->db->delete("t_menu", "id_menu = ".$id_menu)) { 
              return true; 
           } 
        } 

  

}
?>