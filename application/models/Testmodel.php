<?php 


class Testmodel extends CI_Model
{
	function Testmodel()
	{
		$this->load->database();
	}
	
	function getdata()
	{
		$query = $this->db->get("auth");
		return $query->result();
		
	}

}
