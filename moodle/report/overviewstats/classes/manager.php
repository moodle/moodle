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
 * @package     report_overviewstats
 * @copyright   2013 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Provides general methods for the plugin functionality
 */
class report_overviewstats_manager {

    /**
     * Factory method returning instances of charts to be displayed for the site
     *
     * @return array of {@link report_overviewstats_chart} subclasses
     */
    public static function get_site_charts() {

        $list = array(
            new report_overviewstats_chart_logins(),
            new report_overviewstats_chart_countries(),
            new report_overviewstats_chart_langs(),
            new report_overviewstats_chart_courses(),
        );

        return $list;
    }

    /**
     * Factory method returning instances of charts to be displayed for the given course
     *
     * @param stdClass $course The reported course's record
     * @return array of {@link report_overviewstats_chart} subclasses
     */
    public static function get_course_charts(stdClass $course) {

        $list = array(
            new report_overviewstats_chart_enrolments($course),
        );

        return $list;
    }
}
