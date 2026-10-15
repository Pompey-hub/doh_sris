<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Userclass extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		if(!$this->session->userdata('loggedin')) {
			redirect('');
		}

		$this->load->model(['Commonclass_model', 'Userclass_model']);
	}

	public function index()
	{
		$this->load->view('settings/userpage');
	}

	public function dtuser()
	{
		$col = [0=>'CONCAT(b.fname, " ", LEFT(b.mname, 1), ". ", b.lname, " ", b.suffix )', 1=>'a.tlluname', 2=>'a.tllfactype', 3=>'CONCAT(e.divabbrev, "-",f.secabbrev)', 4=>'a.tllpriv', 5=>'a.tllstatus', 6=>'a.tlldateadded'];
		$order = $col[$_GET['order']['0']['column']];
        $dir = $_GET['order']['0']['dir'];
		$prm = [ '%'.$_GET['search']['value'].'%',  '%'.$_GET['search']['value'].'%', '%'.$_GET['search']['value'].'%',  '%'.$_GET['search']['value'].'%', 
				 '%'.$_GET['search']['value'].'%',  '%'.$_GET['search']['value'].'%',  '%'.$_GET['search']['value'].'%' ];

		$sql_tr = $this->Userclass_model->sqldtusers();



		$start = $_GET['start'];
		$length = $_GET['length'];
		$data_obj = $this->Commonclass_model->datatablefunc($col, $order, $dir, $prm, $sql_tr, $start, $length);
		$totaldata = $data_obj['totalrecord'];
		$data = $data_obj['limit'];	


		

		$len = count($data);
		for ($i=0; $i < $len; $i++) { 
			$data[$i]['tllid'] = $this->cryptography->encrypt($data[$i]['tllid']);
			$data[$i]['tlldateadded'] = $this->Commonclass_model->displaychangeDateFormat($data[$i]['tlldateadded']);
		}

		$json_data = array(
	        "draw"            => intval($_REQUEST['draw']),
	        "recordsTotal"    => intval($totaldata),
	        "recordsFiltered" => intval($totaldata),
	        "data"            => $data
	    );

		echo json_encode($json_data);
	}


	public function credentials()
	{
		$this->load->library('form_validation');

		#$this->form_validation->set_rules('srtllnumber', 'Mobile Number', 'required|regex_match[/^09[0-9]{9}$/]'); 
		#$this->form_validation->set_rules('srtllemail', 'Email', 'required');

		$this->form_validation->set_rules('srtllfactype', 'Facility type', 'required');
		$this->form_validation->set_rules('srtllpriv', 'Privilege', 'required');
		$this->form_validation->set_rules('srtlluname', 'Username', 'required');
		$this->form_validation->set_rules('srtllsecid', 'Office', 'required');
		$this->form_validation->set_rules('srtllstatus', 'Status', 'required');

		if ($this->form_validation->run() == FALSE) {
			$emplist = $this->Commonclass_model->employee();
			$offlist = $this->Commonclass_model->office();
			$poslist = $this->Commonclass_model->position();

			$res = $this->Userclass_model->srsqlcredentials();
			/*var_dump($res);
			die();*/
			
            $this->load->view('settings/usercredentialpage', ['res'=>$res, 'emplist'=>$emplist, 'offlist'=>$offlist, 'poslist'=>$poslist]);
    	} else {
    		$this->Userclass_model->srcredentials();
    		$this->session->set_flashdata('success', 'Successfully  saved!');
            redirect('users');
        }
	}


	public function edituser()
	{
		$lid = $this->cryptography->decrypt(trim($this->input->get('lid')));
		$res = $this->Userclass_model->edituser($lid);
		$emplist = $this->Commonclass_model->employee();
		echo json_encode(['res'=>$res, 'emplist'=>$emplist]);
	}

	/*
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
	}*/

}
