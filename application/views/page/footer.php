<footer >
    <div class="">
        <div class="pt-3 pb-3 text-center">
            &copy; 2019-2024 DOH EVCHD. All rights reserved.
        </div>
    </div>
</footer>

<div id="modal_">
    <div class="modal fade" id="srmodalabout" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">About the System</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group text-justify">
                        <b>ICTU Service Request </b>
                         is an online system that allows you to report any issues and concerns and make a request for any repair and technical assistance on internet or network connectivity, hardware and software, IP Phone and other computer related issues and DOH Information Systems. 
                    </div>
                    <div class="form-group text-justify">
                        This aims to streamline and prioritize your IT technical support needs. Please fill out the request form completely and submit one request at a time. Once you fill in the form, click Submit to allow the information to be submitted to the ICTU for resolution.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<a href="javascript:void(0);" id="return-top" title="Click to scroll top"><i class="fa fa-caret-square-o-up"></i></a>
<span id="myspinner">
    <i class="fa fa-spinner fa-spin myspinnerstyle"></i><br>
    <h3 class="spinner-loading">L o a d i n g ...</h3>
</span>


<script type="text/javascript" src="<?php echo base_url() . 'assets/js/master.js';?>"></script>
<script type="text/javascript" src="<?php echo base_url() . 'assets/credits/bootstrap4.3.1/js/bootstrap.min.js';?>"></script>
<script type="text/javascript" src="<?php echo base_url() . 'assets/credits/datatable/jquery.dataTables.min.js';?>"></script>
<script type="text/javascript" src="<?php echo base_url() . 'assets/credits/datatable/dataTables.bootstrap4.min.js';?>"></script>
<script type="text/javascript" src="<?php echo base_url() . 'assets/credits/toastr/toastr.min.js';?>"></script>
<script type="text/javascript" src="<?php echo base_url() . 'assets/credits/select2/js/select2.full.min.js';?>"></script>
<script type="text/javascript" src="<?php echo base_url() . 'assets/credits/sweetalert2/sweetalert2.all.min.js';?>"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#logout').on('click', function() {
            var cnfrm = confirm('Continue Logout?');
            if (cnfrm) {
                window.location.href = '<?php echo base_url() . 'logout';?>';
            }
        });


        $('#about_a').on('click', function() {
            $('#srmodalabout').modal({
                backdrop:'static',
                keyboard: false
            });
        });

        $('form input').on('keypress', function(e) {
            return e.which !== 13;
        });

        $('#return-top').click(function() {
            $('body, html').animate({
                scrollTop : 0    
            }, 400);
        });

        <?php if($this->session->flashdata('success')){ ?>
                toastr.success("<?php echo $this->session->flashdata('success');?>");
        <?php } else if ($this->session->flashdata('error')) { ?>
                toastr.error("<?php echo $this->session->flashdata('error');?>");
        <?php } ?>
    });

    $(window).scroll(function() {
        if ($(this).scrollTop() >= 200) {
            $('#return-top').fadeIn(200);       
        } else {
            $('#return-top').fadeOut(200);   
        }
    });
</script>
