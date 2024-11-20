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

use mod_quiz\local\reports\attempts_report;
use mod_quiz\local\reports\attempts_report_options_form;

/**
 * Quiz overview report settings form.
 *
 * @copyright 2008 Jamie Pratt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_overview_settings_form extends attempts_report_options_form {

    protected function other_attempt_fields(MoodleQuickForm $mform) {
        if (has_capability('mod/quiz:regrade', $this->_customdata['context'])) {
            $mform->addElement('advcheckbox', 'onlyregraded', get_string('reportshowonly', 'quiz'),
                    get_string('optonlyregradedattempts', 'quiz_overview'));
            $mform->disabledIf('onlyregraded', 'attempts', 'eq', attempts_report::ENROLLED_WITHOUT);
        }
    }

    protected function other_preference_fields(MoodleQuickForm $mform) {
        if (quiz_has_grades($this->_customdata['quiz'])) {
            $mform->addElement('selectyesno', 'slotmarks',
                    get_string('showdetailedmarks', 'quiz_overview'));
        } else {
            $mform->addElement('hidden', 'slotmarks', 0);
            $mform->setType('slotmarks', PARAM_INT);
        }
    }
}
