function setCookie(cname, cvalue, exdays) {
	exdays = exdays || 1000;
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

$(document).ready(function() {
    if (getCookie('summary') == 'true') {
        $('#summaryButton').click();
    }
    if (getCookie('entry') == 'true') {
        $('#entryButton').click();
    }

    $('.header-menu').click(function(e) {
    	var cookieName = e.currentTarget.dataset.cookie;
    	if ($(e.currentTarget).find('span').hasClass('collapsed')) {
    		setCookie(cookieName, true);
    	} else {
    		setCookie(cookieName, false);
    	}
    });
});