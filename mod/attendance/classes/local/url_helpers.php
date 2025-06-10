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
 * Attendance module renderable component.
 *
 * @package    mod_attendance
 * @copyright  2022 Dan Marsden
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_attendance\local;

use mod_attendance_structure;

/**
 * Url helpers
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class url_helpers {
    /**
     * Url take.
     * @param mod_attendance_structure $att
     * @param int $sessionid
     * @param int $grouptype
     * @return mixed
     */
    public static function url_take($att, $sessionid, $grouptype) {
        $params = array('sessionid' => $sessionid);
        if (isset($grouptype)) {
            $params['grouptype'] = $grouptype;
        }

        return $att->url_take($params);
    }

    /**
     * Must be called without or with both parameters
     * @param mod_attendance_structure $att
     * @param null $sessionid
     * @param null $action
     * @return mixed
     */
    public static function url_sessions($att, $sessionid=null, $action=null) {
        if (isset($sessionid) && isset($action)) {
            $params = array('sessionid' => $sessionid, 'action' => $action);
        } else {
            $params = array();
        }

        return $att->url_sessions($params);
    }

    /**
     * Url view helper.
     * @param mod_attendance_structure $att
     * @param array $params
     * @return mixed
     */
    public static function url_view($att, $params=array()) {
        return $att->url_view($params);
    }
}
