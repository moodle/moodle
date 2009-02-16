<?php  //$Id$

/**
 * Represents a course category context in the tree navigated by @see{file_browser}.
 */
class file_info_coursesection extends file_info {
    protected $course;

    public function __construct($browser, $context, $course) {
        parent::__construct($browser, $context);
        $this->course = $course;
    }

    public function get_params() {
        return array('contextid'=>$this->context->id,
                     'filearea' =>'course_section',
                     'itemid'   =>null,
                     'filepath' =>null,
                     'filename' =>null);
    }

    public function get_visible_name() {
        $format = $this->course->format;
        $sectionsname = get_string("coursesections$format","format_$format"); // TODO: localise
        if ($sectionsname === "[[coursesections$format]]") {
            $sectionsname = get_string("coursesections$format", 'repository'); // TODO: localise
        }

        return $sectionsname;
    }

    public function is_writable() {
        return false;
    }

    public function is_directory() {
        return true;
    }

    public function get_children() {
        global $DB;

        $children = array();

        $course_sections = $DB->get_records('course_sections', array('course'=>$this->course->id), 'section');
        foreach ($course_sections as $section) {
            if ($child = $this->browser->get_file_info($this->context, 'course_section', $section->id)) {
                $children[] = $child;
            }
        }

        return $children;
    }

    public function get_parent() {
        return $this->browser->get_file_info($this->context);
    }
}
