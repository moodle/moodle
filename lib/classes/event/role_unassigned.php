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

/**
 * Role unassigned event.
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class role_unassigned extends base {
    protected function init() {
        $this->data['crud'] = 'd';
        // TODO: MDL-37658 set level
        $this->data['level'] = 50;
    }

    /**
     * Returns localised general event name.
     *
     * @return string|\lang_string
     */
    public static function get_name() {
        //TODO: MDL-37658 localise
        return 'Role unassigned';
    }

    /**
     * Returns localised description of what happened.
     *
     * @return string|\lang_string
     */
    public function get_description() {
        //TODO: MDL-37658 localise
        return 'Role '.$this->objectid.'was unassigned from user '.$this->relateduserid.' in context '.$this->contextid;
    }

    /**
     * Returns relevant URL.
     * @return \moodle_url
     */
    public function get_url() {
        return new moodle_url('/admin/roles/assign.php', array('contextid'=>$this->contextid, 'roleid'=>$this->objectid));
    }

    /**
     * Does this event replace legacy event?
     *
     * @return null|string legacy event name
     */
    public function get_legacy_eventname() {
        return 'role_unassigned';
    }

    /**
     * Legacy event data if get_legacy_eventname() is not empty.
     *
     * @return mixed
     */
    public function get_legacy_eventdata() {
        return $this->get_cached_record('role_assignments', $this->data['other']['id']);
    }
}
