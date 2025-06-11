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
 * Deadlines refresh task.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\task;
use context_course;
use core\task\scheduled_task;
use core_date;
use theme_snap\activity;

/**
 * Deadlines refresh task class.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class refresh_deadline_caches_task extends scheduled_task {

    private $cachekeys;

    /**
     * {@inheritDoc}
     */
    public function get_name() {
        return get_string('refreshdeadlinestask', 'theme_snap');
    }

    /**
     * {@inheritDoc}
     */
    public function execute() {
        global $DB, $CFG;

        // Purge deadlines cache, previous dates will not be queried again.
        $muc = \cache::make('theme_snap', 'activity_deadlines');
        $muc->purge();

        // Reset query count.
        activity::reset_deadline_query_count();

        if (empty(get_config('theme_snap', 'refreshdeadlines'))) {
            mtrace(get_string('refreshdeadlinestaskoff', 'theme_snap'));
            // Skip, setting is off.
            return;
        }

        $lastlogindateformat = empty($CFG->theme_snap_refresh_deadlines_last_login) ?
            '6 months ago' : $CFG->theme_snap_refresh_deadlines_last_login;

        $maxduration = empty($CFG->theme_snap_refresh_deadlines_max_duration) ?
            (6 * HOURSECS) : $CFG->theme_snap_refresh_deadlines_max_duration;
        $starttime = time();

        // Fill deadlines for users who logged in yesterday.
        $query                    = <<<SQL
  SELECT u.id, u.lastlogin
    FROM {user} u
   WHERE u.deleted = :deleted
     AND u.lastlogin >= :lastlogints
ORDER BY u.lastlogin DESC
SQL;
        $lastlogindate            = new \DateTime($lastlogindateformat, core_date::get_server_timezone_object());
        $lastlogints              = $lastlogindate->getTimestamp();
        $users                    = $DB->get_recordset_sql($query, [
            'deleted'             => 0,
            'lastlogints'         => strtotime(date('Y-m-d', $lastlogints)),
        ]);
        $blockinstances           = []; // Local cache of instances in courses.
        $snapfeedsdeadlinesconfig = base64_encode(serialize((object) [
            'feedtype' => 'deadlines',
        ]));
        $snapfeedsblockexists     = (get_config('block_snapfeeds') !== false) ||
            (is_callable('mr_on') && mr_on('snapfeeds', 'block'));

        $this->cachekeys = [];
        // We should skip CM checks to only populate caches for events.
        // This flag should only be used for testing.
        $skipcmchecks = empty($CFG->theme_snap_include_cm_checks_in_deadlines_task);
        foreach ($users as $userid => $user) {
            if ((time() - $starttime) > $maxduration) {
                // Max duration reached. Bye bye.
                break;
            }

            $courses = enrol_get_users_courses($userid, true);

            if ($this->has_or_add_cachekey($user, $courses)) {
                continue;
            }

            // This populates deadline caches or does nothing if run the same day.
            activity::upcoming_deadlines($userid, 500, 0, $skipcmchecks);

            if (!$snapfeedsblockexists) {
                // No need to populate deadline data for courses if the block is not present.
                continue;
            }

            // Give a helping hand populating caches for course snap feeds blocks.
            foreach ($courses as $courseid => $course) {
                if ((time() - $starttime) > $maxduration) {
                    // Max duration reached. Bye bye.
                    break 2;
                }

                if ($this->has_or_add_cachekey($user, [$courseid => $course])) {
                    continue;
                }

                if (!isset($blockinstances[$course->id])) {
                    $contextcourse = context_course::instance($course->id);
                    $parentcontextid = $contextcourse->id;
                    $query = <<<SQL
   SELECT *
     FROM {block_instances}
    WHERE blockname = :blockname
      AND parentcontextid = :parentcontextid
      AND configdata = :configdata
SQL;

                    $blockinstances[$course->id] = $DB->record_exists_sql($query, [
                        'blockname'       => 'snapfeeds',
                        'parentcontextid' => $parentcontextid,
                        'configdata'      => $snapfeedsdeadlinesconfig,
                    ]);
                }

                if ($blockinstances[$course->id]) {
                    activity::upcoming_deadlines($userid, 500, $course, $skipcmchecks);
                }
            }
        }
        $users->close();
    }

    /**
     * Looks for a key and adds it to the index if not present.
     * @param \stdClass[] $courses
     * @return bool true if present, false if had to add it.
     */
    private function has_or_add_cachekey($user, array $courses): bool {
        // Cache key HAS to have courses.
        $cachekey = activity::get_id_indexed_array_cache_key($courses);

        // It also can have group ids for this user within the courses.
        $groupkey = activity::get_user_group_cache_key($user, $courses);
        if (!empty($groupkey)) {
            $cachekey .= '_' . $groupkey;
        }

        if (isset($this->cachekeys[$cachekey])) {
            // Cache is already populated for this user and their courses.
            return true;
        }

        $this->cachekeys[$cachekey] = true;
        return false;
    }
}
