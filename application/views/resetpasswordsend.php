<?php $this->load->view('page/headlink'); ?>

<link rel="stylesheet" type="text/css" href="">
<link href="<?php echo base_url() . 'assets/css/auth.css';?>" rel="stylesheet">

<body>
    <div class="container">
        <?php $this->load->view('page/headerlogin'); ?>

        <div class="card card-container" style="text-align: center;">
            <h6 >
                <div>Your new password link has been sent to <u><?php echo $email;?></u></div>
                <div>If you don't see it, be sure to check your spam folder.</div>
                <div>Thank you!</div>
            </h6>
            <a href="<?php echo base_url();?>" class="forgot-password">
                >> Click here to go back to login page <<
            </a>
        </div>
    </div>
</body>
