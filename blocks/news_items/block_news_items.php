<?PHP //$Id$

class block_news_items extends block_base {
    function init() {
        $this->title = get_string('latestnews');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->version = 2004052600;
    }

    function get_content() {
        global $CFG;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        require_once($CFG->dirroot.'/course/lib.php');
        require_once($CFG->dirroot.'/mod/forum/lib.php');

        $course = get_record('course', 'id', $this->instance->pageid);

        if ($course->newsitems) {
            $news = forum_get_course_forum($this->instance->pageid, 'news');
            // Slightly hacky way to do it but...
            ob_start();
            forum_print_latest_discussions($news->id, $course->newsitems, "minimal", "", get_current_group($this->instance->pageid));
            $this->content->text = ob_get_contents();
            ob_end_clean();
        }
        return $this->content;
    }
}

?>
