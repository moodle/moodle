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
 * Sinigle Section Renderable - A topics based format that uses card layout to diaply the content.
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
use context_course;
use html_writer;
use moodle_url;
use core_completion\progress;
use core_course\external\course_summary_exporter;

require_once($CFG->dirroot.'/course/format/renderer.php');
require_once($CFG->dirroot.'/course/renderer.php');
require_once($CFG->dirroot.'/course/format/remuiformat/classes/mod_stats.php');
require_once($CFG->dirroot.'/course/format/remuiformat/classes/course_format_data_common_trait.php');
require_once($CFG->dirroot.'/course/format/remuiformat/lib.php');

/**
 * This file contains the definition for the renderable classes for the card all sections summary page.
 *
 * @package   format_remuiformat
 * @copyright  2019 WisdmLabs
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_remuiformat_card_all_sections_summary implements renderable, templatable {

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
     * Course renderer object
     * @var course_renderer
     */
    protected $courserenderer;

    /**
     * Activity statistics
     * @var \format_remuiformat\ModStats
     */
    private $modstats;

    /**
     * Course format data common trait class object
     * @var course_format_data_common_trait
     */
    private $courseformatdatacommontrait;

    /**
     * Format Settings
     * @var array
     */
    private $settings;

    /**
     * Constructor
     * @param object          $course         Course object
     * @param course_renderer $renderer       Course renderer object
     */
    public function __construct($course, $renderer) {
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
        global $PAGE, $CFG;
        unset($output);
        $export = new \stdClass();
        $renderer = $PAGE->get_renderer('format_remuiformat');
        $rformat = $this->settings['remuicourseformat'];

        // Get necessary default values required to display the UI.
        $editing = $PAGE->user_is_editing();
        $export->editing = $editing;
        $export->courseformat = get_config('format_remuiformat', 'defaultcourseformat');
        $export->theme = $PAGE->theme->name;

        if ($editing) {
            $PAGE->requires->js_call_amd('format_remuiformat/card_editing_observer', 'init');
        }
        if ($rformat == REMUI_CARD_FORMAT) {
            $PAGE->requires->js_call_amd('format_remuiformat/format_card', 'init');
            $this->get_card_format_context($export, $renderer, $editing, $rformat);
        }
        return  $export;
    }

    /**
     * Returns the context containing the details required by the cards format mustache.
     *
     * @param object             $export   Object to export
     * @param format_remuiformat $renderer Format renderer object
     * @param bool               $editing  Is user is editing
     * @param int                $rformat  Layout
     */
    private function get_card_format_context(&$export, $renderer, $editing, $rformat) {
        global $OUTPUT;
        $output = array();
        $coursecontext = context_course::instance($this->course->id);
        $modinfo = get_fast_modinfo($this->course);
        $sections = $modinfo->get_section_info_all();
        $hidegeneralsection = $this->courseformat->hide_general_section_when_empty($this->course, $modinfo);

        if (!$hidegeneralsection) {
            // Setting up data for General Section.
            $generalsection = $modinfo->get_section_info(0);
            $export->generalsection['index'] = 0;
            $generalsectionsummary = $renderer->format_summary_text($generalsection);
            if ($generalsection) {
                if ($editing) {
                    $export->generalsection['title'] = $renderer->section_title($generalsection, $this->course);
                    $export->generalsection['editsetionurl'] = new \moodle_url(
                        'editsection.php',
                        array('id' => $generalsection->id)
                    );
                    $export->generalsection['leftsection'] = $renderer->section_left_content($generalsection, $this->course, false);
                    $export->generalsection['optionmenu'] = $renderer->section_right_content($generalsection, $this->course, false);
                } else {
                    $export->generalsection['title'] = $this->courseformat->get_section_name($generalsection);
                }

                $generalsecactivities = $this->get_activities_details($generalsection);
                $export->generalsection['activities'] = $generalsecactivities;
                // Check if activities exists in general section.
                if ( !empty($generalsecactivities) ) {
                    $export->generalsection['activityexists'] = 1;
                } else {
                    $export->generalsection['activityexists'] = 0;
                }
                $export->generalsection['availability'] = $renderer->section_availability($generalsection);
                $sectiontitlesummarymaxlength = $this->settings['sectiontitlesummarymaxlength'];

                $export->generalsection['summary'] = $renderer->abstract_html_contents(
                    $generalsectionsummary, 400
                );
                $export->generalsection['fullsummary'] = $generalsectionsummary;

                // Get course image if added.
                $imgurl = $this->courseformatdatacommontrait->display_file($coursecontext, $this->settings['remuicourseimage_filemanager']);
                if (empty($imgurl)) {
                    $imgurl = $this->courseformatdatacommontrait->get_dummy_image_for_id($this->course->id);
                }
                $export->generalsection['coursemainimage'] = $imgurl;
                $percentage = progress::get_course_progress_percentage($this->course);
                if (!is_null($percentage)) {
                    $percentage = floor($percentage);
                    $export->generalsection['percentage'] = $percentage;
                } else {
                    $export->generalsection['percentage'] = 0;
                }

                // Get the all activities count from the all sections.
                $sectionmods = array();
                for ($i = 0; $i < count($sections); $i++) {
                    if (isset($modinfo->sections[$i])) {
                        foreach ($modinfo->sections[$i] as $cmid) {
                            $thismod = $modinfo->cms[$cmid];
                            if (!$thismod->is_visible_on_course_page()) {
                                continue;
                            }
                            if (isset($sectionmods[$thismod->modname])) {
                                $sectionmods[$thismod->modname]['name'] = $thismod->modplural;
                                $sectionmods[$thismod->modname]['count']++;
                            } else {
                                $sectionmods[$thismod->modname]['name'] = $thismod->modfullname;
                                $sectionmods[$thismod->modname]['count'] = 1;
                            }
                        }
                    }
                }
                foreach ($sectionmods as $mod) {
                    $output['activitylist'][] = $mod['count'].' '.$mod['name'];
                }
                $export->activitylist = $output['activitylist'];

                if ($export->generalsection['percentage'] != 100) {
                    // Get reseume activity link.
                    $export->resumeactivityurl = $this->courseformatdatacommontrait->get_activity_to_resume($this->course);
                }
                // Add new activity.
                $export->generalsection['addnewactivity'] = $this->courserenderer->course_section_add_cm_control(
                    $this->course,
                    0,
                    0
                );
            }
        }

        $export->courseid = $this->course->id;
        // Setting up data for remianing sections.
        $export->sections = $this->courseformatdatacommontrait->get_all_section_data(
            $renderer,
            $editing, $rformat,
            $this->settings,
            $this->course,
            $this->courseformat,
            $this->courserenderer
        );
    }

    /**
     * Get activities details from section
     * @param  object $section        Section object
     * @param  array  $displayoptions Display options
     * @return array                  Output array
     */
    private function get_activities_details($section, $displayoptions = array()) {
        global $PAGE, $USER, $CFG;
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
                $activitydetails->title = $this->courserenderer->course_section_cm_name($mod, $displayoptions);
                if (array_search($mod->modname, array('folder')) !== false) {
                    $activitydetails->title .= $this->courserenderer->course_section_cm_text($mod, $displayoptions);
                }
                $activitydetails->title .= $mod->afterlink;
                $activitydetails->modulename = $mod->modname;
                if ($mod->modname != 'folder') {
                    $activitydetails->summary = $this->courserenderer->course_section_cm_text($mod, $displayoptions);
                    $activitydetails->summary = $this->modstats->get_formatted_summary(
                        $activitydetails->summary,
                        $this->settings
                    );
                } else {
                    $activitydetails->summary = '';
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
                    $editactions = course_get_cm_edit_actions($mod, $mod->indent, $section->section);
                    $modicons .= ' '. $this->courserenderer->course_section_cm_edit_actions($editactions, $mod, 0);
                    $modicons .= $mod->afterediticons;
                    $activitydetails->modicons = $modicons;
                }
                $output[] = $activitydetails;
                $count++;
            }
        }
        return $output;
    }
}
