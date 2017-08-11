<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

	public function index()
	{
		$this->load->model('Testmodel');

		$data['auth'] = $this->Testmodel->getdata();
		
		$this->load->view('Testview', $data);
		
	}
}

?>
