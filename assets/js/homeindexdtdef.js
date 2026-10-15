$(document).ready(function() {
    'use strict';

    var tblservicerequest = function() {
        var table = $('#tbl_servicerequest').DataTable( {
            'responsive':true,
            'autoWidth':false,
            'processing': true,
            'serverSide': true,
            'stateSave': true,
            'destroy': true,
            'ajax': {
                url: 'servicerequest/dtrequestdtdef',
                error: function () {
                    console.log('error');
                }
            },
            'columns': [
                { 'data': 'ticketnum' },
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
                        return a;
                    } 
                },
                { 'data': 'tsrmobilenum' },
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
                { 'data': 'officer'},
                { 'data': 'officer',
                    "searchable": false,
                    "render": function(data,type,row,meta) {
                        var a = '';
                        return a;
                    }  
                }
            ],
            'columnDefs': [ {
                'targets': [0,1,2,3,4,5,6,7,8],
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

    tblservicerequest();
    setInterval(tblservicerequest, 60 * 5000); /*refresh every 10 minutes*/

});
