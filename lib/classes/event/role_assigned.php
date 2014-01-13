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

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Role assigned event.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      @type int id role assigned id.
 *      @type string component name of component.
 *      @type int itemid id of item.
 * }
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class role_assigned extends base {
    protected function init() {
        $this->data['objecttable'] = 'role';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventroleassigned', 'role');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        return 'Role '.$this->objectid.' was assigned to user '.$this->relateduserid.' in context '.$this->contextid;
    }

    /**
     * Returns relevant URL.
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/admin/roles/assign.php', array('contextid' => $this->contextid, 'roleid' => $this->objectid));
    }

    /**
     * Does this event replace legacy event?
     *
     * @return null|string legacy event name
     */
    public static function get_legacy_eventname() {
        return 'role_assigned';
    }

    /**
     * Legacy event data if get_legacy_eventname() is not empty.
     *
     * @return mixed
     */
    protected function get_legacy_eventdata() {
        return $this->get_record_snapshot('role_assignments', $this->data['other']['id']);
    }

    /**
     * Returns array of parameters to be passed to legacy add_to_log() function.
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        $roles = get_all_roles();
        $rolenames = role_fix_names($roles, $this->get_context(), ROLENAME_ORIGINAL, true);
        return array($this->courseid, 'role', 'assign', 'admin/roles/assign.php?contextid='.$this->contextid.'&roleid='.$this->objectid,
                $rolenames[$this->objectid], '', $this->userid);
    }
}
