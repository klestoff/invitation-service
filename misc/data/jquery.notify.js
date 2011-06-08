/**
* Notify plugin 1.0
*
* Copyright (c) 2010 Landgraf Paul (http://landgraf-paul.blogspot.com/)
* Licensed under the MIT
*/
jQuery.notify = {};

jQuery.notify.add = function (message, css, timeOut) {
    if (!timeOut) {
        timeOut = css;
        css = undefined;
    }

    if ($('#notifyBox').length == 0)
        $(document.body).append(
			'<table width="100%" id="notifyBox" style="position: fixed; '
			+ 'top: 0px;"><tr><td align="center"><div id="boxs"></div>'
			+ '</td></tr></table>'
		);

    var mesgId = 'messageBox' + (new Date).getTime();

    $('#boxs').append('<div id="' + mesgId + '" class="messageBox"></div>');

    if (css)
        $('#' + mesgId).addClass(css);
    else
        $('#' + mesgId).attr('className', 'messageBox');

    $('#' + mesgId).text(message).fadeIn();

    setTimeout(
		function() {
			$('#' + mesgId).fadeOut(
				"normal",
				function() {$(this).remove();}
			);
		},
		timeOut * 1000
	);
		
    return $('#' + mesgId);
}

jQuery.notify.remove = function (obj, timeOut) {
    setTimeout(function () {
        obj.fadeOut("normal", function () {
            $(this).remove();
        });
    }, timeOut * 1000);
}