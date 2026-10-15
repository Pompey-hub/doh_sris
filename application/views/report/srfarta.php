<?php $this->load->view('page/headlink'); ?>

<body>
    <?php $this->load->view('page/header'); ?>
    <div id="wrapper" style="padding-left: 5px; padding-right: 5px;">
        <div class="bg-white px-2">
            <div class="row pt-2 pb-4">
                <div class="col-2 text-left" style="font-size: 1.2em;">
                    Generate Report
                </div>
                <div class="col-10 text-right">
                    <?php
                    $mm = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                    ?>

                    <form class="form-inline" style="float: right;">

                        <div class="form-group mx-1 mb-2">
                            <select class="form-control" name="mm">
                                <option value="" disabled>Select month..</option>
                                <?php
                                $len = count($mm);
                                for ($i = 0; $i < $len; $i++) {
                                    $sel = $mm_g === $mm[$i] ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $mm[$i]; ?>" <?php echo $sel; ?>><?php echo $mm[$i]; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group mx-1 mb-2">
                            <select class="form-control" name="yy">
                                <option value="" disabled>Select year..</option>
                                <?php
                                $yy = date('Y');
                                for ($i = $yy; $i > 2018; $i--) {
                                    $sel = (string) $yy_g === (string) $i ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $i; ?>" <?php echo $sel; ?>><?php echo $i; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>


                        <!-- <div class="form-group mx-1 mb-2">
                            <select class="form-control" name="tlsrsid">
                                <option value="" disabled>Select Action taken...</option>
                                <option value="<?php #echo $this->cryptography->encrypt(-1);
                                                ?>" <?php #echo ($tlsrsid_g === '-1' ? 'selected' : '');
                                                    ?> >All</option>
                                <option value="<?php #echo $this->cryptography->encrypt(1);
                                                ?>" <?php #echo ($tlsrsid_g === '1' ? 'selected' : '');
                                                    ?> >Received</option>
                                <option value="<?php #echo $this->cryptography->encrypt(2);
                                                ?>" <?php #echo ($tlsrsid_g === '2' ? 'selected' : '');
                                                    ?> >For Action</option>
                                <option value="<?php #echo $this->cryptography->encrypt(3);
                                                ?>" <?php #echo ($tlsrsid_g === '3' ? 'selected' : '');
                                                    ?> >Accomplished</option>
                            </select>
                        </div> -->


                        <div class="form-group mx-1 mb-2">
                            <select class="form-control" name="off">
                                <option value="" disabled>Select officer..</option>
                                <option value="<?php echo $this->cryptography->encrypt(-1); ?>" <?php echo ($off_g === '-1' ? 'selected' : ''); ?>>All</option>
                                <?php
                                $len = count($actoff);
                                for ($i = 0; $i < $len; $i++) {
                                    $sel = $off_g === $actoff[$i]['lid'] ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $this->cryptography->encrypt($actoff[$i]['lid']); ?>" <?php echo $sel; ?>><?php echo $actoff[$i]['actoff']; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group mx-1 mb-2">
                            <select class="form-control" name="serv">
                                <option value="" disabled>Select service..</option>
                                <option value="<?php echo $this->cryptography->encrypt(-1); ?>" <?php echo ($serv_g === '-1' ? 'selected' : ''); ?>>All</option>
                                <?php
                                $len = count($srv);
                                for ($i = 0; $i < $len; $i++) {
                                    $sel = $serv_g === $srv[$i]['tlsid'] ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $this->cryptography->encrypt($srv[$i]['tlsid']); ?>" <?php echo $sel; ?>><?php echo $srv[$i]['tlssdesc']; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success mx-1 mb-2">Generate</button>
                        <a href="javascript:void(0);" class="btn btn-primary mx-1 mb-2" id="myprint"><i class="fas fa-print"></i> Print</a>
                    </form>
                </div>
            </div>


            <table class="table table-striped table-bordered" style="text-align: center;">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 10%;">Ticket #</th>
                        <th style="width: 10%;">Requesting Date</th>
                        <th style="width: 10%;">Received Date</th>
                        <th style="width: 10%;">For Action Date</th>
                        <th style="width: 10%;">Accomplished Date</th>
                        <th style="width: 15%;">Action Taken</th>

                        <th style="width: 10%;"># of Days/Hours/Minutes Received</th>
                        <th style="width: 10%;"># of Days/Hours/Minutes Action Taken</th>
                        <th style="width: 10%;"># of Days/Hours/Minutes Accomplished</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    function srfWorkingDuration($start, $end, $specialDays = [])
                    {
                        if (
                            empty($start) ||
                            empty($end) ||
                            strtotime($start) === false ||
                            strtotime($end) === false
                        ) {
                            return '';
                        }

                        $startTimestamp = strtotime($start);
                        $endTimestamp   = strtotime($end);

                        if ($startTimestamp > $endTimestamp) {
                            return '';
                        }

                        // Group special days by date.
                        $specialDaysByDate = [];

                        foreach ($specialDays as $specialDay) {
                            if (empty($specialDay['sd_date'])) {
                                continue;
                            }

                            $dateTimestamp = strtotime($specialDay['sd_date']);

                            if ($dateTimestamp === false) {
                                continue;
                            }

                            $date = date('Y-m-d', $dateTimestamp);

                            if (!isset($specialDaysByDate[$date])) {
                                $specialDaysByDate[$date] = [];
                            }

                            $specialDaysByDate[$date][] =
                                $specialDay['sd_start_time'] ?? null;
                        }

                        $totalSeconds = 0;

                        $currentDate = new DateTimeImmutable(
                            date('Y-m-d', $startTimestamp)
                        );

                        $lastDate = new DateTimeImmutable(
                            date('Y-m-d', $endTimestamp)
                        );

                        while ($currentDate <= $lastDate) {
                            $dateString = $currentDate->format('Y-m-d');
                            $dayOfWeek = (int) $currentDate->format('N');

                            // Exclude Saturday and Sunday.
                            if ($dayOfWeek >= 6) {
                                $currentDate = $currentDate->modify('+1 day');
                                continue;
                            }

                            $isFullDaySpecial = false;
                            $specialStart = null;

                            // Check special-day rules for this date.
                            if (isset($specialDaysByDate[$dateString])) {
                                foreach ($specialDaysByDate[$dateString] as $time) {
                                    // NULL or blank means a full-day special day.
                                    if ($time === null || trim((string) $time) === '') {
                                        $isFullDaySpecial = true;
                                        break;
                                    }

                                    $specialTimestamp = strtotime(
                                        $dateString . ' ' . $time
                                    );

                                    if (
                                        $specialTimestamp !== false &&
                                        (
                                            $specialStart === null ||
                                            $specialTimestamp < $specialStart
                                        )
                                    ) {
                                        $specialStart = $specialTimestamp;
                                    }
                                }
                            }

                            // Do not count any working time on full-day special days.
                            if ($isFullDaySpecial) {
                                $currentDate = $currentDate->modify('+1 day');
                                continue;
                            }

                            // Regular working periods, excluding lunch.
                            $periods = [
                                ['08:00:00', '12:00:00'],
                                ['13:00:00', '17:00:00']
                            ];

                            foreach ($periods as $period) {
                                $periodStart = strtotime(
                                    $dateString . ' ' . $period[0]
                                );

                                $periodEnd = strtotime(
                                    $dateString . ' ' . $period[1]
                                );

                                // Restrict this period to the requested interval.
                                $periodStart = max(
                                    $periodStart,
                                    $startTimestamp
                                );

                                $periodEnd = min(
                                    $periodEnd,
                                    $endTimestamp
                                );

                                // Stop counting at the partial special-day start time.
                                if ($specialStart !== null) {
                                    $periodEnd = min(
                                        $periodEnd,
                                        $specialStart
                                    );
                                }

                                if ($periodEnd > $periodStart) {
                                    $totalSeconds += $periodEnd - $periodStart;
                                }
                            }

                            $currentDate = $currentDate->modify('+1 day');
                        }

                        // Display duration using 8-hour working days.
                        $days = (int) floor($totalSeconds / 28800);
                        $remaining = $totalSeconds % 28800;

                        $hours = (int) floor($remaining / 3600);
                        $remaining %= 3600;

                        $minutes = (int) floor($remaining / 60);
                        $seconds = (int) ($remaining % 60);

                        $result = '';

                        if ($days > 0) {
                            $result .= $days . 'd ';
                        }

                        if ($hours > 0) {
                            $result .= $hours . 'h ';
                        }

                        if ($minutes > 0) {
                            $result .= $minutes . 'm ';
                        }

                        if ($seconds > 0) {
                            $result .= $seconds . 's';
                        }

                        return trim($result);
                    }

                    $len = count($res);
                    for ($i = 0; $i < $len; $i++) {
                        $tlsrsid = explode('&#@s#&', $res[$i]['tlsrsid_alias']);
                        $takdate = explode('&#@s#&', $res[$i]['takdateadded_alias']);
                        $takdesc = explode('&#@s#&', $res[$i]['takdesc']);
                        $actoff = explode('&#@s#&', $res[$i]['actoff']);
                        $actoffid = explode('&#@s#&', $res[$i]['actoffid_alias']);

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


                        $tlsrsid_temp = '-1';
                        $tlsrsid_len = count($tlsrsid);
                        for ($x = 0; $x < $tlsrsid_len; $x++) {
                            if ($x === 0) {
                                $tlsrsid_temp = $tlsrsid[$x];
                            } elseif ($tlsrsid[$x] != $tlsrsid_temp) {
                                if ($tlsrsid_temp == '1') {
                                    $rdate = date('M d, Y', strtotime($takdate[$x - 1]));
                                    $rtime = date('H:i:s', strtotime($takdate[$x - 1]));
                                    $ractoffid = $actoffid[$x - 1] == $off_g || $off_g == '-1' ? '' : 'verified';
                                } elseif ($tlsrsid_temp == '2') {
                                    $fadate = date('M d, Y', strtotime($takdate[$x - 1]));
                                    $fatime = date('H:i:s', strtotime($takdate[$x - 1]));
                                    $faactoffid = $actoffid[$x - 1] == $off_g || $off_g == '-1' ? '' : 'verified';
                                } elseif ($tlsrsid_temp == '3' || $tlsrsid_temp == '6') {
                                    $adate = date('M d, Y', strtotime($takdate[$x - 1]));
                                    $atime = date('H:i:s', strtotime($takdate[$x - 1]));
                                    $atakdesc = $tlsrsid[$x - 1] == '6' ? 'C A N C E L L E D' : $takdesc[$x - 1];
                                    $aactoffid = $actoffid[$x - 1] == $off_g || $off_g == '-1' ? '' : 'verified';
                                }

                                $tlsrsid_temp = $tlsrsid[$x];
                            }
                        }

                        if ($tlsrsid_temp == '1') {
                            $x = $tlsrsid_len > 0 ? $x - 1 : $x;
                            $rdate = date('M d, Y', strtotime($takdate[$x]));
                            $rtime = date('H:i:s', strtotime($takdate[$x]));
                            $ractoffid = $actoffid[$x] == $off_g || $off_g == '-1' ? '' : 'verified';
                        } elseif ($tlsrsid_temp == '2') {
                            $x = $tlsrsid_len > 0 ? $x - 1 : $x;
                            $fadate = date('M d, Y', strtotime($takdate[$x]));
                            $fatime = date('H:i:s', strtotime($takdate[$x]));
                            $faactoffid = $actoffid[$x] == $off_g || $off_g == '-1' ? '' : 'verified';
                        } elseif ($tlsrsid_temp == '3' || $tlsrsid_temp == '6') {
                            $x = $tlsrsid_len > 0 ? $x - 1 : $x;
                            $adate = date('M d, Y', strtotime($takdate[$x]));
                            $atime = date('H:i:s', strtotime($takdate[$x]));

                            $atakdesc = $tlsrsid[$x] == '6' ? 'C A N C E L L E D' : $takdesc[$x];
                            $aactoffid = $actoffid[$x] == $off_g || $off_g == '-1' ? '' : 'verified';
                        }

                        if ($tlsrsid_g === '1' || $tlsrsid_g === '2') {
                            $rdate = date('M d, Y', strtotime($takdate[$x]));
                            $rtime = date('H:i:s', strtotime($takdate[$x]));
                        } elseif ($tlsrsid_g === '3') {
                            $rdate = date('M d, Y', strtotime($takdate[$x]));
                            $rtime = date('H:i:s', strtotime($takdate[$x]));
                            $atakdesc = $tlsrsid[$x] == '6' ? 'C A N C E L L E D' : $takdesc[$x];
                        }


                        $specialDays = isset($special_days) && is_array($special_days)
                            ? $special_days
                            : [];

                        $hrs_rec = srfWorkingDuration(
                            $res[$i]['tsrdateadded'],
                            $rdate . ' ' . $rtime,
                            $specialDays
                        );

                        $hrs_act = srfWorkingDuration(
                            $rdate . ' ' . $rtime,
                            $fadate . ' ' . $fatime,
                            $specialDays
                        );

                        $hrs_acc = srfWorkingDuration(
                            $fadate . ' ' . $fatime,
                            $adate . ' ' . $atime,
                            $specialDays
                        );


                        if (strlen(trim($fadate)) === 0) {
                            $hrs_act = '';
                            $hrs_acc = '';
                        }

                        if (strlen(trim($adate)) === 0) {
                            $hrs_acc = '';
                        }

                    ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td><a href="<?php echo base_url() . ($res[$i]['tvcdid'] === NULL ? 'servicerequest' : 'servicerequestvidcon') . 'view?q=' . $this->cryptography->encrypt($res[$i]['tsrid']);; ?>"><?php echo date('ymdhis', strtotime($res[$i]['tsrdateadded'])); ?></a></td>

                            <td class="<?php echo $ractoffid; ?>"><?php echo date('M d, Y H:i:s', strtotime($res[$i]['tsrdateadded'])); ?></td>

                            <td class="<?php echo $ractoffid; ?>"><?php echo $rdate . ' ' . $rtime; ?></td>
                            <td class="<?php echo $faactoffid; ?>"><?php echo $fadate . ' ' . $fatime; ?></td>
                            <td class="<?php echo $aactoffid; ?>"><?php echo $adate . ' ' . $atime; ?></td>
                            <td class="<?php echo $aactoffid; ?>" style="text-align: left;"><?php echo $atakdesc; ?></td>

                            <td class="<?php echo $faactoffid; ?>"><?php echo $hrs_rec; ?></td>
                            <td class="<?php echo $aactoffid; ?>"><?php echo $hrs_act; ?></td>
                            <td class="<?php echo $aactoffid; ?>"><?php echo $hrs_acc; ?></td>
                        </tr>
                    <?php
                    }

                    if ($len === 0) {
                    ?>
                        <tr>
                            <td colspan="10" class="text-center"><i>No data available in table</i></td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php $this->load->view('page/footer'); ?>

</body>


<script type="text/javascript" src="<?php echo base_url() . 'assets/js/repsrfsummaryarta.js'; ?>"></script>