<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
/**
* 
*/
class Checkout extends CI_Controller {
	
	function __construct() {
		parent::__construct();

		$this->load->library('session');
		$this->load->model('OrderModel');
	}

	function index() {
		$this->load->view('checkout');
	}

	function submit() {

		$userdata = array(
			'name'			=> $this->input->get('name'),
			'phone' 		=> $this->input->get('mobile'),
			'address' 		=> $this->input->get('address'),
			'order_date' 	=> date('Y/m/d h:i:s', time()),
			'status'		=> 'Processing..'
		);

		$order_id = $this->OrderModel->insert_user($userdata);

		if ($order_id != '') {

			$orderdata = array();
			foreach ($this->session->cart as $value) {
				$elem['order_id'] 	= $order_id ;
				$elem['product_id'] = $value['id'];
				$elem['quantity']	= $value['quantity'];
				$orderdata[] = $elem ;
			}

			$result = $this->OrderModel->insert_order($orderdata);

			if ($result == TRUE) {
				redirect('order/show/' .$order_id);
			} else {
				redirect('checkout');
			}
		}
	}
}

?>