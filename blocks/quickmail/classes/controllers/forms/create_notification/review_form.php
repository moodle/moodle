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

class review_form extends controller_form {

    /*
     * Moodle form definition
     */
    public function definition() {

        $mform =& $this->_form;

        // View_form_name directive: TO BE INCLUDED ON ALL FORMS.
        $mform->addElement('hidden', 'view_form_name');
        $mform->setType('view_form_name', PARAM_TEXT);
        $mform->setDefault('view_form_name', $this->get_view_form_name());

        // Edit select type.
        $mform->addElement(
            'static',
            'type_description',
            block_quickmail_string::get('notification_type'),
            block_quickmail_string::get('notification_model_'
                . $this->get_session_stored('notification_type') . '_'
                . $this->get_session_stored('notification_model')) . ' '
                . block_quickmail_string::get('notification_type_'
                . $this->get_session_stored('notification_type'))
        );

        $mform->addElement(
            'static',
            'title',
            block_quickmail_string::get('notification_name'),
            $this->get_session_stored('notification_name')
        );

        $mform->addGroup([
            $mform->createElement('submit', 'edit_select_type', block_quickmail_string::get('edit_notification'))
        ], 'actions', '&nbsp;', array(' '), false);

        $mform->addElement('html', '<hr>');

        /*
         * Edit select object.
         * if ($this->get_session_stored('notification_object_id')) {
         *  Show object details here….
         * }
         */

        // Edit conditions (if has conditions).
        if ($this->get_custom_data('condition_summary')) {
            $mform->addElement(
                'static',
                'condition_summary',
                block_quickmail_string::get('notification_conditions'),
                $this->get_custom_data('condition_summary')
            );

            $mform->addGroup([
                $mform->createElement('submit', 'edit_set_conditions', block_quickmail_string::get('edit_conditions'))
            ], 'actions', '&nbsp;', array(' '), false);

            $mform->addElement('html', '<hr>');
        }

        // Edit schedule (if reminder notification).
        if ($this->is_notification_type('reminder')) {
            $mform->addElement(
                'static',
                'schedule_summary',
                block_quickmail_string::get('notification_schedule'),
                $this->get_custom_data('schedule_summary')
            );

            $mform->addGroup([
                $mform->createElement('submit', 'edit_create_schedule', block_quickmail_string::get('edit_schedule'))
            ], 'actions', '&nbsp;', array(' '), false);

            $mform->addElement('html', '<hr>');
        }

        // Event details (if event notification).
        if ($this->is_notification_type('event')) {
            $mform->addElement(
                'static',
                'time_delay_summary',
                block_quickmail_string::get('time_delay_summary'),
                $this->get_time_summary('time_delay')
            );

            if (!$this->get_custom_data('is_one_time_event')) {
                $mform->addElement(
                    'static',
                    'mute_time_summary',
                    block_quickmail_string::get('mute_time_summary'),
                    $this->get_time_summary('mute_time')
                );
            }

            $mform->addGroup([
                $mform->createElement('submit', 'edit_set_event_details', block_quickmail_string::get('edit_event_details'))
            ], 'actions', '&nbsp;', array(' '), false);

            $mform->addElement('html', '<hr>');
        }

        /*
         * Edit create message
         * 'message_alternate_email_id',
         * 'message_signature_id',
         */
        $mform->addElement(
            'static',
            'message_type_description',
            block_quickmail_string::get('notified_by'),
            block_quickmail_string::get('message_type_' . $this->get_session_stored('message_type'))
        );

        $mform->addElement(
            'static',
            'message_subject',
            block_quickmail_string::get('subject'),
            $this->get_session_stored('message_subject')
        );

        $mform->addElement(
            'static',
            'message_body',
            block_quickmail_string::get('body'),
            $this->get_session_stored('message_body')
        );

        if ($this->get_session_stored('message_send_to_mentors')) {
            $mform->addElement(
                'static',
                'message_mentors',
                block_quickmail_string::get('mentors_copied'),
                get_string('yes')
            );
        }

        $mform->addGroup([
            $mform->createElement('submit', 'edit_create_message', block_quickmail_string::get('edit_message'))
        ], 'actions', '&nbsp;', array(' '), false);

        $mform->addElement('html', '<hr>');

        // Submit notification.
        // Notification_is_enabled.
        $enabledoptions = [
            $mform->createElement('radio', 'notification_is_enabled', '', get_string('yes'), 1),
            $mform->createElement('radio', 'notification_is_enabled', '', get_string('no'), 0)
        ];

        $mform->addGroup(
            $enabledoptions,
            'notification_is_enabled_action',
            block_quickmail_string::get('enable_notification'),
            [' '],
            false
        );
        $mform->addHelpButton(
            'notification_is_enabled_action',
            'notification_is_enabled',
            'block_quickmail'
        );

        $mform->setDefault(
            'notification_is_enabled',
            1 // Default to enabled.
        );

        $mform->addGroup([
            $mform->createElement('submit', 'next', 'Create Notification')
        ], 'actions', '&nbsp;', array(' '), false);

        $mform->addElement('html', '<hr>');
    }

    /**
     * Reports whether or not the notification being created is of the given type
     *
     * @param  string  $type
     * @return bool
     */
    private function is_notification_type($type) {
        return $this->get_session_stored('notification_type') == $type;
    }

    /**
     * Returns a descriptive summary of the given type of time parameter
     *
     * @param  string  $type  time_delay|mute_time
     * @return string
     */
    private function get_time_summary($type) {
        if ($unit = $this->get_session_stored($type . '_unit')) {
            if ($amount = $this->get_session_stored($type . '_amount')) {
                $stringkey = (int) $amount > 1
                    ? $unit . 's'
                    : $unit;

                return $amount . ' ' . ucfirst(get_string($stringkey));
            }
        }

        return get_string('none');
    }

}
