<?php 
defined('BASEPATH') or exit('No direct script access allowed');

class Commonclass_model extends CI_model 
{
	public function changeDateFormat($mdate)
	{
		$myDateTime = DateTime::createFromFormat('M d, Y', $mdate);
		$newDateString = $myDateTime->format('Y-m-d');
		return $newDateString;
	}

	public function displaychangeDateFormat($mdate)
	{
		$newDateString = '';
		if (trim($mdate) != '0000-00-00' && trim($mdate) != null) {
			$myDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $mdate);
			$newDateString = $myDateTime->format('M d, Y');
		}
		return $newDateString;
	}



	
	/** datatable 
	 * @param array $col
	 * @param str $order = $col[$_GET['order']['0']['column']]
	 * @param str $dir = $_GET['order']['0']['dir'];
	 * @param array $prm
	 * @param str $sql_totalrecord
	 * @param int $start
	 * @param int $length
	 */
	public function datatablefunc($col, $order, $dir, $prm, $sql_tr, $start, $length)
	{
		$res_tr = $this->db->query($sql_tr, $prm)->result_array();
		$prm[] = ($start * 1);
		$prm[] = ($length * 1);
		$sql_limit = $sql_tr . ' ORDER BY ' . $order . ' ' . $dir . ' LIMIT ?, ?';
		$res_limit = $this->db->query($sql_limit, $prm)->result_array();
		return ['limit'=>$res_limit, 'totalrecord'=>count($res_tr)];
	}


	/*public function listsrstatus()
	{
		$prm = ['Active'];
		$sql = 'SELECT a.tlsrsid, a.tlsrsdesc, a.tlsrorder, a.tlsrsid AS tlsrsidnew
				FROM tbl_lib_srstatus a
				WHERE tlsrsstatus=? 
				ORDER BY a.tlsrorder ASC';
		$res = $this->db->query($sql, $prm)->result_array();
		return $res;
	}*/

	public function listsrstatus()
	{
		#10232020
		if ($this->session->userdata('privilege') == '99') {
        	$this->priv_ = 'Superadmin';
	    } elseif (in_array('SRIS', $this->session->userdata('tlarsys')) ) {
	        $exp_sys = $this->session->userdata('tlarsys'); 
            $index = array_search('SRIS', $exp_sys);

            $exp_priv = $this->session->userdata('tlarpriv'); 
            $this->priv_ = $exp_priv[$index];
	    } else {
	        $this->priv_ = 'Client';
	    }

	    $tlsrspriv = $this->priv_ === 'HRDU-Admin' ? 'HRDU-Admin' : 'Admin';

		$prm = ['Active', $tlsrspriv];
		$sql = 'SELECT a.tlsrsid, a.tlsrsdesc, a.tlsrorder, a.tlsrsid AS tlsrsidnew
				FROM tbl_lib_srstatus a
				WHERE tlsrsstatus=? AND tlsrspriv=?
				ORDER BY a.tlsrorder ASC';
		$res = $this->db->query($sql, $prm)->result_array();
		return $res;
	}
	


	public function employee()
	{
		$this->db->select('a.empid, a.prefix, a.isactive, a.fpid, a.empdateadded, a.emplid, 
			' . $this->cryptography->mysql_aes_decrypt('a.fname') . ' AS fname,
			' . $this->cryptography->mysql_aes_decrypt('a.mname') . ' AS mname,
			' . $this->cryptography->mysql_aes_decrypt('a.lname') . ' AS lname,
			' . $this->cryptography->mysql_aes_decrypt('a.suffix') . ' AS suffix,
			' . $this->cryptography->mysql_aes_decrypt('a.extname') . ' AS extname');
		$this->db->from('db_common.tbl_employee a');
		$this->db->where('a.isactive', 1);
		$this->db->order_by($this->cryptography->mysql_aes_decrypt('a.fname') . ' ASC');
		$this->db->order_by($this->cryptography->mysql_aes_decrypt('a.lname') . ' ASC');
		return $this->db->get()->result_array();
	}

	public function employeepersection()
	{
		$this->db->select('a.empid, a.prefix, a.isactive, a.fpid, a.empdateadded, a.emplid, 
			' . $this->cryptography->mysql_aes_decrypt('a.fname') . ' AS fname,
			' . $this->cryptography->mysql_aes_decrypt('a.mname') . ' AS mname,
			' . $this->cryptography->mysql_aes_decrypt('a.lname') . ' AS lname,
			' . $this->cryptography->mysql_aes_decrypt('a.suffix') . ' AS suffix,
			' . $this->cryptography->mysql_aes_decrypt('a.extname') . ' AS extname');
		$this->db->from('db_common.tbl_employee a');
		$this->db->join('db_common.tbl_emp_details c', 'a.empid=c.empid AND c.dbstatus=0 AND c.dbenddate IS NULL', 'left');
		$this->db->join('db_common.tbl_section d', 'c.secid=d.secid', 'left');
		$this->db->where('a.isactive', 1);
		$this->db->where('d.secabbrev', $this->session->userdata('secabbrev'));
		$this->db->where('d.secabbrev IS NOT NULL'); #05112026
		return $this->db->get()->result_array();
	}


	public function assistingoffice($secabbrev='')
	{
		$this->db->select('a.cdcs_secid, a.secid, div_.divabbrev, sec.secabbrev');
		$this->db->from('conn_db_common_section a');
		$this->db->join('db_common.tbl_section sec', 'a.secid=sec.secid', 'left');
		$this->db->join('db_common.tbl_division div_', 'sec.divid=div_.divid', 'left');
		$this->db->where('a.status', 'Active');
		if (strlen(trim($secabbrev)) > 0) {
			$this->db->where('sec.secabbrev', $secabbrev);
		}
		$this->db->order_by('div_.divabbrev', 'ASC');
		$this->db->order_by('sec.secabbrev', 'ASC');
		$res = $this->db->get()->result_array();
		return $res;
	}

	public function section($secabbrev='')
	{
		$this->db->select('sec.*');
		$this->db->from('db_common.tbl_section sec');
		if (strlen(trim($secabbrev)) > 0) {
			$this->db->where('sec.secabbrev', $secabbrev);
		}
		$res = $this->db->get()->result_array();
		return $res;
	}



	



	public function office()
	{
		$prm = [1, 1];
		$sql = $this->sqloffice();
		$res = $this->db->query($sql, $prm)->result_array();
		return $res;
	}

	public function position()
	{
		$prm = [1];
		$sql = $this->sqlposition();
		$res = $this->db->query($sql, $prm)->result_array();
		return $res;
	}


	public function services()
	{
		$this->db->select('a.*');
		$this->db->from('tbl_lib_services a');
		$this->db->join('db_common.tbl_section sec', 'a.secid=sec.secid', 'left');
		$this->db->where('a.tlsstatus', 'Active');
		$this->db->where('sec.secabbrev', $this->session->userdata('secabbrev'));
		$this->db->order_by('a.tlssdesc', 'ASC');
		$res = $this->db->get()->result_array();
		return $res;
	}

	public function levels()
	{
		$this->db->select('a.*');
		$this->db->from('tbl_lib_level a');
		$this->db->where('a.tllstatus', 'ACTIVE');
		$this->db->order_by('a.tlltype', 'ASC');
		$res = $this->db->get()->result_array();
		return $res;
	}


	private function sqlposition()
	{
		$sql = 'SELECT a.*
				FROM db_common.tbl_position a
				WHERE a.dbpstatus=? 
				ORDER BY a.dbposition ASC';
		return $sql;
	}


	private function sqloffice()
	{
		$sql = 'SELECT a.divid, a.divabbrev, b.secid, b.secabbrev
				FROM db_common.tbl_division a
				LEFT JOIN db_common.tbl_section b ON a.divid=b.divid
				WHERE a.isactive=? AND b.isactive=?
				ORDER BY a.divabbrev ASC, b.secabbrev ASC';
		return $sql;
	}


	


	public function encrypt_decrypt($action, $string) {
	    $output = false;
	    $encrypt_method = "AES-256-CBC";
	    $secret_key = 'doh_evchd_key';
	    $secret_iv = 'doh_evchd_iv';
	    // hash
	    $key = hash('sha256', $secret_key);
	    
	    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	    $iv = substr(hash('sha256', $secret_iv), 0, 16);
	    if ( $action == 'encrypt' ) {
	        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
	        $output = base64_encode($output);
	    } else if( $action == 'decrypt' ) {
	        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
	    }
	    return $output;
	}


	#07072020
	public function selactionofficer($offid)
	{
		$prm = ['Active', 'SRIS'];
		$sql = $this->sqlselactionofficer();

		if ($offid != '-1') {
			$prm[] = $offid;
			$sql .= ' AND a.lid=? ';
		}
		$res = $this->db->query($sql, $prm)->result_array();
		
		$len = count($res);
		for ($i=0; $i < $len; $i++) { 
			$res[$i]['fullname'] =  $this->concat_fullname($res[$i]['fname'], $res[$i]['mname'], $res[$i]['lname'], $res[$i]['suffix'], $res[$i]['extname']);
		}
		return $res;
	}
	private function sqlselactionofficer()
	{
		// ' . $this->cryptography->mysql_aes_decrypt('b.lname') . ' AS lname,
		$sql = 'SELECT a.lid, 
					' . $this->cryptography->mysql_aes_decrypt('c.fname') . ' AS fname,
					' . $this->cryptography->mysql_aes_decrypt('c.mname') . ' AS mname,
					' . $this->cryptography->mysql_aes_decrypt('c.lname') . ' AS lname,
					' . $this->cryptography->mysql_aes_decrypt('c.suffix') . ' AS suffix,
					' . $this->cryptography->mysql_aes_decrypt('c.extname') . ' AS extname,
					"" AS fullname,
					e.dbposition
				FROM home_db.tbl_login_access_rights a 
				LEFT JOIN home_db.tbl_login b ON a.lid=b.lid 
				LEFT JOIN db_common.tbl_employee c ON b.empid=c.empid 
				LEFT JOIN db_common.tbl_emp_details d ON c.empid=d.empid 
				LEFT JOIN db_common.tbl_position e ON d.dbpid=e.dbpid
				WHERE a.tlarstatus=? AND a.tlarsys=?';
		return $sql;
	}


	#02022021
	public function record()
	{
		$this->db->select('trrid, trrname');
		//$this->db->order_by('brgyDesc', 'ASC');
		$this->db->where('trrstatus', 'Active');
		return $this->db->get('tbl_vidcon_record_request')->result_array();
	}


	// 08162023 auto receive TA
	// public function autoReceived($tsrid_)
	// {
	// 	$prm = [
	// 		'tsrid' => $tsrid_,
	//         'tlsrsid' => 1,
	//         'tllid' => 0,
	//         'takdateadded' => date('Y-m-d H:i:s', strtotime('+1 sec')),
	//         'takdesc' => '',
	//         'takstatus' => 'Active',
	//         'takremarks' => 'Thank you for your service request. This is an auto-system-generated response. Our representative will attend to your request shortly.'
	// 	];
	// 	return $this->db->insert('tbl_action_taken', $prm);
	// }

	#08162023
	public function category()
	{
		$this->db->select('*');
		$this->db->where('tlcstatus', 'ACTIVE');
		$this->db->order_by('tlcorder', 'ASC');
		return $this->db->get('tbl_lib_category')->result_array();
	}

	public function subcategory($tlcid)
	{
		$this->db->select('*');
		$this->db->where('tlcid', $tlcid);
		$this->db->where('tlscstatus', 'ACTIVE');
		$this->db->order_by('tlcproblem', 'ASC');
		return $this->db->get('tbl_lib_subcategory')->result_array();
	}

	public function actiontakensubcategory($tlsid)
	{
		$this->db->select('a.*, b.tlccat');
		$this->db->from('tbl_lib_subcategory a');
		$this->db->join('tbl_lib_category b', 'a.tlcid=b.tlcid', 'left');
		$this->db->where('a.tlsid', $tlsid);
		$this->db->where('a.tlscstatus', 'ACTIVE');
		$this->db->order_by('b.tlccat', 'ASC');
		$this->db->order_by('a.tlcproblem', 'ASC');
		return $this->db->get()->result_array();
	}

	/** concat fullname 
	 */
	public function concat_fullname($fname, $mname, $lname, $suffix, $extname)
	{
		return $fname . 
		' ' . 
		(strlen(trim($mname)) > 0 ? substr($mname, 0, 1) . '. ' : ' ') .
		$lname . 
		' ' . 
		(strlen(trim($suffix)) > 0 ? $suffix . ' ' : '') .
		$extname;
	}

	public function concat_fullname_last($fname, $mname, $lname, $suffix, $extname)
	{
		return $lname . 
			', ' . 
			$lname . 
			' ' . 
			(strlen(trim($mname)) > 0 ? substr($mname, 0, 1) . '. ' : ' ') .
			(strlen(trim($suffix)) > 0 ? $suffix . ' ' : '') .
			$extname;
	}

	public function group_concat_max_len()
    	{
        	$this->db->query("SET SESSION group_concat_max_len = 10000");
    	}
}
