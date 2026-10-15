<?php $this->load->view('page/headlink'); ?>

<link rel="stylesheet" type="text/css" href="">
<link href="<?php echo base_url() . 'assets/css/auth.css';?>" rel="stylesheet">

<body id="wrapper_body">
    <div class="container">
        <?php $this->load->view('page/headerlogin'); ?>

        <div class="card card-container">
            <h6 style="text-align: center;">Please update your password</h6>
            <div>
                <span class="required" style="color: #f60000; font-weight: 600;"><?php echo $this->session->flashdata('error'); ?></span>
            </div>
            <div style="margin: 15px 0 10px 0;">
                <form class="form-signin" method="post" action="<?php echo base_url() . 'updatepassword';?>">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>" />

                    <?php 
                        if (isset($email_hash, $email_code)) {
                    ?>
                            <input type="hidden" name="email_hash" value="<?php echo $email_hash;?>" />
                            <input type="hidden" name="email_code" value="<?php echo $email_code;?>" />
                    <?php 
                        }
                    ?>
                    <div class="form-group">
                        <input type="email" class="form-control form-control-lg" placeholder="Enter new password" id="email" name="email" value="<?php echo $email;?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="newpassword">New Password</label>
                        <input type="password" class="form-control form-control-lg" placeholder="Enter new password" id="newpassword" name="newpassword" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmpassword">Confirm Password</label>
                        <input type="password" class="form-control form-control-lg" placeholder="Enter confirm password" id="confirmpassword" name="confirmpassword" required>
                    </div>
                    <button type="submit" class="btn btn-lg btn-primary btn-block" id="submitpassword" disabled>Update Password</button>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            'use strict';

            $('#wrapper_body').on('keyup', '#newpassword, #confirmpassword', function() {
                var newpassword = $.trim($('#newpassword').val());
                var confirmpassword = $.trim($('#confirmpassword').val());

                if (newpassword.length < 6 || confirmpassword.length < 6 ) {
                    $('#submitpassword').prop('disabled', true);
                } else {
                    $('#submitpassword').prop('disabled', true);
                    if (newpassword === confirmpassword) {
                        $('#submitpassword').prop('disabled', false);    
                    }
                    
                }
            });
        });
    </script>

</body>
