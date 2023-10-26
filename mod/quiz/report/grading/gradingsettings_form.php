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
 * This file defines the setting form for the quiz grading report.
 *
 * @package   quiz_grading
 * @copyright 2010 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');


/**
 * Quiz grading report settings form.
 *
 * @copyright 2010 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_grading_settings_form extends moodleform {
    protected $includeauto;
    protected $hidden = array();
    protected $counts;
    protected $shownames;

    /** @var bool $showcustomfields whether custom field values should be shown. */
    protected $showcustomfields;

    /** @var stdClass $context the quiz context. */
    protected $context;

    /**
     * quiz_grading_settings_form constructor.
     *
     * @param array $hidden Array of options form.
     * @param stdClass $counts object that stores the number of each type of attempt.
     * @param bool $shownames whether student names should be shown.
     * @param bool $showcustomfields whether custom field values should be shown.
     * @param stdClass $context context object.
     */
    public function __construct(array $hidden, stdClass $counts, bool $shownames, bool $showcustomfields, stdClass $context) {
        global $CFG;
        $this->includeauto = !empty($hidden['includeauto']);
        $this->hidden = $hidden;
        $this->counts = $counts;
        $this->shownames = $shownames;
        $this->showcustomfields = $showcustomfields;
        $this->context = $context;
        parent::__construct($CFG->wwwroot . '/mod/quiz/report.php');
    }

    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'options', get_string('options', 'quiz_grading'));

        $gradeoptions = array();
        foreach (array('needsgrading', 'manuallygraded', 'autograded', 'all') as $type) {
            if (empty($this->counts->$type)) {
                continue;
            }
            if ($type == 'autograded' && !$this->includeauto) {
                continue;
            }
            $gradeoptions[$type] = get_string('gradeattempts' . $type, 'quiz_grading',
                    $this->counts->$type);
        }
        $mform->addElement('select', 'grade', get_string('attemptstograde', 'quiz_grading'),
                $gradeoptions);

        $mform->addElement('text', 'pagesize', get_string('questionsperpage', 'quiz_grading'),
                array('size' => 3));
        $mform->addRule('pagesize', null, 'positiveint', null, 'client');
        $mform->setType('pagesize', PARAM_INT);

        $orderoptions = [
            'random' => get_string('random', 'quiz_grading'),
            'date' => get_string('date')
        ];
        if ($this->shownames) {
            $orderoptions['studentfirstname'] = get_string('firstname');
            $orderoptions['studentlastname']  = get_string('lastname');
        }
        // If the current user can see custom user fields, add the custom user fields to the select menu.
        if ($this->showcustomfields) {
            $userfieldsapi = \core_user\fields::for_identity($this->context);
            foreach ($userfieldsapi->get_required_fields([\core_user\fields::PURPOSE_IDENTITY]) as $field) {
                $orderoptions[s($field)] = \core_user\fields::get_display_name(s($field));
            }
        }
        $mform->addElement('select', 'order', get_string('orderattemptsby', 'quiz_grading'),
                $orderoptions);

        foreach ($this->hidden as $name => $value) {
            $mform->addElement('hidden', $name, $value);
            if ($name == 'mode') {
                $mform->setType($name, PARAM_ALPHA);
            } else {
                $mform->setType($name, PARAM_INT);
            }
        }

        $mform->addElement('submit', 'submitbutton', get_string('changeoptions', 'quiz_grading'));
    }
}
