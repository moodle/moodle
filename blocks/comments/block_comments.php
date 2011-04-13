<?php

require_once($CFG->dirroot . '/comment/lib.php');

class block_comments extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_comments');
    }

    function specialization() {
        // require js for commenting
        comment::init();
    }
    function applicable_formats() {
        return array('all' => true);
    }

    function instance_allow_multiple() {
        return false;
    }

    function get_content() {
        global $CFG, $PAGE;
        if (!$CFG->usecomments) {
            $this->content->text = '';
            if ($this->page->user_is_editing()) {
                $this->content->text = get_string('disabledcomments');
            }
            return $this->content;
        }
        if ($this->content !== NULL) {
            return $this->content;
        }
        if (empty($this->instance)) {
            return null;
        }
        $this->content->footer = '';
        $this->content->text = '';
        list($context, $course, $cm) = get_context_info_array($PAGE->context->id);
        $args = new stdClass();
        $args->context   = $PAGE->context;
        $args->course    = $course;
        $args->area      = 'page_comments';
        $args->itemid    = 0;
        // set 'env' to tell moodle tweak ui for this block
        $args->env       = 'block_comments';
        $args->component = 'block_comments';
        $args->linktext  = get_string('showcomments');
        $args->notoggle  = true;
        $args->autostart = true;
        $args->displaycancel = true;
        $comment = new comment($args);
        $comment->set_view_permission(true);

        $this->content = new stdClass();
        $this->content->text = $comment->output(true);
        $this->content->footer = '';
        return $this->content;
    }
}
