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
 * This file contains the forms to create and edit an instance of this module
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/assign/locallib.php');

/**
 * Assignment settings form.
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_assign_mod_form extends moodleform_mod {

    /**
     * Called to define this moodle form
     *
     * @return void
     */
    public function definition() {
        global $CFG, $DB, $PAGE;
        $mform = $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('assignmentname', 'assign'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $this->add_intro_editor(true, get_string('description', 'assign'));

        $ctx = null;
        if ($this->current && $this->current->coursemodule) {
            $cm = get_coursemodule_from_instance('assign', $this->current->id, 0, false, MUST_EXIST);
            $ctx = context_module::instance($cm->id);
        }
        $assignment = new assign($ctx, null, null);
        if ($this->current && $this->current->course) {
            if (!$ctx) {
                $ctx = context_course::instance($this->current->course);
            }
            $course = $DB->get_record('course', array('id'=>$this->current->course), '*', MUST_EXIST);
            $assignment->set_course($course);
        }

        $config = get_config('assign');

        $mform->addElement('header', 'availability', get_string('availability', 'assign'));
        $mform->setExpanded('availability', true);

        $name = get_string('allowsubmissionsfromdate', 'assign');
        $options = array('optional'=>true);
        $mform->addElement('date_time_selector', 'allowsubmissionsfromdate', $name, $options);
        $mform->addHelpButton('allowsubmissionsfromdate', 'allowsubmissionsfromdate', 'assign');
        $mform->setDefault('allowsubmissionsfromdate', time());

        $name = get_string('duedate', 'assign');
        $mform->addElement('date_time_selector', 'duedate', $name, array('optional'=>true));
        $mform->addHelpButton('duedate', 'duedate', 'assign');
        $mform->setDefault('duedate', time()+7*24*3600);

        $name = get_string('cutoffdate', 'assign');
        $mform->addElement('date_time_selector', 'cutoffdate', $name, array('optional'=>true));
        $mform->addHelpButton('cutoffdate', 'cutoffdate', 'assign');

        $name = get_string('alwaysshowdescription', 'assign');
        $mform->addElement('checkbox', 'alwaysshowdescription', $name);
        $mform->addHelpButton('alwaysshowdescription', 'alwaysshowdescription', 'assign');
        $mform->setDefault('alwaysshowdescription', 1);
        $mform->disabledIf('alwaysshowdescription', 'allowsubmissionsfromdate[enabled]', 'notchecked');

        $assignment->add_all_plugin_settings($mform);

        $mform->addElement('header', 'submissionsettings', get_string('submissionsettings', 'assign'));

        $name = get_string('submissiondrafts', 'assign');
        $mform->addElement('selectyesno', 'submissiondrafts', $name);
        $mform->addHelpButton('submissiondrafts', 'submissiondrafts', 'assign');
        $mform->setDefault('submissiondrafts', 0);

        if (empty($config->submissionstatement)) {
            $mform->addElement('hidden', 'requiresubmissionstatement', 0);
        } else if (empty($config->requiresubmissionstatement)) {
            $name = get_string('requiresubmissionstatement', 'assign');
            $mform->addElement('selectyesno', 'requiresubmissionstatement', $name);
            $mform->setDefault('requiresubmissionstatement', 0);
            $mform->addHelpButton('requiresubmissionstatement',
                                  'requiresubmissionstatementassignment',
                                  'assign');
        } else {
            $mform->addElement('hidden', 'requiresubmissionstatement', 1);
        }
        $mform->setType('requiresubmissionstatement', PARAM_BOOL);

        $options = array(
            ASSIGN_ATTEMPT_REOPEN_METHOD_NONE => get_string('attemptreopenmethod_none', 'mod_assign'),
            ASSIGN_ATTEMPT_REOPEN_METHOD_MANUAL => get_string('attemptreopenmethod_manual', 'mod_assign'),
            ASSIGN_ATTEMPT_REOPEN_METHOD_UNTILPASS => get_string('attemptreopenmethod_untilpass', 'mod_assign')
        );
        $mform->addElement('select', 'attemptreopenmethod', get_string('attemptreopenmethod', 'mod_assign'), $options);
        $mform->setDefault('attemptreopenmethod', ASSIGN_ATTEMPT_REOPEN_METHOD_NONE);
        $mform->addHelpButton('attemptreopenmethod', 'attemptreopenmethod', 'mod_assign');

        $options = array(ASSIGN_UNLIMITED_ATTEMPTS => get_string('unlimitedattempts', 'mod_assign'));
        $options += array_combine(range(1, 30), range(1, 30));
        $mform->addElement('select', 'maxattempts', get_string('maxattempts', 'mod_assign'), $options);
        $mform->addHelpButton('maxattempts', 'maxattempts', 'assign');
        $mform->setDefault('maxattempts', -1);
        $mform->disabledIf('maxattempts', 'attemptreopenmethod', 'eq', ASSIGN_ATTEMPT_REOPEN_METHOD_NONE);

        $mform->addElement('header', 'groupsubmissionsettings', get_string('groupsubmissionsettings', 'assign'));

        $name = get_string('teamsubmission', 'assign');
        $mform->addElement('selectyesno', 'teamsubmission', $name);
        $mform->addHelpButton('teamsubmission', 'teamsubmission', 'assign');
        $mform->setDefault('teamsubmission', 0);

        $name = get_string('requireallteammemberssubmit', 'assign');
        $mform->addElement('selectyesno', 'requireallteammemberssubmit', $name);
        $mform->addHelpButton('requireallteammemberssubmit', 'requireallteammemberssubmit', 'assign');
        $mform->setDefault('requireallteammemberssubmit', 0);
        $mform->disabledIf('requireallteammemberssubmit', 'teamsubmission', 'eq', 0);
        $mform->disabledIf('requireallteammemberssubmit', 'submissiondrafts', 'eq', 0);

        $groupings = groups_get_all_groupings($assignment->get_course()->id);
        $options = array();
        $options[0] = get_string('none');
        foreach ($groupings as $grouping) {
            $options[$grouping->id] = $grouping->name;
        }

        $name = get_string('teamsubmissiongroupingid', 'assign');
        $mform->addElement('select', 'teamsubmissiongroupingid', $name, $options);
        $mform->addHelpButton('teamsubmissiongroupingid', 'teamsubmissiongroupingid', 'assign');
        $mform->setDefault('teamsubmissiongroupingid', 0);
        $mform->disabledIf('teamsubmissiongroupingid', 'teamsubmission', 'eq', 0);

        $mform->addElement('header', 'notifications', get_string('notifications', 'assign'));

        $name = get_string('sendnotifications', 'assign');
        $mform->addElement('selectyesno', 'sendnotifications', $name);
        $mform->addHelpButton('sendnotifications', 'sendnotifications', 'assign');
        $mform->setDefault('sendnotifications', 1);

        $name = get_string('sendlatenotifications', 'assign');
        $mform->addElement('selectyesno', 'sendlatenotifications', $name);
        $mform->addHelpButton('sendlatenotifications', 'sendlatenotifications', 'assign');
        $mform->setDefault('sendlatenotifications', 1);
        $mform->disabledIf('sendlatenotifications', 'sendnotifications', 'eq', 1);

        // Plagiarism enabling form.
        if (!empty($CFG->enableplagiarism)) {
            require_once($CFG->libdir . '/plagiarismlib.php');
            plagiarism_get_form_elements_module($mform, $ctx->get_course_context(), 'mod_assign');
        }

        $this->standard_grading_coursemodule_elements();
        $name = get_string('blindmarking', 'assign');
        $mform->addElement('selectyesno', 'blindmarking', $name);
        $mform->addHelpButton('blindmarking', 'blindmarking', 'assign');
        $mform->setDefault('blindmarking', 0);
        if ($assignment->has_submissions_or_grades() ) {
            $mform->freeze('blindmarking');
        }

        $this->standard_coursemodule_elements();

        $this->add_action_buttons();

        // Add warning popup/noscript tag, if grades are changed by user.
        $hasgrade = false;
        if (!empty($this->_instance)) {
            $hasgrade = $DB->record_exists_select('assign_grades',
                                                  'assignment = ? AND grade <> -1',
                                                  array($this->_instance));
        }

        if ($mform->elementExists('grade') && $hasgrade) {
            $module = array(
                'name' => 'mod_assign',
                'fullpath' => '/mod/assign/module.js',
                'requires' => array('node', 'event'),
                'strings' => array(array('changegradewarning', 'mod_assign'))
                );
            $PAGE->requires->js_init_call('M.mod_assign.init_grade_change', null, false, $module);

            // Add noscript tag in case.
            $noscriptwarning = $mform->createElement('static',
                                                     'warning',
                                                     null,
                                                     html_writer::tag('noscript',
                                                     get_string('changegradewarning', 'mod_assign')));
            $mform->insertElementBefore($noscriptwarning, 'grade');
        }
    }

    /**
     * Perform minimal validation on the settings form
     * @param array $data
     * @param array $files
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($data['allowsubmissionsfromdate'] && $data['duedate']) {
            if ($data['allowsubmissionsfromdate'] > $data['duedate']) {
                $errors['duedate'] = get_string('duedatevalidation', 'assign');
            }
        }
        if ($data['duedate'] && $data['cutoffdate']) {
            if ($data['duedate'] > $data['cutoffdate']) {
                $errors['cutoffdate'] = get_string('cutoffdatevalidation', 'assign');
            }
        }
        if ($data['allowsubmissionsfromdate'] && $data['cutoffdate']) {
            if ($data['allowsubmissionsfromdate'] > $data['cutoffdate']) {
                $errors['cutoffdate'] = get_string('cutoffdatefromdatevalidation', 'assign');
            }
        }

        return $errors;
    }

    /**
     * Any data processing needed before the form is displayed
     * (needed to set up draft areas for editor and filemanager elements)
     * @param array $defaultvalues
     */
    public function data_preprocessing(&$defaultvalues) {
        global $DB;

        $ctx = null;
        if ($this->current && $this->current->coursemodule) {
            $cm = get_coursemodule_from_instance('assign', $this->current->id, 0, false, MUST_EXIST);
            $ctx = context_module::instance($cm->id);
        }
        $assignment = new assign($ctx, null, null);
        if ($this->current && $this->current->course) {
            if (!$ctx) {
                $ctx = context_course::instance($this->current->course);
            }
            $course = $DB->get_record('course', array('id'=>$this->current->course), '*', MUST_EXIST);
            $assignment->set_course($course);
        }
        $assignment->plugin_data_preprocessing($defaultvalues);
    }

    public function add_completion_rules() {
        $mform =& $this->_form;

        $mform->addElement('checkbox', 'completionsubmit', '', get_string('completionsubmit', 'assign'));
        return array('completionsubmit');
    }

    public function completion_rule_enabled($data) {
        return !empty($data['completionsubmit']);
    }

}
