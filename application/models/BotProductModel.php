<?php  
/**
* 
*/
class BotProductModel extends CI_Model {	
	
	function __construct() {
		parent::__construct();
		
		$this->load->database();
	}

	function get_brand($brand) {

		return $this->db->where('brand', $brand)->get('product')->result();
	}

	function get_cart($sender_id) {

		return $this->db->where('sender_id', $sender_id)->get('cart')->result();
	}

}

?>