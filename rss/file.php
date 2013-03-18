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
 * rss/file.php - entry point to serve rss streams
 *
 * This script simply checks the parameters to construct a $USER
 * then finds and calls a function in the relevant component to
 * actually check security and create the RSS stream
 *
 * @package   moodlecore
 * @copyright 1999 onwards Martin Dougiamas  http://moodle.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


// Disable moodle specific debug messages and any errors in output
define('NO_DEBUG_DISPLAY', true);//comment this out to see any error messages during RSS generation

// Sessions not used here, we recreate $USER every time we are called
define('NO_MOODLE_COOKIES', true);

require_once('../config.php');
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/rsslib.php');

// RSS feeds must be enabled site-wide
if (empty($CFG->enablerssfeeds)) {
    debugging('DISABLED (admin variables)');
    rss_error();
}


// All the arguments are in the path
$relativepath = get_file_argument();
if (!$relativepath) {
    rss_error();
}


// Extract relative path components into variables
$args = explode('/', trim($relativepath, '/'));
if (count($args) < 5) {
    rss_error();
}

$contextid   = (int)$args[0];
$token  = clean_param($args[1], PARAM_ALPHANUM);
$componentname = clean_param($args[2], PARAM_FILE);

//check if they have requested a 1.9 RSS feed
//if token is an int its a user id (1.9 request)
//if token contains any letters its a token (2.0 request)
$inttoken = intval($token);
if ($token==="$inttoken") {
    //they've requested a feed using a 1.9 url. redirect them to the 2.0 url using the guest account

    $instanceid  = clean_param($args[3], PARAM_INT);

    //1.9 URL puts course id where the context id is in 2.0 URLs
    $courseid = $contextid;
    unset($contextid);

    //find the context id
    if ($course = $DB->get_record('course', array('id' => $courseid))) {
        $modinfo =& get_fast_modinfo($course);

        if (!isset($modinfo->instances[$componentname])) {
            $modinfo->instances[$componentname] = array();
        }

        foreach ($modinfo->instances[$componentname] as $modinstanceid=>$cm) {
            if ($modinstanceid==$instanceid) {
                $context = get_context_instance(CONTEXT_MODULE, $cm->id);
                break;
            }
        }
    }

    if (empty($context)) {
        //this shouldnt happen. something bad is going on.
        rss_error('rsserror');
    }

    //make sure that $CFG->siteguest is set
    if (empty($CFG->siteguest)) {
        if (!$guestid = $DB->get_field('user', 'id', array('username'=>'guest', 'mnethostid'=>$CFG->mnet_localhost_id))) {
            // guest does not exist yet, weird
            rss_error('rsserror');
        }
        set_config('siteguest', $guestid);
    }
    $guesttoken = rss_get_token($CFG->siteguest);

    //change forum to mod_forum (for example)
    $componentname = 'mod_'.$componentname;

    $url = $PAGE->url;
    $url->set_slashargument("/{$context->id}/$guesttoken/$componentname/$instanceid/rss.xml");

    //redirect to the 2.0 rss URL
    redirect($url);
} else {
    // Authenticate the user from the token
    $userid = rss_get_userid_from_token($token);
    if (!$userid) {
        rss_error('rsserrorauth');
    }
}

// Check the context actually exists
list($context, $course, $cm) = get_context_info_array($contextid);

$PAGE->set_context($context);

$user = get_complete_user_data('id', $userid);

// let enrol plugins deal with new enrolments if necessary
enrol_check_plugins($user);

session_set_user($user); //for login and capability checks

try {
    $autologinguest = true;
    $setwantsurltome = true;
    $preventredirect = true;
    require_login($course, $autologinguest, $cm, $setwantsurltome, $preventredirect);
} catch (Exception $e) {
    if (isguestuser()) {
        rss_error('rsserrorguest');
    } else {
        rss_error('rsserrorauth');
    }
}

// Work out which component in Moodle we want (from the frankenstyle name)
$componentdir = get_component_directory($componentname);
list($type, $plugin) = normalize_component($componentname);


// Call the component to check/update the feed and tell us the path to the cached file
$pathname = null;

if (file_exists($componentdir)) {
    require_once("$componentdir/rsslib.php");
    $functionname = $plugin.'_rss_get_feed';

    if (function_exists($functionname)) {
        // $pathname will be null if there was a problem (eg user doesn't have the necessary capabilities)
        // NOTE:the component providing the feed must do its own capability checks and security
        try {
            $pathname = $functionname($context, $args);
        } catch (Exception $e) {
            rss_error('rsserror');
        }
    }
}


// Check that file exists
if (empty($pathname) || !file_exists($pathname)) {
    rss_error();
}

// Send the RSS file to the user!
send_file($pathname, 'rss.xml', 3600);   // Cached by browsers for 1 hour


/*
 * Sends an error formatted as an rss file and then dies
 */
function rss_error($error='rsserror', $filename='rss.xml', $lifetime=0) {
    send_file(rss_geterrorxmlfile($error), $filename, $lifetime, false, true);
    exit;
}
