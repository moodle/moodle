<?php  //$Id$

class file_info_coursecat extends file_info {
    protected $category;

    public function __construct($browser, $context, $category) {
        parent::__construct($browser, $context);
        $this->category = $category;
    }

    public function get_params() {
        return array('contextid'=>$this->context->id,
                     'filearea' =>null,
                     'itemid'   =>null,
                     'filepath' =>null,
                     'filename' =>null);
    }

    public function get_visible_name() {
        return format_string($this->category->name);
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

        if ($child = $this->browser->get_file_info($this->context, 'coursecat_intro', 0)) {
            $children[] = $child;
        }

        $course_cats = $DB->get_records('course_categories', array('parent'=>$this->category->id), 'sortorder');
        foreach ($course_cats as $category) {
            $context = get_context_instance(CONTEXT_COURSECAT, $category->id);
            if (!$category->visible and !has_capability('moodle/course:viewhiddencourses', $context)) {
                continue;
            }
            if ($child = $this->browser->get_file_info($context)) {
                $children[] = $child;
            }
        }

        $courses = $DB->get_records('course', array('category'=>$this->category->id), 'sortorder');
        foreach ($courses as $course) {
            $context = get_context_instance(CONTEXT_COURSE, $course->id);
            if (!$course->visible and !has_capability('moodle/course:viewhiddencourses', $context)) {
                continue;
            }
            if ($child = $this->browser->get_file_info($context)) {
                $children[] = $child;
            }
        }

        return $children;
    }

    public function get_parent() {
        $cid = get_parent_contextid($this->context);
        $parent = get_context_instance_by_id($cid);
        return $this->browser->get_file_info($parent);
    }
}
