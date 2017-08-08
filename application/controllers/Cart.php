<?php 
defined('BASEPATH') OR exit('No direct script access allowed');	
/**
* 
*/
class Cart extends CI_Controller {
	
	function __construct() {
		parent::__construct();

		$this->load->helper('url');

	    $this->load->library('session');
	    $this->load->model('ProductModel');	
	}

	function index() {
		
	}

	function add()
	{
		$product_id	= $this->input->get('product_id'); 
		$quantity	= $this->input->get('quantity');

		$cart = $this->session->userdata('cart') ? $this->session->userdata('cart') : array();

		$cart_data  = array( 
			'id'		=> $product_id,
			'quantity'	=> $quantity
		);
		$cart[] = $cart_data; 

		$this->session->set_userdata('cart', $cart );

		redirect('cart/show');
	}

	function show()
	{
		$cart = $this->session->userdata('cart') ? $this->session->userdata('cart') : array();

		$data['cart'] = $this->ProductModel->getCartItems($cart);

		$grandtotal = 0 ;
		foreach ($data['cart'] as $value) {
			$total = $value['quantity'] * $value['detail']->price ;	
			$grandtotal += $total ;
		}
		$this->session->set_userdata('total', $grandtotal);

		$this->load->view('cart', $data);
	}

}

 ?>