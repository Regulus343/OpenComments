<?php namespace Regulus\OpenComments;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

use Illuminate\Support\Facades\Route;

//map entire controller
Route::controller('comments', 'Regulus\OpenComments\CoreController');

Route::controller('ajax/comments', 'Regulus\OpenComments\AjaxController');

Route::when('ajax/comments/*',  'ajax');

Route::when('comments', 'comments');
Route::when('comments/*', 'comments');