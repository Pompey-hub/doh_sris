<?php $this->load->view('page/headlink'); ?>

<link rel="stylesheet" type="text/css" href="">
<link href="<?php echo base_url() . 'assets/css/auth.css';?>" rel="stylesheet">

<body>
    <div class="container">
        <?php $this->load->view('page/headerlogin'); ?>

        <div class="card card-container">
            <h6 style="text-align: center;">Please login to start your session</h6>
            <div style="margin: 15px 0 10px 0;">
                <form class="form-signin" method="post" action="validaterequest" autocomplete="on">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>" />
                    <input type="text" name="uname" class="form-control form-control-lg" placeholder="Username | Email Address" required autofocus>
                    <input type="password" name="pword" class="form-control form-control-lg" placeholder="Password" required>
                    <button class="btn btn-lg btn-success btn-block" type="submit">Login</button>
                </form>
                <div style="text-align:right;">
                    <a href="<?php echo base_url() . 'resetpassword';?>" class="forgot-password">
                        Forgot password?
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('page/footerlogin'); ?>
</body>
