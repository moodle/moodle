<?php  //$Id$

class file_info_system extends file_info {
    public function __construct($browser) {
        parent::__construct($browser, get_context_instance(CONTEXT_SYSTEM));
    }

    public function get_params() {
        return array('contextid'=>$this->context->id,
                     'filearea' =>null,
                     'itemid'   =>null,
                     'filepath' =>null,
                     'filename' =>null);
    }

    public function get_visible_name() {
        return get_string('arearoot', 'repository');
    }

    public function is_writable() {
        return false;
    }

    public function is_directory() {
        return true;
    }

    public function get_children() {
        global $DB, $USER;

        $children = array();

        if ($child = $this->browser->get_file_info(get_context_instance(CONTEXT_USER, $USER->id))) {
            $children[] = $child;
        }

        $course_cats = $DB->get_records('course_categories', array('parent'=>0), 'sortorder');
        foreach ($course_cats as $category) {
            $context = get_context_instance(CONTEXT_COURSECAT, $category->id);
            if (!$category->visible and !has_capability('moodle/course:viewhiddencourses', $context)) {
                continue;
            }
            if ($child = $this->browser->get_file_info($context)) {
                $children[] = $child;
            }
        }

        $courses = $DB->get_records('course', array('category'=>0), 'sortorder');
        foreach ($courses as $course) {
            if (!$course->visible and !has_capability('moodle/course:viewhiddencourses', $context)) {
                continue;
            }
            $context = get_context_instance(CONTEXT_COURSE, $course->id);
            if ($child = $this->browser->get_file_info($context)) {
                $children[] = $child;
            }
        }

        return $children;
    }

    public function get_parent() {
        return null;
    }
}
