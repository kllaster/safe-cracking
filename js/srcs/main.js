window.result = null;
$(function () {
	setInterval(function () {
		$('.objects_try__item_lock').each(function() {
			let time = parseInt($(this).children('span').text().slice(0, -2));
			if (time > 0)
				$(this).html("The safe is blocked: <span>"+(time-1)+" s.</span>");
			else
			{
				$(this).parent().append("<div class=\"objects_try__item_count\">Attempts: 0 / 10</div>");
				$(this).remove();
			}
		});
	}, 1000);
	$(document).on("input", ".objects_try__item_input", function(){
		this.value = this.value.replace(/[^0-9]/g, '');
		if (this.value.length > 4)
			this.value = this.value.substr(0, 4);
	});
	$(document).on("click", ".btn-try, .btn-try-auto", function() {
		if (!!window.result)
		{
			alert('The PIN came up: '+window.result);
			return ;
		}
		let auto = false;
		if ($(this).is('.btn-try-auto'))
			auto = true;
		let item = $(this).parent();
		let object = item.attr('id').substr(17);
		let pin = item.children('input').val();
		let safe_box = $('#safe_box_select').val();
		if (auto === false && (pin === undefined || pin.length < 4))
		{
			alert("PIN: 4 цифры [0-9]");
			return ;
		}
		if (auto === true)
		{
			if (item.data('auto') === true)
				return ;
			item.data('auto', true);
			let h_interval = setInterval(function () {
				ajax_attempt(object, pin, safe_box, auto, item, h_interval);
			}, 1000);
		}
		else
			ajax_attempt(object, pin, safe_box, auto, item);
	});

	function ajax_attempt(object, pin, safe_box, auto, item, h_interval = null) {
		$.ajax({
			type: "GET",
			dataType: "text",
			url: "/app/ajax.php",
			data: {
				"action" : "attempt",
				"object" : object,
				"pin" : pin,
				"safe_box" : safe_box,
				"auto" : auto
			},
			success: function(response) {
					try{
						response = JSON.parse(response);
					} catch(e) {
						return ;
					}
					if (!!response.lock)
					{
						if (!!h_interval)
							clearInterval(h_interval);
						item.data('auto', false);
						item.children('.objects_try__item_row')
							.children('.objects_try__item_count').css('display', 'none');
						let lock_div = $("#lock_"+object);
						if (lock_div.length > 0)
							lock_div.html("The safe is blocked: <span>"+response.lock+" s.</span>");
						else
							item.children('.objects_try__item_row')
								.append("<div class=\"objects_try__item_lock\" id=\"lock_"+object+"\">The safe is blocked: <span>"+response.lock+" s.</span></div>");
					}
					if (!!response.pin)
					{
						if (response.result)
						{
							window.result = response.pin;
							$('h1').after("<div class=\"message\">The PIN came up: "+response.pin+"</div>")
						}
						$('.log_attempt').prepend("<div class=\"log_attempt__item\">\n" +
							"<div>Object #"+object+"</div>\n" +
							"<div>PIN: "+response.pin+"</div>\n" +
							"<div>"+(response.result ? 'Success, the PIN came up!' : 'PIN didn\'t fit')+"</div>\n" +
							"</div>");
						item.children('.objects_try__item_row')
							.children('.objects_try__item_count').html("Attempts: "+response.attempt+" / 10");
					}
					if (!!window.result && !!h_interval)
					{
						item.data('auto', false);
						clearInterval(h_interval);
					}
				}
		});
	}
	$(document).on("click", ".btn-add-obj", function(){
		$.ajax({
			type: "GET",
			dataType: "text",
			url: "/app/ajax.php",
			data: {
				"action" : "add_object"
			},
			success: function(response) {
				try{
					response = JSON.parse(response);
				} catch(e) {
					return ;
				}
				let html = "<div class=\"objects_try__item\" id=\"objects_try__item"+response.key+"\">\n" +
							"<div class=\"objects_try__item_row\">\n" +
								"<div class=\"objects_try__item_name\">Object #"+response.key+"</div>\n" +
								"<div class=\"objects_try__item_count\">Attempts: 0 / 10</div>" +
							"</div>\n" +
							"<input class=\"objects_try__item_input\" id=\"objects_try__item"+response.key+"_input\" type=\"number\" max=\"9999\" required=\"\"" +
							"><button class=\"btn-try\">Check</button" +
							"><button class=\"btn-try btn-try-auto\">Auto</button>\n" +
						"</div>"
				$('.objects_try').append(html);
			}
		});
	});
});