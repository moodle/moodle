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

namespace block_site_main_menu\output;

use core_courseformat\base as courseformat;
use renderable;
use section_info;
use templatable;

/**
 * Class mainsection
 *
 * @package    block_site_main_menu
 * @copyright  2024 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mainsection implements renderable, templatable {

    /**
     * The class constructor.
     *
     * @param courseformat $format the course format instance
     * @param section_info $section the section to render
     */
    public function __construct(
        /** @var courseformat $format the course format instance. */
        protected courseformat $format,
        /** @var section_info $section the section to render. */
        protected section_info $section,
    ) {
    }

    /**
     * Export for template.
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(\renderer_base $output) {
        $format = $this->format;
        $course = $format->get_course();
        $section = $this->section;

        $sectionoutputclass = $format->get_output_classname('content\\section\\cmlist');
        $sectionoutput = new $sectionoutputclass($format, $section);

        $cmlist = $output->render($sectionoutput);

        return [
            'courseid' => $course->id,
            'cmlist' => $cmlist,
            'sectionid' => $section->id,
            'sectionname' => $format->get_section_name($section),
            'sectionnum' => $section->sectionnum,
            'editing' => $format->show_editor(),
        ];
    }
}
