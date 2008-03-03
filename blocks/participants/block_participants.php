<?PHP //$Id$

class block_participants extends block_list {
    function init() {
        $this->title = get_string('people');
        $this->version = 2007101509;
    }

    function get_content() {

        global $CFG, $COURSE;

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        // the following 3 lines is need to pass _self_test();
        if (empty($this->instance->pageid)) {
            return '';
        }
        
        $this->content = new object();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';
        
        /// MDL-13252 Always get the course context or else the context may be incorrect in the user/index.php
        if (!$currentcontext = get_context_instance(CONTEXT_COURSE, $COURSE->id)) {
            $this->content = '';
            return $this->content;
        }
        
        if ($COURSE->id == SITEID) {
            if (!has_capability('moodle/site:viewparticipants', get_context_instance(CONTEXT_SYSTEM))) {
                $this->content = '';
                return $this->content;
            }
        } else {
            if (!has_capability('moodle/course:viewparticipants', $currentcontext)) {
                $this->content = '';
                return $this->content;
            }
        }

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
