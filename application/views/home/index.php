<?php
$this->load->view('page/headlink');

if ($this->session->userdata('privilege') == '99') {
    $priv_ = 'Superadmin';
} elseif (in_array('SRIS', $this->session->userdata('tlarsys'))) {
    $exp_sys = $this->session->userdata('tlarsys');
    $index = array_search('SRIS', $exp_sys);

    $exp_priv = $this->session->userdata('tlarpriv');
    $priv_ = $exp_priv[$index];
} else {
    $priv_ = 'Client';
}
?>
<style type="text/css">
    .form-group {
        margin-bottom: 1rem !important;
    }
</style>

<body>
    <?php $this->load->view('page/header'); ?>
    <div class="pb-5" id="wrapper">
        <div class="px-2">
            <div class="row pt-2 pb-1">
                <div class="col text-left">
                    <h5>List of Service Request <span class="text-muted"> ( Auto refresh every 5 minutes. ) </span></h5>
                </div>
                <div class="col text-right">
                    <a href="javascript:void(0);" id="servicerequest" class="btn btn-success"><i class="fas fa-plus-square"> </i> Technical Assistance</a>
                    <a href="javascript:void(0);" id="servicerequestvidcon" class="btn btn-primary"><i class="fas fa-plus-square"> </i> Video Conference</a>
                </div>
            </div>

            <div class="row pt-0 pb-1">
                <div class="col text-right">

                    <?php if ($ass_office_exist === true || $priv_ == 'HRDU-Admin') { ?>
                        <label class="ml-2">
                            <a href="<?php echo base_url(); ?>">
                                <input
                                    class="center-radio-btn"
                                    type="radio"
                                    id="qstatus1"
                                    name="qstatus"
                                    <?php echo ($this->input->get('q') === null ? 'checked' : ''); ?>>
                                <span for="qstatus1">
                                    For <?php echo $this->session->userdata('secabbrev'); ?> Request
                                </span>
                            </a>
                        </label>
                    <?php } ?>

                    <!-- ASSIGNED TO ME -->
                    <label class="ml-2">
                        <a href="<?php echo base_url() . '?q=assigned'; ?>">
                            <input
                                class="center-radio-btn"
                                type="radio"
                                id="qstatus_assigned"
                                name="qstatus"
                                <?php echo ($this->input->get('q') === 'assigned' ? 'checked' : ''); ?>>
                            <span for="qstatus_assigned">
                                Assigned to me
                            </span>
                        </a>
                    </label>

                    <!-- MY PENDING REQUEST -->
                    <label class="ml-2">
                        <a href="<?php echo base_url() . '?q=myrequest'; ?>">
                            <input
                                class="center-radio-btn"
                                type="radio"
                                id="qstatus2"
                                name="qstatus"
                                <?php echo (
                                    $this->input->get('q') === 'myrequest' ||
                                    (
                                        $this->input->get('q') === null &&
                                        $ass_office_exist === false &&
                                        $priv_ != 'HRDU-Admin'
                                    )
                                    ? 'checked'
                                    : ''
                                ); ?>>
                            <span for="qstatus2">
                                My Pending Request
                            </span>
                        </a>
                    </label>

                    <!-- ACCOMPLISHED / CANCELLED -->
                    <label class="ml-2">
                        <a href="<?php echo base_url() . '?q=Accomplished'; ?>">
                            <input
                                class="center-radio-btn"
                                type="radio"
                                id="qstatus3"
                                name="qstatus"
                                <?php echo ($this->input->get('q') === 'Accomplished' ? 'checked' : ''); ?>>
                            <span for="qstatus3">
                                My Accomplished/Cancelled Request
                            </span>
                        </a>
                    </label>

                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped" id="tbl_servicerequest">
                    <thead>
                        <tr>
                            <th scope="col" class="no-wrap wd-1">Ticket #</th>
                            <?php if (($ass_office_exist === true && $this->input->get('q') === null) ||
                                ($ass_office_exist === false && $this->input->get('q') === null && $priv_ == 'HRDU-Admin')
                            ) { ?>
                            <?php } else { ?>
                                <th scope="col" class="no-wrap wd-2">Assisting Office</th>
                            <?php } ?>
                            <th scope="col" class="no-wrap wd-2">Date &amp; Time</th>
                            <th scope="col" class="no-wrap wd-3">Name of Client</th>
                            <th scope="col" class="no-wrap wd-5">Description / Problem</th>
                            <th scope="col" class="no-wrap wd-6">Status</th>
                            <th scope="col" class="no-wrap wd-7">Remarks</th>
                            <th scope="col" class="no-wrap wd-8">Previous Assignee</th>
                            <th scope="col" class="no-wrap wd-8">Current Assignee</th>
                            <th scope="col" class="no-wrap wd-9">Assignment Updated By</th>
                            <th scope="col" class="wd-9">User</th>
                            <th scope="col" class="wd-10">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php $this->load->view('page/footer'); ?>

    <div id="srmodal">
        <!-- Modal -->
        <div class="modal fade" id="srmodalactiontaken" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Action Taken</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="srformreceived" action="sractiontakensave" class="form">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                            <input type="hidden" name="xtsrid" value="">
                            <input type="hidden" name="redirect_q" value="">

                            <div class="form-group">
                                <label><i class="fas fa-asterisk fa-xs required"></i> Select action</label>
                                <select name="tlsrsid" class="form-control">
                                    <option value="">Select...</option>
                                </select>
                            </div>
                            <div class="form-group" id="assignedToGroup">
                                <label>
                                    <i class="fas fa-asterisk fa-xs required"></i>
                                    Select ICTU Employee
                                </label>

                                <select name="assigned_to" class="form-control">
                                    <option value="">Select ICTU Employee...</option>
                                </select>
                            </div>
                            <div id="srmodalactiontakengroupdiv">
                                <div class="form-group">
                                    <label><i class="fas fa-asterisk fa-xs required"></i> Name of Client</label>
                                    <select name="takclientempid" class="form-control">
                                        <option value="">Select...</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-asterisk fa-xs required"></i> Type of Service</label>
                                    <select name="xtlsid" class="form-control">
                                        <option value="">Select...</option>
                                        <?php
                                        $len = count($serv);
                                        for ($i = 0; $i < $len; $i++) {
                                        ?>
                                            <option value="<?php echo $serv[$i]['tlsid']; ?>"><?php echo $serv[$i]['tlsdesc']; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-asterisk fa-xs required"></i> Category Request</label>
                                    <select name="tlscid" class="form-control">
                                        <option value="">Select...</option>
                                    </select>
                                </div>

                                <div class="form-group takapprove">
                                    <div>
                                        <label><i class="fas fa-asterisk fa-xs required"></i> Action</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="takapprove" id="takapprove1" value="APPROVE">
                                        <label class="form-check-label" style="color: unset; font-weight: unset;" for="takapprove1">APPROVE</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="takapprove" id="takapprove2" value="DISAPPROVE">
                                        <label class="form-check-label" style="color: unset; font-weight: unset;" for="takapprove2">DISAPPROVE</label>
                                    </div>
                                </div>


                                <div class="form-group takhostnamediv">
                                    <label><i class="fas fa-asterisk fa-xs required"></i> Host Name</label>
                                    <input class="form-control" name="takhostname" placeholder="Start typing here..">
                                </div>
                                <div class="form-group serialnumberdiv">
                                    <label> Serial #</label>
                                    <input class="form-control" name="serialnumber" placeholder="Start typing here..">
                                </div>

                                <div class="form-group takmacaddressdiv">
                                    <label><i class="fas fa-asterisk fa-xs required"></i> MAC Address</label>
                                    <input class="form-control" name="takmacaddress" placeholder="XX:XX:XX:XX:XX:XX">
                                </div>

                                <div class="form-group takdescdiv">
                                    <label><i class="fas fa-asterisk fa-xs required"></i> Action taken description</label>
                                    <textarea class="form-control" name="takdesc" rows="2" placeholder="Start typing here.."></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Remarks <span class="text-muted"> (Optional) </span></label>
                                <textarea class="form-control" name="takremarks" rows="2" placeholder="Start typing here.."></textarea>
                            </div>

                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-success" id="srbtnsavechanges">Save changes</button>
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade"
            id="srmodallogstatus"
            tabindex="-1"
            role="dialog"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true">

            <div class="modal-dialog modal-xl" role="document">

                <div class="modal-content">

                    <div class="modal-header">

                        <h5 class="modal-title" id="exampleModalLabel">
                            Action Taken Log Status
                        </h5>

                        <button type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close">

                            <span aria-hidden="true">&times;</span>

                        </button>

                    </div>

                    <div class="modal-body">

                        <input
                            type="hidden"
                            name="modxtsrid"
                            value="">

                        <div class="pt-2 pb-4">

                            <table class="table table-modal">

                                <tbody>

                                    <tr>

                                        <td class="tbl-desc-prob">
                                            Description of Request / Problem :
                                        </td>

                                        <td
                                            class="tbl-srtakremarks"
                                            id="srtakremarks">
                                        </td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                        <div class="table-responsive">

                            <table class="table table-striped">

                                <thead>

                                    <tr>

                                        <th class="no-wrap">
                                            Date
                                        </th>

                                        <th class="no-wrap">
                                            Time
                                        </th>

                                        <th class="no-wrap">
                                            Status
                                        </th>

                                        <th class="tbl-actiontaken">
                                            Action Taken
                                        </th>

                                        <th>
                                            Remarks
                                        </th>

                                        <th class="no-wrap">
                                            Previous Assignee
                                        </th>

                                        <th class="no-wrap">
                                            Current Assignee
                                        </th>

                                        <th class="no-wrap">
                                            Assignment Updated By
                                        </th>

                                        <th class="no-wrap">
                                            Log Recorded By
                                        </th>

                                        <?php if ($priv_ != 'Client') { ?>

                                            <th></th>

                                        <?php } ?>

                                    </tr>

                                </thead>

                                <tbody id="tbodysrlogstatus">

                                </tbody>

                            </table>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-outline-danger"
                            data-dismiss="modal">
                            Close
                        </button>

                    </div>

                </div>

            </div>

        </div>


        <div class="modal fade" id="srmodaleditactiontaken" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Action Taken</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="srformeditreceived">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                            <input type="hidden" name="xtakid" value="">

                            <div class="form-group">
                                <label><i class="fas fa-asterisk fa-xs required"></i> Type of Service</label>
                                <select name="extlsid" class="form-control">
                                    <option value="">Select...</option>
                                    <?php
                                    $len = count($serv);
                                    for ($i = 0; $i < $len; $i++) {
                                    ?>
                                        <option value="<?php echo $serv[$i]['tlsid']; ?>"><?php echo $serv[$i]['tlsdesc']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-asterisk fa-xs required"></i> Action taken description</label>
                                <textarea class="form-control" name="takdesc" rows="5" placeholder="Start typing here.."></textarea>
                            </div>

                            <div class="form-group">
                                <label>Remarks <span class="text-muted"> (Optional) </span></label>
                                <textarea class="form-control" name="takremarks" rows="3" placeholder="Start typing here.."></textarea>
                            </div>

                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-success" id="srbtneditsavechanges">Save changes</button>
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- additional remarks -->
        <div class="modal fade" id="srmodaladditionalremarks" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Add For Action Remarks</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="srformadditionalremarks">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                            <input type="hidden" name="takid_rem" value="">

                            <div class="form-group">
                                <label><i class="fas fa-asterisk fa-xs required"></i> Remarks</label>
                                <textarea class="form-control" name="takr_remarks" rows="3" placeholder="Start typing here.."></textarea>
                            </div>

                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-success" id="srbtnadditionalremarks">Save changes</button>
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="srmodalconforme" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Conforme</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="srformconforme" action="servicerequest/conforme">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                            <input type="hidden" name="xtsrid_c" value="">

                            <div class="form-group">
                                Click "Save changes" to close the service request. Thank you!
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-success" id="srbtnconformesavechanges">Save changes</button>
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="srmodalcss" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Costumer Satisfaction Survey</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="srbtncssclosetimes">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="get">
                            <input type="hidden" id="tsrid_enc" value="">
                            <input type="hidden" id="jsictu" value="">

                            <div class="form-group">
                                We would like to request you to fill-up the Costumer Satisfaction Survey
                                for the services provided to you.
                            </div>
                            <div class="form-group">
                                Thank You!
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-success" id="srbtncssproceed">Proceed</button>
                        <button type="button" class="btn btn-outline-danger" id="srbtncssclose">Close</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="srmodalcheckconforme_index" tabindex="-1" role="dialog" aria-hidden="true">
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
                            <div>You have Accomplished request that have no CSS. </div>
                            <div>Please click the CSS by clicking the icon <i class="fas fa-user fa-2x"></i> </div>
                            <div>in the <a href="<?php echo base_url() . '?q=Accomplished'; ?>">Accomplished/Cancelled</a> page</div>
                            <div>Thank you!</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="srmodalcheckconforme_index_check" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Alert <span class="text-muted">(Only 1 request at a time)</span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group text-center">
                            <div>You have a pending request</div>
                            <div>OR</div>
                            <div>You have Accomplished request that have no CSS. </div>
                            <div>Please click the CSS by clicking the icon <i class="fas fa-user fa-2x"></i> </div>
                            <div>in the <a href="<?php echo base_url() . '?q=Accomplished'; ?>">Accomplished/Cancelled</a> page.</div>
                            <div>Thank you!</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>


<?php if (
    ($ass_office_exist === true || $priv_ == 'HRDU-Admin') &&
    $this->input->get('q') === null
) { ?>

    <!-- FOR OFFICE REQUEST -->
    <script
        type="text/javascript"
        src="<?php echo base_url() . 'assets/js/homeindexdtdefwithnum.js?v=1.1'; ?>">
    </script>

<?php } elseif ($this->input->get('q') === 'assigned') { ?>

    <!-- ASSIGNED TO ME -->
    <script
        type="text/javascript"
        src="<?php echo base_url() . 'assets/js/homeindexdtassigned.js?v=1.0'; ?>">
    </script>

<?php } elseif ($this->input->get('q') === 'Accomplished') { ?>

    <!-- ACCOMPLISHED / CANCELLED -->
    <script
        type="text/javascript"
        src="<?php echo base_url() . 'assets/js/homeindexdtaccom.js?v=1.1'; ?>">
    </script>

<?php } else { ?>

    <!-- MY PENDING REQUEST -->
    <script
        type="text/javascript"
        src="<?php echo base_url() . 'assets/js/homeindexdtmyreq.js?v=1.1'; ?>">
    </script>

<?php } ?>

<script>
    var srisPrivilege = <?php echo json_encode($priv_); ?>;
</script>

<script
    type="text/javascript"
    src="<?php echo base_url() . 'assets/js/homeindex.js'; ?>">
</script>