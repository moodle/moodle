<?php

global $CFG;
require_once($CFG->dirroot . '/course/edit_form.php');

class testable_course_edit_form extends course_edit_form {

    /**
     * Expose the internal moodleform's MoodleQuickForm
     *
     * @return MoodleQuickForm
     */
    public function get_quick_form() {
        return $this->_form;
    }
}
