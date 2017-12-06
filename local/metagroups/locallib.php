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
 * @package    local_metagroups
 * @copyright  2014 Paul Holden (pholden@greenhead.ac.uk)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/grouplib.php');
require_once($CFG->dirroot . '/group/lib.php');

/**
 * Get a list of parent courses for a given course ID
 *
 * @param int|null $courseid or null for all parents
 * @return array of course IDs
 */
function local_metagroups_parent_courses($courseid = null) {
    global $DB;

    $conditions = array('enrol' => 'meta', 'status' => ENROL_INSTANCE_ENABLED);
    if ($courseid !== null) {
        $conditions['customint1'] = $courseid;
    }

    return $DB->get_records_menu('enrol', $conditions, 'sortorder', 'id, courseid');
}

/**
 * Get a list of all child courses for a given course ID
 *
 * @param int $courseid
 * @return array of course IDs
 */
function local_metagroups_child_courses($courseid) {
    global $DB;

    $conditions = array('enrol' => 'meta', 'courseid' => $courseid, 'status' => ENROL_INSTANCE_ENABLED);

    return $DB->get_records_menu('enrol', $conditions, 'sortorder', 'id, customint1');
}

/**
 * Run synchronization process
 *
 * @param progress_trace $trace
 * @param int|null $courseid or null for all courses
 * @return void
 */
function local_metagroups_sync(progress_trace $trace, $courseid = null) {
    global $DB;

    if ($courseid !== null) {
        $courseids = array($courseid);
    } else {
        $courseids = local_metagroups_parent_courses();
    }

    foreach (array_unique($courseids) as $courseid) {
        $parent = get_course($courseid);

        // If parent course doesn't use groups, we can skip synchronization.
        if (groups_get_course_groupmode($parent) == NOGROUPS) {
            continue;
        }

        $trace->output($parent->fullname, 1);

        $children = local_metagroups_child_courses($parent->id);
        foreach ($children as $childid) {
            $child = get_course($childid);
            $trace->output($child->fullname, 2);

            $groups = groups_get_all_groups($child->id);
            foreach ($groups as $group) {
                if (! $metagroup = $DB->get_record('groups', array('courseid' => $parent->id, 'idnumber' => $group->id))) {
                    $metagroup = new stdClass();
                    $metagroup->courseid = $parent->id;
                    $metagroup->idnumber = $group->id;
                    $metagroup->name = $group->name;

                    $metagroup->id = groups_create_group($metagroup, false, false);
                }

                $trace->output($metagroup->name, 3);

                $users = groups_get_members($group->id);
                foreach ($users as $user) {
                    groups_add_member($metagroup->id, $user->id, 'local_metagroups', $group->id);
                }
            }
        }
    }
}
