<?PHP //$Id$

class CourseBlock_course_summary extends MoodleBlock {
    function init() {
        $this->title = get_string('pagedescription', 'block_course_summary');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->version = 2004052600;
    }

    function specialization() {
        if($this->instance->pagetype == MOODLE_PAGE_COURSE && $this->instance->pageid != SITEID) {
            $this->title = get_string('coursesummary', 'block_course_summary');
        }
    }

    function get_content() {
        global $CFG, $THEME;

        if($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->instance)) {
            return '';
        }

        $course  = get_record('course', 'id', $this->instance->pageid);
        
        $this->content = New stdClass;
        $options->noclean = true;    // Don't clean Javascripts etc
        $this->content->text = format_text($course->summary, FORMAT_HTML, $options);
        if(isediting($this->instance->pageid)) {
            if($this->instance->pageid == SITEID) {
                $editpage = $CFG->wwwroot.'/admin/site.php';
            } else {
                $editpage = $CFG->wwwroot.'/course/edit.php?id='.$this->instance->pageid;
            }
            $this->content->text .= "<div align=\"right\"><a href=\"$editpage\"><img src=\"$CFG->pixpath/t/edit.gif\" alt=\"\" /></a></div>";
        }
        $this->content->footer = '';

        return $this->content;
    }

    function hide_header() {
        return true;
    }

    function preferred_width() {
        return 210;
    }

}

?>
