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
use block_quickmail\repos\notification_repo;
use block_quickmail\persistents\notification;
use block_quickmail_string;

class notification_index_controller extends base_controller {

    public static $baseuri = '/blocks/quickmail/notifications.php';

    public static $views = [
        'notification_index' => [],
    ];

    public static $actions = [
        'disable',
        'enable',
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
        return ['courseid' => $this->props->course->id];
    }

    /**
     * Manage draft messages
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function notification_index(controller_request $request) {
        // Get all notifications belonging to this course user.
        $notifications = notification_repo::get_all_for_course($this->props->course->id, $this->props->user->id, [
            'sort' => $this->props->page_params['sort'],
            'dir' => $this->props->page_params['dir'],
            'paginate' => true,
            'page' => $this->props->page_params['page'],
            'per_page' => $this->props->page_params['per_page'],
            'uri' => $_SERVER['REQUEST_URI']
        ]);

        $this->render_component('notification_index', [
            'notifications' => $notifications->data,
            'courseid' => $this->props->course->id,
            'user' => $this->props->user,
            'pagination' => $notifications->pagination,
            'sort_by' => $this->props->page_params['sort'],
            'sort_dir' => $this->props->page_params['dir'],
        ]);
    }

    /**
     * Disable notification action
     *
     * @param  controller_request  $request
     * @return void
     */
    public function action_disable(controller_request $request) {
        $this->handle_status_action($request, 'disable');
    }

    /**
     * Enable notification action
     *
     * @param  controller_request  $request
     * @return void
     */
    public function action_enable(controller_request $request) {
        $this->handle_status_action($request, 'enable');
    }

    /**
     * Delete notification action
     *
     * @param  controller_request  $request
     * @return void
     */
    public function action_delete(controller_request $request) {
        if ($notification = notification::find_or_null($this->props->page_params['notificationid'])) {
            $notification->delete_self();
        }

        // Redirect back to index as success.
        $request->redirect_as_success(block_quickmail_string::get('notification_deleted'),
            static::$baseuri, $this->get_form_url_params());
    }

    /**
     * Handles the given status edit action
     *
     * @param  controller_request $request
     * @param  string             $type       enable|disable
     * @return void
     */
    private function handle_status_action(controller_request $request, $type) {
        // Validate action.
        if (!in_array($type, ['enable', 'disable'])) {
            // Redirect back to index with error.
            $request->redirect_as_error('Invalid action!', static::$baseuri, $this->get_form_url_params());
        }

        // Grab the notification which must belong to this course and user.
        if (!$notification = notification_repo::get_notification_for_course_user_or_null(
                $this->props->page_params['notificationid'],
                $this->props->course->id,
                $this->props->user->id)) {
            // Redirect back to index with error.
            $request->redirect_as_error(block_quickmail_string::get('notification_not_found'),
                static::$baseuri, $this->get_form_url_params());
        }

        // Handle the action.
        if ($type == 'enable') {
            $notification->enable();
        } else {
            $notification->disable();
        }

        // Redirect back to index as success.
        $request->redirect_as_success(block_quickmail_string::get('notification_updated'),
            static::$baseuri, $this->get_form_url_params());
    }

}
