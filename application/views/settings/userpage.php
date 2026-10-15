<?php $this->load->view('page/headlink'); ?>
<style type="text/css">
    
.sorting_desc:before, .sorting_desc:after  {
    display: none !important;
}

#tbl_servicerequest thead tr th:nth-child(5) {
    width: 35%;
}

</style>
<body>
    <?php $this->load->view('page/header'); ?>
    <div class="container" id="wrapper">
        <div class="bg-white px-2">
            <!-- <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-light">
                    <li class="breadcrumb-item"><a href="<?php #echo base_url();?>">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
                </ol>
            </nav> -->

            <div class="row pt-2 pb-1">
                <div class="col text-left" style="font-size: 1.2em;">
                    List of Users
                </div>
                <div class="col text-right">
                    <a href="<?php echo base_url() . 'users/credentials';?>" class="btn btn-success"><i class="fas fa-plus-square"> </i> New</a>
                </div>
            </div>
            <div class="pt-0 pb-1">
            </div>


            <table class="table table-striped" id="tbl_users" >
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Username</th>
                        <th scope="col">Facility Type</th>
                        <th scope="col">Office</th>
                        <th scope="col">Privilege</th>
                        <th scope="col">Status</th>
                        <th scope="col">Date Added</th>
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
                        <form method="post" id="srformreceived" action="sractiontakensave">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>" />
                            <input type="hidden" name="xtsrid" value="">

                            <div class="form-group">
                                <label for="tlsrsid"><i class="fas fa-asterisk fa-xs required"></i> Select action</label>
                                <select name="tlsrsid" class="form-control">
                                    <option value="">Select...</option>
                                </select>
                            </div>
                            <div id="srmodalactiontakengroupdiv" style="display: none;">
                                <div class="form-group">
                                    <label for="xtlsid"><i class="fas fa-asterisk fa-xs required"></i> Type of Service</label>
                                    <select name="xtlsid" class="form-control">
                                        <option value="">Select...</option>
                                        <option value="1">Repair</option>
                                        <option value="2">Technical Assistance</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="tlsrsid"><i class="fas fa-asterisk fa-xs required"></i> Action taken description</label>
                                    <textarea class="form-control" name="takdesc" rows="5" placeholder="Start typing here.."></textarea>
                                </div>
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
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th style="width: 40%;">Action Taken</th>
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
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>" />
                            <input type="hidden" name="xtakid" value="">

                            <div class="form-group">
                                <label for="extlsid"><i class="fas fa-asterisk fa-xs required"></i> Type of Service</label>
                                <select name="extlsid" class="form-control">
                                    <option value="">Select...</option>
                                    <option value="1">Repair</option>
                                    <option value="2">Technical Assistance</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="tlsrsid"><i class="fas fa-asterisk fa-xs required"></i> Action taken description</label>
                                <textarea class="form-control" name="takdesc" rows="5" placeholder="Start typing here.."></textarea>
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

    </div>
</body>

<script type="text/javascript" src="<?php echo base_url() . 'assets/js/userindex.js';?>"></script>

