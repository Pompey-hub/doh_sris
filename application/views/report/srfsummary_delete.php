<?php $this->load->view('page/headlink'); ?>
<link href="<?php echo base_url() . 'assets/css/dashboard.css';?>" rel="stylesheet">

<body>
    <?php $this->load->view('page/header'); ?>
    <div class="container" id="wrapper">
        <div class="row pb-4">
            <div class="col-3">
                <div class="panel panel-green">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-3">
                                <i class="fas fa-home fa-5x"></i>
                            </div>
                            <div class="col-9 text-right adjust-padd">
                                <div class="huge">9</div>
                                <div class="pnltxt">Pending</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-3">
                                <i class="fas fa-home fa-5x"></i>
                            </div>
                            <div class="col-9 text-right adjust-padd">
                                <div class="huge">2</div>
                                <div class="pnltxt">Received</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="panel panel-red">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-3">
                                <i class="fas fa-home fa-5x"></i>
                            </div>
                            <div class="col-9 text-right adjust-padd">
                                <div class="huge">150</div>
                                <div>For Action</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="panel panel-yellow">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-3">
                                <i class="fas fa-home fa-5x"></i>
                            </div>
                            <div class="col-9 text-right adjust-padd">
                                <div class="huge">161</div>
                                <div class="pnltxt">Accomplished</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


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
                            <select class="form-control" name="sid">
                                <option value="" disabled>Select status..</option>
                                <?php 
                                    $len = count($srstatus);
                                    for ($i=0; $i < $len; $i++) { 
                                        $sel = $sid_g === $srstatus[$i]['tlsrsid'] ? 'selected' : '';
                                ?>
                                        <option value="<?php echo $this->cryptography->encrypt($srstatus[$i]['tlsrsid']);?>" <?php echo $sel;?>><?php echo $srstatus[$i]['tlsrsdesc'];?></option>
                                <?php 
                                    }
                                ?>
                            </select>
                        </div>

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
                                        $sel = $yy_g === $i ? 'selected' : '';
                                ?>
                                        <option value="<?php echo $i;?>" <?php echo $sel;?>><?php echo $i;?></option>
                                <?php 
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group mx-1 mb-2">
                            <select class="form-control"  name="off">
                                <option value="" disabled>Select officer..</option>
                                <option value="<?php echo $this->cryptography->encrypt(-1);?>" <?php echo ($off_g === '-1' ? 'selected' : '');?>>All</option>
                                <?php 
                                    $len = count($actoff);
                                    for ($i=0; $i < $len; $i++) { 
                                        $sel = $off_g === $actoff[$i]['tllid'] ? 'selected' : '';
                                ?>
                                        <option value="<?php echo $this->cryptography->encrypt($actoff[$i]['tllid']);?>" <?php echo $sel;?>><?php echo $actoff[$i]['actoff'];?></option>
                                <?php 
                                    }
                                ?>
                            </select>
                        </div>
                         <div class="form-group mx-1 mb-2">
                            <select class="form-control"  name="serv">
                                <option value="" disabled>Select service..</option>
                                <option value="<?php echo $this->cryptography->encrypt(-1);?>" <?php echo ($serv_g === '-1' ? 'selected' : '');?>>All</option>
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
                        <button type="submit" class="btn btn-primary mx-1 mb-2"><i class="fas fa-print"></i> Print</button>
                    </form>
                </div>
            </div>
            

            <table class="table table-striped table-bordered" style="text-align: center;">
                <thead>
                    <tr>
                        <th rowspan="2">#</th>
                        <th colspan="3">Received</th>
                        <th colspan="3">For Action</th>
                        <th colspan="5">Accomplished</th>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Action Officer</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Action Officer</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Service</th>
                        <th>Action Taken</th>
                        <th>Action Officer</th>
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

                            $rdate = '';
                            $rtime = '';
                            $ractoff = '';
                            $fadate = '';
                            $fatime = '';
                            $faactoff = '';
                            $adate = '';
                            $atime = '';
                            $atakdesc = '';
                            $aactoff = '';


                            if (isset($tlsrsid[0])) {
                                $rdate = date('M d, Y', strtotime($takdate[0]));
                                $rtime = date('H:i', strtotime($takdate[0]));
                                $ractoff = $actoff[0];
                            } 

                            if (isset($tlsrsid[1])) {
                                $fadate = date('M d, Y', strtotime($takdate[1]));
                                $fatime = date('H:i', strtotime($takdate[1]));
                                $faactoff = $actoff[1];
                            } 

                            if (isset($tlsrsid[2])) {
                                $adate = date('M d, Y', strtotime($takdate[2]));
                                $atime = date('H:i', strtotime($takdate[2]));
                                $atakdesc = $takdesc[2];
                                $aactoff = $actoff[2];
                            }
                            
                    ?>
                            <tr>
                                <td><?php echo ($i+1);?></td>
                                <td><?php echo $rdate;?></td>
                                <td><?php echo $rtime;?></td>
                                <td><?php echo $ractoff;?></td>
                                <td><?php echo $fadate;?></td>
                                <td><?php echo $fatime;?></td>
                                <td><?php echo $faactoff;?></td>
                                <td><?php echo $adate;?></td>
                                <td><?php echo $atime;?></td>
                                <td><?php echo $res[$i]['tlssdesc'];?></td>
                                <td style="text-align: left;"><?php echo $atakdesc;?></td>
                                <td><?php echo $aactoff;?></td>
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

