<?php  

class BotProductModel extends CI_Model 
{
	function __construct() 
	{
		parent::__construct();
		
		$this->load->database();
	}

	function get_brand($brand) 
	{
		return $this->db->where('brand', $brand)->get('product')->result();
	}

	function get_product($id) 
	{
		return $this->db->where('id', $id)->get('product')->row();
	}
}

?>