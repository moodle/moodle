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
 * Cohort deleted event.
 *
 * @package    core
 * @copyright  2013 Dan Poltawski <dan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class cohort_deleted extends base {
    protected function init() {
        $this->data['crud'] = 'd';
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
        return 'Cohort deleted';
    }

    /**
     * Returns localised description of what happened.
     *
     * @return string|\lang_string
     */
    public function get_description() {
        //TODO: localise
        return 'Cohort '.$this->objectid.' was deleted by '.$this->userid.' from context '.$this->contextid;
    }

    /**
     * Returns relevant URL.
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/cohort/index.php', array('contextid' => $this->contextid));
    }

    /**
     * Does this event replace legacy event?
     *
     * @return null|string legacy event name
     */
    public function get_legacy_eventname() {
        return 'cohort_deleted';
    }

    /**
     * Legacy event data if get_legacy_eventname() is not empty.
     *
     * @return mixed
     */
    public function get_legacy_eventdata() {
        return $this->get_record_snapshot('cohort', $this->objectid);
    }
}
