<?php  // $Id$
/*
resetcourse.php  - Mark Flach and moodle.com
The purpose of this feature is to quickly remove all user related data from a course
in order to make it available for a new semester.  This feature can handle the removal
of general course data like students, teachers, logs, events and groups as well as module
specific data.  Each module must be modified to take advantage of this new feature.
The feature will also reset the start date of the course if necessary.
*/

require('../config.php');
require_once('reset_form.php');

$id = required_param('id', PARAM_INT);

if (!$course = get_record('course', 'id', $id)) {
    error("Course is misconfigured");
}

require_login($course);
require_capability('moodle/course:update', get_context_instance(CONTEXT_COURSE, $course->id));

$strreset       = get_string('reset');
$strresetcourse = get_string('resetcourse');
$strremove      = get_string('remove');

$navlinks = array(array('name' => $strresetcourse, 'link' => null, 'type' => 'misc'));
$navigation = build_navigation($navlinks);

$mform = new course_reset_form();

if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot.'/course/view.php?id='.$id);

} else if ($data = $mform->get_data(false)) { // no magic quotes

    if (isset($data->selectdefault)) {
        $_POST = array();
        $mform = new course_reset_form();
        $mform->load_defaults();

    } else if (isset($data->deselectall)) {
        $_POST = array();
        $mform = new course_reset_form();

    } else {
        print_header($course->fullname.': '.$strresetcourse, $course->fullname.': '.$strresetcourse, $navigation);
        print_heading($strresetcourse);

        $data->reset_start_date_old = $course->startdate;
        $status = reset_course_userdata($data);

        $data = array();;
        foreach ($status as $item) {
            $line = array();
            $line[] = $item['component'];
            $line[] = $item['item'];
            $line[] = ($item['error']===false) ? get_string('ok') : '<div class="notifyproblem">'.$item['error'].'</div>';
            $data[] = $line;
        }

        $table = new object();
        $table->head  = array(get_string('resetcomponent'), get_string('resettask'), get_string('resetstatus'));
        $table->size  = array('20%', '40%', '40%');
        $table->align = array('left', 'left', 'left');
        $table->width = '80%';
        $table->data  = $data;
        print_table($table);

        print_continue('view.php?id='.$course->id);  // Back to course page
        print_footer($course);
        exit;
    }
}

print_header($course->fullname.': '.$strresetcourse, $course->fullname.': '.$strresetcourse, $navigation);
print_heading($strresetcourse);

print_simple_box(get_string('resetinfo'), 'center', '60%');

$mform->display();
print_footer($course);

?>
