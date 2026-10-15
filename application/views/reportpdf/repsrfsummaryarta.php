<!DOCTYPE html>
<html>

<head>
    <title>
        <?= 'Service Request Report as of ' . date('F d, Y H:i:s'); ?>
    </title>

    <link rel="stylesheet" media="print"
        href="<?= base_url(); ?>assets/css/pdfprint.css">

    <style>
        .table th,
        .table td {
            font-size: 2.8mm;
        }

        .wd0 {
            width: 10mm;
        }

        .wd1 {
            width: 28mm;
        }

        .wd2 {
            width: 28mm;
        }

        .wd3 {
            width: 58mm;
        }

        .wd4 {
            width: 20mm;
        }

        .wds1,
        .wds3 {
            width: 60mm;
        }

        .wds2 {
            width: 120mm;
        }
    </style>
</head>

<body>

    <htmlpagefooter name="footer">
        <div id="footer" style="text-align:center;font-size:3mm;">
            Page {PAGENO} of {nbpg}
        </div>

        <div id="footers" style="text-align:right;font-size:2.5mm;">
            <i>Date Generated: <?= date('F d, Y H:i:s'); ?></i>
        </div>
    </htmlpagefooter>

    <sethtmlpagefooter name="footer" value="on" />

    <div style="margin-bottom:10px;">
        <table style="width:100%;">
            <thead>
                <tr>
                    <th rowspan="4" style="text-align:right;width:90mm;">
                        <img src="<?= base_url(); ?>assets/img/dohlogo.png"
                            style="width:60px;">
                    </th>

                    <th style="width:95mm;">Republic of the Philippines</th>

                    <th rowspan="4" style="text-align:left;">
                        <img src="<?= base_url(); ?>assets/img/ro8logo.png"
                            style="width:60px;">
                        <img src="<?= base_url(); ?>assets/img/f1plus.png"
                            style="width:60px;">
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
                    <th style="color:#fff;">--</th>
                    <th></th>
                </tr>

                <tr>
                    <th></th>
                    <th>MSD-ICTU</th>
                    <th></th>
                </tr>

                <tr>
                    <th colspan="3">
                        ARTA SUMMARY <?= html_escape($mm_g . ' ' . $yy_g); ?>
                    </th>
                </tr>
            </thead>
        </table>
    </div>

    <?php
    /*
|--------------------------------------------------------------------------
| Working-time calculation helpers
|--------------------------------------------------------------------------
| Working hours: Monday-Friday, 8:00 AM-12:00 PM and 1:00 PM-5:00 PM.
| One working day = 8 hours.
*/

    if (!function_exists('totalNumberOfSeconds')) {
        function totalNumberOfSeconds($start, $end)
        {
            if (
                !is_string($start) ||
                !is_string($end) ||
                trim($start) === '' ||
                trim($end) === ''
            ) {
                return 0;
            }

            $startTimestamp = strtotime($start);
            $endTimestamp = strtotime($end);

            if (
                $startTimestamp === false ||
                $endTimestamp === false ||
                $endTimestamp <= $startTimestamp
            ) {
                return 0;
            }

            return (int) ($endTimestamp - $startTimestamp);
        }
    }

    if (!function_exists('totalNumberOfDays')) {
        function totalNumberOfDays($start, $end)
        {
            if (
                !is_string($start) ||
                !is_string($end) ||
                trim($start) === '' ||
                trim($end) === ''
            ) {
                return '';
            }

            $startTimestamp = strtotime($start);
            $endTimestamp = strtotime($end);

            if (
                $startTimestamp === false ||
                $endTimestamp === false ||
                $endTimestamp <= $startTimestamp
            ) {
                return '';
            }

            $startDate = new DateTime(date('Y-m-d', $startTimestamp));
            $endDate = new DateTime(date('Y-m-d', $endTimestamp));

            $totalSeconds = 0;

            $periods = [
                ['08:00:00', '12:00:00'],
                ['13:00:00', '17:00:00']
            ];

            while ($startDate <= $endDate) {
                // Monday = 1, Sunday = 7
                $dayOfWeek = (int) $startDate->format('N');

                if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                    $date = $startDate->format('Y-m-d');

                    foreach ($periods as $period) {
                        $periodStart = strtotime($date . ' ' . $period[0]);
                        $periodEnd = strtotime($date . ' ' . $period[1]);

                        $actualStart = max(
                            $startTimestamp,
                            $periodStart
                        );

                        $actualEnd = min(
                            $endTimestamp,
                            $periodEnd
                        );

                        if ($actualEnd > $actualStart) {
                            $totalSeconds += (
                                $actualEnd - $actualStart
                            );
                        }
                    }
                }

                $startDate->modify('+1 day');
            }

            $totalSeconds = (int) $totalSeconds;

            $workingDaySeconds = 8 * 60 * 60;

            $days = intdiv($totalSeconds, $workingDaySeconds);
            $remainingSeconds = $totalSeconds % $workingDaySeconds;

            $hours = intdiv($remainingSeconds, 3600);
            $minutes = intdiv($remainingSeconds % 3600, 60);
            $seconds = $remainingSeconds % 60;

            $time = '';

            if ($days > 0) {
                $time .= $days . 'd ';
            }

            if ($hours > 0) {
                $time .= $hours . 'h ';
            }

            if ($minutes > 0) {
                $time .= $minutes . 'm ';
            }

            if ($seconds > 0) {
                $time .= $seconds . 's';
            }

            return trim($time);
        }
    }
    ?>

    <table class="table" cellpadding="5">
        <thead>
            <tr>
                <th class="wd0">#</th>
                <th class="wd1">Ticket #</th>
                <th class="wd2">Requesting Date</th>
                <th class="wd2">Received Date</th>
                <th class="wd2">For Action Date</th>
                <th class="wd2">Accomplished Date</th>
                <th class="wd3">Action Taken</th>
                <th class="wd4"># of Days/Hours/Minutes Received</th>
                <th class="wd4"># of Days/Hours/Minutes Action Taken</th>
                <th class="wd4"># of Days/Hours/Minutes Accomplished</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $len = is_array($res ?? null) ? count($res) : 0;

            for ($i = 0; $i < $len; $i++) {

                $row = $res[$i];

                $tlsrsid = explode(
                    '&#@s#&',
                    $row['tlsrsid_alias'] ?? ''
                );

                $takdate = explode(
                    '&#@s#&',
                    $row['takdateadded_alias'] ?? ''
                );

                $takdesc = explode(
                    '&#@s#&',
                    $row['takdesc'] ?? ''
                );

                $actoffHistory = explode(
                    '&#@s#&',
                    $row['actoff'] ?? ''
                );

                $actoffid = explode(
                    '&#@s#&',
                    $row['actoffid_alias'] ?? ''
                );

                $rdate = '';
                $rtime = '';

                $fadate = '';
                $fatime = '';

                $adate = '';
                $atime = '';
                $atakdesc = '';

                $ractoffid = '';
                $faactoffid = '';
                $aactoffid = '';

                // Follow the original report's status transition logic.
                $tlsrsid_temp = '-1';
                $tlsrsid_len = count($tlsrsid);

                for ($x = 0; $x < $tlsrsid_len; $x++) {

                    if ($x === 0) {
                        $tlsrsid_temp = $tlsrsid[$x];
                    } elseif ($tlsrsid[$x] != $tlsrsid_temp) {

                        $idx = $x - 1;
                        $status = (string) $tlsrsid_temp;
                        $historyTimestamp = !empty($takdate[$idx])
                            ? strtotime($takdate[$idx])
                            : false;

                        if ($historyTimestamp !== false) {

                            $historyOfficerId = $actoffid[$idx] ?? '';

                            $officerClass = (
                                (string) $historyOfficerId === (string) $off_g ||
                                (string) $off_g === '-1'
                            ) ? '' : 'verified';

                            if ($status === '1') {
                                $rdate = date('M d, Y', $historyTimestamp);
                                $rtime = date('H:i:s', $historyTimestamp);
                                $ractoffid = $officerClass;
                            } elseif ($status === '2') {
                                $fadate = date('M d, Y', $historyTimestamp);
                                $fatime = date('H:i:s', $historyTimestamp);
                                $faactoffid = $officerClass;
                            } elseif ($status === '3' || $status === '6') {
                                $adate = date('M d, Y', $historyTimestamp);
                                $atime = date('H:i:s', $historyTimestamp);

                                $atakdesc = ($status === '6')
                                    ? 'C A N C E L L E D'
                                    : ($takdesc[$idx] ?? '');

                                $aactoffid = $officerClass;
                            }
                        }

                        $tlsrsid_temp = $tlsrsid[$x];
                    }
                }

                // Process the final status entry, as in the original report.
                if ($tlsrsid_len > 0) {

                    $idx = $tlsrsid_len - 1;
                    $status = (string) $tlsrsid_temp;

                    $historyTimestamp = !empty($takdate[$idx])
                        ? strtotime($takdate[$idx])
                        : false;

                    if ($historyTimestamp !== false) {

                        $historyOfficerId = $actoffid[$idx] ?? '';

                        $officerClass = (
                            (string) $historyOfficerId === (string) $off_g ||
                            (string) $off_g === '-1'
                        ) ? '' : 'verified';

                        if ($status === '1') {
                            $rdate = date('M d, Y', $historyTimestamp);
                            $rtime = date('H:i:s', $historyTimestamp);
                            $ractoffid = $officerClass;
                        } elseif ($status === '2') {
                            $fadate = date('M d, Y', $historyTimestamp);
                            $fatime = date('H:i:s', $historyTimestamp);
                            $faactoffid = $officerClass;
                        } elseif ($status === '3' || $status === '6') {
                            $adate = date('M d, Y', $historyTimestamp);
                            $atime = date('H:i:s', $historyTimestamp);

                            $atakdesc = ($status === '6')
                                ? 'C A N C E L L E D'
                                : ($takdesc[$idx] ?? '');

                            $aactoffid = $officerClass;
                        }
                    }
                }

                // Preserve the original report's selected-status behavior.
                if (
                    (string) $tlsrsid_g === '1' ||
                    (string) $tlsrsid_g === '2'
                ) {
                    if (!empty($takdate[$idx ?? 0])) {
                        $timestamp = strtotime($takdate[$idx ?? 0]);

                        if ($timestamp !== false) {
                            $rdate = date('M d, Y', $timestamp);
                            $rtime = date('H:i:s', $timestamp);
                        }
                    }
                } elseif ((string) $tlsrsid_g === '3') {

                    if (!empty($takdate[$idx ?? 0])) {
                        $timestamp = strtotime($takdate[$idx ?? 0]);

                        if ($timestamp !== false) {
                            $rdate = date('M d, Y', $timestamp);
                            $rtime = date('H:i:s', $timestamp);

                            $lastStatus = (string) ($tlsrsid[$idx ?? 0] ?? '');

                            $atakdesc = ($lastStatus === '6')
                                ? 'C A N C E L L E D'
                                : ($takdesc[$idx ?? 0] ?? '');
                        }
                    }
                }

                $requestDate = $row['tsrdateadded'] ?? '';

                $receivedDateTime = (
                    $rdate !== '' && $rtime !== ''
                ) ? $rdate . ' ' . $rtime : '';

                $forActionDateTime = (
                    $fadate !== '' && $fatime !== ''
                ) ? $fadate . ' ' . $fatime : '';

                $accomplishedDateTime = (
                    $adate !== '' && $atime !== ''
                ) ? $adate . ' ' . $atime : '';

                $hrs_rec = totalNumberOfDays(
                    (string) $requestDate,
                    $receivedDateTime
                );

                $hrs_act = totalNumberOfDays(
                    $receivedDateTime,
                    $forActionDateTime
                );

                $hrs_acc = totalNumberOfDays(
                    $forActionDateTime,
                    $accomplishedDateTime
                );

                if ($forActionDateTime === '') {
                    $hrs_act = '';
                    $hrs_acc = '';
                }

                if ($accomplishedDateTime === '') {
                    $hrs_acc = '';
                }

                $ticketTimestamp = (
                    $requestDate !== '' ? strtotime($requestDate) : false
                );
            ?>
                <tr>
                    <td><?= $i + 1; ?></td>

                    <td>
                        <?= $ticketTimestamp !== false
                            ? date('ymdhis', $ticketTimestamp)
                            : ''; ?>
                    </td>

                    <td class="<?= html_escape($ractoffid); ?>">
                        <?= $ticketTimestamp !== false
                            ? date('M d, Y H:i:s', $ticketTimestamp)
                            : ''; ?>
                    </td>

                    <td class="<?= html_escape($ractoffid); ?>">
                        <?= html_escape($receivedDateTime); ?>
                    </td>

                    <td class="<?= html_escape($faactoffid); ?>">
                        <?= html_escape($forActionDateTime); ?>
                    </td>

                    <td class="<?= html_escape($aactoffid); ?>">
                        <?= html_escape($accomplishedDateTime); ?>
                    </td>

                    <td class="<?= html_escape($aactoffid); ?>"
                        style="text-align:left;">
                        <?= html_escape($atakdesc); ?>
                    </td>

                    <td class="<?= html_escape($faactoffid); ?>">
                        <?= html_escape((string) $hrs_rec); ?>
                    </td>

                    <td class="<?= html_escape($aactoffid); ?>">
                        <?= html_escape((string) $hrs_act); ?>
                    </td>

                    <td class="<?= html_escape($aactoffid); ?>">
                        <?= html_escape((string) $hrs_acc); ?>
                    </td>
                </tr>
            <?php
            }

            if ($len === 0) {
            ?>
                <tr>
                    <td colspan="10" class="text-center">
                        <i>No data available in table</i>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>

    <?php
    /*
|--------------------------------------------------------------------------
| Signature section
|--------------------------------------------------------------------------
| The controller must pass 'actofficer' to this view.
*/

    $actofficer = $actofficer ?? [];

    $officerName = $actofficer[0]['fullname'] ?? '';
    $officerPosition = $actofficer[0]['dbposition'] ?? '';

    if (
        (string) ($off_g ?? '-1') !== '-1' &&
        $officerName !== ''
    ) {
    ?>
        <br><br>

        <table style="width:100%;">
            <tbody>
                <tr>
                    <td class="wds1" style="text-align:left;">
                        Submitted by:
                    </td>

                    <td class="wds2"></td>

                    <td class="wds3" style="text-align:left;">
                        Noted by:
                    </td>
                </tr>

                <tr>
                    <td style="color:#fff;">s</td>
                    <td></td>
                    <td style="color:#fff;">s</td>
                </tr>

                <tr>
                    <td style="border-bottom:1px solid #222;">
                        <b><?= html_escape($officerName); ?></b>
                    </td>

                    <td></td>

                    <td style="border-bottom:1px solid #222;">
                        <b>ELMA BERNADETTE B. LEGO</b>
                    </td>
                </tr>

                <tr>
                    <td>
                        <?= html_escape(strtoupper($officerPosition)); ?>
                    </td>

                    <td></td>

                    <td>COMPUTER MAINTENANCE TECHNOLOGIST III</td>
                </tr>
            </tbody>
        </table>
    <?php
    }
    ?>

</body>

</html>