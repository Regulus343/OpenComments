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
	$('#loading-comments').css('height', $('#comments').height()).fadeIn('fast');
	$('#comments').hide();

	clearTimeout(editCommentCountdown);

	var page = $('#comments-page').val();

	$.ajax({
		url: baseURL + 'comments/list',
		type: 'post',
		data: { 'content_id': contentID, 'content_type': contentType, 'page': page },
		dataType: 'json',
		success: function(result){
			showCommentMessage('#message-comments', 'info', result.message, false);

			if (result.totalPages > 0) {

				/* Create and Set Up Pagination */
				var commentsPagination = buildCommentsPagination(result.totalPages, result.currentPage);
				$('ul.comments-pagination').html(commentsPagination).removeClass('hidden');
				setupCommentsPagination();
			} else {
				$('ul.comments-pagination').fadeOut();
			}

			comments = result.comments;
			if (comments != undefined && comments.length > 0) {
				$('.comments-number').text(result.totalComments);

				var source   = $('#comments-template').html();
				var template = Handlebars.compile(source);
				var context  = { comments: comments };
				var html     = template(context);

				hideCommentMessage('#add-comment', 'success');

				$('#comments').html(html).removeClass('hidden').show();
				$('#loading-comments').hide();
			} else {
				$('#loading-comments').fadeOut('fast');
			}

			/* Load WYSIHTML5 */
			setupWysiwygEditors();

			/* Set Up Comment Form */
			setupCommentForm();

			/* Set Up Comment Actions */
			setupCommentActions();

			/* Set Up Comment Edit Countdown */
			setupEditCountdown();

			/* Scroll to Comment */
			if (commentScroll > 0) {
				setTimeout("scrollToElement('#comment"+commentScroll+"');", 250);

				setTimeout("showCommentMessage('#comment"+commentScroll+" .top-messages', 'success', '"+commentMessage+"', true);", 1000);
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

function buildCommentsPagination(totalPages, currentPage) {
	var html = "";
	if (totalPages == 1) return html;
	if (currentPage == null || currentPage == "") currentPage = 1;
	if (totalPages > 5) {
		var startPage = currentPage - 4;
		if (startPage > 1) {
			var halfwayPage = 1 + Math.floor(startPage / 2);
			html += '<li><a href="" rel="1">1</a></li>';
			if (halfwayPage > 2) {
				html += '<li><a href="" rel="'+halfwayPage+'">...</a></li>';
			}
		} else {
			startPage = 1;
		}

		var endPage   = currentPage + 4;
		if (endPage > totalPages) endPage = totalPages;

		for (p = startPage; p <= endPage; p++) {
			if (p == currentPage) {
				html += '<li class="selected">';
			} else {
				html += '<li>';
			}
			html += '<a href="" rel="'+p+'">'+p+'</a></li>';
		}
		if (endPage < totalPages) {
			var halfwayPage = endPage + Math.round((totalPages - endPage) / 2);
			if (halfwayPage < totalPages) {
				html += '<li><a href="" rel="'+halfwayPage+'">...</a></li>';
			}
			html += '<li><a href="" rel="'+totalPages+'">'+totalPages+'</a></li>';
		}
	} else {
		for (p=1; p <= totalPages; p++) {
			if (p == currentPage) {
				html += '<li class="selected">';
			} else {
				html += '<li>';
			}
			html += '<a href="" rel="'+p+'">'+p+'</a></li>';
		}
	}
	return html;
}

function setupCommentsPagination() {
	$('ul.comments-pagination li a').each(function(){
		$(this).on('click', function(e){
			e.preventDefault();
			$('ul.comments-pagination li').removeClass('selected');
			$(this).parents('li').addClass('selected');
			$('#comments-page').val($(this).attr('rel'));

			if ($(this).parents('ul').attr('id') == "comments-pagination-top") {
				setTimeout("scrollToElement('#"+$(this).parents('ul').attr('id')+"');", 250);
			}

			loadComments();
		});
	});
}

function showCommentMessage(elementID, type, message, timeLimit) {
	$(elementID+' .message.'+type).html(message).hide().removeClass('hidden').fadeIn('fast');

	if (timeLimit) {
		commentMessageTimeout = setTimeout("$('"+elementID+" .message."+type+"').html('"+message+"').fadeOut();", commentMessageTimeLimit);
	}
}

function hideCommentMessage(elementID, type, message, timeLimit) {
	$(elementID+' .message.'+type).html(message).hide().fadeOut();
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
		if ($(this).parents('li').hasClass('add-reply')) {
			var containerID = "#"+$(this).parents('li').attr('id');
		} else if ($(this).parents('div').hasClass('edit-comment')) {
			var containerID = "#"+$(this).parents('div').attr('id');
		} else {
			var containerID = "#add-comment";
		}

		$.ajax({
			url: url,
			type: 'post',
			data: data,
			dataType: 'json',
			success: function(result) {
				if (result.resultType == "Success") {
					showCommentMessage(containerID, 'success', commentMessages.postingComment, true);

					commentScroll  = result.commentID;
					commentMessage = result.message;
					loadComments();
				} else {
					showCommentMessage(containerID, 'error', result.message, true);
				}
			},
			error: function(){
				console.log('Add/Edit Comment Failed');
			}
		});
	});
}

function setupCommentActions() {

	$('#comments .button-reply').on('click', function(e){
		e.preventDefault();

		var commentID = $(this).attr('rel');
		var label     = $(this).text().trim();

		if (label == commentLabels.cancelReply && ! $(this).hasClass('reply-to-parent')) {
			$(this).children('span').text(commentLabels.reply);
			$('#reply'+commentID).slideUp(commentSlideTime);
		} else if (label == commentLabels.reply || label == commentLabels.replyToParent) {
			$('.add-reply').slideUp(commentSlideTime);

			resetReplyButtonText();

			if (label == commentLabels.reply) {
				$(this).children('span').text(commentLabels.cancelReply);
			} else {
				$('#comment'+commentID+' .button-reply').text(commentLabels.cancelReply);
			}

			setTimeout("scrollToElement('#comment"+ commentID +"');", 250);
			$('#reply'+commentID).hide().removeClass('hidden').css('min-height', 0).slideDown(commentSlideTime);
			$('#reply'+commentID).find('iframe').contents().find('.wysihtml5-editor').focus();
		}
	});

	$('#comments .button-edit').on('click', function(e){
		e.preventDefault();

		var commentID = $(this).attr('rel');
		var label     = $(this).text().trim();

		if (label == commentLabels.cancelEdit) {
			$(this).children('span').text(commentLabels.edit);

			$('#comment'+commentID+' .edit-comment').slideUp(commentSlideTime);
		} else {
			$('#comments .button-edit span').text(commentLabels.edit);
			$('#comments .edit-comment').slideUp(commentSlideTime);

			$(this).children('span').text(commentLabels.cancelEdit);

			//set edit comment text field to comment text
			var text = $('#comment'+commentID+' .comment .text').html();
			$('#comment-edit'+commentID).val(text);
			$('#comment'+commentID).find('iframe').contents().find('.wysihtml5-editor').html(text);

			$('#comment'+commentID+' .edit-comment').hide().removeClass('hidden').css('min-height', 0).slideDown(commentSlideTime);

			setTimeout("scrollToElement('#comment"+ commentID +"');", 250);
		}
	});

	$('#comments .button-delete').on('click', function(e){
		e.preventDefault();
		var commentID = $(this).attr('rel');

		Boxy.confirm(commentMessages.confirmDelete, function(){
			$.ajax({
				url: baseURL + 'comments/delete/' + commentID,
				dataType: 'json',
				success: function(result){
					if (result.resultType == "Success") {
						showCommentMessage('#comment'+commentID+' .top-messages', 'success', result.message, true);
						setTimeout("$('#comment"+commentID+"').slideUp("+commentSlideTime+");", 1500);
						setTimeout("$('#comment"+commentID+"').remove();", 3000);
						$('#comments li').each(function(){
							if ($(this).attr('data-parent-id') == commentID) {
								$('#comment'+$(this).attr('data-parent-id')).remove();
							}
						});
					} else {
						showCommentMessage('#comment'+commentID+' .top-messages', 'error', result.message, true);
					}
				},
				error: function(result){
					showCommentMessage('#comment'+commentID+' .top-messages', 'error', result.message, true);
					console.log('Delete Comment Failed');
				}
			});
		},
		{title: 'Delete Comment', closeable: true, closeText: 'X'});
	});

	$('#comments .button-approve').on('click', function(e){
		e.preventDefault();

		var commentID = $(this).attr('rel');
		var label     = $(this).text().trim();

		if (label == commentLabels.approve) {
			var title   = commentMessages.confirmApproveTitle;
			var message = commentMessages.confirmApprove;
		} else {
			var title   = commentMessages.confirmUnapproveTitle;
			var message = commentMessages.confirmUnapprove;
		}

		Boxy.confirm(message, function(){
			$.ajax({
				url: baseURL + 'comments/approve/' + commentID,
				dataType: 'json',
				success: function(result){
					if (result.resultType == "Success") {
						if (result.approved) {
							$('#comment'+commentID).removeClass('unapproved');
							$('#comment'+commentID+' .button-approve .icon')
								.removeClass('icon-plus-sign')
								.addClass('icon-minus-sign');
							$('#comment'+commentID+' .button-approve').text(commentLabels.unapprove);
						} else {
							$('#comment'+commentID).addClass('unapproved');
							$('#comment'+commentID+' .button-approve .icon')
								.removeClass('icon-minus-sign')
								.addClass('icon-plus-sign');
							$('#comment'+commentID+' .button-approve').text(commentLabels.approve);
						}
						showCommentMessage('#comment'+commentID+' .top-messages', 'success', result.message, true);
					} else {
						showCommentMessage('#comment'+commentID+' .top-messages', 'error', result.message, true);
					}
				},
				error: function(result){
					showCommentMessage('#comment'+commentID+' .top-messages', 'error', result.message, true);
					console.log('Approve Comment Failed');
				}
			});
		},
		{title: title, closeable: true, closeText: 'X'});
	});

}

var editCommentCountdown;
function setupEditCountdown() {
	$('#comments .edit-countdown span.number').each(function(){
		editCommentCountdown = setTimeout("commentCountdown('#"+$(this).parents('li').attr('id')+" .edit-countdown span')", 1000);
	});
}

function commentCountdown(element) {
	var newCount = parseInt($(element).text()) - 1;
	if (newCount <= 0) {
		clearTimeout(editCommentCountdown);
		$(element).parents('.edit-countdown').fadeOut();
		$(element).parents('li').removeClass('editable');
		$(element).parents('li').children('ul.actions').children('li.action-edit').fadeOut('fast');
		$(element).parents('li').children('ul.actions').children('li.action-delete').fadeOut('fast');
		$(element).parents('li').children('div.edit-comment').slideUp();
	} else {
		if (newCount > 1) {
			$(element).text(newCount);
		} else {
			var singularText = $(element).parents('.edit-countdown').html().replace('seconds', 'second').replace((newCount + 1), newCount);
			console.log(singularText);
			$(element).parents('.edit-countdown').html(singularText);
		}
		editCommentCountdown = setTimeout("commentCountdown('"+element+"')", 1000);
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