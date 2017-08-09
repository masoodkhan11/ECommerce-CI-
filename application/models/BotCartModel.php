<?php  
/**
* 
*/
class BotCartModel extends CI_Model {
	
	function __construct() {
		parent::__construct();
		
		$this->load->database();
	}

	function get_cart($sender_id) {

		return $this->db->where('sender_id', $sender_id)->get('cart')->result();
	}

	function insert_cart($data) {

		$this->db->insert('cart', $data);
		return ($this->db->affected_rows() >= 1) ? TRUE : FALSE ;
	}

	function get_cartproduct($id) {

		return $this->db->where('id', $id)->get('cart')->row();
	}

	function remove_product($id) {

		$this->db->where('id', $id)->delete('cart');
		return ($this->db->affected_rows() >= 1) ? TRUE : FALSE ;
	}

	function delete_product($product_id) {

		$this->db->where('product_id', $product_id)->delete('cart');
	}

}

?>