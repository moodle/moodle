<?php
/// inspired/taken from moodle calendar module's set.php file

    require_once('../config.php');
    require_once('lib.php');

    require_variable($_GET['referrer']);
    require_variable($_GET['var']);
    optional_variable($_GET['value']);
    optional_variable($_GET['userid']);
    optional_variable($_GET['courseid']);
    optional_variable($_GET['categoryid']);
    optional_variable($_GET['d']);
    optional_variable($_GET['m']);
    optional_variable($_GET['y']);

    switch($_GET['var']) {
        case 'setcourse':
            $id = intval($_GET['id']);
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