<?php $classes = array();
if ($comment->user_id == $this->session->userdata('user_id'))	$classes[] = "current-user";
if ($comment->parent_id)										$classes[] = "sub"; ?>
<li id="comment<?=$comment->id?>"<?php if (!empty($classes)) echo ' class="'.implode(' ', $classes).'"'; ?>>
	<div class="info">
		<h1><a href="javascript:void(0);" class="profile-popup" rel="u<?=$comment->user_id?>"><?=$comment->user?></a></h1>
		<ul class="info">
			<li><label>Role:</label> <span><?=$comment->user_role?></span></li>
			<li><label>Comments:</label> <span><?=$comment->user_comments?></span></li>
			<li><label>Member Since:</label> <span><?=date('F Y', strtotime($comment->date_user_created)) ?></span></li>
		</ul>
		<div class="clear"></div>

		<?php $user_image = asset_url('img/display-pic-default.png');
		if (is_file('uploads/user_images/thumbs/'.$comment->user_id.'.jpg')) $user_image = upload_url('user_images/thumbs/'.$comment->user_id.'.jpg'); ?>
		<a href="javascript:void(0);" class="display-pic profile-popup" rel="u<?=$comment->user_id?>"><img src="<?=$user_image?>" alt="" /></a>
	</div><div class="comment">
		<?=$comment->comment?>

		<div class="date-posted">
			<?php echo date('F j, Y \a\t g:ia', strtotime($comment->date_created));
			if ($comment->date_updated != $comment->date_created) echo ', last updated '.date('F j, Y \a\t g:ia', strtotime($comment->date_updated)); ?>
		</div>
	</div>

	<?php if ($this->auth->active()) { ?>
		<ul class="actions">
			<?php //allow temporary editing or deleting for users and permanent editing and deleting for admin
			$edit_comment = false;
			if ($this->auth->allow('admin')
			|| ($this->session->userdata('user_id') == $comment->user_id && strtotime($comment->date_created) >= strtotime('-12 minutes'))) {
				$edit_comment = true; ?>
				<li class="action-delete"><a href="javascript:void(0);" class="button button-delete" onclick="deleteComment(<?=$comment->id?>);">Delete</a></li>

				<li class="action-edit"><a href="javascript:void(0);" class="button button-edit" onclick="editComment(<?=$comment->id?>);">Edit</a></li>
			<?php }

			//reply button
			if ($comment->parent_id) { ?>
				<li class="action-reply"><a href="javascript:void(0);" class="button button-reply" onclick="replyComment(<?=$comment->parent_id?>);">Reply to Parent</a></li>
			<?php } else { ?>
				<li class="action-reply"><a href="javascript:void(0);" class="button button-reply" onclick="replyComment(<?=$comment->id?>);">Reply</a></li>
			<?php } ?>
		</ul>
		<?php if ($edit_comment) {
			if (get_cookie('comment_id_actioned') == $comment->id && $this->session->flashdata('error') != "") {
				$comment_text = get_cookie('comment_incomplete');
			} else {
				$comment_text = $comment->comment;
			}
			$this->load->view('common/comment_add', array('comment_id'=>$comment->id, 'reply_comment'=>false, 'comment_text'=>$comment_text));
		}
	} ?>
	<div class="clear"></div>
</li>
<?php if ($this->auth->active() && !$comment->parent_id) { //add reply area
	$hide_comment_area = true;
	if (get_cookie('reply_id_actioned') == $comment->id && $this->session->flashdata('error') != "") $hide_comment_area = false; ?>
	<li id="reply<?=$comment->id?>" class="reply sub"<?php if ($hide_comment_area) echo ' style="display: none;"'; ?>>
		<div class="info">
			<?php $user = $this->users->user(); ?>
			<h1><a href="javascript:void(0);"><?=$user->name?></a></h1>
			<ul class="info">
				<li><label>Role:</label> <span><?=$user->role_name?></span></li>
				<li><label>Comments:</label> <span><?=($user->comments + 1)?></span></li>
				<li><label>Member Since:</label> <span><?=date('F Y', strtotime($user->created)) ?></span></li>
			</ul>
			<div class="clear"></div>

			<?php $user_image = asset_url('img/display-pic-default.png');
			if (is_file('uploads/user_images/thumbs/'.$user->id.'.jpg')) $user_image = upload_url('user_images/thumbs/'.$user->id.'.jpg'); ?>
			<a href="<?=URL::to('member/'.$user->username)?>" class="display-pic"><img src="<?=$user_image?>" alt="" /></a>
		</div>

		<?php if (!$hide_comment_area) {
			$comment_text = get_cookie('comment_incomplete');
		} else {
			$comment_text = "";
		}
		$this->load->view('common/comment_add', array('comment_id'=>'', 'parent_id'=>$comment->id, 'reply_comment'=>true, 'comment_text'=>$comment_text)); ?>
	</li>
<?php } ?>