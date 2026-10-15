<?php $this->load->view('page/headlink'); ?>

<body>

    <?php $this->load->view('page/header'); ?>

    <div id="wrapper" style="padding-left: 5px; padding-right: 5px;">
        <div class="bg-white px-2">

            <?php
            /*
            |--------------------------------------------------------------------------
            | Helper: Format elapsed seconds
            |--------------------------------------------------------------------------
            */
            $formatDuration = function ($seconds) {
                if ($seconds === null || $seconds === '') {
                    return '-';
                }

                $seconds = max(0, (int) $seconds);

                $days = floor($seconds / 86400);
                $hours = floor(($seconds % 86400) / 3600);
                $minutes = floor(($seconds % 3600) / 60);
                $secs = $seconds % 60;

                if ($days > 0) {
                    return $days . 'd ' . $hours . 'h ' . $minutes . 'm';
                }

                if ($hours > 0) {
                    return $hours . 'h ' . $minutes . 'm ' . $secs . 's';
                }

                if ($minutes > 0) {
                    return $minutes . 'm ' . $secs . 's';
                }

                return $secs . 's';
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
            ?>



            <div class="row">
                <div class="col-md-12">

                    <h3>
                        <i class="fa fa-clock-o"></i>
                        SRF Assignment &amp; Resolution Report
                    </h3>

                    <p class="text-muted">
                        Monitor service requests, assigned ICTU officers,
                        reassignment history, and resolution time.
                    </p>

                    <hr>

                    <!-- REPORT FILTERS -->

                    <form
                        method="get"
                        action="<?= site_url('report/srf-assignment-resolution'); ?>"
                        class="form-inline report-filter"
                        style="margin-bottom: 20px;">

                        <!-- Month -->
                        <div class="form-group" style="margin-right: 10px;">
                            <label for="mm">Month:</label>

                            <select name="mm" id="mm" class="form-control">
                                <?php foreach ($monthNames as $month): ?>
                                    <option
                                        value="<?= html_escape($month); ?>"
                                        <?= $mm_g === $month ? 'selected' : ''; ?>>
                                        <?= html_escape($month); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Year -->
                        <div class="form-group" style="margin-right: 10px;">
                            <label for="yy">Year:</label>

                            <select name="yy" id="yy" class="form-control">
                                <?php
                                $currentYear = (int) date('Y');
                                $selectedYear = (int) $yy_g;

                                $startYear = min($currentYear, $selectedYear) - 5;
                                $endYear = max($currentYear, $selectedYear) + 1;
                                ?>

                                <?php for ($year = $endYear; $year >= $startYear; $year--): ?>
                                    <option
                                        value="<?= $year; ?>"
                                        <?= $selectedYear === $year ? 'selected' : ''; ?>>
                                        <?= $year; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Service -->
                        <div class="form-group" style="margin-right: 10px;">
                            <label for="serv">Service:</label>

                            <select name="serv" id="serv" class="form-control">

                                <option
                                    value="<?= html_escape($this->cryptography->encrypt('-1')); ?>"
                                    <?= (string) $serv_g === '-1' ? 'selected' : ''; ?>>
                                    All Services
                                </option>

                                <option
                                    value="<?= html_escape($this->cryptography->encrypt('-2')); ?>"
                                    <?= (string) $serv_g === '-2' ? 'selected' : ''; ?>>
                                    All Corrective
                                </option>

                                <option
                                    value="<?= html_escape($this->cryptography->encrypt('-3')); ?>"
                                    <?= (string) $serv_g === '-3' ? 'selected' : ''; ?>>
                                    All Technical
                                </option>

                                <?php foreach ($srv as $service): ?>
                                    <option
                                        value="<?= html_escape(
                                                    $this->cryptography->encrypt(
                                                        (string) $service['tlsid']
                                                    )
                                                ); ?>"
                                        <?= (string) $serv_g === (string) $service['tlsid']
                                            ? 'selected'
                                            : ''; ?>>
                                        <?= html_escape($service['tlssdesc']); ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>

                        <!-- Generate -->
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i>
                            Generate
                        </button>

                        <!-- Print -->
                        <button
                            type="button"
                            id="myprint"
                            class="btn btn-default"
                            onclick="window.print();">
                            <i class="fa fa-print"></i>
                            Print
                        </button>

                    </form>

                    <!-- REPORT TABLE -->

                    <div class="table-responsive">

                        <table
                            id="assignmentResolutionTable"
                            class="table table-bordered table-striped table-hover">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ticket #</th>
                                    <th>Client</th>
                                    <th>Request Description</th>
                                    <th>Date Received</th>
                                    <th>Current Status</th>
                                    <th>Date Assigned</th>
                                    <th>Assigned ICTU Officer</th>
                                    <th>Assigned By</th>
                                    <th>Reassigned On</th>
                                    <th>Assignment Elapsed Time</th>
                                    <th>Date Completed</th>
                                    <th>Total Resolution Time</th>
                                    <th>Reassignments</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php if (!empty($res)): ?>

                                    <?php $counter = 1; ?>

                                    <?php foreach ($res as $row): ?>

                                        <?php
                                        /*
                                            |--------------------------------------------------------------------------
                                            | Received timestamp
                                            |--------------------------------------------------------------------------
                                            */
                                        $received = !empty($row['received_at'])
                                            ? $row['received_at']
                                            : ($row['tsrdateadded'] ?? null);

                                        /*
                                            |--------------------------------------------------------------------------
                                            | Completion timestamp
                                            |--------------------------------------------------------------------------
                                            */
                                        $completion = !empty($row['completion_date'])
                                            ? $row['completion_date']
                                            : null;

                                        /*
                                            |--------------------------------------------------------------------------
                                            | Total resolution duration
                                            |--------------------------------------------------------------------------
                                            */
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

                                        $isOngoing = empty($completion);

                                        $assignmentDate = !empty($row['assigned_date'])
                                            ? $row['assigned_date']
                                            : null;

                                        $nextAssignmentDate = !empty($row['next_assignment_date'])
                                            ? $row['next_assignment_date']
                                            : null;

                                        $clientName = trim($row['client_name'] ?? '');
                                        $assignedOfficer = trim($row['assigned_officer'] ?? '');
                                        $assignedByOfficer = trim($row['assigned_by_officer'] ?? '');
                                        ?>

                                        <tr>

                                            <!-- Row number -->
                                            <td><?= $counter++; ?></td>

                                            <!-- Ticket -->
                                            <td>
                                                <?= html_escape($row['tsrid'] ?? '-'); ?>
                                            </td>

                                            <!-- Client -->
                                            <td>
                                                <?= $clientName !== ''
                                                    ? html_escape($clientName)
                                                    : '-'; ?>
                                            </td>

                                            <!-- Request description -->
                                            <td>
                                                <?= !empty($row['tsrreqdesc'])
                                                    ? nl2br(html_escape($row['tsrreqdesc']))
                                                    : '-'; ?>
                                            </td>

                                            <!-- Date received -->
                                            <td>
                                                <?= !empty($received)
                                                    ? html_escape(date(
                                                        'M d, Y h:i A',
                                                        strtotime($received)
                                                    ))
                                                    : '-'; ?>
                                            </td>

                                            <!-- Current status -->
                                            <td>
                                                <?= html_escape(
                                                    $row['current_status'] ?? 'Pending'
                                                ); ?>
                                            </td>

                                            <!-- Date assigned -->
                                            <td>
                                                <?= $assignmentDate
                                                    ? html_escape(date(
                                                        'M d, Y h:i A',
                                                        strtotime($assignmentDate)
                                                    ))
                                                    : '-'; ?>
                                            </td>

                                            <!-- Assigned officer -->
                                            <td>
                                                <?= $assignedOfficer !== ''
                                                    ? html_escape($assignedOfficer)
                                                    : '-'; ?>
                                            </td>

                                            <!-- Assigned by -->
                                            <td>
                                                <?= $assignedByOfficer !== ''
                                                    ? html_escape($assignedByOfficer)
                                                    : '-'; ?>
                                            </td>

                                            <!-- Next assignment -->
                                            <td>
                                                <?= $nextAssignmentDate
                                                    ? html_escape(date(
                                                        'M d, Y h:i A',
                                                        strtotime($nextAssignmentDate)
                                                    ))
                                                    : '-'; ?>
                                            </td>

                                            <!-- Assignment elapsed time -->
                                            <td>
                                                <?= $formatDuration(
                                                    $row['assignment_duration_seconds'] ?? null
                                                ); ?>
                                            </td>

                                            <!-- Completion date -->
                                            <td>
                                                <?php if ($completion): ?>

                                                    <?= html_escape(date(
                                                        'M d, Y h:i A',
                                                        strtotime($completion)
                                                    )); ?>

                                                <?php elseif ($isOngoing): ?>

                                                    <span class="text-warning">
                                                        Ongoing
                                                    </span>

                                                <?php else: ?>

                                                    -

                                                <?php endif; ?>
                                            </td>

                                            <!-- Total resolution time -->
                                            <td>
                                                <?= $formatDuration($resolutionSeconds); ?>

                                                <?php if ($isOngoing && $resolutionSeconds !== null): ?>
                                                    <br>
                                                    <small class="text-warning">
                                                        Ongoing
                                                    </small>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Reassignment count -->
                                            <td>
                                                <?= max(
                                                    0,
                                                    (int) ($row['reassignment_count'] ?? 0)
                                                ); ?>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php else: ?>

                                    <tr>
                                        <td colspan="14" class="text-center">
                                            No service requests found for
                                            the selected filters.
                                        </td>
                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                    <!-- REPORT NOTES -->

                    <div class="row report-notes">
                        <div class="col-md-12">

                            <p class="text-muted">
                                <small>
                                    <strong>Notes:</strong>
                                    Assignment elapsed time measures the
                                    duration of an individual assignment.
                                    Total resolution time measures calendar
                                    time from receipt until completion, or
                                    until the current time for ongoing
                                    requests. These durations do not
                                    represent actual active working time.
                                    A request with multiple assignments may
                                    appear on multiple rows.
                                </small>
                            </p>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <style>
        #assignmentResolutionTable {
            width: 100%;
            font-size: 12px;
        }

        #assignmentResolutionTable th,
        #assignmentResolutionTable td {
            vertical-align: middle;
        }

        #assignmentResolutionTable th {
            white-space: nowrap;
        }

        #assignmentResolutionTable td {
            overflow-wrap: anywhere;
        }

        .report-notes {
            margin-top: 15px;
        }

        @media print {
            @page {
                size: landscape;
                margin: 8mm;
            }

            body {
                font-size: 9px !important;
            }

            #wrapper {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            .report-filter,
            #myprint,
            button,
            .btn {
                display: none !important;
            }

            .table-responsive {
                overflow: visible !important;
            }

            #assignmentResolutionTable {
                width: 100% !important;
                font-size: 8px !important;
            }

            #assignmentResolutionTable th,
            #assignmentResolutionTable td {
                padding: 3px !important;
            }

            #assignmentResolutionTable tr {
                page-break-inside: avoid;
            }
        }
    </style>

    <?php $this->load->view('page/footer'); ?>

    <script src="<?= base_url('assets/js/reportindex.js'); ?>"></script>

</body>