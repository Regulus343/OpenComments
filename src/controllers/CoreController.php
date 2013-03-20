<?php namespace Regulus\OpenComments;

use \BaseController;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

use Regulus\TetraText\TetraText as Format;
use Regulus\SolidSite\SolidSite as Site;

class CoreController extends BaseController {

	public function getIndex()
	{
		echo 'TEST 9!'; exit;
	}

	public function postCreate()
	{
		print_r('wak!');
	}

}