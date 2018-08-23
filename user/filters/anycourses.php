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
 * This is filter is used to see which students are enroled on any courses
 *
 * @package   core_user
 * @copyright 2014 Krister Viirsaar
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * User filter to distinguish users with no or any enroled courses.
 * @copyright 2014 Krister Viirsaar
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_filter_anycourses extends user_filter_yesno {

    /**
     * Returns the condition to be used with SQL
     *
     * @param array $data filter settings
     * @return array sql string and $params
     */
    public function get_sql_filter($data) {
        $value = $data['value'];

        $not = $value ? '' : 'NOT';

        return array("EXISTS ( SELECT userid FROM {user_enrolments} ) AND " .
            " id $not IN ( SELECT userid FROM {user_enrolments} )", array());
    }
}

