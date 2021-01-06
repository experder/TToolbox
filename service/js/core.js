/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

function tt_ajax_post(url, data_object, Funktion) {
	$.ajax({
		type: 'POST',
		url: url,
		data: data_object,
		dataType: 'json',
		success: function (data) {
			if (data.ok) {
				Funktion(data);
			} else {
				let backtrace = '';
				if (data.backtrace && Array.isArray(data.backtrace)) {
					backtrace = "<hr>" + data.backtrace.join('\n');
				}
				let msg = '';
				if (data.error_msg) {
					msg = htmlEntities(data.error_msg);
				} else {
					msg = 'Response doesn\'t have \'error_msg\' value. See console for response object.';
					console.log(data);
				}
				let message = "<h1>Ajax returns error!</h1><pre class='dev'>" + url + '</pre><div class=\'ajax_response\'>' + msg + backtrace + "</div>";
				tt_error(message);
			}
		},
		error: function (jqXHR, textStatus, errorThrown) {
			let message;
			if (jqXHR.readyState === 0) {
				message = "Could not connect to the server. Please check your network connection.";
			} else if (String(textStatus) === 'parsererror' && String(errorThrown) === 'SyntaxError: JSON.parse: unexpected character at line 1 column 1 of the JSON data') {
				message = '<div class="dev"><h1>Invalid JSON</h1><div class="dev ajax_response">' + htmlEntities(jqXHR.responseText) + '</div></div>';
			} else {
				message = '<div class="dev"><h1>' + textStatus + '</h1>' + errorThrown + '<pre>' + url + '<br>Status code: ' + jqXHR.status + '</pre><div class="dev ajax_response">' + jqXHR.responseText + '</div></div>';
			}
			tt_error(message);
		}
	});
}

function htmlEntities(str) {
	return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;');
}

function tt_error(message, classname = 'error ajax_error') {
	let msg = $('<div>', {'class': 'message ' + classname}).html(message);
	let msgDiv = $('#tt_pg_messages');
	msgDiv.append(msg);
	t2_spinner_stop();
	tt_scroll_to(msgDiv.children().last());
}

function tt_scroll_to(jQo, millis = 400) {
	$('html, body').animate({
		scrollTop: jQo.offset().top
	}, millis);
}

/*
       WAIT SPINNER
 */
function t2_spinner_start() {
	let uS = document.getElementById('uploadSpinner');
	if (uS) uS.style.display = "block";
	scope_disableKeys = true;
}

function t2_spinner_stop() {
	let uS = document.getElementById('uploadSpinner');
	if (uS) uS.style.display = "none";
	scope_disableKeys = false;
}

let scope_disableKeys = false;
window.addEventListener('keydown', function (event) {
	if (scope_disableKeys === true) {
		event.preventDefault();
		return false;
	}
});

