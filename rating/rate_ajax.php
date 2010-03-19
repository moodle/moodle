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
* This page receives ajax rating submissions
 *
 * It is similar to rate.php. Unlike rate.php a return url is NOT required.
 *
 * @package   moodlecore
 * @copyright 2010 Andrew Davis
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once('lib.php');

$contextid = required_param('contextid', PARAM_INT);
$itemid = required_param('itemid', PARAM_INT);
$scaleid = required_param('scaleid', PARAM_INT);
$userrating = required_param('rating', PARAM_INT);

$result = new stdClass;

//if session has expired and its an ajax request so we cant do a page redirect
if( !isloggedin() ){
    $result->error = get_string('sessionerroruser', 'error');
    echo json_encode($result);
    die();
}

list($context, $course, $cm) = get_context_info_array($contextid);
require_login($course, false, $cm);

if( !has_capability('moodle/rating:rate',$context) ) {
    $result->error = get_string('ratepermissiondenied', 'ratings');
    echo json_encode($result);
    die();
}

//todo andrew deny access to guest user. Petr to define "guest"

$userid = $USER->id;

$PAGE->set_url('/lib/rate.php', array(
        'contextid'=>$contextid
    ));

//todo how can we validate the forum post,glossary entry or whatever id?
//how do we know where to look for the item? how we we work from module to forum_posts, glossary_entries etc?

$ratingoptions = new stdclass;
$ratingoptions->context = $context;
$ratingoptions->itemid  = $itemid;
$ratingoptions->scaleid = $scaleid;
$ratingoptions->userid  = $userid;
$rating = new rating($ratingoptions);

$rating->update_rating($userrating);

$result = new stdClass;
$result->success = true;
echo json_encode($result);