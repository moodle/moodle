<?PHP //$Id$

class CourseBlock_course_summary extends MoodleBlock {
    function CourseBlock_course_summary ($course) {
        if (empty($course->category)) {   // Site level
            $this->title = get_string('frontpagedescription');
        } else {
            $this->title = get_string('blockname','block_course_summary');
        }
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->course = $course;
        $this->version = 2004052600;
    }

    function applicable_formats() {
        return COURSE_FORMAT_WEEKS | COURSE_FORMAT_TOPICS | COURSE_FORMAT_SOCIAL | COURSE_FORMAT_SITE;
    }

    function get_content() {
        global $CFG, $THEME;

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = New stdClass;
        $this->content->text = format_text($this->course->summary, FORMAT_HTML);
        if (isediting($this->course->id)) {
            if (empty($this->course->category)) {
                $editpage = "$CFG->wwwroot/admin/site.php";
            } else {
                $editpage = $CFG->wwwroot.'/course/edit.php?id='.$this->course->id;
            }
            if (empty($THEME->custompix)) {
                 $pixpath = $CFG->wwwroot.'/pix';
            } else {
                 $pixpath = $CFG->wwwroot.'/theme/'.$CFG->theme.'/pix';
            }
            $this->content->text .= "<div align=\"right\"><a href=\"$editpage\"><img src=\"$pixpath/t/edit.gif\" /></a></div>";
        }
        $this->content->footer = '';

        return $this->content;
    }

    function hide_header() {return true;}
}

?>
