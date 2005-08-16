<?php

require_once(dirname(dirname(__FILE__)).'/config.php');
require_once(dirname(dirname(__FILE__)).'/course/lib.php');
require_login();

$courses = get_my_courses($USER->id);
$site = get_site();

if (array_key_exists($site->id,$courses)) {
    unset($courses[$site->id]);
}

if (empty($courses)) {
    print_simple_box(get_string('nocourses','my'),'center');
}
else {
    foreach ($courses as $course) {
        print_overview($course);
    }
}

?>