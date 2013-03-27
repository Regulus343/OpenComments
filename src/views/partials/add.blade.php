<?php
$contentType  = Session::get('contentType');

$commentID    = Session::get('commentID');
$commentText  = Session::get('commentText');
$parentID     = Session::get('commentParentID');
$commentReply = Session::get('commentReply');

$class = "";
if ($commentReply) {
	$divID = "reply-comment".$parentID;
	$class = "reply-comment";
	$label = "Add Reply";
} else {
	if ($commentID) {
		$divID = "edit-comment".$commentID;
		$class = "edit-comment";
		$label = "Edit Comment";
	} else {
		$divID = "add-comment";
		$label = "Add Comment";
	}
}

$hideCommentArea = false;
if ($commentID && $class != "reply-comment") $hideCommentArea = true; ?>

@if (!Auth::guest())

	<div class="clear"></div>
	<div class="add-comment{{ HTML::dynamicArea($class != "", $class) }} {{ HTML::hiddenArea($hideCommentArea) }}" id="{{ $divID }}">

		{{-- Success Message --}}
		<div class="message success hidden">
			<div class="main"></div>
			<div class="sub"></div>
		</div>

		{{-- Error Message --}}
		<div class="message error hidden">
			<div class="main"></div>
			<div class="sub"></div>
		</div>

		{{-- General Info Message --}}
		<div class="message info hidden">
			<div class="main"></div>
			<div class="sub"></div>
		</div>

		{{-- Add/Edit Comment Form --}}
		{{ Form::open('comments/create', 'post', array('class' => 'form-comment')) }}
			<label for="comment{{ $commentID }}">{{ $label }}:</label>
			<textarea name="comment" class="field-comment" id="comment{{ $commentID }}" class="wysiwyg" placeholder="Add a comment...">{{ $commentText }}</textarea>

			<input type="hidden" name="content_type" class="content-type" value="{{ $contentType }}" />
			<input type="hidden" name="content_id" class="content-id" value="{{ $id }}" />
			<input type="hidden" name="comment_id" class="comment-id" value="{{ $commentID }}" />
			<input type="hidden" name="parent_id" class="parent-id" value="{{ $parentID }}" />

			<div>
				<input type="submit" name="add_comment" class="left" value="{{ $label }}" />
				<div class="clear"></div>
			</div>
		{{ Form::close() }}
	</div><!--/add-comment-->
@else
	@if (!$parentID)
		<div class="add-comment">
			<p class="login"><a href="{{{ URL::to('login') }}}">Log in</a> to add a comment.</p>
		</div><!--/add-comment-->
	@endif
@endif