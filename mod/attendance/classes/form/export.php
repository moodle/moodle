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
 * Export attendance sessions forms
 *
 * @package   mod_attendance
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_attendance\form;

defined('MOODLE_INTERNAL') || die();

/**
 * class for displaying export form.
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class export extends \moodleform {

    /**
     * Called to define this moodle form
     *
     * @return void
     */
    public function definition() {
        global $USER, $DB, $PAGE, $CFG;
        $mform    =& $this->_form;
        $course        = $this->_customdata['course'];
        $cm            = $this->_customdata['cm'];
        $modcontext    = $this->_customdata['modcontext'];

        $mform->addElement('header', 'general', get_string('export', 'attendance'));

        $groupmode = groups_get_activity_groupmode($cm, $course);
        $groups = groups_get_activity_allowed_groups($cm, $USER->id);
        if ($groupmode == VISIBLEGROUPS or has_capability('moodle/site:accessallgroups', $modcontext)) {
            $grouplist[0] = get_string('allparticipants');
        }
        if ($groups) {
            foreach ($groups as $group) {
                $grouplist[$group->id] = $group->name;
            }
        }

        // Restrict the export to the selected users.
        $namefields = get_all_user_name_fields(true, 'u');
        $allusers = get_enrolled_users($modcontext, 'mod/attendance:canbelisted', 0, 'u.id,'.$namefields);
        $userlist = array();
        foreach ($allusers as $user) {
            $userlist[$user->id] = fullname($user);
        }
        unset($allusers);
        $tempusers = $DB->get_records('attendance_tempusers', array('courseid' => $course->id), 'studentid, fullname');
        foreach ($tempusers as $user) {
            $userlist[$user->studentid] = $user->fullname;
        }
        if (empty($userlist)) {
            $mform->addElement('static', 'nousers', '', get_string('noattendanceusers', 'attendance'));
            return;
        }

        list($gsql, $gparams) = $DB->get_in_or_equal(array_keys($grouplist), SQL_PARAMS_NAMED);
        list($usql, $uparams) = $DB->get_in_or_equal(array_keys($userlist), SQL_PARAMS_NAMED);
        $params = array_merge($gparams, $uparams);
        $groupmembers = $DB->get_recordset_select('groups_members', "groupid {$gsql} AND userid {$usql}", $params,
                                                  '', 'groupid, userid');
        $groupmappings = array();
        foreach ($groupmembers as $groupmember) {
            if (!isset($groupmappings[$groupmember->groupid])) {
                $groupmappings[$groupmember->groupid] = array();
            }
            $groupmappings[$groupmember->groupid][$groupmember->userid] = $userlist[$groupmember->userid];
        }
        if (isset($grouplist[0])) {
            $groupmappings[0] = $userlist;
        }

        $mform->addElement('select', 'group', get_string('group'), $grouplist);

        $mform->addElement('selectyesno', 'selectedusers', get_string('onlyselectedusers', 'mod_attendance'));
        $sel = $mform->addElement('select', 'users', get_string('users', 'mod_attendance'), $userlist, array('size' => 12));
        $sel->setMultiple(true);
        $mform->disabledIf('users', 'selectedusers', 'eq', 0);

        $opts = array('groupmappings' => $groupmappings);
        $PAGE->requires->yui_module('moodle-mod_attendance-groupfilter', 'M.mod_attendance.groupfilter.init', array($opts));

        $ident = array();
        $checkedfields = array();

        $adminsetfields = get_config('attendance', 'customexportfields');
        if (in_array('id', explode(',', $adminsetfields))) {
            $ident[] =& $mform->createElement('checkbox', 'id', '', get_string('studentid', 'attendance'));
            $checkedfields['ident[id]'] = true;
        }

        $extrafields = get_extra_user_fields($modcontext);
        foreach ($extrafields as $field) {
            $ident[] =& $mform->createElement('checkbox',  $field, '', get_string( $field));
            $mform->setType($field, PARAM_NOTAGS);
            $checkedfields['ident['. $field .']'] = true;
        }

        require_once($CFG->dirroot . '/user/profile/lib.php');
        $customfields = profile_get_custom_fields();

        foreach ($customfields as $field) {
            if ((is_siteadmin($USER) || $field->visible == PROFILE_VISIBLE_ALL || $field->visible == PROFILE_VISIBLE_TEACHERS)
            && in_array($field->shortname, explode(',', $adminsetfields))) {
                $ident[] =& $mform->createElement('checkbox', $field->shortname, '',
                    format_string($field->name, true, array('context' => $modcontext)));
                $mform->setType($field->shortname, PARAM_NOTAGS);
                $checkedfields['ident['. $field->shortname .']'] = true;
            }
        }

        if (count($ident) > 0) {
            $mform->addGroup($ident, 'ident', get_string('identifyby', 'attendance'), array('<br />'), true);
            $mform->setDefaults($checkedfields);
        }
        $mform->setType('id', PARAM_INT);

        $mform->addElement('checkbox', 'includeallsessions', get_string('includeall', 'attendance'), get_string('yes'));
        $mform->setDefault('includeallsessions', true);
        $mform->addElement('checkbox', 'includenottaken', get_string('includenottaken', 'attendance'), get_string('yes'));
        $mform->addElement('checkbox', 'includeremarks', get_string('includeremarks', 'attendance'), get_string('yes'));
        $mform->addElement('checkbox', 'includedescription', get_string('includedescription', 'attendance'), get_string('yes'));
        $mform->addElement('date_selector', 'sessionstartdate', get_string('startofperiod', 'attendance'));
        $mform->setDefault('sessionstartdate', $course->startdate);
        $mform->disabledIf('sessionstartdate', 'includeallsessions', 'checked');
        $mform->addElement('date_selector', 'sessionenddate', get_string('endofperiod', 'attendance'));
        $mform->disabledIf('sessionenddate', 'includeallsessions', 'checked');

        $formatoptions = array('excel' => get_string('downloadexcel', 'attendance'),
                               'ooo' => get_string('downloadooo', 'attendance'),
                               'text' => get_string('downloadtext', 'attendance'));
        $mform->addElement('select', 'format', get_string('format'), $formatoptions);

        $submitstring = get_string('ok');
        $this->add_action_buttons(false, $submitstring);

        $mform->addElement('hidden', 'id', $cm->id);
    }

    /**
     * Validate form.
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Validate the 'users' field.
        if ($data['selectedusers'] && empty($data['users'])) {
            $errors['users'] = get_string('mustselectusers', 'mod_attendance');
        }

        return $errors;
    }
}

