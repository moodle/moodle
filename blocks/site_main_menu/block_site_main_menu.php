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
 * Site main menu block.
 *
 * @package    block_site_main_menu
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_site_main_menu extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_site_main_menu');
    }

    function applicable_formats() {
        return array('site' => true);
    }

    function get_content() {
        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        $course = get_site();

        course_create_sections_if_missing($course, 0);
        $format = course_get_format($course);
        $modinfo = $format->get_modinfo();
        $section = $modinfo->get_section_info(0);

        $courserenderer = $format->get_renderer($this->page);

        $output = new block_site_main_menu\output\mainsection($format, $section);

        $this->content->text = $courserenderer->render($output);

        $this->content->footer = $courserenderer->course_section_add_cm_control(
            course: $course,
            section: 0,
            sectionreturn: null,
            displayoptions: ['inblock' => true],
        );
        return $this->content;
    }
}
