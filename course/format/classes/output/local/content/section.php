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
 * Contains the default section course format output class.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_courseformat\output\local\content;

use context_course;
use core\output\named_templatable;
use core_courseformat\base as course_format;
use core_courseformat\output\local\courseformat_named_templatable;
use renderable;
use renderer_base;
use section_info;
use stdClass;

/**
 * Base class to render a course section.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class section implements named_templatable, renderable {
    use courseformat_named_templatable;

    /** @var course_format the course format */
    protected $format;

    /** @var section_info the section info */
    protected $section;

    /** @var section header output class */
    protected $headerclass;

    /** @var cm list output class */
    protected $cmlistclass;

    /** @var section summary output class */
    protected $summaryclass;

    /** @var activities summary output class */
    protected $cmsummaryclass;

    /** @var section control menu output class */
    protected $controlclass;

    /** @var section availability output class */
    protected $availabilityclass;

    /** @var optional move here output class */
    protected $movehereclass;

    /** @var bool if the title is hidden for some reason */
    protected $hidetitle = false;

    /** @var bool if the title is hidden for some reason */
    protected $hidecontrols = false;

    /** @var bool if the section is considered stealth */
    protected $isstealth = false;


    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param section_info $section the section info
     */
    public function __construct(course_format $format, section_info $section) {
        $this->format = $format;
        $this->section = $section;

        if ($section->section > $format->get_last_section_number()) {
            $this->isstealth = true;
        }

        // Load output classes names from format.
        $this->headerclass = $format->get_output_classname('content\\section\\header');
        $this->cmlistclass = $format->get_output_classname('content\\section\\cmlist');
        $this->summaryclass = $format->get_output_classname('content\\section\\summary');
        $this->cmsummaryclass = $format->get_output_classname('content\\section\\cmsummary');
        $this->controlmenuclass = $format->get_output_classname('content\\section\\controlmenu');
        $this->availabilityclass = $format->get_output_classname('content\\section\\availability');
        $this->movehereclass = $format->get_output_classname('content\\section\\movehere');
    }

    /**
     * Hide the section title.
     *
     * This is used on blocks or in the home page where an isolated section is displayed.
     */
    public function hide_title(): void {
        $this->hidetitle = true;
    }

    /**
     * Hide the section controls.
     *
     * This is used on blocks or in the home page where an isolated section is displayed.
     */
    public function hide_controls(): void {
        $this->hidecontrols = true;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(renderer_base $output): stdClass {
        global $USER, $PAGE;

        $format = $this->format;
        $course = $format->get_course();
        $section = $this->section;

        $summary = new $this->summaryclass($format, $section);

        $data = (object)[
            'num' => $section->section ?? '0',
            'id' => $section->id,
            'sectionreturnid' => $format->get_section_number(),
            'insertafter' => false,
            'summary' => $summary->export_for_template($output),
            'highlightedlabel' => $format->get_section_highlighted_name(),
            'sitehome' => $course->id == SITEID,
            'editing' => $PAGE->user_is_editing()
        ];

        $haspartials = [];
        $haspartials['availability'] = $this->add_availability_data($data, $output);
        $haspartials['visibility'] = $this->add_visibility_data($data, $output);
        $haspartials['editor'] = $this->add_editor_data($data, $output);
        $haspartials['header'] = $this->add_header_data($data, $output);
        $haspartials['cm'] = $this->add_cm_data($data, $output);
        $this->add_format_data($data, $haspartials, $output);

        return $data;
    }

    /**
     * Add the section header to the data structure.
     *
     * @param stdClass $data the current cm data reference
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return bool if the cm has name data
     */
    protected function add_header_data(stdClass &$data, renderer_base $output): bool {
        if (!empty($this->hidetitle)) {
            return false;
        }

        $section = $this->section;
        $format = $this->format;

        $header = new $this->headerclass($format, $section);
        $headerdata = $header->export_for_template($output);

        // When a section is displayed alone the title goes over the section, not inside it.
        if ($section->section != 0 && $section->section == $format->get_section_number()) {
            $data->singleheader = $headerdata;
        } else {
            $data->header = $headerdata;
        }
        return true;
    }

    /**
     * Add the section cm list to the data structure.
     *
     * @param stdClass $data the current cm data reference
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return bool if the cm has name data
     */
    protected function add_cm_data(stdClass &$data, renderer_base $output): bool {
        $result = false;

        $section = $this->section;
        $format = $this->format;

        $showsummary = ($section->section != 0 &&
            $section->section != $format->get_section_number() &&
            $format->get_course_display() == COURSE_DISPLAY_MULTIPAGE &&
            !$format->show_editor()
        );

        $showcmlist = $section->uservisible;

        // Add activities summary if necessary.
        if ($showsummary) {
            $cmsummary = new $this->cmsummaryclass($format, $section);
            $data->cmsummary = $cmsummary->export_for_template($output);
            $data->onlysummary = true;
            $result = true;

            if (!$format->is_section_current($section)) {
                // In multipage, only the current section (and the section zero) has elements.
                $showcmlist = false;
            }
        }
        // Add the cm list.
        if ($showcmlist) {
            $cmlist = new $this->cmlistclass($format, $section);
            $data->cmlist = $cmlist->export_for_template($output);
            $result = true;
        }
        return $result;
    }

    /**
     * Add the section availability to the data structure.
     *
     * @param stdClass $data the current cm data reference
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return bool if the cm has name data
     */
    protected function add_availability_data(stdClass &$data, renderer_base $output): bool {
        $availability = new $this->availabilityclass($this->format, $this->section);
        $data->availability = $availability->export_for_template($output);
        $data->restrictionlock = !empty($this->section->availableinfo);
        $data->hasavailability = $availability->has_availability($output);
        return true;
    }

    /**
     * Add the section vibility information to the data structure.
     *
     * @param stdClass $data the current cm data reference
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return bool if the cm has name data
     */
    protected function add_visibility_data(stdClass &$data, renderer_base $output): bool {
        global $USER;
        $result = false;
        $course = $this->format->get_course();
        $context = context_course::instance($course->id);
        // Check if it is a stealth sections (orphaned).
        if ($this->isstealth) {
            $data->isstealth = true;
            $data->ishidden = true;
            $result = true;
        }
        if (!$this->section->visible) {
            $data->ishidden = true;
            $data->notavailable = true;
            if (has_capability('moodle/course:viewhiddensections', $context, $USER)) {
                $data->hiddenfromstudents = true;
                $data->notavailable = false;
                $result = true;
            }
        }
        return $result;
    }

    /**
     * Add the section editor attributes to the data structure.
     *
     * @param stdClass $data the current cm data reference
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return bool if the cm has name data
     */
    protected function add_editor_data(stdClass &$data, renderer_base $output): bool {
        if (!$this->format->show_editor()) {
            return false;
        }

        $course = $this->format->get_course();
        if (empty($this->hidecontrols)) {
            $controlmenu = new $this->controlmenuclass($this->format, $this->section);
            $data->controlmenu = $controlmenu->export_for_template($output);
        }
        if (!$this->isstealth) {
            $data->cmcontrols = $output->course_section_add_cm_control(
                $course,
                $this->section->section,
                $this->format->get_section_number()
            );
        }
        return true;
    }

    /**
     * Add the section format attributes to the data structure.
     *
     * @param stdClass $data the current cm data reference
     * @param bool[] $haspartials the result of loading partial data elements
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return bool if the cm has name data
     */
    protected function add_format_data(stdClass &$data, array $haspartials, renderer_base $output): bool {
        $section = $this->section;
        $format = $this->format;

        $data->iscoursedisplaymultipage = ($format->get_course_display() == COURSE_DISPLAY_MULTIPAGE);

        if ($data->num === 0 && !$data->iscoursedisplaymultipage) {
            $data->collapsemenu = true;
        }

        $data->contentcollapsed = false;
        $preferences = $format->get_sections_preferences();
        if (isset($preferences[$section->id])) {
            $sectionpreferences = $preferences[$section->id];
            if (!empty($sectionpreferences->contentcollapsed)) {
                $data->contentcollapsed = true;
            }
        }

        if ($format->is_section_current($section)) {
            $data->iscurrent = true;
            $data->currentlink = get_accesshide(
                get_string('currentsection', 'format_' . $format->get_format())
            );
        }
        return true;
    }
}
