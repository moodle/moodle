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
 * Revoke factor form
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class revoke_factor_form extends \moodleform {

    /**
     * {@inheritDoc}
     * @see moodleform::definition()
     */
    public function definition(): void {
        global $OUTPUT;
        $mform = $this->_form;
        $factorname = $this->_customdata['factorname'];
        $devicename = $this->_customdata['devicename'];

        $mform->addElement('html', $OUTPUT->heading(get_string('areyousure', 'tool_mfa'), 4));
        $mform->addElement('html', $OUTPUT->heading(get_string('factor', 'tool_mfa').': '.$factorname, 5));
        $mform->addElement('html', $OUTPUT->heading(get_string('devicename', 'tool_mfa').': '.$devicename, 5));

        $this->add_action_buttons(true, get_string('revoke', 'tool_mfa'));
    }
}
