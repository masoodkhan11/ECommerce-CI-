<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
/**
* 
*/
class Order extends CI_Controller {
	
	function __construct() {
		parent::__construct();

		$this->load->model('OrderModel');
		$this->load->library('session');
	}

	function show($order_id) 
	{
		$data['order'] 	= $this->OrderModel->order_detail($order_id);
		$data['total'] 	= $this->session->total ; 
		
		$this->session->sess_destroy();
		
		$this->load->view('order', $data);
	}
}

?>