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

namespace mod_quiz\local\reports;

use html_writer;
use MoodleQuickForm;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Base class for the settings form for {@see attempts_report}s.
 *
 * @package   mod_quiz
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class attempts_report_options_form extends \moodleform {

    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'preferencespage',
                get_string('reportwhattoinclude', 'quiz'));

        $this->standard_attempt_fields($mform);
        $this->other_attempt_fields($mform);

        $mform->addElement('header', 'preferencesuser',
                get_string('reportdisplayoptions', 'quiz'));

        $this->standard_preference_fields($mform);
        $this->other_preference_fields($mform);

        $mform->addElement('submit', 'submitbutton',
                get_string('showreport', 'quiz'));
    }

    /**
     * Add the standard form fields for selecting which attempts to include in the report.
     *
     * @param MoodleQuickForm $mform the form we are building.
     */
    protected function standard_attempt_fields(MoodleQuickForm $mform) {

        $mform->addElement('select', 'attempts', get_string('reportattemptsfrom', 'quiz'), [
                    attempts_report::ENROLLED_WITH    => get_string('reportuserswith', 'quiz'),
                    attempts_report::ENROLLED_WITHOUT => get_string('reportuserswithout', 'quiz'),
                    attempts_report::ENROLLED_ALL     => get_string('reportuserswithorwithout', 'quiz'),
                    attempts_report::ALL_WITH         => get_string('reportusersall', 'quiz'),
        ]);

        $stategroup = [
            $mform->createElement('advcheckbox', 'stateinprogress', '',
                    get_string('stateinprogress', 'quiz')),
            $mform->createElement('advcheckbox', 'stateoverdue', '',
                    get_string('stateoverdue', 'quiz')),
            $mform->createElement('advcheckbox', 'statefinished', '',
                    get_string('statefinished', 'quiz')),
            $mform->createElement('advcheckbox', 'stateabandoned', '',
                    get_string('stateabandoned', 'quiz')),
        ];
        $mform->addGroup($stategroup, 'stateoptions',
                get_string('reportattemptsthatare', 'quiz'), [' '], false);
        $mform->setDefault('stateinprogress', 1);
        $mform->setDefault('stateoverdue',    1);
        $mform->setDefault('statefinished',   1);
        $mform->setDefault('stateabandoned',  1);
        $mform->disabledIf('stateinprogress', 'attempts', 'eq', attempts_report::ENROLLED_WITHOUT);
        $mform->disabledIf('stateoverdue',    'attempts', 'eq', attempts_report::ENROLLED_WITHOUT);
        $mform->disabledIf('statefinished',   'attempts', 'eq', attempts_report::ENROLLED_WITHOUT);
        $mform->disabledIf('stateabandoned',  'attempts', 'eq', attempts_report::ENROLLED_WITHOUT);

        if (quiz_report_can_filter_only_graded($this->_customdata['quiz'])) {
            $gm = html_writer::tag('span',
                    quiz_get_grading_option_name($this->_customdata['quiz']->grademethod),
                    ['class' => 'highlight']);
            $mform->addElement('advcheckbox', 'onlygraded', '',
                    get_string('reportshowonlyfinished', 'quiz', $gm));
            $mform->disabledIf('onlygraded', 'attempts', 'eq', attempts_report::ENROLLED_WITHOUT);
            $mform->disabledIf('onlygraded', 'statefinished', 'notchecked');
        }
    }

    /**
     * Extension point to allow subclasses to add their own fields in the attempts section.
     *
     * @param MoodleQuickForm $mform the form we are building.
     */
    protected function other_attempt_fields(MoodleQuickForm $mform) {
    }

    /**
     * Add the standard options fields to the form.
     *
     * @param MoodleQuickForm $mform the form we are building.
     */
    protected function standard_preference_fields(MoodleQuickForm $mform) {
        $mform->addElement('text', 'pagesize', get_string('pagesize', 'quiz'));
        $mform->setType('pagesize', PARAM_INT);
    }

    /**
     * Extension point to allow subclasses to add their own fields in the options section.
     *
     * @param MoodleQuickForm $mform the form we are building.
     */
    protected function other_preference_fields(MoodleQuickForm $mform) {
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($data['attempts'] != attempts_report::ENROLLED_WITHOUT && !(
                $data['stateinprogress'] || $data['stateoverdue'] || $data['statefinished'] || $data['stateabandoned'])) {
            $errors['stateoptions'] = get_string('reportmustselectstate', 'quiz');
        }

        return $errors;
    }
}
