<?php $this->load->view('page/headlink'); ?>

<link rel="stylesheet" type="text/css" href="">
<link href="<?php echo base_url() . 'assets/css/auth.css';?>" rel="stylesheet">

<body>
    <div class="container">
        <?php $this->load->view('page/headerlogin'); ?>

        <div class="card card-container" style="text-align: center;">
            <h6>Please enter your email to reset your password</h6>
            <div style="margin: 15px 0 10px 0;" class="text-center">
                <form class="form-signin" method="post" action="resetpasswordsend">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>" />
                    <input type="email" name="email" class="form-control form-control-lg" placeholder="Email" required autofocus>
                    <button class="btn btn-lg btn-danger btn-block" type="submit">Reset Password</button>
                </form>
                <a href="<?php echo base_url();?>" class="forgot-password">
                    >> Click here to go back to login page <<
                </a>
            </div>
        </div>
    </div>

    <?php $this->load->view('page/footerlogin'); ?>
</body>
