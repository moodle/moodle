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
use core_courseformat\sectiondelegate;
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
 * @package   core_courseformat
 * @copyright 2021 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cm implements renderable {
    /**
     * Constructor.
     */
    public function __construct(
        /** @var course_format $format The course format. */
        protected course_format $format,
        /** @var section_info $section The section data. */
        protected section_info $section,
        /** @var cm_info $cm The course module data. */
        protected cm_info $cm,
        /** @var bool $exportcontent False if pre-rendered cmitem HTML content must be exported. */
        protected bool $exportcontent = false,
        /** @var ?bool $istrackeduser If is_tracked_user is pre-computed for this CM's course, it can be provided here. */
        protected ?bool $istrackeduser = null,
    ) {
    }

    /**
     * Export this data so it can be used as state object in the course editor.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(renderer_base $output): stdClass {
        global $CFG, $USER;

        $format = $this->format;
        $section = $this->section;
        $cm = $this->cm;
        $course = $format->get_course();
        $delegatedsectioninfo = $cm->get_delegated_section_info();

        $data = (object)[
            'id' => $cm->id,
            'anchor' => "module-{$cm->id}",
            'name' => \core_external\util::format_string($cm->name, $cm->context, true),
            'visible' => !empty($cm->visible),
            'stealth' => $cm->is_stealth(),
            'sectionid' => $section->id,
            'sectionnumber' => $section->section,
            'uservisible' => $cm->uservisible,
            'hascmrestrictions' => $this->get_has_restrictions(),
            'modname' => get_string('pluginname', 'mod_' . $cm->modname),
            'indent' => ($format->uses_indentation()) ? $cm->indent : 0,
            'groupmode' => $cm->groupmode,
            'module' => $cm->modname,
            'plugin' => 'mod_' . $cm->modname,
            // Activities with delegate section has some restriction to prevent structure loops.
            'hasdelegatedsection' => !empty($delegatedsectioninfo),
        ];

        if (!empty($delegatedsectioninfo)) {
            $data->delegatesectionid = $delegatedsectioninfo->id;
        }

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
        $data->istrackeduser = $this->istrackeduser ?? $completioninfo->is_tracked_user($USER->id);
        if ($data->istrackeduser && $completioninfo->is_enabled($cm)) {
            $completiondata = new \core_completion\cm_completion_details($completioninfo, $cm, $USER->id, false);
            $data->completionstate = $completiondata->get_overall_completion();
            $data->isoverallcomplete = $completiondata->is_overall_complete();
        }

        $data->allowstealth = !empty($CFG->allowstealth) && $format->allow_stealth_module_visibility($cm, $section);

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
