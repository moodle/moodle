<?PHP //$Id$

class CourseBlock_news_items extends MoodleBlock {
    function CourseBlock_news_items ($course) {
        $this->title = get_string('latestnews');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->course = $course;
        $this->version = 2004041200;
    }

    function get_content() {
        global $CFG;

        if($this->content !== NULL) {
            return $this->content;
        }

        require_once($CFG->dirroot.'/course/lib.php');

        $this->content = New object;
        $this->content->text = '';
        $this->content->footer = '';

        if ($this->course->newsitems) {
            $news = forum_get_course_forum($this->course->id, 'news');
            // Slightly hacky way to do it but...
            ob_start();
            echo '<font size="-2">';
            forum_print_latest_discussions($news->id, $this->course->newsitems, "minimal", "", get_current_group($this->course->id));
            echo '</font>';
            $this->content->text = ob_get_contents();
            ob_end_clean();
        }
        return $this->content;
    }
}

?>
