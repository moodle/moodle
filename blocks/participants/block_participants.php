<?PHP //$Id$

class CourseBlock_participants extends MoodleBlock {
    function CourseBlock_participants ($course) {
        $this->title = get_string('people');
        $this->content_type = BLOCK_TYPE_LIST;
        $this->course = $course;
        $this->version = 2004041800;
    }

    function get_content() {
        global $USER, $CFG;

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = New object;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        $strgroups   = get_string('groups');
        $strgroupmy  = get_string('groupmy');

        $this->content->items[]='<a title="'.get_string('listofallpeople').'" href="../user/index.php?id='.$this->course->id.'">'.get_string('participants').'</a>';
        $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/users.gif" height="16" width="16" alt="">';

        if ($this->course->groupmode or !$this->course->groupmodeforce) {
            if ($this->course->groupmode == VISIBLEGROUPS or isteacheredit($this->course->id)) {
                $this->content->items[]='<a title="'.$strgroups.'" href="groups.php?id='.$this->course->id.'">'.$strgroups.'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/group.gif" height="16" width="16" alt="">';
            } else if ($this->course->groupmode == SEPARATEGROUPS and $this->course->groupmodeforce) {
                // Show nothing
            } else if ($currentgroup = get_current_group($this->course->id)) {
                $this->content->items[]='<a title="'.$strgroupmy.'" href="group.php?id='.$this->course->id.'">'.$strgroupmy.'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/group.gif" height="16" width="16" alt="">';
            }
        }

        $fullname = fullname($USER, true);
        $editmyprofile = '<a title="'.$fullname.'" href="../user/edit.php?id='.$USER->id.'&amp;course='.$this->course->id.'">'.get_string('editmyprofile').'</a>';
        if ($USER->description) {
            $this->content->items[]= $editmyprofile;
        } else {
            $this->content->items[]= $editmyprofile." <blink>*</blink>";
        }
        $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/user.gif" height="16" width="16" alt="">';

        return $this->content;
    }
}

?>
