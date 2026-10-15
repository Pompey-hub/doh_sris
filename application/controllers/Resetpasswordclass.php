<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Resetpasswordclass extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		if($this->session->userdata('loggedin')) {
			redirect('');
		}

		$this->load->model(['Resetpasswordclass_model', 'Commonclass_model']);
	}

	public function resetpassword()
	{
		$this->load->view('resetpassword');
	}

	public function resetpasswordsend()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('email', 'Email Address', 'required|min_length[6]|max_length[100]|valid_email');
		if ($this->form_validation->run() == FALSE) {
			redirect('resetpassword');
		} else {
			$email = trim($this->input->post('email'));
			$emailexist = $this->Resetpasswordclass_model->emailExist($email);

			/*exist*/
			if (count($emailexist) * 1 == 1) {
				$this->Resetpasswordclass_model->addtllchangepassexp($email);
				$this->sendresetpasswordemail($email, $emailexist[0]['tlluname']);
			} else {
				$this->session->set_flashdata('error', $email . ' doesn\'t exist!');
				redirect('resetpassword');
			}
		}
	}

	private function sendresetpasswordemail($email, $tlluname)
	{
		$adminemail = 'dohevchdictu@gmail.com';
		$adminpass = 'Zaq12wsx#@!!@#';

		$email_code = md5($this->config->item('salt') . $tlluname);

		$config = array(
		    'protocol' => 'smtp',
		    'smtp_host' => 'ssl://smtp.googlemail.com',
		    'smtp_port' => 465,
		    'smtp_user' => $adminemail, 
			'smtp_pass' => $adminpass, 
		    'mailtype'  => 'html', 
		    'charset'   => 'iso-8859-1'
		);
		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");

		$this->email->initialize($config);
		$this->email->from($adminemail, 'DOH-EVCHD');
		$this->email->to($email);
		$this->email->subject('Please reset your password of DOH-EVCHD SRIS');
		$message = '<!DOCTYPE html>
					<html lang="en">
					<head></head>
					<body>
						<div>Hi ' . strtoupper($tlluname) . ',</div>
						<div>Please <strong><a href="' . base_url() . 'resetpassword/newform/' . $email . '/' . $email_code . '">click here</a></strong> to change your password! You only have <strong>1 hour</strong> to change it.</div>
						<br>
						<div>If you didn\'t request a password reset, please delete this email!</div>
						<br>
						<div>Best regards,</div>
						<div>DOH-EVCHD ICTU</div>
						<br>
						<br>
						<div style="color:red;"><i><strong>Note: Do not reply this message.</strong></i></div>
					</body>';
		$this->email->message($message);

		if($this->email->send()) {
			$this->load->view('resetpasswordsend', ['email'=>$email]); 
		} else {
			print_r($this->email->print_debugger());  
		}
	}


	/* after clicking the link in gmail account */
	public function resetpasswordnewform($email, $email_code)
	{
		if (isset($email, $email_code)) {
			$email = trim($email);
			$email_hash = sha1($email . $email_code);
			$verified = $this->Resetpasswordclass_model->verifyresetpasswordcode($email, $email_code);

			if ($verified) {
				$this->load->view('updatepassword', ['email'=>$email, 'email_hash'=>$email_hash, 'email_code'=>$email_code]);
			} else {
				$this->session->set_flashdata('error', 'Oops! There\'s something wrong with your link!. Please click it again or request to reset your password again.');
				redirect('resetpassword');
			}
		} else {
			$this->session->set_flashdata('error', 'Oops! There\'s something wrong with your link!. Please click it again or request to reset your password again.');
			redirect('resetpassword');
		}
	}


	/*update password*/
	public function updatepassword()
	{

		$email = $this->input->post('email');
		$email_hash = $this->input->post('email_hash');
		$email_code = $this->input->post('email_code');
		$newpassword = $this->input->post('newpassword');
		$confirmpassword = $this->input->post('confirmpassword');

		if (!isset($email, $email_hash) || $email_hash !== sha1($email.$email_code) ) {
			die('Error updating your password!');
		}

		$this->load->library('form_validation');

		$this->form_validation->set_rules('email', 'Email Address', 'required|min_length[6]|max_length[100]|valid_email');
		$this->form_validation->set_rules('newpassword', 'New password', 'required|min_length[6]|max_length[100]|matches[confirmpassword]');
		$this->form_validation->set_rules('confirmpassword', 'Confirm password', 'required|min_length[6]|max_length[100]');


		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			redirect('resetpassword/newform/' . $email . '/' . $email_code);
		} else {
			$checkexp = $this->Resetpasswordclass_model->checkupdatepasswordexptime($email);
			if ($checkexp == false) {
				$this->session->set_flashdata('error', 'Your 1 hour session was ended. Please request to reset your password again.');
				redirect('resetpassword');
			} 

			$res = $this->Resetpasswordclass_model->updatepassword($email, $newpassword);
			if ($res) {
				$this->session->set_flashdata('success', 'Password has been successfully updated.');
				redirect('');
			} else {
				$this->session->set_flashdata('error', 'Oops! There\'s something wrong!. Please click it again or request to reset your password again.');
				redirect('resetpassword');
			}
		}
	}

}
