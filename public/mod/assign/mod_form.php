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
        global $CFG, $COURSE, $OUTPUT, $PAGE;
        $mform = $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('assignmentname', 'assign'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 1333), 'maxlength', 1333, 'client');

        $this->standard_intro_elements(get_string('description', 'assign'));

        // Activity.
        $mform->addElement('editor', 'activityeditor',
             get_string('activityeditor', 'assign'), array('rows' => 10), array('maxfiles' => EDITOR_UNLIMITED_FILES,
            'noclean' => true, 'context' => $this->context, 'subdirs' => true));
        $mform->addHelpButton('activityeditor', 'activityeditor', 'assign');
        $mform->setType('activityeditor', PARAM_RAW);

        $mform->addElement('filemanager', 'introattachments',
                            get_string('introattachments', 'assign'),
                            null, array('subdirs' => 0, 'maxbytes' => $COURSE->maxbytes) );
        $mform->addHelpButton('introattachments', 'introattachments', 'assign');

        $mform->addElement('advcheckbox', 'submissionattachments', get_string('submissionattachments', 'assign'));
        $mform->addHelpButton('submissionattachments', 'submissionattachments', 'assign');

        [$assignment, $ctx] = $this->get_assign();

        $mform->addElement('header', 'availability', get_string('availability', 'assign'));
        $mform->setExpanded('availability', true);

        $name = get_string('allowsubmissionsfromdate', 'assign');
        $options = array('optional'=>true);
        $mform->addElement('date_time_selector', 'allowsubmissionsfromdate', $name, $options);
        $mform->addHelpButton('allowsubmissionsfromdate', 'allowsubmissionsfromdate', 'assign');

        // Add the option to recalculate the penalty if there is existing grade.
        $penaltysettingmessage = '';
        $gradecount = $assignment->count_grades();
        if ($assignment->has_instance()
            && \mod_assign\penalty\helper::is_penalty_enabled($assignment->get_instance()->id)
            && $gradecount > 0
        ) {
            // Create notification.
            $penaltysettingmessage = $OUTPUT->notification(get_string('penaltyduedatechangemessage', 'assign'), 'warning', false);
            $mform->addElement('html', $penaltysettingmessage);
            $mform->addElement('select', 'recalculatepenalty', get_string('modgraderecalculatepenalty', 'grades'), [
                '' => get_string('choose'),
                'no' => get_string('no'),
                'yes' => get_string('yes'),
            ]);
            $mform->addHelpButton('recalculatepenalty', 'modgraderecalculatepenalty', 'grades');
        }

        $name = get_string('duedate', 'assign');
        $mform->addElement('date_time_selector', 'duedate', $name, array('optional'=>true));
        $mform->addHelpButton('duedate', 'duedate', 'assign');
        $mform->disabledIf('duedate', 'recalculatepenalty', 'eq', '');

        $name = get_string('cutoffdate', 'assign');
        $mform->addElement('date_time_selector', 'cutoffdate', $name, array('optional'=>true));
        $mform->addHelpButton('cutoffdate', 'cutoffdate', 'assign');

        $name = get_string('gradingduedate', 'assign');
        $mform->addElement('date_time_selector', 'gradingduedate', $name, array('optional' => true));
        $mform->addHelpButton('gradingduedate', 'gradingduedate', 'assign');

        $timelimitenabled = get_config('assign', 'enabletimelimit');
        // Time limit.
        if ($timelimitenabled) {
            $mform->addElement('duration', 'timelimit', get_string('timelimit', 'assign'),
                array('optional' => true));
            $mform->addHelpButton('timelimit', 'timelimit', 'assign');
        }

        $name = get_string('alwaysshowdescription', 'assign');
        $mform->addElement('checkbox', 'alwaysshowdescription', $name);
        $mform->addHelpButton('alwaysshowdescription', 'alwaysshowdescription', 'assign');
        $mform->disabledIf('alwaysshowdescription', 'allowsubmissionsfromdate[enabled]', 'notchecked');

        $assignment->add_all_plugin_settings($mform);

        $mform->addElement('header', 'submissionsettings', get_string('submissionsettings', 'assign'));

        $name = get_string('submissiondrafts', 'assign');
        $mform->addElement('selectyesno', 'submissiondrafts', $name);
        $mform->addHelpButton('submissiondrafts', 'submissiondrafts', 'assign');
        if ($assignment->has_submissions_or_grades()) {
            $mform->freeze('submissiondrafts');
        }

        $name = get_string('requiresubmissionstatement', 'assign');
        $mform->addElement('selectyesno', 'requiresubmissionstatement', $name);
        $mform->addHelpButton('requiresubmissionstatement',
                              'requiresubmissionstatement',
                              'assign');
        $mform->setType('requiresubmissionstatement', PARAM_BOOL);

        $options = [ASSIGN_UNLIMITED_ATTEMPTS => get_string('unlimitedattempts', 'mod_assign')];
        $options += array_combine(range(1, 30), range(1, 30));
        $mform->addElement('select', 'maxattempts', get_string('maxattempts', 'mod_assign'), $options);
        $mform->addHelpButton('maxattempts', 'maxattempts', 'assign');

        $choice = new core\output\choicelist();

        $choice->add_option(
            value: ASSIGN_ATTEMPT_REOPEN_METHOD_MANUAL,
            name: get_string('attemptreopenmethod_manual', 'mod_assign'),
            definition: ['description' => get_string('attemptreopenmethod_manual_help', 'mod_assign')]
        );
        $choice->add_option(
            value: ASSIGN_ATTEMPT_REOPEN_METHOD_AUTOMATIC,
            name: get_string('attemptreopenmethod_automatic', 'mod_assign'),
            definition: ['description' => get_string('attemptreopenmethod_automatic_help', 'mod_assign')]
        );
        $choice->add_option(
            value: ASSIGN_ATTEMPT_REOPEN_METHOD_UNTILPASS,
            name: get_string('attemptreopenmethod_untilpass', 'mod_assign'),
            definition: ['description' => get_string('attemptreopenmethod_untilpass_help', 'mod_assign')]
        );

        $mform->addElement('choicedropdown', 'attemptreopenmethod', get_string('attemptreopenmethod', 'mod_assign'), $choice);
        $mform->hideIf('attemptreopenmethod', 'maxattempts', 'eq', 1);

        $mform->addElement('header', 'groupsubmissionsettings', get_string('groupsubmissionsettings', 'assign'));

        $name = get_string('teamsubmission', 'assign');
        $mform->addElement('selectyesno', 'teamsubmission', $name);
        $mform->addHelpButton('teamsubmission', 'teamsubmission', 'assign');
        if ($assignment->has_submissions_or_grades()) {
            $mform->freeze('teamsubmission');
        }

        $name = get_string('preventsubmissionnotingroup', 'assign');
        $mform->addElement('selectyesno', 'preventsubmissionnotingroup', $name);
        $mform->addHelpButton('preventsubmissionnotingroup',
            'preventsubmissionnotingroup',
            'assign');
        $mform->setType('preventsubmissionnotingroup', PARAM_BOOL);
        $mform->hideIf('preventsubmissionnotingroup', 'teamsubmission', 'eq', 0);

        $name = get_string('requireallteammemberssubmit', 'assign');
        $mform->addElement('selectyesno', 'requireallteammemberssubmit', $name);
        $mform->addHelpButton('requireallteammemberssubmit', 'requireallteammemberssubmit', 'assign');
        $mform->hideIf('requireallteammemberssubmit', 'teamsubmission', 'eq', 0);
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
        $mform->hideIf('teamsubmissiongroupingid', 'teamsubmission', 'eq', 0);
        if ($assignment->has_submissions_or_grades()) {
            $mform->freeze('teamsubmissiongroupingid');
        }

        $mform->addElement('header', 'notifications', get_string('notifications', 'assign'));

        $name = get_string('sendnotifications', 'assign');
        $mform->addElement('selectyesno', 'sendnotifications', $name);
        $mform->addHelpButton('sendnotifications', 'sendnotifications', 'assign');

        $name = get_string('sendlatenotifications', 'assign');
        $mform->addElement('selectyesno', 'sendlatenotifications', $name);
        $mform->addHelpButton('sendlatenotifications', 'sendlatenotifications', 'assign');
        $mform->disabledIf('sendlatenotifications', 'sendnotifications', 'eq', 1);

        $name = get_string('sendstudentnotificationsdefault', 'assign');
        $mform->addElement('selectyesno', 'sendstudentnotifications', $name);
        $mform->addHelpButton('sendstudentnotifications', 'sendstudentnotificationsdefault', 'assign');

        $this->standard_grading_coursemodule_elements();
        $name = get_string('blindmarking', 'assign');
        $mform->addElement('selectyesno', 'blindmarking', $name);
        $mform->addHelpButton('blindmarking', 'blindmarking', 'assign');
        if ($assignment->has_submissions_or_grades() ) {
            $mform->freeze('blindmarking');
        }

        $name = get_string('hidegrader', 'assign');
        $mform->addElement('selectyesno', 'hidegrader', $name);
        $mform->addHelpButton('hidegrader', 'hidegrader', 'assign');

        $name = get_string('markingworkflow', 'assign');
        $mform->addElement('selectyesno', 'markingworkflow', $name);
        $mform->addHelpButton('markingworkflow', 'markingworkflow', 'assign');

        $name = get_string('markingallocation', 'assign');
        $mform->addElement('selectyesno', 'markingallocation', $name);
        $mform->addHelpButton('markingallocation', 'markingallocation', 'assign');
        $mform->hideIf('markingallocation', 'markingworkflow', 'eq', 0);

        // Add hidden total marker count field for multiple marking conditions.
        $mform->addElement('hidden', 'totalmarkercount');
        $mform->setType('totalmarkercount', PARAM_INT);
        $mform->setDefault('totalmarkercount', $assignment->has_instance() ? $assignment->total_marker_count() : 1);

        $name = get_string('markercount', 'assign');
        $configmaxmarkers = get_config('assign', 'maxmarkercount');
        if ($configmaxmarkers === false) {
            $configmaxmarkers = ASSIGN_MULTIMARKING_DEFAULT_MAX_MARKERS;
        }
        // Existing values should always remain selectable if the global limit changes.
        $maxmarkers = max($configmaxmarkers, $this->current->markercount ?? 1, 1);
        $markercount = range(1, $maxmarkers);
        $mform->addElement('select', 'markercount', $name, array_combine($markercount, $markercount));
        $mform->addHelpButton('markercount', 'markercount', 'assign');
        $this->apply_multimark_conditions('markercount');

        $name = get_string('optionalmarkercount', 'assign');
        $configmaxoptionalmarkers = get_config('assign', 'maxoptionalmarkercount');
        if ($configmaxoptionalmarkers === false) {
            $configmaxoptionalmarkers = $configmaxmarkers - 1;
        }
        $maxoptionalmarkers = max($configmaxoptionalmarkers, $this->current->optionalmarkercount ?? 0);
        $optionalmarkercount = range(0, $maxoptionalmarkers);
        $mform->addElement('select', 'optionalmarkercount', $name, array_combine($optionalmarkercount, $optionalmarkercount));
        $mform->addHelpButton('optionalmarkercount', 'optionalmarkercount', 'assign');
        $this->apply_multimark_conditions('optionalmarkercount');
        if (!has_capability('mod/assign:manageoptionalallocations', $ctx)) {
            $mform->freeze('optionalmarkercount');
        }

        // Require an explicit confirmation for recalculating agreed grades.
        // Only display this when agreed grades or grades that would be converted to agreed grades exist.
        if ($gradecount > 0 && $assignment->has_agreed_grade([], false)) {
            // Show a different message when enabling multiple marking.
            $messagename = $assignment->is_using_multiple_marking() ? 'multimarkupdatemessage' : 'multimarkupdateenable';
            $message = $OUTPUT->notification(get_string($messagename, 'assign'), 'warning', false);
            $mform->addElement('static', 'multimarkupdatemessage', '', $message);
            $this->apply_multimark_conditions('multimarkupdatemessage', true);

            $actions = [
                ASSIGN_MULTIMARKING_ACTION_NONE,
                ASSIGN_MULTIMARKING_ACTION_RECALCULATE,
                ASSIGN_MULTIMARKING_ACTION_CLEAR,
            ];
            $options = new core\output\choicelist();
            $options->add_option('', get_string('choosedots'));
            foreach ($actions as $action) {
                $name = "multimarkupdate:$action";
                $options->add_option($action, get_string($name, 'assign'), ['description' => get_string("{$name}_help", 'assign')]);
            }
            $mform->addElement('choicedropdown', 'multimarkupdate', get_string('multimarkupdate', 'assign'), $options);
            $this->apply_multimark_conditions('multimarkupdate', true);
        }

        $name = get_string('multimarkmethod', 'assign');
        $options = new core\output\choicelist();
        $options->add_option(
            ASSIGN_MULTIMARKING_METHOD_MANUAL,
            get_string('markgrade' . ASSIGN_MULTIMARKING_METHOD_MANUAL, 'assign'),
            [
                'description' => get_string('markgrade' . ASSIGN_MULTIMARKING_METHOD_MANUAL . '_help', 'assign'),
            ]
        );
        $options->add_option(
            ASSIGN_MULTIMARKING_METHOD_MAX,
            get_string('markgrade' . ASSIGN_MULTIMARKING_METHOD_MAX, 'assign'),
            [
                'description' => get_string('markgrade' . ASSIGN_MULTIMARKING_METHOD_MAX . '_help', 'assign'),
            ]
        );
        $options->add_option(
            ASSIGN_MULTIMARKING_METHOD_AVERAGE,
            get_string('markgrade' . ASSIGN_MULTIMARKING_METHOD_AVERAGE, 'assign'),
            [
                'description' => get_string('markgrade' . ASSIGN_MULTIMARKING_METHOD_AVERAGE . '_help', 'assign'),
            ]
        );
        $mform->addElement('choicedropdown', 'multimarkmethod', $name, $options);
        $this->apply_multimark_conditions('multimarkmethod', true, true);

        $name = get_string('multimarkrounding', 'assign');
        $options = new core\output\choicelist();
        $options->add_option(
            ASSIGN_MULTIMARKING_AVERAGE_ROUND_NONE,
            get_string('multimarkrounding:none', 'assign'),
            [
                'description' => get_string('multimarkrounding:none_help', 'assign'),
            ]
        );
        $options->add_option(
            ASSIGN_MULTIMARKING_AVERAGE_ROUND_NATURAL,
            get_string('multimarkrounding:natural', 'assign'),
            [
                'description' => get_string('multimarkrounding:natural_help', 'assign'),
            ]
        );
        $options->add_option(
            ASSIGN_MULTIMARKING_AVERAGE_ROUND_DOWN,
            get_string('multimarkrounding:down', 'assign'),
            [
                'description' => get_string('multimarkrounding:down_help', 'assign'),
            ]
        );
        $options->add_option(
            ASSIGN_MULTIMARKING_AVERAGE_ROUND_UP,
            get_string('multimarkrounding:up', 'assign'),
            [
                'description' => get_string('multimarkrounding:up_help', 'assign'),
            ]
        );
        $mform->addElement('choicedropdown', 'multimarkrounding', $name, $options);
        $this->apply_multimark_conditions('multimarkrounding', true, true);
        $mform->hideIf('multimarkrounding', 'multimarkmethod', 'neq', 'average');

        $name = get_string('markinganonymous', 'assign');
        $mform->addElement('selectyesno', 'markinganonymous', $name);
        $mform->addHelpButton('markinganonymous', 'markinganonymous', 'assign');
        $mform->hideIf('markinganonymous', 'markingworkflow', 'eq', 0);
        $mform->hideIf('markinganonymous', 'blindmarking', 'eq', 0);

        // Add Penalty settings if the module supports it.
        if (\core_grades\penalty_manager::is_penalty_enabled_for_module('assign')) {
            // Show the message if we need to change the penalty settings.
            if (!empty($penaltysettingmessage)) {
                $mform->addElement('html', $penaltysettingmessage);
            }

            // Enable or disable the penalty settings.
            $mform->addElement('selectyesno', 'gradepenalty', get_string('gradepenalty', 'mod_assign'));
            $mform->addHelpButton('gradepenalty', 'gradepenalty', 'mod_assign');
            $mform->setDefault('gradepenalty', 0);

            // Hide if the due date is not enabled.
            $mform->hideIf('gradepenalty', 'duedate[enabled]');

            // Hide if the grade type is not set to point.
            $mform->hideIf('gradepenalty', 'grade[modgrade_type]', 'neq', 'point');

            // Disable if the recalculate penalty is not set.
            $mform->disabledIf('gradepenalty', 'recalculatepenalty', 'eq', '');
        }

        $this->standard_coursemodule_elements();
        $this->apply_admin_defaults();

        $this->add_action_buttons();

        $PAGE->requires->js_call_amd('mod_assign/mod_form', 'init');
    }

    /**
     * Applies multiple marking hide and disabled conditions.
     *
     * @param string $elementname The element to apply the condition to.
     * @param bool $markercount Apply marker count conditions.
     * @param bool $recalculate Apply recalculate conditions.
     */
    private function apply_multimark_conditions(string $elementname, bool $markercount = false, bool $recalculate = false): void {
        $mform = $this->_form;
        $mform->hideIf($elementname, 'markingworkflow', 'neq', '1');
        $mform->hideIf($elementname, 'markingallocation', 'neq', '1');
        $mform->disabledIf($elementname, 'markingallocation', 'neq', '1');
        $mform->hideIf($elementname, 'advancedgradingmethod_submissions', 'neq', '');

        if ($markercount) {
            $mform->hideIf($elementname, 'totalmarkercount', 'eq', '1');
        }

        if ($recalculate) {
            $mform->disabledIf($elementname, 'multimarkupdate', 'eq', '');
        }
    }

    /**
     * Override definition after data has been set.
     *
     * The value of date time selector will be lost in a POST request, if the selector is disabled.
     * So, we need to set the value again.
     *
     * return void
     */
    public function definition_after_data() {
        parent::definition_after_data();
        $mform = $this->_form;

        // The value of date time selector will be lost in a POST request.
        $recalculatepenalty = optional_param('recalculatepenalty', null, PARAM_TEXT);
        if ($recalculatepenalty === '') {
            $mform->setConstant('duedate', $mform->_defaultValues['duedate']);
        }
    }

    /**
     * Perform minimal validation on the settings form
     * @param array $data
     * @param array $files
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!empty($data['allowsubmissionsfromdate']) && !empty($data['duedate'])) {
            if ($data['duedate'] <= $data['allowsubmissionsfromdate']) {
                $errors['duedate'] = get_string('duedateaftersubmissionvalidation', 'assign');
            }
        }
        if (!empty($data['cutoffdate']) && !empty($data['duedate'])) {
            if ($data['cutoffdate'] < $data['duedate'] ) {
                $errors['cutoffdate'] = get_string('cutoffdatevalidation', 'assign');
            }
        }
        if (!empty($data['allowsubmissionsfromdate']) && !empty($data['cutoffdate'])) {
            if ($data['cutoffdate'] < $data['allowsubmissionsfromdate']) {
                $errors['cutoffdate'] = get_string('cutoffdatefromdatevalidation', 'assign');
            }
        }
        if ($data['gradingduedate']) {
            if ($data['allowsubmissionsfromdate'] && $data['allowsubmissionsfromdate'] > $data['gradingduedate']) {
                $errors['gradingduedate'] = get_string('gradingduefromdatevalidation', 'assign');
            }
            if ($data['duedate'] && $data['duedate'] > $data['gradingduedate']) {
                $errors['gradingduedate'] = get_string('gradingdueduedatevalidation', 'assign');
            }
        }
        $multipleattemptsallowed = $data['maxattempts'] > 1 || $data['maxattempts'] == ASSIGN_UNLIMITED_ATTEMPTS;
        if ($data['blindmarking'] && $multipleattemptsallowed &&
                $data['attemptreopenmethod'] == ASSIGN_ATTEMPT_REOPEN_METHOD_UNTILPASS) {
            $errors['attemptreopenmethod'] = get_string('reopenuntilpassincompatiblewithblindmarking', 'assign');
        }

        [$assignment] = $this->get_assign();
        $errors = array_merge($errors, $assignment->plugin_settings_validation($data, $files));

        if (!$assignment->has_instance()) {
            return $errors;
        }

        // Update only validation.
        $instance = $assignment->get_instance();
        $multimarkenabled = !empty($data['markingworkflow']) && !empty($data['markingallocation']) &&
            (($data['markercount'] ?? 1) + ($data['optionalmarkercount'] ?? 0)) > 1;
        $multimarkupdatemissing = $multimarkenabled && isset($data['multimarkupdate']) && $data['multimarkupdate'] === '';
        if (isset($data['markercount'])) {
            if (!$assignment->can_change_marker_count($data['markercount'], false)) {
                $errors['markercount'] = get_string('markercountdecreasevalidation', 'assign');
            }

            if ($multimarkupdatemissing && $data['markercount'] > $assignment->required_marker_count()) {
                $errors['multimarkupdate'] = get_string('multimarkupdatemarkervalidation', 'assign');
            }
        }

        if (isset($data['optionalmarkercount']) && !$assignment->can_change_marker_count($data['optionalmarkercount'], true)) {
            $errors['optionalmarkercount'] = get_string('optionalmarkercountdecreasevalidation', 'assign');
        }

        // Note that the comparison of defaults when multi marking is not enabled is not accurate.
        $methodchanged = isset($data['multimarkmethod']) && $data['multimarkmethod'] !== $instance->multimarkmethod;
        $roundingchanged = isset($data['multimarkrounding']) && $data['multimarkrounding'] !== $instance->multimarkrounding;
        if ($multimarkupdatemissing && ($methodchanged || $roundingchanged)) {
            $errors['multimarkupdate'] = get_string('multimarkupdatecalculatevalidation', 'assign');
        }

        return $errors;
    }

    /**
     * Any data processing needed before the form is displayed
     * (needed to set up draft areas for editor and filemanager elements)
     * @param array $defaultvalues
     */
    public function data_preprocessing(&$defaultvalues) {
        [$assignment, $ctx] = $this->get_assign();

        $draftitemid = file_get_submitted_draft_itemid('introattachments');
        file_prepare_draft_area($draftitemid, $ctx->id, 'mod_assign', ASSIGN_INTROATTACHMENT_FILEAREA,
                                0, array('subdirs' => 0));
        $defaultvalues['introattachments'] = $draftitemid;

        // Activity editor fields.
        $activitydraftitemid = file_get_submitted_draft_itemid('activityeditor');
        if (!empty($defaultvalues['activity'])) {
            $defaultvalues['activityeditor'] = array(
                'text' => file_prepare_draft_area($activitydraftitemid, $ctx->id, 'mod_assign', ASSIGN_ACTIVITYATTACHMENT_FILEAREA,
                    0, array('subdirs' => 0), $defaultvalues['activity']),
                'format' => $defaultvalues['activityformat'],
                'itemid' => $activitydraftitemid
            );
        }

        $assignment->plugin_data_preprocessing($defaultvalues);
    }

    /**
     * Add any custom completion rules to the form.
     *
     * @return array Contains the names of the added form elements
     */
    public function add_completion_rules() {
        $mform =& $this->_form;

        $suffix = $this->get_suffix();
        $completionsubmitel = 'completionsubmit' . $suffix;
        $mform->addElement('advcheckbox', $completionsubmitel, '', get_string('completionsubmit', 'assign'));
        // Enable this completion rule by default.
        $mform->setDefault($completionsubmitel, 1);

        return [$completionsubmitel];
    }

    /**
     * Determines if completion is enabled for this module.
     *
     * @param array $data
     * @return bool
     */
    public function completion_rule_enabled($data) {
        $suffix = $this->get_suffix();
        return !empty($data['completionsubmit' . $suffix]);
    }

    /**
     * Get the list of admin settings for this module and apply any defaults/advanced/locked/required settings.
     *
     * @param array $datetimeoffsets  - If passed, this is an array of fieldnames => times that the
     *                          default date/time value should be relative to. If not passed, all
     *                          date/time fields are set relative to the users current midnight.
     * @return void
     */
    public function apply_admin_defaults($datetimeoffsets = []): void {
        parent::apply_admin_defaults($datetimeoffsets);

        $isupdate = !empty($this->_cm);
        if ($isupdate) {
            return;
        }

        $settings = get_config('mod_assign');
        $mform = $this->_form;

        if ($mform->elementExists('grade')) {
            $element = $mform->getElement('grade');

            if (property_exists($settings, 'defaultgradetype')) {
                $modgradetype = $element->getName() . '[modgrade_type]';
                switch ((int)$settings->defaultgradetype) {
                    case GRADE_TYPE_NONE :
                        $mform->setDefault($modgradetype, 'none');
                        break;
                    case GRADE_TYPE_SCALE :
                        $mform->setDefault($modgradetype, 'scale');
                        break;
                    case GRADE_TYPE_VALUE :
                        $mform->setDefault($modgradetype, 'point');
                        break;
                }
            }

            if (property_exists($settings, 'defaultgradescale')) {
                /** @var grade_scale|false $gradescale */
                $gradescale = grade_scale::fetch(['id' => (int)$settings->defaultgradescale, 'courseid' => 0]);

                if ($gradescale) {
                    $mform->setDefault($element->getName() . '[modgrade_scale]', $gradescale->id);
                }
            }
        }
    }

    /**
     * Get a relevant assign instance for this form, and the context.
     *
     * If we are editing an existing assign, it is that assignment and context, otherwise it is for the course context.
     *
     * @return array [$assignment, $ctx] the assignment object and the context.
     */
    protected function get_assign(): array {
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
            $course = $DB->get_record('course', ['id' => $this->current->course], '*', MUST_EXIST);
            $assignment->set_course($course);
        }
        return [$assignment, $ctx];
    }
}
