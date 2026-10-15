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
                                    for ($i=0; $i < $len; $i++) { 
                                        $sel = $mm_g === $mm[$i] ? 'selected' : '';
                                ?>
                                        <option value="<?php echo $mm[$i];?>" <?php echo $sel;?>><?php echo $mm[$i];?></option>
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
                                    for ($i=$yy; $i > 2018; $i--) { 
                                        $sel = ($yy_g * 1 === $i ? 'selected' : '');
                                ?>
                                        <option value="<?php echo $i;?>" <?php echo $sel;?>><?php echo $i;?></option>
                                <?php 
                                    }
                                ?>
                            </select>
                        </div>

                        <div class="form-group mx-1 mb-2">
                            <select class="form-control" name="tlsrsid">
                                <option value="" disabled>Select Action taken...</option>
                                <option value="<?php echo $this->cryptography->encrypt(-1);?>" <?php echo ($tlsrsid_g === '-1' ? 'selected' : '');?> >All</option>
                                
                                <option value="<?php echo $this->cryptography->encrypt(1);?>" <?php echo ($tlsrsid_g === '1' ? 'selected' : '');?> >Received</option>
                                <option value="<?php echo $this->cryptography->encrypt(2);?>" <?php echo ($tlsrsid_g === '2' ? 'selected' : '');?> >For Action</option>
                                <option value="<?php echo $this->cryptography->encrypt(3);?>" <?php echo ($tlsrsid_g === '3' ? 'selected' : '');?> >Accomplished</option>
                            </select>
                        </div>


                        <div class="form-group mx-1 mb-2">
                            <select class="form-control"  name="off">
                                <option value="" disabled>Select officer..</option>
                                <option value="<?php echo $this->cryptography->encrypt(-1);?>" <?php echo ($off_g === '-1' ? 'selected' : '');?>>All</option>
                                <?php 
                                    $len = count($actoff);
                                    for ($i=0; $i < $len; $i++) { 
                                        $sel = $off_g === $actoff[$i]['lid'] ? 'selected' : '';
                                ?>
                                        <option value="<?php echo $this->cryptography->encrypt($actoff[$i]['lid']);?>" <?php echo $sel;?>><?php echo $actoff[$i]['actoff'];?></option>
                                <?php 
                                    }
                                ?>
                            </select>
                        </div>
                         <div class="form-group mx-1 mb-2">
                            <select class="form-control"  name="serv">
                                <option value="" disabled>Select service..</option>
                                <option value="<?php echo $this->cryptography->encrypt(-1);?>" <?php echo ($serv_g === '-1' ? 'selected' : '');?>>All</option>
                                <option value="<?php echo $this->cryptography->encrypt(-2);?>" <?php echo ($serv_g === '-2' ? 'selected' : '');?>>All (Corrective)</option>
                                <option value="<?php echo $this->cryptography->encrypt(-3);?>" <?php echo ($serv_g === '-3' ? 'selected' : '');?>>All (Technical)</option>
                                <?php 
                                    $len = count($srv);
                                    for ($i=0; $i < $len; $i++) { 
                                        $sel = $serv_g === $srv[$i]['tlsid'] ? 'selected' : '';
                                ?>
                                        <option value="<?php echo $this->cryptography->encrypt($srv[$i]['tlsid']);?>" <?php echo $sel;?>><?php echo $srv[$i]['tlssdesc'];?></option>
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
                        <th rowspan="2">#</th>
                        <th rowspan="2">Ticket #</th>
                        <th rowspan="2">Client</th>
                        <th rowspan="2">Requesting Date</th>

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
                        <?php if ($tlsrsid_g === '1') { ?>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Receiving Officer</th>
                        <?php } elseif ($tlsrsid_g === '2') { ?>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Action Officer</th>
                        <?php } elseif ($tlsrsid_g === '3') { ?>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Service</th>
                            <th style="width: 30%;">Action Taken</th>
                            <th>Action Officer</th>
                        <?php } else { ?>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Receiving Officer</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Action Officer</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Service</th>
                            <th style="width: 30%;">Action Taken</th>
                            <th>Action Officer</th>
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
                            $takmacaddress = explode('&#@s#&', $res[$i]['takmacaddress']);
                            $actoff = explode('&#@s#&', $res[$i]['actoff']);
                            $actoffid = explode('&#@s#&', $res[$i]['actoffid_alias']);
                    
                            $rdate = ''; $rtime = ''; $ractoff = '';
                            $fadate = ''; $fatime = ''; $faactoff = '';
                            $adate = ''; $atime = ''; $atakdesc = ''; $aactoff = '';
                            $ractoffid = ''; $faactoffid = ''; $aactoffid = '';

                            $mac_address_value = '<br>' . (isset($takmacaddress[0]) && $takmacaddress[0] != null ? 'MAC ADDRESS: ' . $takmacaddress[0] : '' );

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

                                #$atakdesc = $tlsrsid[$x] == '6' ? 'C A N C E L L E D' : $takdesc[$x];
				 $atakdesc = isset($tlsrsid[$x]) && $tlsrsid[$x] == '6' ? 'C A N C E L L E D' : (isset($takdesc[$x]) ? $takdesc[$x] : '' );
				$aactoff = $actoff[$x];
                            }
                    ?>
                            <tr>
                                <td><?php echo $i+1;?></td>
                                <td><a href="<?php echo base_url().($res[$i]['tvcdid'] === NULL ? 'servicerequest' : 'servicerequestvidcon') . 'view?q=' . $this->cryptography->encrypt($res[$i]['tsrid']);; ?>"><?php echo date('ymdHis', strtotime($res[$i]['tsrdateadded']));?></a></td>
                                <td><?php echo $res[$i]['uname'];?></td>
                                <td><?php echo date('M d, Y H:i:s', strtotime($res[$i]['tsrdateadded']));?></td>
                                <?php if ($tlsrsid_g === '1' || $tlsrsid_g === '2') { ?>
                                    <td class="<?php echo $ractoffid;?>"><?php echo $rdate;?></td>
                                    <td class="<?php echo $ractoffid;?>"><?php echo $rtime;?></td>
                                    <td class="<?php echo $ractoffid;?>"><?php echo $ractoff;?></td>
                                <?php } elseif ($tlsrsid_g === '3') { ?>
                                    <td class="<?php echo $ractoffid;?>"><?php echo $rdate;?></td>
                                    <td class="<?php echo $ractoffid;?>"><?php echo $rtime;?></td>
                                    <td class="<?php echo $aactoffid;?>"><?php echo (isset($res[$i]['tlssdesc']) ? $res[$i]['tlssdesc'] : '');?></td>
                                    <td class="<?php echo $aactoffid;?>" style="text-align: left;"><?php echo $atakdesc . $mac_address_value;?></td>
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
                                    <td class="<?php echo $aactoffid;?>" style="text-align: left;"><?php echo $atakdesc . $mac_address_value;?></td>
                                    <td class="<?php echo $aactoffid;?>"><?php echo $aactoff;?></td>
                                <?php } ?>
                            </tr>
                    <?php 
                        }

                        if ($len === 0) {
                    ?>
                            <tr>
                                <td colspan="12" class="text-center"><i>No data available in table</i></td>
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


<script type="text/javascript" src="<?php echo base_url() . 'assets/js/reportindex.js';?>"></script>

