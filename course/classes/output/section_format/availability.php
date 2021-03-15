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
 * Contains the default section availability output class.
 *
 * @package   core_course
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_course\output\section_format;

use core_course\course_format;
use section_info;
use renderable;
use templatable;
use core_availability\info_section;
use core_availability\info;
use context_course;
use stdClass;

/**
 * Base class to render section availability.
 *
 * @package   core_course
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class availability implements renderable, templatable {

    /** @var course_format the course format class */
    protected $format;

    /** @var section_info the section object */
    protected $section;

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param section_info $section the section info
     */
    public function __construct(course_format $format, section_info $section) {
        $this->format = $format;
        $this->section = $section;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * If section is not visible, display the message about that ('Not available
     * until...', that sort of thing). Otherwise, returns blank.
     *
     * For users with the ability to view hidden sections, it shows the
     * information even though you can view the section and also may include
     * slightly fuller information (so that teachers can tell when sections
     * are going to be unavailable etc). This logic is the same as for
     * activities.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return stdclass data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): stdClass {
        global $CFG, $USER;

        $format = $this->format;
        $section = $this->section;
        $course = $format->get_course();
        $context = context_course::instance($section->course);

        $canviewhidden = has_capability('moodle/course:viewhiddensections', $context, $USER);

        $info = '';
        if (!$section->visible) {
            if ($canviewhidden) {
                $info = $output->availability_info(get_string('hiddenfromstudents'), 'ishidden');
            } else {
                // We are here because of the setting "Hidden sections are shown in collapsed form".
                // Student can not see the section contents but can see its name.
                $info = $output->availability_info(get_string('notavailable'), 'ishidden');
            }
        } else if (!$section->uservisible) {
            if ($section->availableinfo) {
                // Note: We only get to this function if availableinfo is non-empty,
                // so there is definitely something to print.
                $formattedinfo = info::format_info($section->availableinfo, $section->course);
                $info = $output->availability_info($formattedinfo, 'isrestricted');
            }
        } else if ($canviewhidden && !empty($CFG->enableavailability)) {
            // Check if there is an availability restriction.
            $ci = new info_section($section);
            $fullinfo = $ci->get_full_information();
            if ($fullinfo) {
                $formattedinfo = info::format_info($fullinfo, $section->course);
                $info = $output->availability_info($formattedinfo, 'isrestricted isfullinfo');
            }
        }

        $data = (object)[
            'info' => $info,
        ];

        if (!empty($info)) {
            $data->hasavailability = true;
        }

        return $data;
    }
}
