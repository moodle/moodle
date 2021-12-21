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
 * Contains the default frontpage section displayer.
 *
 * The frontpage has a different wat of rendering the main topic.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_courseformat\output\local\content;

use core_courseformat\base as course_format;
use renderable;
use templatable;
use section_info;
use context_course;
use moodle_url;
use stdClass;

/**
 * Represents the frontpage section 1.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class frontpagesection implements renderable, templatable {

    /** @var course_format the course format class */
    protected $format;

    /** @var section_info the course section class */
    protected $section;

    /** @var string the section output class name */
    protected $sectionclass;

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     * @param section_info $section the section info
     */
    public function __construct(course_format $format, section_info $section) {
        $this->format = $format;
        $this->section = $section;

        // Get the necessary classes.
        $this->sectionclass = $format->get_output_classname('content\\section');
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
        $context = context_course::instance($course->id);
        $section = $this->section;

        $sectionoutput = new $this->sectionclass($format, $section);
        $sectionoutput->hide_controls();

        if (trim($section->name) == '') {
            $sectionoutput->hide_title();
        }

        $data = (object)[
            'sections' => [$sectionoutput->export_for_template($output)],
        ];

        if ($format->show_editor() && has_capability('moodle/course:update', $context)) {
            $data->showsettings = true;
            $data->settingsurl = new moodle_url('/course/editsection.php', ['id' => $section->id]);
        }

        return $data;
    }
}
