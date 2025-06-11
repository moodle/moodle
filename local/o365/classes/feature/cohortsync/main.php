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
 * Microsoft Group and Moodle cohort mapping feature main class
 *
 * @package     local_o365
 * @copyright   Enovation Solutions Ltd. {@link https://enovation.ie}
 * @author      Patryk Mroczko <patryk.mroczko@enovation.ie>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_o365\feature\cohortsync;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/cohort/lib.php');

use context_system;
use local_o365\httpclient;
use local_o365\oauth2\clientdata;
use local_o365\rest\unified;
use local_o365\utils;
use moodle_exception;
use stdClass;

/**
 * Microsoft Group and Moodle cohort mapping feature main class.
 */
class main {
    /**
     * @var unified Graph client instance.
     */
    private $graphclient;

    /**
     * @var array|null List of groups.
     */
    private $grouplist;

    /**
     * @var array|null List of cohorts.
     */
    private $cohortlist;

    /**
     * Return the list of groups.
     *
     * @return array
     */
    public function get_grouplist(): array {
        if (is_null($this->grouplist)) {
            $this->fetch_groups_from_cache();
        }

        return $this->grouplist;
    }

    /**
     * Return the list of cohorts.
     *
     * @return array
     */
    public function get_cohortlist(): array {
        if (is_null($this->cohortlist)) {
            $this->fetch_cohorts();
        }

        return $this->cohortlist;
    }

    /**
     * Class constructor that initializes Graph API client.
     *
     * @param unified $graphclient
     */
    public function __construct(unified $graphclient) {
        $this->graphclient = $graphclient;
        $this->grouplist = null;
        $this->cohortlist = null;
    }

    /**
     * Add a new mapping between a Microsoft group and a Moodle cohort.
     *
     * @param string $groupoid
     * @param int $cohortid
     * @return bool
     */
    public function add_mapping(string $groupoid, int $cohortid): bool {
        global $DB;

        if (!$groupoid || !$cohortid) {
            return false;
        }

        $existingmappings = $this->get_mappings();

        foreach ($existingmappings as $mapping) {
            // Prevent a Microsoft group to be mapped to multiple Moodle cohorts, or a Moodle cohort to be mapped to multiple
            // Microsoft groups.
            if ($mapping->objectid === $groupoid || $mapping->moodleid === $cohortid) {
                return false;
            }
        }

        $groupcache = $DB->get_record('local_o365_groups_cache', ['objectid' => $groupoid], '*', MUST_EXIST);

        $record = new stdClass();
        $record->type = 'group';
        $record->subtype = 'cohort';
        $record->objectid = $groupoid ?? null;
        $record->moodleid = $cohortid;
        $record->o365name = $groupcache->name ?? null;
        $record->tenant = '';
        $record->metadata = null;
        $record->timecreated = time();
        $record->timemodified = time();
        $DB->insert_record('local_o365_objects', $record);

        return true;
    }

    /**
     * Retrieve existing mappings from the database.
     *
     * @return array
     */
    public function get_mappings(): array {
        global $DB;

        $mappings = $DB->get_records('local_o365_objects', ['type' => 'group', 'subtype' => 'cohort']);

        return $mappings;
    }

    /**
     * Delete an existing mapping between a Microsoft group and a Moodle cohort.
     *
     * @param string $groupoid
     * @param int $cohortid
     * @return void
     */
    public function delete_mapping_by_group_oid_and_cohort_id(string $groupoid, int $cohortid): void {
        global $DB;

        $params = ['objectid' => $groupoid, 'moodleid' => $cohortid];
        $DB->delete_records('local_o365_objects', $params);
    }

    /**
     * Delete an existing mapping by its ID.
     *
     * @param int $id
     * @return void
     */
    public function delete_mapping_by_id(int $id): void {
        global $DB;

        $params = ['id' => $id];
        $DB->delete_records('local_o365_objects', $params);
    }

    /**
     * Fetch groups from the local Moodle cache.
     *
     * This function populates the $this->grouplist with the groups fetched from
     * the local Moodle cache.
     */
    public function fetch_groups_from_cache(): void {
        global $DB;

        $sql = 'SELECT *
                  FROM {local_o365_groups_cache}
                 WHERE not_found_since = 0';
        $records = $DB->get_records_sql($sql);

        $this->grouplist = [];
        foreach ($records as $record) {
            $this->grouplist[] = [
                'id' => $record->objectid,
                'displayName' => $record->name,
            ];
        }
    }

    /**
     * Fetch cohorts from the local Moodle cache.
     */
    public function fetch_cohorts(): void {
        $systemcontext = context_system::instance();
        $systemcohorts = cohort_get_cohorts($systemcontext->id, 0, 0);
        $this->cohortlist = $systemcohorts['cohorts'];
    }

    /**
     * Update Groups cache.
     *
     * @return bool
     */
    public function update_groups_cache(): bool {
        global $DB;

        if (utils::update_groups_cache($this->graphclient, 1)) {
            $sql = 'SELECT *
                      FROM {local_o365_groups_cache}
                     WHERE not_found_since = 0';
            $this->grouplist = $DB->get_records_sql($sql);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Synchronize the members of a Moodle cohort based on a Microsoft group.
     *
     * @param string $groupoid
     * @param int $cohortid
     * @return void
     */
    public function sync_members_by_group_oid_and_cohort_id(string $groupoid, int $cohortid): void {
        $groupownersandmembers = $this->get_group_owners_and_members($groupoid);

        if ($groupownersandmembers !== false) {
            $this->sync_cohort_members_by_cohort_id_and_microsoft_user_objects($cohortid, $groupownersandmembers);
        }
    }

    /**
     * Fetches members and owners of a specific Microsoft group.
     *
     * @param string $groupoid
     * @return array|false
     */
    public function get_group_owners_and_members(string $groupoid) {
        global $DB;

        $groupmembers = [];

        if (empty($this->graphclient)) {
            return false;
        }

        try {
            $memberrecords = $this->graphclient->get_group_members($groupoid);
            $ownerrecords = $this->graphclient->get_group_owners($groupoid);
        } catch (moodle_exception $e) {
            if (strpos($e->getMessage(), utils::RESOURCE_NOT_EXIST_ERROR) !== false) {
                $DB->delete_records('local_o365_objects', ['objectid' => $groupoid]);
                mtrace("...... Deleted mapping for non-existing group ID $groupoid.");
            } else {
                mtrace("...... Error fetching group members for group ID $groupoid: " . $e->getMessage());
            }

            return false;
        }

        foreach ($memberrecords as $memberrecord) {
            if (!array_key_exists($memberrecord['id'], $groupmembers)) {
                $groupmembers[$memberrecord['id']] = $memberrecord;
            }
        }
        foreach ($ownerrecords as $ownerrecord) {
            if (!array_key_exists($ownerrecord['id'], $groupmembers)) {
                $groupmembers[$ownerrecord['id']] = $ownerrecord;
            }
        }

        return $groupmembers;
    }

    /**
     * Manage the members of a Moodle cohort based on Microsoft group members.
     *
     * @param int $cohortid
     * @param array $microsoftuserobjects
     * @return void
     */
    private function sync_cohort_members_by_cohort_id_and_microsoft_user_objects(int $cohortid,
        array $microsoftuserobjects): void {
        global $DB;

        $microsoftuseroids = array_column($microsoftuserobjects, 'id');
        $currentmembers = $DB->get_records('cohort_members', ['cohortid' => $cohortid], '', 'userid');
        $connectedusers = $this->get_all_potential_user_details($microsoftuseroids, array_keys($currentmembers));

        $microsoftuseroidsflipped = array_flip($microsoftuseroids);
        $currentmemberlist = array_flip(array_keys($currentmembers));

        foreach ($connectedusers as $connecteduser) {
            $userid = $connecteduser->userid;
            $microsoftuseroid = $connecteduser->objectid;

            if (isset($microsoftuseroidsflipped[$microsoftuseroid]) && !isset($currentmemberlist[$userid])) {
                cohort_add_member($cohortid, $userid);
                mtrace("............ Added user with ID $userid, object ID $microsoftuseroid to cohort ID $cohortid.");
            }

            if (!isset($microsoftuseroidsflipped[$microsoftuseroid]) && isset($currentmemberlist[$userid])) {
                cohort_remove_member($cohortid, $userid);
                mtrace("............ Removed user with ID $userid, object ID $microsoftuseroid from cohort ID $cohortid.");
            }
        }
    }

    /**
     * Check the synchronization status of Azure usernames.
     *
     * @param array $microsoftuseroids
     * @param array $moodleuserids
     * @return array
     */
    private function get_all_potential_user_details(array $microsoftuseroids, array $moodleuserids): array {
        global $DB;

        if (empty($microsoftuseroids) && empty($moodleuserids)) {
            return [];
        }

        if (!empty($microsoftuseroids)) {
            [$microsoftuseroidsql, $microsoftuseroidparams] = $DB->get_in_or_equal($microsoftuseroids, SQL_PARAMS_NAMED,
                'microsoftuseroid');
        }

        if (!empty($moodleuserids)) {
            [$moodleuseridsql, $moodleuseridparams] = $DB->get_in_or_equal($moodleuserids, SQL_PARAMS_NAMED, 'moodleuserid');
        }

        $sql = "SELECT u.id AS userid, objects.objectid AS objectid
                  FROM {user} u
            INNER JOIN {local_o365_objects} objects ON objects.moodleid = u.id AND objects.type = :user
                 WHERE 1 = 1 ";
        $params = ['user' => 'user'];

        if (!empty($microsoftuseroids) && !empty($moodleuserids)) {
            $sql .= " AND (objects.objectid {$microsoftuseroidsql} OR objects.moodleid {$moodleuseridsql})";
            $params = array_merge($params, $microsoftuseroidparams, $moodleuseridparams);
        } else if (!empty($microsoftuseroids)) {
            $sql .= " AND objects.objectid {$microsoftuseroidsql}";
            $params = array_merge($params, $microsoftuseroidparams);
        } else if (!empty($moodleuserids)) {
            $sql .= " AND objects.moodleid {$moodleuseridsql}";
            $params = array_merge($params, $moodleuseridparams);
        }

        $connectedusers = $DB->get_records_sql($sql, $params);

        return $connectedusers;
    }

    /**
     * Get the name of a Microsoft group by its object ID.
     *
     * @param string $groupoid
     * @return string
     */
    public function get_group_name_by_group_oid(string $groupoid): string {
        $groupname = '';

        foreach ($this->grouplist as $group) {
            if ($group['id'] === $groupoid) {
                $groupname = $group['displayName'];
                break;
            }
        }

        return $groupname;
    }

    /**
     * Get the name of a Moodle cohort by its ID.
     *
     * @param int $cohortid
     * @return string
     */
    public function get_cohort_name_by_cohort_id(int $cohortid): string {
        $cohortname = '';

        if (is_null($this->cohortlist)) {
            $this->fetch_cohorts();
        }
        if (array_key_exists($cohortid, $this->cohortlist)) {
            $cohortname = $this->cohortlist[$cohortid]->name;
        }

        return $cohortname;
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
