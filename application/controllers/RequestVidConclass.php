<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RequestVidConclass extends CI_Controller {
	public $priv_ = 'Client';

	function __construct()
	{
		parent::__construct();

		if(!$this->session->userdata('loggedin')) {
			redirect('');
		}

		$this->load->model(['RequestVidConclass_model', 'Commonclass_model', 'Requestclass_model']);

		if ($this->session->userdata('privilege') == '99') {
        	$this->priv_ = 'Superadmin';
	    } elseif (in_array('SRIS', $this->session->userdata('tlarsys')) ) {
	        $exp_sys = $this->session->userdata('tlarsys'); #explode('&shen&', $this->session->userdata('tlarsys'));
            $index = array_search('SRIS', $exp_sys);

            $exp_priv = $this->session->userdata('tlarpriv'); #explode('&shen&', $this->session->userdata('tlarpriv'));
            $priv_ = $exp_priv[$index];
	    } else {
	        $this->priv_ = 'Client';
	    }
	}

	public function servicerequestvidcon()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('tsrclient', 'Name of client', 'trim|required'); 
		$this->form_validation->set_rules('tsrmobilenum', 'Mobile #', 'trim|required'); 
		$this->form_validation->set_rules('tsremail', 'Email address', 'trim|valid_email|required'); 
		$this->form_validation->set_rules('tvcdplatform', 'Platform', 'trim|required'); 
		$this->form_validation->set_rules('tvcdtitle', 'Topeic/ Title', 'trim|required'); 
		$this->form_validation->set_rules('tvcddate', 'Date', 'trim|required'); 
		$this->form_validation->set_rules('tvcdtimestart', 'Time start', 'trim|required'); 
		$this->form_validation->set_rules('tvcdtimeend', 'Time end', 'trim|required'); 
		$this->form_validation->set_rules('tvcdusertype', 'User type', 'trim|required'); 
		$this->form_validation->set_rules('trrid', 'Record Request', 'trim|required'); 

		if ($this->form_validation->run() == FALSE) {
			$res = $this->RequestVidConclass_model->selservicerequest();
			$emp = $this->Commonclass_model->employeepersection();
			$record = $this->Commonclass_model->record();
			$part = [];
			if (count($res) > 0) {
				$part = $this->RequestVidConclass_model->selvidconparticipant($res[0]['tvcdid']);
			}
            $this->load->view('home/servicerequestvidcon', ['res'=>$res, 'emp'=>$emp, 'part'=>$part, 'record'=>$record]);
    	} else {
    		$this->RequestVidConclass_model->srsave();
    		$this->session->set_flashdata('success', 'Successfully  saved!');
    		redirect('?q=myrequest');


    		// $res = $this->Requestclass_model->srcheckconforme();							/*11162020*/
			// $cnt = isset($res[0]['cnt']) ? (count($res) * 1) : 0;							/*11162020*/
			// if ($cnt < 1) {																	/*11162020*/
			// 	$this->RequestVidConclass_model->srsave();
    		// 	$this->session->set_flashdata('success', 'Successfully  saved!');
			// } else {																		/*11162020*/
			// 	$this->session->set_flashdata('error', 'You have 1 pending request!');		/*11162020*/
			// }																				/*11162020*/
            // redirect('?q=myrequest');
        }
	}


	public function servicerequestvidconview()
	{
		$res = $this->RequestVidConclass_model->selservicerequest();
		$emp = $this->Commonclass_model->employee();
		$record = $this->Commonclass_model->record();	/*02022021*/

		$part = [];
		if (count($res) > 0) {
			$part = $this->RequestVidConclass_model->selvidconparticipant($res[0]['tvcdid']);
		}
        $this->load->view('home/servicerequestvidcon', ['res'=>$res, 'emp'=>$emp, 'part'=>$part, 'record'=>$record, 'viewonly'=>true]);
	}


}
