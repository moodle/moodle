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
 * Contains class core_group\output\user_groups_editable
 *
 * @package   core_group
 * @copyright 2017 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_group\output;

use context_course;
use core_user;
use core_external;
use coding_exception;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/group/lib.php');

/**
 * Class to display list of user groups.
 *
 * @package   core_group
 * @copyright 2017 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_groups_editable extends \core\output\inplace_editable {

    /** @var $coursegroups */
    private $coursegroups = null;
    /** @var $context */
    private $context = null;

    /**
     * Constructor.
     *
     * @param \stdClass $course The current course
     * @param \context $context The course context
     * @param \stdClass $user The current user
     * @param \stdClass[] $coursegroups The list of course groups from groups_get_all_groups with membership.
     * @param array $value Array of groupids.
     */
    public function __construct($course, $context, $user, $coursegroups, $value) {
        // Check capabilities to get editable value.
        $editable = has_capability('moodle/course:managegroups', $context) && !empty($coursegroups);

        // Invent an itemid.
        $itemid = $course->id . ':' . $user->id;

        $value = json_encode($value);

        // Remember these for the display value.
        $this->coursegroups = $coursegroups;
        $this->context = $context;

        parent::__construct('core_group', 'user_groups', $itemid, $editable, $value, $value);

        // Assignable groups.
        $options = [];

        foreach ($coursegroups as $group) {
            $options[$group->id] = format_string($group->name, true, ['context' => $this->context]);
        }
        $this->edithint = get_string('editusersgroupsa', 'group', fullname($user));
        $this->editlabel = get_string('editusersgroupsa', 'group', fullname($user));

        $attributes = ['multiple' => true];
        $this->set_type_autocomplete($options, $attributes);
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(\renderer_base $output) {
        $listofgroups = [];
        $groupids = json_decode($this->value);
        foreach ($groupids as $id) {
            $listofgroups[] = format_string($this->coursegroups[$id]->name, true, ['context' => $this->context]);
        }

        if (!empty($listofgroups)) {
            $this->displayvalue = implode($listofgroups, ', ');
        } else {
            $this->displayvalue = get_string('groupsnone');
        }
        return parent::export_for_template($output);
    }

    /**
     * Updates the value in database and returns itself, called from inplace_editable callback
     *
     * @param int $itemid
     * @param mixed $newvalue
     * @return \self
     */
    public static function update($itemid, $newvalue) {
        // Check caps.
        // Do the thing.
        // Return one of me.
        // Validate the inputs.
        list($courseid, $userid) = explode(':', $itemid, 2);

        $courseid = clean_param($courseid, PARAM_INT);
        $userid = clean_param($userid, PARAM_INT);
        $groupids = json_decode($newvalue);
        foreach ($groupids as $index => $groupid) {
            $groupids[$index] = clean_param($groupid, PARAM_INT);
        }

        // Check user is enrolled in the course.
        $context = context_course::instance($courseid);
        core_external::validate_context($context);

        if (!is_enrolled($context, $userid)) {
            throw new coding_exception('User does not belong to the course');
        }

        // Check that all the groups belong to the course.
        $coursegroups = groups_get_all_groups($courseid, 0, 0, 'g.*', true);

        $byid = [];
        foreach ($groupids as $groupid) {
            if (!isset($coursegroups[$groupid])) {
                throw new coding_exception('Group does not belong to the course');
            }
            $byid[$groupid] = $groupid;
        }
        $groupids = $byid;
        // Check permissions.
        require_capability('moodle/course:managegroups', $context);

        // Process adds.
        foreach ($groupids as $groupid) {
            if (!isset($coursegroups[$groupid]->members[$userid])) {
                // Add them.
                groups_add_member($groupid, $userid);
                // Keep this variable in sync.
                $coursegroups[$groupid]->members[$userid] = $userid;
            }
        }

        // Process removals.
        foreach ($coursegroups as $groupid => $group) {
            if (isset($group->members[$userid]) && !isset($groupids[$groupid])) {
                if (groups_remove_member_allowed($groupid, $userid)) {
                    groups_remove_member($groupid, $userid);
                    unset($coursegroups[$groupid]->members[$userid]);
                } else {
                    $groupids[$groupid] = $groupid;
                }
            }
        }

        $course = get_course($courseid);
        $user = core_user::get_user($userid);
        return new self($course, $context, $user, $coursegroups, array_values($groupids));
    }
}
