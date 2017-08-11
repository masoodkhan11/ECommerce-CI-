<?php 
defined('BASEPATH') OR exit('No direct script access allowed');	

class Products extends CI_Controller {
	function __construct() 
	{
		parent::__construct();

		$this->load->helper('url');
	    $this->load->model('ProductModel');
	}

	function show($id)
	{
		$data['product'] = $this->ProductModel->get($id);
		$data['title'] = 'product : ' . $data['product']->name;

		$this->load->view('product', $data);
	}
}


 ?>