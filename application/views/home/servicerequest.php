<?php $this->load->view('page/headlink'); ?>
<?php
$tlsrsid_arr = [4, 5, 8];
if (isset($res[0]['tlsrsid']) && !in_array($res[0]['tlsrsid'], $tlsrsid_arr)) {
    $viewonly = true;
}
?>

<body>
    <?php $this->load->view('page/header'); ?>
    <div id="wrapper">
        <div class="row">

            <div class="col-md-9">
                <div class="px-2">
                    <div class="row pt-2 pb-4">
                        <h5 class="text-uppercase">
                            <?php
                            if (isset($viewonly) && $viewonly == true) {
                                $viewonly_dis = 'disabled';
                            } else {
                                $viewonly_dis = '';
                                echo ($this->input->get('q') == NULL ? 'Add' : 'Edit') . '';
                            }
                            ?>

                            Service Request for Repair/Technical Assistance
                        </h5>
                    </div>

                    <?php $this->load->view('page/validation'); ?>
                    <?php if (count($emp) > 0) { ?>
                        <form method="post" id="sr_form" class="form">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                            <input type="hidden" name="tsrid" value="<?php echo $this->input->get('q'); ?>">

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <?php $office_det = isset($res[0]['office']) ? $res[0]['office'] : ($this->session->userdata('divabbrev') . '-' . $this->session->userdata('secabbrev')); ?>
                                    <label for="">Requesting Office</label>
                                    <input type="text" name="" class="form-control" value="<?php echo $office_det; ?>" disabled>
                                </div>
                                <div class="form-group col-md-4">

                                    <?php $sel_date = isset($res[0]['tsrdateadded']) ? date('F d, Y', strtotime($res[0]['tsrdateadded'])) : date('F d, Y'); ?>
                                    <?php $sel_time = isset($res[0]['tsrdateadded']) ? date('H:i:s', strtotime($res[0]['tsrdateadded'])) : 'Auto Generated'; ?>

                                    <label for="">Date</label>
                                    <input type="text" name="" class="form-control" value="<?php echo $sel_date ?>" disabled>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="">Time</label>
                                    <input type="text" name="" class="form-control" value="<?php echo $sel_time ?>" disabled>
                                </div>
                            </div>


                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for=""><i class="fas fa-asterisk fa-xs required"></i> Assisting Office</label>
                                    <select name="tsrsecid" id="tsrsecid" class="form-control" <?php echo $viewonly_dis; ?>>
                                        <option value="">Select..</option>
                                        <?php
                                        $temp_tsrsecid = isset($res[0]['secid']) ? $res[0]['secid'] : '';
                                        $len = count($office);
                                        for ($i = 0; $i < $len; $i++) {
                                            $sel = ($temp_tsrsecid == $office[$i]['secid']) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $office[$i]['secid']; ?>" <?php echo $sel; ?>>
                                                <?php echo $office[$i]['divabbrev'] . '-' . $office[$i]['secabbrev']; ?>
                                            </option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for=""><i class="fas fa-asterisk fa-xs required"></i> Name of Requester</label>
                                    <select name="tsrclient" id="tsrclient" class="form-control" <?php echo $viewonly_dis; ?>>
                                        <option value="">Select..</option>
                                        <?php
                                        $temp = isset($res[0]['tsrclient']) ? $res[0]['tsrclient'] : $this->cryptography->decrypt($this->session->userdata('empid'));
                                        $len = count($emp);
                                        for ($i = 0; $i < $len; $i++) {
                                            $sel = ($temp == $emp[$i]['empid']) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $emp[$i]['empid']; ?>" <?php echo $sel; ?>>
                                                <?php echo $emp[$i]['fname'] . ' ' . ($emp[$i]['mname'] === NULL || $emp[$i]['mname'] === '' ? '' : $emp[$i]['mname'][0] . '. ') .
                                                    $emp[$i]['lname'] . ($emp[$i]['suffix'] === NULL || $emp[$i]['suffix'] === '' ? '' : $emp[$i]['suffix']) .
                                                    ($emp[$i]['extname'] === NULL || $emp[$i]['extname'] === '' ? '' : ', ' . $emp[$i]['extname']); ?>
                                            </option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for=""><i class="fas fa-asterisk fa-xs required"></i> Mobile #</label>
                                    <input type="text" name="tsrmobilenum" class="form-control" value="<?php echo isset($res[0]['tsrmobilenum']) ? $res[0]['tsrmobilenum'] : ''; ?>" placeholder="Start typing here.." <?php echo $viewonly_dis; ?>>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="">Email Address</label>
                                    <input type="email" name="tsremail" class="form-control" value="<?php echo isset($res[0]['tsremail']) ? $res[0]['tsremail'] : ''; ?>" placeholder="Start typing here.." <?php echo $viewonly_dis; ?>>
                                </div>
                            </div>


                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for=""><i class="fas fa-asterisk fa-xs required"></i> Request</label>
                                    <select name="tsrtlcid" id="tsrtlcid" class="form-control" <?php echo $viewonly_dis; ?>>
                                        <option value="">Select..</option>
                                        <?php
                                        $temp_tlcid = isset($res[0]['tlcid']) ? $res[0]['tlcid'] : '';
                                        $len = count($category);
                                        for ($i = 0; $i < $len; $i++) {
                                            $sel = ($temp_tlcid == $category[$i]['tlcid']) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $category[$i]['tlcid']; ?>" <?php echo $sel; ?>>
                                                <?php echo $category[$i]['tlccat']; ?>
                                            </option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-9">
                                    <label for=""><i class="fas fa-asterisk fa-xs required"></i> Specific Request</label>
                                    <select name="tsrtlscid" id="tsrtlscid" class="form-control" <?php echo $viewonly_dis; ?>>
                                        <option value="">Select..</option>
                                        <?php
                                        $temp_tlscid = isset($res[0]['tlscid']) ? $res[0]['tlscid'] : '';
                                        $len = count($subcategory);
                                        for ($i = 0; $i < $len; $i++) {
                                            $sel = ($temp_tlscid == $subcategory[$i]['tlscid']) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $subcategory[$i]['tlscid']; ?>" <?php echo $sel; ?>>
                                                <?php echo $subcategory[$i]['tlcproblem']; ?>
                                            </option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>



                            <div class="form-group">
                                <label for=""><i class="fas fa-asterisk fa-xs required"></i> Description of Request <span class="required"> (Note: One request / problem at a time)</span></label>
                                <textarea rows="9" placeholder="Start typing here..." class="form-control" name="srdesc" autofocus required <?php echo $viewonly_dis; ?>><?php echo isset($res[0]['tsrreqdesc']) ? $res[0]['tsrreqdesc'] : ''; ?></textarea>
                            </div>


                            <?php if (!(isset($viewonly) && $viewonly == true)) { ?>
                                <div>
                                    <a href="javascript:void(0);" class="btn btn-success" id="sr_reqsubmit"><i class="fas fa-paper-plane"></i> Submit</a>
                                    <a href="<?php echo base_url(); ?>" class="btn btn-danger"><i class="fas fa-window-close"></i> Cancel</a>
                                </div>
                            <?php } ?>

                        </form>
                    <?php } else { ?>
                        <div class="required"><b>Please inform the HRMU to add your division and section details in the HRMIS.</b></div>
                    <?php } ?>
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
                        <div>Please click the CSS by clicking the icon <a href="<?php echo base_url() . '?q=Accomplished'; ?>"><i class="fas fa-user fa-2x"></i></a> before adding new request. </div>
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

<script type="text/javascript" src="<?php echo base_url() . 'assets/js/homeindex.js'; ?>"></script>