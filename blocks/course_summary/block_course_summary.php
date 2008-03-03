<?PHP //$Id$

class block_course_summary extends block_base {
    function init() {
        $this->title = get_string('pagedescription', 'block_course_summary');
        $this->version = 2007101509;
    }

    function specialization() {
        global $COURSE;
        if($this->instance->pagetype == PAGE_COURSE_VIEW && $COURSE->id != SITEID) {
            $this->title = get_string('coursesummary', 'block_course_summary');
        }
    }

    function get_content() {
        global $CFG, $COURSE;

        if($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->instance)) {
            return '';
        }

        $this->content = new object();
        $options = new object();
        $options->noclean = true;    // Don't clean Javascripts etc
        $this->content->text = format_text($COURSE->summary, FORMAT_HTML, $options);
        if (isediting($COURSE->id)) { // ?? courseid param not there??
            if($COURSE->id == SITEID) {
                $editpage = $CFG->wwwroot.'/'.$CFG->admin.'/settings.php?section=frontpagesettings';
            } else {
                $editpage = $CFG->wwwroot.'/course/edit.php?id='.$COURSE->id;
            }
            $this->content->text .= "<div class=\"editbutton\"><a href=\"$editpage\"><img src=\"$CFG->pixpath/t/edit.gif\" alt=\"".get_string('edit')."\" /></a></div>";
        }
        $this->content->footer = '';

        return $this->content;
    }

    function hide_header() {
        return true;
    }

    function preferred_width() {
        return 210;
    }

}

?>
