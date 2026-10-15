<?php 
    $this->load->view('page/headlink'); 
?>
<style type="text/css">
    .table > tbody > tr > td {
        vertical-align: middle;
        text-align: center;
        padding: 15px 10px;
    }
</style>
<body>
    <?php $this->load->view('page/header'); ?>
    <div class="" id="wrapper">
        <div class="bg-white px-2">
            <div class="row pt-2 pb-1">
                <div class="col text-left" style="font-size: 1.2em;">
                    Video Conference Tutorial
                </div>
            </div>

            <div>
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td style="width: 45%">
                                <video style="width: 100%;" controls>
                                    <source src="<?php echo base_url().'assets/video/tutorial/HowtoJoinaCiscoWebexMeeting.mp4';?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </td>
                            <td style="width: 10%;"><i class="fa fa-arrow-circle-right fa-4x"></i></td>
                            <td style="width: 45%">
                                <h2>How to Join a Cisco Webex Meeting</h2>
                                <span class="text-muted"><i>https://www.youtube.com/watch?v=MKpMZrdZXGQ&t=54s</i></span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h2>How To Join a Cisco Webex Meeting from your Android Phone</h2>
                                <span class="text-muted"><i>https://www.youtube.com/watch?v=8jcBswuDaGk&t=19s</i></span>
                            </td>
                            <td><i class="fa fa-arrow-circle-left fa-4x"></i></td>
                            <td>
                                <video style="width: 100%;" controls>
                                    <source src="<?php echo base_url().'assets/video/tutorial/HowToJoinaCiscoWebexMeetingfromyourAndroidPhone.mp4';?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <video style="width: 100%;" controls>
                                    <source src="<?php echo base_url().'assets/video/tutorial/HowtoJoinaCiscoWebexMeetingfromaniOSMobileDevice.mp4';?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </td>
                            <td><i class="fa fa-arrow-circle-right fa-4x"></i></td>
                            <td>
                                <h2>How to Join a Cisco Webex Meeting from an iOS Mobile Device</h2>
                                <span class="text-muted"><i>https://www.youtube.com/watch?v=rbLABQEhYkw</i></span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h2>GOOGLE MEET TUTORIAL 2020 - How To Use Google Meet STEP BY STEP For Beginners! [COMPLETE GUIDE]</h2>
                                <span class="text-muted"><i>https://www.youtube.com/watch?v=d8L2bbZxvYQ&t=96s</i></span>
                            </td>
                            <td><i class="fa fa-arrow-circle-left fa-4x"></i></td>
                            <td>
                                <video style="width: 100%;" controls>
                                    <source src="<?php echo base_url().'assets/video/tutorial/GoogleMeetTutorial2020.mp4';?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>





           
        </div>
    </div>

    <?php $this->load->view('page/footer'); ?>

</body>
