<?php namespace Regulus\OpenComments;

/*----------------------------------------------------------------------------------------------------------
	OpenComments
		A light, effective user comments composer package that is easy to configure and implement.

		created by Cody Jassman
		last updated on May 17, 2013
----------------------------------------------------------------------------------------------------------*/

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

class OpenComments {

	public static $auth;

	/**
	 * Authenticates users for the default OpenForum views while remaining authorization class-agnostic.
	 *
	 * @return boolean
	 */
	public static function auth()
	{
		$auth = static::configAuth();
		if ($auth->methodActiveCheck != false) {
			$function = static::separateFunction($auth->methodActiveCheck);
			return static::callFunction($function);
		}
		return false;
	}

	/**
	 * Authenticates admin for the default OpenForum views while remaining authorization class-agnostic.
	 *
	 * @return boolean
	 */
	public static function admin()
	{
		$auth = static::configAuth();
		if ($auth->methodAdminCheck) {
			if (static::auth()) {
				$user = static::user();
				if ($user->roles[0]->role == $auth->methodAdminRole) return true;
			}
		}
		return false;
	}

	/**
	 * Gets the active user.
	 *
	 * @return boolean
	 */
	public static function user()
	{
		$auth = static::configAuth();
		if ($auth->methodActiveUser != false) {
			$function = static::separateFunction($auth->methodActiveUser);
			return static::callFunction($function);
		}
		return false;
	}

	/**
	 * Gets the active user ID.
	 *
	 * @return boolean
	 */
	public static function userID()
	{
		$auth = static::configAuth();
		$user = static::user();

		if (isset($user->{$auth->methodActiveUserID}))
			return $user->{$auth->methodActiveUserID};

		return false;
	}

	/**
	 * Prepare authorization configuration.
	 *
	 * @return array
	 */
	private static function configAuth()
	{
		if (is_null(static::$auth)) {
			static::$auth = (object) array(
				'class'              => Config::get('open-comments::authClass'),
				'methodActiveCheck'  => Config::get('open-comments::authMethodActiveCheck'),
				'methodActiveUser'   => Config::get('open-comments::authMethodActiveUser'),
				'methodActiveUserID' => Config::get('open-comments::authMethodActiveUserID'),
				'methodAdminCheck'   => Config::get('open-comments::authMethodAdminCheck'),
				'methodAdminRole'    => Config::get('open-comments::authMethodAdminRole'),
			);
		}
		return static::$auth;
	}

	/**
	 * Delete a comment.
	 *
	 * @return string
	 */
	public static function delete($id)
	{
		$results = array(
			'resultType' => 'Error',
			'message'    => Lang::get('open-comments::messages.errorGeneral'),
		);

		$comment = Comment::find($id);
		if (!empty($comment)) {
			$userID = static::userID();
			$admin  = static::admin();

			if ($admin || ($userID == $comment->user_id && strtotime($comment->created_at) >= strtotime('-'.Config::get('open-comments::commentWaitTime').' seconds'))) {
				if ($admin) {
					$comment->delete();
					$replies = Comment::where('parent_id', '=', $id)->get();
					foreach ($replies as $reply) {
						$reply->delete();
					}
					$results['resultType'] = "Success";
					$results['message']    = Lang::get('open-comments::messages.successDeleted');
					return $results;
				} else {
					$repliesExist = Comment::where('parent_id', '=', $id)->where('deleted', '=', 0)->count();
					if (!$admin && $repliesExist) {
						$results['message'] = Lang::get('open-comments::messages.errorDeleteRepliesExist');
					}

					$dateDeleted = date('Y-m-d H:i:s');
					$comment->deleted    = true;
					$comment->deleted_at = $dateDeleted;
					$comment->save();

					Comment::where('parent_id', '=', $id)->update(array('deleted' => true, 'deleted_at' => $dateDeleted));
				}
			} else {
				return $results;
			}
		}
		$results['message'] = Lang::get('open-comments::messages.successDeleted');
		return $results;
	}

	/**
	 * Approves/unapproves a comment.
	 *
	 * @return string
	 */
	public static function toggleApproval($id)
	{
		$results = array(
			'resultType' => 'Error',
			'message'    => Lang::get('open-comments::messages.errorGeneral'),
			'approved'   => false,
		);

		$admin  = static::admin();
		if (!$admin) return $results;

		$comment = Comment::find($id);
		if (empty($comment)) return $results;

		$results['resultType'] = "Success";
		if (!$comment->approved) {
			$comment->approved      = true;
			$comment->approved_at   = date('Y-m-d H:i:s');

			$results['message'] = Lang::get('open-comments::messages.successApproved');
			$results['approved'] = true;
		} else {
			$comment->approved      = false;
			$comment->approved_at   = "0000-00-00 00:00:00";

			$results['message'] = Lang::get('open-comments::messages.successUnapproved');
		}
		$comment->save();

		return $results;
	}

	/**
	 * Separates a function string "function('array')" into the
	 * function name and the parameters for use with call_user_func.
	 *
	 * @param  string   $function
	 * @return object
	 */
	public static function separateFunction($function)
	{
		$data = preg_match('/([\w\_\d]+)\(([\w\W]*)\)/', $function, $matches);
		if (!isset($matches[0])) $matches[0] = $function;
		if (!isset($matches[1])) $matches[1] = str_replace('()', '', $function);
		if (!isset($matches[2])) $matches[2] = null;
		return (object) array(
			'method'     => $matches[1],
			'parameters' => str_replace("'", '', $matches[2]),
		);
	}

	/**
	 * Calls a function using call_user_func and call_user_func array.
	 *
	 * @param  object   $function
	 * @return boolean
	 */
	public static function callFunction($function)
	{
		if (!isset($function->method) OR !isset($function->parameters)) return false;

		$auth = static::configAuth();
		if (substr($function->parameters, 0, 6) == "array(") {

			$function->parameters = explode(',', $function->parameters);
			for ($p = 0; $p < count($function->parameters); $p++) {
				$function->parameters[$p] = str_replace("'", '', $function->parameters[$p]);
				$function->parameters[$p] = str_replace('array(', '', $function->parameters[$p]);
				$function->parameters[$p] = str_replace(')', '', $function->parameters[$p]);
			}

			return call_user_func_array($auth->class.'::'.$function->method, $function->parameters);
		} else {
			return call_user_func($auth->class.'::'.$function->method, $function->parameters);
		}
	}

}