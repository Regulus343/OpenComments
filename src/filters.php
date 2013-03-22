<?php namespace Regulus\OpenComments;

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Request;

Route::filter('ajax', function()
{
	if (!Request::ajax()) exit;
});