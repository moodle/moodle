<?php
/**
 * Course Copier Block: Failure notice
 *
 * Author: Steve Bader
 */

class block_course_copier extends block_list {

    function init() {
        $this->title = "Course Copier";
        $this->version = 2013010400;
        $this->cron = 1;
    }

    function get_content() {
        global $COURSE;

        if ($this->content !== NULL) {
            return $this->content;
        }

        if (!has_capability('moodle/site:restore', get_context_instance(CONTEXT_COURSE, $COURSE->id))) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        // content
        $this->content->items[] = "Courses can be copied using the new Wolfware Course Copy tool.  <a href=\"https://wolfware.ncsu.edu/\" target=\"_blank\" >Click here to go there now</a>.";

        return $this->content;
    }

    function instance_allow_multiple() {
        return false;
    }

    function instance_allow_config() {
        return false;
    }

    function has_config() {
        return false;
    }

    function applicable_formats() {
        return array(
            'site-index' => true,
            'course-view' => true,
            'course-view-social' => true,
            'mod' => true,
            'mod-quiz' => false,
        );
    }
}
