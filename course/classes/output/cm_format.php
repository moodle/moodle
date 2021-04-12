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
 * Contains the default activity list from a section.
 *
 * @package   core_course
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_course\output;

use cm_info;
use core\activity_dates;
use core_completion\cm_completion_details;
use core_course\course_format;
use renderable;
use section_info;
use stdClass;
use templatable;

/**
 * Base class to render a course module inside a course format.
 *
 * @package   core_course
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cm_format implements renderable, templatable {

    /** @var course_format the course format */
    protected $format;

    /** @var section_info the section object */
    private $section;

    /** @var cm_info the course module instance */
    protected $mod;

    /** @var array optional display options */
    protected $displayoptions;

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param section_info $section the section info
     * @param cm_info $mod the course module ionfo
     * @param array $displayoptions optional extra display options
     */
    public function __construct(course_format $format, section_info $section, cm_info $mod, array $displayoptions = []) {
        $this->format = $format;
        $this->section = $section;
        $this->mod = $mod;
        $this->displayoptions = $displayoptions;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): stdClass {
        global $USER;

        $format = $this->format;
        $mod = $this->mod;
        $displayoptions = $this->displayoptions;
        $course = $mod->get_course();

        // Fetch completion details.
        $showcompletionconditions = $course->showcompletionconditions == COMPLETION_SHOW_CONDITIONS;
        $completiondetails = cm_completion_details::get_instance($mod, $USER->id, $showcompletionconditions);
        $ismanualcompletion = $completiondetails->has_completion() && !$completiondetails->is_automatic();

        // Fetch activity dates.
        $activitydates = [];
        if ($course->showactivitydates) {
            $activitydates = activity_dates::get_dates_for_module($mod, $USER->id);
        }

        $activityinfodata = null;
        // Show the activity information if:
        // - The course's showcompletionconditions setting is enabled; or
        // - The activity tracks completion manually; or
        // - There are activity dates to be shown.
        if ($showcompletionconditions || $ismanualcompletion || $activitydates) {
            $activityinfo = new activity_information($mod, $completiondetails, $activitydates);
            $activityinfodata = $activityinfo->export_for_template($output);
        }

        $data = (object)[
            'cmname' => $output->course_section_cm_name($mod, $displayoptions),
            'afterlink' => $mod->afterlink,
            'altcontent' => $output->course_section_cm_text($mod, $displayoptions),
            'availability' => $output->course_section_cm_availability($mod, $displayoptions),
            'url' => $mod->url,
            'activityinfo' => $activityinfodata,
        ];

        if (!empty($mod->indent)) {
            $data->indent = $mod->indent;
            if ($mod->indent > 15) {
                $data->hugeindent = true;
            }
        }

        if (!empty($data->cmname)) {
            $data->hasname = true;
        }
        if (!empty($data->url)) {
            $data->hasurl = true;
        }

        $returnsection = $format->get_section_number();
        $data->extras = [];
        if ($format->show_editor()) {
            // Edit actions.
            $editactions = course_get_cm_edit_actions($mod, $mod->indent, $returnsection);
            $data->extras[] = $output->course_section_cm_edit_actions($editactions, $mod, $displayoptions);
            if (!empty($mod->afterediticons)) {
                $data->extras[] = $mod->afterediticons;
            }
            // Move and select options.
            $data->moveicon = course_get_cm_move($mod, $returnsection);
        }

        if (!empty($data->extras)) {
            $data->hasextras = true;
        }

        return $data;
    }
}
