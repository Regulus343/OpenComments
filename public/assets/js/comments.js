/*function searchEvents() {
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

var comments;
var commentMessage;
var commentMessageTimeLimit = 6000;
var commentMessageTimeout;
var commentScroll = 0;
var commentScrollTime = 500;

var commentSlideTime = 250;

function scrollToElement(element) {
	$('html, body').animate({ scrollTop: $(element).offset().top - 7 }, commentScrollTime);
}

function loadComments() {
	$('#loading-comments').fadeIn('fast');
	$('#comments').fadeOut('fast');

	$.ajax({
		url: baseURL + 'comments/list',
		type: 'post',
		data: { 'content_id': contentID, 'content_type': contentType },
		dataType: 'json',
		success: function(result){
			showCommentMessage('#message-comments', 'info', result.message, false);

			comments = result.comments;
			if (comments != undefined && comments.length > 0) {
				$('.comments-number').text(comments.length);

				var source   = $('#comments-template').html();
				var template = Handlebars.compile(source);
				var context  = { comments: comments };
				var html     = template(context);

				$('#loading-comments').hide();
				$('#comments').html(html).removeClass('hidden').slideDown('fast');
			} else {
				$('#loading-comments').fadeOut('fast');
			}

			/* Load WYSIHTML5 */
			setupWysiwygEditors();

			/* Setup Comment Form */
			setupCommentForm();

			/* Setup Comment Actions */
			setupCommentActions();

			/* Setup Comment Edit Countdown */
			setupEditCountdown();

			/* Scroll to Comment */
			if (commentScroll > 0) {
				setTimeout("scrollToElement('#comment"+commentScroll+"');", 250);

				setTimeout("showCommentMessage('#comment"+commentScroll+"', 'success', '"+commentMessage+"', true);", 1250);
				commentScroll  = false;
				commentMessage = "";
			}
		},
		error: function(){
			showCommentMessage('#message-comments', 'info', commentMessages.noComments, true);
			$('#loading-comments').fadeOut('fast');
			console.log('Load Comments Error');
		}
	});
}

function showCommentMessage(elementID, type, message, timeLimit) {
	$(elementID+' .message.'+type).html(message).hide().removeClass('hidden').fadeIn('fast');

	if (timeLimit) {
		commentMessageTimeout = setTimeout("$('"+elementID+" .message."+type+"').html('"+message+"').fadeOut();", commentMessageTimeLimit);
	}
}

function setupWysiwygEditors() {
	$('.wysihtml5-toolbar').remove();
	$('iframe.wysihtml5-sandbox').remove();
	$('textarea.wysiwyg').val('').show();
	$('textarea.wysiwyg').each(function(){
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
}

function setupCommentForm() {
	$('.form-comment').off('submit');
	$('.form-comment').on('submit', function(e){
		e.preventDefault();

		var url         = $(this).attr('action');
		var data        = $(this).serialize();
		var containerID = "#add-comment";

		$.ajax({
			url: url,
			type: 'post',
			data: data,
			dataType: 'json',
			success: function(result) {
				if (result.resultType == "Success") {
					showCommentMessage(containerID, 'success', commentMessages.postingComment, true);

					$(containerID+' .field-comment').val('sdfsfsadfasd');

					commentScroll  = result.commentID;
					commentMessage = result.message;
					loadComments();
				} else {
					showCommentMessage(containerID, 'error', result.message, true);
				}
			},
			error: function(){
				console.log('Add Comment Failed');
			}
		});
	});
}

function setupCommentActions() {

	$('#comments .button-reply').on('click', function(e){
		e.preventDefault();

		var commentID = $(this).attr('rel');
		var label = $(this).text().trim();

		if (label == commentLabels.cancelReply && ! $(this).hasClass('reply-to-parent')) {
			$(this).text(commentLabels.reply);
			$('#reply'+commentID).slideUp(commentSlideTime);
		} else if (label == commentLabels.reply || label == commentLabels.replyToParent) {
			$('.add-reply').slideUp(commentSlideTime);

			resetReplyButtonText();

			if (label == commentLabels.reply) {
				$(this).text(commentLabels.cancelReply);
			} else {
				$('#comment'+commentID+' .button-reply').text(commentLabels.cancelReply);
			}

			setTimeout("scrollToElement('#comment"+ commentID +"');", 250);
			$('#reply'+commentID).hide().removeClass('hidden').css('min-height', 0).slideDown(commentSlideTime);
		}
	});

	$('#comments .button-edit').on('click', function(e){
		e.preventDefault();
		var commentID = $(this).attr('rel');
		var label = $(this).text().trim();
		if (label == commentLabels.cancelEdit) {
			$(this).text(commentLabels.edit);

			$('#comment'+commentID+' .edit-comment').slideUp(commentSlideTime);
		} else {
			$('#comments .button-edit').text(commentLabels.edit);
			$('#comments .edit-comment').slideUp(commentSlideTime);

			$(this).text(commentLabels.cancelEdit);

			//set edit comment text field to comment text
			var text = $('#comment'+commentID+' .comment .text').html();
			$('#comment-edit'+commentID).val(text);
			$('#comment'+commentID).find('iframe').contents().find('.wysihtml5-editor').html(text);

			$('#comment'+commentID+' .edit-comment').hide().removeClass('hidden').css('min-height', 0).slideDown(commentSlideTime);

			setTimeout("scrollToElement('#comment"+ commentID +"');", 250);
		}
	});

}

var editCommentCountdown;
function setupEditCountdown() {
	$('#comments .edit-countdown span.number').each(function(){
		setTimeout("commentCountdown('#"+$(this).parents('li').attr('id')+" .edit-countdown span')", 1000);
	});
}

function commentCountdown(element) {
	var newCount = parseInt($(element).text()) - 1;
	console.log(newCount);
	if (newCount <= 0) {
		clearInterval(editCommentCountdown);
		$(element).parents('.edit-countdown').fadeOut();
		$(element).parents('li').children('ul.actions').children('li.action-edit').fadeOut('fast');
		$(element).parents('li').children('ul.actions').children('li.action-delete').fadeOut('fast');
		$(element).parents('li').children('div.edit-comment').slideUp();
	} else {
		$(element).text(newCount);
		setTimeout("commentCountdown('"+element+"')", 1000);
	}
}

function resetReplyButtonText() {
	$('#comments .button-reply').each(function(){
		if ($(this).text().trim() == commentLabels.cancelReply) {
			$(this).text(commentLabels.reply);
		}
	});
}

$(document).ready(function(){

	/* Load Initial Comments */
	loadComments();

	/* Load Comment Actions */
	setupCommentForm();

});