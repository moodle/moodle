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
 * User removed from a cohort event.
 *
 * @package    core
 * @copyright  2013 Dan Poltawski <dan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;
defined('MOODLE_INTERNAL') || die();

/**
 * User removed from a cohort event class.
 *
 * @package    core
 * @since      Moodle 2.6
 * @copyright  2013 Dan Poltawski <dan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class cohort_member_removed extends base {

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'cohort';
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventcohortmemberremoved', 'core_cohort');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with the id '$this->relateduserid' was removed from the cohort with the id '$this->objectid' by the " .
            "user with the id '$this->userid'.";
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/cohort/assign.php', array('id' => $this->objectid));
    }

    /**
     * Return legacy event name.
     *
     * @return string legacy event name.
     */
    public static function get_legacy_eventname() {
        return 'cohort_member_removed';
    }

    /**
     * Return legacy event data.
     *
     * @return stdClass
     */
    protected function get_legacy_eventdata() {
        $data = new \stdClass();
        $data->cohortid = $this->objectid;
        $data->userid = $this->relateduserid;
        return $data;
    }
}
