<?PHP //$Id$

class CourseBlock_course_summary extends MoodleBlock {
    function CourseBlock_course_summary ($course) {
        $this->title = get_string('blockname','block_course_summary');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->course = $course;
        $this->version = 2004052600;
    }

    function applicable_formats() {
        return COURSE_FORMAT_WEEKS | COURSE_FORMAT_TOPICS | COURSE_FORMAT_SOCIAL | COURSE_FORMAT_SITE;
    }

    function get_content() {

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = New object;
        $this->content->text = format_text($this->course->summary, FORMAT_HTML);
        $this->content->footer = '';

        return $this->content;
    }

    function hide_header() {return true;}
}

?>
