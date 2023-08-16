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
 * @package   mod_qbassign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/qbassign/locallib.php');

/**
 * qbassignment settings form.
 *
 * @package   mod_qbassign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_qbassign_mod_form extends moodleform_mod {

    /**
     * Called to define this moodle form
     *
     * @return void
     */
    public function definition() {
        global $CFG, $COURSE, $DB, $PAGE;
        $mform = $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('qbassignmentname', 'qbassign'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $this->standard_intro_elements(get_string('description', 'qbassign'));

        // Activity.
        $mform->addElement('editor', 'activityeditor',
             get_string('activityeditor', 'qbassign'), array('rows' => 10), array('maxfiles' => EDITOR_UNLIMITED_FILES,
            'noclean' => true, 'context' => $this->context, 'subdirs' => true));
        $mform->addHelpButton('activityeditor', 'activityeditor', 'qbassign');
        $mform->setType('activityeditor', PARAM_RAW);

        $mform->addElement('filemanager', 'introattachments',
                            get_string('introattachments', 'qbassign'),
                            null, array('subdirs' => 0, 'maxbytes' => $COURSE->maxbytes) );
        $mform->addHelpButton('introattachments', 'introattachments', 'qbassign');

        $mform->addElement('advcheckbox', 'submissionattachments', get_string('submissionattachments', 'qbassign'));
        $mform->addHelpButton('submissionattachments', 'submissionattachments', 'qbassign');

        $ctx = null;
        if ($this->current && $this->current->coursemodule) {
            $cm = get_coursemodule_from_instance('qbassign', $this->current->id, 0, false, MUST_EXIST);
            $ctx = context_module::instance($cm->id);
        }
        $qbassignment = new qbassign($ctx, null, null);
        if ($this->current && $this->current->course) {
            if (!$ctx) {
                $ctx = context_course::instance($this->current->course);
            }
            $course = $DB->get_record('course', array('id'=>$this->current->course), '*', MUST_EXIST);
            $qbassignment->set_course($course);
        }

        $mform->addElement('header', 'availability', get_string('availability', 'qbassign'));
        $mform->setExpanded('availability', true);

        $name = get_string('allowsubmissionsfromdate', 'qbassign');
        $options = array('optional'=>true);
        $mform->addElement('date_time_selector', 'allowsubmissionsfromdate', $name, $options);
        $mform->addHelpButton('allowsubmissionsfromdate', 'allowsubmissionsfromdate', 'qbassign');

        $name = get_string('duedate', 'qbassign');
        $mform->addElement('date_time_selector', 'duedate', $name, array('optional'=>true));
        $mform->addHelpButton('duedate', 'duedate', 'qbassign');

        $name = get_string('cutoffdate', 'qbassign');
        $mform->addElement('date_time_selector', 'cutoffdate', $name, array('optional'=>true));
        $mform->addHelpButton('cutoffdate', 'cutoffdate', 'qbassign');

        $name = get_string('gradingduedate', 'qbassign');
        $mform->addElement('date_time_selector', 'gradingduedate', $name, array('optional' => true));
        $mform->addHelpButton('gradingduedate', 'gradingduedate', 'qbassign');

        $timelimitenabled = get_config('qbassign', 'enabletimelimit');
        // Time limit.
        if ($timelimitenabled) {
            $mform->addElement('duration', 'timelimit', get_string('timelimit', 'qbassign'),
                array('optional' => true));
            $mform->addHelpButton('timelimit', 'timelimit', 'qbassign');
        }

        $name = get_string('alwaysshowdescription', 'qbassign');
        $mform->addElement('checkbox', 'alwaysshowdescription', $name);
        $mform->addHelpButton('alwaysshowdescription', 'alwaysshowdescription', 'qbassign');
        $mform->disabledIf('alwaysshowdescription', 'allowsubmissionsfromdate[enabled]', 'notchecked');

        $qbassignment->add_all_plugin_settings($mform);

        $mform->addElement('header', 'submissionsettings', get_string('submissionsettings', 'qbassign'));

        $name = get_string('submissiondrafts', 'qbassign');
        $mform->addElement('selectyesno', 'submissiondrafts', $name);
        $mform->addHelpButton('submissiondrafts', 'submissiondrafts', 'qbassign');
        if ($qbassignment->has_submissions_or_grades()) {
            $mform->freeze('submissiondrafts');
        }

        $name = get_string('requiresubmissionstatement', 'qbassign');
        $mform->addElement('selectyesno', 'requiresubmissionstatement', $name);
        $mform->addHelpButton('requiresubmissionstatement',
                              'requiresubmissionstatement',
                              'qbassign');
        $mform->setType('requiresubmissionstatement', PARAM_BOOL);

        $options = array(
            qbassign_ATTEMPT_REOPEN_METHOD_NONE => get_string('attemptreopenmethod_none', 'mod_qbassign'),
            qbassign_ATTEMPT_REOPEN_METHOD_MANUAL => get_string('attemptreopenmethod_manual', 'mod_qbassign'),
            qbassign_ATTEMPT_REOPEN_METHOD_UNTILPASS => get_string('attemptreopenmethod_untilpass', 'mod_qbassign')
        );
        $mform->addElement('select', 'attemptreopenmethod', get_string('attemptreopenmethod', 'mod_qbassign'), $options);
        $mform->addHelpButton('attemptreopenmethod', 'attemptreopenmethod', 'mod_qbassign');

        $options = array(qbassign_UNLIMITED_ATTEMPTS => get_string('unlimitedattempts', 'mod_qbassign'));
        $options += array_combine(range(1, 30), range(1, 30));
        $mform->addElement('select', 'maxattempts', get_string('maxattempts', 'mod_qbassign'), $options);
        $mform->addHelpButton('maxattempts', 'maxattempts', 'qbassign');
        $mform->hideIf('maxattempts', 'attemptreopenmethod', 'eq', qbassign_ATTEMPT_REOPEN_METHOD_NONE);

        $mform->addElement('header', 'groupsubmissionsettings', get_string('groupsubmissionsettings', 'qbassign'));

        $name = get_string('teamsubmission', 'qbassign');
        $mform->addElement('selectyesno', 'teamsubmission', $name);
        $mform->addHelpButton('teamsubmission', 'teamsubmission', 'qbassign');
        if ($qbassignment->has_submissions_or_grades()) {
            $mform->freeze('teamsubmission');
        }

        $name = get_string('preventsubmissionnotingroup', 'qbassign');
        $mform->addElement('selectyesno', 'preventsubmissionnotingroup', $name);
        $mform->addHelpButton('preventsubmissionnotingroup',
            'preventsubmissionnotingroup',
            'qbassign');
        $mform->setType('preventsubmissionnotingroup', PARAM_BOOL);
        $mform->hideIf('preventsubmissionnotingroup', 'teamsubmission', 'eq', 0);

        $name = get_string('requireallteammemberssubmit', 'qbassign');
        $mform->addElement('selectyesno', 'requireallteammemberssubmit', $name);
        $mform->addHelpButton('requireallteammemberssubmit', 'requireallteammemberssubmit', 'qbassign');
        $mform->hideIf('requireallteammemberssubmit', 'teamsubmission', 'eq', 0);
        $mform->disabledIf('requireallteammemberssubmit', 'submissiondrafts', 'eq', 0);

        $groupings = groups_get_all_groupings($qbassignment->get_course()->id);
        $options = array();
        $options[0] = get_string('none');
        foreach ($groupings as $grouping) {
            $options[$grouping->id] = $grouping->name;
        }

        $name = get_string('teamsubmissiongroupingid', 'qbassign');
        $mform->addElement('select', 'teamsubmissiongroupingid', $name, $options);
        $mform->addHelpButton('teamsubmissiongroupingid', 'teamsubmissiongroupingid', 'qbassign');
        $mform->hideIf('teamsubmissiongroupingid', 'teamsubmission', 'eq', 0);
        if ($qbassignment->has_submissions_or_grades()) {
            $mform->freeze('teamsubmissiongroupingid');
        }

        $mform->addElement('header', 'notifications', get_string('notifications', 'qbassign'));

        $name = get_string('sendnotifications', 'qbassign');
        $mform->addElement('selectyesno', 'sendnotifications', $name);
        $mform->addHelpButton('sendnotifications', 'sendnotifications', 'qbassign');

        $name = get_string('sendlatenotifications', 'qbassign');
        $mform->addElement('selectyesno', 'sendlatenotifications', $name);
        $mform->addHelpButton('sendlatenotifications', 'sendlatenotifications', 'qbassign');
        $mform->disabledIf('sendlatenotifications', 'sendnotifications', 'eq', 1);

        $name = get_string('sendstudentnotificationsdefault', 'qbassign');
        $mform->addElement('selectyesno', 'sendstudentnotifications', $name);
        $mform->addHelpButton('sendstudentnotifications', 'sendstudentnotificationsdefault', 'qbassign');

        // Plagiarism enabling form. To be removed (deprecated) with MDL-67526.
        if (!empty($CFG->enableplagiarism)) {
            require_once($CFG->libdir . '/plagiarismlib.php');
            plagiarism_get_form_elements_module($mform, $ctx->get_course_context(), 'mod_qbassign');
        }

        $this->standard_grading_coursemodule_elements();
        $name = get_string('blindmarking', 'qbassign');
        $mform->addElement('selectyesno', 'blindmarking', $name);
        $mform->addHelpButton('blindmarking', 'blindmarking', 'qbassign');
        if ($qbassignment->has_submissions_or_grades() ) {
            $mform->freeze('blindmarking');
        }

        $name = get_string('hidegrader', 'qbassign');
        $mform->addElement('selectyesno', 'hidegrader', $name);
        $mform->addHelpButton('hidegrader', 'hidegrader', 'qbassign');

        $name = get_string('markingworkflow', 'qbassign');
        $mform->addElement('selectyesno', 'markingworkflow', $name);
        $mform->addHelpButton('markingworkflow', 'markingworkflow', 'qbassign');

        $name = get_string('markingallocation', 'qbassign');
        $mform->addElement('selectyesno', 'markingallocation', $name);
        $mform->addHelpButton('markingallocation', 'markingallocation', 'qbassign');
        $mform->hideIf('markingallocation', 'markingworkflow', 'eq', 0);

        $this->standard_coursemodule_elements();
        $this->apply_admin_defaults();

        $this->add_action_buttons();
    }

    /**
     * Perform minimal validation on the settings form
     * @param array $data
     * @param array $files
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!empty($data['allowsubmissionsfromdate']) && !empty($data['duedate'])) {
            if ($data['duedate'] < $data['allowsubmissionsfromdate']) {
                $errors['duedate'] = get_string('duedatevalidation', 'qbassign');
            }
        }
        if (!empty($data['cutoffdate']) && !empty($data['duedate'])) {
            if ($data['cutoffdate'] < $data['duedate'] ) {
                $errors['cutoffdate'] = get_string('cutoffdatevalidation', 'qbassign');
            }
        }
        if (!empty($data['allowsubmissionsfromdate']) && !empty($data['cutoffdate'])) {
            if ($data['cutoffdate'] < $data['allowsubmissionsfromdate']) {
                $errors['cutoffdate'] = get_string('cutoffdatefromdatevalidation', 'qbassign');
            }
        }
        if ($data['gradingduedate']) {
            if ($data['allowsubmissionsfromdate'] && $data['allowsubmissionsfromdate'] > $data['gradingduedate']) {
                $errors['gradingduedate'] = get_string('gradingduefromdatevalidation', 'qbassign');
            }
            if ($data['duedate'] && $data['duedate'] > $data['gradingduedate']) {
                $errors['gradingduedate'] = get_string('gradingdueduedatevalidation', 'qbassign');
            }
        }
        if ($data['blindmarking'] && $data['attemptreopenmethod'] == qbassign_ATTEMPT_REOPEN_METHOD_UNTILPASS) {
            $errors['attemptreopenmethod'] = get_string('reopenuntilpassincompatiblewithblindmarking', 'qbassign');
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
            $cm = get_coursemodule_from_instance('qbassign', $this->current->id, 0, false, MUST_EXIST);
            $ctx = context_module::instance($cm->id);
        }
        $qbassignment = new qbassign($ctx, null, null);
        if ($this->current && $this->current->course) {
            if (!$ctx) {
                $ctx = context_course::instance($this->current->course);
            }
            $course = $DB->get_record('course', array('id'=>$this->current->course), '*', MUST_EXIST);
            $qbassignment->set_course($course);
        }

        $draftitemid = file_get_submitted_draft_itemid('introattachments');
        file_prepare_draft_area($draftitemid, $ctx->id, 'mod_qbassign', qbassign_INTROATTACHMENT_FILEAREA,
                                0, array('subdirs' => 0));
        $defaultvalues['introattachments'] = $draftitemid;

        // Activity editor fields.
        $activitydraftitemid = file_get_submitted_draft_itemid('activityeditor');
        if (!empty($defaultvalues['activity'])) {
            $defaultvalues['activityeditor'] = array(
                'text' => file_prepare_draft_area($activitydraftitemid, $ctx->id, 'mod_qbassign', qbassign_ACTIVITYATTACHMENT_FILEAREA,
                    0, array('subdirs' => 0), $defaultvalues['activity']),
                'format' => $defaultvalues['activityformat'],
                'itemid' => $activitydraftitemid
            );
        }

        $qbassignment->plugin_data_preprocessing($defaultvalues);
    }

    /**
     * Add any custom completion rules to the form.
     *
     * @return array Contains the names of the added form elements
     */
    public function add_completion_rules() {
        $mform =& $this->_form;

        $mform->addElement('advcheckbox', 'completionsubmit', '', get_string('completionsubmit', 'qbassign'));
        // Enable this completion rule by default.
        $mform->setDefault('completionsubmit', 1);
        return array('completionsubmit');
    }

    /**
     * Determines if completion is enabled for this module.
     *
     * @param array $data
     * @return bool
     */
    public function completion_rule_enabled($data) {
        return !empty($data['completionsubmit']);
    }

}
