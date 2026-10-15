<?php
defined('BASEPATH') or exit('No direct script access allowed');

require 'vendor/autoload.php';

class Reportclass extends CI_Controller
{

	function __construct()
	{
		parent::__construct();

		if (!$this->session->userdata('loggedin')) {
			redirect('');
		}

		$this->load->model(['Reportclass_model', 'Commonclass_model']);
	}

	public function srfrep()
	{
		$tlsrsid = ($this->cryptography->decrypt($this->input->get('tlsrsid')) === false ? '-1' : $this->cryptography->decrypt($this->input->get('tlsrsid')));
		$mm = ($this->input->get('mm') === null ? date('F') : $this->input->get('mm'));
		$yy = ($this->input->get('yy') === null ? date('Y') : $this->input->get('yy'));
		$off = ($this->cryptography->decrypt($this->input->get('off')) === false ? '-1' : $this->cryptography->decrypt($this->input->get('off')));
		$serv = ($this->cryptography->decrypt($this->input->get('serv')) === false ? '-1' : $this->cryptography->decrypt($this->input->get('serv')));

		$srv = $this->Commonclass_model->services();
		$actoff = $this->Reportclass_model->actionofficer();
		$res = $this->Reportclass_model->srfrep(date('m', strtotime($mm . ' 1')), $yy, $off, $serv, $tlsrsid);

		$this->load->view('report/srf', ['off_g' => $off, 'mm_g' => $mm, 'yy_g' => $yy, 'serv_g' => $serv, 'actoff' => $actoff, 'res' => $res, 'srv' => $srv, 'tlsrsid_g' => $tlsrsid]);
	}

	public function srfsummary()
	{
		$mm = ($this->input->get('mm') === null ? date('F') : $this->input->get('mm'));
		$yy = ($this->input->get('yy') === null ? date('Y') : $this->input->get('yy'));
		$off = ($this->cryptography->decrypt($this->input->get('off')) === false ? '-1' : $this->cryptography->decrypt($this->input->get('off')));

		$actoff = $this->Reportclass_model->actionofficer();
		$rcvd = $this->Reportclass_model->srfsummary(date('m', strtotime($mm . ' 1')), $yy, $off, 1);
		$actn = $this->Reportclass_model->srfsummary(date('m', strtotime($mm . ' 1')), $yy, $off, 2);
		$accomp = $this->Reportclass_model->srfsummary(date('m', strtotime($mm . ' 1')), $yy, $off, 3);
		$this->load->view('report/srfsummary', ['off_g' => $off, 'mm_g' => $mm, 'yy_g' => $yy, 'actoff' => $actoff, 'rcvd' => $rcvd, 'actn' => $actn, 'accomp' => $accomp]);
	}




	public function srfsummaryarta()
	{
		// ==========================================================
		// GET REPORT FILTERS
		// ==========================================================
		$mm = $this->input->get('mm');
		$yy = $this->input->get('yy');

		$mm = ($mm === null || $mm === '') ? date('F') : $mm;
		$yy = ($yy === null || $yy === '') ? date('Y') : $yy;

		// Validate month.
		$validMonths = [
			'January',
			'February',
			'March',
			'April',
			'May',
			'June',
			'July',
			'August',
			'September',
			'October',
			'November',
			'December'
		];

		if (!in_array($mm, $validMonths, true)) {
			$mm = date('F');
		}

		// Validate year.
		if (!preg_match('/^\d{4}$/', (string) $yy)) {
			$yy = date('Y');
		}

		// Decrypt filters.
		$tlsrsidValue = $this->cryptography->decrypt(
			$this->input->get('tlsrsid')
		);

		$offValue = $this->cryptography->decrypt(
			$this->input->get('off')
		);

		$servValue = $this->cryptography->decrypt(
			$this->input->get('serv')
		);

		$tlsrsid = (
			$tlsrsidValue !== false &&
			$tlsrsidValue !== null &&
			$tlsrsidValue !== ''
		) ? (string) $tlsrsidValue : '-1';

		$off = (
			$offValue !== false &&
			$offValue !== null &&
			$offValue !== ''
		) ? (string) $offValue : '-1';

		$serv = (
			$servValue !== false &&
			$servValue !== null &&
			$servValue !== ''
		) ? (string) $servValue : '-1';

		// ==========================================================
		// LOAD REPORT DATA
		// ==========================================================
		$srv = $this->Commonclass_model->services();
		$actoff = $this->Reportclass_model->actionofficer();

		$monthNumber = date('m', strtotime($mm . ' 1'));

		$res = $this->Reportclass_model->srfrep(
			$monthNumber,
			$yy,
			$off,
			$serv,
			$tlsrsid
		);

		// ==========================================================
		// LOAD SPECIAL DAYS
		// ==========================================================
		$specialDays = [];

		$specialDaysQuery = $this->db->query("
        SELECT
            sd_date,
            sd_start_time,
            scope_id,
            scope_relation_id_val
        FROM db_common.tbl_lib_special_days
        WHERE sd_date IS NOT NULL
    ");

		if ($specialDaysQuery !== false) {
			$specialDays = $specialDaysQuery->result_array();
		}

		// ==========================================================
		// LOAD WEB REPORT
		// ==========================================================
		$this->load->view('report/srfarta', [
			'off_g'        => $off,
			'mm_g'         => $mm,
			'yy_g'         => $yy,
			'serv_g'       => $serv,
			'actoff'       => $actoff,
			'res'          => $res,
			'srv'          => $srv,
			'tlsrsid_g'    => $tlsrsid,
			'special_days' => $specialDays
		]);
	}




	public function vidcon()
	{
		$mm = ($this->input->get('mm') === null ? date('F') : $this->input->get('mm'));
		$yy = ($this->input->get('yy') === null ? date('Y') : $this->input->get('yy'));

		$mm_ = date('m', strtotime($mm));
		$res = $this->Reportclass_model->vidcon($mm_, $yy);
		$this->load->view('report/vidcon', ['mm_g' => $mm, 'yy_g' => $yy, 'res' => $res]);
	}

	/*mpdf below*/

	public function repsrfsummary()
	{
		$mm = ($this->input->get('mm') === null ? date('F') : $this->input->get('mm'));
		$yy = ($this->input->get('yy') === null ? date('Y') : $this->input->get('yy'));
		$off = ($this->cryptography->decrypt($this->input->get('off')) === false ? '-1' : $this->cryptography->decrypt($this->input->get('off')));

		$actofficer = $this->Commonclass_model->selactionofficer($off);
		#$res = $this->Reportclass_model->srfsummary(date('m', strtotime($mm)), $yy, $off);

		$rcvd = $this->Reportclass_model->srfsummary(date('m', strtotime($mm)), $yy, $off, 1);
		$actn = $this->Reportclass_model->srfsummary(date('m', strtotime($mm)), $yy, $off, 2);
		$accomp = $this->Reportclass_model->srfsummary(date('m', strtotime($mm)), $yy, $off, 3);

		$html = $this->load->view('reportpdf/repsrfsummary', ['off_g' => $off, 'mm_g' => $mm, 'yy_g' => $yy, 'off' => $off, 'actofficer' => $actofficer, 'rcvd' => $rcvd, 'actn' => $actn, 'accomp' => $accomp], true);
		$pdf = new \Mpdf\Mpdf(['format' => 'A4']);
		$pdf->AddPage(
			'P',
			'',
			'',
			'',
			'',
			10,
			10,
			10,
			13,
			10,
			5
		);
		$pdf->WriteHTML($html); // write the HTML into the PDF
		$pdf->Output();
	}


	public function repsrflist()
	{
		$tlsrsid = ($this->cryptography->decrypt($this->input->get('tlsrsid')) === false ? '-1' : $this->cryptography->decrypt($this->input->get('tlsrsid')));
		$mm = ($this->input->get('mm') === null ? date('F') : $this->input->get('mm'));
		$yy = ($this->input->get('yy') === null ? date('Y') : $this->input->get('yy'));
		$off = ($this->cryptography->decrypt($this->input->get('off')) === false ? '-1' : $this->cryptography->decrypt($this->input->get('off')));
		$serv = ($this->cryptography->decrypt($this->input->get('serv')) === false ? '-1' : $this->cryptography->decrypt($this->input->get('serv')));

		$srv = $this->Commonclass_model->services();
		$actofficer = $this->Commonclass_model->selactionofficer($off);
		$res = $this->Reportclass_model->srfrep(date('m', strtotime($mm)), $yy, $off, $serv, $tlsrsid);

		$html = $this->load->view('reportpdf/repsrflist', ['off_g' => $off, 'mm_g' => $mm, 'yy_g' => $yy, 'serv_g' => $serv, 'off' => $off, 'actofficer' => $actofficer, 'res' => $res, 'srv' => $srv, 'tlsrsid_g' => $tlsrsid], true);

		if ($tlsrsid === '-1') {
			$orientation = 'L';
		} else {
			$orientation = 'P';
		}

		$pdf = new \Mpdf\Mpdf(['format' => 'A4']);
		$pdf->AddPage(
			$orientation,
			'',
			'',
			'',
			'',
			5,
			5,
			10,
			13,
			10,
			5
		);
		$pdf->WriteHTML($html); // write the HTML into the PDF
		$pdf->Output();
	}

	public function repsrfassignmentresolution()
	{

		// Get report filters.
		$mm = ($this->input->get('mm') === null)
			? date('F')
			: $this->input->get('mm');

		$yy = ($this->input->get('yy') === null)
			? date('Y')
			: $this->input->get('yy');

		// Decrypt service filter.
		$serv_decrypted = $this->cryptography->decrypt(
			$this->input->get('serv')
		);

		$serv = ($serv_decrypted === false || $serv_decrypted === null || $serv_decrypted === '')
			? '-1'
			: (string) $serv_decrypted;

		// Get assigned ICTU officer filter.
		$officer = $this->input->get('officer');

		if ($officer === null || $officer === '') {
			$officer = '';
		} elseif (ctype_digit((string) $officer) && (int) $officer > 0) {
			$officer = (string) $officer;
		} else {
			$officer = '';
		}

		// Get services and assigned ICTU officers for the PDF view.
		$srv = $this->Commonclass_model->services();

		$officers = $this->Reportclass_model
			->srfassignmentresolutionofficers();

		// Get filtered report data.
		$res = $this->Reportclass_model->srfassignmentresolution(
			date('m', strtotime($mm)),
			$yy,
			$serv,
			$officer
		);

		// Load the PDF template.
		$html = $this->load->view(
			'reportpdf/repsrfassignmentresolution',
			[
				'mm_g'     => $mm,
				'yy_g'     => $yy,
				'serv_g'   => $serv,
				'officer_g' => $officer,
				'srv'      => $srv,
				'officers' => $officers,
				'res'      => $res
			],
			true
		);

		// The assignment resolution report uses landscape orientation.
		$orientation = 'L';

		// Generate PDF using the existing mPDF configuration.
		$pdf = new \Mpdf\Mpdf([
			'format' => 'A4'
		]);

		$pdf->AddPage(
			$orientation,
			'',
			'',
			'',
			'',
			5,
			5,
			10,
			13,
			10,
			5
		);

		$pdf->WriteHTML($html);
		$pdf->Output();
	}



	public function repsrfsummaryarta()
	{
		// ==========================================================
		// GET FILTERS
		// ==========================================================
		$mm = $this->input->get('mm');
		$yy = $this->input->get('yy');

		$mm = !empty($mm) ? $mm : date('F');
		$yy = !empty($yy) ? $yy : date('Y');

		$off = $this->cryptography->decrypt(
			$this->input->get('off')
		);

		$serv = $this->cryptography->decrypt(
			$this->input->get('serv')
		);

		$tlsrsid = $this->cryptography->decrypt(
			$this->input->get('tlsrsid')
		);

		$off = ($off === false || $off === null || $off === '')
			? '-1'
			: (string) $off;

		$serv = ($serv === false || $serv === null || $serv === '')
			? '-1'
			: (string) $serv;

		$tlsrsid = ($tlsrsid === false || $tlsrsid === null || $tlsrsid === '')
			? '-1'
			: (string) $tlsrsid;

		// Convert month name to a two-digit month number.
		$monthNumber = date('m', strtotime($mm . ' 1'));

		// ==========================================================
		// LOAD REPORT DATA
		// ==========================================================
		$srv = $this->Commonclass_model->services();

		$actoff = $this->Reportclass_model->actionofficer();

		// Selected officer details for the signature.
		$actofficer = [];

		if ($off !== '-1') {
			$actofficer = $this->Commonclass_model
				->selactionofficer($off);
		}

		// Existing SRF report query.
		$res = $this->Reportclass_model->srfrep(
			$monthNumber,
			$yy,
			$off,
			$serv,
			$tlsrsid
		);

		// ==========================================================
		// LOAD SPECIAL DAYS FROM db_common
		// ==========================================================
		$specialDays = [];

		$specialDaysQuery = $this->db->query("
        SELECT
            sd_date,
            sd_start_time,
            scope_id,
            scope_relation_id_val
        FROM db_common.tbl_lib_special_days
        WHERE sd_date IS NOT NULL
    ");

		if ($specialDaysQuery !== false) {
			$specialDays = $specialDaysQuery->result_array();
		}

		// ==========================================================
		// GENERATE PDF
		// ==========================================================
		$html = $this->load->view(
			'reportpdf/repsrfsummaryarta',
			[
				'off_g'        => $off,
				'mm_g'         => $mm,
				'yy_g'         => $yy,
				'serv_g'       => $serv,
				'actoff'       => $actoff,
				'actofficer'   => $actofficer,
				'res'          => $res,
				'srv'          => $srv,
				'tlsrsid_g'    => $tlsrsid,
				'special_days' => $specialDays
			],
			true
		);

		$pdf = new \Mpdf\Mpdf(['format' => 'A4']);

		$pdf->AddPage(
			'P',
			'',
			'',
			'',
			'',
			10,
			10,
			10,
			13,
			10,
			5
		);

		$pdf->WriteHTML($html);
		$pdf->Output();
	}


	public function srfassignmentresolution()
	{
		// Get report filters.
		$mm = $this->input->get('mm', true);
		$yy = $this->input->get('yy', true);
		$encryptedServ = $this->input->get('serv', true);
		$officer = $this->input->get('officer', true);

		// Decrypt service filter safely.
		$serv = '-1';

		if ($encryptedServ !== null && $encryptedServ !== '') {
			$decryptedServ = $this->cryptography->decrypt($encryptedServ);

			if (
				$decryptedServ !== false &&
				$decryptedServ !== null &&
				is_numeric($decryptedServ)
			) {
				$serv = (string) (int) $decryptedServ;
			}
		}

		// Validate month.
		$validMonths = [
			'January',
			'February',
			'March',
			'April',
			'May',
			'June',
			'July',
			'August',
			'September',
			'October',
			'November',
			'December'
		];

		if (!in_array($mm, $validMonths, true)) {
			$mm = date('F');
		}

		// Validate year.
		if (
			$yy === null ||
			!preg_match('/^\d{4}$/', (string) $yy)
		) {
			$yy = date('Y');
		}

		// Validate service filter.
		$validSpecialServices = ['-1', '-2', '-3'];

		if (
			!in_array($serv, $validSpecialServices, true) &&
			(!ctype_digit($serv) || (int) $serv <= 0)
		) {
			$serv = '-1';
		}

		// Validate assigned ICTU officer.
		$officer = (
			$officer !== null &&
			ctype_digit((string) $officer) &&
			(int) $officer > 0
		) ? (string) (int) $officer : '';

		// Retrieve available services.
		$srv = $this->Commonclass_model->services();

		// Retrieve available ICTU officers.
		$officers = $this->Reportclass_model
			->srfassignmentresolutionofficers();

		// Retrieve report records, including officer filtering.
		$res = $this->Reportclass_model->srfassignmentresolution(
			date('m', strtotime($mm . ' 1')),
			$yy,
			$serv,
			$officer
		);

		// Retrieve Action Taken Log Status history.
		$actionLogs = $this->Reportclass_model
			->srfassignmentresolutionactionlogs($res);

		// Display report.
		$this->load->view('report/srf_assignment_resolution', [
			'mm_g'       => $mm,
			'yy_g'       => $yy,
			'serv_g'     => $serv,
			'officer_g'  => $officer,
			'srv'        => $srv,
			'officers'   => $officers,
			'res'        => $res,
			'action_logs' => $actionLogs
		]);
	}
}
