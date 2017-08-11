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
 * Contains class core_user\output\user_roles_editable
 *
 * @package   core_user
 * @copyright 2017 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_user\output;

use context_course;
use core_user;
use core_external;
use coding_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Class to display list of user roles.
 *
 * @package   core_user
 * @copyright 2017 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_roles_editable extends \core\output\inplace_editable {

    /** @var $context */
    private $context = null;

    /** @var $courseroles Array */
    private $courseroles = null;

    /**
     * Constructor.
     *
     * @param \stdClass $course The current course
     * @param \context $context The course context
     * @param \stdClass $user The current user
     * @param \stdClass[] $courseroles The list of course roles.
     * @param \stdClass[] $assignableroles The list of assignable roles in this course.
     * @param array $value Array of role ids.
     */
    public function __construct($course, $context, $user, $courseroles, $assignableroles, $value) {
        // Check capabilities to get editable value.
        $editable = has_capability('moodle/role:assign', $context);

        // Invent an itemid.
        $itemid = $course->id . ':' . $user->id;

        $value = json_encode($value);

        // Remember these for the display value.
        $this->courseroles = $courseroles;
        $this->context = $context;

        parent::__construct('core_user', 'user_roles', $itemid, $editable, $value, $value);

        // Assignable roles.
        $options = $assignableroles;
        $this->edithint = get_string('xroleassignments', 'role', fullname($user));
        $this->editlabel = get_string('xroleassignments', 'role', fullname($user));

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
        $listofroles = [];
        $roleids = json_decode($this->value);
        foreach ($roleids as $id) {
            $listofroles[] = format_string($this->courseroles[$id]->localname, true, ['context' => $this->context]);
        }

        if (!empty($listofroles)) {
            $this->displayvalue = implode($listofroles, ', ');
        } else {
            $this->displayvalue = get_string('noroles', 'role');
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
        global $DB, $CFG;

        require_once($CFG->libdir . '/external/externallib.php');
        // Check caps.
        // Do the thing.
        // Return one of me.
        // Validate the inputs.
        list($courseid, $userid) = explode(':', $itemid, 2);

        $courseid = clean_param($courseid, PARAM_INT);
        $userid = clean_param($userid, PARAM_INT);
        $roleids = json_decode($newvalue);
        foreach ($roleids as $index => $roleid) {
            $roleids[$index] = clean_param($roleid, PARAM_INT);
        }

        // Check user is enrolled in the course.
        $context = context_course::instance($courseid);
        core_external::validate_context($context);

        // Check permissions.
        require_capability('moodle/role:assign', $context);

        if (!is_enrolled($context, $userid)) {
            throw new coding_exception('User does not belong to the course');
        }

        // Check that all the groups belong to the course.
        $allroles = role_fix_names(get_all_roles($context), $context);
        $assignableroles = get_assignable_roles($context, ROLENAME_ALIAS, false);
        $userroles = get_user_roles($context, $userid, true, 'c.contextlevel DESC, r.sortorder ASC');
        $ids = [];

        foreach ($userroles as $role) {
            $ids[$role->roleid] = $role->roleid;
        }

        $byid = [];
        foreach ($roleids as $roleid) {
            if (!isset($assignableroles[$roleid])) {
                throw new coding_exception('Role cannot be assigned in this course.');
            }
            $byid[$roleid] = $roleid;
        }
        $roleids = $byid;
        // Process adds.
        foreach ($roleids as $roleid) {
            if (!isset($ids[$roleid])) {
                // Add them.
                role_assign($roleid, $userid, $context);
                // Keep this variable in sync.
                $ids[$roleid] = $roleid;
            }
        }

        // Process removals.
        foreach ($assignableroles as $id => $role) {
            if (isset($ids[$id]) && !isset($roleids[$id])) {
                $ras = $DB->get_records('role_assignments', ['contextid' => $context->id, 'userid' => $userid, 'roleid' => $id]);
                $allremoved = true;
                foreach ($ras as $ra) {
                    if ($ra->component) {
                        if (strpos($ra->component, 'enrol_') !== 0) {
                            continue;
                        }
                        if (!$plugin = enrol_get_plugin(substr($ra->component, 6))) {
                            continue;
                        }
                        if ($plugin->roles_protected()) {
                            $allremoved = false;
                            continue;
                        }
                    }
                    role_unassign($ra->roleid, $ra->userid, $ra->contextid, $ra->component, $ra->itemid);
                }
                if ($allremoved) {
                    unset($ids[$id]);
                }
            }
        }

        $course = get_course($courseid);
        $user = core_user::get_user($userid);
        return new self($course, $context, $user, $allroles, $assignableroles, array_values(array_unique($ids)));
    }
}
