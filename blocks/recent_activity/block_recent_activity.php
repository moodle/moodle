<?php

require_once($CFG->dirroot.'/course/lib.php');

class block_recent_activity extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_recent_activity');
    }

    function get_content() {
        if ($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        // Slightly hacky way to do it but...
        ob_start();
        print_recent_activity($this->page->course);
        $this->content->text = ob_get_contents();
        ob_end_clean();

        return $this->content;
    }

    function applicable_formats() {
        return array('all' => true, 'my' => false, 'tag' => false);
    }
}

