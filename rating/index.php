<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * A page to display a list of ratings for a given item (forum post etc)
 *
 * @package   moodlecore
 * @copyright 2010 Andrew Davis
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../config.php");
require_once("lib.php");

$contextid   = required_param('contextid', PARAM_INT);
$itemid   = required_param('itemid', PARAM_INT);
$scaleid   = required_param('scaleid', PARAM_INT);
$sort = optional_param('sort', '', PARAM_ALPHA);

list($context, $course, $cm) = get_context_info_array($contextid);
require_login($course, false, $cm);

$url = new moodle_url('/rating/index.php', array('contextid'=>$contextid,'itemid'=>$itemid,'scaleid'=>$scaleid));
if ($sort !== 0) {
    $url->param('sort', $sort);
}
$PAGE->set_url($url);

if ( !has_capability(RATING_VIEW,$context) ) {
    print_error('noviewrate', 'rating');
}
if ( !has_capability(RATING_VIEW,$context) and $USER->id != $item->userid) {
    print_error('noviewanyrate', 'rating');
}

switch ($sort) {
    case 'firstname': $sqlsort = "u.firstname ASC"; break;
    case 'rating':    $sqlsort = "r.rating ASC"; break;
    default:          $sqlsort = "r.timemodified ASC";
}

$scalemenu = make_grades_menu($scaleid);

$strratings = get_string('ratings', 'rating');
$strrating  = get_string('rating', 'rating');
$strname    = get_string('name');
$strtime    = get_string('time');

//Is there something more meaningful we can put in the title?
//$PAGE->set_title("$strratings: ".format_string($post->subject));
$PAGE->set_title("$strratings: ".format_string($itemid));
echo $OUTPUT->header();

//if (!$ratings = forum_get_ratings($post->id, $sqlsort)) {
$ratings = rating::load_ratings_for_item($context, $itemid, $sort);
if (!$ratings) {
    //print_error('noresult', 'forum', '', format_string($post->subject));
    print_error('noresult');
} else {
    echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"3\" class=\"generalbox\" style=\"width:100%\">";
    echo "<tr>";
    echo "<th class=\"header\" scope=\"col\">&nbsp;</th>";
    echo "<th class=\"header\" scope=\"col\"><a href=\"report.php?id=$itemid&amp;sort=firstname\">$strname</a></th>";
    echo "<th class=\"header\" scope=\"col\" style=\"width:100%\"><a href=\"report.php?id=$itemid&amp;sort=rating\">$strrating</a></th>";
    echo "<th class=\"header\" scope=\"col\"><a href=\"report.php?id=$itemid&amp;sort=time\">$strtime</a></th>";
    echo "</tr>";
    $user = null;
    foreach ($ratings as $rating) {
        //undo the aliasing necessary for user_picture::fields
        $user = clone($rating);//could get away with just overwriting rating->id and not cloning
        //the rating object as we don't use rating->id again. That just seems like a bad idea.
        $user->id = $user->uid;
        
        echo '<tr class="ratingitemheader">';
        echo "<td>";
        if($course && $course->id) {
            echo $OUTPUT->user_picture($rating, array('courseid'=>$course->id));
        } else {
            echo $OUTPUT->user_picture($rating);
        }
        echo '</td><td>'.fullname($rating).'</td>';
        echo '<td style="white-space:nowrap" align="center" class="rating">'.$scalemenu[$rating->rating]."</td>";
        echo '<td style="white-space:nowrap" align="center" class="time">'.userdate($rating->timemodified)."</td>";
        echo "</tr>\n";
    }
    echo "</table>";
    echo "<br />";
}

echo $OUTPUT->close_window_button();
echo $OUTPUT->footer();
