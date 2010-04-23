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
 * This page receives non-ajax rating submissions
 *
 * It is similar to rate_ajax.php. Unlike rate_ajax.php a return url is required.
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
$rateduserid = required_param('rateduserid', PARAM_INT);//which user is being rated. Required to update their grade
$returnurl = required_param('returnurl', PARAM_LOCALURL);//required for non-ajax requests

$result = new stdClass;

list($context, $course, $cm) = get_context_info_array($contextid);
require_login($course, false, $cm);

//check the module rating permissions
$pluginrateallowed = true;
$pluginpermissionsarray = null;
if ($context->contextlevel==CONTEXT_MODULE) {
    $plugintype = 'mod';
    $pluginname = $cm->modname;
    $rm = new rating_manager();
    $pluginpermissionsarray = $rm->get_plugin_permissions_array($context->id, $plugintype, $pluginname);
    $pluginrateallowed = $pluginpermissionsarray['rate'];
}

if (!$pluginrateallowed || !has_capability('moodle/rating:rate',$context)) {
    echo $OUTPUT->header();
    echo get_string('ratepermissiondenied', 'ratings');
    echo $OUTPUT->footer();
    die();
}

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

//todo add a setting to turn grade updating off for those who don't want them in gradebook
//note that this needs to be done in both rate.php and rate_ajax.php
if(true){
    //tell the module that its grades have changed
    if ( !$modinstance = $DB->get_record($cm->modname, array('id' => $cm->instance)) ) {
        print_error('invalidid');
    }
    $modinstance->cmidnumber = $cm->id; //MDL-12961
    $functionname = $cm->modname.'_update_grades';
    require_once($CFG->dirroot."/mod/{$cm->modname}/lib.php");
    if(function_exists($functionname)) {
        $functionname($modinstance, $rateduserid);
    }
}

redirect($returnurl);