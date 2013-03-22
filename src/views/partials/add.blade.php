<?php
$contentType  = Session::get('contentType');

$commentID    = Session::get('commentID');
$commentText  = Session::get('commentText');
$parentID     = Session::get('commentParentID');
$commentReply = Session::get('commentReply');

$class = "";
if ($commentReply) {
	$divId = "reply-comment".$parentID;
	$class = "reply-comment";
	$label = "Add Reply";
} else {
	if ($commentID) {
		$divId = "edit-comment".$commentID;
		$class = "edit-comment";
		$label = "Edit Comment";
	} else {
		$divId = "add-comment";
		$label = "Add Comment";
	}
}

$hideCommentArea = false;
if ($commentID && $class != "reply-comment") $hideCommentArea = true; ?>

@if (!Auth::guest())

	<?php /*if (Session::get('commentIdActioned') == $commentId
	&& (Session::get('replyIdActioned') == $parentId OR Session::get('messageSuccess') != "")) {


		<?php if (Session::get('messageSuccess') != "") { ?>
			<div class="clear" id="comment" style="margin-bottom: 18px;"></div>
			<div class="message success">
				<div><?=$this->session->flashdata('success')?></div>
			</div>
		<?php }

		//error message
		if ($this->session->flashdata('error') != "") {
			$hideCommentArea = false; ?>
			<div class="clear" id="comment" style="margin-bottom: 18px;"></div>
			<div class="message error">
				<div><?=$this->session->flashdata('error')?></div>
			</div>
		<?php }
	}*/ ?>
	<div class="clear"></div>
	<div class="add-comment{{ HTML::dynamicArea($class != "", $class) }} {{ HTML::hiddenArea($hideCommentArea) }}" id="{{ $divId }}">
		<form action="{{ URL::to('comments/create') }}" method="post" class="form-comment">
			<label for="comment{{ $commentID }}">{{ $label }}:</label>
			<textarea name="comment" id="comment{{ $commentID }}" class="wysiwyg" placeholder="Add a comment...">{{ $commentText }}</textarea>

			<input type="hidden" name="content_type" class="content-type" value="{{ $contentType }}" />
			<input type="hidden" name="content_id" class="content-id" value="{{ $id }}" />
			<input type="hidden" name="comment_id" class="comment-id" value="{{ $commentID }}" />
			<input type="hidden" name="parent_id" class="parent-id" value="{{ $parentID }}" />

			<div>
				<input type="submit" name="add_comment" class="left" value="{{ $label }}" />
				<div class="clear"></div>
			</div>
		</form>
	</div><!--/add-comment-->
@else
	@if (!$parentID)
		<div class="add-comment">
			<p class="login"><a href="{{{ URL::to('login') }}}">Log in</a> to add a comment.</p>
		</div><!--/add-comment-->
	@endif
@endif