<?PHP //$Id$

class block_participants extends block_list {
    function init() {
        $this->title = get_string('people');
        $this->version = 2004052600;
    }

    function get_content() {
        global $USER, $CFG, $COURSE;

        if ($this->content !== NULL) {
            return $this->content;
        }
        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';


        if (empty($this->instance->pageid)) {
            $this->instance->pageid = SITEID;
        }

        /// MDL-13252 Always get the course page id or else the id may be incorrect in the user/index.php
        if ($COURSE->id != SITEID || 
            $CFG->showsiteparticipantslist > 1 || 
            ($CFG->showsiteparticipantslist == 1 && isteacherinanycourse()) || 
            isteacher(SITEID)) {

            $this->content->items[] = '<a title="'.get_string('listofallpeople').'" href="'.
                                      $CFG->wwwroot.'/user/index.php?id='.$COURSE->id.'">'.get_string('participants').'</a>';
            $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/users.gif" height="16" width="16" alt="" />';
        }


        return $this->content;
    }
    
    // my moodle can only have SITEID and it's redundant here, so take it away
    function applicable_formats() {
        return array('all' => true, 'my' => false);
    }

}

?>
