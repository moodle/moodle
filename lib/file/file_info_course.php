<?php  //$Id$

class file_info_course extends file_info {
    protected $course;

    public function __construct($browser, $context, $course) {
        global $DB;
        parent::__construct($browser, $context);
        $this->course   = $course;
    }

    public function get_params() {
        return array('contextid'=>$this->context->id,
                     'filearea' =>null,
                     'itemid'   =>null,
                     'filepath' =>null,
                     'filename' =>null);
    }

    public function get_visible_name() {
        return ($this->course->id == SITEID) ? get_string('frontpage', 'admin') : format_string($this->course->fullname);
    }

    public function is_writable() {
        return false;
    }

    public function is_directory() {
        return true;
    }

    public function get_children() {
        $children = array();

        if (has_capability('moodle/course:update', $this->context)) {
            if ($child = $this->browser->get_file_info($this->context, 'course_intro', 0)) {
                $children[] = $child;
            }
        }

        if (has_capability('moodle/site:backup', $this->context) or has_capability('moodle/site:restorep', $this->context)) {
            if ($child = $this->browser->get_file_info($this->context, 'course_backup', 0)) {
                $children[] = $child;
            }
        }

        if (has_capability('moodle/course:managefiles', $this->context)) {
            if ($child = $this->browser->get_file_info($this->context, 'course_content', 0)) {
                $children[] = $child;
            }
        }

        $modinfo = get_fast_modinfo($this->course);
        foreach ($modinfo->cms as $cminfo) {
            if (empty($cminfo->uservisible)) {
                continue;
            }
            $modcontext = get_context_instance(CONTEXT_MODULE, $cminfo->id);
            if ($child = $this->browser->get_file_info($modcontext)) {
                $children[] = $child;
            }
        }

        return $children;
    }

    public function get_parent() {
        $pcid = get_parent_contextid($this->context);
        $parent = get_context_instance_by_id($pcid);
        return $this->browser->get_file_info($parent);
    }
}
