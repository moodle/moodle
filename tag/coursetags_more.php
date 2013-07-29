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
 * A full display of tags allowing some filtering and reordering
 *
 * @package    core_tag
 * @category   tag
 * @copyright  2007 j.beedell@open.ac.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->dirroot.'/tag/coursetagslib.php');
require_once($CFG->dirroot.'/tag/lib.php');

$sort = optional_param('sort', 'alpha', PARAM_ALPHA); //alpha, date or popularity
$show = optional_param('show', 'all', PARAM_ALPHA); //all, my, official, community or course
$courseid = optional_param('courseid', 0, PARAM_INT);

$url = new moodle_url('/tag/coursetags_more.php');
if ($sort !== 'alpha') {
    $url->param('sort', $sort);
}
if ($show !== 'all') {
    $url->param('show', $show);
}
if ($courseid !== 0) {
    $url->param('courseid', $courseid);
}
$PAGE->set_url($url);

if (empty($CFG->usetags)) {
    print_error('tagsaredisabled', 'tag');
}

// Some things require logging in
if ($CFG->forcelogin or $show == 'my') {
    require_login();
}

// Permissions
$loggedin = isloggedin() && !isguestuser();

// Course check
if ($courseid) {
    if (!($course = $DB->get_record('course', array('id'=>$courseid)))) {
        $courseid = 0;
    }
    if ($courseid == SITEID) $courseid = 0;
}

if ($courseid) {
    $PAGE->set_context(context_course::instance($courseid));
} else {
    $PAGE->set_context(get_system_context());
}

// Language strings
$tagslang = 'block_tags';
$title = get_string('moretitle', $tagslang);
$link1 = get_string('moreshow', $tagslang);
$link2 = get_string('moreorder', $tagslang);
$showalltags = get_string('moreshowalltags', $tagslang);
$showofficialtags = get_string('moreshowofficialtags', $tagslang);
$showmytags = get_string('moreshowmytags', $tagslang);
$showcommtags = get_string('moreshowcommtags', $tagslang);
$orderalpha = get_string('moreorderalpha', $tagslang);
$orderdate = get_string('moreorderdate', $tagslang);
$orderpop = get_string('moreorderpop', $tagslang);
$welcome = get_string('morewelcome', $tagslang);

// The title and breadcrumb
if ($courseid) {
    $courseshortname = format_string($course->shortname, true, array('context' => context_course::instance($courseid)));
    $PAGE->navbar->add($courseshortname, new moodle_url('/course/view.php', array('id'=>$courseid)));
}
$PAGE->navbar->add($title);
$PAGE->set_title($title);
$PAGE->set_heading($COURSE->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading($title, 2, 'centre');

// Prepare data for tags
$courselink = '';
if ($courseid) {
    $courselink = '&amp;courseid='.$courseid;
}
$myurl = $CFG->wwwroot.'/tag/coursetags_more.php';
$myurl2 = $CFG->wwwroot.'/tag/coursetags_more.php?show='.$show;

if ($show == 'course' and $courseid) { // Course tags.
    $tags = tag_print_cloud(coursetag_get_tags($courseid, 0, ''), 150, true, $sort);
} else if ($show == 'my' and $loggedin) { // My tags.
    $tags = tag_print_cloud(coursetag_get_tags(0, $USER->id, 'default'), 150, true, $sort);
} else if ($show == 'official') { // Official course tags.
    $tags = tag_print_cloud(coursetag_get_tags(0, 0, 'official'), 150, true, $sort);
} else if ($show == 'community') { // Community (official and personal together) also called user tags.
    $tags = tag_print_cloud(coursetag_get_tags(0, 0, 'default'), 150, true, $sort);
} else {
    // All tags for courses and blogs and any thing else tagged - the fallback default ($show == all).
    $subtitle = $showalltags;
    $tags = tag_print_cloud(coursetag_get_all_tags(), 150, true, $sort);
}

// Prepare the links for the show and order lines
if ($show == 'all') {
    $link1 .= '<b>'.$showalltags.'</b>';
} else {
    $link1 .= '<a href="'.$myurl.'?show=all'.$courselink.'">'.$showalltags.'</a>';
}
//if ($show == 'official') { //add back in if you start to use official course tags
//    $link1 .= ' | <b>'.$showofficialtags.'</b>';
//} else {
//    $link1 .= ' | <a href="'.$myurl.'?show=official'.$courselink.'">'.$showofficialtags.'</a>';
//}
if ($show == 'community') {
    $link1 .= ' | <b>'.$showcommtags.'</b>';
} else {
    $link1 .= ' | <a href="'.$myurl.'?show=community'.$courselink.'">'.$showcommtags.'</a>';
}
if ($loggedin) {
    if ($show == 'my') {
        $link1 .= ' | <b>'.$showmytags.'</b>';
    } else {
        $link1 .= ' | <a href="'.$myurl.'?show=my'.$courselink.'">'.$showmytags.'</a>';
    }
}
if ($courseid) {
    $fullname = format_string($course->fullname, true, array('context' => context_course::instance($course->id)));
    if ($show == 'course') {
        $link1 .= ' | <b>'.get_string('moreshowcoursetags', $tagslang, $fullname).'</b>';
    } else {
        $link1 .= ' | <a href="'.$myurl.'?show=course'.$courselink.'">'.get_string('moreshowcoursetags', $tagslang, $fullname).'</a>';
    }
}
if ($sort == 'alpha') {
    $link2 .= '<b>'.$orderalpha.'</b> | ';
} else {
    $link2 .= '<a href="'.$myurl2.'&amp;sort=alpha'.$courselink.'">'.$orderalpha.'</a> | ';
}
if ($sort == 'popularity') {
    $link2 .= '<b>'.$orderpop.'</b> | ';
} else {
    $link2 .= '<a href="'.$myurl2.'&amp;sort=popularity'.$courselink.'">'.$orderpop.'</a> | ';
}
if ($sort == 'date') {
    $link2 .= '<b>'.$orderdate.'</b>';
} else {
    $link2 .= '<a href="'.$myurl2.'&amp;sort=date'.$courselink.'">'.$orderdate.'</a>';
}

// Prepare output
$fclass = '';
// make the tags larger when there are not so many
if (strlen($tags) < 10000) {
    $fclass = 'coursetag_more_large';
}
$outstr = '
<div class="coursetag_more_title">
<div style="padding-bottom:5px">'.$welcome.'</div>
<div class="coursetag_more_link">'.$link1.'</div>
<div class="coursetag_more_link">'.$link2.'</div>
</div>
<div class="coursetag_more_tags '.$fclass.'">'.
$tags.'
</div>';
echo $outstr;

echo $OUTPUT->footer();
