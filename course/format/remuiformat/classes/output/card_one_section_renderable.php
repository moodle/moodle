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
 * @copyright  2019 Wisdmlabs
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

require_once($CFG->dirroot.'/course/format/renderer.php');
require_once($CFG->dirroot.'/course/format/remuiformat/classes/mod_stats.php');
require_once($CFG->dirroot.'/course/format/remuiformat/classes/course_format_data_common_trait.php');
require_once($CFG->dirroot.'/course/format/remuiformat/lib.php');

/**
 * This file contains the definition for the renderable classes for the activity page.
 *
 * @package   format_remuiformat
 * @copyright  2018 Wisdmlabs
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_remuiformat_card_one_section implements renderable, templatable {
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
     * Course format data common trait class object
     * @var course_format_data_common_trait
     */
    private $courseformatdatacommontrait;

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
        global $PAGE, $CFG, $COURSE;
        unset($output);
        $export = new \stdClass();
        $modinfo = get_fast_modinfo($this->course);
        $sections = $modinfo->get_section_info_all();

        $renderer = $PAGE->get_renderer('format_remuiformat');

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
        if ($PAGE->user_is_editing()) {
            $export->editing = 1;
        }
        // The requested section page.
        $currentsection = $modinfo->get_section_info($this->displaysection);
        // Title with section navigation links.
        $sectionnavlinks = $renderer->get_nav_links($this->course, $modinfo->get_section_info_all(), $this->displaysection);
        $export->leftnav = $sectionnavlinks['previous'];
        $export->rightnav = $sectionnavlinks['next'];
        $export->leftside = $renderer->section_left_content($currentsection, $this->course, false);
        $export->optionmenu = $renderer->section_right_content($currentsection, $this->course, false);

        // Title.
        $sectionname = $renderer->section_title_without_link($currentsection, $this->course);
        $export->title = $sectionname;
        $sectiontitlesummarymaxlength = $this->settings['sectiontitlesummarymaxlength'];
        if (!empty($currentsection->summary)) {
            $export->summary = $renderer->format_summary_text($currentsection);
        }

        // Get the details of the activities.
        $export->activities = $this->get_activities_details($currentsection);
        $export->courseid = $this->course->id;
        $export->addnewactivity = $this->courserenderer->course_section_add_cm_control(
            $this->course,
            $this->displaysection,
            $this->displaysection
        );
        $export->remuicourseformatcard = true;
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
        $PAGE->requires->js_call_amd('format_remuiformat/format_card', 'init');
        return $export;
    }

    /**
     * Get activities details from section
     * @param  object $section        Section object
     * @param  array  $displayoptions Display options
     * @return array                  Output array
     */
    private function get_activities_details($section, $displayoptions = array()) {
        global $PAGE, $USER, $DB, $CFG;
        $modinfo = get_fast_modinfo($this->course);
        $output = array();
        $completioninfo = new \completion_info($this->course);
        if (!empty($modinfo->sections[$section->section])) {
            $count = 1;
            foreach ($modinfo->sections[$section->section] as $modnumber) {
                $mod = $modinfo->cms[$modnumber];
                if (!$mod->is_visible_on_course_page()) {
                    continue;
                }

                $completiondata = $completioninfo->get_data($mod, true);
                $activitydetails = new \stdClass();
                $activitydetails->index = $count;
                $activitydetails->id = $mod->id;
                $activitydetails = $this->courseformatdatacommontrait->activity_completion(
                    $this->course,
                    $completioninfo,
                    $activitydetails,
                    $mod,
                    $this->courserenderer,
                    $displayoptions
                );
                $activitydetails->viewurl = $mod->url;
                $activitydetails->move = course_get_cm_move($mod, $section->section);
                $activitydetails->title = $this->courserenderer->course_section_cm_name($mod, $displayoptions);
                $activitydetails->title .= $mod->afterlink;
                $activitydetails->modulename = $mod->modname;
                $activitydetails->modulefullname = $mod->modfullname;
                $activitydetails->summary = $this->modstats->get_formatted_summary(
                    $this->courserenderer->course_section_cm_text($mod, $displayoptions),
                    $this->settings
                );

                // In case of label activity send full text of cm to open in modal.
                if (array_search($mod->modname, array('label', 'folder')) !== false) {
                    $activitydetails->viewurl = $mod->modname.'_'.$mod->id;
                    $activitydetails->label = 1;
                    $activitydetails->fullcontent = $this->courserenderer->course_section_cm_text($mod, $displayoptions);
                }

                $activitydetails->completed = $completiondata->completionstate;
                $modicons = '';
                if ($mod->visible == 0) {
                    $activitydetails->hidden = 1;
                }
                $availstatus = $this->courserenderer->course_section_cm_availability($mod, $modnumber);
                if ($availstatus != "") {
                    $activitydetails->availstatus = $availstatus;
                }
                if ($PAGE->user_is_editing()) {
                    $activitydetails->editing = 1;
                    $editactions = course_get_cm_edit_actions($mod, $mod->indent, $this->displaysection);
                    $modicons .= ' '. $this->courserenderer->course_section_cm_edit_actions(
                        $editactions,
                        $mod,
                        $this->displaysection
                    );
                    $modicons .= $mod->afterediticons;
                    $activitydetails->modicons = $modicons;
                }

                // Set the section layout using the databases value.
                $table = 'format_remuiformat';
                $record = $DB->get_record(
                    $table,
                    array('courseid' => $this->course->id, 'sectionid' => $section->section, 'activityid' => $modnumber),
                    '*'
                );

                if ( !empty($record) ) {
                    if ($record->layouttype == 'row') {
                        $activitydetails->layouttyperow = 'row';
                    } else {
                        $activitydetails->layouttypecol = 'col';
                    }
                } else {
                    $activitydetails->layouttypecol = 'col';
                }

                // Get all sections from course.
                $sections = $DB->get_records(
                    'course_sections',
                    array(
                        'course' => $this->course->id),
                        'section',
                        'id,section,name,sequence'
                    );

                // Create a section dropdown with section name, section ID and activity ID.
                $sectionlist = '';
                foreach ($sections as $value) {
                    // Skip current section.
                    if ($section->section != $value->section ) {
                        if (empty($value->name)) {
                            $value->name = 'Section '.$value->section;
                        }
                        $sectionlist .= html_writer::span(
                            $value->name,
                            'ecfsectionname dropdown-item p-1',
                            array('data-sectionidtomove' => $value->section, 'data-oldsectionid' => $section->section)
                        );
                    }
                }
                $activitydetails->sectionlist = $sectionlist;

                $output[] = $activitydetails;
                $count++;
            }
        }
        return $output;
    }
}
