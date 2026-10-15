<?php $this->load->view('page/headlink'); ?>
<style type="text/css">
    .table td, .table th {
        padding: .5rem;
    }
    .table {
        margin-bottom: 0;
    }
</style>
<body>
    <?php $this->load->view('page/header'); ?>
    <div class="container" id="wrapper">
        <div class="bg-white px-2">
            <!-- <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-light">
                    <li class="breadcrumb-item"><a href="<?php #echo base_url();?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php #echo base_url().'users';?>">Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php #echo ($this->input->get('q') == NULL) ? 'Add' : 'Edit';?> User</li>
                </ol>
            </nav> -->

            <div class="row pt-2 pb-1">
                <div class="col text-left" style="font-size: 1.2em;">
                    <?php echo ($this->input->get('q') == NULL) ? 'Add' : 'Edit';?> User
                </div>
            </div>

            <div class="col-6">
                <?php $this->load->view('page/validation'); ?>
                <form method="post">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>" />
                    <input type="hidden" name="srtllid" value="<?php echo $this->input->get('q');?>">

                    <div class="form-group row">
                        <label for="" class="col-sm-3 col-form-label"><i class="fas fa-asterisk fa-xs required"></i> Facility Type</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="srtllfactype" required>
                                <option value="">Select...</option>
                                <option value="DOH" <?php echo isset($res[0]['tllfactype']) && $res[0]['tllfactype'] === 'DOH'? 'selected' : '';?> >DOH</option>
                                <option value="PHO" <?php echo isset($res[0]['tllfactype']) && $res[0]['tllfactype'] === 'PHO'? 'selected' : '';?> >PHO</option>
                                <option value="CHO" <?php echo isset($res[0]['tllfactype']) && $res[0]['tllfactype'] === 'CHO'? 'selected' : '';?> >CHO</option>
                                <option value="RHU" <?php echo isset($res[0]['tllfactype']) && $res[0]['tllfactype'] === 'RHU'? 'selected' : '';?> >RHU</option>
                                <option value="HOSPITAL" <?php echo isset($res[0]['tllfactype']) && $res[0]['tllfactype'] === 'HOSPITAL'? 'selected' : '';?> >HOSPITAL</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="" class="col-sm-3 col-form-label"><i class="fas fa-asterisk fa-xs required"></i> Privilege</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="srtllpriv" required>
                                <option value="">Select...</option>
                                <option value="1" <?php echo isset($res[0]['tllpriv']) && $res[0]['tllpriv'] === '1'? 'selected' : '';?> >Client</option>
                                <option value="2" <?php echo isset($res[0]['tllpriv']) && $res[0]['tllpriv'] === '2'? 'selected' : '';?> >Admin</option>
                                <option value="99" <?php echo isset($res[0]['tllpriv']) && $res[0]['tllpriv'] === '99'? 'selected' : '';?> >Super Admin</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row" id="srempid_div" style="display: <?php echo isset($res[0]['tllpriv']) && $res[0]['tllpriv'] === '1'? 'none' : 'flex'; ?>">
                        <label for="" class="col-sm-3 col-form-label"><i class="fas fa-asterisk fa-xs required"></i> Name</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="srempid" <?php echo isset($res[0]['tllpriv']) && $res[0]['tllpriv'] === '1'? '' : 'required'; ?>>
                                <option value="">Select...</option>
                                <?php 
                                    $len = count($emplist);
                                    for ($i=0; $i < $len; $i++) { 
                                        $sel = isset($res[0]['empid']) && $res[0]['empid'] === $emplist[$i]['empid'] ? 'selected' : '';
                                ?>
                                        <option value="<?php echo $this->cryptography->encrypt($emplist[$i]['empid']);?>" <?php echo $sel;?> ><?php echo $emplist[$i]['fname'] . ' ' . $emplist[$i]['iname'] . '. ' . $emplist[$i]['lname'] . ' ' . $emplist[$i]['suffix'];?></option>
                                <?php 
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                     <div class="form-group row">
                        <label for="" class="col-sm-3 col-form-label"><i class="fas fa-asterisk fa-xs required"></i> Username</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Start typing here.." name="srtlluname" value="<?php echo isset($res[0]['tlluname'])?$res[0]['tlluname']:'';?>" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-3 col-form-label"><i class="fas fa-asterisk fa-xs required"></i> Office</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="srtllsecid" required>
                                <option value="">Select...</option>
                                <?php 
                                    $len = count($offlist);
                                    for ($i=0; $i < $len; $i++) { 
                                        $sel = isset($res[0]['secid']) && $res[0]['secid'] === $offlist[$i]['secid'] ? 'selected' : '';
                                ?>
                                        <option value="<?php echo $this->cryptography->encrypt($offlist[$i]['secid']);?>" <?php echo $sel;?> ><?php echo $offlist[$i]['divabbrev'] . '-' . $offlist[$i]['secabbrev'];?></option>
                                <?php 
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="" class="col-sm-3 col-form-label">Position</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="srdbpid">
                                <option value="">Select...</option>
                                <?php 
                                    $len = count($poslist);
                                    for ($i=0; $i < $len; $i++) { 
                                        $sel = isset($res[0]['dbpid']) && $res[0]['dbpid'] === $poslist[$i]['dbpid'] ? 'selected' : '';
                                ?>
                                        <option value="<?php echo $this->cryptography->encrypt($poslist[$i]['dbpid']);?>" <?php echo $sel;?> ><?php echo $poslist[$i]['dbposition'];?></option>
                                <?php 
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    
                     <div class="form-group row">
                        <label for="" class="col-sm-3 col-form-label">Email</label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control" placeholder="Start typing here.." name="srtllemail" value="<?php echo isset($res[0]['tllemail'])?$res[0]['tllemail']:'';?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-3 col-form-label">Contact #</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" placeholder="Start typing here.." name="srtllnumber" value="<?php echo isset($res[0]['tllnumber'])?$res[0]['tllnumber']:'';?>" >
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-3 col-form-label"><i class="fas fa-asterisk fa-xs required"></i> Status</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="srtllstatus" required>
                                <option value="">Select...</option>
                                <option value="Active" <?php echo isset($res[0]['tllstatus']) && $res[0]['tllstatus'] === 'Active'? 'selected' : '';?> >Active</option>
                                <option value="Inactive" <?php echo isset($res[0]['tllstatus']) && $res[0]['tllstatus'] === 'Inactive'? 'selected' : '';?> >Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="" class="col-sm-3 col-form-label"></label>
                        <div class="col-sm-9">
                            <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane"></i> Submit</button>
                            <a href="<?php echo base_url() . 'users';?>" class="btn btn-danger"><i class="fas fa-window-close"></i> Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php $this->load->view('page/footer'); ?>

</body>

 <script type="text/javascript" src="<?php echo base_url() . 'assets/js/usercredentials.js';?>"></script>
