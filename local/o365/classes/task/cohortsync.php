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
 * A scheduled task to process Microsoft group and Moodle cohort mapping.
 *
 * @package     local_o365
 * @copyright   Enovation Solutions Ltd. {@link https://enovation.ie}
 * @author      Patryk Mroczko <patryk.mroczko@enovation.ie>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_o365\task;

use core\exception\moodle_exception;
use core\task\scheduled_task;
use local_o365\feature\cohortsync\main;
use local_o365\utils;

/**
 * A scheduled task to process Microsoft group and Moodle cohort mapping.
 */
class cohortsync extends scheduled_task {
    /**
     * Get the name of the task.
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('cohortsync_taskname', 'local_o365');
    }

    /**
     * Execute the scheduled task.
     *
     * @return bool
     */
    public function execute(): bool {
        try {
            $graphclient = main::get_unified_api(__METHOD__);
            if (empty($graphclient)) {
                utils::mtrace("Failed to get Graph API client. Exiting.", 1);

                return true;
            }
        } catch (moodle_exception $e) {
            utils::mtrace("Failed to get Graph API client. Exiting.", 1);

            return true;
        }

        $cohortsyncmain = new main($graphclient);
        $this->execute_sync($cohortsyncmain);

        return true;
    }

    /**
     * Execute synchronization.
     *
     * @param main $cohortsync
     * @return void
     */
    private function execute_sync(main $cohortsync): void {
        if ($cohortsync->update_groups_cache()) {
            utils::clean_up_not_found_groups();
        } else {
            utils::mtrace("Failed to update groups cache. Exiting.", 1);

            return;
        }

        utils::mtrace("Start processing cohort mappings.", 1);
        $grouplist = $cohortsync->get_grouplist();
        utils::mtrace("Found " . count($grouplist) . " groups.", 2);
        $grouplistbyoid = [];
        foreach ($grouplist as $group) {
            $grouplistbyoid[$group->objectid] = $group;
        }

        $mappings = $cohortsync->get_mappings();

        if (empty($mappings)) {
            utils::mtrace("No mappings found. Nothing to process. Exiting.", 1);
            utils::mtrace("", 1);

            return;
        }
        utils::mtrace("Found " . count($mappings) . " mappings.", 2);

        $cohorts = $cohortsync->get_cohortlist();

        foreach ($mappings as $key => $mapping) {
            // Verify that the group still exists.
            if (!in_array($mapping->objectid, array_keys($grouplistbyoid))) {
                $cohortsync->delete_mapping_by_group_oid_and_cohort_id($mapping->objectid, $mapping->moodleid);
                utils::mtrace("Deleted mapping for non-existing group ID {$mapping->objectid}.", 3);
                unset($mappings[$key]);
            }

            // Verify that the cohort still exists.
            if (!in_array($mapping->moodleid, array_keys($cohorts))) {
                $cohortsync->delete_mapping_by_group_oid_and_cohort_id($mapping->objectid, $mapping->moodleid);
                utils::mtrace("Deleted mapping for non-existing cohort ID {$mapping->moodleid}.", 3);
                unset($mappings[$key]);
            }
        }

        foreach ($mappings as $mapping) {
            utils::mtrace("Processing mapping for group ID {$mapping->objectid} and cohort ID {$mapping->moodleid}.", 3);
            $cohortsync->sync_members_by_group_oid_and_cohort_id($mapping->objectid, $mapping->moodleid);
        }
    }
}
