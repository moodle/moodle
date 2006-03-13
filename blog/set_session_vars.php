<?php
/// inspired/taken from moodle calendar module's set.php file

    require_once('../config.php');
    require_once('lib.php');

    $referrer = required_param('referrer');
    $var = required_param('var');
    $value = optional_param('value');
    $userid = optional_param('userid');
    $courseid = optional_param('courseid');
    $d = optional_param('d');
    $m = optional_param('m');
    $y = optional_param('y');
    $id = optional_param('id');

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
