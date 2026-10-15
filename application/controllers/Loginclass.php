<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Loginclass extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->model(['Loginclass_model', 'Commonclass_model']);
	}

	public function index()
	{
		if ($this->session->userdata('loggedin') === true) {
			// $res = $this->Loginclass_model->cntAccomplishedRequest();
			// $cnt = isset($res[0]['cnt']) ? $res[0]['cnt'] : 0;
			$cnt = 0;
			$serv = $this->Commonclass_model->services();
			$a_office = $this->Commonclass_model->assistingoffice($this->session->userdata('secabbrev'));

			$ass_office_exist = false;
			if (count($a_office) > 0) {
				$ass_office_exist = true;
			}
			$this->load->view('home/index', ['cnt' => $cnt, 'serv' => $serv, 'ass_office_exist' => $ass_office_exist]);
		} else {
			$domains = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'];
			header('Location: ' . $domains . '/home/');
		}
	}

	public function srview()
	{
		if ($this->session->userdata('loggedin') === true) {
			$this->load->view('home/srview');
		} else {
			/*#$this->load->view('auth');*/
			$domains = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'];
			header('Location: ' . $domains . '/home/');
		}
	}


	public function validaterequest()
	{
		$res = $this->Loginclass_model->validaterequest();
		if ($res === false) {
			$this->session->set_flashdata('error', 'Error: Invalid Username or Password!');
		}
		redirect('');
	}


	public function sractiontakensave()
	{
		$this->load->library('form_validation');

		// =========================================================
		// GET REQUEST ID
		// =========================================================

		$tsrid = $this->cryptography->decrypt(
			$this->input->post('xtsrid')
		);


		// =========================================================
		// GET ACTION
		// =========================================================

		$tlsrsid = $this->cryptography->decrypt(
			$this->input->post('tlsrsid')
		);


		// =========================================================
		// ACTION VALIDATION
		// =========================================================

		$this->form_validation->set_rules(
			'tlsrsid',
			'Please select action',
			'trim|required'
		);


		// =========================================================
		// CHECK EXISTING ACTIVE ASSIGNMENT
		//
		// STEP 1:
		// Received / Returned
		// No active assignment yet.
		// ICTU Employee must be selected.
		//
		// STEP 2:
		// For Action
		// Active assignment already exists.
		// ICTU Employee is hidden.
		//
		// STEP 3:
		// Accomplished / Cancelled
		// Active assignment already exists.
		// ICTU Employee is hidden.
		// =========================================================

		$has_assignment = false;

		if (!empty($tsrid)) {

			$assignment = $this->db
				->select('assignment_id, assigned_to')
				->from('tbl_service_request_assignment')
				->where('tsrid', $tsrid)
				->where('status', 'Active')
				->limit(1)
				->get()
				->row_array();

			if (!empty($assignment)) {
				$has_assignment = true;
			}
		}


		// =========================================================
		// ICTU EMPLOYEE VALIDATION
		//
		// Received / Reassigned:
		// ICTU Employee MUST be selected.
		//
		// For Action / Accomplished / Cancelled:
		// Existing assignment is retained.
		// =========================================================

		if (
			$tlsrsid === '1' ||
			$tlsrsid === '9' ||
			$tlsrsid === '10'
		) {

			$this->form_validation->set_rules(
				'assigned_to',
				'Please select ICTU Employee',
				'trim|required'
			);
		}


		// =========================================================
		// ACCOMPLISHED VALIDATION
		//
		// tlsrsid === '3' = Accomplished
		// =========================================================

		if ($tlsrsid === '3') {

			$xtlsid = trim(
				$this->input->post('xtlsid')
			);


			// =====================================================
			// SERVICE TYPE 7
			// =====================================================

			if ($xtlsid == '7') {

				$this->form_validation->set_rules(
					'takapprove',
					'Action',
					'trim|required'
				);

				$takapprove = $this->input->post('takapprove');


				// -------------------------------------------------
				// APPROVE
				// -------------------------------------------------

				if ($takapprove === 'APPROVE') {

					$this->form_validation->set_rules(
						'takmacaddress',
						'Invalid MAC Address',
						'trim|required|callback_isvalidmacaddress'
					);

					$this->form_validation->set_rules(
						'takhostname',
						'Host name',
						'trim|required'
					);
				}


				// =====================================================
				// SERVICE TYPE 1 OR 2
				// =====================================================

			} elseif (
				$xtlsid === '1' ||
				$xtlsid === '2'
			) {

				$this->form_validation->set_rules(
					'takhostname',
					'Host name',
					'trim|required'
				);
			}


			// =====================================================
			// COMMON ACCOMPLISHED FIELDS
			// =====================================================

			$this->form_validation->set_rules(
				'takclientempid',
				'Name of client',
				'trim|required'
			);

			$this->form_validation->set_rules(
				'xtlsid',
				'Type of service',
				'trim|required'
			);

			$this->form_validation->set_rules(
				'tlscid',
				'Category Request',
				'trim|required'
			);

			$this->form_validation->set_rules(
				'takdesc',
				'Description',
				'trim|required'
			);
		}


		// =========================================================
		// RUN VALIDATION
		// =========================================================

		if ($this->form_validation->run() == FALSE) {

			$this->session->set_flashdata(
				'error',
				'Ooops! There\'s something wrong!'
			);
		} else {

			// =====================================================
			// SAVE ACTION TAKEN
			// =====================================================

			$res = $this->Loginclass_model->sractiontakensave();


			if ($res === false) {

				$this->session->set_flashdata(
					'error',
					'Ooops! There\'s something wrong!'
				);
			} else {

				$this->session->set_flashdata(
					'success',
					'Successfully saved!'
				);
			}
		}


		// =========================================================
		// REDIRECT
		// =========================================================

		$redirect_q = $this->input->post('redirect_q');

		if ($redirect_q === 'assigned') {
			redirect('?q=assigned');
		} else {
			redirect('');
		}
	}

	public function isvalidmacaddress($takmacaddress)
	{
		$isValidtakmacaddress = filter_var($takmacaddress, FILTER_VALIDATE_MAC);
		if ($isValidtakmacaddress == false) {
			return false;
		}
		return true;
	}


	public function logout()
	{
		$domainname = $this->session->userdata('domainname');
		clearstatcache();
		$this->load->helper('dynamic_helper');
		my_log(' LOGOUT / ' . $this->session->userdata('lid') . ' / ' . $this->session->userdata('uname'), 'user');
		$this->session->sess_destroy();
		redirect($domainname);
	}
}
