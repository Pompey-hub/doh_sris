$(document).ready(function() {
    const url = new URL(window.location.href);
    if (url.searchParams.has('csrf_redirect')) {
        toastr.error('Your session expired or the form is invalid. Please try again.');
        url.searchParams.delete('csrf_redirect');
        window.history.replaceState({}, document.title, url.href);
    }
});

String.prototype.escapeHTML = function() {
        return this.replace(/&/g, "&amp;")
                   .replace(/</g, "&lt;")
                   .replace(/>/g, "&gt;")
                   .replace(/"/g, "&quot;")
                   .replace(/'/g, "&#039;");
}
/*str.escapeHTML()*/


function validMACAddress(data) {
        var regex = /^([0-9A-F]{2}[:-]){5}([0-9A-F]{2})$/;
        return regex.test(data);
}
