<?PHP //$Id$

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->dirroot.'/mod/forum/lib.php');

class block_search_forums extends block_base {
    function init() {
        $this->title = get_string('blocktitle', 'block_search_forums');
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

        $advancedsearch = get_string('advancedsearch', 'block_search_forums');

        $this->content->text  = '<div class="searchform">';
        $this->content->text .= '<form name="search" action="'.$CFG->wwwroot.'/mod/forum/search.php" style="display:inline">';
        $this->content->text .= '<input name="id" type="hidden" value="'.$this->instance->pageid.'" />';  // course
        $this->content->text .= '<input name="search" type="text" size="16" value="" alt="search" />';
        $this->content->text .= '<input value=">" type="submit" /><br />';
        $this->content->text .= '<a href="'.$CFG->wwwroot.'/mod/forum/search.php?id='.$this->instance->pageid.'">'.$advancedsearch.'</a>';
        $this->content->text .= helpbutton('search', $advancedsearch, 'moodle', true, false, '', true);
        $this->content->text .= '</form></div>';

        return $this->content;
    }

    function applicable_formats() {
        return array('site' => true, 'course' => true);
    }
}

?>
