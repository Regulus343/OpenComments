<?php namespace Regulus\OpenComments;

use \BaseController;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;

use Regulus\TetraText\TetraText as Format;
use Regulus\SolidSite\SolidSite as Site;

class CommentsController extends BaseController {

	public function postCreate()
	{
		return json_encode(Comment::createUpdate());
	}

	public function getDelete($id = 0) {
		return json_encode(OpenComments::delete($id));
	}

	public function postList()
	{
		$contentID   = Input::get('content_id');
		$contentType = Input::get('content_type');

		$comments    = Comment::compileList($contentID, $contentType);

		$message = Lang::get('open-comments::messages.noComments');
		if (count($comments) > 0) {
			$commentStr = Lang::get('open-comments::messages.comment');
			if (count($comments) != 1) $commentStr = Str::plural($commentStr);
			$message = Lang::get('open-comments::messages.numberComments', array('number' => count($comments), 'item' => $commentStr));
		}

		return json_encode(array('comments' => Comment::format($comments), 'message' => $message));
	}

}