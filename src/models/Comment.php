<?php namespace Regulus\OpenComments;

use Illuminate\Database\Eloquent\Model as Eloquent;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

use Regulus\TetraText\TetraText as Format;

class Comment extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'comments';

	/**
	 * The attributes that cannot be updated.
	 *
	 * @var array
	 */
	protected $guarded = array('id');

	/**
	 * The default order of the comments, "asc" being oldest to newest and
	 * "desc" being newest to oldest.
	 *
	 * @var string
	 */
	public static $commentOrder = false;

	/**
	 * Gets the creator of the comment.
	 *
	 * @return object
	 */
	public function creator()
	{
		return $this->belongsTo(Config::get('auth.model'), 'user_id');
	}

	/**
	 * Can be used to fetch the content that the comments are attached to.
	 *
	 * @return object
	 */
	public function content()
	{
		return $this->morphTo();
	}

	/**
	 * Creates or updates a comment.
	 *
	 * @param  integer  $id
	 * @return mixed
	 */
	public static function createUpdate($id = 0)
	{
		$results = array(
			'resultType' => 'Error',
			'action'     => 'Create',
			'commentID'  => false,
			'comment'    => '',
			'message'    => Lang::get('open-comments::messages.errorGeneral'),
		);

		//ensure user is logged in
		if (!OpenComments::auth()) return $results;

		$userID      = OpenComments::userID();
		$contentID   = trim(Input::get('content_id'));
		$contentType = trim(Input::get('content_type'));
		$id          = (int) trim(Input::get('comment_id'));
		$parentID    = (int) trim(Input::get('parent_id'));
		$commentText = trim(Input::get('comment'));
		$editLimit   = date('Y-m-d H:i:s', strtotime('-'.Config::get('open-comments::editLimit').' minutes'));

		//if allowedContentTypes config is set, require the content type to be specified and the item to exist in the database
		$allowedContentTypes = Config::get('open-comments::allowedContentTypes');
		if ($allowedContentTypes && is_array($allowedContentTypes)) {

			//content type is not allowed; return error results
			if (!isset($allowedContentTypes[$contentType])) return $results;

			//item does not exist in specified table; return error results
			$item = DB::table($allowedContentTypes[$contentType])->find($contentID);
			if (empty($item)) return $results;

			//item is deleted; return error results
			$itemArray = (array) $item;
			if (isset($itemArray['deleted']) && $itemArray['deleted']) return $results;
		}

		//if parent ID is set, make sure it exists for content item
		if ($parentID) {
			$exists = Comment::where('id', '=', $parentID)
								->where('parent_id', '=', 0)
								->where('content_id', '=', $contentID)
								->where('content_type', '=', $contentType)
								->count();

			if (!$exists) return $results;
		}

		//purify HTML
		$commentText = Format::purifyHTML($commentText);

		//require minimum length
		if (Config::get('open-comments::commentMinLength') && strlen($commentText) < Config::get('open-comments::commentMinLength')) {
			$results['message'] = Lang::get('open-comments::messages.errorMinLength', array('number' => Config::get('open-comments::commentMinLength')));
			return $results;
		}

		$admin = OpenComments::admin();

		if ($id) {
			$results['action'] = "Update";

			//if editing, ensure user has sufficient privileges to edit
			if (!$admin) {
				$commentEditable = Comment::where('id', '=', $id)
										->where('user_id', '=', $userID)
										->where('created_at', '>=', $editLimit)
										->count();
				if (!$commentEditable) {
					$results['message'] = Lang::get('open-comments::messages.errorUneditable');
					return $results;
				}
			}

			if ($admin) {
				$comment = static::find($id);
			} else {
				$comment = static::where('id', '=', $id)->where('user_id', '=', $userID)->first();
			}

			if (empty($comment)) return $results;

		} else {
			//ensure user has not posted a comment too soon after another one
			if (!$admin) {
				$commentWaitTime = Config::get('open-comments::commentWaitTime');
				if ($commentWaitTime) {
					$lastComment = static::where('user_id', '=', $userID)->orderBy('id', 'desc')->first();
					if (!empty($lastComment)) {
						$timeToWait = $commentWaitTime - (time() - strtotime($lastComment->created_at));
						if ($timeToWait > 0) {
							$results['message'] = Lang::get('open-comments::messages.errorWaitTime', array('number' => $commentWaitTime, 'time' => $timeToWait, 'secondPlural' => 'second'.($commentWaitTime == 1 ? '' : 's')));
							return $results;
						}
					}
				}
			}
		}

		if ($results['action'] == "Create") {
			$comment = new static;
			$comment->user_id = $userID;

			$autoApproval = Config::get('open-comments::commentAutoApproval');
			if ($autoApproval || $admin) {
				$comment->approved    = true;
				$comment->approved_at = date('Y-m-d H:i:s');
			}

			$comment->content_id   = $contentID;
			$comment->content_type = $contentType;
			$comment->parent_id    = $parentID;
			$comment->ip_address   = Request::getClientIp();
		}

		$comment->comment = $commentText;
		$comment->save();

		$results['commentID'] = $comment->id;

		//add order ID for easy comment ordering for queries
		if ($results['action'] == "Create") {
			if ($parentID) {
				$comment->order_id = $parentID;
			} else {
				$comment->order_id = $comment->id;
			}
			$comment->save();

			Session::set('lastComment', $comment->id);
		}

		$results['resultType'] = "Success";
		if ($results['action'] == "Create") {
			$results['message'] = Lang::get('open-comments::messages.successCreated');
			if (!$autoApproval) $results['message'] .= ' '.Lang::get('open-comments::messages.notYetApproved');
		} else {
			$results['message'] = Lang::get('open-comments::messages.successUpdated');
		}

		//set the comment totals for the model declared by the content type if feature is enabled
		if ($allowedContentTypes && is_array($allowedContentTypes) && Config::get('open-comments::setCommentTotals')) {
			$totalComments = static::where('content_id', '=', $contentID)->where('content_type', '=', $contentType)->count();

			DB::table($allowedContentTypes[$contentType])->where('id', '=', $contentID)->update(array('comments' => $totalComments));
		}

		//log activity
		//Activity::log(ucwords($data['content_type']).' - Comment Updated', '', $data['content_id']);

		return $results;
	}

	/**
	 * Compiles a list of comments based on the content ID and content type.
	 *
	 * @param  integer  $contentID
	 * @param  string   $contentType
	 * @param  mixed    $page
	 * @return mixed
	 */
	public static function compileList($contentID, $contentType, $page = false)
	{
		if (!static::$commentOrder) {
			if (!is_null(Session::get('commentOrder'))) {
				static::$commentOrder = Session::get('commentOrder');
			} else {
				static::$commentOrder = Config::get('open-comments::commentOrder');
			}
		}

		$comments = static::where('content_id', '=', $contentID)
			->where('content_type', '=', $contentType);

		$admin = OpenComments::admin();
		if (!$admin) {
			$comments
				->where('approved', '=', true)
				->where('deleted', '=', false);
		}

		$comments
			->orderBy('order_id', static::$commentOrder)
			->orderBy('parent_id')
			->orderBy('id', static::$commentOrder);

		if ($page) {
			$commentsPerPage = Config::get('open-comments::commentsPerPage');
			$commentsToSkip  = ($page - 1) * $commentsPerPage;
			$comments = $comments->skip($commentsToSkip)->take($commentsPerPage);
		}
		$comments = $comments->get();

		return $comments;
	}

	/**
	 * Formats the comments for Handlebars JS.
	 *
	 * @param  object   $comments
	 * @return mixed
	 */
	public static function format($comments)
	{
		$commentsFormatted = array();

		$admin        = OpenComments::admin();
		$autoApproval = Config::get('open-comments::commentAutoApproval');

		if (OpenComments::auth()) {
			$user = OpenComments::user();
			$activeUser = array(
				'id'           => $user->id,
				'name'         => $user->getName(),
				'role'         => 'User',
				'member_since' => date('F Y', strtotime($user->activated_at)),
				'image'        => $user->getPicture(),
			);
		} else {
			$activeUser = array(
				'id'           => 0,
				'name'         => '',
				'role'         => '',
				'member_since' => '',
				'image'        => '',
			);
		}

		foreach ($comments as $comment) {
			$commentArray = $comment->toArray();

			$commentArray['logged_in'] = OpenComments::auth();

			$creator                       = $comment->creator;
			$commentArray['user']          = $creator->getName();
			$commentArray['user_role']     = $creator->roles[0]->name;
			$commentArray['user_comments'] = 0;
			$commentArray['user_since']    = date('F Y', strtotime($creator->activated_at));
			$commentArray['user_image']    = $comment->creator->getPicture();

			$commentArray['created_at'] = date('F j, Y \a\t g:i:sa', strtotime($commentArray['created_at']));
			$commentArray['updated_at'] = date('F j, Y \a\t g:i:sa', strtotime($commentArray['updated_at']));
			if (substr($commentArray['created_at'], 0, 13) != substr($commentArray['updated_at'], 0, 13)) {
				$commentArray['updated'] = true;
			} else {
				$commentArray['updated'] = false;
			}

			if ($commentArray['logged_in'] && !$commentArray['parent_id']) {
				$commentArray['reply'] = true;
			} else {
				$commentArray['reply'] = false;
			}

			$commentArray['approve']  = ! $autoApproval;
			$commentArray['approved'] = (bool) $commentArray['approved'];

			$commentArray['deleted']  = (bool) $commentArray['deleted'];

			$commentArray['edit_time'] = strtotime($comment->created_at) - strtotime('-'.Config::get('open-comments::commentEditLimit').' seconds');

			if ($commentArray['edit_time'] < 0)
				$commentArray['edit_time'] = 0;

			if (Session::get('lastComment') != $commentArray['id'] || $admin)
				$commentArray['edit_time'] = 0;

			if ((int) $commentArray['user_id'] == (int) $activeUser['id']) {
				$commentArray['active_user_comment'] = false;
			} else {
				$commentArray['active_user_comment'] = false;
				$commentArray['edit_time']           = 0;
			}

			if ($commentArray['edit_time'] || $admin) {
				$commentArray['edit'] = true;
			} else {
				$commentArray['edit'] = false;
			}

			$commentArray['edit_countdown'] = $commentArray['edit_time'] > 0 ? Lang::get('open-comments::messages.editCountdown', array('seconds' => $commentArray['edit_time'])) : '';

			$commentArray['parent_id'] = (int) $commentArray['parent_id'];
			$commentArray['parent']    = ! $commentArray['parent_id'] ? true : false;

			$commentArray['active_user_name']  = $activeUser['name'];
			$commentArray['active_user_role']  = $activeUser['role'];
			$commentArray['active_user_since'] = $activeUser['member_since'];
			$commentArray['active_user_image'] = $activeUser['image'];

			$commentsFormatted[] = $commentArray;
		}
		return $commentsFormatted;
	}

}