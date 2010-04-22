<?php

//  For a given entry, shows a report of all the ratings it has
//todo andrew this file can be removed

require_once("../../config.php");
require_once("lib.php");

$id   = required_param('id', PARAM_INT);
$sort = optional_param('sort', '', PARAM_ALPHA);

$url = new moodle_url('/mod/glossary/report.php', array('id'=>$id));
if ($sort !== '') {
    $url->param('sort', $sort);
}
$PAGE->set_url($url);

if (! $entry = $DB->get_record('glossary_entries', array('id'=>$id))) {
    print_error('invalidentry');
}

if (! $glossary = $DB->get_record('glossary', array('id'=>$entry->glossaryid))) {
    print_error('invalidid', 'glossary');
}

if (! $course = $DB->get_record('course', array('id'=>$glossary->course))) {
    print_error('invalidcourseid');
}

if (! $cm = get_coursemodule_from_instance('glossary', $glossary->id, $course->id)) {
    print_error('invalidcoursemodule');
}

require_login($course, false, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

if (!$glossary->assessed) {
    print_error('nopermissiontorate');
}

if (!has_capability('mod/glossary:manageentries', $context) and $USER->id != $entry->userid) {
    print_error('nopermissiontoviewresult', 'glossary');
}

switch ($sort) {
    case 'firstname': $sqlsort = "u.firstname ASC"; break;
    case 'rating':    $sqlsort = "r.rating ASC"; break;
    default:          $sqlsort = "r.time ASC";
}

$scalemenu = make_grades_menu($glossary->scale);

$strratings = get_string('ratings', 'glossary');
$strrating  = get_string('rating', 'glossary');
$strname    = get_string('name');
$strtime    = get_string('time');

$PAGE->set_title("$strratings: $entry->concept");
echo $OUTPUT->header();

if (!$ratings = glossary_get_ratings($entry->id, $sqlsort)) {
    print_error('ratingno', 'glossary');

} else {
    echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"3\" class=\"generalbox\" style=\"width:100%\">";
    echo "<tr>";
    echo "<th class=\"header\" scope=\"col\">&nbsp;</th>";
    echo "<th class=\"header\" scope=\"col\"><a href=\"report.php?id=$entry->id&amp;sort=firstname\">$strname</a></th>";
    echo "<th class=\"header\" scope=\"col\" style=\"width:100%\"><a href=\"report.php?id=$entry->id&amp;sort=rating\">$strrating</a></th>";
    echo "<th class=\"header\" scope=\"col\"><a href=\"report.php?id=$entry->id&amp;sort=time\">$strtime</a></th>";
    echo "</tr>";
    foreach ($ratings as $rating) {
        if (has_capability('mod/glossary:manageentries', $context)) {
            echo '<tr class="teacher">';
        } else {
            echo '<tr>';
        }
        echo '<td class="picture">';
        echo $OUTPUT->user_picture($rating, array('courseid'=>$glossary->course));
        echo '</td>';
        echo '<td class="author"><a href="'.$CFG->wwwroot.'/user/view.php?id='.$rating->id.'&amp;course='.$glossary->course.'">'.fullname($rating).'</a></td>';
        echo '<td style="white-space:nowrap" align="center" class="rating">'.$scalemenu[$rating->rating].'</td>';
        echo '<td style="white-space:nowrap" align="center" class="time">'.userdate($rating->time).'</td>';
        echo "</tr>\n";
    }
    echo "</table>";
    echo "<br />";
}

echo $OUTPUT->close_window_button();
echo $OUTPUT->footer();

