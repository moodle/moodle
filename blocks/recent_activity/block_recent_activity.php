<?PHP //$Id$

class CourseBlock_recent_activity extends MoodleBlock {
    function CourseBlock_recent_activity ($course) {
        $this->title = get_string('recentactivity');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->course = $course;
        $this->version = 2004042900;
    }

    function get_content() {

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = New object;
        $this->content->text = '';
        $this->content->footer = '';

        // Slightly hacky way to do it but...
        ob_start();
        print_recent_activity($this->course);
        $this->content->text = ob_get_contents();
        ob_end_clean();

        return $this->content;
    }
}

?>
