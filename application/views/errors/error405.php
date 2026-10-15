<?php $this->load->view('page/headlink'); ?>





<body class="">
    <div class="wrapper">
        <?php $this->load->view('page/header'); ?>
        <?php $this->load->view('page/aside'); ?>
        <div class="content-wrapper" id="content-wrapper">
            <div class="content section">
                <?php $this->load->view('page/welcome'); ?>
                <div id="page-wrapper">
                    <div class="content-wrapper">
                        <div class="row" id="rightside">
                            <div class="">
                                <section class="" id="content_id">
                                    <div class="error-page">
                                        <h1 class="headline text-red">Error 405</h1>
                                        <div class="error-content">
                                            <h2><i class="fa fa-warning text-red"></i> Method Not Allowed</h2>
                                            <p>
                                                Your not allowed to view this page.
                                                Click here to <a href="<?php echo base_url();?>">return to dashboard</a>.
                                            </p>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
       
         <?php $this->load->view('page/footer'); ?>
    </div>
</body>

<style type="text/css">
    .content:before {
        content: '';
        display: block;
        height: 136px;
    }
</style>
<script type="text/javascript">
    $('#sidebar-toggle').addClass('toggle-off');
    $('#sidebar-nav').addClass('mrgnleft')
    $('#sidebar-logo').addClass('toggle-off');
    $('#content-wrapper').addClass('mrgnleft');
    $('#main-sidebar').addClass('dsply');
    $('#main-footer').addClass('mrgnleft');
    $('#sidebar-toggle').hide();
</script>
