<?php $this->load->view('page/headlink'); ?>

<?php
    if ($this->session->userdata('privilege') == '99') {
        $priv_ = 'Superadmin';
    } elseif (in_array('SRIS', $this->session->userdata('tlarsys')) ) {
        $exp_sys = $this->session->userdata('tlarsys'); #explode('&shen&', $this->session->userdata('tlarsys'));
        $index = array_search('SRIS', $exp_sys);

        $exp_priv = $this->session->userdata('tlarpriv'); #explode('&shen&', $this->session->userdata('tlarpriv'));
        $priv_ = $exp_priv[$index];
    } else {
        $priv_ = 'Client';
    }
?>

<style type="text/css">
    
.sorting_desc:before, .sorting_desc:after  {
    display: none !important;
}

</style>
<body>
    <div class="">
        <div style="margin-bottom: -15px;">
            <div class="px-1 py-3 header-account">
                <div class="col-4">
                    Welcome, <?php echo $this->session->userdata('fullname'); ?>
                </div>
                <div class="col-4" style="text-align: center;">Account: <?php echo $priv_;?></div>
                <div class="col-4" style="text-align: right;"><?php echo date('F d, Y');?></div>
            </div>

            <nav class="navbar navbar-expand-lg navbar-light bg-header py-3">
                <a class="navbar-brand" href="javascript:void(0);">
                    <img class="img-logo" src="<?php echo base_url('assets/img/doh300.png');?>">
                    <img class="img-logo" src="<?php echo base_url('assets/img/dohevchd300.png');?>">
                </a>
                <div>
                    Department of Health <br>
                    Eastern Visayas - Center for Health Development <br>
                    ICTU Service Request v1.0
                </div>
            </nav>

            <div class="px-1 py-2" style="border-top: 2px solid #e8ebee; display: flex; flex-wrap: wrap;">
            </div>
        </div>
    </div>

    <div class="" id="wrapper" style="min-height: 76vh;">
        <div class="bg-white px-2">
            <div class="row pt-2 pb-4">
                <div class="col text-left" style="font-size: 1.2em;">
                    List of Service Request <span class="text-muted" style="font-size: 12px;"> ( Auto refresh every 1 minute. ) </span>
                </div>
            </div>


            <table class="table table-striped" id="tbl_servicerequest" >
                <thead>
                    <tr>
                        <th>Ticket #</th>
                        <th scope="col" style="width: 6%;">Date</th>
                        <th scope="col">Time</th>
                        <th scope="col" style="width: 15%;">Name</th>
                        <th scope="col">Office</th>
                        <th scope="col" style="width: 35%;">Description / Problem</th>
                        <th scope="col" style="width: 6%;">Status</th>
                        <th scope="col">Remarks</th>
                        <th scope="col">Officer</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <?php $this->load->view('page/footer'); ?>

    <div id="srmodal">
        <div class="modal fade" id="srmodallogstatus" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Action Taken Log Status</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="modxtsrid" value="">

                        <div class="pt-2 pb-4">
                            <table style="width: 100%;">
                                <tbody>
                                    <tr>
                                        <td style="width: 30%; font-weight: 600;">Description of Request / Problem : </td>
                                        <td style="width: 70%; font-weight: 600;" id="srtakremarks"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th style="width: 40%;">Action Taken</th>
                                    <th>Remarks</th>
                                    <th>Action Officer</th>
                                    <?php if ($this->session->userdata('tllpriv') != 1) { ?> 
                                        <th></th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody id="tbodysrlogstatus"></tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<script type="text/javascript" src="<?php echo base_url() . 'assets/js/homeindexdtsrview.js'; ?>"></script>
<script type="text/javascript" src="<?php echo base_url() . 'assets/js/homeindex.js';?>"></script>

