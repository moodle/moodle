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
 * This page receives rating submissions
 *
 * This page can be the target for either ajax or non-ajax rating submissions.
 * If a return url is supplied the request is presumed to be a non-ajax request so a page
 * is returned.
 * If there is no return url the request is presumed to be ajax so a json response is returned.
 *
 * @package   moodlecore
 * @copyright 2010 Andrew Davis
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once('ratinglib.php');

$contextid = required_param('contextid', PARAM_INT);
$itemid = required_param('itemid', PARAM_INT);
$scaleid = required_param('scaleid', PARAM_INT);
$userrating = required_param('rating'.$itemid, PARAM_INT);
$returnurl = optional_param('returnurl', null, PARAM_LOCALURL);//will only be supplied for non-ajax requests

$result = new stdClass;

if( !isloggedin() && !$returnurl ){ //session has expired and its an ajax request
    $result->error = get_string('sessionexpired', 'ratings');
    echo json_encode($result);
    die();
}

list($context, $course, $cm) = get_context_info_array($contextid);
require_login($course, false, $cm);

$permissions = rating::get_rating_permissions($context);
if( !$permissions[RATING_POST] ) {
    if( $returnurl ) { //if its a non-ajax request
        echo $OUTPUT->header();
        echo get_string('ratepermissiondenied', 'ratings');
        echo $OUTPUT->footer();
    }
    else {
        $result->error = get_string('ratepermissiondenied', 'ratings');
        echo json_encode($result);
    }
    die();
}

//todo andrew deny access to guest user. Petr to define "guest"

$userid = $USER->id;

$PAGE->set_url('/lib/rate.php', array(
        'contextid'=>$contextid,
        'itemid'=>$itemid,
        'scaleid'=>$scaleid,
        'rating'=>$userrating,
        'userid'=>$userid,
        'returnurl'=>$returnurl,
    ));

//todo how can we validate the forum post,glossary entry or whatever id?
//how do we know where to look for the item? how we we work from module to forum_posts, glossary_entries etc?
//if ($rating_context->contextlevel == CONTEXT_COURSE) {
//    $courseid = $rating_context->instanceid;
//    $course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
//if ($rating_context->contextlevel == CONTEXT_MODULE) {
//    $cm = get_coursemodule_from_id(false, $rating_context->instanceid, 0, false, MUST_EXIST);
//    $courseid = $cm->course;
//}

$rating = new Rating($context, $itemid, $scaleid, $userid);
$rating->update_rating($userrating);

//if its a non-ajax request
if($returnurl) {
    redirect($CFG->wwwroot.'/'.$returnurl);
}
else { //this is an ajax request
    $result = new stdClass;
    $result->success = true;
    echo json_encode($result);
    die();
}