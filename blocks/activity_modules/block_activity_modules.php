<?PHP //$Id$

class CourseBlock_activity_modules extends MoodleBlock {
    function CourseBlock_activity_modules($course) {
        $this->title = get_string('activities');
        $this->content_type = BLOCK_TYPE_LIST;
        $this->course = $course;
        $this->version = 2004041000;
    }

    function get_content() {
        global $USER, $CFG;

        // This is really NOT pretty, but let's do it simple for now...
        global $modnamesused, $modnamesplural;

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = New object;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if ($modnamesused) {
            foreach ($modnamesused as $modname => $modfullname) {
                if ($modname != 'label') {
                    $this->content->items[] = '<a href="../mod/'.$modname.'/index.php?id='.$this->course->id.'">'.$modnamesplural[$modname].'</a>';
                    $this->content->icons[] = '<img src="'.$CFG->modpixpath.'/'.$modname.'/icon.gif" height="16" width="16" alt="">';
                }
            }
        }

        return $this->content;
    }
}

?>
