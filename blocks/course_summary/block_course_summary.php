<?PHP //$Id$

class CourseBlock_course_summary extends MoodleBlock {
    function CourseBlock_course_summary ($course) {
        if(!empty($course) && $course->id == SITEID) {   // Site level
            $this->title = get_string('frontpagedescription');
        } else {
            $this->title = get_string('blockname','block_course_summary');
        }
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->course = $course;
        $this->version = 2004052600;
    }

    function get_content() {
        global $CFG, $THEME;

        if($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->course)) {
            return '';
        }

        $this->content = New stdClass;
        $options->noclean = true;    // Don't clean Javascripts etc
        $this->content->text = format_text($this->course->summary, FORMAT_HTML, $options);
        if(isediting($this->course->id)) {
            if($this->course->id == SITEID) {
                $editpage = $CFG->wwwroot.'/admin/site.php';
            } else {
                $editpage = $CFG->wwwroot.'/course/edit.php?id='.$this->course->id;
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
