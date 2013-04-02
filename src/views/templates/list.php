<script id="comments-template" type="text/x-handlebars-template">
	{{#each comments}}

		<?php /*$classes = array();
		if ($comment->user_id == $this->session->userdata('user_id'))	$classes[] = "current-user";
		if ($comment->parent_id)										$classes[] = "sub";*/ ?>

		<li id="comment{{id}}"{{#if active_user_post}} class="active-user"{{/if}}>

			<!-- Message -->
			<div class="message success hidden"></div>

			<div class="info">
				<h1><a href="" class="profile-popup" rel="{{user_id}}">{{user}}</a></h1>
				<ul class="info">
					<li><label>Role:</label> <span>{{user_role}}</span></li>
					<li><label>Comments:</label> <span>{{user_comments}}</span></li>
					<li><label>Member Since:</label> <span>{{user_since}}</span></li>
				</ul>

				<a href="" class="display-pic profile-popup" rel="u{{user_id}}"><img src="{{user_image}}" alt="" /></a>

				<div class="clear"></div>
			</div>

			<div class="comment">
				{{{comment}}}

				<div class="date-posted">
					{{created_at}}

					{{#if updated}}
						last updated {{updated_at}}
					{{/if}}
				</div>
			</div>

			<!-- Actions -->
			{{#if logged_in}}

				<ul class="actions">
					{{#if edit}}

						<li class="action-delete">
							<?php echo Lang::get('open-comments::messages.delete') ?>
						</li>

						<li class="action-edit">
							<?php echo Lang::get('open-comments::messages.edit') ?>
						</li>

					{{/if}}

					{{#if parent_id}}

						<li class="action-reply">
							<a href="" class="button button-reply" class="button button-reply-comment" rel="{{parent_id}}">
								<?php echo Lang::get('open-comments::messages.replyToParent') ?>
							</a>
						</li>

					{{else}}

						<li class="action-reply">
							<a href="" class="button button-reply" class="button button-reply-comment" rel="{{id}}">
								<?php echo Lang::get('open-comments::messages.reply') ?>
							</a>
						</li>

					{{/if}}
				</ul>

			{{/if}}

			{{#if edit_comment}}

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
				<div class="add-comment" id="">

					<!-- Success Message -->
					<div class="message success hidden">
						<div class="main"></div>
						<div class="sub"></div>
					</div>

					<!-- Error Message -->
					<div class="message error hidden">
						<div class="main"></div>
						<div class="sub"></div>
					</div>

					<!-- General Info Message -->
					<div class="message info hidden">
						<div class="main"></div>
						<div class="sub"></div>
					</div>

					<!-- Comment Form - Edit -->
					<?php echo Form::open('comments/create', 'post', array('class' => 'form-comment')); ?>
						<label for="comment-edit{{id}}"><?php echo Lang::get('open-comments::messages.editComment') ?>:</label>
						<textarea name="comment" class="field-comment wysiwyg" id="comment-edit{{id}}">{{comment}}</textarea>

						<input type="hidden" name="content_type" class="content-type" value="{{content_type}}" />
						<input type="hidden" name="content_id" class="content-id" value="{{content_id}}" />
						<input type="hidden" name="comment_id" class="comment-id" value="{{id}}" />
						<input type="hidden" name="parent_id" class="parent-id" value="{{parent_id}}" />

						<input type="submit" name="add_comment" class="left" value="<?php echo Lang::get('open-comments::messages.editComment') ?>" />

					<?php echo Form::close(); ?>

				</div><!-- /add-comment -->

			{{else}}

				{{#if parent_id}}
					<div class="add-comment">
						<p class="login"><a href="<?php echo URL::to('login'); ?>">Log in</a> to add a comment.</p>
					</div><!-- /add-comment -->
				{{/if}}

			{{/if}}

			<div class="clear"></div>
		</li>

		{{#if reply}}

			<!-- Reply Area -->
			<li id="reply{{id}}" class="reply sub active-user">
				<div class="info">
					<h1><a href="javascript:void(0);">{{active_user_name}}</a></h1>
					<ul class="info">
						<li><label>Role:</label> <span>{{active_role_name}}</span></li>
						<li><label>Comments:</label> <span>{{comments_with_reply}}</span></li>
						<li><label>Member Since:</label> <span>{{active_member_since}}</span></li>
					</ul>
					<div class="clear"></div>

					<a href="" class="display-pic profile-popup" rel="u{{user_id}}"><img src="{{active_user_image}}" alt="" /></a>
				</div>

				<!-- Comment Form - Reply -->
				<?php echo Form::open('comments/create', 'post', array('class' => 'form-comment')); ?>
					<label for="comment{{id}}"><?php echo Lang::get('open-comments::messages.addReply') ?>:</label>
					<textarea name="comment" class="field-comment wysiwyg" id="comment{{id}}" placeholder="Add a reply..."></textarea>

					<input type="hidden" name="content_type" class="content-type" value="{{content_type}}" />
					<input type="hidden" name="content_id" class="content-id" value="{{content_id}}" />
					<input type="hidden" name="comment_id" class="comment-id" value="{{id}}" />
					<input type="hidden" name="parent_id" class="parent-id" value="{{parent_id}}" />

					<input type="submit" name="add_comment" class="left" value="<?php echo Lang::get('open-comments::messages.addReply') ?>" />

				<?php echo Form::close(); ?>

				<div class="clear"></div>
			</li>

		{{/if}}

	{{/each}}
</script>