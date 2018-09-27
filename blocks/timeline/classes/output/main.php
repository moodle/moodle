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
 * Class containing data for timeline block.
 *
 * @package    block_timeline
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_timeline\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use core_course\external\course_summary_exporter;

require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/completionlib.php');

/**
 * Class containing data for timeline block.
 *
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class main implements renderable, templatable {

    /** Number of courses to load per page */
    const COURSES_PER_PAGE = 2;

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {

        $nocoursesurl = $output->image_url('courses', 'block_timeline')->out();
        $noeventsurl = $output->image_url('activities', 'block_timeline')->out();

        $requiredproperties = course_summary_exporter::define_properties();
        $fields = join(',', array_keys($requiredproperties));
        $courses = course_get_enrolled_courses_for_logged_in_user(0, 0, null, $fields);
        list($inprogresscourses, $processedcount) = course_filter_courses_by_timeline_classification(
            $courses,
            COURSE_TIMELINE_INPROGRESS,
            self::COURSES_PER_PAGE
        );
        $formattedcourses = array_map(function($course) use ($output) {
            \context_helper::preload_from_record($course);
            $context = \context_course::instance($course->id);
            $exporter = new course_summary_exporter($course, ['context' => $context]);
            return $exporter->export($output);
        }, $inprogresscourses);

        return [
            'midnight' => usergetmidnight(time()),
            'coursepages' => [$formattedcourses],
            'urls' => [
                'nocourses' => $nocoursesurl,
                'noevents' => $noeventsurl
            ]
        ];
    }
}
