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
 * Defines all the backup steps that will be used by {@link backup_attendance_activity_task}
 *
 * @package    mod_attendance
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Defines the complete attendance structure for backup, with file and id annotations
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_attendance_activity_structure_step extends backup_activity_structure_step {

    /**
     * Define the structure of the backup workflow.
     *
     * @return restore_path_element $structure
     */
    protected function define_structure() {

        // Are we including userinfo?
        $userinfo = $this->get_setting_value('userinfo');

        // XML nodes declaration - non-user data.
        $attendance = new backup_nested_element('attendance', array('id'), array(
            'name', 'intro', 'introformat', 'grade', 'showextrauserdetails', 'showsessiondetails', 'sessiondetailspos', 'subnet'));

        $statuses = new backup_nested_element('statuses');
        $status  = new backup_nested_element('status', array('id'), array(
            'acronym', 'description', 'grade', 'studentavailability', 'setunmarked', 'visible', 'deleted', 'setnumber'));

        $warnings = new backup_nested_element('warnings');
        $warning  = new backup_nested_element('warning', array('id'), array('warningpercent', 'warnafter',
            'maxwarn', 'emailuser', 'emailsubject', 'emailcontent', 'emailcontentformat', 'thirdpartyemails'));

        $sessions = new backup_nested_element('sessions');
        $session  = new backup_nested_element('session', array('id'), array(
            'groupid', 'sessdate', 'duration', 'lasttaken', 'lasttakenby', 'timemodified',
            'description', 'descriptionformat', 'studentscanmark', 'studentpassword', 'autoassignstatus',
            'subnet', 'automark', 'automarkcompleted', 'statusset', 'absenteereport', 'preventsharedip',
            'preventsharediptime', 'caleventid'));

        // XML nodes declaration - user data.
        $logs = new backup_nested_element('logs');
        $log  = new backup_nested_element('log', array('id'), array(
            'sessionid', 'studentid', 'statusid', 'statusset', 'timetaken', 'takenby', 'remarks'));

        // Build the tree in the order needed for restore.
        $attendance->add_child($statuses);
        $statuses->add_child($status);

        $attendance->add_child($warnings);
        $warnings->add_child($warning);

        $attendance->add_child($sessions);
        $sessions->add_child($session);

        $session->add_child($logs);
        $logs->add_child($log);

        // Data sources - non-user data.

        $attendance->set_source_table('attendance', array('id' => backup::VAR_ACTIVITYID));

        $status->set_source_table('attendance_statuses', array('attendanceid' => backup::VAR_PARENTID));

        $warning->set_source_table('attendance_warning',
            array('idnumber' => backup::VAR_PARENTID));

        $session->set_source_table('attendance_sessions', array('attendanceid' => backup::VAR_PARENTID));

        // Data sources - user related data.
        if ($userinfo) {
            $log->set_source_table('attendance_log', array('sessionid' => backup::VAR_PARENTID));
        }

        // Id annotations.
        $session->annotate_ids('user', 'lasttakenby');
        $session->annotate_ids('group', 'groupid');
        $log->annotate_ids('user', 'studentid');
        $log->annotate_ids('user', 'takenby');

        // File annotations.
        $session->annotate_files('mod_attendance', 'session', 'id');

        // Return the root element (workshop), wrapped into standard activity structure.
        return $this->prepare_activity_structure($attendance);
    }
}
