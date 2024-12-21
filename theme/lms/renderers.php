<?php

namespace lms;
defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot.'/course/renderer.php');

use core_course_list_element;
use core_course_renderer;
use coursecat_helper;
use stdClass;

class theme_lms_core_course_renderer extends core_course_renderer
{
    /**
     * Customizes the course box content.
     *
     * @param coursecat_helper $chelper Helper object for rendering.
     * @param stdClass $course The course object.
     * @return string Customized HTML for the course box.
     */
    protected function coursecat_coursebox_content(coursecat_helper $chelper, $course)
    {
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            return '';
        }
        if ($course instanceof stdClass) {
            $course = new core_course_list_element($course);
        }
        $content = \html_writer::start_tag('div', ['class' => 'd-flex']);
        $content .= $this->course_overview_files($course);
        $content .= \html_writer::start_tag('div', ['class' => 'flex-grow-1']);
//        $content .= $this->course_summary($chelper, $course);
//        $content .= $this->course_contacts($course);
        $content .= $this->course_category_name($chelper, $course);
        $content .= $this->course_custom_fields($course);
        $content .= \html_writer::end_tag('div');
        $content .= \html_writer::end_tag('div');
        return $content;
    }
}

