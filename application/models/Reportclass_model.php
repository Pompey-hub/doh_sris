<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reportclass_model extends CI_Model
{
    public function actionofficer()
    {
        $prm = ['Active', 'SRIS', 'Active', $this->session->userdata('secabbrev')];
        $sql = $this->sqlactionofficer();
        $res = $this->db->query($sql, $prm)->result_array();
        return $res;
    }

    public function srfrep($mm, $yy, $off, $serv, $tlsrsid)
    {
        $this->load->model('Commonclass_model', 'common');
        $this->common->group_concat_max_len();

        $secid_ = $this->common->section(
            $this->session->userdata('secabbrev')
        );

        $secid = isset($secid_[0]['secid'])
            ? $secid_[0]['secid']
            : '';

        $where_tlsrsid = '';

        if ($tlsrsid === '-1') {
            $prm = [
                1,
                2,
                3,
                6,
                $secid,
                'Active',
                1,
                2,
                3,
                6,
                $secid,
                '%' . $yy . '-' . $mm . '-%'
            ];

            $where_tlsrsid = ' b.tlsrsid IN (?, ?, ?, ?) AND a.secid = ? ';
        } else {
            $prm = [
                $tlsrsid,
                $secid,
                'Active',
                $tlsrsid,
                $secid,
                '%' . $yy . '-' . $mm . '-%'
            ];

            $where_tlsrsid = ' b.tlsrsid = ? AND a.secid = ? ';
        }

        $where = '';

        if ($off !== '-1') {
            $prm[] = $off;
            $where .= ' AND b.tllid = ? ';
        }

        if ($serv === '-2') {
            $where .= ' AND a.tlsid IN (1, 2) ';
        } elseif ($serv === '-3') {
            $where .= ' AND a.tlsid IN (3, 4, 5, 6) ';
        } elseif ($serv !== '-1') {
            $prm[] = $serv;
            $where .= ' AND a.tlsid = ? ';
        }

        $sql = $this->sqlsrfrep($where, $where_tlsrsid);

        $query = $this->db->query($sql, $prm);

        if ($query === false) {
            log_message(
                'error',
                'ARTA report SQL error: ' .
                    print_r($this->db->error(), true)
            );

            log_message('error', 'ARTA report SQL: ' . $sql);
            log_message(
                'error',
                'ARTA report parameters: ' . print_r($prm, true)
            );

            return [];
        }

        return $query->result_array();
    }


    public function srfassignmentresolution($mm, $yy, $serv, $officer = '')
    {
        $this->load->model('Commonclass_model', 'common');

        // Get current section.
        $secid_ = $this->common->section(
            $this->session->userdata('secabbrev')
        );

        $secid = isset($secid_[0]['secid'])
            ? $secid_[0]['secid']
            : '';

        if ($secid === '') {
            return [];
        }

        // Decrypt employee names.
        $clientFirst = $this->cryptography->mysql_aes_decrypt(
            'client_emp.fname'
        );
        $clientLast = $this->cryptography->mysql_aes_decrypt(
            'client_emp.lname'
        );
        $assignedFirst = $this->cryptography->mysql_aes_decrypt(
            'assigned_emp.fname'
        );
        $assignedLast = $this->cryptography->mysql_aes_decrypt(
            'assigned_emp.lname'
        );
        $assignerFirst = $this->cryptography->mysql_aes_decrypt(
            'assigner_emp.fname'
        );
        $assignerLast = $this->cryptography->mysql_aes_decrypt(
            'assigner_emp.lname'
        );

        // Main report query.
        $sql = '
        SELECT
            a.tsrid,
            a.tsrclient,
            a.tsrreqdesc,
            a.tsrdateadded,
            a.tlsid,

            svc.tlssdesc AS service_name,

            CONCAT_WS(
                " ",
                ' . $clientFirst . ',
                ' . $clientLast . '
            ) AS client_name,

            rec.received_at,

            current_status.tlsrsdesc AS current_status,

            assignment.assignment_id,
            assignment.assigned_date,
            assignment.status AS assignment_status,
            assignment.remarks AS assignment_remarks,

            CONCAT_WS(
                " ",
                ' . $assignedFirst . ',
                ' . $assignedLast . '
            ) AS assigned_officer,

            CONCAT_WS(
                " ",
                ' . $assignerFirst . ',
                ' . $assignerLast . '
            ) AS assigned_by_officer,

            assignment.assigned_to AS assigned_officer_id,

            next_assignment.next_assignment_date,

            resolution.completion_date,

            CASE
                WHEN assignment.assignment_id IS NULL THEN NULL
                ELSE GREATEST(
                    0,
                    TIMESTAMPDIFF(
                        SECOND,
                        assignment.assigned_date,
                        LEAST(
                            COALESCE(
                                next_assignment.next_assignment_date,
                                "9999-12-31 23:59:59"
                            ),
                            COALESCE(
                                resolution.completion_date,
                                "9999-12-31 23:59:59"
                            ),
                            NOW()
                        )
                    )
                )
            END AS assignment_duration_seconds,

            CASE
                WHEN assignment.assignment_id IS NULL THEN 0
                ELSE GREATEST(
                    0,
                    (
                        SELECT COUNT(*)
                        FROM tbl_service_request_assignment history
                        WHERE history.tsrid = a.tsrid
                    ) - 1
                )
            END AS reassignment_count

        FROM tbl_service_request a

        LEFT JOIN tbl_lib_services svc
            ON a.tlsid = svc.tlsid

        LEFT JOIN db_common.tbl_employee client_emp
            ON a.tsrclient = client_emp.empid

        LEFT JOIN (
            SELECT
                tsrid,
                MIN(takdateadded) AS received_at
            FROM tbl_action_taken
            WHERE tlsrsid = 1
              AND takstatus = "Active"
            GROUP BY tsrid
        ) rec
            ON rec.tsrid = a.tsrid

        LEFT JOIN tbl_service_request_assignment assignment
            ON assignment.tsrid = a.tsrid

        LEFT JOIN home_db.tbl_login assigned_login
            ON assignment.assigned_to = assigned_login.lid

        LEFT JOIN db_common.tbl_employee assigned_emp
            ON assigned_login.empid = assigned_emp.empid

        LEFT JOIN home_db.tbl_login assigner_login
            ON assignment.assigned_by = assigner_login.lid

        LEFT JOIN db_common.tbl_employee assigner_emp
            ON assigner_login.empid = assigner_emp.empid

        LEFT JOIN (
            SELECT
                current_assignment.assignment_id,

                (
                    SELECT next_assignment.assigned_date
                    FROM tbl_service_request_assignment next_assignment
                    WHERE next_assignment.tsrid =
                          current_assignment.tsrid
                      AND (
                          next_assignment.assigned_date >
                              current_assignment.assigned_date
                          OR (
                              next_assignment.assigned_date =
                                  current_assignment.assigned_date
                              AND next_assignment.assignment_id >
                                  current_assignment.assignment_id
                          )
                      )
                    ORDER BY
                        next_assignment.assigned_date ASC,
                        next_assignment.assignment_id ASC
                    LIMIT 1
                ) AS next_assignment_date

            FROM tbl_service_request_assignment current_assignment
        ) next_assignment
            ON next_assignment.assignment_id =
               assignment.assignment_id

        LEFT JOIN (
            SELECT
                tsrid,
                MIN(takdateadded) AS completion_date
            FROM tbl_action_taken
            WHERE tlsrsid IN (3, 6)
              AND takstatus = "Active"
            GROUP BY tsrid
        ) resolution
            ON resolution.tsrid = a.tsrid

        LEFT JOIN tbl_action_taken latest_action
            ON latest_action.takid = (
                SELECT latest.takid
                FROM tbl_action_taken latest
                WHERE latest.tsrid = a.tsrid
                  AND latest.takstatus = "Active"
                  AND latest.tlsrsid NOT IN (9, 10)
                ORDER BY
                    latest.takdateadded DESC,
                    latest.takid DESC
                LIMIT 1
            )

        LEFT JOIN tbl_lib_srstatus current_status
            ON latest_action.tlsrsid = current_status.tlsrsid

        WHERE a.secid = ?

          AND MONTH(
              COALESCE(rec.received_at, a.tsrdateadded)
          ) = ?

          AND YEAR(
              COALESCE(rec.received_at, a.tsrdateadded)
          ) = ?
    ';

        $params = [
            $secid,
            (int) $mm,
            (int) $yy
        ];

        // Service filter.
        if ((string) $serv === '-2') {
            $sql .= ' AND a.tlsid IN (1, 2) ';
        } elseif ((string) $serv === '-3') {
            $sql .= ' AND a.tlsid IN (3, 4, 5, 6) ';
        } elseif ((string) $serv !== '-1') {
            $sql .= ' AND a.tlsid = ? ';
            $params[] = (int) $serv;
        }

        // Assigned ICTU officer filter.
        if (
            $officer !== '' &&
            ctype_digit((string) $officer) &&
            (int) $officer > 0
        ) {
            $sql .= ' AND assignment.assigned_to = ? ';
            $params[] = (int) $officer;
        }

        // Report ordering.
        $sql .= '
        ORDER BY
            COALESCE(rec.received_at, a.tsrdateadded) DESC,
            a.tsrid DESC,
            assignment.assigned_date ASC,
            assignment.assignment_id ASC
    ';

        return $this->db->query($sql, $params)->result_array();
    }

    public function srfassignmentresolutionofficers()
    {
        $this->load->model('Commonclass_model', 'common');

        $section = $this->common->section(
            $this->session->userdata('secabbrev')
        );

        $secid = isset($section[0]['secid'])
            ? $section[0]['secid']
            : '';

        if ($secid === '') {
            return [];
        }

        $firstName = $this->cryptography->mysql_aes_decrypt(
            'emp.fname'
        );

        $lastName = $this->cryptography->mysql_aes_decrypt(
            'emp.lname'
        );

        $sql = '
        SELECT DISTINCT
            login.lid AS id,
            CONCAT_WS(
                " ",
                ' . $firstName . ',
                ' . $lastName . '
            ) AS name

        FROM tbl_service_request_assignment assignment

        INNER JOIN tbl_service_request request
            ON request.tsrid = assignment.tsrid

        INNER JOIN home_db.tbl_login login
            ON login.lid = assignment.assigned_to

        INNER JOIN db_common.tbl_employee emp
            ON emp.empid = login.empid

        WHERE request.secid = ?

        ORDER BY name ASC
    ';

        return $this->db->query(
            $sql,
            [$secid]
        )->result_array();
    }


    public function srfassignmentresolutionactionlogs($reportRows)
    {
        if (empty($reportRows)) {
            return [];
        }

        /*
    |--------------------------------------------------------------------------
    | 1. Collect unique ticket IDs and request descriptions
    |--------------------------------------------------------------------------
    */

        $ticketIds = [];
        $descriptions = [];

        foreach ($reportRows as $row) {
            if (!isset($row['tsrid']) || !is_numeric($row['tsrid'])) {
                continue;
            }

            $ticketId = (string) (int) $row['tsrid'];

            $ticketIds[] = $ticketId;

            if (!isset($descriptions[$ticketId])) {
                $descriptions[$ticketId] = $row['tsrreqdesc'] ?? '';
            }
        }

        $ticketIds = array_values(array_unique($ticketIds));

        if (empty($ticketIds)) {
            return [];
        }

        $placeholders = implode(
            ',',
            array_fill(0, count($ticketIds), '?')
        );

        /*
    |--------------------------------------------------------------------------
    | 2. Prepare encrypted employee-name expressions
    |--------------------------------------------------------------------------
    */

        $officerName = $this->cryptography->mysql_aes_decrypt(
            'action_emp.lname'
        );

        $assignedOfficerName = $this->cryptography->mysql_aes_decrypt(
            'assigned_emp.lname'
        );

        $assignedByName = $this->cryptography->mysql_aes_decrypt(
            'assigned_by_emp.lname'
        );

        $previousOfficerName = $this->cryptography->mysql_aes_decrypt(
            'previous_emp.lname'
        );

        /*
    |--------------------------------------------------------------------------
    | 3. Retrieve the action history
    |--------------------------------------------------------------------------
    |
    | The assignment applicable to an action is the latest assignment
    | created on or before the action timestamp.
    |
    */

        $sql = '
        SELECT
            action.takid,
            action.tsrid,
            action.takdateadded,
            DATE_FORMAT(
                action.takdateadded,
                "%b %d, %Y"
            ) AS date,
            DATE_FORMAT(
                action.takdateadded,
                "%H:%i:%s"
            ) AS time,
            action.takdesc,
            action.takremarks,
            action.takstatus AS record_state,
            status.tlsrsdesc AS status,

            ' . $officerName . ' AS officer,

            assignment.assignment_id,
            assignment.assigned_to,

            ' . $assignedOfficerName . ' AS assigned_officer,

            assignment.assigned_by,

            ' . $assignedByName . ' AS assigned_by_officer,

            previous_assignment.assignment_id
                AS old_assignment_id,

            ' . $previousOfficerName . ' AS old_officer

        FROM tbl_action_taken action

        LEFT JOIN tbl_lib_srstatus status
            ON status.tlsrsid = action.tlsrsid

        LEFT JOIN home_db.tbl_login action_login
            ON action.tllid = action_login.lid

        LEFT JOIN db_common.tbl_employee action_emp
            ON action_login.empid = action_emp.empid

        LEFT JOIN tbl_service_request_assignment assignment
            ON assignment.assignment_id = (
                SELECT MAX(sa.assignment_id)
                FROM tbl_service_request_assignment sa
                WHERE sa.tsrid = action.tsrid
                  AND sa.assigned_date <= action.takdateadded
            )

        LEFT JOIN home_db.tbl_login assigned_login
            ON assignment.assigned_to = assigned_login.lid

        LEFT JOIN db_common.tbl_employee assigned_emp
            ON assigned_login.empid = assigned_emp.empid

        LEFT JOIN home_db.tbl_login assigned_by_login
            ON assignment.assigned_by = assigned_by_login.lid

        LEFT JOIN db_common.tbl_employee assigned_by_emp
            ON assigned_by_login.empid = assigned_by_emp.empid

        LEFT JOIN tbl_service_request_assignment previous_assignment
            ON previous_assignment.assignment_id = (
                SELECT MAX(prev.assignment_id)
                FROM tbl_service_request_assignment prev
                WHERE prev.tsrid = action.tsrid
                  AND prev.assignment_id < assignment.assignment_id
            )

        LEFT JOIN home_db.tbl_login previous_login
            ON previous_assignment.assigned_to = previous_login.lid

        LEFT JOIN db_common.tbl_employee previous_emp
            ON previous_login.empid = previous_emp.empid

        WHERE action.tsrid IN (' . $placeholders . ')
          AND action.takstatus = "Active"

        ORDER BY
            action.tsrid ASC,
            action.takdateadded ASC,
            action.takid ASC
    ';

        $rows = $this->db->query(
            $sql,
            $ticketIds
        )->result_array();

        if (empty($rows)) {
            $actionLogs = [];

            foreach ($ticketIds as $ticketId) {
                $actionLogs[$ticketId] = [
                    'description' => $descriptions[$ticketId] ?? '',
                    'logs' => []
                ];
            }

            return $actionLogs;
        }

        /*
    |--------------------------------------------------------------------------
    | 4. Retrieve additional remarks for all action records
    |--------------------------------------------------------------------------
    */

        $actionIds = [];

        foreach ($rows as $row) {
            if (!empty($row['takid'])) {
                $actionIds[] = (int) $row['takid'];
            }
        }

        $actionIds = array_values(array_unique($actionIds));
        $remarksByAction = [];

        if (!empty($actionIds)) {
            $remarkPlaceholders = implode(
                ',',
                array_fill(0, count($actionIds), '?')
            );

            $remarkOfficer = $this->cryptography->mysql_aes_decrypt(
                'remark_emp.lname'
            );

            $remarkSql = '
            SELECT
                remark.takrid,
                remark.takid,
                remark.takdateadded,
                DATE_FORMAT(
                    remark.takdateadded,
                    "%b %d, %Y"
                ) AS date,
                DATE_FORMAT(
                    remark.takdateadded,
                    "%H:%i:%s"
                ) AS time,
                remark.takremarks,
                remark.takstatus,

                ' . $remarkOfficer . ' AS officer

            FROM tbl_action_taken_remarks remark

            LEFT JOIN home_db.tbl_login remark_login
                ON remark.tllid = remark_login.lid

            LEFT JOIN db_common.tbl_employee remark_emp
                ON remark_login.empid = remark_emp.empid

            WHERE remark.takid IN (' . $remarkPlaceholders . ')
              AND remark.takstatus = "ACTIVE"

            ORDER BY
                remark.takdateadded ASC,
                remark.takrid ASC
        ';

            $remarkRows = $this->db->query(
                $remarkSql,
                $actionIds
            )->result_array();

            foreach ($remarkRows as $remark) {
                $actionId = (string) $remark['takid'];

                if (!isset($remarksByAction[$actionId])) {
                    $remarksByAction[$actionId] = [];
                }

                $remarksByAction[$actionId][] = [
                    'takrid' => $remark['takrid'],
                    'takdateadded' => $remark['date'],
                    'taktimeadded' => $remark['time'],
                    'takremarks' => $remark['takremarks'] ?? '',
                    'takstatus' => $remark['takstatus'] ?? '',
                    'officer' => $remark['officer'] ?? ''
                ];
            }
        }

        /*
    |--------------------------------------------------------------------------
    | 5. Group action history by ticket ID
    |--------------------------------------------------------------------------
    */

        $actionLogs = [];

        foreach ($ticketIds as $ticketId) {
            $actionLogs[$ticketId] = [
                'description' => $descriptions[$ticketId] ?? '',
                'logs' => []
            ];
        }

        foreach ($rows as $row) {
            $ticketId = (string) $row['tsrid'];
            $actionId = (string) $row['takid'];

            if (!isset($actionLogs[$ticketId])) {
                $actionLogs[$ticketId] = [
                    'description' => $descriptions[$ticketId] ?? '',
                    'logs' => []
                ];
            }

            $actionLogs[$ticketId]['logs'][] = [
                'takid' => $row['takid'],
                'takdateadded' => $row['date'] ?? '',
                'taktimeadded' => $row['time'] ?? '',
                'tlsrsdesc' => $row['status'] ?? '',
                'takdesc' => $row['takdesc'] ?? '',
                'takremarks' => $row['takremarks'] ?? '',
                'takstatus' => $row['record_state'] ?? '',
                'officer' => $row['officer'] ?? '',
                'assigned_officer' => $row['assigned_officer'] ?? '',
                'assigned_by_officer' => $row['assigned_by_officer'] ?? '',
                'old_officer' => $row['old_officer'] ?? '',
                'add_remarks' => $remarksByAction[$actionId] ?? []
            ];
        }

        return $actionLogs;
    }



    public function srfsummary($mm, $yy, $off, $tlsrsid)
    {
        $this->load->model('Commonclass_model', 'common');
        $secid_ = $this->common->section($this->session->userdata('secabbrev'));
        $secid =  isset($secid_[0]['secid']) ? $secid_[0]['secid'] : '';

        $prm = [$tlsrsid, $tlsrsid, $tlsrsid, $tlsrsid, $tlsrsid, $tlsrsid, $tlsrsid, 'Active', '%' . $yy . '-' . $mm . '-%', $tlsrsid];

        $sql_ = '';
        if ($off !== '-1') {
            $prm[] = $off;
            $sql_ = ' AND b.tllid=? ';
        }

        $prm[] = $secid;
        $sql_ .= ' AND a.secid=? ';

        $sql = $this->sqlsrfsummary($sql_);
        $res = $this->db->query($sql, $prm)->result_array();
        return $res;
    }

    private function sqlsrfsummary($sql_)
    {
        /*WHERE b.takstatus="Active" 060321*/
        $sql = '
				SELECT 
					SUM(cnt_HW) AS cnt_HW, SUM(cnt_SW) AS cnt_SW, SUM(cnt_VC) AS cnt_VC, SUM(cnt_HIS) AS cnt_HIS, 
					SUM(cnt_IIS) AS cnt_IIS, SUM(cnt_OC) AS cnt_OC, SUM(cnt_unk) AS cnt_unk

				FROM (
						SELECT 
							(IF(a.tlsid=1 AND b.tlsrsid=?, 1, 0)) AS cnt_HW,
							(IF(a.tlsid=2 AND b.tlsrsid=?, 1, 0)) AS cnt_SW,
							(IF(a.tlsid=3 AND b.tlsrsid=?, 1, 0)) AS cnt_VC,
							(IF(a.tlsid=4 AND b.tlsrsid=?, 1, 0)) AS cnt_HIS,
							(IF(a.tlsid=5 AND b.tlsrsid=?, 1, 0)) AS cnt_IIS,
							(IF(a.tlsid=6 AND b.tlsrsid=?, 1, 0)) AS cnt_OC,
							(IF((a.tlsid=0 OR a.tlsid IS NULL) AND b.tlsrsid=?, 1, 0)) AS cnt_unk
						FROM tbl_service_request a 
						LEFT JOIN tbl_action_taken b ON a.tsrid=b.tsrid 
						WHERE b.takstatus="Active" AND a.tsrstatus=? AND b.takdateadded LIKE ? AND b.tlsrsid = ? ' . $sql_ . '
						GROUP BY b.tsrid, b.takdateadded
                		ORDER BY b.tsrid ASC
					) temp';
        return $sql;
    }



    private function sqlsrfrep($where, $where_tlsrsid)
    {
        /*AND b.takstatus="Active" 060321*/
        $sql = 'SELECT a.tsrdateadded, a.tlsid, a.tsrstatus, a.cssid, b.tsrid, b.tlsrsid, b.takdateadded, e.tlssdesc,
					GROUP_CONCAT(b.tlsrsid ORDER BY c.tlsrorder ASC SEPARATOR "&#@s#&") as tlsrsid_alias,
					GROUP_CONCAT(b.takdateadded ORDER BY c.tlsrorder ASC SEPARATOR "&#@s#&") as takdateadded_alias,
					GROUP_CONCAT(IF(b.takdesc IS NULL, "", b.takdesc) ORDER BY c.tlsrorder ASC SEPARATOR "&#@s#&") as takdesc,
					GROUP_CONCAT(b.takmacaddress ORDER BY c.tlsrorder ASC SEPARATOR "&#@s#&") as takmacaddress,
					GROUP_CONCAT(' . $this->cryptography->mysql_aes_decrypt('d.lname') . ' ORDER BY c.tlsrorder ASC SEPARATOR "&#@s#&") as actoff,
					GROUP_CONCAT(x.lid ORDER BY c.tlsrorder ASC SEPARATOR "&#@s#&") as actoffid_alias,
					n.tvcdid, ' . $this->cryptography->mysql_aes_decrypt('yy.uname') . ' AS uname
				FROM tbl_service_request a 
				LEFT JOIN tbl_action_taken b ON a.tsrid=b.tsrid AND b.takstatus="Active" 
				LEFT JOIN tbl_lib_srstatus c ON b.tlsrsid=c.tlsrsid
				LEFT JOIN home_db.tbl_login_access_rights x ON b.tllid=x.lid
				LEFT JOIN home_db.tbl_login y ON x.lid=y.lid 
				LEFT JOIN db_common.tbl_employee d ON y.empid=d.empid 	 
				LEFT JOIN tbl_lib_services e ON a.tlsid=e.tlsid 
				LEFT JOIN tbl_vidcon_det n ON a.tsrid=n.tsrid
				LEFT JOIN home_db.tbl_login yy ON a.tllid=yy.lid 
				WHERE ' . $where_tlsrsid . '
				GROUP BY b.tsrid
				HAVING b.tsrid IN (SELECT DISTINCT(b.tsrid)
                        FROM tbl_service_request a 
                        LEFT JOIN tbl_action_taken b ON a.tsrid=b.tsrid 

                        LEFT JOIN home_db.tbl_login_access_rights x ON b.tllid=x.lid
						LEFT JOIN home_db.tbl_login y ON x.lid=y.lid 


                        LEFT JOIN db_common.tbl_employee d ON y.empid=d.empid 	 
                        WHERE 
                            b.tsrid IS NOT NULL AND a.tsrstatus=? AND ' . $where_tlsrsid . ' AND 
                            b.takdateadded LIKE ? ' . $where . ' )
				ORDER BY takdateadded_alias ASC ';
        return $sql;
    }

    private function sqlactionofficer()
    {
        $sql = 'SELECT a.lid, ' . $this->cryptography->mysql_aes_decrypt('c.lname') . ' as actoff
				FROM home_db.tbl_login_access_rights a 
				LEFT JOIN home_db.tbl_login b ON a.lid=b.lid 
				LEFT JOIN db_common.tbl_employee c ON b.empid=c.empid 
				LEFT JOIN db_common.tbl_emp_details bb ON c.empid=bb.empid AND bb.dbenddate IS NULL AND bb.dbstatus=0 
				LEFT JOIN conn_db_common_section e ON bb.secid=e.secid
				LEFT JOIN db_common.tbl_section sec ON e.secid=sec.secid
				WHERE a.tlarstatus=? AND a.tlarsys=? 
					AND e.status=? AND sec.secabbrev=?';
        return $sql;
    }


    public function vidcon($mm, $yy)
    {
        $prm = [$yy . '-' . $mm . '-%'];
        $sql = $this->sqlvidcon();
        $res = $this->db->query($sql, $prm)->result_array();
        return $res;
    }

    private function sqlvidcon()
    {
        $sql = 'SELECT 
					a.tsrid, a.cssid, a.tsrstatus, a.tsrdateadded, a.tsrdateadded as tsrdateadded_time, 
				    CONCAT(e.divabbrev, "-", f.secabbrev) as office, 
				    IF(i.tlsrsdesc IS NULL, "Pending", substring_index(group_concat(cast(i.tlsrsdesc as CHAR) order by h.takdateadded desc), ",", 1 )) as tlsrsdesc, 
				    substring_index(group_concat(cast(CONCAT(LEFT(' . $this->cryptography->mysql_aes_decrypt('k.fname') . ', 1), ".", ' . $this->cryptography->mysql_aes_decrypt('k.fname') . ') as CHAR) order by h.takdateadded desc), ",", 1 ) as officer,
				    substring_index(group_concat(cast(i.tlsrsid as CHAR) order by h.takdateadded desc), ",", 1 ) as tlsrsid,
				    substring_index(group_concat(cast(h.takremarks as CHAR) order by h.takdateadded desc SEPARATOR "&$sh3n$&"), "&$sh3n$&", 1 ) as takremarks,
				    ' . $this->cryptography->mysql_aes_decrypt('x.fname') . ' AS fname,
					' . $this->cryptography->mysql_aes_decrypt('x.mname') . ' AS mname,
					' . $this->cryptography->mysql_aes_decrypt('x.lname') . ' AS lname,
					' . $this->cryptography->mysql_aes_decrypt('x.suffix') . ' AS suffix,
					' . $this->cryptography->mysql_aes_decrypt('x.extname') . ' AS extname,
					"" AS tsrclient,
					n.tvcdplatform, n.tvcdid, n.tvcddate, n.tvcdtimestart, n.tvcdtimeend, n.tvcdtitle, n.tvcdnumpar
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
            LEFT JOIN tbl_vidcon_det n ON a.tsrid=n.tsrid

			GROUP BY a.tsrid 
			HAVING a.tsrstatus="Active" AND tlsrsid IN (3, 6) AND n.tvcdid IS NOT NULL AND n.tvcddate LIKE ?
				ORDER BY n.tvcddate DESC, n.tvcdtimestart DESC';
        return $sql;
    }
}
