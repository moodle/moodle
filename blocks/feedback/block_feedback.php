<?php
if (is_file($CFG->dirroot.'/mod/feedback/lib.php')) {
    require_once($CFG->dirroot.'/mod/feedback/lib.php');
    define('FEEDBACK_BLOCK_LIB_IS_OK', true);
}

class block_feedback extends block_list {

    function init() {
        $this->title = get_string('feedback', 'block_feedback');
    }

    function applicable_formats() {
        return array('site' => true, 'course' => true);
    }

    function get_content() {
        global $CFG, $OUTPUT;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if (!defined('FEEDBACK_BLOCK_LIB_IS_OK')) {
            $this->content->items = array(get_string('missing_feedback_module', 'block_feedback'));
            return $this->content;
        }

        $courseid = $this->page->course->id;
        if ($courseid <= 0) {
            $courseid = SITEID;
        }

        $icon = '<img src="'.$OUTPUT->pix_url('icon', 'feedback') . '" class="icon" alt="" />';


        if (empty($this->instance->pageid)) {
            $this->instance->pageid = SITEID;
        }

        if ($feedbacks = feedback_get_feedbacks_from_sitecourse_map($courseid)) {
            $baseurl = new moodle_url('/mod/feedback/view.php');
            foreach ($feedbacks as $feedback) {
                $url = new moodle_url($baseurl);
                $url->params(array('id'=>$feedback->cmid, 'courseid'=>$courseid));
                $this->content->items[] = '<a href="'.$url->out().'">'.$icon.$feedback->name.'</a>';
            }
        }

        return $this->content;
    }
}
