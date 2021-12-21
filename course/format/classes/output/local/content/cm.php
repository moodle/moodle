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
use context_module;
use core\activity_dates;
use core_completion\cm_completion_details;
use core_courseformat\base as course_format;
use core_course\output\activity_information;
use renderable;
use section_info;
use stdClass;
use templatable;
use \core_availability\info_module;

/**
 * Base class to render a course module inside a course format.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cm implements renderable, templatable {

    /** @var course_format the course format */
    protected $format;

    /** @var section_info the section object */
    private $section;

    /** @var cm_info the course module instance */
    protected $mod;

    /** @var array optional display options */
    protected $displayoptions;

    /** @var string activity link css classes */
    protected $linkclasses = null;

    /** @var string text css classes */
    protected $textclasses = null;

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
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): stdClass {
        global $USER;

        $format = $this->format;
        $section = $this->section;
        $mod = $this->mod;
        $displayoptions = $this->displayoptions;
        $course = $mod->get_course();

        // Fetch completion details.
        $showcompletionconditions = $course->showcompletionconditions == COMPLETION_SHOW_CONDITIONS;
        $completiondetails = cm_completion_details::get_instance($mod, $USER->id, $showcompletionconditions);

        // Fetch activity dates.
        $activitydates = [];
        if ($course->showactivitydates) {
            $activitydates = activity_dates::get_dates_for_module($mod, $USER->id);
        }

        $displayoptions['linkclasses'] = $this->get_link_classes();
        $displayoptions['textclasses'] = $this->get_text_classes();

        // Grouping activity.
        $groupinglabel = $mod->get_grouping_label($displayoptions['textclasses']);

        $activityinfodata = (object) ['hasdates' => false, 'hascompletion' => false];
        // - There are activity dates to be shown; or
        // - Completion info needs to be displayed
        //   * The activity tracks completion; AND
        //   * The showcompletionconditions setting is enabled OR an activity that tracks manual completion needs the manual
        //     completion button to be displayed on the course homepage.
        $showcompletioninfo = $completiondetails->has_completion() && ($showcompletionconditions ||
                        (!$completiondetails->is_automatic() && $completiondetails->show_manual_completion()));
        if ($showcompletioninfo || !empty($activitydates)) {
            $activityinfo = new activity_information($mod, $completiondetails, $activitydates);
            $activityinfodata = $activityinfo->export_for_template($output);
        }

        // Mod inplace name editable.
        $cmname = new $this->cmnameclass(
            $format,
            $this->section,
            $mod,
            $format->show_editor(),
            $this->displayoptions
        );

        // Mod availability.
        $availability = new $this->availabilityclass(
            $format,
            $this->section,
            $mod,
            $this->displayoptions
        );

        $modavailability = $availability->export_for_template($output);

        $data = (object)[
            'cmname' => $cmname->export_for_template($output),
            'grouping' => $groupinglabel,
            'afterlink' => $mod->afterlink,
            'altcontent' => $mod->get_formatted_content(['overflowdiv' => true, 'noclean' => true]),
            'modavailability' => $mod->visible ? $modavailability : null,
            'modname' => get_string('pluginname', 'mod_' . $mod->modname),
            'url' => $mod->url,
            'activityinfo' => $activityinfodata,
            'activityname' => $mod->get_formatted_name(),
            'textclasses' => $displayoptions['textclasses'],
            'classlist' => []
        ];

        if (!empty($mod->indent) && $format->uses_indentation()) {
            $data->indent = $mod->indent;
            if ($mod->indent > 15) {
                $data->hugeindent = true;
            }
        }

        $data->altcontent = (empty($data->altcontent)) ? false : $data->altcontent;

        $data->hasname = !empty($data->cmname['displayvalue']);

        if (!empty($data->url)) {
            $data->hasurl = true;
        }

        if (!$mod->visible) {
            // This module is hidden but current user has capability to see it.
            $data->modhiddenfromstudents = true;
        } else if ($mod->is_stealth()) {
            // This module is available but is normally not displayed on the course page
            // (this user can see it because they can manage it).
            $data->modstealth = true;
        }

        if ($mod->modname == 'label' &&
            !$modavailability->hasmodavailability &&
            !$activityinfodata->hascompletion &&
            !isset($data->modhiddenfromstudents) &&
            !isset($data->modstealth) &&
            !$format->show_editor()
            ) {
            $data->modinline = true;
        }

        $returnsection = $format->get_section_number();

        if ($format->show_editor()) {
            // Edit actions.
            $controlmenu = new $this->controlmenuclass(
                $format,
                $this->section,
                $mod,
                $this->displayoptions
            );
            $data->controlmenu = $controlmenu->export_for_template($output);

            if ($format->supports_components()) {
                $data->moveicon = $output->pix_icon('i/dragdrop', '', 'moodle', ['class' => 'editing_move dragicon']);
            } else {
                // Add the legacy YUI move link.
                $data->moveicon = course_get_cm_move($mod, $returnsection);
            }

            // Move and select options.
            $modcontext = context_module::instance($mod->id);
        }

        return $data;
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
        $this->linkclasses = $linkclasses;
        $this->textclasses = $textclasses;
    }

    /**
     * Get the activity link classes.
     *
     * @return string the activity link classes.
     */
    public function get_link_classes(): string {
        return $this->linkclasses;
    }

    /**
     * Get the activity text/description classes.
     *
     * @return string the activity text classes.
     */
    public function get_text_classes(): string {
        return $this->textclasses;
    }
}
