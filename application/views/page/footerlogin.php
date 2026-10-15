<script type="text/javascript" src="<?php echo base_url() . 'assets/credits/bootstrap4.3.1/js/bootstrap.min.js';?>"></script>
<script type="text/javascript" src="<?php echo base_url() . 'assets/credits/datatable/jquery.dataTables.min.js';?>"></script>
<script type="text/javascript" src="<?php echo base_url() . 'assets/credits/datatable/dataTables.bootstrap4.min.js';?>"></script>
<script type="text/javascript" src="<?php echo base_url() . 'assets/credits/toastr/toastr.min.js';?>"></script>


<script type="text/javascript">
    $(document).ready(function() {
        <?php if($this->session->flashdata('success')){ ?>
                toastr.success("<?php echo $this->session->flashdata('success');?>");
        <?php } else if ($this->session->flashdata('error')) { ?>
                toastr.error("<?php echo $this->session->flashdata('error');?>");
        <?php } ?>
    });
</script>
