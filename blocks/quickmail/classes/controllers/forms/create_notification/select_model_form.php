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

class select_model_form extends controller_form {

    /*
     * Moodle form definition
     */
    public function definition() {

        $mform =& $this->_form;

        // View_form_name directive: TO BE INCLUDED ON ALL FORMS.
        $mform->addElement('hidden', 'view_form_name');
        $mform->setType('view_form_name', PARAM_TEXT);
        $mform->setDefault('view_form_name', $this->get_view_form_name());

        // Notification_model (select).

        $mform->addElement(
            'select',
            'notification_model',
            block_quickmail_string::get('notification_model'),
            $this->get_notification_model_options()
        );

        $mform->setDefault(
            'notification_model',
            $this->has_session_stored('notification_model') ? $this->get_session_stored('notification_model') : ''
        );

        $mform->addRule('notification_model', block_quickmail_string::get('invalid_notification_model'), 'required', '', 'server');

        // Get keys for validation below.
        $validvalues = $this->get_custom_data('available_model_keys');
        $mform->addRule('notification_model', block_quickmail_string::get('invalid_notification_model'), 'callback',
            function($value) use ($validvalues) {
                return in_array($value, $validvalues);
            }, 'server');

        // Model descriptions.
        foreach ($this->get_notification_model_descriptions() as $name => $description) {
            $mform->addElement('static', 'model_description', '', '<strong>' . $name . '</strong>: ' . $description);
        }

        // Buttons!
        $buttons = [
            $mform->createElement('submit', 'back', get_string('back')),
            $mform->createElement('submit', 'next', get_string('next')),
        ];

        $mform->addGroup($buttons, 'actions', '&nbsp;', array(' '), false);
    }

    /**
     * Returns the options for notification model selection for this notification type, including a "select" option
     *
     * @return array
     */
    private function get_notification_model_options() {
        $notificationtype = $this->get_session_stored('notification_type');

        $options = array_reduce($this->get_custom_data('available_model_keys'), function($carry, $key) use ($notificationtype) {
            $carry[$key] = block_quickmail_string::get('notification_model_'. $notificationtype . '_' . $key);
            return $carry;
        }, []);

        return array_merge(
            ['' => get_string('select')],
            $options
        );
    }

    /**
     * Returns the descriptions for notification model options for this notification type
     *
     * @return array
     */
    private function get_notification_model_descriptions() {
        $notificationtype = $this->get_session_stored('notification_type');

        return array_reduce($this->get_custom_data('available_model_keys'), function($carry, $key) use ($notificationtype) {
            $carry[block_quickmail_string::get('notification_model_'. $notificationtype . '_' . $key)]
                = block_quickmail_string::get('notification_model_'. $notificationtype . '_' . $key . '_description');
            return $carry;
        }, []);
    }
}
