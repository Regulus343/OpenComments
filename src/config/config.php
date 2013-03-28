<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Views Location
	|--------------------------------------------------------------------------
	|
	| The location of your comments views. It is defaulted to "open-comments::" to
	| use OpenForum's built-in views, but you may point it towards a views
	| directory of your own for full view customization.
	|
	*/
	'viewsLocation' => 'open-comments::',

	/*
	|--------------------------------------------------------------------------
	| Authorization Class
	|--------------------------------------------------------------------------
	|
	| The name of your authorization class including the namespace and a
	| leading backslash. This variable along with the "authMethod" variables
	| allow OpenForum's built-in views to remain authoriztion class agnostic.
	| The default is "\Illuminate\Support\Facades\Auth" which is Laravel 4's
	| native authorization class.
	|
	*/
	'authClass' => '\Illuminate\Support\Facades\Auth',

	/*
	|--------------------------------------------------------------------------
	| Authorization Method - Authentication Check
	|--------------------------------------------------------------------------
	|
	| The method in your authorization class that checks if user is logged in.
	| The default is "check()" which, along with the default auth class above,
	| selects Laravel 4's native authentication method.
	|
	*/
	'authMethodActiveCheck' => 'check()',

	/*
	|--------------------------------------------------------------------------
	| Authorization Method - Admin Check
	|--------------------------------------------------------------------------
	|
	| The method in your authorization class that checks if the logged in user
	| is an administrator. Set this to false if you do not have a method of
	| identifying an admin.
	|
	*/
	'authMethodAdminCheck' => false,

	/*
	|--------------------------------------------------------------------------
	| Authorization Method - User
	|--------------------------------------------------------------------------
	|
	| The method for getting the active user.
	|
	*/
	'authMethodActiveUser' => 'user()',

	/*
	|--------------------------------------------------------------------------
	| Authorization Method - User ID
	|--------------------------------------------------------------------------
	|
	| The method for getting the active user ID which is used in conjunction
	| with the user method about. By default, they get "user()->id" together.
	|
	*/
	'authMethodActiveUserID' => 'id',

	/*
	|--------------------------------------------------------------------------
	| Allowed Content Types and Corresponding Tables
	|--------------------------------------------------------------------------
	|
	| It is recommended that you declare a list of allowed content types with
	| their corresponding tables to prevent users from getting invalid
	| comments in your database. In the below example, "Blog Entry" is the
	| content type and "blog_entries" is the database table:
	|
	|     array('Blog Entry' => 'blog_entries')
	|
	*/
	'allowedContentTypes' => false,

	/*
	|--------------------------------------------------------------------------
	| Comment Edit Limit (in minutes)
	|--------------------------------------------------------------------------
	|
	| The comment editing limit in minutes. By default, users may edit or
	| delete their comment for 10 minutes.
	|
	*/
	'commentEditLimit' => 10,

	/*
	|--------------------------------------------------------------------------
	| Comment Minimum Length
	|--------------------------------------------------------------------------
	|
	| The minimum length of characters for a comment. Set to false if for no
	| minimum. The default is 24 characters to prevent pointless "First!"
	| comments and other short, useless comments.
	|
	*/
	'commentMinLength' => 24,

	/*
	|--------------------------------------------------------------------------
	| Comment Wait Time
	|--------------------------------------------------------------------------
	|
	| The minimum length of time in seconds that must pass between comments
	| for a particular user. The default is 90 seconds. This can prevent a
	| user from flooding your website.
	|
	*/
	'commentWaitTime' => 90,

);