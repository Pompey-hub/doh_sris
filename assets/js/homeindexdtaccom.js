$(document).ready(function() {
    'use strict';
    var servicerequest = function() {
        var table = $('#tbl_servicerequest').DataTable( {
            'responsive':true,
            'autoWidth':false,
            'processing': true,
            'serverSide': true,
            'stateSave': true,
            'destroy': true,
            'ajax': {
                url: 'servicerequest/dtrequestdtaccom',
                error: function () {
                    console.log('error');
                }
            },
            'columns': [
                { 'data': 'ticketnum' },
                { 'data': 'assoff_secabbrev',
                    "render": function(data,type,row,meta) {
                        var a = '<div class="no-wrap">' + row.assoff_divabbrev + '-' + row.assoff_secabbrev + '</div>';
                        return a;
                    } 
                },
                { 'data': 'tsrdateadded',
                    "render": function(data,type,row,meta) {
                        var a = '<div>' + row.tsrdateadded + '</div>';
                        a += '<div>' + row.tsrdateadded_time + '</div>';
                        return a;
                    } 
                },
                { 'data': 'tsrclient',
                    "render": function(data,type,row,meta) {
                        var a = '<div class="no-wrap">' + row.tsrclient + '</div>';
                        a += '<div>' + row.office + '</div>';
                        a += '<div>' + row.tsrmobilenum + '</div>';
                        return a;
                    } 
                },
                { 'data': 'tsrreqdesc',
                    "searchable": false,
                    "render": function(data,type,row,meta) {
                        var a = '<div>' + row.tlccat + ': ' + row.tlcproblem + '</div>';
                        a += '<div>' + row.tsrreqdesc + '</div>';
                        return a;
                    }  
                },
                { 'data': 'tlsrsdesc',
                    "searchable": false,
                    "render": function(data,type,row,meta) {
                        var a = '<a href="javascript:void(0);" title="Click to show log status" style="white-space: nowrap;" name="srlogstatus" class="'+row.tlsrsclass+'" data-xtsrid="'+row.tsrid+'"><i>' + row.tlsrsdesc + '</i></a> ';
                        return a;
                    }  
                },
                { 'data': 'takremarks' },
                { 'data': 'officer' },
                { 'data': 'uname' },
                { 'data': 'tsrid',
                    "searchable": false,
                    "render": function(data,type,row,meta) {
                        var a = '<div class="no-wrap">';
                        if (meta.settings.json.jspriv == 'client') {
                            // a += ' <a href="https://bit.ly/cssForm1" target="_blank" title="Fill up CSS" class="' + row.isdisabled + '" name="srcss"><i class="fas fa-pencil-alt fa-n2x"></i></a> ';
                            a += '<a href="javascript:void(0);" title="Mark as completed" data-tsrid_enc='+row.tsrid_enc+'  data-jsictu='+meta.settings.json.jsictu+' name="srconforme" data-xtsrid="'+row.tsrid+'" class="' + (row.tsrconforme==='Confirmed' ? 'disabled' : row.isdisabled) + '"><i class="fas fa-thumbs-up fa-n2x"></i></a> ';
                        } else {
                            a += '<a href="'+row.hrefroute+'view?q='+row.tsrid+'" title="view request"><i class="fas fa-eye fa-n2x"></i></a> ';
                            a += ' <a href="https://bit.ly/cssForm1" target="_blank" title="Fill up CSS" class="' + row.isdisabled + '" name="srcss"><i class="fas fa-pencil-alt fa-n2x"></i></a> ';
                            // a += '<a href="javascript:void(0);" title="Mark as completed" data-tsrid_enc='+row.tsrid_enc+'  data-jsictu='+meta.settings.json.jsictu+' name="srconforme" data-xtsrid="'+row.tsrid+'" class="' + (row.tsrconforme==='Confirmed' ? 'disabled' : row.isdisabled) + '"><i class="fas fa-thumbs-up fa-n2x"></i></a> ';
                        }
                        a += '</div>';
                        return a;
                    }  
                }
            ],
            initComplete:function( settings, json){
                /*$('#myspinner').hide();*/
            },
            'columnDefs': [ {
                'targets': [0,1,2,3,4,5,6,7,8,9],
                'orderable': false,
            }],
            'order': [[0, 'desc']]
        });


        $('.dataTables_filter input').unbind().keyup(function(e) {
            if(e.keyCode == 13) {
                var value = $(this).val();
                table.search(value).draw();
            }
        });
    };
    servicerequest();
    setInterval(servicerequest, 60 * 5000); /*refresh every 5 minutes*/
    


    $('#wrapper').on('click', 'a[name="srconforme"]', function() {
        var tsrid_enc = $(this).attr('data-tsrid_enc');
        var jsictu = $(this).attr('data-jsictu');
        $('#tsrid_enc').val(tsrid_enc);
        $('#jsictu').val(jsictu);

        var xtsrid_c = $(this).attr('data-xtsrid');
        $('input[name="xtsrid_c"]').val(xtsrid_c);
        $('#srmodalconforme').modal({
            backdrop:'static',
            keyboard: false
        });
    });


    $('#srmodalconforme').on('click', '#srbtnconformesavechanges', function() {
        $('#myspinner').show();
        $.post('servicerequest/conforme', $('#srformconforme').serialize(), function(data) {
            var res = JSON.parse(data);
            if (res) {
                toastr.success('Successfully  saved!');
                $('#srmodalconforme').modal('hide');

                $('#srmodalcss').modal({
                    backdrop:'static',
                    keyboard: false
                });

                
            } else {
                toastr.success('Ooops! There\'s something wrong.. Please try again!');
            }
        })
        .always(function() {
            $('#myspinner').hide();
        });

        //$('#srformconforme').submit();     
    });


    $('#srmodalcss').on('click', '#srbtncssproceed', function() {
        var tsrid_enc = $('#tsrid_enc').val();
        var jsictu = $('#jsictu').val();

        // $('#srmodalcss').modal('hide');
        // servicerequest();

        // window.open($(location).attr('protocol')+'//'+$(location).attr('host')+'/survey/addcss?q='+tsrid_enc+'&s='+jsictu, '_blank');
        window.open('https://bit.ly/cssForm1', '_blank');

        location.reload();
    });
    

    $('#srmodalcss').on('click', '#srbtncssclose, #srbtncssclosetimes', function() {
        location.reload();
    });

    
        
});
