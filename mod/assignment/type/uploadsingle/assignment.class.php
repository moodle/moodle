<?php // $Id$

/**
 * Extend the base assignment class for assignments where you upload a single file
 *
 */
class assignment_uploadsingle extends assignment_base {

    function assignment_uploadsingle($cmid=0) {
        parent::assignment_base($cmid);

    }

    function setup($form) {
        global $CFG, $usehtmleditor;

        parent::setup($form);
        include("$CFG->dirroot/mod/assignment/type/uploadsingle/mod.html");
        parent::setup_end(); 
    }

    function submittedlink() {
        global $USER;

        $submitted = '';

        if (isteacher($this->course->id)) {
            if ($this->currentgroup and isteacheredit($this->course->id)) {
                $group = get_record('groups', 'id', $this->currentgroup);
                $groupname = ' ('.$group->name.')';
            } else {
                $groupname = '';
            }
            $count = $this->count_real_submissions($this->currentgroup);
            $submitted = '<a href="submissions.php?id='.$this->cm->id.'">'.
                         get_string('viewsubmissions', 'assignment', $count).'</a>'.$groupname;
        } else {
            if (isset($USER->id)) {
                if ($submission = $this->get_submission($USER)) {
                    if ($submission->timemodified <= $this->assignment->timedue) {
                        $submitted = userdate($submission->timemodified);
                    } else {
                        $submitted = '<span class="late">'.userdate($submission->timemodified).'</span>';
                    }
                }
            }
        }

        return $submitted;
    }


}

?>
