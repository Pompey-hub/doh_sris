<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Settingsclass_model extends CI_Model
{
	public function sraccountdetails()
	{
		$prm = [$this->cryptography->decrypt($this->session->userdata('tllid'))];
		$sql = $this->sqlsraccountdetails();
		$res = $this->db->query($sql, $prm)->result_array();
		return $res;
	}

	public function srchangepasssave($newpassword)
	{
		$prm = [ 'tllpword' => PASSWORD_HASH($newpassword, PASSWORD_DEFAULT) ];
		$this->db->where('tllid', $this->cryptography->decrypt($this->session->userdata('tllid')));
		$res = $this->db->update('tbl_lib_login', $prm);
		return $res;
	}

	/**
	 * check if email is already exist except for this user login
	 *
	 */
	public function isemailexist($email)
	{
		$prm = [$this->cryptography->decrypt($this->session->userdata('tllid')), $email];
		$sql = $this->sqlisemailexist();
		$res = $this->db->query($sql, $prm)->result_array();
		return isset($res[0]['tllemail']) ? true : false;
	}

	private function sqlisemailexist()
	{
		$sql  = 'SELECT tllemail
				FROM tbl_lib_login 
				WHERE tllid!=? AND tllemail=?';
		return $sql;
	}


	/**
	 * save contact details
	 */
	public function srchangecontactsave($email, $number)
	{
		$prm = [ 'tllnumber' => $number, 'tllemail' => $email];
		$this->db->where('tllid', $this->cryptography->decrypt($this->session->userdata('tllid')));
		$res = $this->db->update('tbl_lib_login', $prm);
		return $res;
	}



	private function sqlsraccountdetails()
	{
		$sql = 'SELECT a.tllnumber, a.tllemail, a.tllfactype, a.tlluname,
					CONCAT(d.divabbrev, "-",c.secabbrev) as office,
					CONCAT(e.fname, " ", LEFT(e.mname, 1), ". ", e.lname, " ", e.suffix ) as fullname
				FROM tbl_lib_login a 
				/*LEFT JOIN tbl_lib_detail_emp b ON a.tllid=b.tllid*/
				LEFT JOIN db_common.tbl_section c ON a.secid=c.secid 
				LEFT JOIN db_common.tbl_division d ON c.divid=d.divid 
				LEFT JOIN db_common.tbl_employee e ON a.empid=e.empid 
				/*LEFT JOIN db_common.tbl_position f ON b.dbpid=f.dbpid*/
				WHERE a.tllid=?';
		return $sql;
	}
}


