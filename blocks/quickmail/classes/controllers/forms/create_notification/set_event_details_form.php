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
use block_quickmail_plugin;
use block_quickmail_string;

class set_event_details_form extends controller_form {

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
            . block_quickmail_string::get('set_event_details_description') . '</div>');

        // Time_delay_unit (select).
        $mform->addElement(
            'select',
            'time_delay_unit',
            block_quickmail_string::get('time_delay_unit'),
            $this->get_time_unit_options()
        );

        $mform->setDefault(
            'time_delay_unit',
            $this->has_session_stored('time_delay_unit') ? $this->get_session_stored('time_delay_unit') : ''
        );

        $mform->addHelpButton(
            'time_delay_unit',
            'time_delay_unit',
            'block_quickmail'
        );

        // Time_delay_amount (text).
        $mform->addElement(
            'text',
            'time_delay_amount',
            block_quickmail_string::get('time_amount'),
            ['size' => 4]
        );

        $mform->setType(
            'time_delay_amount',
            PARAM_TEXT
        );

        $mform->setDefault(
            'time_delay_amount',
            $this->has_session_stored('time_delay_amount') ? $this->get_session_stored('time_delay_amount') : '0'
        );

        $mform->addRule('time_delay_amount', block_quickmail_string::get('invalid_time_amount'), 'callback', function($value) {
            return empty($value)
                ? true
                : is_numeric($value);
        }, 'server');

        // Mute_time_unit (select).
        if (!$this->get_custom_data('is_one_time_event')) {
            $mform->addElement(
                'select',
                'mute_time_unit',
                block_quickmail_string::get('mute_time_unit'),
                $this->get_time_unit_options()
            );

            $mform->setDefault(
                'mute_time_unit',
                $this->has_session_stored('mute_time_unit') ? $this->get_session_stored('mute_time_unit') : ''
            );

            $mform->addHelpButton(
                'mute_time_unit',
                'mute_time_unit',
                'block_quickmail'
            );

            // Mute_time_amount (text).
            $mform->addElement(
                'text',
                'mute_time_amount',
                block_quickmail_string::get('time_amount'),
                ['size' => 4]
            );

            $mform->setType(
                'mute_time_amount',
                PARAM_TEXT
            );

            $mform->setDefault(
                'mute_time_amount',
                $this->has_session_stored('mute_time_amount') ? $this->get_session_stored('mute_time_amount') : '0'
            );

            $mform->addRule('mute_time_amount', block_quickmail_string::get('invalid_time_amount'), 'callback', function($value) {
                return empty($value)
                    ? true
                    : is_numeric($value);
            }, 'server');
        }

        // Buttons!
        $buttons = [
            $mform->createElement('submit', 'back', get_string('back')),
            $mform->createElement('submit', 'next', get_string('next')),
        ];

        $mform->addGroup($buttons, 'actions', '&nbsp;', array(' '), false);
    }

    /**
     * Returns the options time_delay_unit selection
     *
     * @return array
     */
    private function get_time_unit_options() {
        return block_quickmail_plugin::get_time_unit_selection_array(['minute', 'hour', 'day'], 'none');
    }

}
