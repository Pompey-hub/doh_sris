<?php $this->load->view('page/headlink'); ?>
<body>
    <?php $this->load->view('page/header'); ?>
    <div id="wrapper" style="padding-left: 5px; padding-right: 5px;">
        <div class="bg-white px-2">
            <div class="row pt-2 pb-4">
                <div class="col-4 text-left" style="font-size: 1.2em;">
                    Lists of Approved Video Conference <span class="text-muted">(Total Entries: <?php echo count($res);?>)</span>
                </div>
                <div class="col-8 text-right">
                    <?php 
                        $mm = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                    ?>  

                    <form class="form-inline" style="float: right;">

                        <div class="form-group mx-1 mb-2">
                            <select class="form-control" name="mm">
                                <option value="" disabled>Select month..</option>
                                <?php 
                                    $len = count($mm);
                                    for ($i=0; $i < $len; $i++) { 
                                        $sel = $mm_g === $mm[$i] ? 'selected' : '';
                                ?>
                                        <option value="<?php echo $mm[$i];?>" <?php echo $sel;?>><?php echo $mm[$i];?></option>
                                <?php 
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group mx-1 mb-2">
                            <select class="form-control" name="yy">
                                <option value="" disabled>Select year..</option>
                                <?php 
                                    $yy = date('Y');
                                    for ($i=$yy; $i > 2018; $i--) { 
                                        $sel = $yy_g === $i ? 'selected' : '';
                                ?>
                                        <option value="<?php echo $i;?>" <?php echo $sel;?>><?php echo $i;?></option>
                                <?php 
                                    }
                                ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success mx-1 mb-2">Generate</button>
                    </form>
                </div>
            </div>
            

            <table class="table table-striped" style="text-align: center;">
                <thead>
                    <tr>
                    	<th style="width: 6%;">Ticket #</th>
                    	<th style="width: 7%;">Date Requested</th>
                    	<th style="width: 6%;">Time Requested</th>
                    	<th style="width: 10%;">Name of Client</th>
                    	<th style="width: 6%;">Office</th>
                    	<th style="width: 6%;">Platform</th>
                        <th style="width: 5%;"># of Participants</th>
                    	<th style="width: 16%;">Topic / Title</th>
                    	<th style="width: 7%;">Date Schedule</th>
                    	<th style="width: 6%;">Time Start</th>
                    	<th style="width: 6%;">Time End</th>
                    	<th style="width: 19%;">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                	<?php 
                		$len = count($res);
                		for ($i=0; $i < $len; $i++) { 
                	?>
                			<tr>
	                			<td><?php echo date('ymdHis', strtotime($res[$i]['tsrdateadded']));?></td>
		                    	<td><?php echo date('M d, Y', strtotime($res[$i]['tsrdateadded']));?></td> 
		                    	<td><?php echo date('H:i:s', strtotime($res[$i]['tsrdateadded_time']));?></td>
		                    	<td><?php echo $res[$i]['fname'] . ' ' . $res[$i]['lname'];?></td>
		                    	<td><?php echo $res[$i]['office'];?></td>
		                    	<td><?php echo $res[$i]['tvcdplatform'];?></td>
                                <td><?php echo ($res[$i]['tvcdnumpar'] === '0' ? '' : $res[$i]['tvcdnumpar']);?></td>
		                    	<td><?php echo $res[$i]['tvcdtitle'];?></td>
		                    	<td class="sorting_bg"><?php echo date('M d, Y', strtotime($res[$i]['tvcddate']));?></td>
		                    	<td class="sorting_bg"><?php echo date('h:iA', strtotime($res[$i]['tvcdtimestart']));?></td>
		                    	<td><?php echo date('h:iA', strtotime($res[$i]['tvcdtimeend']));?></td>
		                    	<td><?php echo nl2br(html_escape($res[$i]['takremarks'], ENT_QUOTES));?></td>
                			</tr>
                	<?php 
                		}
                	?>
                </tbody>
            </table>
        </div>
    </div>
    <?php $this->load->view('page/footer'); ?>
</body>

<script type="text/javascript">
	$(document).ready(function() {
	    'use strict';

	    $('#srli_vidcon').addClass('active');
	});
</script>