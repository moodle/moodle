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
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_courseformat\output\local\content;

use cm_info;
use context_course;
use core\activity_dates;
use core\output\named_templatable;
use core_availability\info_module;
use core_completion\cm_completion_details;
use core_course\output\activity_information;
use core_courseformat\base as course_format;
use core_courseformat\output\local\courseformat_named_templatable;
use renderable;
use renderer_base;
use section_info;
use stdClass;

/**
 * Base class to render a course module inside a course format.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cm implements named_templatable, renderable {
    use courseformat_named_templatable;

    /** @var course_format the course format */
    protected $format;

    /** @var section_info the section object */
    private $section;

    /** @var cm_info the course module instance */
    protected $mod;

    /** @var array optional display options */
    protected $displayoptions;

    /** @var string the activity name output class name */
    protected $cmnameclass;

    /** @var string the activity control menu class name */
    protected $controlmenuclass;

    /** @var string the activity availability class name */
    protected $availabilityclass;

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

        // Add extra display options.
        $this->displayoptions = $displayoptions;
        $this->load_classes();

        // Get the necessary classes.
        $this->cmnameclass = $format->get_output_classname('content\\cm\\cmname');
        $this->controlmenuclass = $format->get_output_classname('content\\cm\\controlmenu');
        $this->availabilityclass = $format->get_output_classname('content\\cm\\availability');
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(renderer_base $output): stdClass {
        $mod = $this->mod;
        $displayoptions = $this->displayoptions;

        $data = (object)[
            'grouping' => $mod->get_grouping_label($displayoptions['textclasses']),
            'modname' => get_string('pluginname', 'mod_' . $mod->modname),
            'url' => $mod->url,
            'activityname' => $mod->get_formatted_name(),
            'textclasses' => $displayoptions['textclasses'],
            'classlist' => [],
            'cmid' => $mod->id,
        ];

        // Add partial data segments.
        $haspartials = [];
        $haspartials['cmname'] = $this->add_cm_name_data($data, $output);
        $haspartials['availability'] = $this->add_availability_data($data, $output);
        $haspartials['alternative'] = $this->add_alternative_content_data($data, $output);
        $haspartials['completion'] = $this->add_completion_data($data, $output);
        $haspartials['editor'] = $this->add_editor_data($data, $output);
        $this->add_format_data($data, $haspartials, $output);

        // Calculated fields.
        if (!empty($data->url)) {
            $data->hasurl = true;
        }

        return $data;
    }

    /**
     * Add course module name attributes to the data structure.
     *
     * @param stdClass $data the current cm data reference
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return bool if the cm has name data
     */
    protected function add_cm_name_data(stdClass &$data, renderer_base $output): bool {
        // Mod inplace name editable.
        $cmname = new $this->cmnameclass(
            $this->format,
            $this->section,
            $this->mod,
            null,
            $this->displayoptions
        );
        $data->cmname = $cmname->export_for_template($output);
        $data->hasname = $cmname->has_name();
        return $data->hasname;
    }

    /**
     * Add the module availability to the data structure.
     *
     * @param stdClass $data the current cm data reference
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return bool if the cm has mod availability
     */
    protected function add_availability_data(stdClass &$data, renderer_base $output): bool {
        if (!$this->mod->visible) {
            $data->modavailability = null;
            return false;
        }
        // Mod availability output class.
        $availability = new $this->availabilityclass(
            $this->format,
            $this->section,
            $this->mod,
            $this->displayoptions
        );
        $modavailability = $availability->export_for_template($output);
        $data->modavailability = $modavailability;
        return $availability->has_availability($output);
    }

    /**
     * Add the alternative content to the data structure.
     *
     * @param stdClass $data the current cm data reference
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return bool if the cm has alternative content
     */
    protected function add_alternative_content_data(stdClass &$data, renderer_base $output): bool {
        $altcontent = $this->mod->get_formatted_content(
            ['overflowdiv' => true, 'noclean' => true]
        );
        $data->altcontent = (empty($altcontent)) ? false : $altcontent;
        $data->afterlink = $this->mod->afterlink;
        return !empty($data->altcontent);
    }

    /**
     * Add activity completion information to the data structure.
     *
     * @param stdClass $data the current cm data reference
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return bool the module has completion information
     */
    protected function add_completion_data(stdClass &$data, renderer_base $output): bool {
        global $USER;
        $course = $this->mod->get_course();
        // Fetch completion details.
        $showcompletionconditions = $course->showcompletionconditions == COMPLETION_SHOW_CONDITIONS;
        $completiondetails = cm_completion_details::get_instance($this->mod, $USER->id, $showcompletionconditions);

        // Fetch activity dates.
        $activitydates = [];
        if ($course->showactivitydates) {
            $activitydates = activity_dates::get_dates_for_module($this->mod, $USER->id);
        }

        $activityinfodata = (object) ['hasdates' => false, 'hascompletion' => false];
        // There are activity dates to be shown; or
        // Completion info needs to be displayed
        // * The activity tracks completion; AND
        // * The showcompletionconditions setting is enabled OR an activity that tracks manual
        // completion needs the manual completion button to be displayed on the course homepage.
        $showcompletioninfo = $completiondetails->has_completion() && ($showcompletionconditions ||
            (!$completiondetails->is_automatic() && $completiondetails->show_manual_completion()));
        if ($showcompletioninfo || !empty($activitydates)) {
            $activityinfo = new activity_information($this->mod, $completiondetails, $activitydates);
            $activityinfodata = $activityinfo->export_for_template($output);
        }

        $data->activityinfo = $activityinfodata;
        return $activityinfodata->hascompletion;
    }

    /**
     * Add activity information to the data structure.
     *
     * @param stdClass $data the current cm data reference
     * @param bool[] $haspartials the result of loading partial data elements
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return bool if the cm has format data
     */
    protected function add_format_data(stdClass &$data, array $haspartials, renderer_base $output): bool {
        $result = false;
        // Legacy indentation.
        if (!empty($this->mod->indent) && $this->format->uses_indentation()) {
            $data->indent = $this->mod->indent;
            if ($this->mod->indent > 15) {
                $data->hugeindent = true;
                $result = true;
            }
        }
        // Stealth and hidden from student.
        if (!$this->mod->visible) {
            // This module is hidden but current user has capability to see it.
            $data->modhiddenfromstudents = true;
            $result = true;
        } else if ($this->mod->is_stealth()) {
            // This module is available but is normally not displayed on the course page
            // (this user can see it because they can manage it).
            $data->modstealth = true;
            $result = true;
        }
        // Special inline activity format.
        if (
            $this->mod->has_custom_cmlist_item() &&
            !$haspartials['availability'] &&
            !$haspartials['completion'] &&
            !isset($data->modhiddenfromstudents) &&
            !isset($data->modstealth) &&
            !$this->format->show_editor()
        ) {
            $data->modinline = true;
            $result = true;
        }
        return $result;
    }

    /**
     * Add course editor attributes to the data structure.
     *
     * @param stdClass $data the current cm data reference
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return bool if the cm has editor data
     */
    protected function add_editor_data(stdClass &$data, renderer_base $output): bool {
        $course = $this->format->get_course();
        $coursecontext = context_course::instance($course->id);
        $editcaps = [];
        if (has_capability('moodle/course:activityvisibility', $coursecontext)) {
            $editcaps = ['moodle/course:activityvisibility'];
        }
        if (!$this->format->show_editor($editcaps)) {
            return false;
        }
        $returnsection = $this->format->get_section_number();
        // Edit actions.
        $controlmenu = new $this->controlmenuclass(
            $this->format,
            $this->section,
            $this->mod,
            $this->displayoptions
        );
        $data->controlmenu = $controlmenu->export_for_template($output);
        if (!$this->format->supports_components()) {
            // Add the legacy YUI move link.
            $data->moveicon = course_get_cm_move($this->mod, $returnsection);
        }
        return true;
    }

    /**
     * Returns the CSS classes for the activity name/content
     *
     */
    protected function load_classes() {
        $mod = $this->mod;

        $linkclasses = '';
        $textclasses = '';
        if ($mod->uservisible) {
            $info = new info_module($mod);
            $conditionalhidden = !$info->is_available_for_all();
            $accessiblebutdim = (!$mod->visible || $conditionalhidden) &&
                has_capability('moodle/course:viewhiddenactivities', $mod->context);
            if ($accessiblebutdim && $conditionalhidden) {
                $linkclasses .= ' conditionalhidden';
                $textclasses .= ' conditionalhidden';
            }
        }
        $this->displayoptions['linkclasses'] = $linkclasses;
        $this->displayoptions['textclasses'] = $textclasses;
        $this->displayoptions['onclick'] = htmlspecialchars_decode($mod->onclick, ENT_QUOTES);;
    }

    /**
     * Get the activity link classes.
     *
     * @return string the activity link classes.
     */
    public function get_link_classes(): string {
        return $this->displayoptions['linkclasses'] ?? '';
    }

    /**
     * Get the activity text/description classes.
     *
     * @return string the activity text classes.
     */
    public function get_text_classes(): string {
        return $this->displayoptions['textclasses'] ?? '';
    }

    /**
     * Get the activity onclick code.
     *
     * @return string the activity onclick.
     */
    public function get_onclick_code(): string {
        return $this->displayoptions['onclick'];
    }
}
