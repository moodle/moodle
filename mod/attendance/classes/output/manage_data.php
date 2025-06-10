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

namespace mod_attendance\output;

use renderable;
use mod_attendance\local\url_helpers;
use mod_attendance_structure;

/**
 * Represents info about attendance sessions taking into account view parameters.
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manage_data implements renderable {
    /** @var array of sessions*/
    public $sessions;

    /** @var int number of hidden sessions (sessions before $course->startdate)*/
    public $hiddensessionscount;
    /** @var array  */
    public $groups;
    /** @var  int */
    public $hiddensesscount;

    /** @var mod_attendance_structure */
    public $att;
    /**
     * Prepare info about attendance sessions taking into account view parameters.
     *
     * @param mod_attendance_structure $att instance
     */
    public function __construct(mod_attendance_structure $att) {

        $this->sessions = $att->get_filtered_sessions();

        $this->groups = groups_get_all_groups($att->course->id);

        $this->hiddensessionscount = $att->get_hidden_sessions_count();

        $this->att = $att;
    }

    /**
     * Helper function to return urls.
     * @param int $sessionid
     * @param int $grouptype
     * @return mixed
     */
    public function url_take($sessionid, $grouptype) {
        return url_helpers::url_take($this->att, $sessionid, $grouptype);
    }

    /**
     * Must be called without or with both parameters
     *
     * @param int $sessionid
     * @param null $action
     * @return mixed
     */
    public function url_sessions($sessionid=null, $action=null) {
        return url_helpers::url_sessions($this->att, $sessionid, $action);
    }
}
