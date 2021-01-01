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
				let message;
				message = "<h1>Ajax returns error!</h1><pre class='dev'>" + url + '<hr>' + data.error_msg + "</pre>";
				if (!data.error_msg) {
					message += 'See console for response object.';
					console.log(data);
				}
				tt_error(message);
			}
		},
		error: function (jqXHR, textStatus, errorThrown) {
			let message;
			if (jqXHR.readyState === 0) {
				message = "Could not connect to the server. Please check your network connection.";
			} else if (textStatus == 'parsererror' && errorThrown == 'SyntaxError: JSON.parse: unexpected character at line 1 column 1 of the JSON data') {
				message = '<div class="dev"><h1>Invalid JSON</h1><div class="dev ajax_response">' + jqXHR.responseText + '</div></div>';
			} else {
				message = '<div class="dev"><h1>' + textStatus + '</h1>' + errorThrown + '<pre>' + url + '<br>Status code: ' + jqXHR.status + '</pre><div class="dev ajax_response">' + jqXHR.responseText + '</div></div>';
			}
			tt_error(message);
		}
	});
}

function tt_error(message) {
	let msg = $('<div>', {'class': 'message error ajax_error'}).html(message);
	$('#tt_pg_messages').append(msg);
	t2_spinner_stop();
	t2_scroll_to("#tt_pg_messages");
}
