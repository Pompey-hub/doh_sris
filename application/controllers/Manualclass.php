<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manualclass extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		if(!$this->session->userdata('loggedin')) {
			redirect('');
		}
	}


	public function vidcontutorial()
	{
		$this->load->view('manual/vidcontutorial');
	}

	public function googleformtemplate()
	{
		$this->load->view('manual/googleformtemplate');
	}
}
