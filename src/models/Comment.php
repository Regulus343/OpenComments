<?php namespace Regulus\OpenComments;

use Illuminate\Database\Eloquent\Model as Eloquent;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

use Regulus\TetraText\TetraText as Format;

class Comment extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'comments';

	public function content()
	{
		return $this->morphTo();
	}

	public function comments()
    {
        return $this->hasMany('Comment', 'parent_id');
    }

    public function comment()
    {
        return $this->belongsTo('Comment');
    }

	/**
	 * Creates or updates a comment.
	 *
	 * @return mixed
	 */
	public static function createUpdate($id = 0)
	{
		$results = array(
			'resultType' => 'Error',
			'action'     => 'Create',
			'comment'    => '',
			'message'    => Lang::get('open-comments::messages.errorGeneral'),
		);

		//ensure user is logged in
		if (!OpenComments::auth()) return $results;

		$userID      = OpenComments::userID();
		$contentID   = trim(Input::get('content_id'));
		$contentType = trim(Input::get('content_type'));
		$id          = trim(Input::get('comment_id'));
		$parentID    = trim(Input::get('parent_id'));
		$editLimit   = date('Y-m-d H:i:s', strtotime('-'.Config::get('open-comments::editLimit').' minutes'));
		$commentText = trim(Input::get('comment'));

		//if allowedContentTypes config is set, require the content type to be specified and the item to exist in the database
		$allowedContentTypes = Config::get('open-comments::allowedContentTypes');
		if ($allowedContentTypes && is_array($allowedContentTypes)) {

			//content type is not allowed; return error results
			if (!isset($allowedContentTypes[$contentType])) return $results;

			//item does not exist in specified table; return error results
			$item = DB::table($allowedContentTypes[$contentType])->find($contentID);
			if (empty($item)) return $results;

			//item is deleted; return error results
			$item = $item->toArray();
			if (isset($item['deleted']) && $item['deleted']) return $results;
		}

		//if parent ID is set, make sure it exists for content item
		if ($parentID != "") {
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
			$results['message'] = sprintf(Lang::get('open-comments::messages.errorMinLength'), Config::get('open-comments::commentMinLength'));
			return $results;
		}

		if ($id && $id != "") {
			$results['action'] = "Update";

			//if editing, ensure user has sufficient privileges to edit
			if (!OpenComments::admin()) {
				$commentEditable = Comment::where('id', '=', $id)
											->where('user_id', '=', Session::get('user_id'))
											->where('created_at', '>=', $editLimit)
											->count();
				if (!$commentEditable) {
					$results['message'] = Lang::get('open-comments::messages.errorUneditable');
					return $results;
				}
			}

			if (OpenComments::admin()) {
				$comment = Comment::find($id);
			} else {
				$comment = Comment::where('id', '=', $id)->where('user_id', '=', $userID);
			}

			if (empty($comment)) return $results;

		} else {

			//ensure user has not posted a comment to soon after another one
			if (!OpenComments::admin()) {
				$commentWaitTime = Config::get('open-comments::commentWaitTime');
				if ($commentWaitTime) {
					$lastComment = Cookie::get('lastComment');
					if ($lastComment != "" && (time() - $lastComment) > $commentWaitTime) {
						$results['message'] = Lang::get('open-comments::messages.errorWaitTime');
						return $results;
					}
				}
			}

			$comment = new Comment;

			$comment->user_id = $userID;
		}

		$comment->content_id   = $contentID;
		$comment->content_type = $contentType;
		$comment->parent_id    = $parentID;
		$comment->comment      = $commentText;
		$comment->save();

		$results['resultType'] = "Success";
		if ($results['action'] == "Create") {
			$results['message'] = Lang::get('open-comments::messages.successCreated');
		} else {
			$results['message'] = Lang::get('open-comments::messages.successUpdated');
		}

		Cookie::make('lastComment', time());

		//log activity
		//Activity::log(ucwords($data['content_type']).' - Comment Updated', '', $data['content_id']);

		return $results;
	}

	public static function compileList($contentID, $contentType)
	{
		return static::where('content_id', '=', $contentID)
			->where('content_type', '=', $contentType)
			//->with('Comment')
			->orderBy('id')
			->get();
	}

	public static function format($comments)
	{
		$commentsFormatted = array();
		foreach ($comments as $comment) {
			$commentArray = $comment->toArray();

			$commentArray['logged_in'] = OpenComments::auth();
			$commentArray['creator'] = $comment->creator->getName();
			$commentArray['image'] = $comment->creator->getImage();

			$commentArray['created_at'] = date('F j, Y', strtotime($commentArray['created_at']));
			$commentArray['updated_at'] = date('F j, Y', strtotime($commentArray['updated_at']));
			if (substr($commentArray['created_at'], 0, 10) != substr($commentArray['updated_at'], 0, 10)) {
				$commentArray['updated'] = true;
			} else {
				$commentArray['updated'] = false;
			}

			/*if (Auth::is('admin') OR $event->id == Auth::userID()) {
				$eventArray['actions'] = true;
			} else {
				$eventArray['actions'] = false;
			}

			$eventArray['action_edit'] = false;
			$eventArray['action_cancel'] = false;
			if (!$event->getDatePast() && !$event->cancelled) {
				$eventArray['action_edit'] = true;

				if ($event->active)
					$eventArray['action_cancel'] = true;
			}*/

			$commentsFormatted[] = $commentArray;
		}
		return $commentsFormatted;
	}

}