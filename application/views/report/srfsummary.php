<?php $this->load->view('page/headlink'); ?>
<style type="text/css">
    .table thead th, .table tbody td {
        vertical-align: middle;
    }
</style>
<body>
    <?php $this->load->view('page/header'); ?>
    <div id="wrapper" style="padding-left: 5px; padding-right: 5px;">
        <div class="bg-white px-2">
            <div class="row pt-2 pb-4">
                <div class="col-6 text-left" style="font-size: 1.2em;">
                    Generate Report: Consolidated Service Request Summary Report per Actions Taken
                </div>
                <div class="col-6 text-right">
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
                                        $sel = $yy_g == $i ? 'selected' : '';
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
                                        $sel = $off_g === $actoff[$i]['lid'] ? 'selected' : '';
                                ?>
                                        <option value="<?php echo $this->cryptography->encrypt($actoff[$i]['lid']);?>" <?php echo $sel;?>><?php echo $actoff[$i]['actoff'];?></option>
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
            
            <div class="container">
                <div class="col-12">
                    <table class="table table-striped table-bordered" style="text-align: center;">
                        <thead>
                            <tr>
                                <th rowspan="2">Actions Taken</th>
                                <th colspan="2">Number of Requests for ITE Support/Others</th>
                                <th colspan="2">Number of Requests for Technical Assistance</th>
                                <th rowspan="2">Number of Requests for Video Conference</th>
                                <th rowspan="2">Other Concerns</th>
                                <th rowspan="2">TOTAL</th>
                            </tr>
                            <tr>
                                <th>Hardware</th>
                                <th>Software</th>
                                <th>DOH HIS</th>
                                <th>DOH EV-CHD IN-HOUSE INFORMATION SYSTEM</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Received</td>
                                <td><?php echo isset($rcvd[0]['cnt_HW']) ? $rcvd[0]['cnt_HW'] : '';?></td>
                                <td><?php echo isset($rcvd[0]['cnt_SW']) ? $rcvd[0]['cnt_SW'] : '';?></td>
                                <td><?php echo isset($rcvd[0]['cnt_HIS']) ? $rcvd[0]['cnt_HIS'] : '';?></td>
                                <td><?php echo isset($rcvd[0]['cnt_IIS']) ? $rcvd[0]['cnt_IIS'] : '';?></td>
                                <td><?php echo isset($rcvd[0]['cnt_VC']) ? $rcvd[0]['cnt_VC'] : '';?></td>
                                <td><?php echo isset($rcvd[0]['cnt_OC']) ? $rcvd[0]['cnt_OC'] : '';?></td>
                                <td>
                                    <?php 
                                        echo isset($rcvd[0]['cnt_HW']) ? ($rcvd[0]['cnt_HW'] + $rcvd[0]['cnt_SW'] + $rcvd[0]['cnt_HIS'] + $rcvd[0]['cnt_IIS'] + $rcvd[0]['cnt_VC'] + $rcvd[0]['cnt_OC'] + $rcvd[0]['cnt_unk'] ) : '';
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>For Action</td>
                                <td><?php echo isset($actn[0]['cnt_HW']) ? $actn[0]['cnt_HW'] : '';?></td>
                                <td><?php echo isset($actn[0]['cnt_SW']) ? $actn[0]['cnt_SW'] : '';?></td>
                                <td><?php echo isset($actn[0]['cnt_HIS']) ? $actn[0]['cnt_HIS'] : '';?></td>
                                <td><?php echo isset($actn[0]['cnt_IIS']) ? $actn[0]['cnt_IIS'] : '';?></td>
                                <td><?php echo isset($actn[0]['cnt_VC']) ? $actn[0]['cnt_VC'] : '';?></td>
                                <td><?php echo isset($actn[0]['cnt_OC']) ? $actn[0]['cnt_OC'] : '';?></td>
                                <td>
                                    <?php 
                                        echo isset($actn[0]['cnt_HW']) ? ($actn[0]['cnt_HW'] + $actn[0]['cnt_SW'] + $actn[0]['cnt_HIS'] + $actn[0]['cnt_IIS'] + $actn[0]['cnt_VC'] + $actn[0]['cnt_OC'] + $actn[0]['cnt_unk'] ) : '';
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Accomplished</td>
                                <td><?php echo isset($accomp[0]['cnt_HW']) ? $accomp[0]['cnt_HW'] : '';?></td>
                                <td><?php echo isset($accomp[0]['cnt_SW']) ? $accomp[0]['cnt_SW'] : '';?></td>
                                <td><?php echo isset($accomp[0]['cnt_HIS']) ? $accomp[0]['cnt_HIS'] : '';?></td>
                                <td><?php echo isset($accomp[0]['cnt_IIS']) ? $accomp[0]['cnt_IIS'] : '';?></td>
                                <td><?php echo isset($accomp[0]['cnt_VC']) ? $accomp[0]['cnt_VC'] : '';?></td>
                                <td><?php echo isset($accomp[0]['cnt_OC']) ? $accomp[0]['cnt_OC'] : '';?></td>
                                <td>
                                    <?php 
                                        echo isset($accomp[0]['cnt_HW']) ? ($accomp[0]['cnt_HW'] + $accomp[0]['cnt_SW'] + $accomp[0]['cnt_HIS'] + $accomp[0]['cnt_IIS'] + $accomp[0]['cnt_VC'] + $accomp[0]['cnt_OC'] + $accomp[0]['cnt_unk'] ) : '';
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('page/footer'); ?>

</body>


<script type="text/javascript" src="<?php echo base_url() . 'assets/js/srfsummary.js';?>"></script>

