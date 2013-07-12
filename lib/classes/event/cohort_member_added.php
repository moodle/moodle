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
 * User added to a cohort
 *
 * @package    core
 * @copyright  2013 Dan Poltawski <dan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class cohort_member_added extends base {
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['level'] = 50;
        $this->data['objecttable'] = 'cohort';
    }

    /**
     * Returns localised general event name.
     *
     * @return string|\lang_string
     */
    public static function get_name() {
        //TODO: localise
        return 'User added to a cohort';
    }

    /**
     * Returns localised description of what happened.
     *
     * @return string|\lang_string
     */
    public function get_description() {
        //TODO: localise
        return 'User '.$this->relateduserid.' was added to cohort '.$this->objectid.' by:'.$this->userid;
    }

    /**
     * Returns relevant URL.
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/cohort/assign.php', array('id' => $this->objectid));
    }

    /**
     * Does this event replace legacy event?
     *
     * @return null|string legacy event name
     */
    public function get_legacy_eventname() {
        return 'cohort_member_added';
    }

    /**
     * Legacy event data if get_legacy_eventname() is not empty.
     *
     * @return mixed
     */
    public function get_legacy_eventdata() {
        $data = new \stdClass();
        $data->cohortid = $this->objectid;
        $data->userid = $this->relateduserid;
        return $data;
    }
}
