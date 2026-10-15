$(document).ready(function() {
    'use strict';
    $('#srli_library').addClass('active');
    
    var tbl_libsubcategory = function() {
        var table = $('#tbl_libsubcategory').DataTable( {
            'responsive':true,
            'autoWidth':false,
            'processing': true,
            'serverSide': true,
            'stateSave': true,
            'destroy': true,
            'ajax': {
                url: 'dtsubcategory',
                error: function () {
                    console.log('error');
                }
            },
            'columns': [
                { 'data': 'tlcproblem' },
                { 'data': 'tlcproblem_desc' },
                { 'data': 'tlccat' },
                { 'data': 'tlsdesc' },
                { 'data': 'tlltype' },
                { 'data': 'tlscstatus' },
                { 'data': 'created_at' },
                { 'data': 'updated_at' },
                { 'data': 'tlscid',
                    "searchable": false,
                    "render": function(data,type,row,meta) {
                        var a = '<a href="javascript:void(0);" title="Edit" data-tlscid="' + row.tlscid + '" name="editsubcategory"><i class="fas fa-edit fa-n2x"></i></a>';
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
    tbl_libsubcategory();


    $('#wrapper').on('click', '#subcategory', function() {
        $('#myspinner').show();
        $.get('getcategory', {}, function(data) {
            var res = JSON.parse(data);

            $('select[name="tlcid"]').html(displayCategory(res.category));
            $('select[name="tlsid"]').html(displayService(res.service));
            $('select[name="tll_id"]').html(displayLevels(res.levels));

            $('input[name="tlscid"]').val('');
            $('select[name="tlcid"]').val('');
            $('input[name="tlcproblem"]').val('');
            $('input[name="tlcproblem_desc"]').val('');
            $('select[name="tlsid"]').val('');
            $('select[name="tll_id"]').val('');
            $('select[name="tlscstatus"]').val('ACTIVE');

            $('#modallibsubcategory').modal({
                backdrop:'static',
                keyboard: false
            });
        })
        .always(function() {
            $('#myspinner').hide();
        });
    }); 


    var displayCategory = function (category) {
        var str = '<option value="">Select...</option>';
        var len = category.length;
        for (var i = 0; i < len; i++) {
            str += '<option value="' + category[i].tlcid + '">' + category[i].tlccat + '</option>';
        }
        return str;
    }

    var displayService = function (service) {
        var str = '<option value="">Select...</option>';
        var len = service.length;
        for (var i = 0; i < len; i++) {
            str += '<option value="' + service[i].tlsid + '">' + service[i].tlsdesc + '</option>';
        }
        return str;
    }

    var displayLevels = function (levels) {
        var str = '<option value="">Select...</option>';
        var len = levels.length;
        for (var i = 0; i < len; i++) {
            str += '<option value="' + levels[i].tll_id + '">' + levels[i].tlltype + ' - ' + levels[i].tllstype + '</option>';
        }
        return str;
    }

    

    
    $('#wrapper').on('click', 'a[name="editsubcategory"]', function() {
        var tlscid = $(this).attr('data-tlscid');

        $('#myspinner').show();
        $.get('editsubcategory', {'tlscid':tlscid}, function(data) {
            var res = JSON.parse(data);

            $('select[name="tlcid"]').html(displayCategory(res.category));
            $('select[name="tlsid"]').html(displayService(res.service));
            $('select[name="tll_id"]').html(displayLevels(res.levels));
            
            $('input[name="tlscid"]').val(res.subcategory[0].tlscid);
            $('select[name="tlcid"]').val(res.subcategory[0].tlcid);
            $('input[name="tlcproblem"]').val(res.subcategory[0].tlcproblem);
            $('input[name="tlcproblem_desc"]').val(res.subcategory[0].tlcproblem_desc);
            $('select[name="tlsid"]').val(res.subcategory[0].tlsid);
            $('select[name="tll_id"]').val(res.subcategory[0].tll_id);
            $('select[name="tlscstatus"]').val(res.subcategory[0].tlscstatus);

            $('#modallibsubcategory').modal({
                backdrop:'static',
                keyboard: false
            });
        })
        .always(function() {
            $('#myspinner').hide();
        });
    }); 

    $('#modallibsubcategory').on('click', '#srbtnsavechanges', function() {
        var tlcid = $('select[name="tlcid"]').val();
        var tlcproblem = $('input[name="tlcproblem"]').val();
        var tlsid = $('select[name="tlsid"]').val();
        var tll_id = $('select[name="tll_id"]').val();
        var tlscstatus = $('select[name="tlscstatus"]').val();

        if ($.trim(tlcid).length === 0 || $.trim(tlcproblem).length === 0 || 
            $.trim(tlsid).length === 0 || 
            $.trim(tll_id).length === 0 || $.trim(tlscstatus).length === 0) {
            Swal.fire({
                title: "Warning!",
                text: "Please complete all required fields.",
                icon: "warning"
            });
        } else {
            $('#myspinner').show();
            $('#formlibsubcategory').submit();
        }
    });
});