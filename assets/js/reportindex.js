$(document).ready(function() {
    'use strict';
    $('#srli_report').addClass('active');
    

    $('#wrapper').on('click', '#myprint', function() {
        window.open('print/srflist' + location.search);
    });
});