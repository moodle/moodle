<?php // $Id$

/**
 * Extend the base assignment class for offline assignments
 *
 */
class assignment_offline extends assignment_base {

    function assignment_offline($cmid=0) {
        parent::assignment_base($cmid);
    }

    function setup($form) {
        global $CFG, $usehtmleditor;

        parent::setup($form);
        include("$CFG->dirroot/mod/assignment/type/offline/mod.html");
        parent::setup_end(); 
    }

    function submittedlink() {
        global $USER;

        $submitted = '';

        if (isteacher($this->course->id)) {
            $submitted =  '<a href="submissions.php?id='.$this->cm->id.'">' .
                           get_string('viewfeedback', 'assignment') . '</a>';
        } else {
            if (isset($USER->id)) {
                if ($submission = $this->get_submission($USER->id)) {
                    if ($submission->timemodified) {
                        if ($submission->timemodified <= $this->assignment->timedue) {
                            $submitted = '<span class="early">'.userdate($submission->timemodified).'</span>';
                        } else {
                            $submitted = '<span class="late">'.userdate($submission->timemodified).'</span>';
                        }
                    }
                }
            }
        }

        return $submitted;
    }



}

?>
