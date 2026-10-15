<?php $this->load->view('page/headlink'); ?>

<body>
    <?php $this->load->view('page/header'); ?>

    <?php
    $formatDuration = function ($seconds) {
        if ($seconds === null || $seconds === '') return '-';
        $seconds = max(0, (int) $seconds);
        $days = (int) floor($seconds / 86400);
        $hours = (int) floor(($seconds % 86400) / 3600);
        $minutes = (int) floor(($seconds % 3600) / 60);
        if ($days > 0) return $days . 'd ' . $hours . 'h';
        if ($hours > 0) return $hours . 'h ' . $minutes . 'm';
        if ($minutes > 0) return $minutes . 'm';
        return $seconds . 's';
    };

    $formatDate = function ($date) {
        if (empty($date)) return '-';
        $timestamp = strtotime($date);
        return $timestamp !== false ? date('M d, Y h:i A', $timestamp) : '-';
    };

    $monthNames = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
    ];
    $selectedOfficer = isset($officer_g) ? (string) $officer_g : '';
    $officerOptions = isset($officers) && is_array($officers) ? $officers : [];
    $actionLogs = isset($action_logs) && is_array($action_logs) ? $action_logs : [];
    ?>

    <div id="wrapper" class="srf-report-wrapper">
        <div class="container-fluid srf-report-container">
            <div class="report-panel">
                <div class="panel-heading-custom">
                    <div><i class="fa fa-sliders"></i> <strong>Report Filters</strong></div>
                    <span>Choose your reporting period, service, and assigned officer</span>
                </div>

                <form method="get" action="<?= site_url('report/srf-assignment-resolution'); ?>" class="report-filter-form">
                    <div class="row">
                        <div class="col-md-2 col-sm-6">
                            <div class="filter-field">
                                <label for="mm">Month</label>
                                <select name="mm" id="mm" class="form-control">
                                    <?php foreach ($monthNames as $month): ?>
                                        <option value="<?= html_escape($month); ?>" <?= $mm_g === $month ? 'selected' : ''; ?>><?= html_escape($month); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2 col-sm-6">
                            <div class="filter-field">
                                <label for="yy">Year</label>
                                <select name="yy" id="yy" class="form-control">
                                    <?php
                                    $currentYear = (int) date('Y');
                                    $selectedYear = (int) $yy_g;
                                    $startYear = min($currentYear, $selectedYear) - 5;
                                    $endYear = max($currentYear, $selectedYear) + 1;
                                    for ($year = $endYear; $year >= $startYear; $year--):
                                    ?>
                                        <option value="<?= $year; ?>" <?= $selectedYear === $year ? 'selected' : ''; ?>><?= $year; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="filter-field">
                                <label for="serv">Service Category</label>
                                <select name="serv" id="serv" class="form-control">
                                    <option value="<?= html_escape($this->cryptography->encrypt('-1')); ?>" <?= (string) $serv_g === '-1' ? 'selected' : ''; ?>>All Services</option>
                                    <option value="<?= html_escape($this->cryptography->encrypt('-2')); ?>" <?= (string) $serv_g === '-2' ? 'selected' : ''; ?>>All Corrective</option>
                                    <option value="<?= html_escape($this->cryptography->encrypt('-3')); ?>" <?= (string) $serv_g === '-3' ? 'selected' : ''; ?>>All Technical</option>
                                    <?php foreach ($srv as $service): ?>
                                        <option value="<?= html_escape($this->cryptography->encrypt((string) $service['tlsid'])); ?>" <?= (string) $serv_g === (string) $service['tlsid'] ? 'selected' : ''; ?>><?= html_escape($service['tlssdesc']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="filter-field">
                                <label for="officer">Assigned ICTU Officer</label>
                                <select name="officer" id="officer" class="form-control">
                                    <option value="">All Officers</option>
                                    <?php foreach ($officerOptions as $option): ?>
                                        <option value="<?= (int) $option['id']; ?>" <?= $selectedOfficer === (string) $option['id'] ? 'selected' : ''; ?>><?= html_escape($option['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-12">
                            <div class="filter-field filter-buttons">
                                <label>&nbsp;</label>

                                <button
                                    type="submit"
                                    class="btn btn-generate">
                                    <i class="fa fa-search"></i> Generate
                                </button>


                                <button
                                    type="submit"
                                    id="printAssignmentResolution"
                                    class="btn btn-print"
                                    formaction="<?= site_url('report/print/srfassignmentresolution'); ?>"
                                    formmethod="get"
                                    formtarget="_blank">
                                    <i class="fa fa-print"></i> Print Report
                                </button>


                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="report-panel table-panel">
                <div class="panel-heading-custom table-panel-heading">
                    <div><i class="fa fa-list-alt"></i> <strong>Service Request Details</strong></div>
                    <span><?= number_format(count($res)); ?> assignment record(s)</span>
                </div>

                <div class="table-responsive">
                    <table id="assignmentResolutionTable" class="table table-hover srf-report-table">
                        <thead>
                            <tr>
                                <th>Ticket #</th>
                                <th>Client / Request</th>
                                <th>Date Received</th>
                                <th>Current Status</th>
                                <th>Assigned ICTU Officer</th>
                                <th>Assigned By</th>
                                <th>Date Assigned</th>
                                <th>Next Assignment</th>
                                <th>Assignment Time</th>
                                <th>Date Completed</th>
                                <th>Total Resolution Time</th>
                                <th>Reassignments</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($res)): ?>
                                <?php foreach ($res as $row): ?>
                                    <?php
                                    $received = !empty($row['received_at']) ? $row['received_at'] : ($row['tsrdateadded'] ?? null);
                                    $completion = !empty($row['completion_date']) ? $row['completion_date'] : null;
                                    $resolutionSeconds = null;
                                    if (!empty($received)) {
                                        $receivedTimestamp = strtotime($received);
                                        $resolutionEnd = $completion ? strtotime($completion) : time();
                                        if ($receivedTimestamp !== false && $resolutionEnd !== false) {
                                            $resolutionSeconds = max(0, $resolutionEnd - $receivedTimestamp);
                                        }
                                    }
                                    $isOngoing = empty($completion);
                                    $assignmentDate = $row['assigned_date'] ?? null;
                                    $nextAssignmentDate = $row['next_assignment_date'] ?? null;
                                    $clientName = trim($row['client_name'] ?? '');
                                    $assignedOfficer = trim($row['assigned_officer'] ?? '');
                                    $assignedByOfficer = trim($row['assigned_by_officer'] ?? '');
                                    $status = trim($row['current_status'] ?? 'Pending');
                                    $statusLower = strtolower($status);
                                    if (strpos($statusLower, 'complete') !== false || strpos($statusLower, 'accomplished') !== false || strpos($statusLower, 'resolved') !== false || strpos($statusLower, 'closed') !== false) {
                                        $statusClass = 'status-completed';
                                    } elseif (strpos($statusLower, 'pending') !== false || $status === '') {
                                        $statusClass = 'status-pending';
                                    } else {
                                        $statusClass = 'status-ongoing';
                                    }
                                    $reassignmentCount = max(0, (int) ($row['reassignment_count'] ?? 0));
                                    $ticketId = (string) ($row['tsrid'] ?? '');
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="<?= base_url('servicerequestview') . '?q=' . urlencode($this->cryptography->encrypt($ticketId)); ?>"
                                                target="_blank"
                                                title="View Service Request Details">
                                                <?= html_escape(date('ymdhis', strtotime($row['tsrdateadded']))); ?>
                                            </a>
                                        </td>

                                        <td class="request-cell">
                                            <strong><?= $clientName !== '' ? html_escape($clientName) : 'Unknown Client'; ?></strong>
                                            <div class="request-description"><?= !empty($row['tsrreqdesc']) ? nl2br(html_escape($row['tsrreqdesc'])) : 'No description provided'; ?></div>
                                        </td>
                                        <td class="date-cell"><?= html_escape($formatDate($received)); ?></td>
                                        <td>
                                            <button type="button" class="status-badge status-link <?= $statusClass; ?>" data-ticket-id="<?= html_escape($ticketId); ?>" data-status="<?= html_escape($status); ?>">
                                                <?= html_escape($status !== '' ? $status : 'Pending'); ?> <i class="fa fa-history"></i>
                                            </button>
                                        </td>
                                        <td><?= $assignedOfficer !== '' ? html_escape($assignedOfficer) : '-'; ?></td>
                                        <td><?= $assignedByOfficer !== '' ? html_escape($assignedByOfficer) : '-'; ?></td>
                                        <td class="date-cell"><?= html_escape($formatDate($assignmentDate)); ?></td>
                                        <td class="date-cell"><?= html_escape($formatDate($nextAssignmentDate)); ?></td>
                                        <td class="duration-cell"><?= $formatDuration($row['assignment_duration_seconds'] ?? null); ?></td>
                                        <td class="date-cell">
                                            <?php if ($completion): ?>
                                                <?= html_escape($formatDate($completion)); ?>
                                            <?php else: ?>
                                                <span class="ongoing-label"><i class="fa fa-clock-o"></i> Ongoing</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="duration-cell">
                                            <?= $formatDuration($resolutionSeconds); ?>
                                            <?php if ($isOngoing && $resolutionSeconds !== null): ?><div class="duration-subtext">Still in progress</div><?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($reassignmentCount > 0): ?><span class="reassignment-badge"><?= $reassignmentCount; ?></span><?php else: ?><span class="no-reassignment">0</span><?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="12" class="empty-state"><i class="fa fa-folder-open-o"></i>
                                        <h4>No Service Requests Found</h4>
                                        <p>Try selecting a different month, year, service category, or assigned officer.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="report-legend">
                    <span class="legend-title">Status Guide:</span>
                    <span><i class="legend-dot dot-completed"></i> Completed</span>
                    <span><i class="legend-dot dot-ongoing"></i> In progress</span>
                    <span><i class="legend-dot dot-pending"></i> Pending</span>
                </div>
            </div>

            <div class="report-footer">
                <div><strong>Reporting Notes</strong>
                    <p>Assignment time measures the duration of an individual assignment. Total resolution time measures calendar time from receipt until completion, or until the current time for ongoing requests. These values do not represent actual active working time. A ticket with multiple assignments may appear on multiple rows.</p>
                </div>
                <div class="report-footer-date">Generated on<br><strong><?= date('M d, Y h:i A'); ?></strong></div>
            </div>
        </div>
    </div>


    <div id="actionLogModal" class="action-log-modal" aria-hidden="true">
        <div class="action-log-backdrop" data-close-action-log></div>

        <section class="action-log-dialog" role="dialog"
            aria-modal="true" aria-labelledby="actionLogTitle">

            <div class="action-log-header">
                <div>
                    <h3 id="actionLogTitle">Action Taken Log Status</h3>
                    <p id="actionLogTicket">Ticket #</p>
                </div>
                <button type="button" class="action-log-close"
                    aria-label="Close" data-close-action-log>&times;</button>
            </div>

            <div class="action-log-content">
                <div class="action-log-description">
                    <strong>Description of Request / Problem:</strong>
                    <p id="actionLogDescription">-</p>
                </div>

                <div class="table-responsive action-log-table-wrapper">
                    <table class="table table-striped action-log-table">
                        <thead>
                            <tr>
                                <th class="no-wrap">Date</th>
                                <th class="no-wrap">Time</th>
                                <th class="no-wrap">Status</th>
                                <th>Action Taken</th>
                                <th>Remarks</th>
                                <th class="no-wrap">Previous Assignee</th>
                                <th class="no-wrap">Current Assignee</th>
                                <th class="no-wrap">Assignment Updated By</th>
                                <th class="no-wrap">Log Recorded By</th>
                            </tr>
                        </thead>
                        <tbody id="tbodysrlogstatus">
                            <tr>
                                <td colspan="9" class="log-empty">
                                    Select a ticket status to view its history.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="action-log-footer">
                <button type="button" class="btn btn-generate"
                    data-close-action-log>Close</button>
            </div>
        </section>
    </div>


    <style>
        :root {
            --srf-primary: #257A62;
            --srf-primary-dark: #1D6250;
            --srf-primary-light: #EAF4F0;
            --srf-primary-border: #CFE4DC;
            --srf-border: #E2E8E5;
            --srf-text: #263A34;
            --srf-muted: #71817B;
            --srf-amber: #B7791F
        }

        body {
            background: #F4F7F5;
            color: var(--srf-text)
        }

        .srf-report-wrapper {
            padding: 22px 15px 35px
        }

        .srf-report-container {
            max-width: 1900px;
            margin: 0 auto
        }

        .report-panel {
            margin-bottom: 20px;
            background: #fff;
            border: 1px solid var(--srf-border);
            border-radius: 9px;
            box-shadow: 0 2px 8px rgba(28, 47, 40, .035);
            overflow: hidden
        }

        .panel-heading-custom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 17px 20px;
            border-bottom: 1px solid var(--srf-border)
        }

        .panel-heading-custom>div {
            display: flex;
            align-items: center;
            gap: 9px;
            color: var(--srf-primary-dark);
            font-size: 14px;
            font-weight: 600
        }

        .panel-heading-custom>div i {
            color: var(--srf-primary)
        }

        .panel-heading-custom>span {
            color: var(--srf-muted);
            font-size: 11px
        }

        .report-filter-form {
            padding: 18px 20px 20px
        }

        .filter-field label {
            display: block;
            margin-bottom: 7px;
            color: #53665E;
            font-size: 12px;
            font-weight: 600
        }

        .filter-field .form-control {
            height: 39px;
            border: 1px solid #D9E3DE;
            border-radius: 6px;
            box-shadow: none;
            font-size: 13px
        }

        .filter-field .form-control:focus {
            border-color: var(--srf-primary);
            box-shadow: 0 0 0 2px rgba(37, 122, 98, .1)
        }

        .filter-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 8px
        }

        .filter-buttons label {
            width: 100%
        }

        .filter-buttons .btn {
            height: 39px;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600
        }

        .btn-generate {
            background: var(--srf-primary);
            border: 1px solid var(--srf-primary);
            color: #fff
        }

        .btn-generate:hover,
        .btn-generate:focus {
            background: var(--srf-primary-dark);
            border-color: var(--srf-primary-dark);
            color: #fff
        }

        .btn-print {
            background: #fff;
            border: 1px solid #D5E0DA;
            color: #41584E
        }

        .btn-print:hover,
        .btn-print:focus {
            background: var(--srf-primary-light);
            border-color: var(--srf-primary-border);
            color: var(--srf-primary-dark)
        }

        .table-panel-heading {
            background: #fff
        }

        .table-responsive {
            border: 0;
            margin: 0
        }

        #assignmentResolutionTable {
            width: 100% !important;
            margin: 0 !important;
            font-size: 12px;
            border-collapse: separate;
            border-spacing: 0
        }

        #assignmentResolutionTable thead th {
            padding: 14px 12px;
            background: var(--srf-primary-light);
            color: #315D4E;
            border-top: 0;
            border-bottom: 1px solid var(--srf-primary-border);
            border-right: 0;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .35px;
            white-space: nowrap;
            vertical-align: middle
        }

        #assignmentResolutionTable tbody td {
            padding: 13px 12px;
            border-top: 0;
            border-bottom: 1px solid #EDF1EE;
            border-right: 0;
            vertical-align: middle;
            color: #384A42;
            line-height: 1.55;
            overflow-wrap: anywhere
        }

        #assignmentResolutionTable tbody tr:hover {
            background: #F5FAF7
        }

        #assignmentResolutionTable tbody tr:last-child td {
            border-bottom: 0
        }

        .ticket-number {
            color: var(--srf-primary);
            font-weight: 700;
            white-space: nowrap
        }

        .request-cell {
            min-width: 220px;
            max-width: 320px
        }

        .request-cell>strong {
            display: block;
            margin-bottom: 4px;
            color: #263F34;
            font-size: 12px
        }

        .request-description {
            color: #77877F;
            font-size: 11px;
            white-space: normal
        }

        .date-cell {
            min-width: 130px;
            font-size: 11px
        }

        .duration-cell {
            min-width: 115px;
            color: #315D4E !important;
            font-weight: 700;
            white-space: nowrap
        }

        .duration-subtext {
            margin-top: 3px;
            color: var(--srf-amber);
            font-size: 10px;
            font-weight: 500
        }

        .status-badge {
            display: inline-block;
            padding: 5px 9px;
            border: 0;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            white-space: nowrap
        }

        .status-link {
            cursor: pointer
        }

        .status-link:hover {
            filter: brightness(.96);
            box-shadow: 0 0 0 2px rgba(37, 122, 98, .13)
        }

        .status-link .fa {
            margin-left: 4px;
            font-size: 10px;
            opacity: .7
        }

        .status-completed {
            background: #E5F5EB;
            color: #24794F
        }

        .status-ongoing {
            background: #FFF1D6;
            color: #99640F
        }

        .status-pending {
            background: #EDF1F6;
            color: #5E6C7E
        }

        .ongoing-label {
            color: var(--srf-amber);
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap
        }

        .reassignment-badge {
            display: inline-block;
            min-width: 25px;
            padding: 4px 7px;
            background: var(--srf-primary-light);
            border: 1px solid var(--srf-primary-border);
            border-radius: 20px;
            color: var(--srf-primary-dark);
            font-size: 11px;
            font-weight: 700
        }

        .no-reassignment {
            color: #9AA59F
        }

        .empty-state {
            padding: 55px 20px !important;
            text-align: center
        }

        .empty-state>i {
            color: #AAB8B0;
            font-size: 36px
        }

        .empty-state h4 {
            margin: 15px 0 7px;
            color: #354B40;
            font-size: 16px;
            font-weight: 700
        }

        .empty-state p {
            margin: 0;
            color: #87958E;
            font-size: 12px
        }

        .report-legend {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 17px;
            padding: 15px 20px;
            border-top: 1px solid var(--srf-border);
            color: #687A70;
            font-size: 11px
        }

        .legend-title {
            color: #40584B;
            font-weight: 700
        }

        .legend-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            margin-right: 5px;
            border-radius: 50%
        }

        .dot-completed {
            background: #25855A
        }

        .dot-ongoing {
            background: #D49A30
        }

        .dot-pending {
            background: #8B98A8
        }

        .report-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            padding: 5px 2px 15px;
            color: #7B8981;
            font-size: 11px
        }

        .report-footer strong {
            color: #53685C
        }

        .report-footer p {
            max-width: 850px;
            margin: 6px 0 0;
            line-height: 1.7
        }

        .report-footer-date {
            flex-shrink: 0;
            text-align: right;
            line-height: 1.8
        }


        .action-log-modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 99999;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .action-log-modal.is-open {
            display: flex;
        }

        .action-log-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(18, 35, 29, .6);
        }

        .action-log-dialog {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 1500px;
            max-height: 90vh;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 18px 60px rgba(0, 0, 0, .25);
            overflow: hidden;
        }

        .action-log-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
            padding: 20px 22px;
            border-bottom: 1px solid var(--srf-border);
        }

        .action-log-header h3 {
            margin: 0;
            color: var(--srf-primary-dark);
            font-size: 18px;
            font-weight: 700;
        }

        .action-log-header p {
            margin: 5px 0 0;
            color: var(--srf-muted);
            font-size: 12px;
        }

        .action-log-close {
            border: 0;
            background: transparent;
            color: #65766D;
            font-size: 28px;
            line-height: 1;
            cursor: pointer;
        }

        .action-log-content {
            padding: 18px 22px;
            overflow: auto;
        }

        .action-log-description {
            margin-bottom: 20px;
            padding: 14px 16px;
            background: #F7FAF8;
            border: 1px solid var(--srf-border);
            border-radius: 6px;
        }

        .action-log-description strong {
            display: block;
            margin-bottom: 7px;
            color: var(--srf-primary-dark);
            font-size: 12px;
        }

        .action-log-description p {
            margin: 0;
            color: var(--srf-text);
            font-size: 13px;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        .action-log-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .action-log-table {
            width: 100%;
            min-width: 1100px;
            margin-bottom: 0;
            border-collapse: collapse;
            font-size: 12px;
        }

        .action-log-table thead th {
            padding: 11px 10px;
            background: var(--srf-primary-light);
            color: var(--srf-primary-dark);
            border-bottom: 1px solid var(--srf-primary-border);
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            vertical-align: middle;
        }

        .action-log-table tbody td {
            padding: 10px;
            border-bottom: 1px solid #EDF1EE;
            vertical-align: top;
            line-height: 1.6;
            overflow-wrap: anywhere;
        }

        .action-log-table .no-wrap {
            white-space: nowrap;
        }

        .action-log-table .log-action {
            min-width: 180px;
            white-space: pre-wrap;
        }

        .action-log-table .log-remarks {
            min-width: 160px;
            white-space: pre-wrap;
        }

        .action-log-table .log-assignee {
            min-width: 130px;
        }

        .action-log-status {
            color: var(--srf-primary-dark);
            font-style: italic;
            font-weight: 600;
        }

        .action-log-additional td {
            background: #FAFCFB;
            color: #65766D;
        }

        .action-log-additional .additional-label {
            display: inline-block;
            margin-bottom: 4px;
            color: var(--srf-primary-dark);
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .log-empty {
            padding: 25px !important;
            text-align: center;
            color: var(--srf-muted);
        }

        .action-log-footer {
            padding: 12px 22px 18px;
            text-align: right;
            border-top: 1px solid var(--srf-border);
            flex-shrink: 0;
        }

        @media (max-width: 767px) {
            .action-log-modal {
                padding: 8px;
            }

            .action-log-dialog {
                max-height: 94vh;
            }

            .action-log-header,
            .action-log-content {
                padding: 14px;
            }

            .action-log-footer {
                padding: 12px 14px;
            }
        }


        @media print {
            @page {
                size: landscape;
                margin: 8mm
            }

            body {
                background: #fff !important;
                color: #222 !important;
                font-size: 9px !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact
            }

            #wrapper,
            .srf-report-wrapper {
                padding: 0 !important;
                margin: 0 !important
            }

            .srf-report-container {
                width: 100% !important;
                max-width: none !important;
                padding: 0 !important
            }

            .report-filter-form,
            #myprint,
            .filter-buttons,
            button,
            .btn,
            .navbar,
            header,
            footer,
            .action-log-modal {
                display: none !important
            }

            .report-panel {
                box-shadow: none !important;
                border-radius: 0 !important;
                margin-bottom: 10px !important;
                overflow: visible !important
            }

            .table-responsive {
                overflow: visible !important;
                width: 100% !important
            }

            #assignmentResolutionTable {
                width: 100% !important;
                font-size: 7px !important
            }

            #assignmentResolutionTable thead {
                display: table-header-group
            }

            #assignmentResolutionTable th,
            #assignmentResolutionTable td {
                padding: 5px 4px !important;
                font-size: 7px !important;
                white-space: normal !important;
                overflow-wrap: anywhere !important
            }

            #assignmentResolutionTable tr {
                break-inside: avoid;
                page-break-inside: avoid
            }

            .ticket-number,
            .status-badge,
            .reassignment-badge {
                white-space: normal !important
            }

            .report-footer {
                padding-top: 5px !important
            }

            .report-footer p {
                max-width: none !important
            }
        }
    </style>

    <script>
        (function() {
            var actionLogs = <?= json_encode(
                                    $actionLogs,
                                    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
                                ); ?> || {};

            var modal = document.getElementById('actionLogModal');
            var ticketLabel = document.getElementById('actionLogTicket');
            var descriptionElement = document.getElementById('actionLogDescription');
            var tableBody = document.getElementById('tbodysrlogstatus');

            function textValue(value, fallback) {
                if (value === null || value === undefined || String(value).trim() === '') {
                    return fallback || '-';
                }

                return String(value);
            }

            function addCell(row, value, className) {
                var cell = document.createElement('td');

                // Use textContent to prevent HTML injection from database values.
                cell.textContent = textValue(value);

                if (className) {
                    cell.className = className;
                }

                row.appendChild(cell);
                return cell;
            }

            function addEmptyRow(message) {
                var row = document.createElement('tr');
                var cell = document.createElement('td');

                cell.colSpan = 9;
                cell.className = 'log-empty';
                cell.textContent = message;

                row.appendChild(cell);
                tableBody.appendChild(row);
            }

            function addAdditionalRemarks(mainLog) {
                var remarks = mainLog.add_remarks;

                if (!Array.isArray(remarks) || remarks.length === 0) {
                    return;
                }

                remarks.forEach(function(remark) {
                    var row = document.createElement('tr');
                    row.className = 'action-log-additional';

                    addCell(row, remark.takdateadded || remark.date);
                    addCell(row, remark.taktimeadded || remark.time);

                    var statusCell = addCell(row, '-----');
                    statusCell.className = 'action-log-status';

                    addCell(row, '');

                    var remarkCell = document.createElement('td');
                    remarkCell.className = 'log-remarks';

                    var label = document.createElement('span');
                    label.className = 'additional-label';
                    label.textContent = 'Additional Remark';

                    var remarkText = document.createElement('div');

                    // Accommodate the field names returned by the remarks query.
                    remarkText.textContent = textValue(
                        remark.takremarks ||
                        remark.remarks ||
                        remark.remark ||
                        remark.add_remarks
                    );

                    remarkCell.appendChild(label);
                    remarkCell.appendChild(remarkText);
                    row.appendChild(remarkCell);

                    addCell(row, '-');
                    addCell(row, '-');
                    addCell(row, '-');
                    addCell(row, remark.officer || remark.recorded_by);

                    tableBody.appendChild(row);
                });
            }

            function addMainLog(log) {
                var row = document.createElement('tr');
                var status = textValue(log.tlsrsdesc || log.status, 'Unknown status');

                addCell(row, log.takdateadded || log.date, 'no-wrap');
                addCell(row, log.taktimeadded || log.time, 'no-wrap');

                var statusCell = addCell(row, status, 'action-log-status no-wrap');

                // Match the home/index.php layout: assignment columns are
                // omitted for "For Action" and "Accomplished" statuses.
                var statusLower = status.trim().toLowerCase();
                var hideAssignmentDetails =
                    statusLower === 'for action' ||
                    statusLower === 'accomplished';

                var actionCell = addCell(row, log.takdesc, 'log-action');
                var remarksCell = addCell(row, log.takremarks, 'log-remarks');

                if (hideAssignmentDetails) {
                    addCell(row, '');
                    addCell(row, log.assigned_officer);
                    addCell(row, '');
                } else {
                    addCell(row, log.old_officer, 'log-assignee');
                    addCell(row, log.assigned_officer, 'log-assignee');
                    addCell(row, log.assigned_by_officer, 'log-assignee');
                }

                addCell(row, log.officer, 'log-assignee');

                tableBody.appendChild(row);

                addAdditionalRemarks(log);
            }

            function openLog(ticketId, currentStatus) {
                var ticketData = actionLogs[ticketId] || {};
                var logs = Array.isArray(ticketData.logs) ? ticketData.logs : [];

                ticketLabel.textContent =
                    'Ticket #' + ticketId +
                    ' | Current Status: ' + textValue(currentStatus, 'Pending');

                descriptionElement.textContent = textValue(
                    ticketData.description,
                    'No description provided.'
                );

                tableBody.innerHTML = '';

                if (logs.length === 0) {
                    addEmptyRow(
                        'No Action Taken Log Status records were found for this ticket.'
                    );
                } else {
                    logs.forEach(function(log) {
                        addMainLog(log);
                    });
                }

                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }

            function closeLog() {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }

            // Event delegation supports all clickable status buttons in the report.
            document.addEventListener('click', function(event) {
                var button = event.target.closest('.status-link');

                if (button) {
                    openLog(
                        button.getAttribute('data-ticket-id') || '',
                        button.getAttribute('data-status') || 'Pending'
                    );
                    return;
                }

                if (event.target.closest('[data-close-action-log]')) {
                    closeLog();
                }
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                    closeLog();
                }
            });
        })();
    </script>

    <?php $this->load->view('page/footer'); ?>
    <script src="<?= base_url('assets/js/reportindex.js'); ?>"></script>
</body>