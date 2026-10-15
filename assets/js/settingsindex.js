$(document).ready(function() {
    'use strict';
    $('#srli_settings').addClass('active');
    
    /* chnage password*/
    $('#wrapper').on('click', '#srchangepassword', function() {
    	$('#srbtnsavechangespassword').prop('disabled', true);
    	$('#srmodalchangepassword').modal({
	        backdrop:'static',
	        keyboard: false
	    });
    });
    
    $('#srmodalchangepassword').on('keyup', '#newpassword, #confirmpassword', function() {
    	console.log('okok');
        var newpassword = $.trim($('#newpassword').val());
        var confirmpassword = $.trim($('#confirmpassword').val());

        if (newpassword.length < 6 || confirmpassword.length < 6 ) {
            $('#srbtnsavechangespassword').prop('disabled', true);
        } else {
            $('#srbtnsavechangespassword').prop('disabled', true);
            if (newpassword === confirmpassword) {
                $('#srbtnsavechangespassword').prop('disabled', false);    
            }
        }
    });

    $('#srmodalchangepassword').on('click', '#srbtnsavechangespassword', function() {
    	$('#srformreceived').submit();
    });



    /* change contact details*/
    $('#wrapper').on('click', '#srchnagecontact', function() {
    	$('#sremail').val($('#sremail_display').val());
    	$('#srnumber').val($('#srcontact_display').val());

    	var sremail = $.trim($('#sremail').val());
        var srnumber = $.trim($('#srnumber').val());
        if (sremail.length < 6 || srnumber.length < 6 ) {
            $('#srbtnsavechangescontact').prop('disabled', true);
        } else {
            $('#srbtnsavechangescontact').prop('disabled', false); 
        }
    	$('#srmodalchangecontacts').modal({
	        backdrop:'static',
	        keyboard: false
	    });
    });
    
    $('#srmodalchangecontacts').on('keyup', '#sremail, #srnumber', function() {
        var sremail = $.trim($('#sremail').val());
        var srnumber = $.trim($('#srnumber').val());

        if (sremail.length < 6 || srnumber.length < 6 ) {
            $('#srbtnsavechangescontact').prop('disabled', true);
        } else {
            $('#srbtnsavechangescontact').prop('disabled', false); 
        }
    });

    $('#srmodalchangecontacts').on('click', '#srbtnsavechangescontact', function() {
    	$('#srformcontact').submit();
    });

});
