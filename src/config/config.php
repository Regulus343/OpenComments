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
	| comments in your database. In the below example, "BlogArticle" is the
	| content type and "blog_entries" is the database table:
	|
	|     array('BlogArticle' => 'blog_articles')
	|
	*/
	'allowedContentTypes' => false,

	/*
	|--------------------------------------------------------------------------
	| Comment Edit Limit (in seconds)
	|--------------------------------------------------------------------------
	|
	| The comment editing limit in secionds. By default, users may edit or
	| delete their comment for 180 seconds after initial post.
	|
	*/
	'commentEditLimit' => 180,

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
	| Comment Wait Time (in seconds)
	|--------------------------------------------------------------------------
	|
	| The minimum length of time in seconds that must pass between comments
	| for a particular user. The default is 90 seconds. This can prevent a
	| user from flooding your website.
	|
	*/
	'commentWaitTime' => 90,

	/*
	|--------------------------------------------------------------------------
	| Comment Order
	|--------------------------------------------------------------------------
	|
	| The order that the comments appear in, "asc" being oldest to newest and
	| "desc" being newest to oldest.
	|
	*/
	'commentOrder' => 'desc',

	/*
	|--------------------------------------------------------------------------
	| Comments Per Page
	|--------------------------------------------------------------------------
	|
	| The number of comments per page. Pagination buttons exist in the comments
	| area to allow the user to page through all comments.
	|
	*/
	'commentsPerPage' => 15,

	/*
	|--------------------------------------------------------------------------
	| Comment Auto-Approval
	|--------------------------------------------------------------------------
	|
	| Determines whether the comments should be auto-approved and show up
	| immediately or whether they are subject to approval by the administrator
	| first. Auto-approval is turned on by default.
	|
	*/
	'commentAutoApproval' => true,

	/*
	|--------------------------------------------------------------------------
	| Load Bootstrap
	|--------------------------------------------------------------------------
	|
	| Whether or not to have Open Comments automatically load Twitter Bootsrap.
	| If set to false, Open Comments will assume you are already loading
	| Bootstrap CSS and JS files. If true, Open Comments will attempt to load
	| "bootstrap.css" and "bootstrap.min.js".
	|
	*/
	'loadBootstrap' => true,

);