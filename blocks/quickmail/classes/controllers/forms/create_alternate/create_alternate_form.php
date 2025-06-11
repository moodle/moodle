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

namespace block_quickmail\controllers\forms\create_alternate;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use block_quickmail\controllers\support\controller_form;
use block_quickmail_string;

class create_alternate_form extends controller_form {

    /*
     * Moodle form definition
     */
    public function definition() {

        $mform =& $this->_form;

        // View_form_name directive: TO BE INCLUDED ON ALL FORMS.
        $mform->addElement('hidden', 'view_form_name');
        $mform->setType('view_form_name', PARAM_TEXT);
        $mform->setDefault('view_form_name', $this->get_view_form_name());

        $mform->addElement('hidden', 'course_id');
        $mform->setType('course_id', PARAM_INT);
        $mform->setDefault('course_id', $this->get_custom_data('course_id'));

        // Email (text).
        $mform->addElement(
            'text',
            'email',
            get_string('email')
        );
        $mform->setType(
            'email',
            PARAM_TEXT
        );
        $mform->addRule('email', get_string('required'), 'required', '', 'server');
        $mform->addRule('email', get_string('invalidemail'), 'email', '', 'server');

        // Firstname (text).
        $mform->addElement(
            'text',
            'firstname',
            get_string('firstname')
        );
        $mform->setType(
            'firstname',
            PARAM_TEXT
        );
        $mform->addRule('firstname', get_string('required'), 'required', '', 'server');

        // Lastname (text).
        $mform->addElement(
            'text',
            'lastname',
            get_string('lastname')
        );
        $mform->setType(
            'lastname',
            PARAM_TEXT
        );
        $mform->addRule('lastname', get_string('required'), 'required', '', 'server');

        // Availability (select).
        if (count($this->get_custom_data('availability_options')) > 1) {
            $mform->addElement(
                'select',
                'availability',
                block_quickmail_string::get('alternate_availability'),
                $this->get_custom_data('availability_options')
            );
            $mform->setType(
                'availability',
                PARAM_TEXT
            );
        } else {
            $mform->addElement('hidden', 'availability');
            $mform->setType('availability', PARAM_TEXT);
            $mform->setDefault('availability', 'user');
        }

        // Allowed_role_ids (multiselect).
        if ($this->should_show_role_selection()) {
            $select = $mform->addElement('select', 'allowed_role_ids', 'Roles Allowed',
                          $this->get_custom_data('role_selection'))->setMultiple(true);
            $mform->disabledIf('allowed_role_ids', 'availability', 'neq', 'course');
        } else {
            $mform->addElement('hidden', 'allowed_role_ids');
            $mform->setType('allowed_role_ids', PARAM_TEXT);
            $mform->setDefault('allowed_role_ids', '');
        }

        // Buttons!
        $buttons = [
            $mform->createElement('cancel', 'cancelbutton', get_string('back')),
            $mform->createElement('submit', 'save', get_string('save', 'block_quickmail')),
        ];

        $mform->addGroup($buttons, 'actions', '&nbsp;', array(' '), false);
    }

    /**
     * Reports whether or not the allowed_role_ids multiselect should be displayed
     *
     * @return bool
     */
    private function should_show_role_selection() {
        return !empty($this->get_custom_data('role_selection')) && !empty($this->get_custom_data('course_id'));
    }

}
