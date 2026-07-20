<?php
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
// Only allow running on local installations.
local_courseanalytics_ensure_local();
require_login();
global $DB, $USER, $PAGE, $OUTPUT;

$PAGE->set_url(new moodle_url('/local/courseanalytics/student.php'));
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_title('Student Activity Dashboard');
$PAGE->set_heading('Student Activity Dashboard');

echo $OUTPUT->header();

$courses = enrol_get_users_courses($USER->id, true);
$enrolledcount = count($courses);
$completedcount = 0;
$pendingassignments = 0;
$upcomingquizzes = 0;
$logincount = 0;
$lastlogin = $USER->lastlogin ? userdate($USER->lastlogin) : 'n/a';

foreach ($courses as $course) {
    $comp = $DB->get_record('course_completions', ['userid'=>$USER->id, 'course'=>$course->id]);
    if ($comp && $comp->timecompleted) {
        $completedcount++;
    }
    // pending assignments for this user in this course
    $assigns = $DB->get_records('assign', ['course'=>$course->id]);
    foreach ($assigns as $a) {
        $sub = $DB->get_record('assign_submission', ['assignment'=>$a->id, 'userid'=>$USER->id, 'latest'=>1]);
        if (!$sub || ($sub && $sub->status !== 'submitted')) {
            $pendingassignments++;
        }
    }
    // upcoming quizzes
    $now = time();
    $upcomingquizzes += $DB->count_records_sql('SELECT COUNT(*) FROM {quiz} q WHERE q.course = ? AND q.timeopen > ? ORDER BY q.timeopen', [$course->id, $now]);
}

// login count (approx) from legacy log table
$logincount = $DB->count_records('log', ['userid'=>$USER->id, 'action'=>'login']);

$avgcompletion = $enrolledcount ? round(($completedcount / $enrolledcount) * 100, 1) : 0;

echo html_writer::alist([
    'Enrolled courses: ' . $enrolledcount,
    'Completed courses: ' . $completedcount,
    'Pending assignments: ' . $pendingassignments,
    'Upcoming quizzes: ' . $upcomingquizzes,
    'Login count: ' . $logincount,
    'Last login: ' . $lastlogin,
    'Average completion %: ' . $avgcompletion . '%'
]);

// course list with detail links and warning for completion < 50%
echo html_writer::start_tag('h3'); echo 'Courses'; echo html_writer::end_tag('h3');
echo html_writer::start_tag('ul');
foreach ($courses as $course) {
    $comp = $DB->get_record('course_completions', ['userid'=>$USER->id, 'course'=>$course->id]);
    $pct = ($comp && $comp->timecompleted) ? 100 : 0;
    $warn = $pct < 50 ? ' <strong style="color:red;">Warning: completion below 50%</strong>' : '';
    $link = new moodle_url('/course/view.php', ['id'=>$course->id]);
    echo html_writer::tag('li', html_writer::link($link, format_string($course->fullname)) . ' - Completion: ' . $pct . '%' . $warn);
}
echo html_writer::end_tag('ul');

echo $OUTPUT->footer();
