<?php
defined('BASEPATH') or exit('No direct script access allowed');

class RequestVidConclass_model extends CI_Model
{
	public $priv_ = 'Client';

	function __construct()
	{
		parent::__construct();
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


	public function selservicerequest()
	{
		$prm = [$this->cryptography->decrypt($this->input->get('q'))];
		$sql = $this->sqlselservicerequest();
		$res = $this->db->query($sql, $prm)->result_array();
		return $res;
	}

	private function sqlselservicerequest() 
	{
		$sql = 'SELECT a.tllid, a.tsrclient, a.tsrreqdesc, a.tsrposition, a.tsrmobilenum, a.tsremail,
					CONCAT(e.divabbrev, "-", f.secabbrev) as office,
					m.tvcdid, m.tvcdplatform, m.tvcdtitle, m.tvcddate, m.tvcdtimestart, m.tvcdtimeend, m.tvcdusertype,
					m.tvcdnumpar, m.trrid, m.tvcdremarks,
					tat.tlsrsid
				FROM tbl_service_request a 
				LEFT JOIN home_db.tbl_login b ON a.tllid=b.lid
				LEFT JOIN db_common.tbl_emp_details c ON b.empid=c.empid AND c.dbenddate IS NULL
	            LEFT JOIN db_common.tbl_section f ON c.secid=f.secid
	            LEFT JOIn db_common.tbl_division e ON f.divid=e.divid 
	            LEFT JOIN tbl_vidcon_det m ON a.tsrid=m.tsrid
	            LEFT JOIN tbl_action_taken tat ON a.tsrid=tat.tsrid AND tat.takstatus="Active" 
				WHERE a.tsrid=?
				ORDER BY tat.takdateadded desc
				LIMIT 1';
		return $sql;
	}


	public function srsave()
	{
		$tvcdremarks = $this->input->post('tvcdremarks');

		$trrid = $this->input->post('trrid');
		$tsrclient = $this->input->post('tsrclient');
		$tsrmobilenum = $this->input->post('tsrmobilenum');
		$tsremail = $this->input->post('tsremail');
		$tsrid = $this->input->post('tsrid');
		$tsrreqdesc = '';

		$tvcdplatform = $this->input->post('tvcdplatform');
		$tvcdtitle = $this->input->post('tvcdtitle');
		$tvcddate = date('Y-m-d', strtotime($this->input->post('tvcddate')));
		$tvcdtimestart = date('H:i', strtotime($this->input->post('tvcdtimestart')));
		$tvcdtimeend = date('H:i', strtotime($this->input->post('tvcdtimeend')));
		$tvcdusertype = $this->input->post('tvcdusertype');

		$tvcdplatform = $tvcdplatform === 'OTHER' ? $this->input->post('tvcdplatform_other') : $tvcdplatform;


		$tvcpid = $this->input->post('tvcpid');
		$tvcdid = $this->input->post('tvcdid');
		$tvcpname = $this->input->post('tvcpname');
		$tvcppos = $this->input->post('tvcppos');
		$tvcpoffice = $this->input->post('tvcpoffice');
		$tvcpemail = $this->input->post('tvcpemail');


		$tvcdnumpar = $tvcdusertype == 'Host' ? $this->input->post('tvcdnumpar') : 0;


		$start = new DateTime($tvcdtimestart);
		$end = new DateTime($tvcdtimeend);
		$difference = $start ->diff($end);

		try {
			$this->db->trans_begin();
			if (strlen($tsrid) === 0) {
				$prm = [
			        'tllid' => $this->session->userdata('lid'),
			        'tsrclient' => $tsrclient,
			        'tsrmobilenum' => $tsrmobilenum,
			        'tsremail' => $tsremail,
			        'tsrreqdesc' => $tsrreqdesc,
			        'tsrdateadded' => date('Y-m-d H:i:s'),
			        'tsrstatus' => "Active",
			        'tlcid' => 5,
		        	'tlscid' => 16
				];
				$this->db->insert('tbl_service_request', $prm);

				$tsrid = $this->db->insert_id();
				$prm = [
			        'tsrid' => $tsrid,
			        'trrid' => $trrid, 
			        'tvcdplatform' => $tvcdplatform,
			        'tvcdtitle' => $tvcdtitle,
			        'tvcddate' => $tvcddate,
			        'tvcdtimestart' => $tvcdtimestart,
			        'tvcdtimeend' => $tvcdtimeend,
			        'tvcdusertype' => $tvcdusertype,
			        'tvcdnumpar' => $tvcdnumpar,
			        'tvcdremarks' => $tvcdremarks,
			        'tvcddateadded' => date('Y-m-d H:i:s')
				];
				$this->db->insert('tbl_vidcon_det', $prm);

				$tvcdid = $this->db->insert_id();

				
				// if (strtotime($difference->format('%H:%I:%S')) <= strtotime('04:00:00')) {
				// 	$this->load->model('Commonclass_model', 'common');
				// 	$this->common->autoReceived($tsrid);
				// }
			} else {
				$tsrid = $this->cryptography->decrypt($tsrid);
				$prm = [
					'tsrclient' => $tsrclient,
			        'tsrmobilenum' => $tsrmobilenum,
			        'tsremail' => $tsremail,
			        'tsrreqdesc' => $tsrreqdesc,
				];
				$this->db->where('tsrid', $tsrid);
				$this->db->update('tbl_service_request', $prm);



				// autoReceived
				// if (strtotime($difference->format('%H:%I:%S')) <= strtotime('04:00:00')) {
				// 	$this->load->model('Commonclass_model', 'common');
				// 	$this->common->autoReceived($tsrid);
				// } else {
					$prm = [
						'tsrid' => $tsrid,
				        'tlsrsid' => 4,
				        'tllid' => 0,
				        'takdateadded' => date('Y-m-d H:i:s'),
				        'takdesc' => '',
				        'takstatus' => 'Active',
				        'takremarks' => 'Edited'
					];
					$this->db->insert('tbl_action_taken', $prm);
				// }
				

				$tvcdid = $this->cryptography->decrypt($tvcdid);
				$prm = [
					'trrid' => $trrid, 
			        'tvcdplatform' => $tvcdplatform,
			        'tvcdtitle' => $tvcdtitle,
			        'tvcddate' => $tvcddate,
			        'tvcdtimestart' => $tvcdtimestart,
			        'tvcdtimeend' => $tvcdtimeend,
			        'tvcdusertype' => $tvcdusertype,
			        'tvcdnumpar' => $tvcdnumpar,
			        'tvcdremarks' => $tvcdremarks
				];
				$this->db->where('tvcdid', $tvcdid);
				$this->db->update('tbl_vidcon_det', $prm);
			}


			$prm = [ 'tvcpstatus' => 'Inactive' ];
			$this->db->where('tvcdid', $tvcdid);
			$this->db->update('tbl_vidcon_participant', $prm);	#07132020


			$len = count($tvcpid);
			for ($i=0; $i < $len; $i++) { 
				if (strlen(trim($tvcpid[$i]))===0) {
					$prm = [
				        'tvcdid' => $tvcdid,
				        'tvcpname' => $tvcpname[$i],
				        'tvcppos' => $tvcppos[$i],
				        'tvcpoffice' => $tvcpoffice[$i],
				        'tvcpemail' => $tvcpemail[$i],
				        'tvcpstatus' => 'Active'
					];
					$this->db->insert('tbl_vidcon_participant', $prm);
				} else {
					$prm = [
				        'tvcpname' => $tvcpname[$i],
				        'tvcppos' => $tvcppos[$i],
				        'tvcpoffice' => $tvcpoffice[$i],
				        'tvcpemail' => $tvcpemail[$i],
				        'tvcpstatus' => 'Active'
					];
					$this->db->where('tvcpid', $tvcpid[$i]);
					$this->db->update('tbl_vidcon_participant', $prm);
				}
			}

			$this->db->trans_commit();
			$res = true;
		} catch (Exception $e) {
			$this->db->trans_rollback();
			$res = false;
		}


		return $res;
	}


	public function selvidconparticipant($tvcdid)
	{
		$prm = [$tvcdid, 'Active'];
		$sql = $this->sqlselvidconparticipant();
		$res = $this->db->query($sql, $prm)->result_array();
		return $res;
	}

	private function sqlselvidconparticipant()
	{
		$sql = 'SELECT a.* 
				FROM tbl_vidcon_participant a
				WHERE a.tvcdid=? AND a.tvcpstatus=?';
		return $sql;
	}
}


