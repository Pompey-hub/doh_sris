<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Resetpasswordclass_model extends CI_Model
{
	public function emailExist($email)
	{
		$prm = ['Active', $email];
		$sql = $this->sqlexistEmail();
		$res = $this->db->query($sql, $prm)->result_array();
		return $res;
	}

	private function sqlexistEmail()
	{
		$sql  = 'SELECT tlluname, tllchangepassexp
				FROM tbl_lib_login 
				WHERE tllstatus=? AND tllemail=?';
		return $sql;
	}



	public function verifyresetpasswordcode($email, $email_code)
	{
		$prm = ['Active', $email];
		$sql = $this->sqlexistEmail();
		$res = $this->db->query($sql, $prm)->result_array();

		if (count($res) * 1 == 1) {
			return ($email_code == md5($this->config->item('salt').$res[0]['tlluname'])) ? true : false;	
		} else {
			return false;
		}
	}


	/** 
	 * check if the time is valid in changing password
	 */
	public function checkupdatepasswordexptime($email)
	{
		$datetoday = strtotime(date('Y-m-d H:i:s'));

		$prm = ['Active', $email];
		$sql = $this->sqlexistEmail();
		$res = $this->db->query($sql, $prm)->result_array();

		if (isset($res[0]['tllchangepassexp']) AND strtotime($res[0]['tllchangepassexp']) > $datetoday ) {
			return true;
		} else {
			return false;
		}
	}


	public function updatepassword($email, $newpassword)
	{
		$prm = [ 'tllpword' => PASSWORD_HASH($newpassword, PASSWORD_DEFAULT) ];
		$this->db->where('tllemail', $email);
		$res = $this->db->update('tbl_lib_login', $prm);
		return $res;
	}


	public function addtllchangepassexp($email)
	{
		$prm = [ 'tllchangepassexp' => date('Y-m-d H:i:s', strtotime('+1 hour')) ];
		$this->db->where('tllemail', $email);
		$res = $this->db->update('tbl_lib_login', $prm);
		return $res;
	}

}
