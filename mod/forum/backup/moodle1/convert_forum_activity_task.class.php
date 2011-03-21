<?php

require_once($CFG->dirroot.'/mod/forum/backup/moodle1/convert_forum_stepslib.php');

/**
 * Convert from Moodle 1 forum task
 */
class moodle1_forum_activity_task extends moodle1_activity_task {
    /**
     * Function responsible for building the steps of any task
     * (must set the $built property to true)
     */
    public function build() {
        $this->add_step(new moodle1_forum_activity_structure_step('forum'));
        $this->built = true;
    }
}