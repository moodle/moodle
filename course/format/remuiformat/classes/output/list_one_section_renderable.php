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
 * Activity Renderable - A topics based format that uses card layout to diaply the content.
 *
 * @package format_remuiformat
 * @copyright  2019 WisdmLabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_remuiformat\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use stdClass;
use html_writer;
use context_course;
use core_completion\progress;
// require_once($CFG->dirroot.'/course/format/renderer.php');
require_once($CFG->dirroot.'/course/format/remuiformat/classes/mod_stats.php');
require_once($CFG->dirroot.'/course/format/remuiformat/lib.php');

/**
 * This file contains the definition for the renderable classes for the activity page.
 *
 * @package   format_remuiformat
 * @copyright  2018 Wisdmlabs
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_remuiformat_list_one_section implements renderable, templatable {

    /**
     * Course object
     * @var object
     */
    private $course;
    /**
     * Course format object
     * @var format_remuiformat
     */
    private $courseformat;
    /**
     * Course renderer
     * @var course_renderer
     */
    private $courserenderer;
    /**
     * Activity statistic
     * @var \format_remuiformat\ModStats
     */
    private $modstats;
    /**
     * Settings array
     * @var array
     */
    private $settings;
    /**
     * Current selected section
     * @var int
     */
    private $displaysection;

    /**
     * Course format data common trait class object
     * @var course_format_data_common_trait
     */
    private $courseformatdatacommontrait;

    /**
     * Constructor
     * @param object          $course         Course object
     * @param int             $displaysection Current section
     * @param course_renderer $renderer       Course renderer object
     */
    public function __construct($course, $displaysection, $renderer) {
        $this->displaysection = $displaysection;
        $this->courseformat = course_get_format($course);
        $this->course = $this->courseformat->get_course();
        $this->courserenderer = $renderer;
        $this->modstats = \format_remuiformat\ModStats::getinstance();
        $this->courseformatdatacommontrait = \format_remuiformat\course_format_data_common_trait::getinstance();
        $this->settings = $this->courseformat->get_settings();
    }

    /**
     * Function to export the renderer data in a format that is suitable for a
     * question mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $PAGE, $USER, $CFG;
        unset($output);
        $export = new \stdClass();
        $modinfo = get_fast_modinfo($this->course);
        $context = context_course::instance($this->course->id);
        $sections = $modinfo->get_section_info_all();
        $renderer = $PAGE->get_renderer('format_remuiformat');
        $format = course_get_format($this->course);

        $export->section = $this->displaysection;
        $export->theme = $PAGE->theme->name;

        // Check if section exists.
        if (!($sectioninfo = $modinfo->get_section_info($this->displaysection))) {
            // This section doesn't exist.
            $export->error = get_string('unknowncoursesection', 'error', $this->course->fullname);
            return $export;
        }

        // Check if the section is hidden section.
        if (!$sectioninfo->uservisible) {
            if (!$this->course->hiddensections) {
                $export->hiddensection = $renderer->start_section_list();
                $export->hiddensection .= $renderer->section_hidden($this->displaysection, $this->course->id);
                $export->hiddensection .= $renderer->end_section_list();
            }
            // Can't view this section.
            return $export;
        }

        // The requested section page.
        $section = $modinfo->get_section_info($this->displaysection);

        if ($format->is_section_current($section)) {
            $export->iscurrent = true;
            $export->highlightedlabel = get_string('highlight');
        }

        if (!$section->visible) {
            $export->notavailable = true;
            if (has_capability('moodle/course:viewhiddensections', $context, $USER)) {
                $export->hiddenfromstudents = true;
                $export->notavailable = false;
            }
        }

        if ($PAGE->user_is_editing()) {
            $export->editing = 1;
            $export->optionmenu = $this->courseformatdatacommontrait->course_section_controlmenu($this->course, $section);
        }

        $singlepageurl = $this->courseformat->get_view_url($sectioninfo->section)->out(true);

        // New menu option.
        $export->optionmenu = $this->courseformatdatacommontrait->course_section_controlmenu($this->course, $section);
        $extradetails = $this->courseformatdatacommontrait->get_section_module_info($section, $this->course, null, $singlepageurl);
        $export->progressinfo = $extradetails['progressinfo'];

        // Title with section navigation links.

        $allsectinswithoutdelegated = $modinfo->get_section_info_all();
        if ($CFG->branch >= '405') {
            $allsectinswithoutdelegated = $modinfo->get_listed_section_info_all();
        }

        if ($CFG->branch >= '405' && $section->component === "mod_subsection") {
            $sectionnavlinks = array('previous' => '', 'next' => '');
        } else {
            $sectionnavlinks = $renderer->get_nav_links($this->course, $allsectinswithoutdelegated, $this->displaysection);
        }

        $export->leftnav = $sectionnavlinks['previous'];
        $export->rightnav = $sectionnavlinks['next'];
        $export->leftside = $renderer->section_left_content($section, $this->course, false);

        // Title.
        $sectionname = $renderer->section_title_without_link($section, $this->course);
        $export->title = $sectionname;
        if (!empty($section->summary)) {
            $export->summary = $renderer->format_summary_text($section);
        }

        // Get the details of the activities.
        $export->remuicourseformatlist = true;
        $export->activities = $this->courseformatdatacommontrait->course_section_cm_list(
            $this->course, $section);
        $export->activities .= $this->courserenderer->course_section_add_cm_control(
            $this->course, $this->displaysection, $this->displaysection
        );
        $export->courseid = $this->course->id;
        $export->sections = [];
        foreach ($sections as $index => $sectioninfo) {
            if ($sectioninfo->section == $this->displaysection) {
                continue;
            }
            $section = new stdClass;
            $section->index = $sectioninfo->section;
            $section->name = $this->courseformat->get_section_name($section->index);
            $export->sections[] = $section;
        }
                 // Get course image if added.
                 $coursecontext = context_course::instance($this->course->id);
                 $imgurl = $this->courseformatdatacommontrait->display_file(
                 $coursecontext,
                 $this->settings['remuicourseimage_filemanager']
                 );
        if (empty($imgurl)) {
            $imgurl = $this->courseformatdatacommontrait->get_dummy_image_for_id($this->course->id);
        }
        $export->resumeactivityurl = $this->courseformatdatacommontrait->get_activity_to_resume($this->course);
        $export->headerdata = get_extra_header_context(
            $export,
            $this->course,
            progress::get_course_progress_percentage($this->course),
            $imgurl
        );
        $PAGE->requires->js_call_amd('format_remuiformat/format_list', 'init');
        return $export;
    }
}
