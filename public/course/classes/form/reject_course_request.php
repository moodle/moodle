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

namespace core_course\form;

use moodleform;

defined('MOODLE_INTERNAL') || die();

require_once("{$CFG->libdir}/formslib.php");

/**
 * A form for an administrator to reject a course request.
 *
 * @package    core_course
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reject_course_request extends moodleform {
    #[\Override]
    public function definition() {
        $mform =& $this->_form;

        $mform->addElement('hidden', 'reject', 0);
        $mform->setType('reject', PARAM_INT);

        $mform->addElement('header', 'coursedetails', get_string('coursereasonforrejecting'));

        $mform->addElement(
            'textarea',
            'rejectnotice',
            get_string('coursereasonforrejectingemail'),
            ['rows' => '15', 'cols' => '50'],
        );
        $mform->addRule('rejectnotice', get_string('missingreqreason'), 'required', null, 'client');
        $mform->setType('rejectnotice', PARAM_TEXT);

        $this->add_action_buttons(true, get_string('reject'));
    }
}
