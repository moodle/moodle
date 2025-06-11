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
use block_quickmail\persistents\alternate_email;
use block_quickmail\services\alternate_manager;

class alternate_index_controller extends base_controller {

    public static $baseuri = '/blocks/quickmail/alternate.php';

    public static $views = [
        'alternate_index' => [],
    ];

    public static $actions = [
        'resend',
        'confirm',
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
        return [
            'courseid' => $this->props->course_id,
        ];
    }

    /**
     * Manage user alternate emails
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function alternate_index(controller_request $request) {
        // Get all alternate emails belonging to this user.
        $alternates = alternate_email::get_all_for_user($this->props->user->id);

        $this->render_component('alternate_index', [
            'alternates' => $alternates,
            'course_id' => $this->props->course_id,
        ]);
    }

    /**
     * Resend alternate email confirmation email action
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function action_resend(controller_request $request) {
        // Validate params.
        if (!$this->props->page_params['alternate_id']) {
            // Redirect back to index with error.
            $request->redirect_as_error(block_quickmail_string::get('alternate_email_not_found'),
                static::$baseuri, $this->get_form_url_params());
        }

        try {
            // Attempt to resend the alternate email confirmation email.
            alternate_manager::resend_confirmation_email_for_user($this->props->page_params['alternate_id'], $this->props->user);

            $request->redirect_as_success(block_quickmail_string::get('alternate_confirmation_email_resent'),
                static::$baseuri, $this->get_form_url_params());
        } catch (\Exception $e) {
            $request->redirect_as_error($e->getMessage(), static::$baseuri, $this->get_form_url_params());
        }
    }

    /**
     * Confirm alternate email action
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function action_confirm(controller_request $request) {
        // Validate params.
        if ( ! $this->props->page_params['alternate_id'] || ! $this->props->page_params['token']) {
            // Redirect back to index with error.
            $request->redirect_as_error(block_quickmail_string::get('alternate_invalid_token'),
                static::$baseuri, $this->get_form_url_params());
        }

        try {
            // Attempt to confirm the alternate email.
            $alternateemail = alternate_manager::confirm_alternate_for_user(
                $this->props->page_params['alternate_id'],
                $this->props->page_params['token'],
                $this->props->user
            );

            $request->redirect_as_success(block_quickmail_string::get('alternate_activated', $alternateemail->get('email')),
                static::$baseuri, $this->get_form_url_params());
        } catch (\Exception $e) {
            $request->redirect_as_error($e->getMessage(), static::$baseuri, $this->get_form_url_params());
        }
    }

    /**
     * Delete alternate email action
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function action_delete(controller_request $request) {
        // Validate params.
        if ( ! $this->props->page_params['alternate_id']) {
            // Redirect back to index with error.
            $request->redirect_as_error(block_quickmail_string::get('alternate_email_not_found'),
                static::$baseuri, $this->get_form_url_params());
        }

        try {
            // Attempt to delete the alternate email.
            alternate_manager::delete_alternate_email_for_user($this->props->page_params['alternate_id'], $this->props->user);

            $request->redirect_as_success(block_quickmail_string::get('alternate_deleted'),
                static::$baseuri, $this->get_form_url_params());
        } catch (\Exception $e) {
            $request->redirect_as_error($e->getMessage(), static::$baseuri, $this->get_form_url_params());
        }
    }

}
