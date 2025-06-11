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
 * An adhoc task to process course request approval.
 *
 * @package     local_o365
 * @copyright   Enovation Solutions Ltd. {@link https://enovation.ie}
 * @author      Patryk Mroczko <patryk.mroczko@enovation.ie>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_o365\task;

use core\task\adhoc_task;
use local_o365\feature\courserequest\main;
use local_o365\feature\coursesync\utils;
use moodle_exception;
use stdClass;

/**
 * Adhoc task for processing course request approval.
 */
class processcourserequestapproval extends adhoc_task {
    /**
     * Execute the task.
     *
     * @return bool
     */
    public function execute(): bool {
        global $DB;

        $coursedata = $this->get_custom_data();

        $courserequestdata = $coursedata->customrequest;

        // If the core course request still exists, it means that the course has not been initiated using a custom course request
        // from Teams feature.
        if ($DB->record_exists('course_request', ['id' => $courserequestdata->requestid])) {
            mtrace("... Course request with shortname {$coursedata->shortname} still exists. " .
                "Course with ID {$coursedata->courseid} was not initiated using a custom course request from Teams feature. " .
                "Exiting.");

            return true;
        }

        $apiclient = main::get_unified_api();
        if (empty($apiclient)) {
            throw new moodle_exception('errorcannotgetapiclient', 'local_o365');
        }
        $courserequest = new main($apiclient);

        mtrace("... Start sync between Moodle Course with ID {$coursedata->courseid} and Microsoft Team with OID " .
            "{$courserequestdata->teamoid}...");

        $courserequest->enrol_team_owners_and_members_in_course_by_team_oid_and_course_id($courserequestdata->teamoid,
            $coursedata->courseid);

        // Update custom course request status.
        $courserequestdata->requeststatus = main::COURSE_REQUEST_STATUS_APPROVED;
        $courserequestdata->courseid = $coursedata->courseid;
        $DB->update_record('local_o365_course_request', $courserequestdata);

        // Add course and Teams connection records.
        $grouprecord = new stdClass();
        $grouprecord->type = 'group';
        $grouprecord->subtype = 'course';
        $grouprecord->objectid = $courserequestdata->teamoid;
        $grouprecord->moodleid = $coursedata->courseid;
        $grouprecord->o365name = $courserequestdata->teamname;
        $grouprecord->timecreated = time();
        $grouprecord->timemodified = $grouprecord->timecreated;
        $DB->insert_record('local_o365_objects', $grouprecord);

        $grouprecord->subtype = 'teamfromgroup';
        // Ideally, this should be "coursefromteam", a new subtype to indicate that the course is created from a Team.
        $DB->insert_record('local_o365_objects', $grouprecord);

        // Save course sync status.
        utils::set_course_sync_enabled($coursedata->courseid);

        // Update teams cache record status.
        if ($teamcacherecord = $DB->get_record('local_o365_teams_cache', ['objectid' => $courserequestdata->teamoid])) {
            if ($teamcacherecord->locked != TEAM_LOCKED) {
                $teamcacherecord->locked = TEAM_LOCKED;
                $DB->update_record('local_o365_teams_cache', $teamcacherecord);
            }
        }

        return true;
    }
}
