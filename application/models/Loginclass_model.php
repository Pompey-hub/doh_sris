<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Loginclass_model extends CI_Model
{
	public function cntAccomplishedRequest()
	{
		$prm = ['Active', $this->session->userdata('lid'), 0, 3, 6];
		$sql = $this->sqlcntAccomplishedRequest();
		$res = $this->db->query($sql, $prm)->result_array();
		return $res;
	}
	private function sqlcntAccomplishedRequest()
	{
		$sql = 'SELECT COUNT(a.tsrid) as cnt
				FROM tbl_service_request a 
				LEFT JOIN tbl_action_taken b ON a.tsrid=b.tsrid AND b.takstatus="Active" 
            	LEFT JOIN tbl_lib_srstatus c ON b.tlsrsid=c.tlsrsid
				WHERE a.tsrstatus=? AND a.tllid=? AND ( DATE(a.tsrdateadded)=CURDATE() AND (a.cssid IS NULL OR a.cssid=?) ) AND (c.tlsrsid IN (?, ?))';
		return $sql;
	}


	/*public function validaterequest()
	{
		$uname = trim($this->input->post('uname'));
		$pword = trim($this->input->post('pword'));

		$prm = [ $uname, $uname, 'Active' ];
		$sql = 'SELECT a.tllid, a.tlluname, a.tllpword, a.empid, a.tllpriv, a.tllnumber, a.tllemail,
						CONCAT(b.fname, " ", LEFT(b.mname, 1), ". ", b.lname, " ", b.suffix ) as fullname,
						c.secabbrev, d.divabbrev
                FROM tbl_lib_login a
                LEFT JOIN db_common.tbl_employee b ON a.empid=b.empid
                LEFT JOIN db_common.tbl_section c ON a.secid=c.secid 
                LEFT JOIN db_common.tbl_division d ON c.divid=d.divid 
                WHERE (a.tlluname = ? OR a.tllemail = ?)
                    AND a.tllstatus = ?';
		$query = $this->db->query($sql, $prm);

		if ($query->num_rows() === 1) {
			$query = $query->row_array();
			
			if (password_verify($pword, $query['tllpword']) && $query['empid'] !== null) {
				$fullname = $this->selectFullname($query['empid']);

				$data = [
                    'tllid' =>  $this->cryptography->encrypt($query['tllid']),
                    'tlluname' => $query['tlluname'],
                    'loggedin' => TRUE,
                    'fullname' => $fullname,
                    'empid' => $this->cryptography->encrypt(trim($query['empid'])),
                    'email' => $query['tllemail'],
                    'tllpriv' => $query['tllpriv'],
                    'tllprivdet' => ($query['tllpriv']==='1'?'Client':($query['tllpriv']==='2'?'Admin':'SuperAdmin')),
                    'contactnum' => $query['tllnumber'],
                    'emailadd' => $query['tllemail'],
                    'secabbrev' => $query['secabbrev'],
                    'divabbrev' => $query['divabbrev']
                ];
                session_set_cookie_params(0);
				$this->session->set_userdata($data);
				$this->group_concat_max_len();
				
				$this->load->helper('dynamic_helper');
				my_log(' LOGIN / ' . $query['lid'] . ' / ' . $query['uname'], 'user');

				return true;
			}
		}

		return false;
	}



	private function selectFullname($empid)
	{
		$prm = [$empid];
		$sql = 'SELECT CONCAT(fname, " ", LEFT(mname, 1), ". ", lname, " ", suffix ) as fullname, empid
				FROM db_common.tbl_employee 
				WHERE empid=?';
		$res = $this->db->query($sql, $prm)->result_array();
		return (isset($res[0]['fullname']) ? $res[0]['fullname'] : 'NONE');
	}*/

	public function sractiontakensave()
	{
		$this->db->trans_begin();

		// ==========================================================
		// GET REQUEST ID
		// ==========================================================
		$tsrid = $this->cryptography->decrypt(
			$this->input->post('xtsrid')
		);

		// ==========================================================
		// GET ACTION
		// ==========================================================
		$tlsrsid = $this->cryptography->decrypt(
			$this->input->post('tlsrsid')
		);

		// ==========================================================
		// GET SELECTED ICTU EMPLOYEE
		//
		// This will only have a value during the initial
		// assignment (Received / Returned).
		//
		// For "For Action", "Accomplished", and "Cancelled",
		// the field is hidden and will be empty.
		// ==========================================================
		$assigned_to = trim(
			$this->input->post('assigned_to')
		);

		// ==========================================================
		// CURRENT LOGGED-IN USER
		//
		// This is the person who records the Action Taken.
		// ==========================================================
		$current_user = $this->session->userdata('lid');


		// ==========================================================
		// 1. SAVE ACTION TAKEN
		// ==========================================================

		$prm = array(
			'tsrid' => $tsrid,

			'tlsrsid' => $tlsrsid,

			// Person who recorded/saved the Action Taken.
			// DO NOT use assigned_to here.
			'tllid' => $current_user,

			'takdateadded' => date('Y-m-d G:i:s'),

			'takdesc' =>
			strlen(trim($this->input->post('takdesc'))) === 0
				? null
				: $this->input->post('takdesc'),

			'takmacaddress' =>
			strlen(trim($this->input->post('takmacaddress'))) === 0
				? null
				: $this->input->post('takmacaddress'),

			'takstatus' => 'Active',

			'takremarks' =>
			$this->input->post('takremarks'),

			'tlscid' =>
			strlen(trim($this->input->post('tlscid'))) === 0
				? null
				: $this->input->post('tlscid'),

			'takclientempid' =>
			$this->input->post('takclientempid'),

			'taktakapprove' =>
			$this->input->post('takapprove'),

			'takhostname' =>
			$this->input->post('takhostname'),

			'takserialnumber' =>
			$this->input->post('serialnumber')
		);

		$this->db->insert(
			'tbl_action_taken',
			$prm
		);


		// ==========================================================
		// 2. UPDATE SERVICE REQUEST
		// ==========================================================

		$prm = array(
			'tlsid' => $this->input->post('xtlsid')
		);

		$this->db->where(
			'tsrid',
			$tsrid
		);

		$this->db->update(
			'tbl_service_request',
			$prm
		);


		// ==========================================================
		// 3. HANDLE ICTU EMPLOYEE ASSIGNMENT
		// ==========================================================
		//
		// IMPORTANT:
		//
		// If assigned_to has a value:
		//     This is a NEW assignment.
		//
		// If assigned_to is empty:
		//     DO NOT change the existing assignment.
		//
		// This allows the workflow:
		//
		// Received / Returned
		//     ↓
		// Select ICTU Employee
		//     ↓
		// Create assignment
		//
		// For Action
		//     ↓
		// Keep existing assignment
		//
		// Accomplished / Cancelled
		//     ↓
		// Keep existing assignment
		//
		// ==========================================================

		if (!empty($assigned_to)) {

			// ------------------------------------------------------
			// Deactivate previous active assignment
			// ------------------------------------------------------
			$this->db->where(
				'tsrid',
				$tsrid
			);

			$this->db->where(
				'status',
				'Active'
			);

			$this->db->update(
				'tbl_service_request_assignment',
				array(
					'status' => 'Inactive'
				)
			);


			// ------------------------------------------------------
			// Create new assignment
			// ------------------------------------------------------
			$assignment = array(
				'tsrid' => $tsrid,

				'assigned_to' => $assigned_to,

				// Person who assigned the ICTU employee.
				'assigned_by' => $current_user,

				'assigned_date' => date('Y-m-d H:i:s'),

				'status' => 'Active',

				'remarks' =>
				strlen(trim($this->input->post('takremarks'))) === 0
					? null
					: $this->input->post('takremarks')
			);

			$this->db->insert(
				'tbl_service_request_assignment',
				$assignment
			);
		} else {

			// ------------------------------------------------------
			// NO NEW EMPLOYEE SELECTED
			//
			// This is expected for:
			//
			//     For Action
			//     Accomplished
			//     Cancelled
			//
			// Therefore, leave the existing active assignment
			// untouched.
			// ------------------------------------------------------
		}


		// ==========================================================
		// 4. CHECK TRANSACTION
		// ==========================================================

		if ($this->db->trans_status() === FALSE) {

			$this->db->trans_rollback();

			$res = false;
		} else {

			$this->db->trans_commit();

			$res = true;
		}


		return $res;
	}


	private function group_concat_max_len()
	{
		$prm = [1000000];
		$sql = 'SET SESSION group_concat_max_len = ?';
		$this->db->query($sql, $prm);

		$prm = [100000];
		$sql = 'SET GLOBAL group_concat_max_len = ?';
		$this->db->query($sql, $prm);
	}
}
