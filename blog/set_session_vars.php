<?php
/// inspired/taken from moodle calendar module's set.php file

    require_once('../config.php');
    require_once('lib.php');

    $referrer = required_param('referrer', PARAM_NOTAGS);
    $var = required_param('var',PARAM_ALPHA);
    $value = optional_param('value','', PARAM_NOTAGS);
    $userid = optional_param('userid',0 , PARAM_INT);
    $courseid = optional_param('courseid',0, PARAM_INT);
    $d = optional_param('d', 0, PARAM_INT);
    $m = optional_param('m', 0, PARAM_INT);
    $y = optional_param('y', 0, PARAM_INT);
    $id = optional_param('id', 0, PARAM_INT);

    switch($var) {
        case 'setcourse':
            $id = intval($id);
            if($id == 0) {
                $SESSION->cal_courses_shown = array();
                calendar_set_referring_course(0);
            }
            else if($id == 1) {
                $SESSION->cal_courses_shown = calendar_get_default_courses(true);
                calendar_set_referring_course(0);
            }
            else {
                // We don't check for membership anymore: if(isstudent($id, $USER->id) || isteacher($id, $USER->id)) {
                if(get_record('course', 'id', $id) === false) {
                    // There is no such course
                    $SESSION->cal_courses_shown = array();
                    calendar_set_referring_course(0);
                }
                else {
                    calendar_set_referring_course($id);
                    $SESSION->cal_courses_shown = $id;
                }
            }
        break;
        case 'setcategory':
        break;
        case 'setblog':
        break;
        case 'showediting':
            $SESSION->blog_editing_enabled = !$SESSION->blog_editing_enabled;
        break;
    }
    redirect($referrer);
?>
