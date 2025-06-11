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
 * Course request from Microsoft Teams feature.
 *
 * @package     local_o365
 * @copyright   Enovation Solutions Ltd. {@link https://enovation.ie}
 * @author      Patryk Mroczko <patryk.mroczko@enovation.ie>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_o365\feature\courserequest;

use context_course;
use core_user;
use course_request;
use local_o365\httpclient;
use local_o365\oauth2\clientdata;
use local_o365\rest\unified;
use local_o365\utils;
use moodle_exception;
use stdClass;

/**
 * Main class for the course request from Microsoft Teams feature.
 */
class main {
    /**
     * @var int The course request status: pending.
     */
    const COURSE_REQUEST_STATUS_PENDING = 0;
    /**
     * @var int The course request status: approved.
     */
    const COURSE_REQUEST_STATUS_APPROVED = 1;
    /**
     * @var int The course request status: rejected.
     */
    const COURSE_REQUEST_STATUS_REJECTED = 2;

    /**
     * @var unified a graph API client.
     */
    private $graphclient;

    /**
     * @var bool whether the debug is turned on.
     */
    private $debug;

    /**
     * Constructor.
     *
     * @param unified $graphclient
     * @param bool $debug
     */
    public function __construct(unified $graphclient, bool $debug = false) {
        $this->graphclient = $graphclient;
        $this->debug = $debug;
    }

    /**
     * Optionally run mtrace() based on $this->debug setting.
     *
     * @param string $msg
     * @param int $level
     * @param string $eol
     * @return void
     */
    protected function mtrace(string $msg, int $level = 0, string $eol = "\n"): void {
        if ($this->debug === true) {
            if ($level) {
                $msg = str_repeat('...', $level) . ' ' . $msg;
            }
            mtrace($msg, $level, $eol);
        }
    }

    /**
     * Save custom course request data to the database.
     *
     * @param course_request $request
     * @param array $teamdata
     * @return bool
     */
    public function save_custom_course_request_data(course_request $request, array $teamdata): bool {
        global $DB;

        if (empty($request) || empty($teamdata)) {
            return false;
        }

        $courserequestrecord = new stdClass();
        $courserequestrecord->requestid = $request->id;
        $courserequestrecord->teamoid = $teamdata['id'];
        $courserequestrecord->teamname = $teamdata['name'];
        $courserequestrecord->courseshortname = $request->shortname;
        $courserequestrecord->requeststatus = self::COURSE_REQUEST_STATUS_PENDING;
        $courserequestrecord->courseid = null;

        $DB->insert_record('local_o365_course_request', $courserequestrecord);

        return true;
    }

    /**
     * Retrieve unmatched teams for a given user based on their object ID.
     *
     * @param string $userobjectid
     *
     * @return array|false
     */
    public function get_unmatched_teams_by_user_oid(string $userobjectid) {
        global $DB;

        $userteams = $this->get_user_teams_by_user_oid($userobjectid);

        if ($userteams === false) {
            return false;
        }

        // Get Teams.
        $teamsoidsql =
            "SELECT distinct objectid
               FROM {local_o365_objects}
              WHERE type = 'group'
                AND subtype IN ('teamfromgroup', 'course', 'courseteam')";
        $matchedgroupoids = $DB->get_fieldset_sql($teamsoidsql);

        // Get SDS courses.
        $sdsmatchedgroupoids = $DB->get_fieldset_select('local_o365_objects', 'objectid',
            "type = 'sdssection' AND subtype = 'course'");

        $matchedgroupoids = array_unique(array_merge($matchedgroupoids, $sdsmatchedgroupoids));
        $unmatchedteams = [];

        foreach ($userteams as $userteam) {
            if (!in_array($userteam['id'], $matchedgroupoids)) {
                $unmatchedteams[] = $userteam;
            }
        }

        usort($unmatchedteams, function($a, $b) {
            return strcmp($a['displayName'], $b['displayName']);
        });

        return $unmatchedteams;
    }

    /**
     * Retrieve the user teams associated with a given user object ID.
     *
     * @param string $userobjectid
     *
     * @return array|false
     */
    private function get_user_teams_by_user_oid(string $userobjectid) {
        try {
            $userteams = $this->graphclient->get_user_teams($userobjectid);
        } catch (moodle_exception $e) {
            $this->mtrace('Error fetching Microsoft Teams for user OID ' . $userobjectid . ': ' . $e->getMessage());
            $userteams = false;
        }

        return $userteams;
    }

    /**
     * Get details of the user's team based on a given team object ID.
     *
     * @param string $teamoid
     * @return array|false
     */
    public function get_user_team_details_by_team_oid(string $teamoid) {
        try {
            [$team, $teamurl, $lockstatus] = $this->graphclient->get_team($teamoid);

            return [
                'name' => $team['displayName'],
                'url' => $teamurl,
                'id' => $team['id'],
            ];
        } catch (moodle_exception $e) {
            $this->mtrace('Error fetching Microsoft Team details for Team OID ' . $teamoid . ': ' . $e->getMessage());
        }

        return false;
    }

    /**
     * Enrol team owners and members in a Moodle course based on a given team object ID and course ID.
     *
     * @param string $teamoid
     * @param int $courseid
     * @return bool
     */
    public function enrol_team_owners_and_members_in_course_by_team_oid_and_course_id(string $teamoid, int $courseid): bool {
        global $DB;

        if (empty($teamoid) || empty($courseid)) {
            mtrace("...... Team OID or course ID is empty. Exiting.");

            return false;
        }

        [$teamowners, $teammembers] = $this->get_team_owners_and_members_by_team_oid($teamoid);

        // Remove owners from members list.
        foreach ($teammembers as $key => $teammember) {
            if (array_key_exists($key, $teamowners)) {
                unset($teammembers[$key]);
            }
        }

        if (empty($teamowners) && empty($teammembers)) {
            mtrace("...... No users to enrol in course ID $courseid.");

            return true;
        }

        mtrace("...... Enrolling " . count($teamowners) . " owners and " . count($teammembers) .
            " members in course ID $courseid.");

        $ownerroleid = get_config('local_o365', 'courserequestownerrole');
        $memberroleid = get_config('local_o365', 'courserequestmemberrole');

        $context = context_course::instance($courseid);
        $enrolleduserids = array_keys(get_enrolled_users($context));

        if (!$ownerroleid) {
            mtrace("...... No owner role defined. Skipping owner enrolments.");
        } else {
            foreach ($teamowners as $teamowner) {
                if (!$userconnectionrecord = $DB->get_record('local_o365_objects',
                    ['type' => 'user', 'objectid' => $teamowner['id']])) {
                    mtrace('......... No user connection record found for user ' . $teamowner['id']);
                    continue;
                }
                $moodleuser = core_user::get_user($userconnectionrecord->moodleid);
                if (!$moodleuser) {
                    mtrace('......... Invalid user connection record found for user ' . $teamowner['id']);
                    continue;
                }
                if ($moodleuser->suspended || $moodleuser->deleted) {
                    mtrace('......... Moodle user matching Microsoft account ' . $teamowner['id'] . ' is suspended or deleted.');
                    continue;
                }
                if (user_has_role_assignment($userconnectionrecord->moodleid, $ownerroleid, $context->id) &&
                    in_array($userconnectionrecord->moodleid, $enrolleduserids)) {
                    mtrace('......... Moodle user matching Microsoft account ' . $teamowner['id'] .
                        ' is already enrolled as owner.');
                    continue;
                }
                enrol_try_internal_enrol($courseid, $userconnectionrecord->moodleid, $ownerroleid);
                mtrace("......... Enrolled user with Moodle ID {$userconnectionrecord->moodleid} in course ID $courseid " .
                    "with role ID $ownerroleid.");
            }
        }

        if (!$memberroleid) {
            mtrace("......... No member role defined. Skipping member enrolments.");
        } else {
            foreach ($teammembers as $teammember) {
                if (!$userconnectionrecord = $DB->get_record('local_o365_objects',
                    ['type' => 'user', 'objectid' => $teammember['id']])) {
                    mtrace('......... No user connection record found for user ' . $teammember['id']);
                    continue;
                }
                $moodleuser = core_user::get_user($userconnectionrecord->moodleid);
                if (!$moodleuser) {
                    mtrace('......... Invalid user connection record found for user ' . $teammember['id']);
                    continue;
                }
                if ($moodleuser->suspended || $moodleuser->deleted) {
                    mtrace('......... Moodle user matching Microsoft account ' . $teammember['id'] . ' is suspended or deleted.');
                    continue;
                }
                if (user_has_role_assignment($userconnectionrecord->moodleid, $memberroleid, $context->id) &&
                    in_array($userconnectionrecord->moodleid, $enrolleduserids)) {
                    mtrace('......... Moodle user matching Microsoft account ' . $teammember['id'] .
                        ' is already enrolled as member.');
                    continue;
                }
                enrol_try_internal_enrol($courseid, $userconnectionrecord->moodleid, $memberroleid);
                mtrace("......... Enrolled user with Moodle ID {$userconnectionrecord->moodleid} in course ID $courseid " .
                    "with role ID $memberroleid.");
            }
        }

        return true;
    }

    /**
     * Fetch all owners and members of the Microsoft team with the given Object ID (OID).
     *
     * @param string $teamoid
     *
     * @return array
     */
    private function get_team_owners_and_members_by_team_oid(string $teamoid): array {
        $teamowners = [];
        $teammembers = [];

        try {
            $ownerrecords = $this->graphclient->get_group_owners($teamoid);
            foreach ($ownerrecords as $ownerrecord) {
                $teamowners[$ownerrecord['id']] = $ownerrecord;
            }
        } catch (moodle_exception $e) {
            mtrace("...... Error fetching Microsoft Team owners for Team OID $teamoid: " . $e->getMessage());
        }

        try {
            $memberrecords = $this->graphclient->get_group_members($teamoid);
            foreach ($memberrecords as $memberrecord) {
                $teammembers[$memberrecord['id']] = $memberrecord;
            }
        } catch (moodle_exception $e) {
            mtrace("...... Error fetching Microsoft Team members for Team OID $teamoid: " . $e->getMessage());
        }

        return [$teamowners, $teammembers];
    }

    /**
     * Get a Microsoft Graph API instance.
     *
     * @param string $caller The calling function, used for logging.
     * @return unified|bool A Microsoft Graph API instance.
     */
    public static function get_unified_api(string $caller = 'local_o365/feature/courserequest/get_unified_api') {
        $clientdata = clientdata::instance_from_oidc();
        $httpclient = new httpclient();
        $tokenresource = unified::get_tokenresource();
        $token = utils::get_application_token($tokenresource, $clientdata, $httpclient);
        if (!empty($token)) {
            return new unified($token, $httpclient);
        } else {
            $msg = 'Couldn\'t construct Microsoft Graph API client because we don\'t have an application token.';
            utils::debug($msg, $caller);

            return false;
        }
    }
}
