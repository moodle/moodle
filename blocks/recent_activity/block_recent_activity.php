<?PHP //$Id$

class CourseBlock_recent_activity extends MoodleBlock {
    function init() {
        $this->title = get_string('recentactivity');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->version = 2004042900;
    }

    function get_content() {

        if ($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        $course = get_record('course', 'id', $this->instance->pageid);

        // Slightly hacky way to do it but...
        ob_start();
        print_recent_activity($course);
        $this->content->text = ob_get_contents();
        ob_end_clean();

        return $this->content;
    }
}

?>
