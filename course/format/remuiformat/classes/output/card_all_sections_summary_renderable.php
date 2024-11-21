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

// require_once($CFG->dirroot.'/course/format/renderer.php');
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
        global $PAGE;
        $output = array();
        $export->courseid = $this->course->id;
        $this->courseformatdatacommontrait->add_generalsection_data(
            $export,
            $renderer,
            $editing,
            $this->course,
            $this->courseformat,
            $this->courserenderer
        );
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

}
