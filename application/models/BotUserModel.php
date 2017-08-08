<?php  
/**
* 
*/
class BotUserModel extends CI_Model {
		
	function __construct()
	{
		$this->load->database();
	}

	function get_user($sender_id) {

		return $this->db->where('sender_id', $sender_id)->get('bot_user')->row();
	}

	function insert_user($insert_data) {

		$this->db->insert('bot_user', $insert_data);
		return ($this->db->affected_rows() >= 1) ? TRUE : FALSE ;
	}

}

?>