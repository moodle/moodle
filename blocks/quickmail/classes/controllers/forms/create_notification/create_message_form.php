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

namespace block_quickmail\controllers\forms\create_notification;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use block_quickmail\controllers\support\controller_form;
use block_quickmail_string;
use block_quickmail\messenger\message\substitution_code;

class create_message_form extends controller_form {

    /*
     * Moodle form definition
     */
    public function definition() {

        $mform =& $this->_form;

        // View_form_name directive: TO BE INCLUDED ON ALL FORMS.
        $mform->addElement('hidden', 'view_form_name');
        $mform->setType('view_form_name', PARAM_TEXT);
        $mform->setDefault('view_form_name', $this->get_view_form_name());

        // Descriptive text.
        $mform->addElement('html', '<div style="margin-bottom: 20px;">'
            . block_quickmail_string::get('create_notification_message_description') . '</div>');

        // TODO: condition summary?
        // Message_alternate_email_id (select).
        $mform->addElement(
            'select',
            'message_alternate_email_id',
            get_string('from'),
            $this->get_from_email_values()
        );

        $mform->setDefault(
            'message_alternate_email_id',
            $this->has_session_stored('message_alternate_email_id') ? $this->get_session_stored('message_alternate_email_id') : ''
        );

        // Message_subject (text).
        $mform->addElement(
            'text',
            'message_subject',
            block_quickmail_string::get('subject'),
            ['size' => 50]
        );
        $mform->setType(
            'message_subject',
            PARAM_TEXT
        );

        $mform->setDefault(
            'message_subject',
            $this->has_session_stored('message_subject') ? $this->get_session_stored('message_subject') : ''
        );

        $mform->addRule('message_subject', block_quickmail_string::get('missing_subject'), 'required', '', 'server');

        // Message_body (textarea).
        $mform->addElement(
            'editor',
            'message_body',
            block_quickmail_string::get('body'),
            '',
            $this->get_custom_data('editor_options')
        )->setValue([
            'text' => $this->has_session_stored('message_body') ? $this->get_session_stored('message_body') : ''
        ]);
        $mform->setType(
            'message_body',
            PARAM_RAW
        );

        $mform->addRule('message_body', block_quickmail_string::get('missing_body'), 'required', '', 'server');

        $mform->addElement('html', '<div class="col-md-3"></div>');
        $mform->addElement('html', '<div class="col-md-9">' . $this->get_user_fields_html() . '</div>');

        // Message_type (select).
        if ($this->should_show_message_type_selection()) {
            $mform->addElement(
                'select',
                'message_type',
                block_quickmail_string::get('select_message_type'),
                $this->get_message_type_options()
            );

            // Inject default if draft mesage.
            $mform->setDefault(
                'message_type',
                $this->has_session_stored('message_type')
                    ? $this->get_session_stored('message_type')
                    : $this->get_custom_data('course_config_array')['default_message_type']
            );
        } else {
            $mform->addElement(
                'hidden',
                'message_type'
            );
            $mform->setDefault(
                'message_type',
                $this->get_custom_data('course_config_array')['default_message_type']
            );
            $mform->setType(
                'message_type',
                PARAM_TEXT
            );
        }

        // Message_signature_id (select).
        if ($this->should_show_signature_selection()) {
            $mform->addElement(
                'select',
                'message_signature_id',
                block_quickmail_string::get('signature'),
                $this->get_user_signature_options()
            );

            $mform->setDefault(
                'message_signature_id',
                $this->has_session_stored('message_signature_id')
                    ? $this->get_session_stored('message_signature_id')
                    : $this->get_custom_data('user_default_signature_id')
            );
        } else {
            $mform->addElement(
                'hidden',
                'message_signature_id',
                0
            );
            $mform->setType(
                'message_signature_id',
                PARAM_INT
            );
        }

        // Message_send_to_mentors (radio) - copy mentors of recipients or not?
        if ($this->should_show_copy_mentor()) {
            $mentorcopyoptions = [
                $mform->createElement('radio', 'message_send_to_mentors', '', get_string('yes'), 1),
                $mform->createElement('radio', 'message_send_to_mentors', '', get_string('no'), 0)
            ];

            $mform->addGroup(
                $mentorcopyoptions,
                'mentor_copy_action',
                block_quickmail_string::get('mentor_copy'),
                [' '],
                false
            );
            $mform->addHelpButton(
                'mentor_copy_action',
                'mentor_copy',
                'block_quickmail'
            );

            $mform->setDefault(
                'message_send_to_mentors',
                $this->has_session_stored('message_send_to_mentors') ? $this->get_session_stored('message_send_to_mentors') : 0
            );
        } else {
            $mform->addElement(
                'hidden',
                'message_send_to_mentors',
                0
            );
            $mform->setType(
                'message_send_to_mentors',
                PARAM_INT
            );
        }

        // Buttons!
        $buttons = [
            $mform->createElement('submit', 'back', get_string('back')),
            $mform->createElement('submit', 'next', get_string('next')),
        ];

        $mform->addGroup($buttons, 'actions', '&nbsp;', array(' '), false);
    }

    /**
     * Returns an array of available sending email options
     *
     * @return array
     */
    private function get_from_email_values() {
        $values = [];

        foreach ($this->get_custom_data('user_alternate_email_array') as $key => $value) {
            $values[(string) $key] = $value;
        }

        $values['-1'] = get_config('moodle', 'noreplyaddress');

        return $values;
    }

    /**
     * Reports whether or not this form should display the message type selection input
     *
     * @return bool
     */
    private function should_show_message_type_selection() {
        return $this->get_custom_data('course_config_array')['message_types_available'] == 'all';
    }

    /**
     * Returns the options for message type selection
     *
     * @return array
     */
    private function get_message_type_options() {
        return [
            'message' => block_quickmail_string::get('message_type_message'),
            'email' => block_quickmail_string::get('message_type_email')
        ];
    }

    /**
     * Reports whether or not this form should display the signature selection input
     *
     * @return bool
     */
    private function should_show_signature_selection() {
        return count($this->get_custom_data('user_signature_array'));
    }

    /**
     * Returns the current user's signatures for selection, plus a "none" option
     *
     * @return array
     */
    private function get_user_signature_options() {
        return [0 => 'None'] + $this->get_custom_data('user_signature_array');
    }

    /**
     * Reports whether or not this form should display the "copy mentor" input
     *
     * @return bool
     */
    private function should_show_copy_mentor() {
        return (bool) ($this->get_custom_data('allow_mentor_copy')
            && $this->get_custom_data('course_config_array')['allow_mentor_copy']);
    }

    /**
     * Returns an array of user-relative data fields that may be injected into the message body
     *
     * @return array
     */
    private function get_allowed_user_fields() {
        return substitution_code::get(['user', $this->get_custom_data('notification_object_type')]);
    }

    /**
     * Returns the HTML that should be displayed as the content of the "user substitution codes" helper display
     *
     * @return string
     */
    private function get_user_fields_html() {
        $html = '<p style="margin-bottom: 4px;"><i>' . block_quickmail_string::get('select_allowed_user_fields') . ':</i></p>';

        foreach ($this->get_allowed_user_fields() as $field) {
            $html .= '<div class="field-label user-field-label">[:' . $field . ':]</div>';
        }

        return $html;
    }

}
