<?PHP //$Id$

class CourseBlock_course_summary extends MoodleBlock {
    function CourseBlock_course_summary ($course) {
        $this->title = get_string('blockname','block_course_summary');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->course = $course;
        $this->version = 2004041400;
    }

    function get_content() {
        global $USER, $CFG;

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = New object;
        $this->content->text = '';
        $this->content->footer = '';
        $this->content->text = format_text($this->course->summary, FORMAT_HTML);

        return $this->content;
    }

    function hide_header() {return true;}
}

?>
