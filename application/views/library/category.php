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
                    <a href="javascript:void(0);" id="category" class="btn btn-primary"><i class="fas fa-plus-square"> </i> Add Category</a>
                </div>
            </div>
            
            <table class="table table-striped" id="tbl_libcategory" >
                <thead>
                    <tr>
                        <th scope="col">Category</th>
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
        <div class="modal fade" id="modallibcategory" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Category</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="formlibcategory" action="savecategory">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>" />
                            <input type="hidden" name="tlcid" value="">

                            <div class="form-group">
                                <label for="tlccat"><i class="fas fa-asterisk fa-xs required"></i> Category</label>
                                <input class="form-control" name="tlccat" placeholder="Start typing here..." required>
                            </div>

                            <div class="form-group">
                                <label for="tlcstatus"><i class="fas fa-asterisk fa-xs required"></i> Status</label>
                                <select name="tlcstatus" class="form-control" required>
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

<script type="text/javascript" src="<?php echo base_url() . 'assets/js/libcategory.js';?>"></script>

