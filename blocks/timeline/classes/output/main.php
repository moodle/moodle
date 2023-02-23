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
require_once($CFG->dirroot . '/blocks/timeline/lib.php');
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
     * @var string The current filter preference
     */
    public $filter;

    /**
     * @var string The current sort/order preference
     */
    public $order;

    /**
     * @var string The current limit preference
     */
    public $limit;

    /** @var int Number of timeline instances displayed. */
    protected static $timelineinstances = 0;

    /** @var int This timeline instance's ID. */
    protected $timelineinstanceid = 0;

    /**
     * main constructor.
     *
     * @param string $order Constant sort value from ../timeline/lib.php
     * @param string $filter Constant filter value from ../timeline/lib.php
     * @param string $limit Constant limit value from ../timeline/lib.php
     */
    public function __construct($order, $filter, $limit) {
        $this->order = $order ? $order : BLOCK_TIMELINE_SORT_BY_DATES;
        $this->filter = $filter ? $filter : BLOCK_TIMELINE_FILTER_BY_7_DAYS;
        $this->limit = $limit ? $limit : BLOCK_TIMELINE_ACTIVITIES_LIMIT_DEFAULT;
        // Increment the timeline instances count on initialisation.
        self::$timelineinstances++;
        // Assign this instance an ID based on the latest timeline instances count.
        $this->timelineinstanceid = self::$timelineinstances;
    }

    /**
     * Test the available filters with the current user preference and return an array with
     * bool flags corresponding to which is active
     *
     * @return array
     */
    protected function get_filters_as_booleans() {
        $filters = [
            BLOCK_TIMELINE_FILTER_BY_NONE => false,
            BLOCK_TIMELINE_FILTER_BY_OVERDUE => false,
            BLOCK_TIMELINE_FILTER_BY_7_DAYS => false,
            BLOCK_TIMELINE_FILTER_BY_30_DAYS => false,
            BLOCK_TIMELINE_FILTER_BY_3_MONTHS => false,
            BLOCK_TIMELINE_FILTER_BY_6_MONTHS => false
        ];

        // Set the selected filter to true.
        $filters[$this->filter] = true;

        return $filters;
    }

    /**
     * Get the offset/limit values corresponding to $this->filter
     * which are used to send through to the context as default values
     *
     * @return array
     */
    private function get_filter_offsets() {

        $limit = '';
        if (in_array($this->filter, [BLOCK_TIMELINE_FILTER_BY_NONE, BLOCK_TIMELINE_FILTER_BY_OVERDUE])) {
            $offset = -14;
            if ($this->filter == BLOCK_TIMELINE_FILTER_BY_OVERDUE) {
                $limit = 1;
            }
        } else {
            $offset = 0;
            $limit = 7;

            switch($this->filter) {
                case BLOCK_TIMELINE_FILTER_BY_30_DAYS:
                    $limit = 30;
                    break;
                case BLOCK_TIMELINE_FILTER_BY_3_MONTHS:
                    $limit = 90;
                    break;
                case BLOCK_TIMELINE_FILTER_BY_6_MONTHS:
                    $limit = 180;
                    break;
            }
        }

        return [
            'daysoffset' => $offset,
            'dayslimit' => $limit
        ];
    }

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

        $filters = $this->get_filters_as_booleans();
        $offsets = $this->get_filter_offsets();
        $contextvariables = [
            'timelineinstanceid' => $this->timelineinstanceid,
            'midnight' => usergetmidnight(time()),
            'coursepages' => [$formattedcourses],
            'urls' => [
                'nocourses' => $nocoursesurl,
                'noevents' => $noeventsurl
            ],
            'sorttimelinedates' => $this->order == BLOCK_TIMELINE_SORT_BY_DATES,
            'sorttimelinecourses' => $this->order == BLOCK_TIMELINE_SORT_BY_COURSES,
            'selectedfilter' => $this->filter,
            'hasdaysoffset' => true,
            'hasdayslimit' => $offsets['dayslimit'] !== '' ,
            'nodayslimit' => $offsets['dayslimit'] === '' ,
            'limit' => $this->limit,
            'hascourses' => !empty($formattedcourses),
        ];
        return array_merge($contextvariables, $filters, $offsets);
    }
}
