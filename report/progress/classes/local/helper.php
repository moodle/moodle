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

namespace report_progress\local;

/**
 * Helper for report progress.
 *
 * @package   report_progress
 * @copyright 2021 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL Juv3 or later
 */
class helper {

    /** The default number of results to be shown per page. */
    const COMPLETION_REPORT_PAGE = 25;

    /**
     * Get activities information by the activity include and activity order option.
     *
     * @param \completion_info $completion Completion information of course.
     * @param string $activityinclude Activity type for filtering.
     * @param string $activityorder Activity sort option.
     * @return array The available activity types and activities array after filtering and sorting.
     * @throws \coding_exception
     */
    public static function get_activities_to_show(\completion_info $completion, string $activityinclude,
            string $activityorder): array {
        // Get all activity types.
        $activities = $completion->get_activities();
        $availableactivitytypes = [];

        foreach ($activities as $activity) {
            $availableactivitytypes[$activity->modname] = $activity->get_module_type_name(true);
        }

        asort($availableactivitytypes);
        $availableactivitytypes = ['all' => get_string('allactivitiesandresources', 'report_progress')] +
            $availableactivitytypes;

        // Filter activities by type.
        if (!empty($activityinclude) && $activityinclude !== 'all') {
            $activities = array_filter($activities, function($activity) use ($activityinclude) {
                return $activity->modname === $activityinclude;
            });
        }

        // The activities are sorted by activity order on course page by default.
        if ($activityorder === 'alphabetical') {
            usort($activities, function($a, $b) {
                return strcmp($a->name, $b->name);
            });
        }

        return [$availableactivitytypes, $activities];
    }
}
