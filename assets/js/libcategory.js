$(document).ready(function() {
    'use strict';
    $('#srli_library').addClass('active');
    
    var servicerequest = function() {
        var table = $('#tbl_libcategory').DataTable( {
            'responsive':true,
            'autoWidth':false,
            'processing': true,
            'serverSide': true,
            'stateSave': true,
            'destroy': true,
            'ajax': {
                url: 'dtcategory',
                error: function () {
                    console.log('error');
                }
            },
            'columns': [
                { 'data': 'tlccat' },
                { 'data': 'tlcstatus' },
                { 'data': 'created_at' },
                { 'data': 'updated_at' },
                { 'data': 'tlcid',
                    "searchable": false,
                    "render": function(data,type,row,meta) {
                        var a = '<a href="javascript:void(0);" title="Edit" data-tlcid="' + row.tlcid + '" name="editcategory"><i class="fas fa-edit fa-n2x"></i></a>';
                        return a;
                    }  
                }
            ],
            'order': [[0, 'asc']]
        });


        $('.dataTables_filter input').unbind().keyup(function(e) {
            if(e.keyCode == 13) {
                var value = $(this).val();
                table.search(value).draw();
            }
        });
    };
    servicerequest();


    $('#wrapper').on('click', '#category', function() {
        $('input[name="tlcid"]').val('');
        $('input[name="tlccat"]').val('');
        $('select[name="tlcstatus"]').val('ACTIVE');

        $('#modallibcategory').modal({
            backdrop:'static',
            keyboard: false
        });
    }); 

    $('#wrapper').on('click', 'a[name="editcategory"]', function() {
        var tlcid = $(this).attr('data-tlcid');

        $('#myspinner').show();
        $.get('editcategory', {'tlcid':tlcid}, function(data) {
            var res = JSON.parse(data);

            $('input[name="tlcid"]').val(res.category[0].tlcid);
            $('input[name="tlccat"]').val(res.category[0].tlccat);
            $('select[name="tlcstatus"]').val(res.category[0].tlcstatus);

            $('#modallibcategory').modal({
                backdrop:'static',
                keyboard: false
            });
        })
        .always(function() {
            $('#myspinner').hide();
        });
    }); 

    $('#modallibcategory').on('click', '#srbtnsavechanges', function() {
        var tlccat = $('input[name="tlccat"]').val();

        if ($.trim(tlccat).length === 0) {
            Swal.fire({
                title: "Warning!",
                text: "Please complete all required fields.",
                icon: "warning"
            });
        } else {
            $('#formlibcategory').submit();
        }
    });
});