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

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot. '/course/format/lib.php');

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

    #[\Override]
    public function specialization() {
        if (isset($this->config->title)) {
            $this->title = format_string($this->config->title, true, ['context' => $this->context]);
        } else {
            $this->title = get_string('pluginname', 'block_site_main_menu');
        }
    }

    #[\Override]
    function applicable_formats() {
        $course = $this->get_block_course();
        if ($course->id == SITEID) {
            return [
                'site' => true,
            ];
        }

        $format = course_get_format($course);
        $applicableformat = $format && !$format->has_view_page();
        return [
            'course-view' => $applicableformat,
            'mod' => $applicableformat,
            'site' => true,
        ];
    }

    #[\Override]
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

        $course = $this->get_block_course();
        course_create_sections_if_missing($course, 0);
        $format = course_get_format($course);
        $modinfo = $format->get_modinfo();
        $section = $modinfo->get_section_info(0);

        if ($format->supports_ajax()) {
            include_course_ajax($course);
        }

        /** @var \core_course_renderer $courserenderer */
        $courserenderer = $format->get_renderer($this->page);

        $output = new block_site_main_menu\output\mainsection($format, $section);

        $this->content->text = $courserenderer->render($output);

        $this->content->footer = $courserenderer->section_add_cm_controls($format, $section);
        return $this->content;
    }
    /**
     * Get the course for the block.
     *
     * @return stdClass The course object.
     */
    protected function get_block_course(): stdClass {
        global $COURSE;

        if (!empty($this->page)) {
            $course = $this->page->course;
            $context = $this->page->context;
        }
        if (empty($course)) {
            $course = $COURSE;
            $context = \core\context\course::instance($course->id);
        }

        if ($context->contextlevel == CONTEXT_COURSE) {
            $courseid = $context->instanceid;
        } else if ($context->contextlevel == CONTEXT_SYSTEM) {
            $courseid = SITEID;
        } else {
            $coursecontext = $context->get_course_context(false);
            if ($coursecontext) {
                $courseid = $coursecontext->instanceid;
            }
        }

        if (isset($courseid) && $courseid != $course->id) {
            $course = get_course($courseid);
        }

        return $course;
    }
}
