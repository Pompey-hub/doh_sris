<?php $this->load->view('page/headlink'); ?>
<?php  
    $host = 'none';
    if (isset($res[0]['tvcdusertype']) && $res[0]['tvcdusertype']==='Host') {
        $host = 'table';
    }

    $tlsrsid_arr = [4,5,8];
    if (isset($res[0]['tlsrsid']) && !in_array($res[0]['tlsrsid'], $tlsrsid_arr)) {
        $viewonly = true;
    }
?>

<style type="text/css">
    .table_ {
        width: 100%;
    }
    .table_ td, .table_ th {
        padding: .5rem;
        vertical-align: top;
    }
    .table_ {
        margin-bottom: 0;
    }

    .table_ th {
        /*background: #eff2f4;*/
        color: #0b8274;
        font-weight: 800;
    }
</style>
<body>
    <?php $this->load->view('page/header'); ?>
    <div id="wrapper">
        <div class="row">
            <div class="col-md-9">
                <div class="px-2">
                    <div class="row pt-2 pb-4">
                        <h5 class="text-uppercase">
                            <?php 
                                if (isset($viewonly) && $viewonly==true) {
                                    $viewonly_dis = 'disabled';
                                } else {
                                    $viewonly_dis = '';
                                    echo ($this->input->get('q') == NULL ? 'Add' : 'Edit') . '';
                                }
                            ?>
                            Service Request for Video Conference
                        </h5>
                    </div>

                    <?php $this->load->view('page/validation'); ?>
                    <form method="post" id="sr_form" class="form">
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>" />
                        <input type="hidden" name="tsrid" value="<?php echo $this->input->get('q');?>">
                        <input type="hidden" name="tvcdid" value="<?php echo isset($res[0]['tvcdid']) ? $this->cryptography->encrypt($res[0]['tvcdid']) : '';?>">

                        <table class="table_">
                            <tbody>
                                <tr>
                                    <th>Office</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                </tr>
                                <tr>
                                    <td>
                                        <?php 
                                            $office_det = isset($res[0]['office']) ? $res[0]['office'] : ($this->session->userdata('divabbrev') . '-' . $this->session->userdata('secabbrev'));
                                        ?>
                                        <input type="text" name="" class="form-control" value="<?php echo $office_det;?>" disabled>
                                    </td>
                                    <td><input type="text" name="" class="form-control" value="<?php echo date('F d, Y');?>" disabled></td>
                                    <td><input type="text" name="" class="form-control" value="Auto Generated" disabled></td>
                                </tr>

                                <tr>
                                    <th><i class="fas fa-asterisk fa-xs required"></i> Name of Client</th>
                                    <th><i class="fas fa-asterisk fa-xs required"></i> Mobile #</th>
                                    <th><i class="fas fa-asterisk fa-xs required"></i> Email Address</th>
                                </tr>
                                <tr>
                                    <td>
                                        <select name="tsrclient" id="tsrclient" class="form-control" <?php echo $viewonly_dis;?>>
                                            <option value="">Select..</option>
                                            <?php 
                                                $temp = isset($res[0]['tsrclient']) ? $res[0]['tsrclient'] : $this->cryptography->decrypt($this->session->userdata('empid'));
                                                $len = count($emp);
                                                for ($i=0; $i < $len; $i++) { 
                                                    $sel = ($temp == $emp[$i]['empid']) ? 'selected' : '';
                                            ?>
                                                    <option value="<?php echo $emp[$i]['empid'];?>" <?php echo $sel;?>>
                                                        <?php echo $emp[$i]['fname'] . ' ' . ($emp[$i]['mname']===NULL || $emp[$i]['mname']==='' ? '' : $emp[$i]['mname'][0] . '. ') . 
                                                            $emp[$i]['lname'] . ($emp[$i]['suffix']===NULL || $emp[$i]['suffix']==='' ? '' : $emp[$i]['suffix']) . 
                                                            ($emp[$i]['extname']===NULL || $emp[$i]['extname']==='' ? '' : ', ' . $emp[$i]['extname']) ;?>
                                                    </option>
                                            <?php 
                                                }
                                            ?>
                                        </select>
                                    </td>
                                    <td><input type="text" name="tsrmobilenum" class="form-control" value="<?php echo isset($res[0]['tsrmobilenum']) ? $res[0]['tsrmobilenum'] : '';?>"  placeholder="Start typing here.." <?php echo $viewonly_dis;?>></td>
                                    <td><input type="email" name="tsremail" class="form-control" value="<?php echo isset($res[0]['tsremail']) ? $res[0]['tsremail'] : '';?>"  placeholder="Start typing here.." <?php echo $viewonly_dis;?>></td>
                                </tr>
                            </tbody>
                        </table>
                        <br>
                        <table class="table_">
                            <tbody>
                                <tr>
                                    <th style="width: 20%;"><i class="fas fa-asterisk fa-xs required"></i> Record Request</th>
                                    <th style="width: 25%;"><i class="fas fa-asterisk fa-xs required"></i> Platform</th>
                                    <th style="width: 35%;" colspan="3"><i class="fas fa-asterisk fa-xs required"></i> Topic / Title</th>
                                    <th style="width: 20%;"> Remarks</th>
                                </tr>
                                <tr>
                                    <td>
                                        <select name="trrid" id="trrid" class="form-control" <?php echo $viewonly_dis;?>>
                                            <option value="">Select..</option>
                                            <?php 
                                                $len = count($record);
                                                for ($i=0; $i < $len; $i++) { 
                                                    $sel = (isset($res[0]['trrid']) && $res[0]['trrid'] == $record[$i]['trrid']) ? 'selected' : '';
                                            ?>
                                                    <option value="<?php echo $record[$i]['trrid'];?>" <?php echo $sel;?>><?php echo $record[$i]['trrname'];?></option>
                                            <?php 
                                                }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <div>
                                            <label class="radio-inline" style="margin-right: .3rem;"><input type="radio" name="tvcdplatform" value="WEBEX" <?php echo isset($res[0]['tvcdplatform']) && $res[0]['tvcdplatform']==='WEBEX' ? 'checked' : '';?> <?php echo $viewonly_dis;?>> WEBEX</label>
                                            <label class="radio-inline" style="margin-right: .3rem;"><input type="radio" name="tvcdplatform" value="SKYPE" <?php echo isset($res[0]['tvcdplatform']) && $res[0]['tvcdplatform']==='SKYPE' ? 'checked' : '';?> <?php echo $viewonly_dis;?>> SKYPE</label>
                                            <label class="radio-inline" style="margin-right: .3rem;"><input type="radio" name="tvcdplatform" value="GOOGLE MEET" <?php echo isset($res[0]['tvcdplatform']) && $res[0]['tvcdplatform']==='GOOGLE MEET' ? 'checked' : '';?> <?php echo $viewonly_dis;?>> GOOGLE MEET</label>


                                            <label class="radio-inline" style="margin-right: .3rem;"><input type="radio" name="tvcdplatform" value="OTHER" <?php echo isset($res[0]['tvcdplatform']) && $res[0]['tvcdplatform']!=='WEBEX' && $res[0]['tvcdplatform']!=='SKYPE' && $res[0]['tvcdplatform']!=='GOOGLE MEET' ? 'checked' : '';?>  <?php echo $viewonly_dis;?>> OTHER PLATFORM</label>
                                        </div>

                                        <?php 
                                            $tvcdplatform_other = '';
                                            $display = 'none';
                                            if (isset($res[0]['tvcdplatform']) && $res[0]['tvcdplatform']!=='WEBEX' && $res[0]['tvcdplatform']!=='SKYPE') {
                                                $tvcdplatform_other = $res[0]['tvcdplatform'];
                                                $display = 'block';
                                            } 
                                        ?>
                                        <input type="text" name="tvcdplatform_other" id="tvcdplatform_other" class="form-control" value="<?php echo $tvcdplatform_other;?>"  placeholder="Please specify other platform" style="display: <?php echo $display;?>;" <?php echo $viewonly_dis;?>>
                                    </td>
                                    <td colspan="3">
                                        <textarea rows="4" class="form-control" placeholder="Start typing here..." name="tvcdtitle" required <?php echo $viewonly_dis;?>><?php echo isset($res[0]['tvcdtitle']) ? $res[0]['tvcdtitle'] : '';?></textarea>
                                    </td>

                                    <td>
                                        <textarea rows="4" class="form-control" placeholder="Start typing here..."  id="tvcdremarks" name="tvcdremarks" required <?php echo $viewonly_dis;?>><?php echo isset($res[0]['tvcdremarks']) ? $res[0]['tvcdremarks'] : '';?></textarea>
                                    </td>
                                </tr>

                                <tr>
                                    <th><i class="fas fa-asterisk fa-xs required"></i> Date</th>
                                    <th><i class="fas fa-asterisk fa-xs required"></i> Time Start</th>
                                    <th colspan="2"><i class="fas fa-asterisk fa-xs required"></i> Time End</th>
                                    <th><i class="fas fa-asterisk fa-xs required"></i> User Type</th>
                                    <th id="tvcdnumpar_head_div" style="width: 15%;">
                                        <?php if ($host === 'none') { ?>
                                                
                                        <?php } else { ?>
                                                <i class="fas fa-asterisk fa-xs required"></i> # of Participants
                                        <?php } ?>
                                    </th>
                                </tr>
                                <tr>
                                    

                                    <td>
                                        <input type="date" name="tvcddate" class="form-control" value="<?php echo isset($res[0]['tvcddate']) ? $res[0]['tvcddate'] : '';?>"  placeholder="Start typing here.." <?php echo $viewonly_dis;?>>
                                    </td>
                                    <td>
                                        <input type="time" name="tvcdtimestart" class="form-control" value="<?php echo isset($res[0]['tvcdtimestart']) ? $res[0]['tvcdtimestart'] : '';?>"  placeholder="Start typing here.." <?php echo $viewonly_dis;?>>
                                    </td>
                                    <td colspan="2">
                                        <input type="time" name="tvcdtimeend" class="form-control" value="<?php echo isset($res[0]['tvcdtimeend']) ? $res[0]['tvcdtimeend'] : '';?>"  placeholder="Start typing here.." <?php echo $viewonly_dis;?>>
                                    </td>
                                    <td>
                                        <select name="tvcdusertype" id="tvcdusertype" class="form-control" <?php echo $viewonly_dis;?>>
                                            <option value="">Select..</option>
                                            <option value="Host" <?php echo isset($res[0]['tvcdusertype']) && $res[0]['tvcdusertype']==='Host' ? 'selected' : '';?> >Host</option>
                                            <!-- <option value="Participant" <?php #echo isset($res[0]['tvcdusertype']) && $res[0]['tvcdusertype']==='Participant' ? 'selected' : '';?> >Participant</option>
                                            <option value="Viewer" <?php #echo isset($res[0]['tvcdusertype']) && $res[0]['tvcdusertype']==='Viewer' ? 'selected' : '';?> >Viewer Only</option> -->
                                        </select>
                                    </td>

                                    <td>
                                        <div style="display: <?php echo $host;?>;" id="tvcdnumpar_val_div">
                                            <input type="number" id="tvcdnumpar" name="tvcdnumpar" class="form-control tvcdnumpar" value="<?php echo isset($res[0]['tvcdnumpar']) ? $res[0]['tvcdnumpar'] : '';?>"  placeholder="Start typing here.." <?php echo $viewonly_dis;?>>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <br>

                        
                        <table class="table_" style="margin-bottom: 6em; display: <?php echo $host;?>;" id="tbl_participant">
                            <thead>
                                <tr>
                                    <th colspan="5">List of Participants <span class="text-muted" style="font-size: 12px;"> (If host, please indicate email of participants below) </span></th>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-asterisk fa-xs required"></i> Name</th>
                                    <th><i class="fas fa-asterisk fa-xs required"></i> Position</th>
                                    <th><i class="fas fa-asterisk fa-xs required"></i> Office</th>
                                    <th><i class="fas fa-asterisk fa-xs required"></i> Email Address</th>

                                    <?php if ($viewonly_dis !== 'disabled') { ?>
                                        <th style="width: 20px;">
                                            <a href="javascript:void(0);" id="addpart"><i class="fas fa-plus-square fa-2x"></i> </a>
                                        </th>
                                    <?php } ?>
                                    
                                </tr>
                            </thead>
                            <tbody id="tbl_participant_body">
                                <?php 
                                    $len = count($part);
                                    for ($i=0; $i < $len; $i++) { 
                                ?>
                                        <tr>
                                            <td><input type="text" name="tvcpname[]" class="form-control" value="<?php echo $part[$i]['tvcpname'];?>" placeholder="" <?php echo $viewonly_dis;?>></td>
                                            <td><input type="text" name="tvcppos[]" class="form-control" value="<?php echo $part[$i]['tvcppos'];?>" placeholder="" <?php echo $viewonly_dis;?>></td>
                                            <td><input type="text" name="tvcpoffice[]" class="form-control" value="<?php echo $part[$i]['tvcpoffice'];?>" placeholder="" <?php echo $viewonly_dis;?>></td>
                                            <td><input type="text" name="tvcpemail[]" class="form-control" value="<?php echo $part[$i]['tvcpemail'];?>" placeholder="" <?php echo $viewonly_dis;?>></td>
                                            <?php if ($viewonly_dis !== 'disabled') { ?>
                                                <td>
                                                    <input type="hidden" name="tvcpid[]" class="form-control" value="<?php echo $part[$i]['tvcpid'];?>" placeholder="">
                                                    <a href="javascript:void(0);" name="adelpart"><i class="fas fa-trash required fa-2x"></i></a>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                <?php 
                                    }

                                    if ($len === 0) {
                                ?>
                                        <tr>
                                            <td><input type="text" name="tvcpname[]" class="form-control" value="" placeholder=""></td>
                                            <td><input type="text" name="tvcppos[]" class="form-control" value="" placeholder=""></td>
                                            <td><input type="text" name="tvcpoffice[]" class="form-control" value="" placeholder=""></td>
                                            <td><input type="text" name="tvcpemail[]" class="form-control" value="" placeholder=""></td>
                                            <?php if ($viewonly_dis !== 'disabled') { ?>
                                                <td>
                                                    <input type="hidden" name="tvcpid[]" class="form-control" value="" placeholder="">
                                                    <a href="javascript:void(0);" name="adelpart"><i class="fas fa-trash required fa-2x"></i></a>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                <?php                                 
                                    }
                                ?>
                            </tbody>
                        </table>


                        <?php if (!(isset($viewonly) && $viewonly==true)) { ?>
                            <div>
                                <a href="javascript:void(0);" class="btn btn-success" id="sr_reqsubmit"><i class="fas fa-paper-plane"></i> Submit</a>
                                <a href="<?php echo base_url();?>" class="btn btn-danger"><i class="fas fa-window-close"></i> Cancel</a>
                            </div>
                        <?php } ?>

                    </form>
                </div>
            </div>

            <div class="col-md-3">
                <div class="px-2">
                    <?php $this->load->view('page/remarksnew'); ?>
                </div>
            </div>

        </div>
    </div>
    <?php $this->load->view('page/footer'); ?>
    <div class="modal fade" id="srmodalcheckconforme" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">CSS Alert</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group text-center">
                        <div>You have 1 request that have no CSS. </div>
                        <div>Please click the CSS by clicking the icon <a href="<?php echo base_url() . '?q=Accomplished';?>"><i class="fas fa-user fa-2x"></i></a> before adding new request. </div>
                        <div>Thank you!</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</body>

 <script type="text/javascript" src="<?php echo base_url() . 'assets/js/homeindexvidcon.js';?>"></script>
