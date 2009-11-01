<?php

class block_course_summary extends block_base {
    function init() {
        $this->title = get_string('pagedescription', 'block_course_summary');
        $this->version = 2007101509;
    }

    function specialization() {
        if($this->page->pagetype == PAGE_COURSE_VIEW && $this->page->course->id != SITEID) {
            $this->title = get_string('coursesummary', 'block_course_summary');
        }
    }

    function get_content() {
        global $CFG, $OUTPUT;

        if($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->instance)) {
            return '';
        }

        $this->content = new object();
        $options = new object();
        $options->noclean = true;    // Don't clean Javascripts etc
        $this->content->text = format_text($this->page->course->summary, FORMAT_HTML, $options);
        if ($this->page->user_is_editing()) {
            if($this->page->course->id == SITEID) {
                $editpage = $CFG->wwwroot.'/'.$CFG->admin.'/settings.php?section=frontpagesettings';
            } else {
                $editpage = $CFG->wwwroot.'/course/edit.php?id='.$this->page->course->id;
            }
            $this->content->text .= "<div class=\"editbutton\"><a href=\"$editpage\"><img src=\"" . $OUTPUT->old_icon_url('t/edit') . "\" alt=\"".get_string('edit')."\" /></a></div>";
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


