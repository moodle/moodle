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
 * This file contains the forms to add
 *
 * @package   mod_attendance
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * class for displaying add form.
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_attendance_add_form extends moodleform {

    /**
     * Called to define this moodle form
     *
     * @return void
     */
    public function definition() {

        global $CFG, $USER;
        $mform    =& $this->_form;

        $course        = $this->_customdata['course'];
        $cm            = $this->_customdata['cm'];
        $modcontext    = $this->_customdata['modcontext'];

        $pluginconfig = get_config('attendance');

        $mform->addElement('header', 'general', get_string('addsession', 'attendance'));

        $groupmode = groups_get_activity_groupmode($cm);
        switch ($groupmode) {
            case NOGROUPS:
                $mform->addElement('static', 'sessiontypedescription', get_string('sessiontype', 'attendance'),
                                  get_string('commonsession', 'attendance'));
                $mform->addHelpButton('sessiontypedescription', 'sessiontype', 'attendance');
                $mform->addElement('hidden', 'sessiontype', mod_attendance_structure::SESSION_COMMON);
                $mform->setType('sessiontype', PARAM_INT);
                break;
            case SEPARATEGROUPS:
                $mform->addElement('static', 'sessiontypedescription', get_string('sessiontype', 'attendance'),
                                  get_string('groupsession', 'attendance'));
                $mform->addHelpButton('sessiontypedescription', 'sessiontype', 'attendance');
                $mform->addElement('hidden', 'sessiontype', mod_attendance_structure::SESSION_GROUP);
                $mform->setType('sessiontype', PARAM_INT);
                break;
            case VISIBLEGROUPS:
                $radio = array();
                $radio[] = &$mform->createElement('radio', 'sessiontype', '', get_string('commonsession', 'attendance'),
                                                  mod_attendance_structure::SESSION_COMMON);
                $radio[] = &$mform->createElement('radio', 'sessiontype', '', get_string('groupsession', 'attendance'),
                                                  mod_attendance_structure::SESSION_GROUP);
                $mform->addGroup($radio, 'sessiontype', get_string('sessiontype', 'attendance'), ' ', false);
                $mform->setType('sessiontype', PARAM_INT);
                $mform->addHelpButton('sessiontype', 'sessiontype', 'attendance');
                $mform->setDefault('sessiontype', mod_attendance_structure::SESSION_COMMON);
                break;
        }
        if ($groupmode == SEPARATEGROUPS or $groupmode == VISIBLEGROUPS) {
            if ($groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $modcontext)) {
                $groups = groups_get_all_groups ($course->id, $USER->id, $cm->groupingid);
            } else {
                $groups = groups_get_all_groups($course->id, 0, $cm->groupingid);
            }
            if ($groups) {
                $selectgroups = array();
                foreach ($groups as $group) {
                    $selectgroups[$group->id] = $group->name;
                }
                $select = &$mform->addElement('select', 'groups', get_string('groups', 'group'), $selectgroups);
                $select->setMultiple(true);
                $mform->disabledIf('groups', 'sessiontype', 'eq', mod_attendance_structure::SESSION_COMMON);
            } else {
                if ($groupmode == VISIBLEGROUPS) {
                    $mform->updateElementAttr($radio, array('disabled' => 'disabled'));
                }
                $mform->addElement('static', 'groups', get_string('groups', 'group'),
                                  get_string('nogroups', 'attendance'));
                if ($groupmode == SEPARATEGROUPS) {
                    return;
                }
            }
        }

        attendance_form_sessiondate_selector($mform);

        // Select which status set to use.
        $maxstatusset = attendance_get_max_statusset($this->_customdata['att']->id);
        if ($maxstatusset > 0) {
            $opts = array();
            for ($i = 0; $i <= $maxstatusset; $i++) {
                $opts[$i] = attendance_get_setname($this->_customdata['att']->id, $i);
            }
            $mform->addElement('select', 'statusset', get_string('usestatusset', 'mod_attendance'), $opts);
        } else {
            $mform->addElement('hidden', 'statusset', 0);
            $mform->setType('statusset', PARAM_INT);
        }

        $mform->addElement('editor', 'sdescription', get_string('description', 'attendance'), array('rows' => 1, 'columns' => 80),
                            array('maxfiles' => EDITOR_UNLIMITED_FILES, 'noclean' => true, 'context' => $modcontext));
        $mform->setType('sdescription', PARAM_RAW);

        // If warnings allow selector for reporting.
        if (!empty(get_config('attendance', 'enablewarnings'))) {
            $mform->addElement('checkbox', 'absenteereport', '', get_string('includeabsentee', 'attendance'));
            $mform->addHelpButton('absenteereport', 'includeabsentee', 'attendance');
            if (isset($pluginconfig->absenteereport_default)) {
                $mform->setDefault('absenteereport', $pluginconfig->absenteereport_default);
            }
        } else {
            $mform->addElement('hidden', 'absenteereport', 1);
            $mform->setType('absenteereport', PARAM_INT);
        }
        // For multiple sessions.
        $mform->addElement('header', 'headeraddmultiplesessions', get_string('addmultiplesessions', 'attendance'));
        if (!empty($pluginconfig->multisessionexpanded)) {
            $mform->setExpanded('headeraddmultiplesessions');
        }
        $mform->addElement('checkbox', 'addmultiply', '', get_string('repeatasfollows', 'attendance'));
        $mform->addHelpButton('addmultiply', 'createmultiplesessions', 'attendance');

        $sdays = array();
        if ($CFG->calendar_startwday === '0') { // Week start from sunday.
            $sdays[] =& $mform->createElement('checkbox', 'Sun', '', get_string('sunday', 'calendar'));
        }
        $sdays[] =& $mform->createElement('checkbox', 'Mon', '', get_string('monday', 'calendar'));
        $sdays[] =& $mform->createElement('checkbox', 'Tue', '', get_string('tuesday', 'calendar'));
        $sdays[] =& $mform->createElement('checkbox', 'Wed', '', get_string('wednesday', 'calendar'));
        $sdays[] =& $mform->createElement('checkbox', 'Thu', '', get_string('thursday', 'calendar'));
        $sdays[] =& $mform->createElement('checkbox', 'Fri', '', get_string('friday', 'calendar'));
        $sdays[] =& $mform->createElement('checkbox', 'Sat', '', get_string('saturday', 'calendar'));
        if ($CFG->calendar_startwday !== '0') { // Week start from sunday.
            $sdays[] =& $mform->createElement('checkbox', 'Sun', '', get_string('sunday', 'calendar'));
        }
        $mform->addGroup($sdays, 'sdays', get_string('repeaton', 'attendance'), array('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'), true);
        $mform->disabledIf('sdays', 'addmultiply', 'notchecked');

        $period = array(1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
            21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36);
        $periodgroup = array();
        $periodgroup[] =& $mform->createElement('select', 'period', '', $period, false, true);
        $periodgroup[] =& $mform->createElement('static', 'perioddesc', '', get_string('week', 'attendance'));
        $mform->addGroup($periodgroup, 'periodgroup', get_string('repeatevery', 'attendance'), array(' '), false);
        $mform->disabledIf('periodgroup', 'addmultiply', 'notchecked');

        $mform->addElement('date_selector', 'sessionenddate', get_string('repeatuntil', 'attendance'));
        $mform->disabledIf('sessionenddate', 'addmultiply', 'notchecked');

        $mform->addElement('hidden', 'coursestartdate', $course->startdate);
        $mform->setType('coursestartdate', PARAM_INT);

        $mform->addElement('hidden', 'previoussessiondate', 0);
        $mform->setType('previoussessiondate', PARAM_INT);

        // Students can mark own attendance.
        if (!empty(get_config('attendance', 'studentscanmark'))) {
            $mform->addElement('header', 'headerstudentmarking', get_string('studentmarking', 'attendance'), true);
            if (!empty($pluginconfig->studentrecordingexpanded)) {
                $mform->setExpanded('headerstudentmarking');
            }
            $mform->addElement('checkbox', 'studentscanmark', '', get_string('studentscanmark', 'attendance'));
            $mform->addHelpButton('studentscanmark', 'studentscanmark', 'attendance');

            $options = attendance_get_automarkoptions();

            $mform->addElement('select', 'automark', get_string('automark', 'attendance'), $options);
            $mform->setType('automark', PARAM_INT);
            $mform->addHelpButton('automark', 'automark', 'attendance');
            $mform->hideif('automark', 'studentscanmark', 'notchecked');
            $mform->setDefault('automark', $this->_customdata['att']->automark);

            $mgroup = array();

            $mgroup[] = & $mform->createElement('text', 'studentpassword', get_string('studentpassword', 'attendance'));
            $mgroup[] = & $mform->createElement('checkbox', 'randompassword', '', get_string('randompassword', 'attendance'));
            $mform->addGroup($mgroup, 'passwordgrp', get_string('passwordgrp', 'attendance'), array(' '), false);

            $mform->setType('studentpassword', PARAM_TEXT);
            $mform->addHelpButton('passwordgrp', 'passwordgrp', 'attendance');

            $mform->hideif('passwordgrp', 'studentscanmark', 'notchecked');
            $mform->hideif('studentpassword', 'randompassword', 'checked');
            $mform->hideif('passwordgrp', 'automark', 'eq', ATTENDANCE_AUTOMARK_ALL);

            $mform->addElement('checkbox', 'autoassignstatus', '', get_string('autoassignstatus', 'attendance'));
            $mform->addHelpButton('autoassignstatus', 'autoassignstatus', 'attendance');
            $mform->hideif('autoassignstatus', 'studentscanmark', 'notchecked');

            if (isset($pluginconfig->autoassignstatus)) {
                $mform->setDefault('autoassignstatus', $pluginconfig->autoassignstatus);
            }
            if (isset($pluginconfig->studentscanmark_default)) {
                $mform->setDefault('studentscanmark', $pluginconfig->studentscanmark_default);
            }
            if (isset($pluginconfig->randompassword_default)) {
                $mform->setDefault('randompassword', $pluginconfig->randompassword_default);
            }
            if (isset($pluginconfig->automark_default)) {
                $mform->setDefault('automark', $pluginconfig->automark_default);
            }
            $mgroup2 = array();
            $mgroup2[] = & $mform->createElement('text', 'subnet', get_string('requiresubnet', 'attendance'));
            if (empty(get_config('attendance', 'subnetactivitylevel'))) {
                $mform->setDefault('subnet', get_config('attendance', 'subnet'));
            } else {
                $mform->setDefault('subnet', $this->_customdata['att']->subnet);
            }

            $mgroup2[] = & $mform->createElement('checkbox', 'usedefaultsubnet', get_string('usedefaultsubnet', 'attendance'));
            $mform->setDefault('usedefaultsubnet', 1);
            $mform->setType('subnet', PARAM_TEXT);

            $mform->addGroup($mgroup2, 'subnetgrp', get_string('requiresubnet', 'attendance'), array(' '), false);
            $mform->setAdvanced('subnetgrp');
            $mform->addHelpButton('subnetgrp', 'requiresubnet', 'attendance');

            $mform->hideif('subnetgrp', 'studentscanmark', 'notchecked');
            $mform->hideif('subnet', 'usedefaultsubnet', 'checked');

            $mgroup3 = array();
            $mgroup3[] = & $mform->createElement('checkbox', 'preventsharedip', '');
            $mgroup3[] = & $mform->createElement('text', 'preventsharediptime',
                get_string('preventsharediptime', 'attendance'), '', 'test');
            $mgroup3[] = & $mform->createElement('static', 'preventsharediptimedesc', '',
                get_string('preventsharedipminutes', 'attendance'));
            $mform->addGroup($mgroup3, 'preventsharedgroup', get_string('preventsharedip', 'attendance'), array(' '), false);
            $mform->addHelpButton('preventsharedgroup', 'preventsharedip', 'attendance');
            $mform->setAdvanced('preventsharedgroup');
            $mform->setType('preventsharediptime', PARAM_INT);
            $mform->hideif('preventsharedgroup', 'studentscanmark', 'notchecked');
            $mform->disabledIf('preventsharediptime', 'preventsharedip', 'notchecked');
            if (isset($pluginconfig->preventsharedip)) {
                $mform->setDefault('preventsharedip', $pluginconfig->preventsharedip);
            }
            if (isset($pluginconfig->preventsharediptime)) {
                $mform->setDefault('preventsharediptime', $pluginconfig->preventsharediptime);
            }

        } else {
            $mform->addElement('hidden', 'studentscanmark', '0');
            $mform->settype('studentscanmark', PARAM_INT);
            $mform->addElement('hidden', 'automark', '0');
            $mform->setType('automark', PARAM_INT);
            $mform->addElement('hidden', 'autoassignstatus', '0');
            $mform->setType('autoassignstatus', PARAM_INT);

            $mform->addElement('hidden', 'subnet', '');
            $mform->setType('subnet', PARAM_TEXT);

            $mform->addElement('hidden', 'preventsharedip', '0');
            $mform->setType('preventsharedip', PARAM_INT);

            $sharedtime = isset($pluginconfig->preventsharediptime) ? $pluginconfig->preventsharediptime : null;
            $mform->addElement('hidden', 'preventsharediptime', $sharedtime);
            $mform->setType('preventsharediptime', PARAM_INT);
        }

        $this->add_action_buttons(true, get_string('add', 'attendance'));
    }

    /**
     * Perform minimal validation on the settings form
     * @param array $data
     * @param array $files
     */
    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);

        $sesstarttime = $data['sestime']['starthour'] * HOURSECS + $data['sestime']['startminute'] * MINSECS;
        $sesendtime = $data['sestime']['endhour'] * HOURSECS + $data['sestime']['endminute'] * MINSECS;
        if ($sesendtime < $sesstarttime) {
            $errors['sestime'] = get_string('invalidsessionendtime', 'attendance');
        }

        if (!empty($data['addmultiply']) && $data['sessiondate'] != 0 && $data['sessionenddate'] != 0 &&
                $data['sessionenddate'] < $data['sessiondate']) {
            $errors['sessionenddate'] = get_string('invalidsessionenddate', 'attendance');
        }

        if ($data['sessiontype'] == mod_attendance_structure::SESSION_GROUP and empty($data['groups'])) {
            $errors['groups'] = get_string('errorgroupsnotselected', 'attendance');
        }

        $addmulti = isset($data['addmultiply']) ? (int)$data['addmultiply'] : 0;
        if (($addmulti != 0) && (!array_key_exists('sdays', $data) || empty($data['sdays']))) {
            $data['sdays'] = array();
            $errors['sdays'] = get_string('required', 'attendance');
        }
        if (isset($data['sdays'])) {
            if (!$this->checkweekdays($data['sessiondate'], $data['sessionenddate'], $data['sdays']) ) {
                $errors['sdays'] = get_string('checkweekdays', 'attendance');
            }
        }
        if ($addmulti && ceil(($data['sessionenddate'] - $data['sessiondate']) / YEARSECS) > 1) {
            $errors['sessionenddate'] = get_string('timeahead', 'attendance');
        }

        if ($data['sessiondate'] < $data['coursestartdate'] && $data['sessiondate'] != $data['previoussessiondate']) {
            $errors['sessiondate'] = get_string('priorto', 'attendance',
                userdate($data['coursestartdate'], get_string('strftimedmy', 'attendance')));
            $this->_form->setConstant('previoussessiondate', $data['sessiondate']);
        }

        if ($data['automark'] == ATTENDANCE_AUTOMARK_CLOSE) {
            $cm            = $this->_customdata['cm'];
            // Check that the selected statusset has a status to use when unmarked.
            $sql = 'SELECT id
            FROM {attendance_statuses}
            WHERE deleted = 0 AND (attendanceid = 0 or attendanceid = ?)
            AND setnumber = ? AND setunmarked = 1';
            $params = array($cm->instance, $data['statusset']);
            if (!$DB->record_exists_sql($sql, $params)) {
                $errors['automark'] = get_string('noabsentstatusset', 'attendance');
            }
        }

        if (!empty($data['studentscanmark']) && !empty($data['preventsharedip']) &&
                empty($data['preventsharediptime'])) {
            $errors['preventsharedgroup'] = get_string('iptimemissing', 'attendance');

        }
        return $errors;
    }

    /**
     * Check weekdays function.
     * @param int $sessiondate
     * @param int $sessionenddate
     * @param int $sdays
     * @return bool
     */
    private function checkweekdays($sessiondate, $sessionenddate, $sdays) {

        $found = false;

        $daysofweek = array(0 => "Sun", 1 => "Mon", 2 => "Tue", 3 => "Wed", 4 => "Thu", 5 => "Fri", 6 => "Sat");
        $start = new DateTime( date("Y-m-d", $sessiondate) );
        $interval = new DateInterval('P1D');
        $end = new DateTime( date("Y-m-d", $sessionenddate) );
        $end->add( new DateInterval('P1D') );

        $period = new DatePeriod($start, $interval, $end);
        foreach ($period as $date) {
            if (!$found) {
                foreach ($sdays as $name => $value) {
                    $key = array_search($name, $daysofweek);
                    if ($date->format("w") == $key) {
                        $found = true;
                        break;
                    }
                }
            }
        }

        return $found;
    }
}
