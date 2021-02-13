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
 * @package   block_mycourses
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_mycourses\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use core_course\external\course_summary_exporter;

/**
 * Class containing data for courses view in the mycourses block.
 *
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class available_view implements renderable, templatable {
    /** Quantity of courses per page. */
    const COURSES_PER_PAGE = 6;

    /**
     * The courses_view constructor.
     *
     * @param array $courses list of courses.
     * @param array $coursesprogress list of courses progress.
     */
    public function __construct($mycompletion, $cutoffdate) {
        $this->mycompletion = $mycompletion;
        $this->cutoffdate = $cutoffdate;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output) {
        global $CFG, $DB;
        require_once($CFG->dirroot.'/course/lib.php');

        // Build courses view data structure.
        $availableview = [];

        foreach ($this->mycompletion->mynotstartedenrolled as $mid => $notstarted) {
            // get the course display info.
            $context = \context_course::instance($notstarted->courseid);
            $course = $DB->get_record("course", array("id"=>$notstarted->courseid));
            $courseobj = new \core_course_list_element($course);

            $exporter = new course_summary_exporter($course, ['context' => $context]);
            $exportedcourse = $exporter->export($output);
            // Convert summary to plain text.
            $coursesummary = content_to_text($exportedcourse->summary, $exportedcourse->summaryformat);
            // display course overview files
            $imageurl = '';
            foreach ($courseobj->get_course_overviewfiles() as $file) {
                $isimage = $file->is_valid_image();
                if (!$isimage) {
                    $imageurl = null;
                } else {
                    $imageurl = file_encode_url("$CFG->wwwroot/pluginfile.php",
                                '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                                $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
                }
            }
            $exportedcourse = $exporter->export($output);
            $exportedcourse->url = new \moodle_url('/course/view.php', array('id' => $notstarted->courseid));
            $exportedcourse->image = $imageurl;
            $exportedcourse->summary = $coursesummary;
            $availableview['courses'][] = $exportedcourse;
        }

        foreach ($this->mycompletion->mynotstartedlicense as $mid => $notstarted) {
            // get the course display info.
            $context = \context_course::instance($notstarted->courseid);
            $course = $DB->get_record("course", array("id"=>$notstarted->courseid));
            $courseobj = new \core_course_list_element($course);

            $exporter = new course_summary_exporter($course, ['context' => $context]);
            $exportedcourse = $exporter->export($output);
            if ($CFG->mycourses_showsummary) {
                // Convert summary to plain text.
                $coursesummary = content_to_text($exportedcourse->summary, $exportedcourse->summaryformat);
            } else {
                $coursesummary = '';
            }
            // display course overview files
            $imageurl = '';
            foreach ($courseobj->get_course_overviewfiles() as $file) {
                $isimage = $file->is_valid_image();
                if (!$isimage) {
                    $imageurl = $output->pix_icon(file_file_icon($file, 24), $file->get_filename(), 'moodle');
                } else {
                    $imageurl = file_encode_url("$CFG->wwwroot/pluginfile.php",
                                '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                                $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
                }
            }
            if (empty($imageurl)) {
                $imageurl = $output->image_url('i/course');
            }
            $exportedcourse = $exporter->export($output);
            $exportedcourse->url = new \moodle_url('/course/view.php', array('id' => $notstarted->courseid));
            $exportedcourse->image = $imageurl;
            $exportedcourse->summary = $coursesummary;
            $availableview['courses'][] = $exportedcourse;
        }
        return $availableview;
    }
}
