<?PHP //$Id$

class block_search_forums extends block_base {
    function init() {
        $this->title = get_string('search', 'forum');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->version = 2004041000;
    }

    function get_content() {
        if($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $course = get_record('course', 'id', $this->instance->pageid);

        $this->content = new stdClass;
        $this->content->text = forum_print_search_form($course, '', true, 'block');
        $this->content->footer = '';

        return $this->content;
    }
}

?>
