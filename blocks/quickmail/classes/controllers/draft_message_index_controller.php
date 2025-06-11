<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\controllers;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\controllers\support\base_controller;
use block_quickmail\controllers\support\controller_request;
use block_quickmail_string;
use block_quickmail\repos\course_repo;
use block_quickmail\repos\draft_repo;
use block_quickmail\persistents\message;

class draft_message_index_controller extends base_controller {

    public static $baseuri = '/blocks/quickmail/drafts.php';

    public static $views = [
        'draft_message_index' => [],
    ];

    public static $actions = [
        'duplicate',
        'delete',
    ];

    /**
     * Returns the query string which this controller's forms will append to target URLs
     *
     * NOTE: this overrides the base controller method
     *
     * @return array
     */
    public function get_form_url_params() {
        return ['courseid' => $this->props->course_id];
    }

    /**
     * Manage draft messages
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function draft_message_index(controller_request $request) {
        // Get all (draft) messages belonging to this user and course.
        $messages = draft_repo::get_for_user($this->props->user->id, $this->props->course_id, [
            'sort' => $this->props->page_params['sort'],
            'dir' => $this->props->page_params['dir'],
            'paginate' => true,
            'page' => $this->props->page_params['page'],
            'per_page' => $this->props->page_params['per_page'],
            'uri' => $_SERVER['REQUEST_URI']
        ]);

        // Filter out messages not in this course.
        $filteredmessages = message::filter_messages_by_course($messages->data, $this->props->course_id);

        // Get this user's courses.
        $usercoursearray = course_repo::get_user_course_array($this->props->user);

        $this->render_component('draft_message_index', [
            'messages' => $filteredmessages,
            'user_course_array' => $usercoursearray,
            'course_id' => $this->props->course_id,
            'user' => $this->props->user,
            'pagination' => $messages->pagination,
            'sort_by' => $this->props->page_params['sort'],
            'sort_dir' => $this->props->page_params['dir'],
        ]);
    }

    /**
     * Delete draft message action
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function action_delete(controller_request $request) {
        // Validate params.
        if ( ! $this->props->page_params['message_id']) {
            // Unset the action param.
            $this->props->page_params['action'] = '';

            // Redirect back to index with error.
            $request->redirect_as_error(block_quickmail_string::get('message_not_found'),
                static::$baseuri, $this->props->page_params);
        }

        // Attempt to fetch the draft message.
        if ( ! $message = draft_repo::find_for_user_or_null($this->props->page_params['message_id'], $this->props->user->id)) {
            // Redirect and notify of error.
            $request->redirect_as_error(block_quickmail_string::get('draft_no_record'),
                static::$baseuri, $this->get_form_url_params());
        }

        // Attempt to hard delete draft.
        $message->hard_delete();

        $request->redirect_as_success(block_quickmail_string::get('message_deleted'),
            static::$baseuri, $this->get_form_url_params());
    }

    /**
     * Duplicate draft message action
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function action_duplicate(controller_request $request) {
        // Validate params.
        if ( ! $this->props->page_params['message_id']) {
            // Unset the action param.
            $this->props->page_params['action'] = '';

            // Redirect back to index with error.
            $request->redirect_as_error(block_quickmail_string::get('message_not_found'),
                static::$baseuri, $this->props->page_params);
        }

        // Attempt to fetch the message which must be non-deleted and belong to this user.
        if ( ! $message = message::find_owned_by_user_or_null($this->props->page_params['message_id'], $this->props->user->id)) {
            // Redirect and notify of error.
            $request->redirect_as_error(block_quickmail_string::get('draft_no_record'),
                static::$baseuri, $this->get_form_url_params());
        }

        // If this message is a draft.
        if ($message->get('is_draft')) {
            // Attempt to duplicate the draft.
            \block_quickmail\messenger\messenger::duplicate_draft($message->get('id'), $this->props->user);

            // Otherwise, this must be a scheduled, sending, or sent message.
        } else {
            // Attempt to duplicate the non-draft message.
            \block_quickmail\messenger\messenger::duplicate_message($message->get('id'), $this->props->user);
        }

        $request->redirect_as_success(block_quickmail_string::get('redirect_back_to_course_from_message_after_duplicate'),
            static::$baseuri, $this->get_form_url_params());
    }

}
