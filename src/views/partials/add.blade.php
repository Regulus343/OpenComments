@if (Regulus\OpenComments\OpenComments::auth())

	<div class="clear"></div>
	<div class="add-comment" id="add-comment">

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
			<label for="comment-new"><?php echo Lang::get('open-comments::messages.addComment') ?>:</label>
			<textarea name="comment" class="field-comment wysiwyg" id="comment-new" placeholder="Add a comment..."></textarea>

			<input type="hidden" name="content_type" class="content-type" value="{{ Site::get('contentType') }}" />
			<input type="hidden" name="content_id" class="content-id" value="{{ Site::get('contentID') }}" />
			<input type="hidden" name="comment_id" class="comment-id" value="" />
			<input type="hidden" name="parent_id" class="parent-id" value="" />

			<div>
				<input type="submit" name="add_comment" class="left" value="<?php echo Lang::get('open-comments::messages.addComment') ?>" />
				<div class="clear"></div>
			</div>
		{{ Form::close() }}

	</div><!-- /add-comment -->
@else
	<div class="add-comment">
		<p class="login"><a href="{{{ URL::to('login') }}}">Log in</a> to add a comment.</p>
	</div><!-- /add-comment -->
@endif