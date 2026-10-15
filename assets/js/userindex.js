$(document).ready(function() {
    'use strict';
    $('#srli_user').addClass('active');

    $('#tbl_users').DataTable( {
        'responsive':true,
        'autoWidth':false,
        'processing': true,
        'serverSide': true,
        'stateSave': true,
        'ajax': {
            url: 'users/dtuser',
            error: function () {
                console.log('error');
            }
        },
        'columns': [
            { 'data': 'fullname' },
            { 'data': 'tlluname' },
            { 'data': 'tllfactype' },
            { 'data': 'office' },
            { 'data': 'tllpriv' },
            { 'data': 'tllstatus'},
            { 'data': 'tlldateadded' },
            { 'data': 'tllid',
                "searchable": false,
                "render": function(data,type,row,meta) {
                    var a = '';
                    a += '<a href="users/credentials?q='+row.tllid+'" title="Edit user" ><i class="fas fa-edit fa-n2x"></i></a>';
                    /*a += '<a href="credentials?q='+row.tllid+'" title="Change password" ><i class="fas fa-unlock"></i></a>';*/
                    return a;
                } 
            }
        ],
        'columnDefs': [ {
            'targets': [7],
            'orderable': false,
        }],
        'order': [[0, 'asc']]
    });
});