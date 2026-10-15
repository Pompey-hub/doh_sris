<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Requestclass_model extends CI_Model
{
	public $priv_ = 'Client';

	function __construct()
	{
		parent::__construct();
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


	public function sqldtrequest($stat)
	{
		if ($stat === 'accom') {

			$sql_stat = ' AND tlsrsid IN (3, 6) ';

			if ($this->priv_ == 'Client') {

				$sql_stat .= ' AND a.tllid=? ';
			} elseif (
				$this->priv_ == 'Admin' ||
				$this->priv_ == 'Superadmin'
			) {

				$sql_stat .= ' AND (a.tllid=? OR assoff.secabbrev=?) ';
			}
		} elseif ($stat === 'def') {

			$sql_stat = ' AND (tlsrsid NOT IN (3, 6) OR tlsrsid IS NULL)
                      AND assoff.secabbrev=? ';
		} elseif ($stat === 'assigned') {

			/*
         * ASSIGNED TO ME
         *
         * The current active assignment is represented by
         * latest_ass.
         *
         * assigned_to is exposed in SELECT and therefore
         * filtered using HAVING below.
         */
			$sql_stat = ' AND assigned_to=? ';
		} else {

			/*
         * MY REQUEST
         */
			$sql_stat = ' AND ((tlsrsid NOT IN (3, 6) OR tlsrsid IS NULL)
                           AND a.tllid=?) ';
		}


		$sql = 'SELECT

                NULL as ticketnum,

                a.tllid,
                a.tsrid,
                a.tsrid as tsrid_enc,
                a.cssid,
                a.tsrreqdesc,
                a.tsrstatus,
                a.tsrdateadded,
                a.tsrdateadded as tsrdateadded_time,
                a.tsrconforme,
                a.tsrmobilenum,


                /* =====================================================
                   ASSIGNED OFFICE
                   ===================================================== */

                CONCAT(
                    e.divabbrev,
                    "-",
                    f.secabbrev
                ) as office,


                /* =====================================================
                   CURRENT REQUEST STATUS
                   
                   IMPORTANT:
                   Reassigned is NOT treated as the current
                   workflow status.

                   The action query below only returns the latest
                   NON-REASSIGNED action.

                   Example:

                       Received
                       Reassigned
                       For Action

                   Main table will show:

                       For Action

                   The Reassigned record remains in
                   tbl_action_taken for the Action Taken Log.
                   ===================================================== */

                IF(
                    i.tlsrsdesc IS NULL,
                    "Pending",
                    i.tlsrsdesc
                ) as tlsrsdesc,


                /* =====================================================
                   CURRENT A. OFFICER
                   
                   Comes from the ACTIVE assignment.
                   ===================================================== */

                IF(
                    ' . $this->cryptography->mysql_aes_decrypt('ass_emp.fname') . ' IS NULL,

                    "-",

                    CAST(
                        CONCAT(
                            LEFT(
                                ' . $this->cryptography->mysql_aes_decrypt('ass_emp.fname') . ',
                                1
                            ),
                            ".",
                            ' . $this->cryptography->mysql_aes_decrypt('ass_emp.lname') . '
                        )
                        AS CHAR
                    )
                ) AS officer,


                /* =====================================================
                   CURRENT ASSIGNED EMPLOYEE ID
                   
                   Used by the "Assigned to me" filter.
                   ===================================================== */

                MAX(ass.assigned_to) AS assigned_to,


                /* =====================================================
                   CURRENT ASSIGNMENT DATE
                   ===================================================== */

                MAX(ass.assigned_date) AS assigned_date,


                /* =====================================================
                   ASSIGNED BY
                   
                   Comes from the CURRENT assignment.
                   ===================================================== */

                IF(
                    ' . $this->cryptography->mysql_aes_decrypt('assby_emp.fname') . ' IS NULL,

                    "-",

                    CAST(
                        CONCAT(
                            LEFT(
                                ' . $this->cryptography->mysql_aes_decrypt('assby_emp.fname') . ',
                                1
                            ),
                            ".",
                            ' . $this->cryptography->mysql_aes_decrypt('assby_emp.lname') . '
                        )
                        AS CHAR
                    )
                ) AS assigned_by,


                /* =====================================================
                   OLD A. OFFICER
                   
                   This comes from the assignment immediately BEFORE
                   the current active assignment.

                   We do NOT overwrite the old assignment.
                   The old assignment remains in the table as
                   status = Inactive.
                   ===================================================== */

                IF(
                    ' . $this->cryptography->mysql_aes_decrypt('old_ass_emp.fname') . ' IS NULL,

                    "-",

                    CAST(
                        CONCAT(
                            LEFT(
                                ' . $this->cryptography->mysql_aes_decrypt('old_ass_emp.fname') . ',
                                1
                            ),
                            ".",
                            ' . $this->cryptography->mysql_aes_decrypt('old_ass_emp.lname') . '
                        )
                        AS CHAR
                    )
                ) AS old_officer,


                /* =====================================================
                   OLD ASSIGNED DATE
                   
                   This is the assigned_date of the previous
                   assignment record.
                   ===================================================== */

                MAX(old_ass.assigned_date) AS old_assigned_date,


                /* =====================================================
                   LATEST NON-REASSIGNED ACTION
                   
                   Reassigned is deliberately excluded from the
                   main request status.

                   It will still exist in tbl_action_taken and
                   will therefore remain available to the
                   Action Taken Log.
                   ===================================================== */

                i.tlsrsid AS tlsrsid,

                i.tlsrsclass AS tlsrsclass,

                i.tlsrorder AS tlsrorder,

                h.takid AS takid,

                h.takremarks AS takremarks,

                h.takdateadded AS takdateadded_dis,


                /* =====================================================
                   OFFICE INFORMATION
                   ===================================================== */

                e.divabbrev,
                f.secabbrev,
                m.tlssdesc,

                NULL as hrefroute,


                /* =====================================================
                   REQUEST CLIENT INFORMATION
                   ===================================================== */

                ' . $this->cryptography->mysql_aes_decrypt('x.fname') . ' AS fname,

                ' . $this->cryptography->mysql_aes_decrypt('x.mname') . ' AS mname,

                ' . $this->cryptography->mysql_aes_decrypt('x.lname') . ' AS lname,

                ' . $this->cryptography->mysql_aes_decrypt('x.suffix') . ' AS suffix,

                ' . $this->cryptography->mysql_aes_decrypt('x.extname') . ' AS extname,

                ' . $this->cryptography->mysql_aes_decrypt('b.uname') . ' AS uname,


                /* =====================================================
                   CLIENT / USER
                   ===================================================== */

                "" AS tsrclient,

                "" AS myuname,


                /* =====================================================
                   VIDEO CONFERENCE
                   ===================================================== */

                n.tvcdplatform,
                n.tvcdid,
                n.tvcddate,
                n.tvcdtimestart,
                n.tvcdtimeend,
                n.tvcdtitle,
                n.tvcdremarks,


                NULL AS isdisabled,


                /* =====================================================
                   ASSIGNED OFFICE
                   ===================================================== */

                assoff.secabbrev AS assoff_secabbrev,

                assoff_div.divabbrev AS assoff_divabbrev,


                /* =====================================================
                   CATEGORY / SUBCATEGORY
                   ===================================================== */

                IF(
                    subcat.tlcproblem IS NULL,
                    "",
                    subcat.tlcproblem
                ) AS tlcproblem,

                IF(
                    cat.tlccat IS NULL,
                    "",
                    cat.tlccat
                ) AS tlccat


            FROM tbl_service_request a


            /* ==========================================================
               REQUEST CLIENT
               ========================================================== */

            LEFT JOIN db_common.tbl_employee x
                ON a.tsrclient=x.empid


            /* ==========================================================
               REQUEST OWNER / USER
               ========================================================== */

            LEFT JOIN home_db.tbl_login b
                ON a.tllid=b.lid

            LEFT JOIN db_common.tbl_emp_details bb
                ON b.empid=bb.empid
                AND bb.dbenddate IS NULL

            LEFT JOIN db_common.tbl_section f
                ON bb.secid=f.secid

            LEFT JOIN db_common.tbl_division e
                ON f.divid=e.divid


            /* ==========================================================
               LATEST NON-REASSIGNED ACTION
               
               This is the key change for the main table.

               We find the latest ACTIVE action whose status is NOT
               "Reassigned".

               Therefore:

                   Received
                   Reassigned

               will still show:

                   Received

               And:

                   Received
                   Reassigned
                   For Action

               will show:

                   For Action

               The Reassigned row itself remains untouched.
               ========================================================== */

            LEFT JOIN tbl_action_taken h
                ON h.takid = (

                    SELECT MAX(h2.takid)

                    FROM tbl_action_taken h2

                    LEFT JOIN tbl_lib_srstatus i2
                        ON h2.tlsrsid=i2.tlsrsid

                    WHERE h2.tsrid=a.tsrid
                    AND h2.takstatus="Active"
                    AND (
                        i2.tlsrsdesc IS NULL
                        OR i2.tlsrsdesc <> "Reassigned"
                    )
                )


            LEFT JOIN tbl_lib_srstatus i
                ON h.tlsrsid=i.tlsrsid


            /* ==========================================================
               SERVICE
               ========================================================== */

            LEFT JOIN tbl_lib_services m
                ON a.tlsid=m.tlsid


            /* ==========================================================
               VIDEO CONFERENCE
               ========================================================== */

            LEFT JOIN tbl_vidcon_det n
                ON a.tsrid=n.tsrid


            /* ==========================================================
               REQUEST ASSIGNED OFFICE
               ========================================================== */

            LEFT JOIN db_common.tbl_section assoff
                ON a.secid=assoff.secid

            LEFT JOIN db_common.tbl_division assoff_div
                ON assoff.divid=assoff_div.divid


            /* ==========================================================
               CATEGORY / SUBCATEGORY
               ========================================================== */

            LEFT JOIN tbl_lib_subcategory subcat
                ON a.tlscid=subcat.tlscid

            LEFT JOIN tbl_lib_category cat
                ON subcat.tlcid=cat.tlcid


            /* ==========================================================
               CURRENT ACTIVE ASSIGNMENT
               
               Only the current ACTIVE assignment is selected.
               ========================================================== */

            LEFT JOIN (
                SELECT
                    tsrid,
                    MAX(assignment_id) AS assignment_id
                FROM tbl_service_request_assignment
                WHERE status="Active"
                GROUP BY tsrid
            ) latest_ass
                ON a.tsrid=latest_ass.tsrid


            /* ==========================================================
               CURRENT ASSIGNMENT
               ========================================================== */

            LEFT JOIN tbl_service_request_assignment ass
                ON latest_ass.assignment_id=ass.assignment_id


            /* ==========================================================
               CURRENT ICTU EMPLOYEE
               ========================================================== */

            LEFT JOIN home_db.tbl_login ass_login
                ON ass.assigned_to=ass_login.lid

            LEFT JOIN db_common.tbl_employee ass_emp
                ON ass_login.empid=ass_emp.empid


            /* ==========================================================
               PERSON WHO ASSIGNED THE CURRENT REQUEST
               ========================================================== */

            LEFT JOIN home_db.tbl_login assby_login
                ON ass.assigned_by=assby_login.lid

            LEFT JOIN db_common.tbl_employee assby_emp
                ON assby_login.empid=assby_emp.empid


            /* ==========================================================
               PREVIOUS ASSIGNMENT
               
               Find the assignment immediately BEFORE the current
               active assignment.

               Example:

                   assignment_id 101 -> Employee 54 -> Inactive
                   assignment_id 102 -> Employee 75 -> Inactive
                   assignment_id 103 -> Employee 82 -> Active

               old_ass becomes assignment_id 102.

               Therefore:

                   old_officer = Employee 75
                   officer     = Employee 82
               ========================================================== */

            LEFT JOIN tbl_service_request_assignment old_ass
                ON old_ass.assignment_id = (
                    SELECT MAX(prev.assignment_id)
                    FROM tbl_service_request_assignment prev
                    WHERE prev.tsrid=a.tsrid
                    AND prev.assignment_id < latest_ass.assignment_id
                )


            /* ==========================================================
               PREVIOUS ICTU EMPLOYEE
               ========================================================== */

            LEFT JOIN home_db.tbl_login old_ass_login
                ON old_ass.assigned_to=old_ass_login.lid

            LEFT JOIN db_common.tbl_employee old_ass_emp
                ON old_ass_login.empid=old_ass_emp.empid


            /* ==========================================================
               GROUP REQUEST
               ========================================================== */

            GROUP BY a.tsrid

            /* ==========================================================
               SEARCH / FILTER
               ========================================================== */

            HAVING (
                DATE_FORMAT(
                    a.tsrdateadded,
                    "%y%m%d%H%i%s"
                ) LIKE ?
                OR a.tsrdateadded LIKE ?
                OR tsrclient LIKE ?
                OR e.divabbrev LIKE ?
                OR f.secabbrev LIKE ?
                OR a.tsrreqdesc LIKE ?
                OR officer LIKE ?
                OR assigned_by LIKE ?
                OR n.tvcdplatform LIKE ?
                OR n.tvcdtitle LIKE ?
            )

            AND a.tsrstatus="Active"

            ' . $sql_stat;


		return $sql;
	}



	public function srsave()
	{
		$tsrsecid = $this->input->post('tsrsecid');
		$tsrclient = $this->input->post('tsrclient');
		$tsrmobilenum = $this->input->post('tsrmobilenum');
		$tsremail = $this->input->post('tsremail');
		$tsrreqdesc = $this->input->post('srdesc');
		$tsrid = $this->input->post('tsrid');
		$tsrtlcid = $this->input->post('tsrtlcid');
		$tsrtlscid = $this->input->post('tsrtlscid');

		$timestamp = date('Y-m-d H:i:s');

		if (strlen($tsrid) === 0) {
			$prm = [
				'tllid' => $this->session->userdata('lid'),
				'tsrclient' => $tsrclient,
				'tsrmobilenum' => $tsrmobilenum,
				'tsremail' => $tsremail,
				'tsrreqdesc' => $tsrreqdesc,
				'tsrdateadded' => $timestamp,
				'tsrstatus' => "Active",
				'secid' => $tsrsecid,
				'tlcid' => $tsrtlcid,
				'tlscid' => $tsrtlscid
			];
			$res = $this->db->insert('tbl_service_request', $prm);


			// 08162023 auto receive TA
			// $tsrid_ = $this->db->insert_id();
		} else {
			$prm = [
				'tsrclient' => $tsrclient,
				'tsrmobilenum' => $tsrmobilenum,
				'tsremail' => $tsremail,
				'tsrreqdesc' => $tsrreqdesc,
				'secid' => $tsrsecid,
				'tlcid' => $tsrtlcid,
				'tlscid' => $tsrtlscid
			];
			$this->db->where('tsrid', $this->cryptography->decrypt($tsrid));
			$res = $this->db->update('tbl_service_request', $prm);

			// 08162023 auto receive TA
			// $tsrid_ = $this->cryptography->decrypt($tsrid);
		}

		// $this->load->model('Commonclass_model', 'common');
		// $this->common->autoReceived($tsrid_);

		return $res;
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
		$sql = 'SELECT a.tllid, a.tsrclient, a.tsrreqdesc, a.tsrposition, a.tsrmobilenum, a.tsremail, a.secid, 
					CONCAT(e.divabbrev, "-", f.secabbrev) as office,
					tat.tlsrsid, 
					a.tlcid, a.tlscid, a.tsrdateadded
				FROM tbl_service_request a 
				LEFT JOIN home_db.tbl_login b ON a.tllid=b.lid
				LEFT JOIN db_common.tbl_emp_details c ON b.empid=c.empid AND c.dbenddate IS NULL
	            LEFT JOIN db_common.tbl_section f ON c.secid=f.secid
	            LEFT JOIN db_common.tbl_division e ON f.divid=e.divid 
	            LEFT JOIN tbl_action_taken tat ON a.tsrid=tat.tsrid AND tat.takstatus="Active" 
				WHERE a.tsrid=?
				ORDER BY tat.takdateadded desc
				LIMIT 1';
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
		$prm = ['Active', $this->session->userdata('lid'), 0];	//, 3, 6
		$sql = $this->sqlsrcheckconforme();

		if (strlen(trim($this->input->post('tsrid'))) > 0) {
			$prm[] = $this->cryptography->decrypt($this->input->post('tsrid'));
			$sql .= ' AND a.tsrid != ? ';
		}

		#05022022
		$prm[] = 1;
		$prm[] = 2;
		$prm[] = 4;
		$prm[] = 7;
		$sql .= ' AND (tlsrsid IN (?, ?, ?, ?) OR tlsrsid IS NULL) ';

		$res = $this->db->query($sql, $prm)->result_array();

		#print_r($this->db->last_query());
		#die();
		return $res;
	}

	private function sqlsrcheckconforme()
	{
		$sql = 'SELECT COUNT(a.tsrid) as cnt, 
					substring_index(group_concat(cast(c.tlsrsid as CHAR) order by b.takdateadded desc), ",", 1 ) as tlsrsid,
					a.tsrstatus, a.tllid, a.tsrconforme, a.cssid, a.tsrdateadded
				FROM tbl_service_request a 
				LEFT JOIN tbl_action_taken b ON a.tsrid=b.tsrid AND b.takstatus="Active" 
            	LEFT JOIN tbl_lib_srstatus c ON b.tlsrsid=c.tlsrsid
				GROUP BY a.tsrid
				HAVING a.tsrstatus=? AND a.tllid=? AND ( DATE(a.tsrdateadded)=CURDATE() AND (a.cssid IS NULL OR a.cssid=?) ) ';  // AND (tlsrsid NOT IN (?, ?) OR tlsrsid IS NULL)
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
		$sql = 'SELECT a.takdesc, a.takremarks, b.tlsid
				FROM tbl_action_taken a 
				LEFT JOIN tbl_service_request b ON a.tsrid=b.tsrid 
				WHERE a.takid=?';
		$res = $this->db->query($sql, $prm)->result_array();
		return $res;
	}

	public function srdeletelogstatus()
	{
		if ($this->priv_ === 'Client') {

			return 403;
		}

		$xtakid = $this->cryptography->decrypt(
			$this->input->get('xtakid')
		);

		/*
     * ==========================================================
     * GET ACTION THAT WILL BE DELETED
     * ==========================================================
     */

		$prm_ = [
			$xtakid
		];

		$sql_ = '
        SELECT
            a.tsrid,
            a.takdateadded,
            a.tlsrsid,
            b.tlsrsdesc

        FROM tbl_action_taken a

        LEFT JOIN tbl_lib_srstatus b
            ON a.tlsrsid = b.tlsrsid

        WHERE a.takid=?
        AND a.takstatus="Active"
    ';

		$res_ = $this->db
			->query(
				$sql_,
				$prm_
			)
			->result_array();

		$tsrid = isset($res_[0]['tsrid'])
			? $res_[0]['tsrid']
			: 0;

		$deleted_status =
			isset($res_[0]['tlsrsdesc'])
			? trim($res_[0]['tlsrsdesc'])
			: '';

		$takdateadded =
			isset($res_[0]['takdateadded'])
			? $res_[0]['takdateadded']
			: null;

		/*
     * ==========================================================
     * VERIFY THAT THE SELECTED ACTION IS THE LATEST ACTIVE
     * ACTION
     * ==========================================================
     */

		$prm_check = [
			$tsrid
		];

		$sql_check = '
        SELECT
            a.takid

        FROM tbl_action_taken a

        WHERE a.tsrid=?
        AND a.takstatus="Active"

        ORDER BY a.takdateadded DESC

        LIMIT 1
    ';

		$res_check = $this->db
			->query(
				$sql_check,
				$prm_check
			)
			->result_array();

		if (
			!isset($res_check[0]['takid']) ||
			$res_check[0]['takid'] * 1 > $xtakid * 1
		) {

			return 403;
		}

		/*
     * ==========================================================
     * START TRANSACTION
     * ==========================================================
     */

		$this->db->trans_begin();

		/*
     * ==========================================================
     * SPECIAL HANDLING FOR REASSIGNED
     * ==========================================================
     *
     * Reassigned is treated as an event that changes the
     * assignment.
     *
     * When the Reassigned action is deleted:
     *
     * 1. Find the assignment created by the reassignment.
     * 2. DELETE that assignment completely.
     * 3. Restore the previous assignment to Active.
     * 4. DELETE the Reassigned action completely.
     *
     * This returns the request to the state before it was
     * reassigned.
     * ==========================================================
     */

		if (
			$deleted_status === 'Reassigned' &&
			$tsrid > 0
		) {

			/*
         * ------------------------------------------------------
         * Find the assignment associated with this Reassigned
         * action.
         *
         * The assignment is created at the same time as the
         * Reassigned action.
         * ------------------------------------------------------
         */

			$current_assignment = $this->db
				->select('
                assignment_id,
                assigned_to,
                assigned_by,
                assigned_date,
                status
            ')
				->from(
					'tbl_service_request_assignment'
				)
				->where(
					'tsrid',
					$tsrid
				)
				->where(
					'assigned_date <=',
					$takdateadded
				)
				->order_by(
					'assignment_id',
					'DESC'
				)
				->limit(1)
				->get()
				->row_array();

			/*
         * ------------------------------------------------------
         * If assignment was found, delete it completely.
         * ------------------------------------------------------
         */

			if (!empty($current_assignment)) {

				$current_assignment_id =
					$current_assignment['assignment_id'];

				/*
             * --------------------------------------------------
             * Find previous assignment BEFORE deleting the
             * current assignment.
             * --------------------------------------------------
             */

				$previous_assignment = $this->db
					->select('
                    assignment_id,
                    assigned_to,
                    assigned_by,
                    assigned_date,
                    status
                ')
					->from(
						'tbl_service_request_assignment'
					)
					->where(
						'tsrid',
						$tsrid
					)
					->where(
						'assignment_id <',
						$current_assignment_id
					)
					->order_by(
						'assignment_id',
						'DESC'
					)
					->limit(1)
					->get()
					->row_array();

				/*
             * --------------------------------------------------
             * DELETE the assignment created by the reassignment.
             * --------------------------------------------------
             */

				$this->db
					->where(
						'assignment_id',
						$current_assignment_id
					)
					->delete(
						'tbl_service_request_assignment'
					);

				/*
             * --------------------------------------------------
             * Restore the previous assignment.
             * --------------------------------------------------
             */

				if (!empty($previous_assignment)) {

					$this->db
						->where(
							'assignment_id',
							$previous_assignment['assignment_id']
						)
						->update(
							'tbl_service_request_assignment',
							[
								'status' => 'Active'
							]
						);
				}
			}

			/*
         * ------------------------------------------------------
         * DELETE the Reassigned Action Taken record completely.
         * ------------------------------------------------------
         */

			$this->db
				->where(
					'takid',
					$xtakid
				)
				->delete(
					'tbl_action_taken'
				);
		} else {

			/*
         * ======================================================
         * EXISTING BEHAVIOR FOR OTHER STATUSES
         * ======================================================
         *
         * For normal statuses such as:
         *
         * Received
         * For Action
         * Pending
         * Returned
         *
         * keep the existing behavior.
         *
         * Accomplished / Cancelled do NOT deactivate the
         * assignment.
         * ======================================================
         */

			if (
				$tsrid > 0 &&
				$deleted_status !== 'Accomplished' &&
				$deleted_status !== 'Cancelled'
			) {

				$this->db
					->where(
						'tsrid',
						$tsrid
					)
					->where(
						'status',
						'Active'
					)
					->update(
						'tbl_service_request_assignment',
						[
							'status' => 'Inactive'
						]
					);
			}

			/*
         * ------------------------------------------------------
         * Normal Action Taken records are NOT physically
         * deleted. They are marked Inactive.
         * ------------------------------------------------------
         */

			$this->db
				->where(
					'takid',
					$xtakid
				)
				->update(
					'tbl_action_taken',
					[
						'takstatus' => 'Inactive'
					]
				);
		}

		/*
     * ==========================================================
     * TRANSACTION RESULT
     * ==========================================================
     */

		if ($this->db->trans_status() === FALSE) {

			$this->db->trans_rollback();

			return false;
		} else {

			$this->db->trans_commit();

			return true;
		}
	}


	public function sradditionalremarks()
	{
		$prm = [
			'takid' => $this->cryptography->decrypt($this->input->get('takid_rem')),
			'tlsrsid' => 2,
			'tllid' => $this->session->userdata('lid'),
			'takstatus' => 'ACTIVE',
			'takremarks' => $this->input->get('takr_remarks')

		];
		return $this->db->insert('tbl_action_taken_remarks', $prm);
	}

	public function srdeleteadditionalremarks()
	{
		$prm = ['takstatus' => 'INACTIVE'];
		$this->db->where('takrid', $this->input->get('xtakrid'));
		return $this->db->update('tbl_action_taken_remarks', $prm);
	}

	public function sreditactiontaken()
	{
		$prm = [
			$this->input->get('takdesc'),
			$this->input->get('takremarks'),
			$this->input->get('extlsid'),
			$this->cryptography->decrypt($this->input->get('xtakid'))
		];
		#$this->db->where('takid', $this->cryptography->decrypt($this->input->post('xtakid')));
		#$res = $this->db->update('tbl_action_taken', $prm);
		$sql = 'UPDATE tbl_action_taken a 
				LEFT JOIN tbl_service_request b ON a.tsrid=b.tsrid 
				SET a.takdesc=?, a.takremarks=?, b.tlsid=?
				WHERE a.takid=?';

		$res = $this->db->query($sql, $prm);
		return $res;
	}


	public function srlogstatus()
	{
		$sql_act_tak =
			($this->priv_ == 'Client')
			? ' a.takstatus="Active" AND '
			: '';

		$tsrid = $this->cryptography->decrypt(
			$this->input->get('xtsrid')
		);

		$prm = [
			$tsrid,
			'Active'
		];

		/*
     * ==========================================================
     * ACTION TAKEN LOG
     * ==========================================================
     *
     * For every action:
     *
     * 1. Find the latest assignment that existed at the time
     *    the action was recorded.
     *
     * 2. Get the officer from that assignment.
     *
     * 3. Get the person who made that assignment.
     *
     * 4. Get the assignment immediately before it as
     *    Old A. Officer.
     *
     * This allows the log to preserve assignment history.
     * ==========================================================
     */

		$sql = '
        SELECT

            /* ==================================================
             * ACTION TAKEN
             * ================================================== */

            a.takid,

            a.takdateadded AS taktimeadded,

            a.takdateadded,

            a.takdesc,

            a.takstatus,

            a.takremarks,

            b.tlsrsdesc,

            /* ==================================================
             * ACTION OFFICER
             * Person who recorded the Action Taken
             * ================================================== */

            ' .
			$this->cryptography->mysql_aes_decrypt(
				'd.lname'
			) .
			' AS officer,

            /* ==================================================
             * SERVICE REQUEST
             * ================================================== */

            e.tsrreqdesc,

            /* ==================================================
             * CURRENT / APPLICABLE ASSIGNMENT
             * ================================================== */

            ass.assignment_id,

            ass.assigned_to,

            ass.assigned_by,

            ass.assigned_date,

            /* ==================================================
             * CURRENT A. OFFICER
             * ================================================== */

            ' .
			$this->cryptography->mysql_aes_decrypt(
				'd_assigned.lname'
			) .
			' AS assigned_officer,

            /* ==================================================
             * ASSIGNED BY
             * ================================================== */

            ' .
			$this->cryptography->mysql_aes_decrypt(
				'd_assigned_by.lname'
			) .
			' AS assigned_by_officer,

            /* ==================================================
             * PREVIOUS ASSIGNMENT
             * ================================================== */

            old_ass.assignment_id AS old_assignment_id,

            old_ass.assigned_to AS old_assigned_to,

            old_ass.assigned_date AS old_assigned_date,

            /* ==================================================
             * OLD A. OFFICER
             * ================================================== */

            ' .
			$this->cryptography->mysql_aes_decrypt(
				'd_old.lname'
			) .
			' AS old_officer,

            /* ==================================================
             * EXISTING FIELD
             * ================================================== */

            "" AS add_remarks

        FROM tbl_action_taken a

        /* ======================================================
         * STATUS
         * ====================================================== */

        LEFT JOIN tbl_lib_srstatus b
            ON a.tlsrsid = b.tlsrsid

        /* ======================================================
         * ACTION OFFICER
         * ====================================================== */

        LEFT JOIN home_db.tbl_login c
            ON a.tllid = c.lid

        LEFT JOIN db_common.tbl_employee d
            ON c.empid = d.empid

        /* ======================================================
         * SERVICE REQUEST
         * ====================================================== */

        LEFT JOIN tbl_service_request e
            ON a.tsrid = e.tsrid

        /* ======================================================
         * CURRENT ASSIGNMENT AT TIME OF ACTION
         *
         * Get the latest assignment whose assigned date is
         * equal to or earlier than the action date.
         *
         * MAX(assignment_id) also handles multiple assignments
         * having the same timestamp.
         * ====================================================== */

        LEFT JOIN tbl_service_request_assignment ass
            ON ass.assignment_id = (
                SELECT MAX(sa.assignment_id)

                FROM tbl_service_request_assignment sa

                WHERE sa.tsrid = a.tsrid

                AND sa.assigned_date <= a.takdateadded
            )

        /* ======================================================
         * CURRENT ASSIGNED EMPLOYEE
         * ====================================================== */

        LEFT JOIN home_db.tbl_login c_assigned
            ON ass.assigned_to = c_assigned.lid

        LEFT JOIN db_common.tbl_employee d_assigned
            ON c_assigned.empid = d_assigned.empid

        /* ======================================================
         * PERSON WHO ASSIGNED THE REQUEST
         * ====================================================== */

        LEFT JOIN home_db.tbl_login c_assigned_by
            ON ass.assigned_by = c_assigned_by.lid

        LEFT JOIN db_common.tbl_employee d_assigned_by
            ON c_assigned_by.empid = d_assigned_by.empid

        /* ======================================================
         * PREVIOUS ASSIGNMENT
         *
         * Get the assignment immediately before the assignment
         * applicable to this action.
         * ====================================================== */

        LEFT JOIN tbl_service_request_assignment old_ass
            ON old_ass.assignment_id = (
                SELECT MAX(prev.assignment_id)

                FROM tbl_service_request_assignment prev

                WHERE prev.tsrid = a.tsrid

                AND prev.assignment_id < ass.assignment_id
            )

        /* ======================================================
         * PREVIOUS ASSIGNED EMPLOYEE
         * ====================================================== */

        LEFT JOIN home_db.tbl_login c_old
            ON old_ass.assigned_to = c_old.lid

        LEFT JOIN db_common.tbl_employee d_old
            ON c_old.empid = d_old.empid

        /* ======================================================
         * FILTER
         * ====================================================== */

        WHERE ' .
			$sql_act_tak .
			' a.tsrid=?
            AND a.takstatus=?

        ORDER BY a.takdateadded ASC
    ';

		$res = $this->db
			->query($sql, $prm)
			->result_array();

		return $res;
	}

	public function additional_remarks($takid)
	{
		$this->db->select('a.*, DATE_FORMAT(a.takdateadded, \'%b %d, %Y\') AS takdateadded, 
			DATE_FORMAT(a.takdateadded, \'%H:%i:%s\') AS taktimeadded, 
			' . $this->cryptography->mysql_aes_decrypt('d.lname') . ' AS officer');
		$this->db->from('tbl_action_taken_remarks a');
		$this->db->join('home_db.tbl_login c', 'a.tllid=c.lid', 'left');
		$this->db->join('db_common.tbl_employee d', 'c.empid=d.empid', 'left');
		$this->db->where('a.takid', $takid);
		$this->db->where('a.takstatus', 'ACTIVE');
		$this->db->order_by('a.takdateadded', 'ASC');
		return $this->db->get()->result_array();
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
		$tsrid = $this->cryptography->decrypt(
			$this->input->get('xtsrid')
		);

		if (empty($tsrid)) {
			return [];
		}

		/*
     * Get the latest ACTIVE action.
     */
		$latest = $this->db
			->select('
            a.tlsrsid,
            b.tlsrsdesc,
            b.tlsrorder,
            a.takdateadded
        ')
			->from('tbl_action_taken a')
			->join(
				'tbl_lib_srstatus b',
				'a.tlsrsid = b.tlsrsid',
				'left'
			)
			->where('a.tsrid', $tsrid)
			->where('a.takstatus', 'Active')
			->order_by('a.takdateadded', 'DESC')
			->limit(1)
			->get()
			->row_array();

		if (empty($latest)) {
			return [];
		}

		$last_action_was_reassigned = (
			trim($latest['tlsrsdesc']) === 'Reassigned'
		);

		/*
     * Reassigned is an event, not a workflow position.
     *
     * If the latest action is Reassigned,
     * get the latest NON-Reassigned action.
     */
		if ($last_action_was_reassigned) {

			$previous = $this->db
				->select('
                a.tlsrsid,
                b.tlsrsdesc,
                b.tlsrorder,
                a.takdateadded
            ')
				->from('tbl_action_taken a')
				->join(
					'tbl_lib_srstatus b',
					'a.tlsrsid = b.tlsrsid',
					'left'
				)
				->where('a.tsrid', $tsrid)
				->where('a.takstatus', 'Active')
				->where('b.tlsrsdesc !=', 'Reassigned')
				->order_by('a.takdateadded', 'DESC')
				->limit(1)
				->get()
				->row_array();

			if (!empty($previous)) {
				$latest = $previous;
			}
		}

		return [
			[
				'tlsrsdesc' => $latest['tlsrsdesc'],
				'takstatus' => 'Active',
				'tlsrorder' => $latest['tlsrorder'],
				'last_action_was_reassigned' =>
				$last_action_was_reassigned ? '1' : '0'
			]
		];
	}


	/* remove all returned request with no action after 7 days*/
	public function removeReturnedWithNoAction()
	{
		$prm = ['Active', 3, 6, 1, 2, 4, 7, date('Y-m-d H:i:s', strtotime('-7 days')), 'Inactive'];
		$sql = $this->sqlremoveReturnedWithNoAction();
		$res = $this->db->query($sql, $prm);
		return $res;
	}

	private function sqlremoveReturnedWithNoAction()
	{
		$sql = 'UPDATE tbl_service_request AS t1,
					(SELECT 
							a.tsrid,
		                    a.tsrstatus, a.tsrdateadded,
						    substring_index(group_concat(cast(i.tlsrsid as CHAR) order by h.takdateadded desc), ",", 1 ) as tlsrsid,
		                    IF(i.tlsrsdesc IS NULL, "Pending", substring_index(group_concat(cast(i.tlsrsdesc as CHAR) order by h.takdateadded desc), ",", 1 )) as tlsrsdesc,
		                    IF(i.tlsrsdesc IS NULL, NULL, substring_index(group_concat(cast(h.takdateadded as CHAR) order by h.takdateadded desc), ",", 1 )) as takdateadded
					FROM tbl_service_request a 
		            LEFT JOIN tbl_action_taken h ON a.tsrid=h.tsrid AND h.takstatus="Active" 
		            LEFT JOIN tbl_lib_srstatus i ON h.tlsrsid=i.tlsrsid
		            LEFT JOIN tbl_lib_services m ON a.tlsid=m.tlsid
		            LEFT JOIN tbl_vidcon_det n ON a.tsrid=n.tsrid
					GROUP BY a.tsrid 
					HAVING a.tsrstatus=? AND (tlsrsid NOT IN (?, ?, ?, ?, ? ,?) OR tlsrsid IS NULL) AND takdateadded<=?  ORDER BY a.tsrdateadded desc LIMIT 0, 10) AS t2 
				 SET 
				 	t1.tsrstatus=?
				 WHERE 
				 	t1.tsrid=t2.tsrid';
		return $sql;
	}

	public function employeepersection()
	{
		$tsrid = $this->cryptography->decrypt($this->input->get('xtsrid'));
		$this->db->select('a.tsrclient, 
			' . $this->cryptography->mysql_aes_decrypt('c.fname') . ' AS fname,
			' . $this->cryptography->mysql_aes_decrypt('c.mname') . ' AS mname,
			' . $this->cryptography->mysql_aes_decrypt('c.lname') . ' AS lname,
			' . $this->cryptography->mysql_aes_decrypt('c.suffix') . ' AS suffix,
			c.empid	
		');
		$this->db->from('tbl_service_request a');
		$this->db->join('db_common.tbl_emp_details b', 'a.tsrclient=b.empid AND b.dbstatus="0" AND b.dbenddate IS NULL', 'left');
		$this->db->join('db_common.tbl_section x', 'b.secid=x.secid', 'left');
		$this->db->join('db_common.tbl_emp_details y', 'x.secid=y.secid AND y.dbstatus="0" AND y.dbenddate IS NULL', 'left');
		$this->db->join('db_common.tbl_employee c', 'y.empid=c.empid', 'left');
		$this->db->where('a.tsrid', $tsrid);
		$this->db->where('c.isactive', 1);
		$this->db->order_by($this->cryptography->mysql_aes_decrypt('c.lname') . ' ASC');
		return $this->db->get()->result_array();
	}

	public function employeeDefault()
	{
		$tsrid = $this->cryptography->decrypt($this->input->get('xtsrid'));
		$this->db->select('a.tsrclient,
			' . $this->cryptography->mysql_aes_decrypt('c.fname') . ' AS fname,
			' . $this->cryptography->mysql_aes_decrypt('c.mname') . ' AS mname,
			' . $this->cryptography->mysql_aes_decrypt('c.lname') . ' AS lname,
			' . $this->cryptography->mysql_aes_decrypt('c.suffix') . ' AS suffix,
			c.empid
		');
		$this->db->from('tbl_service_request a');
		$this->db->join('db_common.tbl_emp_details b', 'a.tsrclient=b.empid AND b.dbstatus="0" AND b.dbenddate IS NULL', 'left');
		$this->db->join('db_common.tbl_employee c', 'b.empid=c.empid', 'left');
		$this->db->where('a.tsrid', $tsrid);
		$this->db->where('c.isactive', 1);
		$this->db->order_by($this->cryptography->mysql_aes_decrypt('c.lname') . ' ASC');
		return $this->db->get()->result_array();
	}

	public function assignRequest()
	{
		$tsrid = $this->cryptography->decrypt($this->input->post('xtsrid'));
		$assigned_to = $this->input->post('assigned_to');
		$remarks = trim($this->input->post('remarks'));

		$assigned_by = $this->session->userdata('lid');

		/*
		 * Validate required values
		 */
		if (empty($tsrid) || empty($assigned_to) || empty($assigned_by)) {
			return false;
		}

		/*
		 * Check if this request already has an active assignment.
		 *
		 * If there is an existing assignment, make it inactive first.
		 * This allows us to reassign a request to another ICTU employee
		 * while keeping the previous assignment in the history.
		 */
		$this->db->where('tsrid', $tsrid);
		$this->db->where('status', 'Active');
		$this->db->update('tbl_service_request_assignment', [
			'status' => 'Inactive'
		]);

		/*
		 * Create the new assignment.
		 */
		$prm = [
			'tsrid'        => $tsrid,
			'assigned_to'  => $assigned_to,
			'assigned_by'  => $assigned_by,
			'assigned_date' => date('Y-m-d H:i:s'),
			'status'       => 'Active',
			'remarks'      => strlen($remarks) === 0 ? null : $remarks
		];

		$res = $this->db->insert('tbl_service_request_assignment', $prm);

		return $res;
	}


	public function getActiveAssignment($tsrid)
	{
		$sql = 'SELECT
					a.assignment_id,
					a.tsrid,
					a.assigned_to,
					a.assigned_by,
					a.assigned_date,
					a.status,
					a.remarks,

					' . $this->cryptography->mysql_aes_decrypt('c.fname') . ' AS assigned_fname,
					' . $this->cryptography->mysql_aes_decrypt('c.mname') . ' AS assigned_mname,
					' . $this->cryptography->mysql_aes_decrypt('c.lname') . ' AS assigned_lname,
					' . $this->cryptography->mysql_aes_decrypt('c.suffix') . ' AS assigned_suffix

				FROM tbl_service_request_assignment a

				LEFT JOIN home_db.tbl_login b
					ON a.assigned_to = b.lid

				LEFT JOIN db_common.tbl_employee c
					ON b.empid = c.empid

				WHERE a.tsrid = ?
				AND a.status = "Active"

				ORDER BY a.assigned_date DESC

				LIMIT 1';

		$prm = [$tsrid];

		return $this->db->query($sql, $prm)->result_array();
	}

	public function getICTUEmployees()
	{
		$employees = $this->Commonclass_model->selactionofficer('-1');

		$unique = [];

		foreach ($employees as $employee) {

			$lid = $employee['lid'];

			if (!isset($unique[$lid])) {
				$unique[$lid] = $employee;
			}
		}

		return array_values($unique);
	}

	public function reassignRequest()
	{
		// ---------------------------------------------------------
		// Get request ID
		// ---------------------------------------------------------

		$tsrid = $this->cryptography->decrypt(
			$this->input->post('reassign_xtsrid')
		);

		// ---------------------------------------------------------
		// Get new ICTU employee
		// ---------------------------------------------------------

		$assigned_to = trim(
			$this->input->post('assigned_to')
		);

		// ---------------------------------------------------------
		// Current logged-in user
		// ---------------------------------------------------------

		$assigned_by = $this->session->userdata('lid');

		if (
			empty($tsrid) ||
			empty($assigned_to) ||
			empty($assigned_by)
		) {
			return false;
		}


		// =========================================================
		// 1. CHECK CURRENT REQUEST STATUS
		// =========================================================

		$current_status = $this->db
			->select('i.tlsrsdesc')
			->from('tbl_action_taken h')
			->join(
				'tbl_lib_srstatus i',
				'h.tlsrsid=i.tlsrsid',
				'left'
			)
			->where('h.tsrid', $tsrid)
			->where('h.takstatus', 'Active')
			->order_by('h.takdateadded', 'DESC')
			->limit(1)
			->get()
			->row_array();

		$status = isset($current_status['tlsrsdesc'])
			? trim($current_status['tlsrsdesc'])
			: '';


		// ---------------------------------------------------------
		// Reassignment is allowed only for:
		//
		// Received
		// For Action
		// ---------------------------------------------------------

		if (
			$status !== 'Received' &&
			$status !== 'For Action'
		) {
			return false;
		}


		// =========================================================
		// 2. GET CURRENT ACTIVE ASSIGNMENT
		// =========================================================

		$current_assignment = $this->db
			->select('assignment_id, assigned_to')
			->from('tbl_service_request_assignment')
			->where('tsrid', $tsrid)
			->where('status', 'Active')
			->order_by('assignment_id', 'DESC')
			->limit(1)
			->get()
			->row_array();

		if (empty($current_assignment)) {
			return false;
		}


		// =========================================================
		// 3. PREVENT ASSIGNING THE SAME EMPLOYEE
		// =========================================================

		if (
			(string) $current_assignment['assigned_to'] ===
			(string) $assigned_to
		) {
			return false;
		}


		// =========================================================
		// 4. START TRANSACTION
		// =========================================================

		$this->db->trans_begin();


		// =========================================================
		// 5. DEACTIVATE PREVIOUS ASSIGNMENT
		// =========================================================

		$this->db
			->where(
				'assignment_id',
				$current_assignment['assignment_id']
			)
			->update(
				'tbl_service_request_assignment',
				[
					'status' => 'Inactive'
				]
			);


		// =========================================================
		// 6. CREATE NEW ASSIGNMENT
		// =========================================================

		$new_assignment = [
			'tsrid'         => $tsrid,
			'assigned_to'   => $assigned_to,
			'assigned_by'   => $assigned_by,
			'assigned_date' => date('Y-m-d H:i:s'),
			'status'        => 'Active',
			'remarks'       => 'Reassigned'
		];

		$this->db->insert(
			'tbl_service_request_assignment',
			$new_assignment
		);


		// =========================================================
		// 7. CHECK TRANSACTION
		// =========================================================

		if ($this->db->trans_status() === FALSE) {

			$this->db->trans_rollback();

			return false;
		} else {

			$this->db->trans_commit();

			return true;
		}
	}
}
