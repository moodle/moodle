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
 * @package    quiz
 * @subpackage overview
 * @copyright  2008 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');


/**
 * Quiz overview report settings form.
 *
 * @copyright  2008 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_quiz_report_overview_settings extends moodleform {

    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'preferencespage',
                get_string('preferencespage', 'quiz_overview'));

        if (!$this->_customdata['currentgroup']) {
            $studentsstring = get_string('participants');
        } else {
            $a = new stdClass();
            $a->coursestudent = get_string('participants');
            $a->groupname = groups_get_group_name($this->_customdata['currentgroup']);
            if (20 < strlen($a->groupname)) {
                $studentsstring = get_string('studentingrouplong', 'quiz_overview', $a);
            } else {
                $studentsstring = get_string('studentingroup', 'quiz_overview', $a);
            }
        }
        $options = array();
        if (!$this->_customdata['currentgroup']) {
            $options[QUIZ_REPORT_ATTEMPTS_ALL] = get_string('optallattempts', 'quiz_overview');
        }
        if ($this->_customdata['currentgroup'] ||
                !is_inside_frontpage($this->_customdata['context'])) {
            $options[QUIZ_REPORT_ATTEMPTS_ALL_STUDENTS] =
                    get_string('optallstudents', 'quiz_overview', $studentsstring);
            $options[QUIZ_REPORT_ATTEMPTS_STUDENTS_WITH] =
                     get_string('optattemptsonly', 'quiz_overview', $studentsstring);
            $options[QUIZ_REPORT_ATTEMPTS_STUDENTS_WITH_NO] =
                    get_string('optnoattemptsonly', 'quiz_overview', $studentsstring);
        }
        $mform->addElement('select', 'attemptsmode', get_string('show', 'quiz_overview'), $options);

        $showattemptsgrp = array();
        if ($this->_customdata['qmsubselect']) {
            $gm = '<span class="highlight">' .
                    quiz_get_grading_option_name($this->_customdata['quiz']->grademethod) .
                    '</span>';
            $showattemptsgrp[] = $mform->createElement('advcheckbox', 'qmfilter',
                    get_string('showattempts', 'quiz_overview'),
                    get_string('optonlygradedattempts', 'quiz_overview', $gm), null, array(0, 1));
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

        $mform->addElement('header', 'preferencesuser',
                get_string('preferencesuser', 'quiz_overview'));

        $mform->addElement('text', 'pagesize', get_string('pagesize', 'quiz_overview'));
        $mform->setType('pagesize', PARAM_INT);

        $mform->addElement('selectyesno', 'detailedmarks',
                get_string('showdetailedmarks', 'quiz_overview'));

        $mform->addElement('submit', 'submitbutton',
                get_string('preferencessave', 'quiz_overview'));
    }
}
