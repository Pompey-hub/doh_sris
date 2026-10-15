<!DOCTYPE html>
<html>
    <head>
        <title><?php echo 'Service Request Report Summary Dated ' . date('F d, Y H:i:s');?></title>
        <link rel="stylesheet" media="print" href="<?php echo base_url();?>assets/css/pdfprint.css">   
        <style>
            .wd1 {
                width: 30mm;
            }
            .wd2 {
                width: 40mm;
            }
            .wd3 {
                width: 40mm;
            }
            .wd4 {
                width: 25mm;
            }

            .wd5 {
                width: 25mm;
            }

            .wd6 {
                width: 25mm;
            }

            .wd23 {
                width: 20mm;
            }


            .wds1, .wds3 {
                width: 70mm;
            }

            .wds2 {
                width: 50mm;
            }
        </style>
    </head>
    <body>
        <!-- <htmlpageheader name="header">
            
        </htmlpageheader> -->
        <htmlpagefooter name="footer">
            <div id="footer" style="text-align: center; font-size: 3mm;"> Page {PAGENO} of {nbpg} </div>
            <div id="footers" style="text-align: right; font-size: 2.5mm;"><i>Date Generated: <?php echo date('F d, Y H:i:s');?></i></div>
        </htmlpagefooter>
        <sethtmlpagefooter name="footer" value="on" />

        <!-- <sethtmlpageheader name="header" value="on" show-this-page="1" /> -->

        <div style="margin-bottom: 10px;">
            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th rowspan="4" style="text-align: right; width: 47mm;"><img src="<?php echo base_url();?>assets/img/dohlogo.png" style="width: 60px;" ></th>
                        <th style="width: 95mm;">Republic of the Philippines</th>
                        <th rowspan="4" style="text-align: left;">
                            <img src="<?php echo base_url();?>assets/img/ro8logo.png" style="width: 60px;" >
                            <img src="<?php echo base_url();?>assets/img/f1plus.png" style="width: 60px;" >
                        </th>
                    </tr>
                    <tr>
                        <th>Department of Health</th>
                    </tr>
                    <tr>
                        <th>EASTERN VISAYAS - CENTER FOR HEALTH DEVELOPMENT</th>
                    </tr>
                    <tr>
                        <th>Government Center, Candahug, Palo, Leyte</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th style="color: #fff;">--</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>MSD-ICTU</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th colspan="3">Internal Service Request Report Summary <?php echo ($off=='-1'? '(All)' : '');?></th>
                    </tr>
                    <tr>
                        <th colspan="3"><?php echo $mm_g . ' ' . $yy_g;?></th>
                    </tr>
                </thead>
            </table>
        </div>
        <br>


        <table class="table" cellpadding="5">
            <thead>
                <tr>
                    <th rowspan="2" class="wd1">Actions Taken</th>
                    <th colspan="2" class="wd2">Number of Requests for ITE Support/Others</th>
                    <th colspan="2" class="wd3">Number of Requests for Technical Assistance</th>
                    <th rowspan="2" class="wd4">Number of Requests for Video Conference</th>
                    <th rowspan="2" class="wd5">Other Concerns</th>
                    <th rowspan="2" class="wd6">TOTAL</th>
                </tr>
                <tr>
                    <th class="wd23">Hardware</th>
                    <th class="wd23">Software</th>
                    <th class="wd23">DOH HIS</th>
                    <th class="wd23">DOH EV-CHD IN-HOUSE INFORMATION SYSTEM</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Received</td>
                    <td><?php echo isset($rcvd[0]['cnt_HW']) && $rcvd[0]['cnt_HW'] != '0' ? $rcvd[0]['cnt_HW'] : '';?></td>
                    <td><?php echo isset($rcvd[0]['cnt_SW']) && $rcvd[0]['cnt_SW'] != '0' ? $rcvd[0]['cnt_SW'] : '';?></td>
                    <td><?php echo isset($rcvd[0]['cnt_HIS']) && $rcvd[0]['cnt_HIS'] != '0' ? $rcvd[0]['cnt_HIS'] : '';?></td>
                    <td><?php echo isset($rcvd[0]['cnt_IIS']) && $rcvd[0]['cnt_IIS'] != '0' ? $rcvd[0]['cnt_IIS'] : '';?></td>
                    <td><?php echo isset($rcvd[0]['cnt_VC']) && $rcvd[0]['cnt_VC'] != '0' ? $rcvd[0]['cnt_VC'] : '';?></td>
                    <td><?php echo isset($rcvd[0]['cnt_OC']) && $rcvd[0]['cnt_OC'] != '0' ? $rcvd[0]['cnt_OC'] : '';?></td>
                    <td>
                        <?php 
                            echo isset($rcvd[0]['cnt_HW']) ? ($rcvd[0]['cnt_HW'] + $rcvd[0]['cnt_SW'] + $rcvd[0]['cnt_HIS'] + $rcvd[0]['cnt_IIS'] + $rcvd[0]['cnt_VC'] + $rcvd[0]['cnt_OC'] + $rcvd[0]['cnt_unk'] ) : '';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>For Action</td>
                    <td><?php echo isset($actn[0]['cnt_HW']) && $actn[0]['cnt_HW'] != '0' ? $actn[0]['cnt_HW'] : '';?></td>
                    <td><?php echo isset($actn[0]['cnt_SW']) && $actn[0]['cnt_SW'] != '0' ? $actn[0]['cnt_SW'] : '';?></td>
                    <td><?php echo isset($actn[0]['cnt_HIS']) && $actn[0]['cnt_HIS'] != '0' ? $actn[0]['cnt_HIS'] : '';?></td>
                    <td><?php echo isset($actn[0]['cnt_IIS']) && $actn[0]['cnt_IIS'] != '0' ? $actn[0]['cnt_IIS'] : '';?></td>
                    <td><?php echo isset($actn[0]['cnt_VC']) && $actn[0]['cnt_VC'] != '0' ? $actn[0]['cnt_VC'] : '';?></td>
                    <td><?php echo isset($actn[0]['cnt_OC']) && $actn[0]['cnt_OC'] != '0' ? $actn[0]['cnt_OC'] : '';?></td>
                    <td>
                        <?php 
                            echo isset($actn[0]['cnt_HW']) ? ($actn[0]['cnt_HW'] + $actn[0]['cnt_SW'] + $actn[0]['cnt_HIS'] + $actn[0]['cnt_IIS'] + $actn[0]['cnt_VC'] + $actn[0]['cnt_OC'] + $actn[0]['cnt_unk'] ) : '';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Accomplished</td>
                    <td><?php echo isset($accomp[0]['cnt_HW']) && $accomp[0]['cnt_HW'] != '0' ? $accomp[0]['cnt_HW'] : '';?></td>
                    <td><?php echo isset($accomp[0]['cnt_SW']) && $accomp[0]['cnt_SW'] != '0' ? $accomp[0]['cnt_SW'] : '';?></td>
                    <td><?php echo isset($accomp[0]['cnt_HIS']) && $accomp[0]['cnt_HIS'] != '0' ? $accomp[0]['cnt_HIS'] : '';?></td>
                    <td><?php echo isset($accomp[0]['cnt_IIS']) && $accomp[0]['cnt_IIS'] != '0' ? $accomp[0]['cnt_IIS'] : '';?></td>
                    <td><?php echo isset($accomp[0]['cnt_VC']) && $accomp[0]['cnt_VC'] != '0' ? $accomp[0]['cnt_VC'] : '';?></td>
                    <td><?php echo isset($accomp[0]['cnt_OC']) && $accomp[0]['cnt_OC'] != '0' ? $accomp[0]['cnt_OC'] : '';?></td>
                    <td>
                        <?php 
                            echo isset($accomp[0]['cnt_HW']) ? ($accomp[0]['cnt_HW'] + $accomp[0]['cnt_SW'] + $accomp[0]['cnt_HIS'] + $accomp[0]['cnt_IIS'] + $accomp[0]['cnt_VC'] + $accomp[0]['cnt_OC'] + $accomp[0]['cnt_unk'] ) : '';
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>


        <?php if ($off != '-1') { ;?>
            <br><br>
            <br><br>
            <table style="width: 100%;">
                <tbody>
                    <tr>
                        <td class="wds1" style="text-align: left;">Submitted by:</td>
                        <td class="wds2"></td>
                        <td class="wds3" style="text-align: left;">Noted by:</td>
                    </tr>
                    <tr>
                        <td style="color: #fff;">s</td>
                        <td></td>
                        <td style="color: #fff;">s</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #222;"><b><?php echo $actofficer[0]['fullname'];?></b></td>
                        <td></td>
                        <td style="border-bottom: 1px solid #222;"><b>ELMA BERNADETTE B. LEGO</b></td>
                    </tr>
                    <tr>
                        <td><?php echo strtoupper($actofficer[0]['dbposition']);?></td>
                        <td></td>
                        <td>COMPUTER MAINTENANCE TECHNOLOGIST III</td>
                    </tr>
                </tbody>
            </table>   
        <?php } ?>
        
    </body>
</html>
