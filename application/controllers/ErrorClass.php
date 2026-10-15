<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ErrorClass extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		if(!$this->session->userdata('loggedin')) {
			redirect('');
		}
	}

	public function error404()
	{
		$this->output->set_status_header('404'); 
    	$this->load->view('errors/error404');
	}
}
