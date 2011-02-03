<?php

class block_course_summary extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_course_summary');
    }

    function specialization() {
        if($this->page->pagetype == PAGE_COURSE_VIEW && $this->page->course->id != SITEID) {
            $this->title = get_string('coursesummary', 'block_course_summary');
        }
    }

    function get_content() {
        global $CFG, $OUTPUT;

        require_once($CFG->libdir . '/filelib.php');

        if($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->instance)) {
            return '';
        }

        $this->content = new stdClass();
        $options = new stdClass();
        $options->noclean = true;    // Don't clean Javascripts etc
        $options->overflowdiv = true;
        $context = get_context_instance(CONTEXT_COURSE, $this->page->course->id);
        $this->page->course->summary = file_rewrite_pluginfile_urls($this->page->course->summary, 'pluginfile.php', $context->id, 'course', 'summary', NULL);
        $this->content->text = format_text($this->page->course->summary, $this->page->course->summaryformat, $options);
        if ($this->page->user_is_editing()) {
            if($this->page->course->id == SITEID) {
                $editpage = $CFG->wwwroot.'/'.$CFG->admin.'/settings.php?section=frontpagesettings';
            } else {
                $editpage = $CFG->wwwroot.'/course/edit.php?id='.$this->page->course->id;
            }
            $this->content->text .= "<div class=\"editbutton\"><a href=\"$editpage\"><img src=\"" . $OUTPUT->pix_url('t/edit') . "\" alt=\"".get_string('edit')."\" /></a></div>";
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


