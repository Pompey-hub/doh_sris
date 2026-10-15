<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Requestclass extends CI_Controller
{
	public $priv_ = 'Client';

	function __construct()
	{
		parent::__construct();

		if (!$this->session->userdata('loggedin')) {
			redirect('');
		}

		$this->load->model(['Requestclass_model', 'Commonclass_model']);

		if ($this->session->userdata('privilege') === '5') {
			redirect((isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . '/hrmis');
		}

		if ($this->session->userdata('privilege') == '99') {
			$this->priv_ = 'Superadmin';
		} elseif (in_array('SRIS', $this->session->userdata('tlarsys'))) {
			$exp_sys = $this->session->userdata('tlarsys'); #explode('&shen&', $this->session->userdata('tlarsys'));
			$index = array_search('SRIS', $exp_sys);

			$exp_priv = $this->session->userdata('tlarpriv'); #explode('&shen&', $this->session->userdata('tlarpriv'));
			$this->priv_ = $exp_priv[$index];
		} else {
			$this->priv_ = 'Client';
		}
	}

	public function servicerequest()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('tsrsecid', 'Assisting Office', 'trim|required');
		$this->form_validation->set_rules('tsrclient', 'Name of client', 'trim|required');
		$this->form_validation->set_rules('tsrtlcid', 'Category', 'trim|required');
		$this->form_validation->set_rules('tsrtlscid', 'SubCategory', 'trim|required');
		$this->form_validation->set_rules('srdesc', 'Description of request', 'trim|required');
		if ($this->form_validation->run() == FALSE) {
			$res = $this->Requestclass_model->selservicerequest();
			$emp = $this->Commonclass_model->employeepersection();
			$office = $this->Commonclass_model->assistingoffice();
			$category = $this->Commonclass_model->category();
			$tlcid = isset($res[0]['tlcid']) ? $res[0]['tlcid'] : 0;
			$subcategory = $this->Commonclass_model->subcategory($tlcid);
			$this->load->view('home/servicerequest', ['res' => $res, 'emp' => $emp, 'office' => $office, 'category' => $category, 'subcategory' => $subcategory]);
		} else {
			$this->Requestclass_model->srsave();
			$this->session->set_flashdata('success', 'Successfully  saved!');
			redirect('?q=myrequest');
		}
	}

	public function dtrequestdtdef()
	{
		if ($this->priv_ !== 'Client') {
			$this->Requestclass_model->removeReturnedWithNoAction();
		}

		$json_data = $this->dtrequest('def');
		echo json_encode($json_data);
	}

	public function dtrequestdtaccom()
	{
		try {

			$json_data = $this->dtrequest('accom');

			echo json_encode($json_data);
		} catch (Throwable $e) {

			header('Content-Type: application/json');

			echo json_encode([
				'status' => false,
				'error' => $e->getMessage(),
				'file' => $e->getFile(),
				'line' => $e->getLine()
			]);
		}
	}

	public function dtrequestdtmyreq()
	{
		$json_data = $this->dtrequest('myreq');
		echo json_encode($json_data);
	}

	public function dtrequestdtassigned()
	{
		$json_data = $this->dtrequest('assigned');

		echo json_encode($json_data);
	}

	public function getICTUEmployees()
	{
		/*
		 * Only Admin/Superadmin/HRDU-Admin should be able
		 * to retrieve the ICTU employee list for assignment.
		 */
		if (!in_array($this->priv_, ['Admin', 'Superadmin', 'HRDU-Admin'])) {
			echo json_encode([
				'status' => false,
				'message' => 'Unauthorized access.'
			]);
			return;
		}

		$employees = $this->Requestclass_model->getICTUEmployees();

		echo json_encode([
			'status' => true,
			'employees' => $employees
		]);
	}

	public function assignRequest()
	{
		/*
     * Only Admin/Superadmin/HRDU-Admin should be able
     * to assign ICTU service requests.
     */
		if (!in_array($this->priv_, ['Admin', 'Superadmin', 'HRDU-Admin'])) {
			echo json_encode([
				'status' => false,
				'message' => 'Unauthorized access.'
			]);
			return;
		}

		$res = $this->Requestclass_model->assignRequest();

		echo json_encode([
			'status' => $res ? true : false,
			'message' => $res
				? 'Request successfully assigned.'
				: 'Unable to assign request.'
		]);
	}

	public function reassignRequest()
	{
		// ---------------------------------------------------------
		// Only Superadmin can reassign
		// ---------------------------------------------------------

		if ($this->priv_ !== 'Superadmin') {

			echo json_encode([
				'status' => false,
				'message' => 'Unauthorized access.'
			]);

			return;
		}

		// ---------------------------------------------------------
		// Reassign request
		// ---------------------------------------------------------

		$res = $this->Requestclass_model->reassignRequest();

		echo json_encode([
			'status' => $res ? true : false,
			'message' => $res
				? 'Request successfully reassigned.'
				: 'Unable to reassign request.'
		]);
	}


	public function dtrequest($stat)
	{
		$col = [
			0 => 'a.tsrdateadded',
			1 => 'a.tsrdateadded',
			2 => 'CONCAT(c.fname, " ", LEFT(c.mname, 1), ". ", c.lname, " ", c.suffix )',
			3 => 'e.divabbrev',
			4 => 'a.tsrreqdesc',
			5 => 'a.tsrreqdesc',
			6 => 'a.tsrreqdesc'
		];

		$order = $col[$_GET['order']['0']['column']];
		$dir = $_GET['order']['0']['dir'];

		$prm = [
			'%' . $_GET['search']['value'] . '%',
			'%' . $_GET['search']['value'] . '%',
			'%' . $_GET['search']['value'] . '%',
			'%' . $_GET['search']['value'] . '%',
			'%' . $_GET['search']['value'] . '%',
			'%' . $_GET['search']['value'] . '%',
			'%' . $_GET['search']['value'] . '%',
			'%' . $_GET['search']['value'] . '%',
			'%' . $_GET['search']['value'] . '%',
			'%' . $_GET['search']['value'] . '%'
		];


		// =========================================================
		// REQUEST FILTER
		// =========================================================

		if ($stat === 'myreq') {

			// My Pending Request
			//
			// Existing behavior:
			// tbl_service_request.tllid = logged-in user
			//
			$prm[] = $this->session->userdata('lid');
		} elseif ($stat === 'accom') {

			// My Accomplished / Cancelled Request

			if ($this->priv_ == 'Client') {

				$prm[] = $this->session->userdata('lid');
			} elseif (
				$this->priv_ == 'Admin' ||
				$this->priv_ == 'Superadmin'
			) {

				$prm[] = $this->session->userdata('lid');
				$prm[] = $this->session->userdata('secabbrev');
			}
		} elseif ($stat === 'def') {

			// For Office Request

			$prm[] =
				$this->priv_ == 'HRDU-Admin'
				? 'ICTU'
				: $this->session->userdata('secabbrev');
		} elseif ($stat === 'assigned') {

			// =====================================================
			// ASSIGNED TO ME
			// =====================================================
			//
			// The model will use this value to filter:
			//
			// tbl_service_request_assignment.assigned_to
			// =
			// currently logged-in user's lid
			//
			// IMPORTANT:
			//
			// We are NOT using tbl_action_taken.tllid here.
			//
			// tbl_action_taken.tllid means:
			//     Person who recorded the Action Taken.
			//
			// tbl_service_request_assignment.assigned_to means:
			//     Current ICTU Action Officer.
			//
			// =====================================================

			$prm[] = $this->session->userdata('lid');
		}


		// =========================================================
		// GET SQL
		// =========================================================

		$sql_tr = $this->Requestclass_model->sqldtrequest($stat);


		// =========================================================
		// DATATABLE PAGINATION
		// =========================================================

		$start = $_GET['start'];
		$length = $_GET['length'];

		$data_obj = $this->Commonclass_model->datatablefunc(
			$col,
			$order,
			$dir,
			$prm,
			$sql_tr,
			$start,
			$length
		);

		$totaldata = $data_obj['totalrecord'];
		$data = $data_obj['limit'];


		// =========================================================
		// FORMAT DATA
		// =========================================================

		$len = count($data);

		for ($i = 0; $i < $len; $i++) {

			$data[$i]['ticketnum'] =
				date(
					'ymdHis',
					strtotime($data[$i]['tsrdateadded'])
				);

			$data[$i]['tsrid'] =
				$this->cryptography->encrypt(
					$data[$i]['tsrid']
				);

			$data[$i]['tsrdateadded'] =
				$this->Commonclass_model->displaychangeDateFormat(
					$data[$i]['tsrdateadded']
				);

			$data[$i]['tsrdateadded_time'] =
				date(
					'H:i:s',
					strtotime($data[$i]['tsrdateadded_time'])
				);

			$tvcddate =
				(
					$data[$i]['tvcddate'] === NULL ||
					strlen(trim($data[$i]['tvcddate'] === 0))
				)
				? ''
				: date(
					'M d, Y',
					strtotime($data[$i]['tvcddate'])
				);

			$tvcdtimestart =
				(
					$data[$i]['tvcdtimestart'] === NULL ||
					strlen(trim($data[$i]['tvcdtimestart'] === 0))
				)
				? ''
				: ' @ ' . date(
					'h:i A',
					strtotime($data[$i]['tvcdtimestart'])
				);

			$data[$i]['tsrreqdesc'] =
				$data[$i]['tvcdid'] === NULL
				? nl2br(
					html_escape(
						$data[$i]['tsrreqdesc'],
						ENT_QUOTES
					)
				)
				: (
					'VIDCON: ' .
					$data[$i]['tvcdplatform'] .
					' (' .
					$tvcddate .
					$tvcdtimestart .
					')<br/><i>Topic: ' .
					$data[$i]['tvcdtitle'] .
					'</i>'
				);

			$data[$i]['hrefroute'] =
				$data[$i]['tvcdid'] === NULL
				? 'servicerequest'
				: 'servicerequestvidcon';

			$data[$i]['takremarks'] =
				(
					$data[$i]['takremarks'] === ',,'
					? ''
					: nl2br(
						html_escape(
							trim($data[$i]['takremarks']),
							ENT_QUOTES
						)
					)
				);

			$data[$i]['tsrmobilenum'] =
				html_escape(
					$data[$i]['tsrmobilenum'],
					ENT_QUOTES
				);

			$data[$i]['tsrid_enc'] =
				html_escape(
					$this->Commonclass_model->encrypt_decrypt(
						'encrypt',
						$data[$i]['tsrid_enc']
					)
				);

			$data[$i]['myuname'] =
				(
					trim($data[$i]['uname']) ===
					trim($this->session->userdata('uname'))
				)
				? TRUE
				: FALSE;

			$data[$i]['isdisabled'] =
				(
					$data[$i]['tsrdateadded'] === date('M d, Y') &&
					(
						$data[$i]['cssid'] === null ||
						$data[$i]['cssid'] === 0
					)
				)
				? ''
				: 'disabled';

			$data[$i]['uname'] =
				strtoupper(
					$data[$i]['uname']
				);

			$data[$i]['officer'] =
				strtoupper(
					$data[$i]['officer']
				);

			$data[$i]['assigned_by'] =
				strtoupper(
					$data[$i]['assigned_by']
				);

			$data[$i]['tsrclient'] =
				isset($data[$i]['fname'])
				? $this->Commonclass_model->concat_fullname(
					$data[$i]['fname'],
					$data[$i]['mname'],
					$data[$i]['lname'],
					$data[$i]['suffix'],
					$data[$i]['extname']
				)
				: 'N/A';


			// =====================================================
			// FOR ACTION REMARKS
			// =====================================================

			if ($data[$i]['tlsrsdesc'] === 'For Action') {

				$add_remarks_str = '';

				$add_remarks =
					$this->Requestclass_model->additional_remarks(
						$data[$i]['takid']
					);

				for (
					$j = 0;
					$j < count($add_remarks);
					$j++
				) {

					$add_remarks_str .=
						'<br><span>' .
						$add_remarks[$j]['takremarks'] .
						'</span>';

					$add_remarks_str .=
						'<br><span class="text-muted" style="font-size: .8rem;">[' .
						$add_remarks[$j]['takdateadded'] .
						' ' .
						$add_remarks[$j]['taktimeadded'] .
						']</span><br>';
				}

				$takremarks_ =
					'<br><span class="text-muted" style="font-size: .8rem;">[' .
					date(
						'M d, Y H:i:s',
						strtotime(
							$data[$i]['takdateadded_dis']
						)
					) .
					']</span><br>';

				$data[$i]['takremarks'] =
					(
						strlen(
							trim(
								$data[$i]['takremarks']
							)
						) === 0
						? '-'
						: $data[$i]['takremarks']
					) .
					$takremarks_ .
					' ' .
					$add_remarks_str;
			}
		}


		// =========================================================
		// DATATABLE RESPONSE
		// =========================================================

		$json_data = array(
			"draw" =>
			intval($_REQUEST['draw']),

			"recordsTotal" =>
			intval($totaldata),

			"recordsFiltered" =>
			intval($totaldata),

			"data" =>
			$data,

			"jspriv" =>
			strtolower($this->priv_),

			"jsictu" =>
			$this->Commonclass_model->encrypt_decrypt(
				'encrypt',
				'1'
			)
		);

		return $json_data;
	}

	public function srcancel()
	{
		$srcancel = $this->Requestclass_model->srcancel();
		echo json_encode(['srcancel' => $srcancel]);
	}

	public function srlogstatus()
	{
		$tsrreqdesc = $this->Requestclass_model->srtsrreqdesc($this->cryptography->decrypt($this->input->get('xtsrid')));
		$srlogstatus = $this->Requestclass_model->srlogstatus();
		$len = count($srlogstatus);
		for ($i = 0; $i < $len; $i++) {
			$srlogstatus[$i]['add_remarks'] = $this->Requestclass_model->additional_remarks($srlogstatus[$i]['takid']);
			$srlogstatus[$i]['takid'] = $this->cryptography->encrypt($srlogstatus[$i]['takid']);
			$srlogstatus[$i]['takdateadded'] = $this->Commonclass_model->displaychangeDateFormat($srlogstatus[$i]['takdateadded']);
			$srlogstatus[$i]['taktimeadded'] = date('H:i:s', strtotime($srlogstatus[$i]['taktimeadded']));
			$srlogstatus[$i]['takdesc'] = html_escape($srlogstatus[$i]['takdesc'], ENT_QUOTES);
			$srlogstatus[$i]['takremarks'] = html_escape($srlogstatus[$i]['takremarks'], ENT_QUOTES);
			/*$srlogstatus[$i]['tsrreqdesc'] = html_escape($srlogstatus[$i]['tsrreqdesc'], ENT_QUOTES);*/
		}

		// var_dump($tsrreqdesc);
		// var_dump($srlogstatus);
		// die();


		$tllpriv = ($this->priv_ === 'Superadmin' ? 'Admin' : $this->priv_);
		echo json_encode(['srlogstatus' => $srlogstatus, 'tllpriv' => $tllpriv, 'tsrreqdesc' => $tsrreqdesc]);
	}

	public function srseleditactiontaken()
	{
		$res = $this->Requestclass_model->srseleditactiontaken();
		echo json_encode(['res' => $res]);
	}

	public function srdeletelogstatus()
	{
		$res = $this->Requestclass_model->srdeletelogstatus();
		echo json_encode(['res' => $res]);
	}

	public function sradditionalremarks()
	{
		$res = $this->Requestclass_model->sradditionalremarks();
		echo json_encode(['res' => $res]);
	}

	public function srdeleteadditionalremarks()
	{
		$res = $this->Requestclass_model->srdeleteadditionalremarks();
		echo json_encode(['res' => $res]);
	}

	public function sreditactiontaken()
	{
		$sreditat = $this->Requestclass_model->sreditactiontaken();
		$csrfhash = $this->security->get_csrf_hash();
		echo json_encode(['sreditat' => $sreditat, 'csrfhash' => $csrfhash]);
	}

	public function srtbllibsrstatus()
	{
		$tlsrstatus = $this->Commonclass_model->listsrstatus();

		$len = count($tlsrstatus);

		for ($i = 0; $i < $len; $i++) {
			$tlsrstatus[$i]['tlsrsid'] =
				$this->cryptography->encrypt(
					$tlsrstatus[$i]['tlsrsid']
				);
		}

		$atoptdisabled =
			$this->Requestclass_model->srtbllibsrstatus();

		$emp =
			$this->Requestclass_model->employeepersection();

		$emp_def =
			$this->Requestclass_model->employeeDefault();

		/*
     * Check if this request currently has
     * an active ICTU employee assignment.
     */
		$tsrid = $this->cryptography->decrypt(
			$this->input->get('xtsrid')
		);

		$has_assignment = false;

		if (!empty($tsrid)) {

			$assignment = $this->db
				->select('assignment_id')
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

		echo json_encode([
			'tlsrstatus'    => $tlsrstatus,
			'atoptdisabled' => $atoptdisabled,
			'emp'           => $emp,
			'emp_def'       => $emp_def,
			'has_assignment' => $has_assignment
		]);
	}

	public function conforme()
	{
		$res = $this->Requestclass_model->conforme();
		echo json_encode(['res' => $res]);
	}

	public function srcheckconforme()
	{
		$res = $this->Requestclass_model->srcheckconforme();
		$cnt = isset($res[0]['cnt']) ? (count($res) * 1) : 0;
		echo json_encode(['cnt' => $cnt]);
	}

	public function servicerequestview()
	{
		$res = $this->Requestclass_model->selservicerequest();
		$emp = $this->Commonclass_model->employee();

		$tlcid = isset($res[0]['tlcid']) ? $res[0]['tlcid'] : 0;
		$category = $this->Commonclass_model->category();
		$subcategory = $this->Commonclass_model->subcategory($tlcid);
		$office = $this->Commonclass_model->assistingoffice();

		$this->load->view('home/servicerequest', ['res' => $res, 'emp' => $emp, 'category' => $category, 'subcategory' => $subcategory, 'office' => $office, 'viewonly' => true]);
	}

	public function subcategory()
	{
		$subcategory = $this->Commonclass_model->subcategory($this->input->get('tsrtlcid'));
		echo json_encode(['subcategory' => $subcategory]);
	}

	public function actiontakensubcategory()
	{
		$subcategory = $this->Commonclass_model->actiontakensubcategory($this->input->get('xtlsid'));
		echo json_encode(compact('subcategory'));
	}
}
