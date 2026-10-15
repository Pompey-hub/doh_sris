$(document).ready(function() {
    'use strict';
    $('#srli_user').addClass('active');


    $('#wrapper').on('change', 'select[name="srtllpriv"]', function() {
    	if ($(this).val() === '1') {
    		$('#srempid_div').hide(100);

    		$('select[name="srempid"]').val('');
    		$('select[name="srempid"]').prop('required', false);
    	} else {
    		$('#srempid_div').show(100);
    		$('select[name="srempid"]').prop('required', true);
    	}
    });
    
});