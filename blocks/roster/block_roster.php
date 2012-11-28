<?php

class block_roster extends block_list {
    function init() {
        $this->title = get_string('pluginname', 'block_roster');
    }

    function get_content() {

        global $CFG, $OUTPUT;

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        // user/index.php expect course context, so get one if page has module context.
        $currentcontext = $this->page->context->get_course_context(false);

        if (empty($currentcontext)) {
            $this->content = '';
            return $this->content;
        } else if ($this->page->course->id == SITEID) {
            if (!has_capability('moodle/site:viewparticipants', get_context_instance(CONTEXT_SYSTEM))) {
                $this->content = '';
                return $this->content;
            }
        } else {
            if (!has_capability('moodle/course:viewparticipants', $currentcontext)) {
                $this->content = '';
                return $this->content;
            }
        }

        $icon = '<img src="'.$OUTPUT->pix_url('i/users') . '" class="icon" alt="" />&nbsp;';
        // TODO: Localize the "Roster" string in the anchor text using get_string
        $this->content->items[] = '<a title="'.get_string('listofallpeople').'" href="'.
                                  $CFG->wwwroot.'/user/roster.php?contextid='.$currentcontext->id.'">'.$icon.'Roster</a>';

        return $this->content;
    }

    // my moodle can only have SITEID and it's redundant here, so take it away
    function applicable_formats() {
        return array('all' => true, 'my' => false, 'tag' => false);
    }

}
