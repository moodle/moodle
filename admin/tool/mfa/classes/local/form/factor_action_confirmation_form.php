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

require_once($CFG->libdir . "/formslib.php");

/**
 * Factor action confirmation form.
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factor_action_confirmation_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition(): void {
        $mform = $this->_form;
        $factor = $this->_customdata['factor'];
        $devicename = $this->_customdata['devicename'];
        $factorid = $this->_customdata['factorid'];
        $action = $this->_customdata['action'];

        $mform->addElement('html', get_string('confirmation' . $action, 'tool_mfa', $devicename));

        $mform->setType('factorid', PARAM_INT);
        $mform->addElement('hidden', 'factorid', $factorid);

        $mform->setType('factor', PARAM_TEXT);
        $mform->addElement('hidden', 'factor', $factor);

        $mform->setType('action', PARAM_TEXT);
        $mform->addElement('hidden', 'action', $action);

        $mform->addElement('hidden', 'sesskey', sesskey());
    }
}
