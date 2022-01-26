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

use core_courseformat\base as course_format;
use context_course;
use renderable;
use templatable;
use section_info;
use stdClass;

/**
 * Base class to render a course section.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class section implements renderable, templatable {

    /** @var course_format the course format */
    protected $format;

    /** @var section_info the section info */
    protected $thissection;

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


    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param section_info $thissection the section info
     */
    public function __construct(course_format $format, section_info $thissection) {
        $this->format = $format;
        $this->thissection = $thissection;

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
    public function export_for_template(\renderer_base $output): stdClass {
        global $USER;

        $format = $this->format;
        $course = $format->get_course();
        $thissection = $this->thissection;
        $singlesection = $format->get_section_number();
        $context = context_course::instance($course->id);

        $summary = new $this->summaryclass($format, $thissection);
        $availability = new $this->availabilityclass($format, $thissection);

        $data = (object)[
            'num' => $thissection->section ?? '0',
            'id' => $thissection->id,
            'sectionreturnid' => $singlesection,
            'insertafter' => false,
            'summary' => $summary->export_for_template($output),
            'availability' => $availability->export_for_template($output),
            'restrictionlock' => !empty($thissection->availableinfo),
        ];

        // Check if it is a stealth sections (orphaned).
        if ($thissection->section > $format->get_last_section_number()) {
            $data->isstealth = true;
            $data->ishidden = true;
        }

        if ($format->show_editor()) {
            if (empty($this->hidecontrols)) {
                $controlmenu = new $this->controlmenuclass($format, $thissection);
                $data->controlmenu = $controlmenu->export_for_template($output);
            }
            if (empty($data->isstealth)) {
                $data->cmcontrols = $output->course_section_add_cm_control($course, $thissection->section, $singlesection);
            }
        }

        $coursedisplay = $course->coursedisplay ?? COURSE_DISPLAY_SINGLEPAGE;
        $data->iscoursedisplaymultipage = ($coursedisplay == COURSE_DISPLAY_MULTIPAGE);

        if ($data->num === 0 && !$data->iscoursedisplaymultipage) {
            $data->collapsemenu = true;
        }

        if ($course->id == SITEID) {
            $data->sitehome = true;
        }

        $data->contentcollapsed = false;
        $preferences = $format->get_sections_preferences();
        if (isset($preferences[$thissection->id])) {
            $sectionpreferences = $preferences[$thissection->id];
            if (!empty($sectionpreferences->contentcollapsed)) {
                $data->contentcollapsed = true;
            }
        }

        if ($thissection->section == 0) {
            // Section zero is always visible only as a cmlist.
            $cmlist = new $this->cmlistclass($format, $thissection);
            $data->cmlist = $cmlist->export_for_template($output);

            $header = new $this->headerclass($format, $thissection);
            if (empty($this->hidetitle)) {
                $data->header = $header->export_for_template($output);
            }
            return $data;
        }

        // When a section is displayed alone the title goes over the section, not inside it.
        $header = new $this->headerclass($format, $thissection);

        if (!$thissection->visible) {
            $data->ishidden = true;
            $data->notavailable = true;
            if (has_capability('moodle/course:viewhiddensections', $context, $USER)) {
                $data->hiddenfromstudents = true;
                $data->notavailable = false;
            }
        }

        if ($thissection->section == $singlesection) {
            if (empty($this->hidetitle)) {
                $data->singleheader = $header->export_for_template($output);
            }
        } else {
            if (empty($this->hidetitle)) {
                $data->header = $header->export_for_template($output);
            }

            // Add activities summary if necessary.
            if (!$format->show_editor() && $course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                $cmsummary = new $this->cmsummaryclass($format, $thissection);
                $data->cmsummary = $cmsummary->export_for_template($output);

                $data->onlysummary = true;
                if (!$format->is_section_current($thissection)) {
                    // In multipage, only the current section (and the section zero) has elements.
                    return $data;
                }
            }
        }

        // Add the cm list.
        if ($thissection->uservisible) {
            $cmlist = new $this->cmlistclass($format, $thissection);
            $data->cmlist = $cmlist->export_for_template($output);
        }

        $data->hasavailability = false;
        if (isset($data->availability->hasavailability)) {
            $data->hasavailability = $data->availability->hasavailability;
        }

        if ($format->is_section_current($thissection)) {
            $data->highlighted = true;
            $data->currentlink = get_accesshide(
                get_string('currentsection', 'format_'.$format->get_format())
            );
        }

        return $data;
    }
}
