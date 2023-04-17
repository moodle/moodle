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

namespace core_courseformat\output\local\state;

use core_courseformat\base as course_format;
use completion_info;
use renderer_base;
use section_info;
use cm_info;
use renderable;
use stdClass;
use core_availability\info_module;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/completionlib.php');

/**
 * Contains the ajax update course module structure.
 *
 * @package   core_course
 * @copyright 2021 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cm implements renderable {

    /** @var course_format the course format class */
    protected $format;

    /** @var section_info the course section class */
    protected $section;

    /** @var bool if cmitem HTML content must be exported as well */
    protected $exportcontent;

    /** @var cm_info the course module to display */
    protected $cm;

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param section_info $section the section data
     * @param cm_info $cm the course module data
     * @param bool $exportcontent = false if pre-rendered cmitem must be exported.
     */
    public function __construct(course_format $format, section_info $section, cm_info $cm, bool $exportcontent = false) {
        $this->format = $format;
        $this->section = $section;
        $this->cm = $cm;
        $this->exportcontent = $exportcontent;
    }

    /**
     * Export this data so it can be used as state object in the course editor.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(renderer_base $output): stdClass {
        global $USER, $CFG;
        require_once($CFG->libdir . '/externallib.php');

        $format = $this->format;
        $section = $this->section;
        $cm = $this->cm;
        $course = $format->get_course();

        $data = (object)[
            'id' => $cm->id,
            'anchor' => "module-{$cm->id}",
            'name' => external_format_string($cm->name, $cm->context, true),
            'visible' => !empty($cm->visible),
            'sectionid' => $section->id,
            'sectionnumber' => $section->section,
            'uservisible' => $cm->uservisible,
            'hascmrestrictions' => $this->get_has_restrictions(),
            'module' => $cm->modname,
            'plugin' => 'mod_' . $cm->modname,
            'indent' => ($format->uses_indentation()) ? $cm->indent : 0,
        ];

        // Check the user access type to this cm.
        $info = new info_module($cm);
        $data->accessvisible = ($data->visible && $info->is_available_for_all());

        // Add url if the activity is compatible.
        $url = $cm->url;
        if ($url) {
            $data->url = $url->out();
        }

        if ($this->exportcontent) {
            $data->content = $output->course_section_updated_cm_item($format, $section, $cm);
        }

        // Completion status.
        $completioninfo = new completion_info($course);
        $data->istrackeduser = $completioninfo->is_tracked_user($USER->id);
        if ($data->istrackeduser && $completioninfo->is_enabled($cm)) {
            $completiondata = $completioninfo->get_data($cm);
            $data->completionstate = $completiondata->completionstate;
        }

        return $data;
    }

    /**
     * Return if the activity has a restrictions icon displayed or not.
     *
     * @return bool if the activity has visible restrictions for the user.
     */
    protected function get_has_restrictions(): bool {
        global $CFG;
        $cm = $this->cm;

        if (empty($cm->visible) || empty($CFG->enableavailability)) {
            return false;
        }
        // Nothing to be displayed to the user.
        if (!$cm->is_visible_on_course_page()) {
            return false;
        }
        // Not allowed to see the module but might be allowed to see some availability.
        if (!$cm->uservisible) {
            return !empty($cm->availableinfo);
        }
        // Content editors can see all restrictions if the activity is visible.
        if (has_capability('moodle/course:viewhiddenactivities', $cm->context)) {
            $ci = new info_module($cm);
            return !empty($ci->get_full_information());
        }
        // Regular users can only see restrictions if apply to them.
        return false;
    }
}
