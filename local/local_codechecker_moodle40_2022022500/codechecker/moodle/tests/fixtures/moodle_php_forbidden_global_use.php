<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.


function ok_function() {
    global $OUTPUT, $PAGE;
    return $OUTPUT->single_button($PAGE->url, 'Click me');
}

class test_renderer extends plugin_renderer_base {
    public function ok() {
        return $this->output->single_button($this->page->url, 'Click me');
    }

    public function bad1() {
        global $OUTPUT;
        return $OUTPUT->single_button($this->page->url, 'Click me');
    }

    public function bad2() {
        global $PAGE;
        return $this->output->single_button($PAGE->url, 'Click me');
    }

    public function bad3() {
        global $OUTPUT, $PAGE;
        return $OUTPUT->single_button($PAGE->url, 'Click me');
    }

    public function bad4() {
        global $PAGE;
        return "You are on $PAGE->url.";
    }
}

class test_renderer_htmlemail extends plugin_renderer_base {
    public function bad() {
        global $OUTPUT;
        return $OUTPUT->single_button($this->page->url, 'Click me');
    }
}

class other_class {
    public function no_worries() {
        global $OUTPUT, $PAGE;
        return $OUTPUT->single_button($PAGE->url, 'Click me');
    }
}

class block_html extends block_base {
    public function ok() {
        return $this->page->url;
    }

    public function bad() {
        global $PAGE;
        return $PAGE->url;
    }
}
