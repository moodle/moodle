<?PHP //$Id$

class block_participants extends block_list {
    function init() {
        $this->title = get_string('people');
        $this->version = 2004052600;
    }

    function get_content() {
        global $USER, $CFG;

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


        if ($this->instance->pageid != SITEID || 
            $CFG->showsiteparticipantslist > 1 || 
            ($CFG->showsiteparticipantslist == 1 && isteacherinanycourse()) || 
            isteacher(SITEID)) {

            $this->content->items[] = '<a title="'.get_string('listofallpeople').'" href="'.
                                      $CFG->wwwroot.'/user/index.php?id='.$this->instance->pageid.'">'.get_string('participants').'</a>';
            $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/users.gif" height="16" width="16" alt="" />';

        }


        return $this->content;
    }
}

?>
