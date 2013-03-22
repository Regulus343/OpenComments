<?php namespace Regulus\OpenComments;

use \BaseController;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

use Regulus\TetraText\TetraText as Format;
use Regulus\SolidSite\SolidSite as Site;

class CommentsController extends BaseController {

	public function postCreate()
	{
		$results = array(
			'result'  => 'Error',
			'comment' => Comment::createUpdate(),
		);

		if (!empty($results['comment'])) $results['result'] = "Success";
		return json_encode($results);
	}

}