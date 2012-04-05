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
 * This file defines the setting form for the quiz overview report.
 *
 * @package   quiz_overview
 * @copyright 2008 Jamie Pratt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/report/attemptsreport_form.php');


/**
 * Quiz overview report settings form.
 *
 * @copyright 2008 Jamie Pratt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_overview_settings_form extends mod_quiz_attempts_report_form {
    protected function definition_inner(MoodleQuickForm $mform) {
        $showattemptsgrp = array();
        if ($this->_customdata['qmsubselect']) {
            $showattemptsgrp[] = $this->create_qmfilter_checkbox($mform);
        }
        if (has_capability('mod/quiz:regrade', $this->_customdata['context'])) {
            $showattemptsgrp[] = $mform->createElement('advcheckbox', 'regradefilter',
                    get_string('showattempts', 'quiz_overview'),
                    get_string('optonlyregradedattempts', 'quiz_overview'), null, array(0, 1));
        }
        if ($showattemptsgrp) {
            $mform->addGroup($showattemptsgrp, null,
                    get_string('showattempts', 'quiz_overview'), '<br />', false);
        }

        $mform->addElement('selectyesno', 'detailedmarks',
                get_string('showdetailedmarks', 'quiz_overview'));
    }
}
