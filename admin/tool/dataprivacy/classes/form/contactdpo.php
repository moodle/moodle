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


namespace tool_dataprivacy\form;

use context;
use context_user;
use moodle_exception;
use moodle_url;
use core_form\dynamic_form;
use tool_dataprivacy\api;
use tool_dataprivacy\external;

/**
 * Contact DPO modal form
 *
 * @package    tool_dataprivacy
 * @copyright  2021 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class contactdpo extends dynamic_form {

    /**
     * Form definition
     */
    protected function definition() {
        global $USER;

        $mform = $this->_form;

        $mform->addElement('static', 'replyto', get_string('replyto', 'tool_dataprivacy'), s($USER->email));

        $mform->addElement('textarea', 'message', get_string('message', 'tool_dataprivacy'), 'cols="60" rows="8"');
        $mform->setType('message', PARAM_TEXT);
        $mform->addRule('message', get_string('required'), 'required', null, 'client');
    }

    /**
     * Return form context
     *
     * @return context
     */
    protected function get_context_for_dynamic_submission(): context {
        global $USER;

        return context_user::instance($USER->id);
    }

    /**
     * Check if current user has access to this form, otherwise throw exception
     *
     * @throws moodle_exception
     */
    protected function check_access_for_dynamic_submission(): void {
        if (!api::can_contact_dpo()) {
            throw new moodle_exception('errorcontactdpodisabled', 'tool_dataprivacy');
        }
    }

    /**
     * Process the form submission, used if form was submitted via AJAX
     *
     * @return array
     */
    public function process_dynamic_submission() {
        return external::contact_dpo($this->get_data()->message);
    }

    /**
     * Load in existing data as form defaults (not applicable)
     */
    public function set_data_for_dynamic_submission(): void {
        return;
    }

    /**
     * Returns url to set in $PAGE->set_url() when form is being rendered or submitted via AJAX
     *
     * @return moodle_url
     */
    protected function get_page_url_for_dynamic_submission(): moodle_url {
        global $USER;

        return new moodle_url('/user/profile.php', ['id' => $USER->id]);
    }
}
