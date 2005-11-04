<?php // $Id$

/**
 * Extend the base assignment class for offline assignments
 *
 */
class assignment_offline extends assignment_base {

    function assignment_offline($cmid=0) {
        parent::assignment_base($cmid);
    }

    function display_lateness($timesubmitted) {
        return '';
    }
    function print_student_answer($studentid){
        return '';//does nothing!
    }

}

?>
