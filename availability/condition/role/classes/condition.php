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
 * Availability role - Condition
 *
 * @package    availability_role
 * @copyright  2015 Bence Laky, Synergy Learning UK <b.laky@intrallect.com>
 *             on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_role;

/**
 * Availability role - Condition class
 *
 * @package    availability_role
 * @copyright  2015 Bence Laky, Synergy Learning UK <b.laky@intrallect.com>
 *             on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class condition extends \core_availability\condition {
    /** @var int ID of role that this condition requires */
    protected $roleid = 0;

    /**
     * Constructor.
     *
     * @param \stdClass $structure Data structure from JSON decode
     * @throws \coding_exception If invalid data structure.
     */
    public function __construct($structure) {
        // Get role id.
        if (isset($structure->id) && is_int($structure->id)) {
            $this->roleid = $structure->id;
        } else {
            throw new \coding_exception('Invalid ->id for role condition');
        }
    }

    /**
     * Save.
     *
     * @return object|\stdClass $result
     */
    public function save() {
        $result = (object)array('type' => 'role');
        if ($this->roleid) {
            $result->id = $this->roleid;
        } else {
            $result->activity = true;
        }
        return $result;
    }

    /**
     * Adding the availability to restored course items.
     *
     * @param string       $restoreid
     * @param int          $courseid
     * @param \base_logger $logger
     * @param string       $name
     *
     * @return bool
     * @throws \dml_exception
     */
    public function update_after_restore($restoreid, $courseid, \base_logger $logger, $name) {
        global $DB;
        if (!$this->roleid) {
            // If using 'same as activity' option, no need to change it.
            return false;
        }
        $rec = \restore_dbops::get_backup_ids_record($restoreid, 'role', $this->roleid);
        if (!$rec || !$rec->newitemid) {
            // If we are on the same course (e.g. duplicate) then we can just
            // use the existing one.
            if ($DB->record_exists('role', array('id' => $this->roleid))) {
                return false;
            }
            // Otherwise it's a warning.
            $this->roleid = -1;
            $logger->process('Restored item ('.$name.') has availability condition on a role that was not restored',
                    \backup::LOG_WARNING);
        } else {
            $this->roleid = (int)$rec->newitemid;
        }

        return true;
    }

    /**
     * Check if the item is available with this restriction.
     *
     * @param bool                    $not
     * @param \core_availability\info $info
     * @param bool                    $grabthelot
     * @param int                     $userid
     *
     * @return bool
     * @throws \coding_exception
     */
    public function is_available($not, \core_availability\info $info, $grabthelot, $userid) {
        global $USER, $CFG;
        $context = \context_course::instance($info->get_course()->id);
        $allow = false;

        // Is the user's course role switched?
        if (!empty($USER->access['rsw'][$context->path])) {
            // Check only switched role.
            if ($USER->access['rsw'][$context->path] == $this->roleid) {
                $allow = true;
            }
            // Or is the user currently having his own role(s)?
        } else {
            // Check all of the user's course roles.
            foreach (get_user_roles($context, $userid) as $role) {
                if ($role->roleid == $this->roleid) {
                    $allow = true;
                    break;
                }
            }

            // As get_user_roles only returns roles for enrolled users, we have to check whether a user
            // is viewing the course as guest or is not logged in separately.

            // Is the user not logged in?
            if (empty($userid) || isguestuser($userid)) {
                if ($CFG->notloggedinroleid == $this->roleid) {
                    $allow = true;
                }
            }

            // Is the user viewing the course as guest?
            if (is_guest($context, $userid)) {
                if (get_guest_role()->id == $this->roleid) {
                    $allow = true;
                }
            }
        }

        if ($not) {
            $allow = !$allow;
        }
        return $allow;
    }

    /**
     * Retrieve the description for the restriction.
     *
     * @param bool                    $full
     * @param bool                    $not
     * @param \core_availability\info $info
     *
     * @return string
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_description($full, $not, \core_availability\info $info) {
        global $DB;
        $context = \context_course::instance($info->get_course()->id);
        $role = $DB->get_record('role', array('id' => $this->roleid));
        if (!$role) {
            $missing = get_string('missing', 'availability_role');
            return get_string($not ? 'requires_notrole' : 'requires_role', 'availability_role', $missing);
        } else {
            $name = role_get_name($role, $context);
            return get_string($not ? 'requires_notrole' : 'requires_role', 'availability_role', $name);
        }
    }

    /**
     * Retrieve debugging string.
     *
     * @return string
     */
    public function get_debug_string() {
        return "Role id: $this->roleid";
    }
}
