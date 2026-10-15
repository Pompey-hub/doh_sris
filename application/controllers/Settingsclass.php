<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settingsclass extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		if(!$this->session->userdata('loggedin')) {
			redirect('');
		}

		$this->load->model(['Commonclass_model', 'Settingsclass_model']);
	}

	public function index()
	{
		$res = $this->Settingsclass_model->sraccountdetails();
		$this->load->view('settings/changesettings', ['res'=>$res]);
	}


	public function srchangepasssave()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('newpassword', 'New password', 'required|min_length[6]|max_length[100]|matches[confirmpassword]');
		$this->form_validation->set_rules('confirmpassword', 'Confirm password', 'required|min_length[6]|max_length[100]');


		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
		} else {
			$newpassword = $this->input->post('newpassword');
			$res = $this->Settingsclass_model->srchangepasssave($newpassword);
			if ($res) {
				$this->session->set_flashdata('success', 'Successfully changed!');
			} else {
				$this->session->set_flashdata('error', 'Oops! There\'s something wrong, Please try again!');
			}
		}

		redirect('settings');
	}


	public function srchangecontactsave()
	{
		$email = $this->input->post('sremail');
		$number = $this->input->post('srnumber');

		$this->load->library('form_validation');
		$this->form_validation->set_rules('sremail', 'Email Address', 'required|min_length[6]|max_length[100]|valid_email');

		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', 'The Email Address field must contain a valid email address.');
		} else {
			$isemailexist = $this->Settingsclass_model->isemailexist($email);
			if ($isemailexist == true) {
				$this->session->set_flashdata('error', 'Email is already in use!');
				redirect('settings');
			} 

			$res = $this->Settingsclass_model->srchangecontactsave($email, $number);
			if ($res) {
				$this->session->set_userdata('email', $email);
				$this->session->set_flashdata('success', 'Contacts has been successfully updated.');
			} else {
				$this->session->set_flashdata('error', 'Oops! There\'s something wrong, Please try again!');
			}
		}
		redirect('settings');
	}



/*delete below*/
	
	public function saveuser()
	{
		$res = $this->Userclass_model->saveuser();
		echo json_encode(['res'=>$res]);
	}

	public function savepassword()
	{
		$m_lid_pword = $this->input->post('m_lid_pword');
		$m_pword_change = $this->input->post('m_pword_change');
		 $res = $this->Userclass_model->savepassword($m_lid_pword, $m_pword_change);
		 echo json_encode((['res'=>$res]));
	}


	public function emplist()
	{
		$res = $this->Commonclass_model->employee();
		echo json_encode($res);
	}

}
