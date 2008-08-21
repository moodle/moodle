<?php
// Toggles the manual completion flag for a particular activity and the current
// user.

require_once('../config.php');
require_once($CFG->libdir.'/completionlib.php');

// Parameters
$cmid=required_param('id',PARAM_INT);
$targetstate=required_param('completionstate',PARAM_INT);
switch($targetstate) {
    case COMPLETION_COMPLETE:
    case COMPLETION_INCOMPLETE:
        break;
    default:
        error('Unsupported completion state');
}
$fromajax=optional_param('fromajax',0,PARAM_INT);

function error_or_ajax($message) {
    global $fromajax;
    if($fromajax) {
        print $message;
        exit;
    } else {
        error($message);
    }
}

// Get course-modules entry
if(!($cm=$DB->get_record('course_modules',array('id'=>$cmid)))) {
    error_or_ajax('Activity ID unknown');
}

if(!($course=$DB->get_record('course',array('id'=>$cm->course)))) {
    error_or_ajax('Missing course (database corrupt?)');
}

// Check user is logged in
require_login($course);

// Check completion state is manual
if($cm->completion!=COMPLETION_TRACKING_MANUAL) {
    error_or_ajax('Activity does not provide manual completion tracking');
}

// Now change state
$completion=new completion_info($course);
$completion->update_state($cm,$targetstate);

// And redirect back to course
if($fromajax) {
    print 'OK';
} else {
    // In case of use in other areas of code we allow a 'backto' parameter,
    // otherwise go back to course page
    $backto=optional_param('backto','view.php?id='.$course->id,PARAM_URL);
    redirect($backto);
}
?>
