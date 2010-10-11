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
 * @package    core
 * @subpackage rating
 * @copyright  2010 Andrew Davis
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../config.php");
require_once("lib.php");

$contextid   = required_param('contextid', PARAM_INT);
$itemid   = required_param('itemid', PARAM_INT);
$scaleid   = required_param('scaleid', PARAM_INT);
$sort = optional_param('sort', '', PARAM_ALPHA);
$popup = optional_param('popup', 0, PARAM_INT);//==1 if in a popup window?

list($context, $course, $cm) = get_context_info_array($contextid);
require_login($course, false, $cm);

$url = new moodle_url('/rating/index.php', array('contextid'=>$contextid,'itemid'=>$itemid,'scaleid'=>$scaleid));
if ($sort !== 0) {
    $url->param('sort', $sort);
}
$PAGE->set_url($url);
$PAGE->set_context($context);

if ($popup) {
    $PAGE->set_pagelayout('popup');
}

if (!has_capability('moodle/rating:view',$context)) {
    print_error('noviewrate', 'rating');
}
if (!has_capability('moodle/rating:viewall',$context) and $USER->id != $item->userid) {
    print_error('noviewanyrate', 'rating');
}

switch ($sort) {
    case 'firstname': $sqlsort = "u.firstname ASC"; break;
    case 'rating':    $sqlsort = "r.rating ASC"; break;
    default:          $sqlsort = "r.timemodified ASC";
}

$scalemenu = make_grades_menu($scaleid);

$strrating  = get_string('rating', 'rating');
$strname    = get_string('name');
$strtime    = get_string('time');

$PAGE->set_title(get_string('allratingsforitem','rating'));
echo $OUTPUT->header();

$ratingoptions = new stdclass();
$ratingoptions->context = $context;
$ratingoptions->itemid = $itemid;
$ratingoptions->sort = $sqlsort;

$rm = new rating_manager();
$ratings = $rm->get_all_ratings_for_item($ratingoptions);
if (!$ratings) {
    $msg = get_string('noratings','rating');
    echo html_writer::tag('div', $msg, array('class'=>'mdl-align'));
} else {
    $sortargs = "contextid=$contextid&amp;itemid=$itemid&amp;scaleid=$scaleid";
    if($popup) {
        $sortargs.="&amp;popup=$popup";
    }
    echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"3\" class=\"generalbox\" style=\"width:100%\">";
    echo "<tr>";
    echo "<th class=\"header\" scope=\"col\">&nbsp;</th>";
    echo "<th class=\"header\" scope=\"col\"><a href=\"index.php?$sortargs&amp;sort=firstname\">$strname</a></th>";
    echo "<th class=\"header\" scope=\"col\" style=\"width:100%\"><a href=\"index.php?$sortargs&amp;sort=rating\">$strrating</a></th>";
    echo "<th class=\"header\" scope=\"col\"><a href=\"index.php?$sortargs&amp;sort=time\">$strtime</a></th>";
    echo "</tr>";

    $maxrating = count($scalemenu);
    foreach ($ratings as $rating) {
        //Undo the aliasing of the user id column from user_picture::fields()
        //we could clone the rating object or preserve the rating id if we needed it again
        //but we don't
        $rating->id = $rating->uid;

        echo '<tr class="ratingitemheader">';
        echo "<td>";
        if($course && $course->id) {
            echo $OUTPUT->user_picture($rating, array('courseid'=>$course->id));
        } else {
            echo $OUTPUT->user_picture($rating);
        }
        echo '</td><td>'.fullname($rating).'</td>';
        
        //if they've switched to rating out of 5 but there were ratings submitted out of 10 for example
        //Not doing this within $rm->get_all_ratings_for_item to allow access to the raw data
        if ($rating->rating > $maxrating) {
            $rating->rating = $maxrating;
        }
        echo '<td style="white-space:nowrap" align="center" class="rating">'.$scalemenu[$rating->rating]."</td>";
        echo '<td style="white-space:nowrap" align="center" class="time">'.userdate($rating->timemodified)."</td>";
        echo "</tr>\n";
    }
    echo "</table>";
    echo "<br />";
}

if ($popup) {
    echo $OUTPUT->close_window_button();
}
echo $OUTPUT->footer();
