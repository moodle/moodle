<?PHP //$Id$

class block_search_forums extends block_base {
    function init() {
        $this->title = get_string('search', 'forum');
        $this->version = 2005030900;
    }

    function get_content() {
        global $CFG;

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->footer = '';

        if (empty($this->instance)) {
            $this->content->text   = '';
            return $this->content;
        }

        $this->content->text  = '<div class="searchform">';
        $this->content->text .= '<form name="search" action="'.$CFG->wwwroot.'/mod/forum/search.php" style="display:inline">';
        $this->content->text .= '<input name="search" type="text" size="18" value="" alt="search" /> ';
        $this->content->text .= '<input value="'.get_string('searchforums', 'forum').'" type="submit" />';
        $this->content->text .= helpbutton('search', get_string('search'), 'moodle', true, false, '', true);
        $this->content->text .= '<input name="id" type="hidden" value="'.$this->instance->pageid.'" />';
        $this->content->text .= '</form></div>';

        return $this->content;
    }
}

?>
