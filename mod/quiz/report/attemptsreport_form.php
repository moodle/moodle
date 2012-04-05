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
 * Base class for the settings form for {@link quiz_attempts_report}s.
 *
 * @package   mod_quiz
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');


/**
 * Base class for the settings form for {@link quiz_attempts_report}s.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class mod_quiz_attempts_report_form extends moodleform {

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
            $options[quiz_attempts_report::ALL_ATTEMPTS] = get_string('optallattempts', 'quiz_overview');
        }
        if ($this->_customdata['currentgroup'] ||
                !is_inside_frontpage($this->_customdata['context'])) {
            $options[quiz_attempts_report::ALL_STUDENTS] =
                    get_string('optallstudents', 'quiz_overview', $studentsstring);
            $options[quiz_attempts_report::STUDENTS_WITH] =
                     get_string('optattemptsonly', 'quiz_overview', $studentsstring);
            $options[quiz_attempts_report::STUDENTS_WITH_NO] =
                    get_string('optnoattemptsonly', 'quiz_overview', $studentsstring);
        }
        $mform->addElement('select', 'attemptsmode',
                get_string('show', 'quiz_overview'), $options);

        $this->definition_inner($mform);

        $mform->addElement('header', 'preferencesuser',
                get_string('preferencesuser', 'quiz_overview'));

        $mform->addElement('text', 'pagesize', get_string('pagesize', 'quiz_overview'));
        $mform->setType('pagesize', PARAM_INT);

        $mform->addElement('submit', 'submitbutton',
                get_string('preferencessave', 'quiz_overview'));
    }

    /**
     * Add any report-specific options to the form.
     *
     * @param MoodleQuickForm $mform the form we are building.
     */
    protected abstract function definition_inner(MoodleQuickForm $mform);

    /**
     * Create the standard checkbox for the 'include highest graded only' option.
     *
     * @param MoodleQuickForm $mform the form we are building.
     */
    protected function create_qmfilter_checkbox(MoodleQuickForm $mform) {
        $gm = html_writer::tag('span', quiz_get_grading_option_name(
                $this->_customdata['quiz']->grademethod), array('class' => 'highlight'));
        return $mform->createElement('advcheckbox', 'qmfilter',
                get_string('showattempts', 'quiz_overview'),
                get_string('optonlygradedattempts', 'quiz_overview', $gm), null, array(0, 1));
    }
}
