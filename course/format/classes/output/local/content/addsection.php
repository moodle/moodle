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

use core\output\named_templatable;
use core_courseformat\base as course_format;
use core_courseformat\output\local\courseformat_named_templatable;
use moodle_url;
use renderable;
use stdClass;

/**
 * Base class to render a course add section buttons.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class addsection implements named_templatable, renderable {

    use courseformat_named_templatable;

    /** @var course_format the course format class */
    protected $format;

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     */
    public function __construct(course_format $format) {
        $this->format = $format;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): stdClass {

        // If no editor must be displayed, just return an empty structure.
        if (!$this->format->show_editor(['moodle/course:update'])) {
            return new stdClass();
        }

        $format = $this->format;
        $course = $format->get_course();
        $options = $format->get_format_options();

        $lastsection = $format->get_last_section_number();
        $maxsections = $format->get_max_sections();

        // Component based formats handle add section button in the frontend.
        $show = ($lastsection < $maxsections) || $format->supports_components();

        $supportsnumsections = array_key_exists('numsections', $options);
        if ($supportsnumsections) {
            $data = $this->get_num_sections_data($output, $lastsection, $maxsections);
        } else if (course_get_format($course)->uses_sections() && $show) {
            $data = $this->get_add_section_data($output, $lastsection, $maxsections);
        }

        if (count((array)$data)) {
            $data->showaddsection = true;
        }

        return $data;
    }

    /**
     * Get the legacy num section add/remove section buttons data.
     *
     * Current course format has 'numsections' option, which is very confusing and we suggest course format
     * developers to get rid of it (see MDL-57769 on how to do it).
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @param int $lastsection the last section number
     * @param int $maxsections the maximum number of sections
     * @return stdClass data context for a mustache template
     */
    protected function get_num_sections_data(\renderer_base $output, int $lastsection, int $maxsections): stdClass {
        $format = $this->format;
        $course = $format->get_course();
        $data = new stdClass();

        if ($lastsection < $maxsections) {
            $data->increase = (object) [
                'url' => new moodle_url(
                    '/course/changenumsections.php',
                    ['courseid' => $course->id, 'increase' => true, 'sesskey' => sesskey()]
                ),
            ];
        }

        if ($course->numsections > 0) {
            $data->decrease = (object) [
                'url' => new moodle_url(
                    '/course/changenumsections.php',
                    ['courseid' => $course->id, 'increase' => false, 'sesskey' => sesskey()]
                ),
            ];
        }
        return $data;
    }

    /**
     * Get the add section button data.
     *
     * Current course format does not have 'numsections' option but it has multiple sections suppport.
     * Display the "Add section" link that will insert a section in the end.
     * Note to course format developers: inserting sections in the other positions should check both
     * capabilities 'moodle/course:update' and 'moodle/course:movesections'.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @param int $lastsection the last section number
     * @param int $maxsections the maximum number of sections
     * @return stdClass data context for a mustache template
     */
    protected function get_add_section_data(\renderer_base $output, int $lastsection, int $maxsections): stdClass {
        $format = $this->format;
        $course = $format->get_course();
        $data = new stdClass();

        $addstring = $format->get_format_string('addsection');

        $params = ['courseid' => $course->id, 'insertsection' => 0, 'sesskey' => sesskey()];

        $singlesection = $this->format->get_sectionnum();
        if ($singlesection) {
            $params['sectionreturn'] = $singlesection;
        }

        $data->addsections = (object) [
            'url' => new moodle_url('/course/changenumsections.php', $params),
            'title' => $addstring,
            'newsection' => $maxsections - $lastsection,
        ];
        return $data;
    }
}
