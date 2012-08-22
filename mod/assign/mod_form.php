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


/** Include moodleform_mod.php */
require_once ($CFG->dirroot.'/course/moodleform_mod.php');
/** Include locallib.php */
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
    function definition() {
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
            $assignment->set_course($DB->get_record('course', array('id'=>$this->current->course), '*', MUST_EXIST));
        }

        $mform->addElement('header', 'general', get_string('settings', 'assign'));
        $mform->addElement('date_time_selector', 'allowsubmissionsfromdate', get_string('allowsubmissionsfromdate', 'assign'), array('optional'=>true));
        $mform->addHelpButton('allowsubmissionsfromdate', 'allowsubmissionsfromdate', 'assign');
        $mform->setDefault('allowsubmissionsfromdate', time());
        $mform->addElement('date_time_selector', 'duedate', get_string('duedate', 'assign'), array('optional'=>true));
        $mform->addHelpButton('duedate', 'duedate', 'assign');
        $mform->setDefault('duedate', time()+7*24*3600);
        $mform->addElement('selectyesno', 'alwaysshowdescription', get_string('alwaysshowdescription', 'assign'));
        $mform->addHelpButton('alwaysshowdescription', 'alwaysshowdescription', 'assign');
        $mform->setDefault('alwaysshowdescription', 1);
        $mform->addElement('selectyesno', 'preventlatesubmissions', get_string('preventlatesubmissions', 'assign'));
        $mform->addHelpButton('preventlatesubmissions', 'preventlatesubmissions', 'assign');
        $mform->setDefault('preventlatesubmissions', 0);
        $mform->addElement('selectyesno', 'submissiondrafts', get_string('submissiondrafts', 'assign'));
        $mform->addHelpButton('submissiondrafts', 'submissiondrafts', 'assign');
        $mform->setDefault('submissiondrafts', 0);
        $mform->addElement('selectyesno', 'sendnotifications', get_string('sendnotifications', 'assign'));
        $mform->addHelpButton('sendnotifications', 'sendnotifications', 'assign');
        $mform->setDefault('sendnotifications', 1);
        $mform->addElement('selectyesno', 'sendlatenotifications', get_string('sendlatenotifications', 'assign'));
        $mform->addHelpButton('sendlatenotifications', 'sendlatenotifications', 'assign');
        $mform->setDefault('sendlatenotifications', 1);
        $mform->disabledIf('sendlatenotifications', 'sendnotifications', 'eq', 1);

        // plagiarism enabling form
        if (!empty($CFG->enableplagiarism)) {
            /** Include plagiarismlib.php */
            require_once($CFG->libdir . '/plagiarismlib.php');
            plagiarism_get_form_elements_module($mform, $ctx->get_course_context());
        }

        $assignment->add_all_plugin_settings($mform);
        $this->standard_grading_coursemodule_elements();
        $this->standard_coursemodule_elements();

        $this->add_action_buttons();

        // Add warning popup/noscript tag, if grades are changed by user.
        if ($mform->elementExists('grade') && !empty($this->_instance) && $DB->record_exists_select('assign_grades', 'assignment = ? AND grade <> -1', array($this->_instance))) {
            $module = array(
                'name' => 'mod_assign',
                'fullpath' => '/mod/assign/module.js',
                'requires' => array('node', 'event'),
                'strings' => array(array('changegradewarning', 'mod_assign'))
                );
            $PAGE->requires->js_init_call('M.mod_assign.init_grade_change', null, false, $module);

            // Add noscript tag in case
            $noscriptwarning = $mform->createElement('static', 'warning', null,  html_writer::tag('noscript', get_string('changegradewarning', 'mod_assign')));
            $mform->insertElementBefore($noscriptwarning, 'grade');
        }
    }

    /**
     * Perform minimal validation on the settings form
     * @param array $data
     * @param array $files
     */
    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($data['allowsubmissionsfromdate'] && $data['duedate']) {
            if ($data['allowsubmissionsfromdate'] > $data['duedate']) {
                $errors['duedate'] = get_string('duedatevalidation', 'assign');
            }
        }
        return $errors;
    }

    /**
     * Any data processing needed before the form is displayed
     * (needed to set up draft areas for editor and filemanager elements)
     * @param array $defaultvalues
     */
    function data_preprocessing(&$defaultvalues) {
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
            $assignment->set_course($DB->get_record('course', array('id'=>$this->current->course), '*', MUST_EXIST));
        }
        $assignment->plugin_data_preprocessing($defaultvalues);
    }


}
