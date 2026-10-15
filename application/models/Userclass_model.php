<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Userclass_model extends CI_Model
{
	public function sqldtusers() {
		$sql = 'SELECT a.tllid, a.tlluname, IF(a.tllpriv=1,"Client", IF(a.tllpriv=2,"Admin", "Super Admin")) as tllpriv,
						a.tllstatus, a.tlldateadded, a.tllfactype, 
						IF(a.tllpriv=1, "N/A", CONCAT(b.fname, " ", LEFT(b.mname, 1), ". ", b.lname, " ", b.suffix )) as fullname, 
						CONCAT(e.divabbrev, "-",f.secabbrev) as office
                FROM tbl_lib_login a
                LEFT JOIN db_common.tbl_employee b ON a.empid=b.empid

                /*LEFT JOIN tbl_lib_detail_emp c ON a.tllid=c.tllid AND c.tldestatus="Active"*/
				
				LEFT JOIN db_common.tbl_section f ON a.secid=f.secid
                LEFT JOIn db_common.tbl_division e ON f.divid=e.divid 


                WHERE CONCAT(b.fname, " ", LEFT(b.mname, 1), ". ", b.lname, " ", b.suffix ) LIKE ? OR a.tlluname LIKE ? OR 
                	a.tllfactype LIKE ? OR CONCAT(e.divabbrev, "-",f.secabbrev) LIKE ? OR a.tllpriv LIKE ? OR a.tllstatus LIKE ? OR a.tlldateadded LIKE ? ';
		return $sql;
	}


	public function edituser($lid)
	{
		$prm = [ $lid ];
		$sql = $this->sqledituser();
		$res = $this->db->query($sql, $prm)->result_array();
		return $res;
	}


	public function srsqlcredentials()
	{
		$prm = [$this->cryptography->decrypt($this->input->get('q'))];
		$sql = 'SELECT a.*
				FROM tbl_lib_login a 
				WHERE a.tllid=?';
		$res = $this->db->query($sql, $prm)->result_array();
		return $res;
	}

	public function srcredentials()
	{
		$srtllid = $this->input->post('srtllid');
		$srtllfactype = $this->input->post('srtllfactype');
		$srempid = $this->input->post('srempid');
		$srtlluname = $this->input->post('srtlluname');
		$srtllpriv = $this->input->post('srtllpriv');

		$srtllemail = $this->input->post('srtllemail');
		$srtllnumber = $this->input->post('srtllnumber');
		$srtllstatus = $this->input->post('srtllstatus');
		$srtllsecid = $this->input->post('srtllsecid');
		$srdbpid = $this->input->post('srdbpid');

		$prm = [
	        'tlluname' => $srtlluname,
	        'tllpword' => PASSWORD_HASH('Zaq12wsx', PASSWORD_DEFAULT),
	        'empid' => $this->cryptography->decrypt($srempid),
	        'secid' => $this->cryptography->decrypt($srtllsecid),


	        'tllfactype' => $srtllfactype,
	        'tllpriv' => $srtllpriv,
	        'tllstatus' => $srtllstatus,
	        'tllnumber' => $srtllnumber,
	        'tllemail' => $srtllemail,
	        'tlldateadded' => date('Y-m-d G:i:s')
		];

		if (strlen(trim($srtllid)) === 0) {	
			$res = $this->db->insert('tbl_lib_login', $prm);
		} else {
			$srtllid = $this->cryptography->decrypt($srtllid);
			$this->db->where('tllid', $srtllid);
			$res = $this->db->update('tbl_lib_login', $prm);
		}
		return $res;

	}



	/* delete below */


	/**
	 * save or update users
	 * @param $request
	 */
	public function saveuser() {
		$m_lid = $this->input->post('m_lid');
		$m_uname = $this->input->post('m_uname');
		$m_empid = $this->input->post('m_empid');
		$m_status = $this->input->post('m_status');
		$m_pword = $this->input->post('m_pword');
		
		$m_priv = $this->input->post('m_priv');

		$expdate = date('Y-m-d', strtotime("+60 days"));
		if (strlen(trim($m_lid)) === 0) {
			$prm = [
			        'uname' => $m_uname,
			        'pword' => PASSWORD_HASH($m_pword, PASSWORD_DEFAULT),
			        'empid' => $m_empid,
			        'privilege' => $m_priv,
			        'isactive' => $m_status,
			        'dateadded' => date('Y-m-d G:i:s'),
			        'lexpdate' => $expdate
				];
			$res = $this->db->insert('tbl_login', $prm);
		} else {
			$prm = [
					'empid' => $m_empid,
					'privilege' => $m_priv,
			        'isactive' => $m_status,
			        'lexpdate' => $expdate
				];
			$this->db->where('lid', $this->cryptography->decrypt($m_lid));
			$res = $this->db->update('tbl_login', $prm);
		}
		return $res;
	}


	public function savepassword($m_lid_pword, $m_pword_change)
	{
		$expdate = date('Y-m-d', strtotime("+60 days"));
		$prm = [ PASSWORD_HASH($m_pword_change, PASSWORD_DEFAULT), $expdate, $this->cryptography->decrypt($m_lid_pword) ];
		$sql = $this->sqlChangeUserPassword();
		$res = $this->db->query($sql, $prm);
		return $res;
	}

	private function sqlChangeUserPassword() {
		$sql = 'UPDATE tbl_login SET pword=?, lexpdate=? WHERE lid=?';
		return $sql;
	}
	
	/*private function sqlSaveUser() {
		$sql = 'INSERT INTO tbl_login(uname, pword, empid, email, privilege, isactive, dateadded, lexpdate) 
				VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
		return $sql;
	}*/

	/*private function sqlUpdateUser() {
		$sql = 'UPDATE tbl_login 
				SET empid=?, privilege=?, isactive=?, lexpdate=?
				WHERE lid=?';
		return $sql;
	}*/


	private function sqlEditUser() {
		$sql = 'SELECT empid, privilege, isactive
				FROM tbl_login 
				WHERE lid=?';
		return $sql;
	}
}
