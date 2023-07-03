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
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_courseformat\output\local\content\section;

use context_course;
use core_availability_multiple_messages;
use core\output\named_templatable;
use core_availability\info;
use core_availability\info_section;
use core_courseformat\base as course_format;
use core_courseformat\output\local\courseformat_named_templatable;
use renderable;
use section_info;
use stdClass;

/**
 * Base class to render section availability.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class availability implements named_templatable, renderable {

    use courseformat_named_templatable;

    /** @var course_format the course format class */
    protected $format;

    /** @var section_info the section object */
    protected $section;

    /** @var string the has availability attribute name */
    protected $hasavailabilityname;

    /** @var stdClass|null the instance export data */
    protected $data = null;

    /** @var int Availability excerpt text max size treshold */
    protected const AVAILABILITY_EXCERPT_MAXSIZE = 100;

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param section_info $section the section info
     */
    public function __construct(course_format $format, section_info $section) {
        $this->format = $format;
        $this->section = $section;
        $this->hasavailabilityname = 'hasavailability';
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): stdClass {
        $this->build_export_data($output);
        return $this->data;
    }

    /**
     * Returns if the output has availability info to display.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return bool if the element has availability data to display
     */
    public function has_availability(\renderer_base $output): bool {
        $this->build_export_data($output);
        $attributename = $this->hasavailabilityname;
        return $this->data->$attributename;
    }

    /**
     * Protected method to build the export data.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     */
    protected function build_export_data(\renderer_base $output) {
        if (!empty($this->data)) {
            return;
        }

        $data = (object) $this->get_info($output);

        $attributename = $this->hasavailabilityname;
        $data->$attributename = !empty($data->info);

        $this->data = $data;
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
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return stdclass data context for a mustache template
     */
    protected function get_info(\renderer_base $output): array {
        global $CFG, $USER;

        $section = $this->section;
        $context = context_course::instance($section->course);

        $canviewhidden = has_capability('moodle/course:viewhiddensections', $context, $USER);

        $editurl = new \moodle_url(
            '/course/editsection.php',
            ['id' => $this->section->id, 'showonly' => 'availabilityconditions']
        );
        $info = ['editurl' => $editurl->out(false)];
        if (!$section->visible) {
            return [];
        } else if (!$section->uservisible) {
            if ($section->availableinfo) {
                // Note: We only get to this function if availableinfo is non-empty,
                // so there is definitely something to print.
                $info['info'] = $this->get_availability_data($output, $section->availableinfo, 'isrestricted');
            }
        } else if ($canviewhidden && !empty($CFG->enableavailability)) {
            // Check if there is an availability restriction.
            $ci = new info_section($section);
            $fullinfo = $ci->get_full_information();
            if ($fullinfo) {
                $info['info'] = $this->get_availability_data($output, $fullinfo, 'isrestricted isfullinfo');
            }
        }

        return $info;
    }

    /**
     * Get the basic availability information data.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @param string|core_availability_multiple_messages $availabilityinfo the avalability info
     * @param string $additionalclasses additional css classes
     * @return stdClass the availability information data
     */
    protected function get_availability_data($output, $availabilityinfo, $additionalclasses = ''): stdClass {
        // At this point, availabilityinfo is either a string or a renderable. We need to handle both cases in a different way.
        if (is_string($availabilityinfo)) {
            $data = $this->availability_info_from_string($output, $availabilityinfo);
        } else {
            $data = $this->availability_info_from_output($output, $availabilityinfo);
        }

        $data->classes = $additionalclasses;

        $additionalclasses = array_filter(explode(' ', $additionalclasses));
        if (in_array('ishidden', $additionalclasses)) {
            $data->ishidden = 1;
        } else if (in_array('isstealth', $additionalclasses)) {
            $data->isstealth = 1;
        } else if (in_array('isrestricted', $additionalclasses)) {
            $data->isrestricted = 1;
            if (in_array('isfullinfo', $additionalclasses)) {
                $data->isfullinfo = 1;
            }
        }

        return $data;
    }

    /**
     * Generate the basic availability information data from a string.
     * Just shorten availability text to generate the excerpt text.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @param string $availabilityinfo the avalability info
     * @return stdClass the availability information data
     */
    protected function availability_info_from_string(\renderer_base $output, string $availabilityinfo): stdClass {
        $course = $this->format->get_course();

        $text = info::format_info($availabilityinfo, $course);
        $data = ['text' => $text];

        if (strlen(html_to_text($text)) > self::AVAILABILITY_EXCERPT_MAXSIZE) {
            $data['excerpt'] = shorten_text($text, self::AVAILABILITY_EXCERPT_MAXSIZE);
        }

        return (object) $data;
    }

    /**
     * Generate the basic availability information data from a renderable.
     * Use the header and the first item to generate the excerpt text.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @param core_availability_multiple_messages $availabilityinfo the avalability info
     * @return stdClass the availability information data
     */
    protected function availability_info_from_output(
        \renderer_base $output,
        core_availability_multiple_messages $availabilityinfo
    ): stdClass {
        $course = $this->format->get_course();

        $renderable = new \core_availability\output\availability_info($availabilityinfo);
        // We need to export_for_template() instead of directly render, to reuse the info for both 'text' and 'excerpt'.
        $info = $renderable->export_for_template($output);

        $text = $output->render_from_template('core_availability/availability_info', $info);
        $data = ['text' => info::format_info($text, $course)];

        if (!empty($info->items)) {
            $excerpttext = $info->header . ' ' . $info->items[0]->header;
            $data['excerpt'] = info::format_info($excerpttext, $course);
        }

        return (object) $data;
    }

    /**
     * Generate the basic availability information data.
     *
     * @deprecated since Moodle 4.3 MDL-78204. Please use {@see self::get_availability_data} instead.
     * @todo MDL-78489 This will be deleted in Moodle 4.7.
     * @param string $text the formatted avalability text
     * @param string $additionalclasses additional css classes
     * @return stdClass the availability information data
     */
    protected function availability_info($text, $additionalclasses = ''): stdClass {
        debugging('Use of ' . __FUNCTION__ . '() have been deprecated, ' .
        'please use core_courseformat\output\local\content\section\availability::get_availability_data()', DEBUG_DEVELOPER);

        $data = (object)[
            'text' => $text,
            'classes' => $additionalclasses
        ];
        $additionalclasses = array_filter(explode(' ', $additionalclasses));

        if (in_array('ishidden', $additionalclasses)) {
            $data->ishidden = 1;
        } else if (in_array('isstealth', $additionalclasses)) {
            $data->isstealth = 1;
        } else if (in_array('isrestricted', $additionalclasses)) {
            $data->isrestricted = 1;

            if (in_array('isfullinfo', $additionalclasses)) {
                $data->isfullinfo = 1;
            }
        }

        return $data;
    }
}
