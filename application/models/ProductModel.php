<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class ProductModel extends CI_Model 
{	
	function __construct() {		
		$this->load->database();
	}

	function get_all() 
	{
		return $this->db->get("product")->result();
	}

	function get($id) 
	{
		return $this->db->where('id', $id)->get("product")->row();
	}

	function get_cart_items($cart) 
	{
		foreach ($cart as $key => $product) {
			$detail = $this->db->where('id', $product['id'])->get('product')->row();
			$cart[$key]['detail'] = $detail;
		}
		return $cart;
	}
}
