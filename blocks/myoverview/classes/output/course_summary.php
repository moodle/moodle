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
 * Class containing data for my overview block.
 *
 * @package    block_myoverview
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_myoverview\output;
defined('MOODLE_INTERNAL') || die();

use core_course\external\course_summary_exporter;
use renderable;
use renderer_base;
use templatable;
/**
 * Class containing data for my overview block.
 *
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_summary implements renderable, templatable {

    /** @var array $courses List of courses the user is enrolled in. */
    protected $courses = [];

    /**
     * The course_summary constructor.
     *
     * @param array $courses list of courses.
     */
    public function __construct($courses) {
        $this->courses = $courses;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {

        $data = [];
        foreach ($this->courses as $courseid => $value) {
            $context = \context_course::instance($courseid);
            $exporter = new course_summary_exporter($this->courses[$courseid], array('context' => $context));
            $data[] = $exporter->export($output);
        }
        return $data;
    }
}
