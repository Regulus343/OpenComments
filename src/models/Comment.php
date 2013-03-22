<?php namespace Regulus\OpenComments;

use Illuminate\Database\Eloquent\Model as Eloquent;

use Illuminate\Support\Facades\Config;
use Regulus\Identify\Identify as Auth;

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

	public static function createUpdate($id = 0)
	{
		$results = array('result'  => 'Error',
						'action'  => 'Added',
						'message' => 'Something went wrong with your attempt to add/update a comment. Please try again.',
						'comment' => array());

		if (!OpenComments::auth()) return $results;

		if ($id) {
			$event = SocialEvent::find($id);
		} else {
			$event          = new SocialEvent;
			$event->user_id = Auth::user()->id;
		}

		$userID      = Auth::userID();
		$contentID   = trim(Input::get('content_id'));
		$contentType = trim(Input::get('content_type'));
		$id          = trim(Input::get('comment_id'));
		$parentID    = trim(Input::get('parent_id'));
		$editLimit   = date('Y-m-d H:i:s', strtotime('-12 minutes'));

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

		//if type does not exist, do not create comment
		if (!in_array($contentType, array('Event', 'Materials Item', 'Bill', 'Candidate'))) return $result;

		//if parent ID is set, make sure it exists for content item
		/*if ($parentID != "") {
			$exists = $this->db->query("SELECT id FROM comments WHERE content_id=".$this->db->escape($data['content_id'])." AND content_type='".$data['content_type']."' AND id=".$this->input->post('parent_id')." AND parent_id='0'")->num_rows();
			if ($exists) {
				$data['parent_id'] = $this->input->post('parent_id');
				$result['parent_id'] = $data['parent_id'];
			}
		}

		//ensure content item exists
		$item = array();
		switch ($data['content_type']) {
			case "Event":
				$item = $this->event($data['content_id']);
				break;
			case "Materials Item":
				$this->load->model('mod_activism', 'activism');
				$item = $this->activism->materials_item($data['content_id']);
				break;
		}
		if (!empty($item)) { //content item exists
			switch ($data['content_type']) {
				case "Event":
					$result['return'] = 'event/'.$data['content_id'];
					break;
				case "Materials Item":
					$result['return'] = 'activism/materials/'.$data['content_id'];
					break;
				case "Bills":
					$result['return'] = 'politics/bills/'.$data['content_id'];
					break;
			}
			$result['return'] .= "#comment".$result['parent_id'];

			if ($id && $id != "") {
				$result['comment_id'] = $id;
				$result['return'] = str_replace('#comment'.$result['parent_id'], '#comment'.$result['comment_id'], $result['return']);
			}

			//purify HTML
			$data['comment'] = $this->purify_html($this->input->post('comment'));

			if (strlen($data['comment']) < 24) {
				$result['message'] = 'Your comment must be longer. Please add a bit more text.';
				return $result;
			}

			if ($id && $id != "") {
				if (!$this->auth->allow('admin')) {
					//if editing, ensure user has sufficient privileges to edit
					$comment_editable = $this->db->query("SELECT * FROM comments WHERE id=".$this->db->escape($id)."
														  AND user_id='".$data['user_id']."'
														  AND date_created >='".$edit_limit."'")->num_rows();
					if (!$comment_editable) {
						$result['message'] = 'The comment you are trying to edit either does not exist or is too old to edit. You may only edit a comment within 12 minutes of the time it is first posted.';
						return $result;
					}
				}

				$data['date_updated'] = date('Y-m-d H:i:s');
				if ($this->auth->allow('admin')) {
					$this->db->update('comments', $data, array('id'=>$id));
				} else {
					$this->db->update('comments', $data, array('id'=>$id, 'user_id'=>$data['user_id']));
				}
				if ($this->db->affected_rows() > 0) {
					$result['result'] = "Success";
					$result['action'] = "Updated";
					$result['message'] = 'You have successfully updated your comment.';

					//log activity
					$this->general->log_activity(ucwords($data['content_type']).' - Comment Updated', '', $data['content_id']);
				} else {
					$result['message'] = 'You have not made any changes to your comment.';
				}
			} else {
				$last_comment = get_cookie('last_comment');
				if ($last_comment != "" && !$this->auth->allow('admin')) {
					$result['message'] = 'You may only post one comment every 90 seconds.';
					return $result;
				}

				$data['date_updated'] = date('Y-m-d H:i:s');
				$this->db->insert('comments', $data);
				$result['result'] = "Success";
				$result['comment_id'] = $this->db->insert_id();
				$result['return'] = str_replace('#comment'.$result['parent_id'], '#comment'.$result['comment_id'], $result['return']);
				$result['message'] = 'You have successfully added a comment. You may revise or delete it for the next 12 minutes. After that, your comment becomes permanent.';

				//log activity
				$this->general->log_activity(ucwords($data['content_type']).' - Comment Added', '', $data['content_id']);

				//set a cookie to allow editing for a limited time - 12 minutes
				set_cookie(array('name'=>	'comment',
								 'value'=>	$this->db->insert_id(),
								 'expire'=>	720));

				//set a cookie to limit comments to one every 90 seconds
				set_cookie(array('name'=>	'last_comment',
								 'value'=>	date('Y-m-d H:i:s'),
								 'expire'=>	90));
			}
		}*/
		return $result;
	}

}