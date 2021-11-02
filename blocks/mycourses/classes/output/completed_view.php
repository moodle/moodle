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
class completed_view implements renderable, templatable {
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
        global $CFG, $DB, $USER;
        require_once($CFG->dirroot.'/course/lib.php');

        // Build courses view data structure.
        $completedview = [];

        foreach ($this->mycompletion->mycompleted as $mid => $completed) {
            $context = \context_course::instance($completed->courseid);
            $course = $DB->get_record("course", array("id"=>$completed->courseid));
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
                    $imageurl = null;
                } else {
                    $imageurl = file_encode_url("$CFG->wwwroot/pluginfile.php",
                                '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                                $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
                }
            }
            if (empty($completed->finalgrade)) {
                $completed->finalgrade = 0;
            }

            if (empty($imageurl)) {
                $imageurl = $output->image_url('i/course');
            }
            $exportedcourse = $exporter->export($output);
            $exportedcourse->url = new \moodle_url('/course/view.php', array('id' => $completed->courseid));
            $exportedcourse->image = $imageurl;
            $exportedcourse->summary = $coursesummary;
            $exportedcourse->timecompleted = date($CFG->iomad_date_format, $completed->timecompleted);
            if ($iomadcourserec = $DB->get_record('iomad_courses', array('courseid' => $completed->courseid))) {
                if (!empty($iomadcourserec->validlength)) {
                    $exportedcourse->timeexpires = date($CFG->iomad_date_format, $completed->timecompleted + $iomadcourserec->validlength * 24 * 60 * 60 );
                }
            }
            $exportedcourse->progress = 100;
            $exportedcourse->hasprogress = true;
            $exportedcourse->certificates = array();
            $certificateimage = $output->image_url('f/pdf-32');
            if (!empty($completed->hasgrade)) {
                $exportedcourse->finalscore = intval($completed->finalgrade);
                $exportedcourse->hasgrade = true;
            } else {
                $exportedcourse->finalscore = get_string('passed', 'block_iomad_company_admin');
            }
            foreach ($completed->certificates as $certificate) {
                $certout = new \stdclass();
                $certout->certificateurl = $certificate->certificateurl;
                $certout->certificatename = $certificate->certificatename;
                $certout->certificateimage = $certificateimage;
                $exportedcourse->certificates[] = $certout;
            }
            $completedview['courses'][] = $exportedcourse;
        }
        return $completedview;
    }
}
