<?php 
    include '../headlink.php';

    if(!isset($_SESSION['intraval']['loggedin'])) {
        header("Location: $domain/auth");
    }

?>

<body style="background-color: #e6f1eb;">
    <div class="">
        <?php include '../header.php'; ?>

            <div class="container" id="rightside">
                <div class="col-md-12">
                    <section id="content_id">
                        <div class="error-page">
                            <h2 class="headline text-red">405</h2>
                            <div class="error-content">
                                <h3><i class="fa fa-warning text-red"></i> Method Not Allowed</h3>
                                <p>
                                    Your not allowed to view this page.
                                    Click here to <a href="<?php echo $domain;?>">return to dashboard</a>.
                                </p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
  
    </div>
</body>
