<?PHP //$Id$

class CourseBlock_search_forums extends MoodleBlock {
    function CourseBlock_search_forums ($course) {
        $this->title = get_string('search', 'forum');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->course = $course;
        $this->version = 2004041000;
    }

    function get_content() {
        global $USER, $CFG, $SESSION;
        optional_variable($_GET['cal_m']);
        optional_variable($_GET['cal_y']);

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = New object;
        $this->content->text = '';
        $this->content->footer = '';

        $form = forum_print_search_form($this->course, '', true);
        $this->content->text = '<div align="center">'.$form.'</div>';

        return $this->content;
    }
}

?>
