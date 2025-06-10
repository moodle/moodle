<?php

defined('MOODLE_INTERNAL') || die();

class block_kalturamediagallery extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'local_kalturamediagallery');
    }

    function get_content() {
        if(!is_null($this->content)) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        if($context = $this->getCourseContext()) {
            $this->content->text = $this->getKalturaMediaGalleryLink($context->instanceid);
        }

        return $this->content;
    }

    function applicable_formats()
    {
        return array(
            'course-view' => true
        );
    }

    private function getKalturaMediaGalleryLink($courseId) {
        $mediaGalleryUrl = new moodle_url('/local/kalturamediagallery/index.php', array(
            'courseid' => $courseId
        ));

        $link = html_writer::tag('a', get_string('nav_mediagallery', 'local_kalturamediagallery'), array(
            'href' => $mediaGalleryUrl->out(false)
        ));

        return $link;
    }

    private function getCourseContext() {
        // Check the current page context.  If the context is not of a course or module then return false.
        $context = context::instance_by_id($this->page->context->id);
        $isCourseContext = $context instanceof context_course;
        if (!$isCourseContext) {
            return false;
        }

        // If the context if a module then get the parent context.
        $courseContext = ($context instanceof context_module) ? $context->get_course_context() : $context;

        return $courseContext;
    }
}