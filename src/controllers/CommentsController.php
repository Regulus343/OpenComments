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

	public function getApprove($id = 0) {
		return json_encode(OpenComments::toggleApproval($id));
	}

	public function postList()
	{
		$contentID   = Input::get('content_id');
		$contentType = Input::get('content_type');
		$page        = (int) Input::get('page');
		if (!$page) $page = 1;

		$comments    = Comment::compileList($contentID, $contentType, $page);

		//get the total number of comments
		$totalComments = Comment::where('content_id', '=', $contentID)->where('content_type', '=', $contentType);
		if (!OpenComments::admin()) {
			$totalComments = $totalComments->where('approved', '=', true)->where('deleted', '=', false);
		}
		$totalComments   = $totalComments->count();
		$commentsPerPage = Config::get('open-comments::commentsPerPage');
		$totalPages = ceil($totalComments / $commentsPerPage);

		//add a message
		$message = Lang::get('open-comments::messages.noComments');
		if (count($comments) > 0) {
			$start = $page * $commentsPerPage - $commentsPerPage + 1;
			$end   = $start + $commentsPerPage - 1;
			if ($end > $totalComments) $end = $totalComments;

			$commentPlural = Lang::get('open-comments::messages.comment').($totalComments == 1 ? '' : 's');

			$message = Lang::get('open-comments::messages.numberComments', array('start' => $start, 'end' => $end, 'total' => $totalComments, 'commentPlural' => $commentPlural));
		}

		$results = array(
			'comments'    => Comment::format($comments),
			'message'     => $message,
			'currentPage' => $page,
			'totalPages'  => $totalPages, 
		);
		return json_encode($results);
	}

}