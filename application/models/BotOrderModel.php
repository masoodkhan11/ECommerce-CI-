<?php  
/**
* 
*/
class BotOrderModel extends CI_Model {
	
	function __construct() {
		parent::__construct();
		
		$this->load->database();
	}

	function insert_order($data) {

		$this->db->insert('bot_order', $data);
		return $this->db->insert_id();	
	}

	function insert_orderDetails($data) {

		$this->db->insert('bot_order_details', $data);
	}

	function update_order($user_id, $email) {
		$condition = array(
			'user_id' 		=> $user_id , 
			'order_detail' 	=> '0'
		);
		$this->db->where($condition)->update('bot_order', array('email' => $email , 'order_detail' => '1'));
	}

}

?>