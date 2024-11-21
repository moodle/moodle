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

// require_once($CFG->dirroot.'/course/format/renderer.php');
require_once($CFG->dirroot.'/course/renderer.php');
require_once($CFG->dirroot.'/course/format/remuiformat/classes/mod_stats.php');
require_once($CFG->dirroot.'/course/format/remuiformat/classes/course_format_data_common_trait.php');
require_once($CFG->dirroot.'/course/format/remuiformat/lib.php');

/**
 * This file contains the definition for the renderable classes for the sections page.
 *
 * @package   format_remuiformat
 * @copyright  2018 Wisdmlabs
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_remuiformat_list_all_sections_summary implements renderable, templatable {

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
     * Activities statistics
     * @var \format_remuiformat\ModStats
     */
    private $modstats;

    /**
     * Course format data common triat
     * @var course_format_data_common_trait
     */
    private $courseformatdatacommontrait;

    /**
     * Course format settings
     * @var array
     */
    private $settings;

    /**
     * Contructor
     * @param object          $course   Course object
     * @param course_renderer $renderer Course renderer
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
        global $PAGE, $DB, $OUTPUT, $CFG;

        $export = new \stdClass();
        $renderer = $PAGE->get_renderer('format_remuiformat');
        $rformat = $this->settings['remuicourseformat'];

        // Get necessary default values required to display the UI.
        $editing = $PAGE->user_is_editing();
        $export->editing = $editing;
        $export->courseformat = get_config('format_remuiformat', 'defaultcourseformat');
        $export->theme = $PAGE->theme->name;

        if ($rformat == REMUI_LIST_FORMAT) {
            $PAGE->requires->js_call_amd('format_remuiformat/format_list', 'init');
            $this->get_list_format_context($export, $renderer, $editing, $rformat);
        }

        return  $export;
    }


    /**
     * Get list layout context
     * @param  object      $export  Object in which context will be stored
     * @param  format_remuiformat $renderer format renderer
     * @param  bool        $editing  Editing mode
     * @param  int         $rformat  layout type
     */
    private function get_list_format_context(&$export, $renderer, $editing, $rformat) {
        global $DB, $OUTPUT, $USER;
        $coursecontext = context_course::instance($this->course->id);
        $modinfo = get_fast_modinfo($this->course);
        $sections = $modinfo->get_section_info_all();
        // Default view for all sections.
        $defaultview = $this->settings['remuidefaultsectionview'];
        $export->defaultview = $defaultview;
        if ($defaultview == 1) {
            $export->expanded = false;
            $export->collapsed = true;
        } else {
            $export->collapsed = true;
        }
        // User id for toggle.
        $export->user_id = $USER->id;
        // Course Information.
        $export->courseid = $this->course->id;
        $this->courseformatdatacommontrait->add_generalsection_data(
            $export,
            $renderer,
            $editing,
            $this->course,
            $this->courseformat,
            $this->courserenderer
        );
        $export->sections = $this->courseformatdatacommontrait->get_all_section_data(
            $renderer,
            $editing,
            $rformat,
            $this->settings,
            $this->course,
            $this->courseformat,
            $this->courserenderer
        );
    }
}
