<script id="comments-template" type="text/x-handlebars-template">
	{{#each comments}}

		<li id="comment{{id}}" class="{{#if active_user_comment}}active-user{{/if}}{{#unless parent}} sub{{/unless}}{{#if edit_time}} editable{{/if}}{{#unless approved}} unapproved{{/unless}}{{#if deleted}} deleted{{/if}}" data-parent-id="{{parent_id}}">

			<!-- Messages -->
			<div class="top-messages">
				<!-- Success Message -->
				<div class="message success hidden"></div>

				<!-- Error Message -->
				<div class="message error hidden"></div>

				<!-- General Info Message -->
				<div class="message info hidden"></div>
			</div>

			<div class="info">
				<h1><a href="" class="profile-popup" rel="{{user_id}}">{{user}}</a></h1>
				<ul class="info">
					<li>
						<label>Role:</label>
						<span>{{user_role}}</span>
					</li><li>
						<label>Member Since:</label>
						<span>{{user_since}}</span>
					</li>
				</ul>

				<a href="" class="display-pic profile-popup" rel="{{user_id}}"><img src="{{user_image}}" alt="" /></a>

				<div class="clear"></div>
			</div>

			<div class="comment">
				<!-- Comment Text -->
				<div class="text">{{{comment}}}</div>

				<!-- Date Posted -->
				<div class="date-posted">
					{{created_at}}

					{{#if updated}}
						last updated {{updated_at}}
					{{/if}}
				</div>
			</div>

			<!-- Actions -->
			{{#if logged_in}}

				{{#if edit_time}}
					<div class="edit-countdown">{{{edit_countdown}}}</div>
				{{/if}}

				<ul class="actions">
					{{#if edit}}

						<li class="action-delete">
							<a href="" class="btn button button-delete button-delete-comment" rel="{{id}}">
								<div class="icon icon-remove"></div><span><?=Lang::get('open-comments::labels.delete')?></span>
							</a>
						</li>

						<li class="action-edit">
							<a href="" class="btn button button-edit button-edit-comment" rel="{{id}}">
								<div class="icon icon-edit"></div><span><?=Lang::get('open-comments::labels.edit')?></span>
							</a>
						</li>

					{{/if}}

					{{#if approve}}

						<li class="action-approve">
							<a href="" class="btn button button-approve button-approve-comment" rel="{{id}}">
								{{#unless approved}}
									<div class="icon icon-plus-sign"></div><span><?=Lang::get('open-comments::labels.approve')?></span>
								{{else}}
									<div class="icon icon-minus-sign"></div><span><?=Lang::get('open-comments::labels.unapprove')?></span>
								{{/unless}}
							</a>
						</li>

					{{/if}}

					{{#if parent}}

						<li class="action-reply">
							<a href="" class="btn button button-reply button-reply-comment" rel="{{id}}">
								<div class="icon icon-share-alt"></div><span><?=Lang::get('open-comments::labels.reply')?></span>
							</a>
						</li>

					{{else}}

						<li class="action-reply">
							<a href="" class="btn button button-reply button-reply-comment reply-to-parent" rel="{{parent_id}}">
								<div class="icon icon-share-alt"></div><span><?=Lang::get('open-comments::labels.replyToParent')?></span>
							</a>
						</li>

					{{/if}}
				</ul>

			{{/if}}

			{{#if edit}}

				<div class="clear"></div>
				<div id="edit-comment{{id}}" class="add-comment edit-comment hidden" id="">

					<!-- Success Message -->
					<div class="message success hidden"></div>

					<!-- Error Message -->
					<div class="message error hidden"></div>

					<!-- General Info Message -->
					<div class="message info hidden"></div>

					<!-- Comment Form - Edit -->
					<?=Form::open('comments/create', 'post', array('class' => 'form-comment'))?>

						<label for="comment-edit{{id}}"><?=Lang::get('open-comments::labels.editComment')?>:</label>
						<textarea name="comment" class="field-comment wysiwyg" id="comment-edit{{id}}">{{comment}}</textarea>

						<input type="hidden" name="content_type" class="content-type" value="{{content_type}}" />
						<input type="hidden" name="content_id" class="content-id" value="{{content_id}}" />
						<input type="hidden" name="comment_id" class="comment-id" value="{{id}}" />
						<input type="hidden" name="parent_id" class="parent-id" value="" />

						<input type="submit" name="add_comment" class="left" value="<?=Lang::get('open-comments::labels.editComment')?>" />

					<?=Form::close()?>

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
						<li><label>Role:</label> <span>{{active_user_role}}</span></li>
						<li><label>Member Since:</label> <span>{{active_user_since}}</span></li>
					</ul>

					<a href="" class="display-pic profile-popup" rel="u{{user_id}}"><img src="{{active_user_image}}" alt="" /></a>

					<div class="clear"></div>
				</div>

				<!-- Success Message -->
				<div class="message success hidden"></div>

				<!-- Error Message -->
				<div class="message error hidden"></div>

				<!-- General Info Message -->
				<div class="message info hidden"></div>

				<!-- Comment Form - Reply -->
				<?=Form::open('comments/create', 'post', array('class' => 'form-comment'))?>

					<label for="comment{{id}}"><?=Lang::get('open-comments::labels.addReply')?>:</label>
					<textarea name="comment" class="field-comment wysiwyg" id="comment{{id}}"></textarea>

					<input type="hidden" name="content_type" class="content-type" value="{{content_type}}" />
					<input type="hidden" name="content_id" class="content-id" value="{{content_id}}" />
					<input type="hidden" name="comment_id" class="comment-id" value="" />
					<input type="hidden" name="parent_id" class="parent-id" value="{{id}}" />

					<input type="submit" name="add_comment" class="left" value="<?=Lang::get('open-comments::labels.addReply')?>" />

				<?=Form::close();?>

				<div class="clear"></div>
			</li>

		{{/if}}

	{{/each}}
</script>