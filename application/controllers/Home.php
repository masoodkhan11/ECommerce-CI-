<?php 
defined('BASEPATH') OR exit('No direct script access allowed');	

/**
* 
*/
class Home extends CI_Controller {

	function __construct() 
	{
		parent::__construct();

	    $this->load->model('ProductModel');
	}

	function index() 
	{
		$data['products'] = $this->ProductModel->get_all();
	    
	    // $this->load->view('header');
		$this->load->view('home', $data);
	}
}

 ?>