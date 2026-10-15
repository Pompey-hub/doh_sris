







	public function sqldtrequest($stat)
	{
		#var_dump($stat);
		if ($stat === 'accom') {
			$sql_stat = ' AND tlsrsid IN (3, 6)  ';
			if ($this->priv_ == 'Client') {
				$sql_stat .= ' AND a.tllid=? ';
			}
		} elseif ($stat === 'def') {
			$sql_stat = ' AND (tlsrsid NOT IN (3, 6) OR tlsrsid IS NULL) ';
		} else { 	/*my request*/
			$sql_stat = ' AND ((tlsrsid NOT IN (3, 6) OR tlsrsid IS NULL) AND a.tllid=?) ';
		}


		$sql = 'SELECT 
					a.tllid, a.tsrid, a.tsrid as tsrid_enc, a.cssid, a.tsrreqdesc, a.tsrstatus, a.tsrdateadded, a.tsrdateadded as tsrdateadded_time, a.tsrconforme, 
				    a.tsrmobilenum, CONCAT(e.divabbrev, "-", f.secabbrev) as office, 
				    IF(i.tlsrsdesc IS NULL, "Pending", substring_index(group_concat(cast(i.tlsrsdesc as CHAR) order by h.takdateadded desc), ",", 1 )) as tlsrsdesc, 
				    substring_index(group_concat(cast(CONCAT(LEFT(k.fname, 1), ". ", k.lname) as CHAR) order by h.takdateadded desc), ",", 1 ) as officer,
				    substring_index(group_concat(cast(i.tlsrsid as CHAR) order by h.takdateadded desc), ",", 1 ) as tlsrsid,
				    substring_index(group_concat(cast(i.tlsrsclass as CHAR) order by h.takdateadded desc), ",", 1 ) as tlsrsclass,
				    substring_index(group_concat(cast(h.takremarks as CHAR) order by h.takdateadded desc), ",", 1 ) as takremarks,
				    e.divabbrev, f.secabbrev, m.tlssdesc,

				    UPPER(
						CONCAT(
							x.fname, 
							" ", 
							LEFT(x.mname, 1), IF(x.mname IS NULL OR x.mname="", "", ". " ), 
							x.lname, 
							IF(x.suffix IS NULL OR x.suffix="", "", " "), 
							x.suffix, 
							IF(x.extname IS NULL OR x.extname="", "", CONCAT(", ", x.extname)) 
						)
					) as tsrclient,
					b.uname, b.uname AS myuname
			FROM tbl_service_request a 
			LEFT JOIN db_common.tbl_employee x ON a.tsrclient=x.empid 
			LEFT JOIN home_db.tbl_login b ON a.tllid=b.lid 
			LEFT JOIN db_common.tbl_emp_details bb ON b.empid=bb.empid 
			
            LEFT JOIN db_common.tbl_section f ON bb.secid=f.secid
            LEFT JOIn db_common.tbl_division e ON f.divid=e.divid 

            LEFT JOIN tbl_action_taken h ON a.tsrid=h.tsrid AND h.takstatus="Active" 
            LEFT JOIN tbl_lib_srstatus i ON h.tlsrsid=i.tlsrsid
            LEFT JOIN home_db.tbl_login j ON h.tllid=j.lid
            LEFT JOIN db_common.tbl_emp_details jj ON j.empid=jj.empid 

            LEFT JOIN db_common.tbl_employee k ON j.empid=k.empid

            LEFT JOIN tbl_lib_services m ON a.tlsid=m.tlsid


			GROUP BY a.tsrid 
			HAVING ( a.tsrdateadded LIKE ? OR tsrclient LIKE ? OR 
				e.divabbrev LIKE ? OR f.secabbrev LIKE ? OR a.tsrreqdesc LIKE ?) AND a.tsrstatus="Active" ' . $sql_stat;
		return $sql;
	}


	

	


	/* update and set conforme */
	public function conforme()
	{
		$prm = [
			 'tsrconforme' => 'Confirmed'
		];
		$this->db->where('tsrid', $this->cryptography->decrypt($this->input->post('xtsrid_c')));
		$res = $this->db->update('tbl_service_request', $prm);
		return $res;
	}

	/* check if the client has approved the conforme btn*/
	public function srcheckconforme()
	{
		$prm = ['Active', $this->session->userdata('lid'), 'Confirmed', 3, 6];
		$sql = $this->sqlsrcheckconforme();
		$res = $this->db->query($sql, $prm)->result_array();
		return $res;
	}

	private function sqlsrcheckconforme()
	{
		$sql = 'SELECT COUNT(a.tsrid) as cnt, 
					substring_index(group_concat(cast(c.tlsrsid as CHAR) order by b.takdateadded desc), ",", 1 ) as tlsrsid,
					a.tsrstatus, a.tllid, a.tsrconforme
				FROM tbl_service_request a 
				LEFT JOIN tbl_action_taken b ON a.tsrid=b.tsrid AND b.takstatus="Active" 
            	LEFT JOIN tbl_lib_srstatus c ON b.tlsrsid=c.tlsrsid
				GROUP BY a.tsrid
				HAVING a.tsrstatus=? AND a.tllid=? AND (a.tsrconforme IS NULL OR a.tsrconforme!=?) AND (tlsrsid NOT IN (?, ?) OR tlsrsid IS NULL)';
		return $sql;
	}


	/* position and division-section*/
	public function selsrdetails()
	{
		$prm = ['Active', $this->session->userdata('lid')];
		$sql = $this->sqlselsrdetails();
		$res = $this->db->query($sql, $prm)->result_array();
		return $res;
	}

	private function sqlselsrdetails()
	{
		$sql = 'SELECT d.tldeid, CONCAT(e.divabbrev, "-", f.secabbrev) as office, g.dbposition
				FROM tbl_lib_detail_emp d
	            LEFT JOIN db_common.tbl_section f ON d.secid=f.secid
	            LEFT JOIn db_common.tbl_division e ON f.divid=e.divid 
	            LEFT JOIN db_common.tbl_position g ON d.dbpid=g.dbpid 
				WHERE d.tldestatus=? AND d.tllid=?';
		return $sql;
	}

	public function srcancel()
	{
		$prm = ['tsrstatus' => 'Inactive'];
		$this->db->where('tsrid', $this->cryptography->decrypt($this->input->get('xtsrid')));
		$res = $this->db->update('tbl_service_request', $prm);
		return $res;
	}


	public function srseleditactiontaken()
	{
		$prm = [$this->cryptography->decrypt($this->input->get('xtakid'))];
		$sql = 'SELECT a.takdesc, b.tlsid, b.tlssys
				FROM tbl_action_taken a 
				LEFT JOIN tbl_service_request b ON a.tsrid=b.tsrid 
				WHERE a.takid=?';
		$res = $this->db->query($sql, $prm)->result_array();
		return $res;
	}


	public function sreditactiontaken()
	{
		$prm = [
				$this->input->get('takdesc'), 
				$this->input->get('extlsid'), 
				$this->input->get('extlssys'),
				$this->cryptography->decrypt($this->input->get('xtakid'))
			];
		#$this->db->where('takid', $this->cryptography->decrypt($this->input->post('xtakid')));
		#$res = $this->db->update('tbl_action_taken', $prm);
		$sql = 'UPDATE tbl_action_taken a 
				LEFT JOIN tbl_service_request b ON a.tsrid=b.tsrid 
				SET a.takdesc=?, b.tlsid=?, b.tlssys=?
				WHERE a.takid=?';

		$res = $this->db->query($sql, $prm);
		return $res;
	}


	public function srlogstatus()
	{
		$sql_act_tak = ($this->priv_ == 'Client') ? ' a.takstatus="Active" AND ' : '';

		$prm = [$this->cryptography->decrypt($this->input->get('xtsrid'))];
		$sql = 'SELECT a.takid, a.takdateadded as taktimeadded, a.takdateadded, a.takdesc, a.takstatus, a.takremarks, b.tlsrsdesc,
					CONCAT(LEFT(d.fname, 1), ". ", d.lname, " ", d.suffix ) as officer, e.tsrreqdesc
				FROM tbl_action_taken a 
				LEFT JOIN tbl_lib_srstatus b ON a.tlsrsid=b.tlsrsid 
				LEFT JOIN home_db.tbl_login c ON a.tllid=c.lid 
				LEFT JOIN db_common.tbl_employee d ON c.empid=d.empid

				LEFT JOIN tbl_service_request e ON a.tsrid=e.tsrid

				WHERE ' . $sql_act_tak . ' a.tsrid=? ORDER BY a.takdateadded ASC';
		$res = $this->db->query($sql, $prm)->result_array();
		return $res;
	}

	public function srtsrreqdesc($xtsrid)
	{
		$prm = [$xtsrid];
		$sql = 'SELECT a.tsrreqdesc
				FROM tbl_service_request a
				WHERE a.tsrid=?';
		$res = $this->db->query($sql, $prm)->result_array();

		return isset($res[0]['tsrreqdesc']) ? html_escape($res[0]['tsrreqdesc'], ENT_QUOTES) : '';
	}
	
	public function srtbllibsrstatus()
	{
		$prm = [$this->cryptography->decrypt($this->input->get('xtsrid'))];
		$sql = 'SELECT GROUP_CONCAT(b.tlsrsdesc) as tlsrsdesc, a.takstatus, GROUP_CONCAT(b.tlsrorder) as tlsrorder
				FROM tbl_action_taken a 
				LEFT JOIN tbl_lib_srstatus b ON a.tlsrsid=b.tlsrsid 
				GROUP BY a.tsrid
				HAVING a.tsrid=? '; /* AND a.takstatus="Inactive" */
		$res = $this->db->query($sql, $prm)->result_array();
		return $res;

	}