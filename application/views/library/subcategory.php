<?php 
    $this->load->view('page/headlink'); 
?>

<body>
    <?php $this->load->view('page/header'); ?>
    <div class="pb-5" id="wrapper">
        <div class="px-2">
            <div class="row pt-2 pb-1">
                <div class="col text-left">
                    <h5></h5>
                </div>
                <div class="col text-right">
                    <a href="javascript:void(0);" id="subcategory" class="btn btn-primary"><i class="fas fa-plus-square"> </i> Add Subcategory</a>
                </div>
            </div>
            
            <table class="table table-striped" id="tbl_libsubcategory" >
                <thead>
                    <tr>
                        <th scope="col">Subcategory</th>
                        <th scope="col">Description</th>
                        <th scope="col">Category</th>
                        <th scope="col">Type of Service</th>
                        <th scope="col">Urgency</th>
                        <th scope="col">Status</th>
                        <th scope="col">Date Created</th>
                        <th scope="col">Date Updated</th>
                        <th scope="col">Action</th>
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
        <div class="modal fade" id="modallibsubcategory" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Subcategory</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="formlibsubcategory" action="savesubcategory">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>" />
                            <input type="hidden" name="tlscid" value="">

                            <div class="form-group">
                                <label><i class="fas fa-asterisk fa-xs required"></i> Category</label>
                                <select name="tlcid" class="form-control" required>
                                    <option value="">Select...</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-asterisk fa-xs required"></i> Subcategory</label>
                                <input class="form-control" name="tlcproblem" placeholder="Start typing here..." required>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <input class="form-control" name="tlcproblem_desc" placeholder="Start typing here..." required>
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-asterisk fa-xs required"></i> Type of Service</label>
                                <select name="tlsid" class="form-control" required>
                                    <option value="">Select...</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-asterisk fa-xs required"></i> Type of Urgency</label>
                                <select name="tll_id" class="form-control" required>
                                    <option value="">Select...</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-asterisk fa-xs required"></i> Status</label>
                                <select name="tlscstatus" class="form-control" required>
                                    <option value="ACTIVE">ACTIVE</option>
                                    <option value="INACTIVE">INACTIVE</option>
                                </select>
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
    </div>
</body>

<script type="text/javascript" src="<?php echo base_url() . 'assets/js/libsubcategory.js';?>"></script>

