$(document).ready(function() {
    'use strict';
    $('#srli_home').addClass('active');
    $('#tsrclient').select2({placeholder: 'Select...', allowClear: true});
    
    function isEmail(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    }

    $('#wrapper').on('click', '#sr_reqsubmit', function() {
        var tsrclient = $('select[name="tsrclient"]').val();
        var tsrmobilenum = $('input[name="tsrmobilenum"]').val();
        var tsremail = $.trim($('input[name="tsremail"]').val());
        var tvcdplatform = $('input[name="tvcdplatform"]').val();

        var tvcdtitle = $('textarea[name="tvcdtitle"]').val();
        var tvcddate = $('input[name="tvcddate"]').val();
        var tvcdtimestart = $('input[name="tvcdtimestart"]').val();
        var tvcdtimeend = $('input[name="tvcdtimeend"]').val();
        var tvcdusertype = $('select[name="tvcdusertype"]').val();

        var trrid = $('select[name="trrid"]').val();
        //var tvcdremarks = $('textarea[name="tvcdremarks"]').val();

        var tvcpname='', tvcpemail='', tvcppos='', tvcpoffice='', error=false;
        


        if ($.trim(tsrclient).length == 0 || $.trim(tsrmobilenum).length == 0 || 
            $.trim(tsremail).length == 0 || $.trim(tvcddate).length == 0 || 
            $.trim(tvcdtitle).length == 0 || $.trim(tvcdplatform).length == 0 || 
            $.trim(tvcdtimestart).length == 0 || $.trim(tvcdtimeend).length == 0 || 
            $.trim(tvcdusertype).length == 0 || $.trim(trrid).length == 0) {
            Swal.fire({
                title: "Warning!",
                text: "Please complete all required fields.",
                icon: "warning"
            });
        } else if (!isEmail(tsremail)) {
            Swal.fire({
                title: "Warning!",
                text: "Your email address is invalid!",
                icon: "warning"
            });
        } else {
            if (tvcdplatform == 'OTHERS' && $.trim($('input[name="tvcdplatform_other"]').val()).length == 0) {
                Swal.fire({
                    title: "Warning!",
                    text: "Please specify other platform!",
                    icon: "warning"
                });
                error = true;
            }

            if (tvcdusertype == 'Host') {
                var tvcdnumpar = $('#tvcdnumpar').val();
                if ($.trim(tvcdnumpar).length == 0) {
                    Swal.fire({
                        title: "Warning!",
                        text: "# of participants must not be empty.",
                        icon: "warning"
                    });
                    error = true;
                    return false;
                }

                var host_size = $('input[name="tvcpname[]"]').length;
                if (host_size < 1) {
                    Swal.fire({
                        title: "Warning!",
                        text: "List of participants: Please complete all required fields.",
                        icon: "warning"
                    });
                    error = true;
                    return false;
                }

                $('#tbl_participant_body tr').each(function(row, tr) {
                    tvcpname = $(tr).find('td input[name="tvcpname[]"]').val();
                    tvcppos = $(tr).find('td input[name="tvcppos[]"]').val();
                    tvcpoffice = $(tr).find('td input[name="tvcpoffice[]"]').val();
                    tvcpemail = $(tr).find('td input[name="tvcpemail[]"]').val();
                    if (tvcpname.length === 0 || tvcpemail.length === 0 || tvcppos.length === 0 || tvcpoffice.length === 0) {
                        Swal.fire({
                            title: "Warning!",
                            text: "List of participants: Please complete all required fields.",
                            icon: "warning"
                        });
                        error = true;
                        return false;
                    }

                    if (!isEmail(tvcpemail)) {
                        Swal.fire({
                            title: "Warning!",
                            text: "Invalid Email address of " + tvcpname,
                            icon: "warning"
                        });
                        error = true;
                        return false;
                    }
                }); 
            }

            if (error === false) {
                Swal.fire({
                    title: "Are you sure?",
                    text: "Submit this request.",
                    icon: "info",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Confirm!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        var tsrid = $('input[name="tsrid"]').val();
                        $('#myspinner').show();
                        $('#sr_form').submit();
                    }
                });       

            }
        }
    });

    
    $('#wrapper').on('change', '#tvcdusertype', function() {
        var tvcdusertype = $(this).val();
        if (tvcdusertype == 'Host') {
            $('#tbl_participant').show(100);

            $('#tvcdnumpar_head_div').html('<i class="fas fa-asterisk fa-xs required"></i> # of Participants');
            $('#tvcdnumpar_val_div').show(200);
        } else {
            $('#tbl_participant').hide(100);

            $('#tvcdnumpar_head_div').text('');
            $('#tvcdnumpar_val_div').hide(200);
        }
    });

    $('#wrapper').on('change', 'input[name="tvcdplatform"]', function() {
        var tvcdplatform = $(this).val();
        if (tvcdplatform === 'OTHER') {
            $('#tvcdplatform_other').show(100);
        } else {
            $('#tvcdplatform_other').hide(100);
        }
    });


    


    $('#wrapper').on('click', '#addpart', function() {
        var str = '';
        str += '<tr>';
            str += '<td><input type="text" name="tvcpname[]" class="form-control" value="" placeholder=""></td>';
            str += '<td><input type="text" name="tvcppos[]" class="form-control" value="" placeholder=""></td>';
            str += '<td><input type="text" name="tvcpoffice[]" class="form-control" value="" placeholder=""></td>';
            str += '<td><input type="text" name="tvcpemail[]" class="form-control" value="" placeholder=""></td>';
            str += '<td>';
                str += '<input type="hidden" name="tvcpid[]" class="form-control" value="" placeholder="">';
                str += '<a href="javascript:void(0);" name="adelpart"><i class="fas fa-trash required fa-2x"></i></a>';
            str += '</td>';
        str += '</tr>';
        

        var host_size = $('input[name="tvcpname[]"]').length;
        if (host_size < 1) {
            $('#tbl_participant_body').append(str);
        } else {
            $('#tbl_participant_body tr:last').after(str);
        }

    });

    $('#wrapper').on('click', 'a[name="adelpart"]', function() {
        $(this).closest('tr').remove();
    });

    




    




    /*delete below */


    $('#wrapper').on('click', 'a[name="sractiontaken"]', function() {
        var xtsrid = $(this).attr('data-xtsrid');
        $('input[name="xtsrid"]').val(xtsrid);
        $('select[name="tlsrsid"]').val('');
        $('textarea[name="takdesc"]').val('');

        $('#srmodalactiontakengroupdiv').hide(100);

        $('#myspinner').show();
        $.get('servicerequest/srtbllibsrstatus', {'xtsrid':xtsrid}, function(data) {
            var res = JSON.parse(data);
            var len = res.tlsrstatus.length;
            var str = '<option value="">Select...</option>';
            var arr_ord = ['1']; //arr = ['-1-'], 
            var order_temp = '0';
            
            if (res.atoptdisabled.length > 0) {
                //arr = (res.atoptdisabled[0].tlsrsdesc).split(',');
                arr_ord = (res.atoptdisabled[0].tlsrorder).split(',');
            }
            
            var last_arr_ord = arr_ord[arr_ord.length - 1] * 1;
            var ctr = false;

            for (var i = 0; i < len; i++) {
                if (last_arr_ord < res.tlsrstatus[i].tlsrorder * 1) {
                    if (ctr == false) {
                        order_temp = res.tlsrstatus[i].tlsrorder;
                        ctr = true;
                    }

                    if (order_temp != res.tlsrstatus[i].tlsrorder) {
                        break;
                    }

                    str += '<option value="'+res.tlsrstatus[i].tlsrsid+'">'+res.tlsrstatus[i].tlsrsdesc+'</option>';
                    
                }
            }
            $('select[name="tlsrsid"]').html(str);
            
            $('#srmodalactiontaken').modal({
                backdrop:'static',
                keyboard: false
            });
        })
        .always(function() {
            $('#myspinner').hide();
        });

    });

    $('#srmodalactiontaken').on('click', '#srbtnsavechanges', function() {
        var xtlsrsid = $.trim($('select[name="tlsrsid"').val());
        if (xtlsrsid.length === 0) {
            Swal.fire({
                title: "Warning!",
                text: "Please complete all required fields.",
                icon: "warning"
            });
        } else {
            var xtlsid = $.trim($('select[name="xtlsid"').val());
            var xtlssys = $.trim($('select[name="xtlssys"').val());
            var takdesc = $.trim($('textarea[name="takdesc"').val());

            var action = $('select[name="tlsrsid"] option:selected').text();

            if (action === 'Accomplished' && (xtlsid.length === 0 || xtlssys.length === 0 || takdesc.length === 0)) {
                Swal.fire({
                    title: "Warning!",
                    text: "Please complete all required fields.",
                    icon: "warning"
                });
            } else {
                $('#srformreceived').submit();
            }
        }
    });


    /* show log status of action taken */
    var srlogstatus = function(xtsrid) {
        $('#myspinner').show();
        $.get('servicerequest/srlogstatus', {'xtsrid':xtsrid}, function(data) {
            var res = JSON.parse(data);
            var len = res.srlogstatus.length;
            var str = '';

            $('#srtakremarks').html(''+res.tsrreqdesc);

            for (var i = 0; i < len; i++) {
                str += '<tr>';
                str += '<td>'+res.srlogstatus[i].takdateadded+'</td>'; 
                str += '<td>'+res.srlogstatus[i].taktimeadded+'</td>'; 
                str += '<td><i>'+res.srlogstatus[i].tlsrsdesc+'</i></td>';
                str += '<td>'+res.srlogstatus[i].takdesc+'</td>';
                str += '<td>'+res.srlogstatus[i].takremarks+'</td>';
                str += '<td>'+(res.srlogstatus[i].officer == null ? '' : res.srlogstatus[i].officer)+'</td>';
                if (res.tllpriv == 'Admin') {
                    if (res.srlogstatus[i].takstatus === "Active") {
                        str += '<td>';
                            str += '<a href="javascript:void(0);" data-xtakid="'+res.srlogstatus[i].takid+'" name="sreditlogstatus" class="'+(res.srlogstatus[i].tlsrsdesc==='Accomplished'?'':'disabled')+'"><i class="fas fa-edit"></i></a> ';
                            //str += '<a href="javascript:void(0);" data-xtakid="'+res.srlogstatus[i].takid+'" name="srdeletelogstatus" ><i class="fas fa-trash-alt"></i></a>';
                        str += '</td>';
                    } else {
                        str += '<td><span class="badge badge-danger" title="Removed by">'+res.srlogstatus[i].delofficer+'</span></td>';
                    }
                }
                str += '</tr>';
            }

            if (len === 0) {
                if (res.tllpriv == 'Admin') {
                    str = '<tr><td colspan="7" class="text-center">No data available in table</td></tr>';
                } else {
                    str = '<tr><td colspan="6" class="text-center">No data available in table</td></tr>';
                }
                
            }

            $('#tbodysrlogstatus').html(str);
            
            $('#srmodallogstatus').modal({
                backdrop:'static',
                keyboard: false
            });
        })
        .always(function() {
            $('#myspinner').hide();
        });
    };

    $('#wrapper').on('click', 'a[name="srlogstatus"]', function() {
        var xtsrid = $(this).attr('data-xtsrid');
        $('input[name="modxtsrid"]').val(xtsrid);
        srlogstatus(xtsrid);
    });


    $('#srmodalactiontaken').on('change', 'select[name="tlsrsid"]', function() {
        $('textarea[name="takdesc"]').val('');
        var action = $('select[name="tlsrsid"] option:selected').text();
        if (action === 'Accomplished') {
            $('#srmodalactiontakengroupdiv').show(100);
        } else {
            $('#srmodalactiontakengroupdiv').hide(100);
        }
    });
    

    $('#srmodallogstatus').on('click', 'a[name="sreditlogstatus"]', function() {
        var xtakid = $(this).attr('data-xtakid');
        $('input[name="xtakid"]').val(xtakid);

        $('#myspinner').show();
        $.get('servicerequest/srseleditactiontaken', {'xtakid':xtakid}, function(data) {
            var res = JSON.parse(data);
            $('textarea[name="takdesc"]').val(res.res[0].takdesc);
            $('select[name="extlsid"]').val(res.res[0].tlsid);
            $('select[name="extlssys"]').val(res.res[0].tlssys);

            
            $('#srmodaleditactiontaken').modal({
                backdrop:'static',
                keyboard: false
            });
        })
        .always(function() {
            $('#myspinner').hide();
        });
    });


    $('#srmodaleditactiontaken').on('click', '#srbtneditsavechanges', function() {
        /*var xtsrid = $(this).attr('data-xtsrid');*/

        $('#myspinner').show();
        $.get('servicerequest/sreditactiontaken', $('#srformeditreceived').serialize(), function(data) {
            var res = JSON.parse(data);
            if (res.sreditat) {
                Swal.fire({
                    title: "Success!",
                    text: "Save successful!",
                    icon: "success"
                });
                
                $('#srmodaleditactiontaken').modal('toggle');
                srlogstatus($('input[name="modxtsrid"]').val());
            } else {
                Swal.fire({
                    title: "Error!",
                    text: "Ooops! There\'s something wrong!",
                    icon: "error"
                });
            }

            $('input[name="csrf_token_"]').val(res.csrfhash);
        })
        .always(function() {
            $('#myspinner').hide();
        });
    });

    

});