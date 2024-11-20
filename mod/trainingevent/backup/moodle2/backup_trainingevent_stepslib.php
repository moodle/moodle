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
 * @package   mod_trainingevent
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the backup steps that will be used by the backup_trainingevent_activity_task
 */

/**
 * Define the complete trainingevent structure for backup, with file and id annotations
 */
class backup_trainingevent_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated.
        $trainingevent = new backup_nested_element('trainingevent', array('id'), array(
            'course', 'name', 'intro', 'introformat', 'timemodified', 'startdatetime', 'enddatetime', 'classroomid', 'approvaltype'));
        $trainingevent_users = new backup_nested_element('trainingevent_user');
        $trainingevent_user = new backup_nested_element('trainingevent_users', array('id'), array('userid', 'trainingeventid', 'waitlisted'));

        $trainingevent->add_child($trainingevent_users);
        $trainingevent_users->add_child($trainingevent_user);

        // Build the tree.
        // (love this).

        // Define sources.
        $trainingevent->set_source_table('trainingevent', array('id' => backup::VAR_ACTIVITYID));
        $trainingevent_user->set_source_table('trainingevent_users', array('trainingeventid' => backup::VAR_ACTIVITYID));

        // Define id annotations.
        // (none).

        // Define file annotations.
        // (none).

        // Return the root element (trainingevent), wrapped into standard activity structure.
        return $this->prepare_activity_structure($trainingevent);
    }
}
