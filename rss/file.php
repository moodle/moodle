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
 * @package    core_rss
 * @category   rss
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** NO_DEBUG_DISPLAY - bool, Disable moodle debug and error messages. Set to false to see any errors during RSS generation */
define('NO_DEBUG_DISPLAY', true);

/** NO_MOODLE_COOKIES - bool, Disable the use of sessions/cookies - we recreate $USER for every call. */
define('NO_MOODLE_COOKIES', true);

require_once('../config.php');
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/rsslib.php');

// RSS feeds must be enabled site-wide.
if (empty($CFG->enablerssfeeds)) {
    rss_error();
}

// All the arguments are in the path.
$relativepath = get_file_argument();
if (!$relativepath) {
    rss_error();
}

// Extract relative path components into variables.
$args = explode('/', trim($relativepath, '/'));
if (count($args) < 5) {
    rss_error();
}

$contextid   = (int)$args[0];
$token  = clean_param($args[1], PARAM_ALPHANUM);
$componentname = clean_param($args[2], PARAM_FILE);

// Check if they have requested a 1.9 RSS feed.
// If token is an int it is a user id (1.9 request).
// If token contains any letters it is a token (2.0 request).
$inttoken = intval($token);
if ($token === "$inttoken") {
    // They have requested a feed using a 1.9 url. redirect them to the 2.0 url using the guest account.

    $instanceid  = clean_param($args[3], PARAM_INT);

    // 1.9 URL puts course id where the context id is in 2.0 URLs.
    $courseid = $contextid;
    unset($contextid);

    // Find the context id.
    if ($course = $DB->get_record('course', array('id' => $courseid))) {
        $modinfo = get_fast_modinfo($course);

        foreach ($modinfo->get_instances_of($componentname) as $modinstanceid => $cm) {
            if ($modinstanceid == $instanceid) {
                $context = context_module::instance($cm->id, IGNORE_MISSING);
                break;
            }
        }
    }

    if (empty($context)) {
        // This shouldnt happen. something bad is going on.
        rss_error();
    }

    // Make sure that $CFG->siteguest is set.
    if (empty($CFG->siteguest)) {
        if (!$guestid = $DB->get_field('user', 'id', array('username' => 'guest', 'mnethostid' => $CFG->mnet_localhost_id))) {
            // Guest does not exist yet, weird.
            rss_error();
        }
        set_config('siteguest', $guestid);
    }
    $guesttoken = rss_get_token($CFG->siteguest);

    // Change forum to mod_forum (for example).
    $componentname = 'mod_'.$componentname;

    $url = $PAGE->url;
    $url->set_slashargument("/{$context->id}/$guesttoken/$componentname/$instanceid/rss.xml");

    // Redirect to the 2.0 rss URL.
    redirect($url);
} else {
    // Authenticate the user from the token.
    $userid = rss_get_userid_from_token($token);
    if (!$userid) {
        rss_error('rsserrorauth', 'rss.xml', 0, '403 Forbidden');
    }
}

// Check the context actually exists.
list($context, $course, $cm) = get_context_info_array($contextid);

$PAGE->set_context($context);

$user = get_complete_user_data('id', $userid);

// Let enrol plugins deal with new enrolments if necessary.
enrol_check_plugins($user);

\core\session\manager::set_user($user); // For login and capability checks.

try {
    $autologinguest = true;
    $setwantsurltome = true;
    $preventredirect = true;
    require_course_login($course, $autologinguest, $cm, $setwantsurltome, $preventredirect);
} catch (Exception $e) {
    if (isguestuser()) {
        rss_error('rsserrorguest', 'rss.xml', 0, '403 Forbidden');
    } else {
        rss_error('rsserrorauth', 'rss.xml', 0, '403 Forbidden');
    }
}

// Work out which component in Moodle we want (from the frankenstyle name).
$componentdir = core_component::get_component_directory($componentname);
list($type, $plugin) = core_component::normalize_component($componentname);

// Call the component to check/update the feed and tell us the path to the cached file.
$pathname = null;

if (file_exists($componentdir)) {
    require_once("$componentdir/rsslib.php");
    $functionname = $plugin.'_rss_get_feed';

    if (function_exists($functionname)) {
        // The $pathname will be null if there was a problem (eg user doesn't have the necessary capabilities).
        // NOTE:the component providing the feed must do its own capability checks and security.
        try {
            $pathname = $functionname($context, $args);
        } catch (Exception $e) {
            rss_error('rsserror');
        }
    }
}

// Check that file exists.
if (empty($pathname) || !file_exists($pathname)) {
    rss_error();
}

// Send the RSS file to the user!
send_file($pathname, 'rss.xml', 3600);   // Cached by browsers for 1 hour.

/**
 * Sends an error formatted as an rss file and then exits
 *
 * @package core_rss
 * @category rss
 *
 * @param string $error the error type, default is rsserror
 * @param string $filename the name of the file to created
 * @param int $unused
 * @param string $statuscode http 1.1 statuscode indicicating the error
 * @uses exit
 */
function rss_error($error='rsserror', $filename='rss.xml', $unused=0, $statuscode='404 Not Found') {
    header("HTTP/1.1 $statuscode");
    header('Content-Disposition: inline; filename="'.$filename.'"');
    header('Content-Type: application/xml');
    echo rss_geterrorxmlfile($error);
    exit;
}
