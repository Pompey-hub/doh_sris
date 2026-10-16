<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Libraryclass_model extends CI_Model
{
	public function sqldtcategory()
	{
		$sql = 'SELECT a.*
				FROM tbl_lib_category a 
				WHERE a.tlccat LIKE ?';
		return $sql;
	}

	public function savecategory()
	{
		$tlcid = trim($this->input->post('tlcid'));

		$prm = [
			'tlccat' => trim($this->input->post('tlccat')),
			'tlcstatus' => trim($this->input->post('tlcstatus'))
		];

		if (strlen($tlcid) === 0) {
			$res = $this->db->insert('tbl_lib_category', $prm);
		} else {
			$this->db->where('tlcid', $tlcid);
			$res = $this->db->update('tbl_lib_category', $prm);
		}

		return $res;
	}

	public function editcategory()
	{
		$this->db->select('a.*');
		$this->db->from('tbl_lib_category a');
		$this->db->where('a.tlcid', $this->input->get('tlcid'));
		return $this->db->get()->result_array();
	}

	/*start subcategory*/
	public function sqldtsubcategory()
	{
		$sql = '
        SELECT
            a.*,
            b.tlccat,
            c.tlsdesc,
            CONCAT(d.tlltype, " - ", d.tllstype) AS tlltype,
            d.tlltarget AS tlltarget
        FROM tbl_lib_subcategory a
        LEFT JOIN tbl_lib_category b
            ON a.tlcid = b.tlcid
        LEFT JOIN tbl_lib_services c
            ON a.tlsid = c.tlsid
        LEFT JOIN tbl_lib_level d
            ON a.tll_id = d.tll_id
        WHERE
            a.tlcproblem LIKE ?
            OR a.tlcproblem_desc LIKE ?
            OR b.tlccat LIKE ?
    ';

		return $sql;
	}

	public function savesubcategory()
	{
		$tlscid = trim($this->input->post('tlscid'));

		$prm = [
			'tlcid' => trim($this->input->post('tlcid')),
			'tlcproblem' => trim($this->input->post('tlcproblem')),
			'tlcproblem_desc' => trim($this->input->post('tlcproblem_desc')),
			'tlscstatus' => trim($this->input->post('tlscstatus')),
			'tlsid' => trim($this->input->post('tlsid')),
			'tll_id' => trim($this->input->post('tll_id'))
		];

		if (strlen($tlscid) === 0) {
			$res = $this->db->insert('tbl_lib_subcategory', $prm);
		} else {
			$this->db->where('tlscid', $tlscid);
			$res = $this->db->update('tbl_lib_subcategory', $prm);
		}

		return $res;
	}

	public function editsubcategory()
	{
		$this->db->select('a.*');
		$this->db->from('tbl_lib_subcategory a');
		$this->db->where('a.tlscid', $this->input->get('tlscid'));
		return $this->db->get()->result_array();
	}
}
