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
 * Condition main class.
 *
 * @package availability_grouping
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_grouping;

defined('MOODLE_INTERNAL') || die();

/**
 * Condition main class.
 *
 * @package availability_grouping
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class condition extends \core_availability\condition {
    /** @var array Array from grouping id => name */
    protected static $groupingnames = array();

    /** @var int ID of grouping that this condition requires */
    protected $groupingid = 0;

    /** @var bool If true, indicates that activity $cm->grouping is used */
    protected $activitygrouping = false;

    /**
     * Constructor.
     *
     * @param \stdClass $structure Data structure from JSON decode
     * @throws \coding_exception If invalid data structure.
     */
    public function __construct($structure) {
        // Get grouping id.
        if (isset($structure->id)) {
            if (is_int($structure->id)) {
                $this->groupingid = $structure->id;
            } else {
                throw new \coding_exception('Invalid ->id for grouping condition');
            }
        } else if (isset($structure->activity)) {
            if (is_bool($structure->activity) && $structure->activity) {
                $this->activitygrouping = true;
            } else {
                throw new \coding_exception('Invalid ->activity for grouping condition');
            }
        } else {
            throw new \coding_exception('Missing ->id / ->activity for grouping condition');
        }
    }

    public function save() {
        $result = (object)array('type' => 'grouping');
        if ($this->groupingid) {
            $result->id = $this->groupingid;
        } else {
            $result->activity = true;
        }
        return $result;
    }

    public function is_available($not, \core_availability\info $info, $grabthelot, $userid) {
        $context = \context_course::instance($info->get_course()->id);
        $allow = true;
        if (!has_capability('moodle/site:accessallgroups', $context, $userid)) {
            // If the activity has 'group members only' and you don't have accessallgroups...
            $groups = $info->get_modinfo()->get_groups($this->get_grouping_id($info));
            if (!$groups) {
                // ...and you don't belong to a group, then set it so you can't see/access it.
                $allow = false;
            }

            // The NOT condition applies before accessallgroups (i.e. if you
            // set something to be available to those NOT in grouping X,
            // people with accessallgroups can still access it even if
            // they are in grouping X).
            if ($not) {
                $allow = !$allow;
            }
        }
        return $allow;
    }

    /**
     * Gets the actual grouping id for the condition. This is either a specified
     * id, or a special flag indicating that we use the one for the current cm.
     *
     * @param \core_availability\info $info Info about context cm
     * @return int Grouping id
     * @throws \coding_exception If it's set to use a cm but there isn't grouping
     */
    protected function get_grouping_id(\core_availability\info $info) {
        if ($this->activitygrouping) {
            $groupingid = $info->get_course_module()->groupingid;
            if (!$groupingid) {
                throw new \coding_exception(
                        'Not supposed to be able to turn on activitygrouping when no grouping');
            }
            return $groupingid;
        } else {
            return $this->groupingid;
        }
    }

    public function get_description($full, $not, \core_availability\info $info) {
        global $DB;
        $course = $info->get_course();

        // Need to get the name for the grouping. Unfortunately this requires
        // a database query. To save queries, get all groupings for course at
        // once in a static cache.
        $groupingid = $this->get_grouping_id($info);
        if (!array_key_exists($groupingid, self::$groupingnames)) {
            $coursegroupings = $DB->get_records(
                    'groupings', array('courseid' => $course->id), '', 'id, name');
            foreach ($coursegroupings as $rec) {
                self::$groupingnames[$rec->id] = $rec->name;
            }
        }

        // If it still doesn't exist, it must have been misplaced.
        if (!array_key_exists($groupingid, self::$groupingnames)) {
            $name = get_string('missing', 'availability_grouping');
        } else {
            $context = \context_course::instance($course->id);
            $name = format_string(self::$groupingnames[$groupingid], true,
                    array('context' => $context));
        }

        return get_string($not ? 'requires_notgrouping' : 'requires_grouping',
                'availability_grouping', $name);
    }

    protected function get_debug_string() {
        if ($this->activitygrouping) {
            return 'CM';
        } else {
            return '#' . $this->groupingid;
        }
    }

    /**
     * Include this condition only if we are including groups in restore, or
     * if it's a generic 'same activity' one.
     *
     * @param int $restoreid The restore Id.
     * @param int $courseid The ID of the course.
     * @param base_logger $logger The logger being used.
     * @param string $name Name of item being restored.
     * @param base_task $task The task being performed.
     *
     * @return Integer groupid
     */
    public function include_after_restore($restoreid, $courseid, \base_logger $logger,
            $name, \base_task $task) {
        return !$this->groupingid || $task->get_setting_value('groups');
    }

    public function update_after_restore($restoreid, $courseid, \base_logger $logger, $name) {
        global $DB;
        if (!$this->groupingid) {
            // If using 'same as activity' option, no need to change it.
            return false;
        }
        $rec = \restore_dbops::get_backup_ids_record($restoreid, 'grouping', $this->groupingid);
        if (!$rec || !$rec->newitemid) {
            // If we are on the same course (e.g. duplicate) then we can just
            // use the existing one.
            if ($DB->record_exists('groupings',
                    array('id' => $this->groupingid, 'courseid' => $courseid))) {
                return false;
            }
            // Otherwise it's a warning.
            $this->groupingid = -1;
            $logger->process('Restored item (' . $name .
                    ') has availability condition on grouping that was not restored',
                    \backup::LOG_WARNING);
        } else {
            $this->groupingid = (int)$rec->newitemid;
        }
        return true;
    }

    public function update_dependency_id($table, $oldid, $newid) {
        if ($table === 'groupings' && (int)$this->groupingid === (int)$oldid) {
            $this->groupingid = $newid;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Wipes the static cache used to store grouping names.
     */
    public static function wipe_static_cache() {
        self::$groupingnames = array();
    }

    public function is_applied_to_user_lists() {
        // Grouping conditions are assumed to be 'permanent', so they affect the
        // display of user lists for activities.
        return true;
    }

    public function filter_user_list(array $users, $not, \core_availability\info $info,
            \core_availability\capability_checker $checker) {
        global $CFG, $DB;

        // If the array is empty already, just return it.
        if (!$users) {
            return $users;
        }

        // List users for this course who match the condition.
        $groupingusers = $DB->get_records_sql("
                SELECT DISTINCT gm.userid
                  FROM {groupings_groups} gg
                  JOIN {groups_members} gm ON gm.groupid = gg.groupid
                 WHERE gg.groupingid = ?",
                array($this->get_grouping_id($info)));

        // List users who have access all groups.
        $aagusers = $checker->get_users_by_capability('moodle/site:accessallgroups');

        // Filter the user list.
        $result = array();
        foreach ($users as $id => $user) {
            // Always include users with access all groups.
            if (array_key_exists($id, $aagusers)) {
                $result[$id] = $user;
                continue;
            }
            // Other users are included or not based on grouping membership.
            $allow = array_key_exists($id, $groupingusers);
            if ($not) {
                $allow = !$allow;
            }
            if ($allow) {
                $result[$id] = $user;
            }
        }
        return $result;
    }

    /**
     * Returns a JSON object which corresponds to a condition of this type.
     *
     * Intended for unit testing, as normally the JSON values are constructed
     * by JavaScript code.
     *
     * @param int $groupingid Required grouping id (0 = grouping linked to activity)
     * @return stdClass Object representing condition
     */
    public static function get_json($groupingid = 0) {
        $result = (object)array('type' => 'grouping');
        if ($groupingid) {
            $result->id = (int)$groupingid;
        } else {
            $result->activity = true;
        }
        return $result;
    }

    public function get_user_list_sql($not, \core_availability\info $info, $onlyactive) {
        global $DB;

        // Get enrolled users with access all groups. These always are allowed.
        list($aagsql, $aagparams) = get_enrolled_sql(
                $info->get_context(), 'moodle/site:accessallgroups', 0, $onlyactive);

        // Get all enrolled users.
        list ($enrolsql, $enrolparams) =
                get_enrolled_sql($info->get_context(), '', 0, $onlyactive);

        // Condition for specified or any group.
        $matchparams = array();
        $matchsql = "SELECT 1
                       FROM {groups_members} gm
                       JOIN {groupings_groups} gg ON gg.groupid = gm.groupid
                      WHERE gm.userid = userids.id
                            AND gg.groupingid = " .
                self::unique_sql_parameter($matchparams, $this->get_grouping_id($info));

        // Overall query combines all this.
        $condition = $not ? 'NOT' : '';
        $sql = "SELECT userids.id
                  FROM ($enrolsql) userids
                 WHERE (userids.id IN ($aagsql)) OR $condition EXISTS ($matchsql)";
        return array($sql, array_merge($enrolparams, $aagparams, $matchparams));
    }
}
