<?php $this->load->view('page/headlink'); ?>
<style type="text/css">
    .table td, .table th {
        padding: .5rem;
    }
    .table {
        margin-bottom: 0;
    }
</style>
<body>
    <?php $this->load->view('page/header'); ?>
    <div class="container" id="wrapper">
        <div class="bg-white px-2">
            <!-- <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-light">
                    <li class="breadcrumb-item"><a href="<?php #echo base_url();?>">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Settings</li>
                </ol>
            </nav> -->

            <div class="row pt-2 pb-1">
                <div class="col text-left" style="font-size: 1.2em;">
                    My Account Details
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <form method="post">
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">Facility Type</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" placeholder="" name="" value="<?php echo $res[0]['tllfactype'];?>" disabled>
                            </div>
                        </div>

                        <?php 
                            if ($this->session->userdata('tllpriv') !== '1') {
                        ?>
                                <div class="form-group row">
                                    <label for="" class="col-sm-3 col-form-label">Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" placeholder="" name="" value="<?php echo $res[0]['fullname'];?>" disabled>
                                    </div>
                                </div>
                        <?php 
                            }
                        ?>
                         
                         <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">Username</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" placeholder="" name="" value="<?php echo $res[0]['tlluname'];?>" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">Office</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" placeholder="" name="" value="<?php echo $res[0]['office'];?>" disabled>
                            </div>
                        </div>

                         <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" placeholder="" id="sremail_display" value="<?php echo $res[0]['tllemail'];?>" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label">Contact #</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" placeholder="" id="srcontact_display"  value="<?php echo $res[0]['tllnumber'];?>" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="" class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9">
                                <a href="javascript:void(0);" class="btn btn-success" title="Change password" id="srchangepassword"><i class="fas fa-lock-open"></i> Password</a>
                                <a href="javascript:void(0);" class="btn btn-success" title="Change contact details" id="srchnagecontact"><i class="fas fa-address-book"></i> Contact Details</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('page/footer'); ?>

    <div id="srmodal">
        <!-- Modal -->
        <div class="modal fade" id="srmodalchangepassword" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Change Password</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="srformreceived" action="settings/srchangepasssave">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>" />
                            <div class="form-group">
                                <label for="newpassword">New Password</label>
                                <input type="password" class="form-control" placeholder="Start typing here.." id="newpassword" name="newpassword" required>
                            </div>
                            <div class="form-group">
                                <label for="confirmpassword">Confirm Password</label>
                                <input type="password" class="form-control" placeholder="Start typing here.." id="confirmpassword" name="confirmpassword" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-outline-success" id="srbtnsavechangespassword">Save changes</button>
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="srmodalchangecontacts" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Change Contacts</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="srformcontact" action="settings/srchangecontactsave">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>" />
                            <div class="form-group">
                                <label for="newpassword">Email</label>
                                <input type="email" class="form-control" placeholder="Start typing here.." id="sremail" name="sremail" required>
                            </div>
                            <div class="form-group">
                                <label for="confirmpassword">Confirm Number</label>
                                <input type="text" class="form-control" placeholder="Start typing here.." id="srnumber" name="srnumber" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-outline-success" id="srbtnsavechangescontact">Save changes</button>
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

</body>

 <script type="text/javascript" src="<?php echo base_url() . 'assets/js/settingsindex.js';?>"></script>
