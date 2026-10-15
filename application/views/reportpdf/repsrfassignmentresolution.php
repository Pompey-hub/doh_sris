<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>
        SRF Assignment Resolution Report - <?= html_escape($mm_g . ' ' . $yy_g); ?>
    </title>

    <link rel="stylesheet" media="print"
        href="<?= base_url('assets/css/pdfprint.css'); ?>">

    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 8mm 14mm 8mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            color: #222;
        }

        table {
            border-collapse: collapse;
        }

        .report-header {
            width: 100%;
            margin-bottom: 12px;
        }

        .report-header th {
            text-align: center;
            padding: 2px;
        }

        .report-title {
            font-size: 13px;
            font-weight: bold;
            padding-top: 9px !important;
        }

        .report-subtitle {
            font-size: 10px;
            padding-top: 4px !important;
        }

        .report-filters {
            font-size: 9px;
            font-weight: normal;
            padding-top: 6px !important;
        }

        .report-table {
            width: 100%;
            table-layout: fixed;
            font-size: 7px;
        }

        .report-table th {
            padding: 5px 3px;
            background: #EAF4F0;
            border: 1px solid #9EBCB0;
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
        }

        .report-table td {
            padding: 5px 3px;
            border: 1px solid #D7DFDA;
            vertical-align: top;
            overflow-wrap: anywhere;
        }

        .ticket-col {
            width: 5%;
        }

        .request-col {
            width: 16%;
        }

        .received-col {
            width: 8%;
        }

        .status-col {
            width: 8%;
        }

        .officer-col {
            width: 9%;
        }

        .assigned-by-col {
            width: 8%;
        }

        .assigned-date-col {
            width: 8%;
        }

        .next-date-col {
            width: 8%;
        }

        .assign-time-col {
            width: 6%;
        }

        .completed-col {
            width: 8%;
        }

        .resolution-col {
            width: 8%;
        }

        .reassign-col {
            width: 4%;
        }

        .request-client {
            font-weight: bold;
            margin-bottom: 3px;
        }

        .request-description {
            font-size: 7px;
            white-space: pre-wrap;
        }

        .center {
            text-align: center;
        }

        .ongoing {
            font-weight: bold;
        }

        .empty-row {
            text-align: center;
            padding: 20px !important;
        }

        .report-notes {
            margin-top: 12px;
            font-size: 8px;
            line-height: 1.5;
        }

        .report-footer {
            width: 100%;
            font-size: 7px;
        }

        .muted {
            color: #666;
        }
    </style>
</head>

<body>

    <?php
    $generatedAt = date('F d, Y H:i:s');

    $formatDuration = function ($seconds) {
        if ($seconds === null || $seconds === '') {
            return '-';
        }

        $seconds = max(0, (int) $seconds);
        $days = (int) floor($seconds / 86400);
        $hours = (int) floor(($seconds % 86400) / 3600);
        $minutes = (int) floor(($seconds % 3600) / 60);

        if ($days > 0) {
            return $days . 'd ' . $hours . 'h';
        }

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }

        if ($minutes > 0) {
            return $minutes . 'm';
        }

        return $seconds . 's';
    };

    $formatDate = function ($date) {
        if (empty($date)) {
            return '-';
        }

        $timestamp = strtotime($date);

        return $timestamp !== false
            ? date('M d, Y h:i A', $timestamp)
            : '-';
    };

    $serviceLabel = 'All Services';

    if ((string) $serv_g === '-2') {
        $serviceLabel = 'All Corrective';
    } elseif ((string) $serv_g === '-3') {
        $serviceLabel = 'All Technical';
    } elseif ((string) $serv_g !== '-1') {
        foreach (($srv ?? []) as $service) {
            if ((string) $service['tlsid'] === (string) $serv_g) {
                $serviceLabel = $service['tlssdesc'];
                break;
            }
        }
    }

    $officerLabel = 'All Officers';

    if (!empty($officer_g)) {
        foreach (($officers ?? []) as $officer) {
            if ((string) $officer['id'] === (string) $officer_g) {
                $officerLabel = $officer['name'];
                break;
            }
        }
    }
    ?>

    <htmlpagefooter name="footer">
        <table class="report-footer">
            <tr>
                <td style="width: 50%; text-align: left;">
                    Date Generated: <?= html_escape($generatedAt); ?>
                </td>
                <td style="width: 50%; text-align: right;">
                    Page {PAGENO} of {nbpg}
                </td>
            </tr>
        </table>
    </htmlpagefooter>

    <sethtmlpagefooter name="footer" value="on" />

    <table class="report-header">
        <thead>
            <tr>
                <th rowspan="4" style="width: 20%; text-align: right;">
                    <img src="<?= base_url('assets/img/dohlogo.png'); ?>"
                        style="width: 48px;">
                </th>

                <th style="width: 60%;">
                    Republic of the Philippines
                </th>

                <th rowspan="4" style="width: 20%; text-align: left;">
                    <img src="<?= base_url('assets/img/ro8logo.png'); ?>"
                        style="width: 42px;">
                    <img src="<?= base_url('assets/img/additionallogo.png'); ?>"
                        style="width: 38px;">
                </th>
            </tr>

            <tr>
                <th>Department of Health</th>
            </tr>

            <tr>
                <th>EASTERN VISAYAS - CENTER FOR HEALTH DEVELOPMENT</th>
            </tr>

            <tr>
                <th>Government Center, Candahug, Palo, Leyte</th>
            </tr>

            <tr>
                <th></th>
                <th>MSD-ICTU</th>
                <th></th>
            </tr>

            <tr>
                <th colspan="3" class="report-title">
                    SRF Assignment Resolution Report
                </th>
            </tr>

            <tr>
                <th colspan="3" class="report-subtitle">
                    <?= html_escape($mm_g . ' ' . $yy_g); ?>
                </th>
            </tr>

            <tr>
                <th colspan="3" class="report-filters">
                    Service Category: <?= html_escape($serviceLabel); ?>
                    &nbsp; | &nbsp;
                    Assigned ICTU Officer: <?= html_escape($officerLabel); ?>
                </th>
            </tr>
        </thead>
    </table>

    <table class="report-table">
        <thead>
            <tr>
                <th class="ticket-col">Ticket #</th>
                <th class="request-col">Client / Request</th>
                <th class="received-col">Date Received</th>
                <th class="status-col">Current Status</th>
                <th class="officer-col">Assigned ICTU Officer</th>
                <th class="assigned-by-col">Assigned By</th>
                <th class="assigned-date-col">Date Assigned</th>
                <th class="next-date-col">Next Assignment</th>
                <th class="assign-time-col">Assignment Time</th>
                <th class="completed-col">Date Completed</th>
                <th class="resolution-col">Total Resolution Time</th>
                <th class="reassign-col">Reassignments</th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($res)): ?>
                <?php foreach ($res as $row): ?>

                    <?php
                    $received = !empty($row['received_at'])
                        ? $row['received_at']
                        : ($row['tsrdateadded'] ?? null);

                    $completion = !empty($row['completion_date'])
                        ? $row['completion_date']
                        : null;

                    $resolutionSeconds = null;

                    if (!empty($received)) {
                        $receivedTimestamp = strtotime($received);
                        $resolutionEnd = $completion
                            ? strtotime($completion)
                            : time();

                        if (
                            $receivedTimestamp !== false &&
                            $resolutionEnd !== false
                        ) {
                            $resolutionSeconds = max(
                                0,
                                $resolutionEnd - $receivedTimestamp
                            );
                        }
                    }

                    $ticketId = (string) ($row['tsrid'] ?? '');

                    $clientName = trim($row['client_name'] ?? '');
                    $requestDescription = $row['tsrreqdesc'] ?? '';

                    $status = trim($row['current_status'] ?? '');
                    if ($status === '') {
                        $status = 'Pending';
                    }

                    $assignedOfficer = trim($row['assigned_officer'] ?? '');
                    $assignedByOfficer = trim($row['assigned_by_officer'] ?? '');

                    $assignmentDate = $row['assigned_date'] ?? null;
                    $nextAssignmentDate = $row['next_assignment_date'] ?? null;

                    $reassignmentCount = max(
                        0,
                        (int) ($row['reassignment_count'] ?? 0)
                    );
                    ?>

                    <tr>
                        <td class="center">
                            <?= html_escape($ticketId !== '' ? '#' . $ticketId : '-'); ?>
                        </td>

                        <td>
                            <div class="request-client">
                                <?= html_escape(
                                    $clientName !== '' ? $clientName : 'Unknown Client'
                                ); ?>
                            </div>

                            <div class="request-description">
                                <?= html_escape(
                                    $requestDescription !== ''
                                        ? $requestDescription
                                        : 'No description provided'
                                ); ?>
                            </div>
                        </td>

                        <td><?= html_escape($formatDate($received)); ?></td>

                        <td><?= html_escape($status); ?></td>

                        <td>
                            <?= html_escape(
                                $assignedOfficer !== '' ? $assignedOfficer : '-'
                            ); ?>
                        </td>

                        <td>
                            <?= html_escape(
                                $assignedByOfficer !== '' ? $assignedByOfficer : '-'
                            ); ?>
                        </td>

                        <td><?= html_escape($formatDate($assignmentDate)); ?></td>

                        <td><?= html_escape($formatDate($nextAssignmentDate)); ?></td>

                        <td class="center">
                            <?= html_escape(
                                $formatDuration(
                                    $row['assignment_duration_seconds'] ?? null
                                )
                            ); ?>
                        </td>

                        <td>
                            <?php if ($completion): ?>
                                <?= html_escape($formatDate($completion)); ?>
                            <?php else: ?>
                                <span class="ongoing">Ongoing</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?= html_escape($formatDuration($resolutionSeconds)); ?>

                            <?php if (empty($completion)): ?>
                                <div class="muted">Still in progress</div>
                            <?php endif; ?>
                        </td>

                        <td class="center">
                            <?= $reassignmentCount; ?>
                        </td>
                    </tr>

                <?php endforeach; ?>

            <?php else: ?>
                <tr>
                    <td colspan="12" class="empty-row">
                        No data available for the selected filters.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="report-notes">
        <strong>Reporting Notes:</strong>
        Assignment time measures the duration of an individual assignment.
        Total resolution time measures calendar time from receipt until completion,
        or until the report generation time for ongoing requests. These durations
        do not represent actual active working time.
    </div>

</body>

</html>