<?php
    require_once('../../config.php');
    require_once('lib.php');
    
    optional_variable($id);    // Course Module ID, or
    optional_variable($a);     // scorm ID

    if ($id) {
        if (! $cm = get_record('course_modules', 'id', $id)) {
            error('Course Module ID was incorrect');
        }
    
        if (! $course = get_record('course', 'id', $cm->course)) {
            error('Course is misconfigured');
        }
    
        if (! $scorm = get_record('scorm', 'id', $cm->instance)) {
            error('Course module is incorrect');
        }

    } else {
        if (! $scorm = get_record('scorm', 'id', $a)) {
            error('Course module is incorrect');
        }
        if (! $course = get_record('course', 'id', $scorm->course)) {
            error('Course is misconfigured');
        }
        if (! $cm = get_coursemodule_from_instance('scorm', $scorm->id, $course->id)) {
            error('Course Module ID was incorrect');
        }
    }

    require_login($course->id, false, $cm);
    
    if (isset($_POST['call']) && confirm_sesskey()) {
	
    }
?>

