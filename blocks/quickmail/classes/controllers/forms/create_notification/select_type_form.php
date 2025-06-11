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

class select_type_form extends controller_form {

    /*
     * Moodle form definition
     */
    public function definition() {

        $mform =& $this->_form;

        // View_form_name directive: TO BE INCLUDED ON ALL FORMS.
        $mform->addElement('hidden', 'view_form_name');
        $mform->setType('view_form_name', PARAM_TEXT);
        $mform->setDefault('view_form_name', $this->get_view_form_name());

        // Name (text).
        $mform->addElement(
            'text',
            'notification_name',
            block_quickmail_string::get('notification_name'),
            ['size' => 40, 'placeholder' => 'My New Notification Title']
        );

        $mform->setType(
            'notification_name',
            PARAM_TEXT
        );

        $mform->setDefault(
            'notification_name',
            $this->has_session_stored('notification_name') ? $this->get_session_stored('notification_name') : ''
        );

        $mform->addRule('notification_name', get_string('required'), 'required', '', 'server');
        $mform->addRule('notification_name', get_string('err_maxlength', 'form',
            (object)['format' => 40]), 'maxlength', 40, 'server');

        $mform->addHelpButton(
            'notification_name',
            'notification_name',
            'block_quickmail'
        );

        $mform->addElement('static', 'reminder_description', '', '<strong>Reminder</strong>: '
            . block_quickmail_string::get('notification_type_reminder_description'));
        $mform->addElement('static', 'event_description', '', '<strong>Event</strong>: '
            . block_quickmail_string::get('notification_type_event_description'));

        // Notification_type (select).
        $mform->addElement(
            'select',
            'notification_type',
            block_quickmail_string::get('notification_type'),
            $this->get_notification_type_options()
        );

        $mform->setDefault(
            'notification_type',
            $this->has_session_stored('notification_type') ? $this->get_session_stored('notification_type') : ''
        );

        $mform->addRule('notification_type', block_quickmail_string::get('invalid_notification_type'), 'required', '', 'server');
        $mform->addRule('notification_type', block_quickmail_string::get('invalid_notification_type'), 'callback',
            function($value) {
                return in_array($value, ['reminder', 'event']);
            }, 'server');

        // Buttons!
        $buttons = [
            $mform->createElement('cancel', 'cancelbutton', get_string('cancel')),
            $mform->createElement('submit', 'next', get_string('next')),
        ];

        $mform->addGroup($buttons, 'actions', '&nbsp;', array(' '), false);
    }

    /**
     * Returns the options for notification type selection
     *
     * @return array
     */
    private function get_notification_type_options() {
        return [
            '' => get_string('select'),
            'reminder' => block_quickmail_string::get('notification_type_reminder'),
            'event' => block_quickmail_string::get('notification_type_event')
        ];
    }

}
