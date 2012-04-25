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
 * This file defines the setting form for the quiz responses report.
 *
 * @package   quiz_responses
 * @copyright 2008 Jean-Michel Vedrine
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/report/attemptsreport_form.php');


/**
 * Quiz responses report settings form.
 *
 * @copyright 2008 Jean-Michel Vedrine
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_responses_settings_form extends mod_quiz_attempts_report_form {
    protected function definition_inner(MoodleQuickForm $mform) {
        if ($this->_customdata['qmsubselect']) {
            $mform->addElement($this->create_qmfilter_checkbox($mform));
        }

        $colsgroup = array();
        $colsgroup[] = $mform->createElement('advcheckbox', 'qtext', '',
                get_string('summaryofquestiontext', 'quiz_responses'));
        $colsgroup[] = $mform->createElement('advcheckbox', 'resp', '',
                get_string('summaryofresponse', 'quiz_responses'));
        $colsgroup[] = $mform->createElement('advcheckbox', 'right', '',
                get_string('summaryofrightanswer', 'quiz_responses'));
        $mform->addGroup($colsgroup, null,
                get_string('include', 'quiz_responses'), '<br />', false);
    }
}
