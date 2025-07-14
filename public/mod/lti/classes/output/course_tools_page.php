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

namespace mod_lti\output;

use core_reportbuilder\system_report_factory;
use mod_lti\reportbuilder\local\systemreports\course_external_tools_list;

/**
 * The course tools page renderable, containing a page header renderable and a course tools system report.
 *
 * @package    mod_lti
 * @copyright  2023 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_tools_page implements \renderable {

    /** @var course_external_tools_list the course tools system report instance. */
    protected course_external_tools_list $coursetoolsreport;

    /** @var course_tools_page_header the page header renderable instance. */
    protected course_tools_page_header $coursetoolspageheader;

    /**
     * Renderable constructor.
     *
     * @param int $courseid the id of the course.
     */
    public function __construct(int $courseid) {
        global $DB;

        $context = \context_course::instance($courseid);

        // Page intro, zero state and 'add new' button.
        $canadd = has_capability('mod/lti:addcoursetool', $context);
        $sql = 'SELECT COUNT(1)
                  FROM {lti_types} tt
                 WHERE tt.course IN(:siteid, :courseid)
                   AND tt.coursevisible NOT IN(:coursevisible)';
        $toolcount = $DB->count_records_sql($sql, ['siteid' => get_site()->id, 'courseid' => $courseid, 'coursevisible' => 0]);
        $this->coursetoolspageheader = new course_tools_page_header($courseid, $toolcount, $canadd);

        // Course tools report itself.
        $this->coursetoolsreport = system_report_factory::create(course_external_tools_list::class, $context);
    }

    /**
     * Get the course tools page header renderable.
     *
     * @return course_tools_page_header the renderable.
     */
    public function get_header(): course_tools_page_header {
        return $this->coursetoolspageheader;
    }

    /**
     * Get the course tools list system report.
     *
     * @return course_external_tools_list the course tools list report.
     */
    public function get_table(): course_external_tools_list {
        return $this->coursetoolsreport;
    }
}
