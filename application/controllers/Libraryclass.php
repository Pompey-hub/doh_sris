<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Libraryclass extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		
		if(!$this->session->userdata('loggedin') || $this->session->userdata('privilege') !== '99') {
			redirect('');
		}

		$this->load->model(['Libraryclass_model', 'Commonclass_model']);
	}


	public function category()
	{
		$this->load->view('library/category');
	}

	public function dtcategory()
	{
		$col = [0=>'a.tlccat', 1=>'a.tlcstatus', 2=>'a.created_at', 3=>'a.updated_at'];
		$order = $col[$_GET['order']['0']['column']];
        $dir = $_GET['order']['0']['dir'];
		$prm = [ '%'.$_GET['search']['value'].'%' ];

		$sql_tr = $this->Libraryclass_model->sqldtcategory();

		$start = $_GET['start'];
		$length = $_GET['length'];
		$data_obj = $this->Commonclass_model->datatablefunc($col, $order, $dir, $prm, $sql_tr, $start, $length);

		$totaldata = $data_obj['totalrecord'];
		$data = $data_obj['limit'];	

		$json_data = array(
	        "draw"            => intval($_REQUEST['draw']),
	        "recordsTotal"    => intval($totaldata),
	        "recordsFiltered" => intval($totaldata),
	        "data"            => $data
	    );

		echo json_encode($json_data);
	}

	public function savecategory()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('tlccat', 'Category', 'trim|required'); 
		$this->form_validation->set_rules('tlcstatus', 'Status', 'trim|required'); 
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', 'Oops!! Something went wrong!');
    	} else {
    		$this->Libraryclass_model->savecategory();
    		$this->session->set_flashdata('success', 'Successfully  saved!');
        }
        redirect('library/category');
	}

	public function editcategory()
	{
		$category = $this->Libraryclass_model->editcategory();
		echo json_encode(compact('category'));
	}

	public function getcategory()
	{
		$category = $this->Commonclass_model->category();
		$service = $this->Commonclass_model->services();
		$levels = $this->Commonclass_model->levels();
		echo json_encode(compact('category', 'service', 'levels'));
	}

	/*start subcategory*/
	public function subcategory()
	{
		$this->load->view('library/subcategory');
	}

	public function dtsubcategory()
	{
		$col = [0=>'a.tlcproblem', 1=>'b.tlccat', 2=>'c.tlsdesc', 3=>'CONCAT(d.tlltype, " - ", d.tllstype)', 4=>'a.tlscstatus', 5=>'a.created_at', 6=>'a.updated_at'];
		$order = $col[$_GET['order']['0']['column']];
        $dir = $_GET['order']['0']['dir'];
		$prm = [ '%'.$_GET['search']['value'].'%', '%'.$_GET['search']['value'].'%', '%'.$_GET['search']['value'].'%' ];

		$sql_tr = $this->Libraryclass_model->sqldtsubcategory();

		$start = $_GET['start'];
		$length = $_GET['length'];
		$data_obj = $this->Commonclass_model->datatablefunc($col, $order, $dir, $prm, $sql_tr, $start, $length);

		$totaldata = $data_obj['totalrecord'];
		$data = $data_obj['limit'];	

		$json_data = array(
	        "draw"            => intval($_REQUEST['draw']),
	        "recordsTotal"    => intval($totaldata),
	        "recordsFiltered" => intval($totaldata),
	        "data"            => $data
	    );

		echo json_encode($json_data);
	}

	public function savesubcategory()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('tlcid', 'Category', 'trim|required'); 
		$this->form_validation->set_rules('tlcproblem', 'Subcategory', 'trim|required'); 
		$this->form_validation->set_rules('tlsid', 'Type of Service', 'trim|required'); 
		$this->form_validation->set_rules('tll_id', 'Type of Urgency', 'trim|required'); 
		$this->form_validation->set_rules('tlscstatus', 'Status', 'trim|required'); 
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', 'Oops!! Something went wrong!');
    	} else {
    		$this->Libraryclass_model->savesubcategory();
    		$this->session->set_flashdata('success', 'Successfully  saved!');
        }
        redirect('library/subcategory');
	}

	public function editsubcategory()
	{
		$category = $this->Commonclass_model->category();
		$subcategory = $this->Libraryclass_model->editsubcategory();
		$service = $this->Commonclass_model->services();
		$levels = $this->Commonclass_model->levels();
		echo json_encode(compact('category', 'subcategory', 'service', 'levels'));
	}
}
