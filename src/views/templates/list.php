<script id="comments-template" type="text/x-handlebars-template">
	{{#each comments}}

		<?php /*$classes = array();
		if ($comment->user_id == $this->session->userdata('user_id'))	$classes[] = "current-user";
		if ($comment->parent_id)										$classes[] = "sub";*/ ?>

		<li id="comment{{id}}" class="{{#if active_user_post}}active-user{{/if}}{{#unless parent}} sub{{/unless}}">

			<!-- Message -->
			<div class="message success hidden"></div>

			<div class="info">
				<h1><a href="" class="profile-popup" rel="{{user_id}}">{{user}}</a></h1>
				<ul class="info">
					<li><label>Role:</label> <span>{{user_role}}</span></li>
					<li><label>Member Since:</label> <span>{{user_since}}</span></li>
				</ul>

				<a href="" class="display-pic profile-popup" rel="{{user_id}}"><img src="{{user_image}}" alt="" /></a>

				<div class="clear"></div>
			</div>

			<div class="comment">
				<div class="text">{{{comment}}}</div>

				<div class="date-posted">
					{{created_at}}

					{{#if updated}}
						last updated {{updated_at}}
					{{/if}}
				</div>
			</div>

			<!-- Actions -->
			{{#if logged_in}}

				{{#if edit}}
					<div class="edit-countdown">You may edit or delete your comment for <strong>90</strong> more seconds</div>
				{{/if}}

				<ul class="actions">
					{{#if edit}}

						<li class="action-delete">
							<a href="" class="button button-delete button-delete-comment" rel="{{id}}">
								<?php echo Lang::get('open-comments::labels.delete'); ?>
							</a>
						</li>

						<li class="action-edit">
							<a href="" class="button button-edit button-edit-comment" rel="{{id}}">
								<?php echo Lang::get('open-comments::labels.edit'); ?>
							</a>
						</li>

					{{/if}}

					{{#if parent}}

						<li class="action-reply">
							<a href="" class="button button-reply button-reply-comment" rel="{{id}}">
								<?php echo Lang::get('open-comments::labels.reply'); ?>
							</a>
						</li>

					{{else}}

						<li class="action-reply">
							<a href="" class="button button-reply button-reply-comment reply-to-parent" rel="{{parent_id}}">
								<?php echo Lang::get('open-comments::labels.replyToParent'); ?>
							</a>
						</li>

					{{/if}}
				</ul>

			{{/if}}

			{{#if edit}}

				</php /*$commentID    = Session::get('commentID');
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
				}*/ ?>

				<div class="clear"></div>
				<div class="add-comment edit-comment hidden" id="">

					<!-- Success Message -->
					<div class="message success hidden"></div>

					<!-- Error Message -->
					<div class="message error hidden"></div>

					<!-- General Info Message -->
					<div class="message info hidden"></div>

					<!-- Comment Form - Edit -->
					<?php echo Form::open('comments/create', 'post', array('class' => 'form-comment')); ?>
						<label for="comment-edit{{id}}"><?php echo Lang::get('open-comments::labels.editComment') ?>:</label>
						<textarea name="comment" class="field-comment wysiwyg" id="comment-edit{{id}}">{{comment}}</textarea>

						<input type="hidden" name="content_type" class="content-type" value="{{content_type}}" />
						<input type="hidden" name="content_id" class="content-id" value="{{content_id}}" />
						<input type="hidden" name="comment_id" class="comment-id" value="{{id}}" />
						<input type="hidden" name="parent_id" class="parent-id" value="" />

						<input type="submit" name="add_comment" class="left" value="<?php echo Lang::get('open-comments::labels.editComment') ?>" />

					<?php echo Form::close(); ?>

				</div><!-- /add-comment -->

			{{/if}}

			<div class="clear"></div>
		</li>

		{{#if reply}}

			<!-- Reply Area -->
			<li id="reply{{id}}" class="add-reply sub active-user hidden">
				<div class="info">
					<h1><a href="javascript:void(0);">{{active_user_name}}</a></h1>
					<ul class="info">
						<li><label>Role:</label> <span>{{active_role_name}}</span></li>
						<li><label>Member Since:</label> <span>{{active_member_since}}</span></li>
					</ul>

					<a href="" class="display-pic profile-popup" rel="u{{user_id}}"><img src="{{active_user_image}}" alt="" /></a>

					<div class="clear"></div>
				</div>

				<!-- Comment Form - Reply -->
				<?php echo Form::open('comments/create', 'post', array('class' => 'form-comment')); ?>
					<label for="comment{{id}}"><?php echo Lang::get('open-comments::labels.addReply') ?>:</label>
					<textarea name="comment" class="field-comment wysiwyg" id="comment{{id}}" placeholder="Add a reply..."></textarea>

					<input type="hidden" name="content_type" class="content-type" value="{{content_type}}" />
					<input type="hidden" name="content_id" class="content-id" value="{{content_id}}" />
					<input type="hidden" name="comment_id" class="comment-id" value="" />
					<input type="hidden" name="parent_id" class="parent-id" value="{{id}}" />

					<input type="submit" name="add_comment" class="left" value="<?php echo Lang::get('open-comments::labels.addReply') ?>" />

				<?php echo Form::close(); ?>

				<div class="clear"></div>
			</li>

		{{/if}}

	{{/each}}
</script>