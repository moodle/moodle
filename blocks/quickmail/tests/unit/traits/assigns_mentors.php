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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Mentor assignment helpers.
trait assigns_mentors {

    public function create_mentor_role() {
        $mentorroleid = $this->getDataGenerator()->create_role([
            'shortname' => 'mentor',
            'name' => 'Mentor',
            'description' => 'you shall pass',
        ]);

        global $DB;

        $capabilities = [
            'moodle/user:editprofile',
            'moodle/user:readuserblogs',
            'moodle/user:readuserposts',
            'moodle/user:viewalldetails',
            'moodle/user:viewuseractivitiesreport',
            'moodle/user:viewdetails',
        ];

        foreach ($capabilities as $capability) {
            $record = (object)[];
            $record->contextid = 1;
            $record->roleid = $mentorroleid;
            $record->capability = $capability;
            $record->permission = 1;
            $record->timemodified = time();
            $record->modifierid = 2;

            $DB->insert_record('role_capabilities', $record, false, false);
        }

        return $mentorroleid;
    }

    public function create_mentor() {
        $mentorroleid = $this->create_mentor_role();

        $mentoruser = $this->getDataGenerator()->create_user();

        $assignmentid = $this->getDataGenerator()->role_assign($mentorroleid, $mentoruser->id);

        return [$mentoruser, $mentorroleid];
    }

    public function create_mentor_for_user($user) {
        list($mentor, $mentorroleid) = $this->create_mentor();

        $this->assign_mentor_to_mentee($mentorroleid, $mentor, $user);

        return $mentor;
    }

    public function assign_mentor_to_mentee($mentorroleid, $mentor, $mentee) {
        $assignmentid = $this->getDataGenerator()->role_assign($mentorroleid, $mentor->id, context_user::instance($mentee->id)->id);

        return $assignmentid;
    }

}
