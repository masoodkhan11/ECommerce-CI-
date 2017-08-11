<?php  
defined('BASEPATH') OR exit('No direct script access allowed');

class OrderModel extends CI_Model 
{	
	function __construct() 
	{
		$this->load->database();
		$this->load->helper('url');
	}

	function insert_user($data) 
	{
		$this->db->insert('user_order', $data);
		return $this->db->insert_id();
	}

	function insert_order($data) 
	{
		foreach ($data as $value) {
			$this->db->insert('order_details', $value);
		}
		return TRUE;
	}	

	function order_detail($order_id) 
	{
		return $this->db->where('id', $order_id)->get('user_order')->row();
	}
}

?>