<?PHP //$Id$

class block_participants extends block_list {
    function init() {
        $this->title = get_string('people');
        $this->version = 2004052600;
    }

    function get_content() {

        global $CFG;

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        // the following 3 lines is need to pass _self_test();
        if (empty($this->instance->pageid)) {
            return '';
        }

        if (!$currentcontext = get_context_instance(CONTEXT_COURSE, $this->instance->pageid)) {
            $this->content = '';
            return $this->content;
        }

        if (!has_capability('moodle/course:viewparticipants', $currentcontext)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new object();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        $this->content->items[] = '<a title="'.get_string('listofallpeople').'" href="'.
                                  $CFG->wwwroot.'/user/index.php?contextid='.$currentcontext->id.'">'.get_string('participants').'</a>';
        $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/users.gif" class="icon" alt="" />';

        return $this->content;
    }

    // my moodle can only have SITEID and it's redundant here, so take it away
    function applicable_formats() {
        return array('all' => true, 'my' => false, 'tag' => false);
    }

}

?>
