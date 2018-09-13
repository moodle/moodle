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
 * This file contains an event for when self-marking is blocked because
 * another student used the same IP address to self-mark.
 *
 * @package mod_attendance
 * @author Dan Marsden <dan@danmarsden.com>
 * @copyright 2018 Catalyst IT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_attendance\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Event for when self-marking is blocked
 *
 * @property-read array $other {
 *                Extra information about event properties.
 *
 *                string mode Mode of the report viewed.
 *                }
 * @package mod_attendance
 * @author Dan Marsden <dan@danmarsden.com>
 * @copyright 2018 Catalyst IT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class session_ip_shared extends \core\event\base {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'attendance_log';
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return 'User with id ' . $this->userid . ' was blocked from taking attendance for sessionid: ' . $this->other['sessionid'] .
               ' because user with id '.$this->other['otheruser'] . ' previously marked attendance with the same IP address.';
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventsessionipshared', 'mod_attendance');
    }

    /**
     * Get URL related to the action
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/attendance/attendance.php');
    }

    /**
     * Get objectid mapping
     *
     * @return array of parameters for object mapping.
     */
    public static function get_objectid_mapping() {
        return array(
            'db' => 'attendance',
            'restore' => 'attendance'
        );
    }
}
