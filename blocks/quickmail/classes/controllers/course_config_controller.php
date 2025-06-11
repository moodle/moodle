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
use block_quickmail_config;

class course_config_controller extends base_controller {

    public static $baseuri = '/blocks/quickmail/configuration.php';

    public static $views = [
        'course_config' => [],
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
     * Manage course configuration
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function course_config(controller_request $request) {
        $form = $this->make_form('course_config\course_config_form', [
            'course_config' => block_quickmail_config::get('', $this->props->course),
            'context' => $this->context,
            'user' => $this->props->user,
            'user_preferred_picker' => get_user_preferences('block_quickmail_preferred_picker', 'autocomplete', $this->props->user)
        ]);

        // List of form submission subactions that may be handled in addition to "back" or "next".
        $subactions = [
            'reset',
            'save',
        ];

        // Route the form submission, if any.
        if ($form->is_submitted_subaction('reset', $subactions)) {
            return $this->post($request, 'course_config', 'reset');
        } else if ($form->is_submitted_subaction('save', $subactions)) {
            return $this->post($request, 'course_config', 'save');
        } else if ($form->is_cancelled()) {
            $request->redirect_to_url('/course/view.php', ['id' => $this->props->course->id]);
        }

        $this->render_form($form);
    }

    /**
     * Handles post of course_config form, reset subaction
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function post_course_config_reset(controller_request $request) {
        // Delete this course's config settings.
        block_quickmail_config::delete_course_config($this->props->course);

        $request->redirect_as_success(get_string('changessaved'),
            '/blocks/quickmail/configuration.php', ['courseid' => $this->props->course->id]);
    }

    /**
     * Handles post of course_config form, save subaction
     *
     * @param  controller_request  $request
     * @return mixed
     */
    public function post_course_config_save(controller_request $request) {
        block_quickmail_config::update_course_config($this->props->course, (array) $request->input);

        $this->set_user_preferred_picker($request->input);

        $request->redirect_as_success(get_string('changessaved'),
        '/blocks/quickmail/configuration.php', ['courseid' => $this->props->course->id]);
    }

    /**
     * Sets the user's personally preferred picker option based on input
     *
     * @param stdClass  $input
     */
    private function set_user_preferred_picker($input) {
        // Sanitize input option, defaulting to autocomplete.
        if (!property_exists($input, 'preferred_picker')) {
            $preferredpicker = 'autocomplete';
        } else if (!in_array($input->preferred_picker, ['autocomplete', 'multiselect'])) {
            $preferredpicker = 'autocomplete';
        } else {
            $preferredpicker = $input->preferred_picker;
        }

        set_user_preference('block_quickmail_preferred_picker', $preferredpicker, $this->props->user);
    }

}
