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

namespace tool_mfa\local\form;

defined('MOODLE_INTERNAL') || die();
require_once("$CFG->libdir/formslib.php");

/**
 * Form to reset gracemode timer for users.
 *
 * @package     tool_mfa
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reset_factor extends \moodleform {

    /**
     * Form definition.
     */
    public function definition(): void {
        $mform = $this->_form;
        $factors = $this->_customdata['factors'];
        $bulkaction = $this->_customdata['bulk'];

        $mform->addElement('hidden', 'bulkaction', $bulkaction);
        $mform->setType('bulkaction', PARAM_BOOL);

        $mform->addElement('hidden', 'returnurl');
        $mform->setType('returnurl', PARAM_LOCALURL);

        $factors = array_map(function ($element) {
            return $element->get_display_name();
        }, $factors);
        // Add an 'all' action.
        $factors['all'] = get_string('all');

        $mform->addElement('select', 'factor', get_string('selectfactor', 'tool_mfa'), $factors);

        if (!$bulkaction) {
            $mform->addElement('text', 'resetfactor', get_string('resetuser', 'tool_mfa'),
            ['placeholder' => get_string('resetfactorplaceholder', 'tool_mfa')]);
            $mform->setType('resetfactor', PARAM_TEXT);
            $mform->addRule('resetfactor', get_string('userempty', 'tool_mfa'), 'required');
        }

        $this->add_action_buttons(true, get_string('resetconfirm', 'tool_mfa'));
    }

    /**
     * Form validation.
     *
     * Server side rules do not work for uploaded files, implement serverside rules here if needed.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);

        if (!$data['bulkaction']) {
            $userinfo = $data['resetfactor'];
            // Try input as username first, then email.
            $user = $DB->get_record('user', ['username' => $userinfo]);
            if (empty($user)) {
                // If not found, try username.
                $user = $DB->get_record('user', ['email' => $userinfo]);
            }

            if (empty($user)) {
                $errors['resetfactor'] = get_string('usernotfound', 'tool_mfa');
            } else {
                // Add custom field to store user.
                $this->_form->addElement('hidden', 'user', $user);
                $this->_form->setType('user', PARAM_RAW);
            }
        }

        return $errors;
    }
}
