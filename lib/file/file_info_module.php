<?php  //$Id$

class file_info_module extends file_info {
    protected $course;
    protected $cm;
    protected $areas;

    public function __construct($browser, $course, $cm, $context, $areas) {
        global $DB;
        parent::__construct($browser, $context);
        $this->course = $course;
        $this->cm     = $cm;
        $this->areas  = $areas;
    }

    public function get_params() {
        return array('contextid'=>$this->context->id,
                     'filearea' =>null,
                     'itemid'   =>null,
                     'filepath' =>null,
                     'filename' =>null);
    }

    public function get_visible_name() {
        return $this->cm->name.' ('.$this->cm->modname.')';
    }

    public function is_writable() {
        return false;
    }

    public function is_directory() {
        return true;
    }

    public function get_children() {
        $children = array();
        foreach ($this->areas as $area=>$desctiption) {
            if ($child = $this->browser->get_file_info($this->context, $area, 0)) {
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
