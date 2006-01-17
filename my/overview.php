<?php

require_once(dirname(dirname(__FILE__)).'/config.php');
require_once(dirname(dirname(__FILE__)).'/course/lib.php');
require_login();

$courses = get_my_courses($USER->id);
$site = get_site();

if (array_key_exists($site->id,$courses)) {
    unset($courses[$site->id]);
}

foreach ($courses as $course) {
    if (array_key_exists($course->id,$USER->timeaccess)) {
        $courses[$course->id]->lastaccess = $USER->timeaccess[$course->id];
    } else {
        $courses[$course->id]->lastaccess = 0;
    }
}

if (empty($courses)) {
    print_simple_box(get_string('nocourses','my'),'center');
}
else {
    print_overview($courses);
}

?>