<?php

//  For a given post, shows a report of all the ratings it has

require_once("../../config.php");
require_once("lib.php");

$id   = required_param('id', PARAM_INT);
$sort = optional_param('sort', '', PARAM_ALPHA);

$url = new moodle_url('/mod/data/report.php', array('id'=>$id));
if ($sort !== 0) {
    $url->param('sort', $sort);
}
$PAGE->set_url($url);

if (!$record = $DB->get_record('data_records', array('id'=>$id))) {
    print_error('invalidrecord', 'data');
}

if (!$data = $DB->get_record('data', array('id'=>$record->dataid))) {
    print_error('invalidid', 'data');
}

if (!$course = $DB->get_record('course', array('id'=>$data->course))) {
    print_error('coursemisconf');
}

if (!$cm = get_coursemodule_from_instance('data', $data->id, $course->id)) {
    print_error('invalidcoursemodule');
}

require_login($course->id, false, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

if (!$data->assessed) {
    print_error('norating', 'data');
}

if (!data_isowner($record->id) and !has_capability('mod/data:viewrating', $context) and !has_capability('mod/data:rate', $context)) {
    print_error('cannotviewrate', 'data');
}

switch ($sort) {
    case 'firstname': $sqlsort = "u.firstname ASC"; break;
    case 'rating':    $sqlsort = "r.rating ASC"; break;
    default:          $sqlsort = "r.id ASC";
}

$scalemenu = make_grades_menu($data->scale);

$strratings = get_string('ratings', 'data');
$strrating  = get_string('rating', 'data');
$strname    = get_string('name');

$PAGE->set_title($strratings);
echo $OUTPUT->header();

if (!$ratings = data_get_ratings($record->id, $sqlsort)) {
    print_error('noratingforrecord', 'data');

} else {
    echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"3\" class=\"generalbox\" style=\"width:100%\">";
    echo "<tr>";
    echo "<th class=\"header\" scope=\"col\">&nbsp;</th>";
    echo "<th class=\"header\" scope=\"col\"><a href=\"report.php?id=$record->id&amp;sort=firstname\">$strname</a></th>";
    echo "<th class=\"header\" scope=\"col\" style=\"width:100%\"><a href=\"report.php?id=$id&amp;sort=rating\">$strrating</a></th>";
    echo "</tr>";
    foreach ($ratings as $rating) {
        if (has_capability('mod/data:manageentries', $context)) {
            echo '<tr class="forumpostheadertopic">';
        } else {
            echo '<tr class="forumpostheader">';
        }
        echo '<td class="picture">';
        echo $OUTPUT->user_picture($rating, array('courseid'=>$data->course));
        echo '</td>';
        echo '<td class="author">' . html_writer::link($CFG->wwwroot.'/user/view.php?id='.$rating->id.'&course='.$data->course, fullname($rating)) . '</td>';
        echo '<td style="white-space:nowrap" align="center" class="rating">'.$scalemenu[$rating->rating].'</td>';
        echo "</tr>\n";
    }
    echo "</table>";
    echo "<br />";
}

echo $OUTPUT->close_window_button();
echo $OUTPUT->footer();
