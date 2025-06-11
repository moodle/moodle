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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\repos;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\repos\repo;
use block_quickmail\repos\interfaces\course_repo_interface;

class course_repo extends repo implements course_repo_interface {

    public $defaultsort = 'id';

    public $defaultdir = 'asc';

    public $sortableattrs = [
        'id' => 'id',
    ];

    /**
     * Returns an array of all courses that the given user is enrolled in
     *
     * @param  object  $user
     * @param  bool    $activeonly
     * @return array   [course id => course shortname]
     */
    public static function get_user_course_array($user, $activeonly = false) {
        if (!$courses = enrol_get_all_users_courses($user->id, $activeonly)) {
            return [];
        }

        return array_map(function ($course) {
            return $course->shortname;
        }, $courses);
    }

}
