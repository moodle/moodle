<?PHP //$Id$

class CourseBlock_participants extends MoodleBlock {
    function init() {
        $this->title = get_string('people');
        $this->content_type = BLOCK_TYPE_LIST;
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

        $strgroups   = get_string('groups');
        $strgroupmy  = get_string('groupmy');

        $course = get_record('course', 'id', $this->instance->pageid);

        if ($this->instance->pageid != SITEID || $CFG->showsiteparticipantslist > 1 || ($CFG->showsiteparticipantslist == 1 && isteacher()) || isteacher(SITEID)) {
            $this->content->items[]='<a title="'.get_string('listofallpeople').'" href="'.$CFG->wwwroot.'/user/index.php?id='.$this->instance->pageid.'">'.get_string('participants').'</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/users.gif" height="16" width="16" alt="" />';
        }

        if ($course->groupmode || !$course->groupmodeforce) {
            if ($course->groupmode == VISIBLEGROUPS or isteacheredit($this->instance->pageid)) {
                $this->content->items[]='<a title="'.$strgroups.'" href="'.$CFG->wwwroot.'/course/groups.php?id='.$this->instance->pageid.'">'.$strgroups.'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/group.gif" height="16" width="16" alt="" />';
            } else if ($course->groupmode == SEPARATEGROUPS and $course->groupmodeforce) {
                // Show nothing
            } else if ($currentgroup = get_current_group($this->instance->pageid)) {
                $this->content->items[]='<a title="'.$strgroupmy.'" href="'.$CFG->wwwroot.'/course/group.php?id='.$this->instance->pageid.'">'.$strgroupmy.'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/group.gif" height="16" width="16" alt="" />';
            }
        }

        if (!empty($USER->id)) {
            $fullname = fullname($USER, true);
            $editmyprofile = '<a title="'.$fullname.'" href="'.$CFG->wwwroot.'/user/edit.php?id='.$USER->id.'&amp;course='.$this->instance->pageid.'">'.get_string('editmyprofile').'</a>';
            if ($USER->description) {
                $this->content->items[]= $editmyprofile;
            } else {
                $this->content->items[]= $editmyprofile." <blink>*</blink>";
            }
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/user.gif" height="16" width="16" alt="" />';
        }

        return $this->content;
    }
}

?>
