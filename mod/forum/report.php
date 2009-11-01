<?php

//  For a given post, shows a report of all the ratings it has

require_once("../../config.php");
require_once("lib.php");

$id   = required_param('id', PARAM_INT);
$sort = optional_param('sort', '', PARAM_ALPHA);

$url = new moodle_url($CFG->wwwroot.'/mod/forum/report.php', array('id'=>$id));
if ($sort !== 0) {
    $url->param('sort', $sort);
}
$PAGE->set_url($url);

if (! $post = $DB->get_record('forum_posts', array('id' => $id))) {
    print_error('invalidpostid','forum');
}

if (! $discussion = $DB->get_record('forum_discussions', array('id' => $post->discussion))) {
    print_error('invaliddiscussion', 'forum');
}

if (! $forum = $DB->get_record('forum', array('id' => $discussion->forum))) {
    print_error('invalidforumid', 'forum');
}

if (! $course = $DB->get_record('course', array('id' => $forum->course))) {
    print_error('invalidcourseid');
}

if (! $cm = get_coursemodule_from_instance('forum', $forum->id, $course->id)) {
    print_error('invalidcoursemodule');
}

require_login($course, false, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

if (!$forum->assessed) {
    print_error('norate', 'forum');
}

if (!has_capability('mod/forum:viewrating', $context)) {
    print_error('noviewrate', 'forum');
}
if (!has_capability('mod/forum:viewanyrating', $context) and $USER->id != $post->userid) {
    print_error('noviewanyrate', 'forum');
}

switch ($sort) {
    case 'firstname': $sqlsort = "u.firstname ASC"; break;
    case 'rating':    $sqlsort = "r.rating ASC"; break;
    default:          $sqlsort = "r.time ASC";
}

$scalemenu = make_grades_menu($forum->scale);

$strratings = get_string('ratings', 'forum');
$strrating  = get_string('rating', 'forum');
$strname    = get_string('name');
$strtime    = get_string('time');

$PAGE->set_title("$strratings: ".format_string($post->subject));
echo $OUTPUT->header();

if (!$ratings = forum_get_ratings($post->id, $sqlsort)) {
    print_error('noresult', 'forum', '', format_string($post->subject));

} else {
    echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"3\" class=\"generalbox\" style=\"width:100%\">";
    echo "<tr>";
    echo "<th class=\"header\" scope=\"col\">&nbsp;</th>";
    echo "<th class=\"header\" scope=\"col\"><a href=\"report.php?id=$post->id&amp;sort=firstname\">$strname</a></th>";
    echo "<th class=\"header\" scope=\"col\" style=\"width:100%\"><a href=\"report.php?id=$post->id&amp;sort=rating\">$strrating</a></th>";
    echo "<th class=\"header\" scope=\"col\"><a href=\"report.php?id=$post->id&amp;sort=time\">$strtime</a></th>";
    echo "</tr>";
    foreach ($ratings as $rating) {
        echo '<tr class="forumpostheader">';
        echo "<td>";
        echo $OUTPUT->user_picture(moodle_user_picture::make($rating, $forum->course));
        echo '</td><td>'.fullname($rating).'</td>';
        echo '<td style="white-space:nowrap" align="center" class="rating">'.$scalemenu[$rating->rating]."</td>";
        echo '<td style="white-space:nowrap" align="center" class="time">'.userdate($rating->time)."</td>";
        echo "</tr>\n";
    }
    echo "</table>";
    echo "<br />";
}

echo $OUTPUT->close_window_button();
echo $OUTPUT->footer();

