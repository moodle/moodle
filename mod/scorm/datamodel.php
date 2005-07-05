<?php
    require_once('../../config.php');
    require_once('lib.php');
    
    optional_variable($id);    // Course Module ID, or
    optional_variable($a);     // scorm IDa

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
    
    if (confirm_sesskey() && (isset($_POST['scoid']))) {
        $scoid = $_POST['scoid'];
        $result = true;
        foreach ($_POST as $element => $value) {
            if (substr($element,0,3) == 'cmi') {
                $element = str_replace('__','.',$element);
                $element = preg_replace('/_(\d+)/',".\$1",$element);
                if ($track = get_record_select('scorm_scoes_track',"userid='$USER->id' AND scormid='$scorm->id' AND scoid='$scoid' AND element='$element'")) {
                    $track->value = $value;
		    $track->timemodified = time();
                    $result = update_record('scorm_scoes_track',$track) && $result;
                } else {
                    $track->userid = $USER->id;
                    $track->scormid = $scorm->id;
                    $track->scoid = $scoid;
                    $track->element = $element;
                    $track->value = $value;
		    $track->timemodified = time();
                    $result = insert_record('scorm_scoes_track',$track) && $result;
                }
            }
        }
        if ($result) {
            echo "true\n0";
        } else {
            echo "false\n101";
        }
    }
?>

