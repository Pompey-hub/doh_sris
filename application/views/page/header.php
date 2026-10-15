<?php
if ($this->session->userdata('privilege') == '99') {
    $priv_ = 'Superadmin';
} elseif (in_array('SRIS', $this->session->userdata('tlarsys'))) {
    $exp_sys = $this->session->userdata('tlarsys');
    $index = array_search('SRIS', $exp_sys);

    $exp_priv = $this->session->userdata('tlarpriv');
    $priv_ = $exp_priv[$index];
} else {
    $priv_ = 'Client';
}
?>

<div class="header-account py-2">
    <div>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarToggler">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item" id="srli_home">
                        <a class="nav-link" href="<?php echo base_url(); ?>"><i class="fas fa-home"></i> Home</a>
                    </li>
                    <li class="nav-item" id="srli_about">
                        <a class="nav-link" href="javascript:void(0);" id="about_a"><i class="fa fa-info-circle"></i> About</a>
                    </li>

                    <li class="nav-item dropdown" id="srli_manual">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownmanual" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-file-pdf"></i> Manual
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownmanual">
                            <a class="dropdown-item" href="<?php echo base_url() . 'assets/img/ictusrismanualv1.pdf'; ?>" target="_blank" id="manual_a"> SRIS PDF Manual</a>
                            <a class="dropdown-item" href="<?php echo base_url() . 'manual/vidcontutorial'; ?>"> Video Conference Tutorial</a>
                            <a class="dropdown-item" href="<?php echo base_url() . 'assets/video/houserules/generalhouserulesvcon.pptx'; ?>"> Video Conference House Rules</a>
                            <!-- <a class="dropdown-item" href="<?php #echo base_url().'manual/googleformtemplate';
                                                                ?>" > Google Form Template</a> -->
                        </div>
                    </li>

                    <li class="nav-item dropdown" id="srli_form">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdowngoogleform" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-columns"></i> Google Forms
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdowngoogleform">
                            <a class="dropdown-item" href="https://docs.google.com/forms/d/1XrbdROb9U2A45e5yrU5ddf7iKpvcdv21BzwfNwjCkDk/copy" target="_blank"> Training Attendance</a>
                            <a class="dropdown-item" href="https://docs.google.com/forms/u/2/d/1B6gg4Gqg-BP2W9427RAuX7HsEgJrDoKIQtMAqEOOF9E/copy" target="_blank">Training Evaluation</a>
                        </div>
                    </li>

                    <?php if ($priv_ != 'Client') { ?>
                        <li class="nav-item" id="srli_vidcon">
                            <a class="nav-link" href="<?php echo base_url() . 'report/vidcon'; ?>"><i class="fas fa-list"></i> VidCon Approved</a>
                        </li>

                        <li class="nav-item dropdown" id="srli_report">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-chart-bar"></i> Report
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="<?php echo base_url() . 'report/srf'; ?>">SRF List</a>
                                <a class="dropdown-item" href="<?php echo base_url() . 'report/srf-assignment-resolution'; ?>"> SRF Assignment &amp; Resolution Report </a>
                                <a class="dropdown-item" href="<?php echo base_url() . 'report/srfsummary'; ?>">SRF Summary</a>
                                <a class="dropdown-item" href="<?php echo base_url() . 'report/srfsummaryarta'; ?>">SRF Summary for ARTA</a>
                            </div>
                        </li>
                    <?php
                    }

                    if ($priv_ === 'Superadmin') {
                    ?>
                        <li class="nav-item dropdown" id="srli_library">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownLibrary" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-chart-bar"></i> Library
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownLibrary">
                                <a class="dropdown-item" href="<?php echo base_url() . 'library/category'; ?>">Category Request</a>
                                <a class="dropdown-item" href="<?php echo base_url() . 'library/subcategory'; ?>">Subcategory Request</a>
                            </div>
                        </li>
                    <?php
                    }
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $this->session->userdata('domainname'); ?>"><i class="fas fa-external-link-alt"></i> Intranet </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:void(0);" id="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>

                <form class="form-inline my-2 my-lg-0">
                    <div class="welcome">Welcome, <?php echo strtoupper($this->session->userdata('uname') . '-' . $this->session->userdata('secabbrev')); ?></div>
                </form>
            </div>
        </nav>
    </div>
</div>

<div class="my-3">
    <img class="header-img" src="<?php echo base_url() . 'assets/img/header.png'; ?>">
</div>

<!-- 
<div class="text-center p-3" style="background: #fff;">
    <div class="mx-5">
        <div class="alert alert-warning border-left border-warning shadow-sm" role="alert" style="border-left:5px solid #ffc107 !important;">
            <div class="d-flex align-items-start">
                <div class="mr-3" style="font-size:4rem;">
                    <i class="fa fa-exclamation-circle text-warning"></i>
                </div>
                <div>
                    <h4 class="alert-heading mb-2 font-weight-bold">
                        ICTU Advisory
                    </h4>
                    <p class="mb-0">
                        Please be advised that the <strong>ICTU personnel will be attending an online workshop on 
                        Information Systems Documentation from July 21–23, 2026.</strong>
                        As a result, there may be delays in responding to requests during this period.
                        Rest assured that all requests will be attended to as soon as possible after the workshop.
                        Thank you for your patience and understanding.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div> -->