/*function loadEvents() {
	if (events != undefined && events.length > 0) {
		var source   = $("#events-template").html();
		var template = Handlebars.compile(source);
		var context  = {events: events};
		var html     = template(context);

		$('#loading-events').hide();
		$('#events').html(html).slideDown('fast');
	} else {
		$('#loading-events').fadeOut('fast');
	}
}

function searchEvents() {
	var data = getFormDataForPost('#form-search');

	$('#events').hide();
	$('#loading-events').show();

	$.ajax({
		url: baseURL + 'ajax/events/search',
		type: 'post',
		data: data,
		dataType: 'json',
		success: function(data){
			$('.square-select li').removeClass('selected');
			$('.square-select li.'+$('#search-event-type').val().toLowerCase()+'-events').addClass('selected');

			if (data.messages.info != undefined) {
				var message = '<div>'+data.messages.info+'</div>';
				if (data.messages.infoSub != undefined) message += '<div class="sub">'+data.messages.infoSub+'</div>';
				$('.message.info').fadeIn('fast').html(message);
			}

			events = data.events;
			loadEvents();
		},
		error: function(result){
			Boxy.alert('Something went wrong with your attempt to search. Please try again.', null, {
				title: 'Search - Error', closeable: true, closeText: 'X'
			});

			$('#events').fadeIn('fast');

			console.log('Search Events Failed');
		}
	});
}

function selectEventType() {
	var eventType = $('#search-event-type').val();
	$('#search-event-type').val(eventType);
	searchEvents(subSection);

	$('.square-select li').removeClass('selected');
	$('.square-select li.'+eventType.toLowerCase()+'-events').addClass('selected');

	if ($('#breadcrumb-trail li:last-child a').text() == "Events") {
		$('#breadcrumb-trail').append('<li> &raquo; <a href="' + baseURL + 'events">All</a></li>');
	}
	if (eventType == "All") {
		var uri = "";
		$('#breadcrumb-trail li:last-child').fadeOut('fast');
	} else {
		var uri = '/'+eventType.toLowerCase();
		$('#breadcrumb-trail li:last-child').fadeIn('fast');
	}
	$('#breadcrumb-trail li:last-child a').attr('href', baseURL + 'events/type' + uri).text(eventType);
}

function eventAction(type, id) {
	switch(type) {
		case "activate":
			message = 'Are you sure you want to '+type+' this event? Upon activation, an event can no longer be deactivated ';
			message += 'or deleted. An activated event may only be cancelled.'; break;
		case "delete":
			message = 'Are you sure you want to '+type+' this event? You can not undo this action.'; break;
		case "cancel":
			message = 'Are you sure you want to '+type+' this event?'; break;
	}
	Boxy.ask(message, ["Yes", "No"],
		function(val) {
			if (val == "Yes") {
				$.ajax({
					url: baseURL + 'ajax/events/' + id + '/action/' + type,
					success: function(data){
						if (data == "Success") {
							var typePastTense;
							switch(type) {
								case "activate":
									typePastTense = "activated";
									$('#event'+id+' .status').html('<span class="green"><strong>Active</strong></span>');
									$('#event'+id+' .number-attending-info').fadeIn('fast');
									$('#event'+id+' .attending-info').fadeIn('fast');
									$('#event'+id+' .action-attendance-status').fadeIn('fast');
									$('#event'+id+' .action-activate').fadeOut('fast');
									$('#event'+id+' .action-delete').fadeOut('fast');
									$('#event'+id+' .action-cancel').fadeIn('fast');
									break;
								case "delete":
									typePastTense = "deleted";
									if (contentID > 0) {
										document.location.href = baseURL + 'events/deleted';
									} else {
										$('#event'+id).fadeOut('fast').remove();
									}
									break;
								case "cancel":
									typePastTense = "cancelled";
									$('#event'+id+' .status').html('<span class="red"><strong>Cancelled</strong></span>');
									$('#event'+id+' .number-attending-info').fadeOut('fast');
									$('#event'+id+' .attending-info').fadeOut('fast');
									$('#event'+id+' .action-edit').fadeOut('fast');
									$('#event'+id+' .action-cancel').fadeOut('fast');
									break;
							}
							Boxy.alert('You have successfully '+typePastTense+' this event.', null,
									   {title: ucFirst(type)+' Event', closeable: true, closeText: 'X'}
							);
						} else if (data == "Error: Past Date") {
							var message;
							switch(type) {
								case "activate":
									message = 'The event\'s date is already past. Please set a future date before activating event.'; break;
								case "delete":
									message = 'The event\'s date is already past. Please set a future date before deleting event.'; break;
								case "cancel":
									message = 'The event\'s date is already past. You may no longer cancel this event.'; break;
							}
							Boxy.alert(message, null, {title: ucFirst(type)+' Event - Error', closeable: true, closeText: 'X'});
						} else {
							Boxy.alert('Something went wrong with your attempt to '+type+' the event. Please try again.', null,
									   {title: ucFirst(type)+' Event - Error', closeable: true, closeText: 'X'}
							);
						}
					},
					error: function(data){
						Boxy.alert('Something went wrong with your attempt to '+type+' the event. Please try again.', null,
								   {title: ucFirst(type)+' Event - Error', closeable: true, closeText: 'X'}
						);
						console.log(ucFirst(type)+' Event Failed');
					}
				});
			}
		},
		{title: ucFirst(type)+' Event', closeable: true, closeText: 'X'}
	);
}

function eventAttendanceStatus(id) {
	var options = ["Attending", "Maybe Attending", "Not Attending"];
	var status = $('#event'+id+' .attending').html();
	if (status != "Unspecified") options[3] = "Remove Status";
	Boxy.ask('Please select a status for this event below.', options,
		function(val) {
			switch (val) {
				case "Attending":
					var uri = "yes";
					break;
				case "Maybe Attending":
					var uri = "maybe";
					break;
				case "Not Attending":
					var uri = "no";
					break;
				case "Remove Status":
					var uri = "remove";
					break;
			}
			$.ajax({
				url: baseURL + 'ajax/events/' + id + '/attendance/' + uri,
				dataType: 'json',
				success: function(data){
					if (data.result == "Success") {
						switch (val) {
							case "Attending":
								$('#event'+id+' .attending').addClass('green')
															.removeClass('orange')
															.removeClass('red')
															.html('<strong>Yes</strong>');
								break;
							case "Maybe Attending":
								$('#event'+id+' .attending').removeClass('green')
															.addClass('orange')
															.removeClass('red')
															.html('<strong>Maybe</strong>');
								break;
							case "Not Attending":
								$('#event'+id+' .attending').removeClass('green')
															.removeClass('orange')
															.addClass('red')
															.html('<strong>No</strong>');
								break;
							case "Remove Status":
								$('#event'+id+' .attending').removeClass('green')
															.removeClass('orange')
															.removeClass('red')
															.html('Unspecified');
								break;
						}
						$('#event'+id+' .number-attending').html('<strong>'+data.number+'</strong>')
					} else if (data.result == "Error: Attendance Required") {
						Boxy.alert('You must attend your own event. You cannot change your attendance status for events that you create.', null,
							   {title: 'Change Attendance Status for Event - Error', closeable: true, closeText: 'X'}
						);
					} else {
						Boxy.alert('Something went wrong with your attempt to change your attendance status for this event. Please try again.', null,
							   {title: 'Change Attendance Status for Event - Error', closeable: true, closeText: 'X'}
						);
					}
				},
				error: function(data){
					Boxy.alert('Something went wrong with your attempt to change your attendance status for this event. Please try again.', null,
							   {title: 'Change Attendance Status for Event - Error', closeable: true, closeText: 'X'}
					);
					console.log('Attendance Status Failed');
				}
			});
		},
		{title: 'Change Attendance Status for Event', closeable: true, closeText: 'X'}
	);
}*/

var commentMessageTimeLimit = 6000;
var commentMessageTimeout;

$(document).ready(function(){

	/* Load Initial Events */
	//loadComments();

	/* Event Actions */
	$('.form-comment').submit(function(e){
		e.preventDefault();

		var url         = $(this).attr('action');
		var data        = $(this).serialize();
		var containerID = "#add-comment";

		$.ajax({
			url: url,
			type: 'post',
			data: data,
			dataType: 'json',
			success: function(results) {
				if (results.resultType == "Success") {
					showCommentMessage(containerID, 'success', results.message);
					$(containerID+' .field-comment').val('');
				} else {
					showCommentMessage(containerID, 'error', results.message);
				}
			},
			error: function(){
				console.log('Add Comment Failed');
			}
		});
	});

	/* Load WYSIHTML5 */
	$('.wysiwyg').each(function(){
		$(this).wysihtml5({
			'stylesheets': baseURL + "assets/css/styles.css",
			'parserRules': wysihtml5ParserRules,

			'font-styles': false,
			'emphasis'   : true,
			'lists'      : true,
			'html'       : false,
			'link'       : true,
			'image'      : true
		});
	});

});

function showCommentMessage(elementID, type, message) {
	clearTimeout(commentMessageTimeout);

	$(elementID+' .message.'+type).html(message).removeClass('hidden');
console.log("$('"+elementID+" .message."+type+"').html('"+message+"').addClass('hidden');");
	commentMessageTimeout = setTimeout("$('"+elementID+" .message."+type+"').html('"+message+"').addClass('hidden');", commentMessageTimeLimit);
}