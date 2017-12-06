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
 * Update form
 *
 * @package    mod_attendance
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/formslib.php');

/**
 * class for displaying update form.
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_attendance_update_form extends moodleform {

    /**
     * Called to define this moodle form
     *
     * @return void
     */
    public function definition() {

        global $DB;
        $mform    =& $this->_form;

        $modcontext    = $this->_customdata['modcontext'];
        $sessionid     = $this->_customdata['sessionid'];

        if (!$sess = $DB->get_record('attendance_sessions', array('id' => $sessionid) )) {
            error('No such session in this course');
        }
        $attendancesubnet = $DB->get_field('attendance', 'subnet', array('id' => $sess->attendanceid));
        $defopts = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'noclean' => true, 'context' => $modcontext);
        $sess = file_prepare_standard_editor($sess, 'description', $defopts, $modcontext, 'mod_attendance', 'session', $sess->id);

        $starttime = $sess->sessdate - usergetmidnight($sess->sessdate);
        $starthour = floor($starttime / HOURSECS);
        $startminute = floor(($starttime - $starthour * HOURSECS) / MINSECS);

        $enddate = $sess->sessdate + $sess->duration;
        $endtime = $enddate - usergetmidnight($enddate);
        $endhour = floor($endtime / HOURSECS);
        $endminute = floor(($endtime - $endhour * HOURSECS) / MINSECS);

        $data = array('sessiondate' => $sess->sessdate,
                'sestime' => array('starthour' => $starthour, 'startminute' => $startminute,
                                   'endhour' => $endhour, 'endminute' => $endminute),
                'sdescription' => $sess->description_editor,
                'studentscanmark' => $sess->studentscanmark,
                'studentpassword' => $sess->studentpassword,
                'subnet' => $sess->subnet,
                'automark' => $sess->automark,
                'automarkcompleted' => 0);
        if ($sess->subnet == $attendancesubnet) {
            $data['usedefaultsubnet'] = 1;
        } else {
            $data['usedefaultsubnet'] = 0;
        }

        $mform->addElement('header', 'general', get_string('changesession', 'attendance'));

        if ($sess->groupid == 0) {
            $strtype = get_string('commonsession', 'attendance');
        } else {
            $groupname = $DB->get_field('groups', 'name', array('id' => $sess->groupid));
            $strtype = get_string('group') . ': ' . $groupname;
        }
        $mform->addElement('static', 'sessiontypedescription', get_string('sessiontype', 'attendance'), $strtype);

        $olddate = construct_session_full_date_time($sess->sessdate, $sess->duration);
        $mform->addElement('static', 'olddate', get_string('olddate', 'attendance'), $olddate);

        attendance_form_sessiondate_selector($mform);

        // Show which status set is in use.
        $maxstatusset = attendance_get_max_statusset($this->_customdata['att']->id);
        if ($maxstatusset > 0) {
            $mform->addElement('static', 'statusset', get_string('usestatusset', 'mod_attendance'),
                attendance_get_setname($this->_customdata['att']->id, $sess->statusset));
        }

        $mform->addElement('editor', 'sdescription', get_string('description', 'attendance'),
                           array('rows' => 1, 'columns' => 80), $defopts);
        $mform->setType('sdescription', PARAM_RAW);

        // Students can mark own attendance.
        if (!empty(get_config('attendance', 'studentscanmark'))) {
            $mform->addElement('header', 'headerstudentmarking', get_string('studentmarking', 'attendance'), true);
            $mform->setExpanded('headerstudentmarking');

            $mform->addElement('checkbox', 'studentscanmark', '', get_string('studentscanmark', 'attendance'));
            $mform->addHelpButton('studentscanmark', 'studentscanmark', 'attendance');

            $options2 = array(
                ATTENDANCE_AUTOMARK_DISABLED => get_string('noautomark', 'attendance'),
                ATTENDANCE_AUTOMARK_ALL => get_string('automarkall', 'attendance'),
                ATTENDANCE_AUTOMARK_CLOSE => get_string('automarkclose', 'attendance'));

            $mform->addElement('select', 'automark', get_string('automark', 'attendance'), $options2);
            $mform->setType('automark', PARAM_INT);
            $mform->addHelpButton('automark', 'automark', 'attendance');
            $mform->disabledif('automark', 'studentscanmark', 'notchecked');

            $mform->addElement('text', 'studentpassword', get_string('studentpassword', 'attendance'));
            $mform->setType('studentpassword', PARAM_TEXT);
            $mform->addHelpButton('studentpassword', 'passwordgrp', 'attendance');
            $mform->disabledif('studentpassword', 'studentscanmark', 'notchecked');
            $mform->disabledif('studentpassword', 'automark', 'eq', ATTENDANCE_AUTOMARK_ALL);
            $mform->disabledif('randompassword', 'automark', 'eq', ATTENDANCE_AUTOMARK_ALL);

            $mgroup = array();
            $mgroup[] = & $mform->createElement('text', 'subnet', get_string('requiresubnet', 'attendance'));
            $mform->setDefault('subnet', $this->_customdata['att']->subnet);
            $mgroup[] = & $mform->createElement('checkbox', 'usedefaultsubnet', get_string('usedefaultsubnet', 'attendance'));
            $mform->setDefault('usedefaultsubnet', 1);
            $mform->setType('subnet', PARAM_TEXT);

            $mform->addGroup($mgroup, 'subnetgrp', get_string('requiresubnet', 'attendance'), array(' '), false);
            $mform->setAdvanced('subnetgrp');
            $mform->addHelpButton('subnetgrp', 'requiresubnet', 'attendance');

            $mform->disabledif('usedefaultsubnet', 'studentscanmark', 'notchecked');
            $mform->disabledif('subnet', 'studentscanmark', 'notchecked');
            $mform->disabledif('subnet', 'usedefaultsubnet', 'checked');

            $mform->addElement('hidden', 'automarkcompleted', '0');
            $mform->settype('automarkcompleted', PARAM_INT);

        } else {
            $mform->addElement('hidden', 'studentscanmark', '0');
            $mform->settype('studentscanmark', PARAM_INT);
            $mform->addElement('hidden', 'subnet', '0');
            $mform->settype('subnet', PARAM_TEXT);
            $mform->addElement('hidden', 'automark', '0');
            $mform->settype('automark', PARAM_INT);
            $mform->addElement('hidden', 'automarkcompleted', '0');
            $mform->settype('automarkcompleted', PARAM_INT);
        }

        $mform->setDefaults($data);

        $this->add_action_buttons(true);
    }

    /**
     * Perform minimal validation on the settings form
     * @param array $data
     * @param array $files
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $sesstarttime = $data['sestime']['starthour'] * HOURSECS + $data['sestime']['startminute'] * MINSECS;
        $sesendtime = $data['sestime']['endhour'] * HOURSECS + $data['sestime']['endminute'] * MINSECS;
        if ($sesendtime < $sesstarttime) {
            $errors['sestime'] = get_string('invalidsessionendtime', 'attendance');
        }

        return $errors;
    }
}
