/*
 * This file is part of the TT toolbox;
 * Copyright (C) 2014-2021 Fabian Perder (t2@qnote.de) and contributors
 * TT comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under
 * certain conditions. See the GNU General Public License (file 'LICENSE' in the root directory) for more details.
 */

tt_tools = {};

tt_tools.globalCounter = 0;
tt_tools.disableKeys = false;

tt_tools.ajaxPost = function(url, data_object, Funktion) {
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
					msg = tt_tools.htmlEntities(data.error_msg);
				} else {
					msg = 'Response doesn\'t have \'error_msg\' value. See console for response object.';
					console.log(data);
				}
				let message = "<h1>Ajax returns error!</h1><pre class='dev'>" + url + '</pre><div class=\'ajax_response\'>' + msg + backtrace + "</div>";
				tt_tools.error(message);
			}
			if(data.tt_stats){
				tt_tools.addStats(data.tt_stats);
			}
		},
		error: function (jqXHR, textStatus, errorThrown) {
			let message;
			if (jqXHR.readyState === 0) {
				message = "Could not connect to the server. Please check your network connection.";
			} else if (
				String(textStatus) === 'parsererror'
				&& ((String(errorThrown) === 'SyntaxError: JSON.parse: unexpected character at line 1 column 1 of the JSON data')
					||(String(errorThrown).substr(0,76)==='SyntaxError: JSON.parse: unexpected non-whitespace character after JSON data')
				)
			) {
				let id = 'id_' + tt_tools.nextGlobalId();
				message = '<div class="dev"><h1>Invalid JSON</h1>'
					+ '<input type="button" value="raw" onclick="$(\'#' + id + '\').toggle(400);" />'
					+ '<div class="dev ajax_response raw" id="' + id + '" style="display: none;">' + tt_tools.htmlEntities(jqXHR.responseText) + '</div>'
					+ '<div class="dev ajax_response">' + tt_tools.htmltrim(jqXHR.responseText) + '</div>'
					+ '</div>';
			} else if (String(textStatus) === 'parsererror' && String(errorThrown) === 'SyntaxError: JSON.parse: unexpected end of data at line 1 column 1 of the JSON data') {
				message = '<div class="dev"><h1>Empty response</h1>See console for request object.</div>';
				console.log('url: ' + url);
				console.log('data:');
				console.log(data_object);
			} else {
				message = '<div class="dev"><h1>' + textStatus + '</h1>' + errorThrown + '<pre>' + url + '<br>Status code: ' + jqXHR.status + '</pre><div class="dev ajax_response">' + jqXHR.responseText + '</div></div>';
			}
			tt_tools.error(message);
		}
	});
};

tt_tools.nextGlobalId = function(){
	return ++this.globalCounter;
};

tt_tools.htmlEntities = function(str) {
	return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;');
};

tt_tools.error = function(message, classname = 'error ajax_error') {
	let msg = $('<div>', {'class': 'message ' + classname}).html(message);
	let msgDiv = $('#tt_pg_messages');
	msgDiv.append(msg);
	this.spinnerStop();
	this.scrollTo(msgDiv.children().last());
};

tt_tools.addStats = function(stats) {
	let target = $('div.tt_stats');
	stats.forEach(function(stat){
		let statHtml = tt_tools.statToHtml(stat.title, stat.class, stat.content);
		let statObj = $.parseHTML(statHtml);
		target.append(statObj);
	});
};

tt_tools.statToHtml = function($title, $class, $content) {
	let id = 'ida'+tt_tools.nextGlobalId();
	if($content){
		let btn = "<div class='statsBtn expand ajax_stat' onclick=\"$('#"+id+"').toggle(400);\">"+$title+"</div>";
		let classes = "statsContent"+($class?" "+$class:"");
		$content = "<div class='"+classes+"'>"+$content+"</div>";
		$content = "<div class='contentWrapper' id='"+id+"'>"+$content+"</div>";
		return btn+$content;
	}
	return "<div class='statsBtn ajax_stat'>"+$title+"</div>";
};

tt_tools.htmltrim = function(string) {
	string = string.replace(new RegExp("<br ?/?>"), "\n");
	string = string.trimLeft();
	return string;
};

tt_tools.scrollTo = function(jQo, millis = 400) {
	$('html, body').animate({
		scrollTop: jQo.offset().top
	}, millis);
};

/*
       WAIT SPINNER
 */
tt_tools.spinnerStart = function() {
	let uS = document.getElementById('uploadSpinner');
	if (uS) uS.style.display = "block";
	this.disableKeys = true;
};

tt_tools.spinnerStop = function() {
	let uS = document.getElementById('uploadSpinner');
	if (uS) uS.style.display = "none";
	this.disableKeys = false;
};


$(function(){
	window.addEventListener('keydown', function (event) {
		if (tt_tools.disableKeys === true) {
			event.preventDefault();
			return false;
		}
	});
});

