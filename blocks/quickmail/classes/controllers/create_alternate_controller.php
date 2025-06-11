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
use block_quickmail_plugin;
use block_quickmail_string;
use block_quickmail\services\alternate_manager;
use block_quickmail\repos\role_repo;
use context_course;

class create_alternate_controller extends base_controller {

    public static $baseuri = '/blocks/quickmail/create_alternate.php';

    public static $views = [
        'create_alternate' => [],
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
     * Show the create alternate form
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function create_alternate(controller_request $request) {
        // Determine whether or not this user is able to create alternates for the course level.
        $coursecontext = context_course::instance($this->props->course_id);
        $allowcoursealternates = block_quickmail_plugin::user_has_capability('allowcoursealternate',
                                                                                 $this->props->user, $coursecontext);

        // Fetch the roles that are available to be assigned to use this new alternate email.
        $roleselection = $allowcoursealternates
            ? role_repo::get_alternate_email_role_selection_array($this->props->course_id)
            : [];

        $form = $this->make_form('create_alternate\create_alternate_form', [
            'course_id' => $this->props->course_id,
            'role_selection' => $roleselection,
            'availability_options' => $this->get_user_availability_options($allowcoursealternates)
        ]);

        $subactions = ['save'];

        // Route the form submission, if any.
        if ($form->is_submitted_subaction('save', $subactions, true)) {
            return $this->post($request, 'create_alternate', 'save');
        } else if ($form->is_cancelled()) {
            return $request->redirect_to_url('/blocks/quickmail/alternate.php', ['courseid' => $this->props->course_id]);
        }

        $this->render_form($form);
    }

    /**
     * Handles post of alternate form
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function post_create_alternate_save(controller_request $request) {
        // Sanitize allowed_role_ids.
        $allowedroleids = property_exists($request->input, 'allowed_role_ids')
            ? $request->input->allowed_role_ids
            : [];

        try {
            // Attempt to create the alternate and send a confirmation email.
            alternate_manager::create_alternate_for_user($this->props->user, [
                'availability' => $request->input->availability,
                'firstname' => $request->input->firstname,
                'lastname' => $request->input->lastname,
                'email' => $request->input->email,
                'allowed_role_ids' => $allowedroleids,
            ], $this->props->course_id);
        } catch (\Exception $e) {
            $request->redirect_as_error($e->getMessage(), static::$baseuri, $this->get_form_url_params());
        }

        // Redirect and notify of success.
        $request->redirect_as_success(block_quickmail_string::get('alternate_created'),
            '/blocks/quickmail/alternate.php', $this->get_form_url_params());
    }

    /**
     * Returns the current user's options for "availability" selection
     *
     * @return array
     */
    private function get_user_availability_options($allowcoursealternates = false) {
        $options['user'] = block_quickmail_string::get('alternate_availability_user');

        if (empty($this->props->course_id) || ! $allowcoursealternates) {
            return $options;
        }

        try {
            $course = get_course($this->props->course_id);
            $courseshortname = $course->shortname;
        } catch (\Exception $e) {
            $courseshortname = 'Non-Existent Course';
        }

        $options['only'] = block_quickmail_string::get('alternate_availability_only',
                                                           (object) ['courseshortname' => $courseshortname]);
        $options['course'] = block_quickmail_string::get('alternate_availability_course',
                                                           (object) ['courseshortname' => $courseshortname]);

        return $options;
    }

}
