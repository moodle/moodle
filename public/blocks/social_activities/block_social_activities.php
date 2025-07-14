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
 * Social activities block.
 *
 * @package    block_social_activities
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_social_activities extends block_base {
    function init(){
        $this->title = get_string('pluginname', 'block_social_activities');
    }

    function applicable_formats() {
        return array('course-view-social' => true);
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

        $course = $this->page->course;

        course_create_sections_if_missing($course, 0);
        $format = course_get_format($course);
        $modinfo = $format->get_modinfo();
        $section = $modinfo->get_section_info(0);

        if ($format->supports_ajax()) {
            include_course_ajax($course);
        }

        $courserenderer = $format->get_renderer($this->page);

        $output = new block_social_activities\output\blocksection($format, $section);

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
