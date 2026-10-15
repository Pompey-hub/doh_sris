<!DOCTYPE html>
<html>
    <head>
        <title><?php echo 'Service Request Report as of ' . date('F d, Y H:i:s');?></title>
        <link rel="stylesheet" media="print" href="<?php echo base_url();?>assets/css/pdfprint.css">   
        <style>
            .wd1_static {
                width: 23mm;
            }
            .wd2_static {
                width: 13mm;
            }


            <?php if ($tlsrsid_g === '1' || $tlsrsid_g === '2') { ?>
                .wd1 {
                    width: 55mm;
                }
                .wd2 {
                    width: 45mm;
                }
                .wd3 {
                    width: 45mm;
                }
                .wd6 {
                    width: 55mm;
                }
            <?php } elseif ($tlsrsid_g === '3') { ?>
                .wd1 {
                    width: 23mm;
                }
                .wd2 {
                    width: 13mm;
                }
                .wd3 {
                    width: 20mm;
                }
                .wd4 {
                    width: 12mm;
                }
                .wd5 {
                    width: 90mm;
                }
                .wd6 {
                    width: 30mm;
                }
            <?php } else { ?>
                .wd1 {
                    width: 23mm;
                }
                .wd2 {
                    width: 13mm;
                }
                .wd3 {
                    width: 20mm;
                }
                .wd4 {
                    width: 12mm;
                }
                .wd5 {
                    width: 90mm;
                }
                .wd6 {
                    width: 30mm;
                }
            <?php } ?>

            


            <?php if ($tlsrsid_g === '-1') { ?>
                .wds1, .wds3 {
                    width: 60mm;
                }
                .wds2 {
                    width: 120mm;
                }
            <?php } else { ?>
                .wds1, .wds3 {
                    width: 60mm;
                }
                .wds2 {
                    width: 40mm;
                }
            <?php } ?>


            
        </style>
    </head>
    <body>
        <htmlpagefooter name="footer">
            <div id="footer" style="text-align: center; font-size: 3mm;"> Page {PAGENO} of {nbpg} </div>
            <div id="footers" style="text-align: right; font-size: 2.5mm;"><i>Date Generated: <?php echo date('F d, Y H:i:s');?></i></div>
        </htmlpagefooter>
        <sethtmlpagefooter name="footer" value="on" />
        <!-- <sethtmlpageheader name="header" value="on" show-this-page="1" /> -->

        <div style="margin-bottom: 10px;">
            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th rowspan="4" style="text-align: right; width: <?php echo $tlsrsid_g === '-1' ? '95mm' : '52mm';?>;"><img src="assets/img/dohlogo.png" style="width: 55px;" ></th>
                        <th style="width: 95mm;">Republic of the Philippines</th>
                        <th rowspan="4" style="text-align: left;">
                            <img src="assets/img/ro8logo.png" style="width: 50px;" >
                            <img src="assets/img/additionallogo.png" style="width: 45px;" >
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
                        <th style="color: #fff;">--</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>MSD-ICTU</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th colspan="3">Internal Service Request Report 
                            <?php 
                                if ($serv_g == '-1') {
                                    echo '(All)';
                                } elseif ($serv_g == '-2') {
                                    echo '(Corrective)';
                                } elseif ($serv_g == '-3') {
                                    echo '(Technical)';
                                } else {
                                    
                                }
                            ?>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="3"><?php echo $mm_g . ' ' . $yy_g;?></th>
                    </tr>
                </thead>
            </table>
        </div>

        <table class="table" cellpadding="5">
            <thead>
                <tr>
                    <th class="wd2" rowspan="2">#</th>
                    <th class="wd6" rowspan="2">Ticket #</th>
                    <th class="wd1" rowspan="2">Client</th>
                    <th colspan="2">Request</th>


                    <?php if ($tlsrsid_g === '1') { ?>
                        <th colspan="3">Received</th>
                    <?php } elseif ($tlsrsid_g === '2') { ?>
                        <th colspan="3">For Action</th>
                    <?php } elseif ($tlsrsid_g === '3') { ?>
                        <th colspan="5">Accomplished</th>
                    <?php } else { ?>
                        <th colspan="3">Received</th>
                        <th colspan="3">For Action</th>
                        <th colspan="5">Accomplished</th>
                    <?php } ?>
                </tr>
                <tr>
                    <th class="wd1_static">Date</th>
                    <th class="wd2_static">Time</th>


                    <?php if ($tlsrsid_g === '1') { ?>
                        <th class="wd1">Date</th>
                        <th class="wd2">Time</th>
                        <th class="wd3">Receiving Officer</th>
                    <?php } elseif ($tlsrsid_g === '2') { ?>
                        <th class="wd1">Date</th>
                        <th class="wd2">Time</th>
                        <th class="wd3">Action Officer</th>
                    <?php } elseif ($tlsrsid_g === '3') { ?>
                        <th class="wd1">Date</th>
                        <th class="wd2">Time</th>
                        <th class="wd4">Service</th>
                        <th class="wd5" >Action Taken</th>
                        <th class="wd3">Action Officer</th>
                    <?php } else { ?>
                        <th class="wd1">Date</th>
                        <th class="wd2">Time</th>
                        <th class="wd3">Receiving Officer</th>
                        <th class="wd1">Date</th>
                        <th class="wd2">Time</th>
                        <th class="wd3">Action Officer</th>
                        <th class="wd1">Date</th>
                        <th class="wd2">Time</th>
                        <th class="wd4">Service</th>
                        <th class="wd5" >Action Taken</th>
                        <th class="wd3">Action Officer</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $len = count($res);
                    for ($i=0; $i < $len; $i++) { 
                        $tlsrsid = explode('&#@s#&', $res[$i]['tlsrsid_alias']);
                        $takdate = explode('&#@s#&', $res[$i]['takdateadded_alias']);
                        $takdesc = explode('&#@s#&', $res[$i]['takdesc']);
                        $actoff = explode('&#@s#&', $res[$i]['actoff']);
                        $actoffid = explode('&#@s#&', $res[$i]['actoffid_alias']);
                
                        $rdate = ''; $rtime = ''; $ractoff = '';
                        $fadate = ''; $fatime = ''; $faactoff = '';
                        $adate = ''; $atime = ''; $atakdesc = ''; $aactoff = '';
                        $ractoffid = ''; $faactoffid = ''; $aactoffid = '';


                        $tlsrsid_temp = '-1';
                        $tlsrsid_len = count($tlsrsid);
                        for ($x=0; $x < $tlsrsid_len; $x++) {
                            if ($x === 0) {
                                $tlsrsid_temp = $tlsrsid[$x];
                            } elseif ($tlsrsid[$x] != $tlsrsid_temp) {
                                if ($tlsrsid_temp == '1') {
                                    $rdate = date('M d, Y', strtotime($takdate[$x-1]));
                                    $rtime = date('H:i', strtotime($takdate[$x-1]));
                                    $ractoff = $actoff[$x-1];
                                    $ractoffid = $actoffid[$x-1] == $off_g || $off_g == '-1'? '' : 'verified';
                                } elseif ($tlsrsid_temp == '2') {
                                    $fadate = date('M d, Y', strtotime($takdate[$x-1]));
                                    $fatime = date('H:i', strtotime($takdate[$x-1]));
                                    $faactoff = isset($actoff[$x-1]) ? $actoff[$x-1] : '-';
                                    $faactoffid = isset($actoff[$x-1]) && ($actoffid[$x-1] == $off_g || $off_g == '-1') ? '' : 'verified';
                                } elseif ($tlsrsid_temp == '3' || $tlsrsid_temp == '6') {
                                    $adate = date('M d, Y', strtotime($takdate[$x-1]));
                                    $atime = date('H:i', strtotime($takdate[$x-1]));
                                    $atakdesc = $tlsrsid[$x-1] == '6' ? 'C A N C E L L E D' : $takdesc[$x-1];
                                    $aactoff = $actoff[$x-1];
                                    $aactoffid = $actoffid[$x-1] == $off_g || $off_g == '-1' ? '' : 'verified';
                                } 

                                $tlsrsid_temp = $tlsrsid[$x];
                            }
                        }

                        if ($tlsrsid_temp == '1') {
                            $x = $tlsrsid_len > 0 ? $x-1 : $x;
                            $rdate = date('M d, Y', strtotime($takdate[$x]));
                            $rtime = date('H:i', strtotime($takdate[$x]));
                            $ractoff = $actoff[$x];
                            $ractoffid = $actoffid[$x] == $off_g || $off_g == '-1'? '' : 'verified';
                        } elseif ($tlsrsid_temp == '2') {
                            $x = $tlsrsid_len > 0 ? $x-1 : $x;
                            $fadate = date('M d, Y', strtotime($takdate[$x]));
                            $fatime = date('H:i', strtotime($takdate[$x]));
                            $faactoff = isset($actoff[$x]) ? $actoff[$x] : '-';
                            $faactoffid = isset($actoff[$x]) && ($actoffid[$x] == $off_g || $off_g == '-1') ? '' : 'verified';
                        } elseif ($tlsrsid_temp == '3' || $tlsrsid_temp == '6') {
                            $x = $tlsrsid_len > 0 ? $x-1 : $x;
                            $adate = date('M d, Y', strtotime($takdate[$x]));
                            $atime = date('H:i', strtotime($takdate[$x]));

                            $atakdesc = isset($tlsrsid[$x]) && $tlsrsid[$x] == '6' ? 'C A N C E L L E D' : (isset($takdesc[$x]) ? $takdesc[$x] : '' );
                            $aactoff = isset($actoff[$x]) ? $actoff[$x] : '';
                            $aactoffid = (isset($actoffid[$x]) && $actoffid[$x] == $off_g) || $off_g == '-1' ? '' : 'verified';
                        } 

                        if ($tlsrsid_g === '1' || $tlsrsid_g === '2') {
                            $rdate = date('M d, Y', strtotime($takdate[$x]));
                            $rtime = date('H:i', strtotime($takdate[$x]));
                            $ractoff = $actoff[$x];
                        } elseif ($tlsrsid_g === '3') {
                            $rdate = date('M d, Y', strtotime($takdate[$x]));
                            $rtime = date('H:i', strtotime($takdate[$x]));

                            $atakdesc = $tlsrsid[$x] == '6' ? 'C A N C E L L E D' : $takdesc[$x];
                            $aactoff = $actoff[$x];
                        }
                            
                        /*if (isset($tlsrsid[0])) {
                            $rdate = date('M d, Y', strtotime($takdate[0]));
                            $rtime = date('H:i', strtotime($takdate[0]));
                            $ractoff = $actoff[0];
                            $ractoffid = $actoffid[0] == $off_g || $off_g == '-1'? '' : 'verified';
                        } 

                        if (isset($tlsrsid[1])) {
                            $fadate = date('M d, Y', strtotime($takdate[1]));
                            $fatime = date('H:i', strtotime($takdate[1]));
                            $faactoff = $actoff[1];
                            $faactoffid = $actoffid[1] == $off_g || $off_g == '-1' ? '' : 'verified';
                        } 

                        if (isset($tlsrsid[2])) {
                            $adate = date('M d, Y', strtotime($takdate[2]));
                            $atime = date('H:i', strtotime($takdate[2]));
                            $atakdesc = $tlsrsid[2] == '6' ? 'C A N C E L L E D' : $takdesc[2];
                            $aactoff = $actoff[2];
                            $aactoffid = $actoffid[2] == $off_g || $off_g == '-1' ? '' : 'verified';
                        }

                        if ($tlsrsid_g === '3') {
                            $atakdesc = $tlsrsid[0] == '6' ? 'C A N C E L L E D' : $takdesc[0];
                            $aactoff = $actoff[0];
                        }*/
                        
                ?>
                        <tr>
                            <td><?php echo $i + 1;?></td>
                            <td><?php echo date('ymdhis', strtotime($res[$i]['tsrdateadded']));?></td>
                            <td><?php echo $res[$i]['uname'];?></td>

                            <td><?php echo date('M d, Y', strtotime($res[$i]['tsrdateadded']));?></td>
                            <td><?php echo date('H:i', strtotime($res[$i]['tsrdateadded']));?></td>

                            <?php if ($tlsrsid_g === '1' || $tlsrsid_g === '2') { ?>
                                <td class="<?php echo $ractoffid;?>"><?php echo $rdate;?></td>
                                <td class="<?php echo $ractoffid;?>"><?php echo $rtime;?></td>
                                <td class="<?php echo $ractoffid;?>"><?php echo $ractoff;?></td>
                            <?php } elseif ($tlsrsid_g === '3') { ?>
                                <td class="<?php echo $ractoffid;?>"><?php echo $rdate;?></td>
                                <td class="<?php echo $ractoffid;?>"><?php echo $rtime;?></td>
                                <td class="<?php echo $aactoffid;?>"><?php echo (isset($res[$i]['tlssdesc']) ? $res[$i]['tlssdesc'] : '');?></td>
                                <td class="<?php echo $aactoffid;?>" style="text-align: left;"><?php echo htmlentities($atakdesc);?></td>
                                <td class="<?php echo $aactoffid;?>"><?php echo $aactoff;?></td>
                            <?php } else { ?>
                                <td class="<?php echo $ractoffid;?>"><?php echo $rdate;?></td>
                                <td class="<?php echo $ractoffid;?>"><?php echo $rtime;?></td>
                                <td class="<?php echo $ractoffid;?>"><?php echo $ractoff;?></td>
                                <td class="<?php echo $faactoffid;?>"><?php echo $fadate;?></td>
                                <td class="<?php echo $faactoffid;?>"><?php echo $fatime;?></td>
                                <td class="<?php echo $faactoffid;?>"><?php echo $faactoff;?></td>
                                <td class="<?php echo $aactoffid;?>"><?php echo $adate;?></td>
                                <td class="<?php echo $aactoffid;?>"><?php echo $atime;?></td>
                                <td class="<?php echo $aactoffid;?>"><?php echo (strlen(trim($adate)) === 0 ? '' : $res[$i]['tlssdesc']);?></td>
                                <td class="<?php echo $aactoffid;?>" style="text-align: left;"><?php echo htmlentities($atakdesc);?></td>
                                <td class="<?php echo $aactoffid;?>"><?php echo $aactoff;?></td>
                            <?php } ?>
                        </tr>
                <?php 
                    }

                    if ($len === 0) {
                ?>
                        <tr>
                            <td colspan="13" class="text-center"><i>No data available in table</i></td>
                        </tr>
                <?php 
                    }
                ?>
            </tbody>
        </table>

        <?php if ($off != '-1') { ;?>
            <br><br>
            <table style="width: 100%;">
                <tbody>
                    <tr>
                        <td class="wds1" style="text-align: left;">Submitted by:</td>
                        <td class="wds2"></td>
                        <td class="wds3" style="text-align: left;">Noted by:</td>
                    </tr>
                    <tr>
                        <td style="color: #fff;">s</td>
                        <td></td>
                        <td style="color: #fff;">s</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #222;"><b><?php echo $actofficer[0]['fullname'];?></b></td>
                        <td></td>
                        <td style="border-bottom: 1px solid #222;"><b>ELMA BERNADETTE B. LEGO</b></td>
                    </tr>
                    <tr>
                        <td><?php echo strtoupper($actofficer[0]['dbposition']);?></td>
                        <td></td>
                        <td>COMPUTER MAINTENANCE TECHNOLOGIST III</td>
                    </tr>
                </tbody>
            </table>   
        <?php } ?>
        
    </body>
</html>
