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
 * This script delegates file serving to individual plugins
 *
 * @package    core
 * @subpackage file
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// disable moodle specific debug messages and any errors in output
//define('NO_DEBUG_DISPLAY', true);
//TODO: uncomment this once the file api stabilises a bit more

require_once('config.php');
require_once('lib/filelib.php');

$relativepath = get_file_argument();
$forcedownload = optional_param('forcedownload', 0, PARAM_BOOL);

// relative path must start with '/'
if (!$relativepath) {
    print_error('invalidargorconf');
} else if ($relativepath[0] != '/') {
    print_error('pathdoesnotstartslash');
}

// extract relative path components
$args = explode('/', ltrim($relativepath, '/'));

if (count($args) < 3) { // always at least context, component and filearea
    print_error('invalidarguments');
}

$contextid = (int)array_shift($args);
$component = clean_param(array_shift($args), PARAM_SAFEDIR);
$filearea  = clean_param(array_shift($args), PARAM_SAFEDIR);

list($context, $course, $cm) = get_context_info_array($contextid);

$fs = get_file_storage();

// If the file is a Flash file and that the user flash player is outdated return a flash upgrader MDL-20841
$mimetype = mimeinfo('type', $args[count($args)-1]);
if (!empty($CFG->excludeoldflashclients) && $mimetype == 'application/x-shockwave-flash'&& !empty($SESSION->flashversion)) {
    $userplayerversion = explode('.', $SESSION->flashversion);
    $requiredplayerversion = explode('.', $CFG->excludeoldflashclients);
    if (($userplayerversion[0] <  $requiredplayerversion[0]) ||
        ($userplayerversion[0] == $requiredplayerversion[0] && $userplayerversion[1] < $requiredplayerversion[1]) ||
        ($userplayerversion[0] == $requiredplayerversion[0] && $userplayerversion[1] == $requiredplayerversion[1]
         && $userplayerversion[2] < $requiredplayerversion[2])) {
        $path = $CFG->dirroot."/lib/flashdetect/flashupgrade.swf";  // Alternate content asking user to upgrade Flash
        $filename = "flashupgrade.swf";
        send_file($path, $filename, 0, 0, false, false, 'application/x-shockwave-flash'); // Do not cache
    }
}

// ========================================================================================================================
if ($component === 'blog') {
    // Blog file serving
    if ($context->contextlevel != CONTEXT_SYSTEM) {
        send_file_not_found();
    }
    if ($filearea !== 'attachment' and $filearea !== 'post') {
        send_file_not_found();
    }

    if (empty($CFG->bloglevel)) {
        print_error('siteblogdisable', 'blog');
    }

    $entryid = (int)array_shift($args);
    if (!$entry = $DB->get_record('post', array('module'=>'blog', 'id'=>$entryid))) {
        send_file_not_found();
    }
    if ($CFG->bloglevel < BLOG_GLOBAL_LEVEL) {
        require_login();
        if (isguestuser()) {
            print_error('noguest');
        }
        if ($CFG->bloglevel == BLOG_USER_LEVEL) {
            if ($USER->id != $entry->userid) {
                send_file_not_found();
            }
        }
    }

    if ('publishstate' === 'public') {
        if ($CFG->forcelogin) {
            require_login();
        }

    } else if ('publishstate' === 'site') {
        require_login();
        //ok
    } else if ('publishstate' === 'draft') {
        require_login();
        if ($USER->id != $entry->userid) {
            send_file_not_found();
        }
    }

    $filename = array_pop($args);
    $filepath = $args ? '/'.implode('/', $args).'/' : '/';

    if (!$file = $fs->get_file($context->id, $component, $filearea, $entryid, $filepath, $filename) or $file->is_directory()) {
        send_file_not_found();
    }

    send_stored_file($file, 10*60, 0, true); // download MUST be forced - security!

// ========================================================================================================================
} else if ($component === 'grade') {
    if (($filearea === 'outcome' or $filearea === 'scale') and $context->contextlevel == CONTEXT_SYSTEM) {
        // Global gradebook files
        if ($CFG->forcelogin) {
            require_login();
        }

        $fullpath = "/$context->id/$component/$filearea/".implode('/', $args);

        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60, 0, $forcedownload);

    } else if ($filearea === 'feedback' and $context->contextlevel == CONTEXT_COURSE) {
        //TODO: nobody implemented this yet in grade edit form!!
        send_file_not_found();

        if ($CFG->forcelogin || $course->id != SITEID) {
            require_login($course);
        }

        $fullpath = "/$context->id/$component/$filearea/".implode('/', $args);

        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60, 0, $forcedownload);
    } else {
        send_file_not_found();
    }

// ========================================================================================================================
} else if ($component === 'tag') {
    if ($filearea === 'description' and $context->contextlevel == CONTEXT_SYSTEM) {

        // All tag descriptions are going to be public but we still need to respect forcelogin
        if ($CFG->forcelogin) {
            require_login();
        }

        $fullpath = "/$context->id/tag/description/".implode('/', $args);

        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60, 0, true);

    } else {
        send_file_not_found();
    }

// ========================================================================================================================
} else if ($component === 'calendar') {
    if ($filearea === 'event_description'  and $context->contextlevel == CONTEXT_SYSTEM) {

        // All events here are public the one requirement is that we respect forcelogin
        if ($CFG->forcelogin) {
            require_login();
        }

        // Get the event if from the args array
        $eventid = array_shift($args);

        // Load the event from the database
        if (!$event = $DB->get_record('event', array('id'=>(int)$eventid, 'eventtype'=>'site'))) {
            send_file_not_found();
        }
        // Check that we got an event and that it's userid is that of the user

        // Get the file and serve if successful
        $filename = array_pop($args);
        $filepath = $args ? '/'.implode('/', $args).'/' : '/';
        if (!$file = $fs->get_file($context->id, $component, $filearea, $eventid, $filepath, $filename) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60, 0, $forcedownload);

    } else if ($filearea === 'event_description' and $context->contextlevel == CONTEXT_USER) {

        // Must be logged in, if they are not then they obviously can't be this user
        require_login();

        // Don't want guests here, potentially saves a DB call
        if (isguestuser()) {
            send_file_not_found();
        }

        // Get the event if from the args array
        $eventid = array_shift($args);

        // Load the event from the database - user id must match
        if (!$event = $DB->get_record('event', array('id'=>(int)$eventid, 'userid'=>$USER->id, 'eventtype'=>'user'))) {
            send_file_not_found();
        }

        // Get the file and serve if successful
        $filename = array_pop($args);
        $filepath = $args ? '/'.implode('/', $args).'/' : '/';
        if (!$file = $fs->get_file($context->id, $component, $filearea, $eventid, $filepath, $filename) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60, 0, $forcedownload);

    } else if ($filearea === 'event_description' and $context->contextlevel == CONTEXT_COURSE) {

        // Respect forcelogin and require login unless this is the site.... it probably
        // should NEVER be the site
        if ($CFG->forcelogin || $course->id != SITEID) {
            require_login($course);
        }

        // Must be able to at least view the course
        if (!is_enrolled($context) and !is_viewing($context)) {
            //TODO: hmm, do we really want to block guests here?
            send_file_not_found();
        }

        // Get the event id
        $eventid = array_shift($args);

        // Load the event from the database we need to check whether it is
        // a) valid course event
        // b) a group event
        // Group events use the course context (there is no group context)
        if (!$event = $DB->get_record('event', array('id'=>(int)$eventid, 'courseid'=>$course->id))) {
            send_file_not_found();
        }

        // If its a group event require either membership of view all groups capability
        if ($event->eventtype === 'group') {
            if (!has_capability('moodle/site:accessallgroups', $context) && !groups_is_member($event->groupid, $USER->id)) {
                send_file_not_found();
            }
        } else if ($event->eventtype === 'course') {
            //ok
        } else {
            // some other type
            send_file_not_found();
        }

        // If we get this far we can serve the file
        $filename = array_pop($args);
        $filepath = $args ? '/'.implode('/', $args).'/' : '/';
        if (!$file = $fs->get_file($context->id, $component, $filearea, $eventid, $filepath, $filename) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60, 0, $forcedownload);

    } else {
        send_file_not_found();
    }

// ========================================================================================================================
} else if ($component === 'user') {
    if ($filearea === 'icon' and $context->contextlevel == CONTEXT_USER) {
        $redirect = false;
        if (count($args) == 1) {
            $themename = theme_config::DEFAULT_THEME;
            $filename = array_shift($args);
        } else {
            $themename = array_shift($args);
            $filename = array_shift($args);
        }
        if ((!empty($CFG->forcelogin) and !isloggedin()) ||
                (!empty($CFG->forceloginforprofileimage) && (!isloggedin() || isguestuser()))) {
            // protect images if login required and not logged in;
            // also if login is required for profile images and is not logged in or guest
            // do not use require_login() because it is expensive and not suitable here anyway
            $redirect = true;
        }
        if (!$redirect and ($filename !== 'f1' and $filename !== 'f2')) {
            $filename = 'f1';
            $redirect = true;
        }
        if (!$redirect && !$file = $fs->get_file($context->id, 'user', 'icon', 0, '/', $filename.'/.png')) {
            if (!$file = $fs->get_file($context->id, 'user', 'icon', 0, '/', $filename.'/.jpg')) {
                $redirect = true;
            }
        }
        if ($redirect) {
            $theme = theme_config::load($themename);
            redirect($theme->pix_url('u/'.$filename, 'moodle'));
        }
        send_stored_file($file, 60*60*24); // enable long caching, there are many images on each page

    } else if ($filearea === 'private' and $context->contextlevel == CONTEXT_USER) {
        require_login();

        if (isguestuser()) {
            send_file_not_found();
        }

        if ($USER->id !== $context->instanceid) {
            send_file_not_found();
        }

        $filename = array_pop($args);
        $filepath = $args ? '/'.implode('/', $args).'/' : '/';
        if (!$file = $fs->get_file($context->id, $component, $filearea, 0, $filepath, $filename) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 0, 0, true); // must force download - security!

    } else if ($filearea === 'profile' and $context->contextlevel == CONTEXT_USER) {

        if ($CFG->forcelogin) {
            require_login();
        }

        $userid = $context->instanceid;

        if ($USER->id == $userid) {
            // always can access own

        } else if (!empty($CFG->forceloginforprofiles)) {
            require_login();

            if (isguestuser()) {
                send_file_not_found();
            }

            // we allow access to site profile of all course contacts (usually teachers)
            if (!has_coursecontact_role($userid) && !has_capability('moodle/user:viewdetails', $context)) {
                send_file_not_found();
            }

            $canview = false;
            if (has_capability('moodle/user:viewdetails', $context)) {
                $canview = true;
            } else {
                $courses = enrol_get_my_courses();
            }

            while (!$canview && count($courses) > 0) {
                $course = array_shift($courses);
                if (has_capability('moodle/user:viewdetails', get_context_instance(CONTEXT_COURSE, $course->id))) {
                    $canview = true;
                }
            }
        }

        $filename = array_pop($args);
        $filepath = $args ? '/'.implode('/', $args).'/' : '/';
        if (!$file = $fs->get_file($context->id, $component, $filearea, 0, $filepath, $filename) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 0, 0, true); // must force download - security!

    } else if ($filearea === 'profile' and $context->contextlevel == CONTEXT_COURSE) {
        $userid = (int)array_shift($args);
        $usercontext = get_context_instance(CONTEXT_USER, $userid);

        if ($CFG->forcelogin) {
            require_login();
        }

        if (!empty($CFG->forceloginforprofiles)) {
            require_login();
            if (isguestuser()) {
                print_error('noguest');
            }

            //TODO: review this logic of user profile access prevention
            if (!has_coursecontact_role($userid) and !has_capability('moodle/user:viewdetails', $usercontext)) {
                print_error('usernotavailable');
            }
            if (!has_capability('moodle/user:viewdetails', $context) && !has_capability('moodle/user:viewdetails', $usercontext)) {
                print_error('cannotviewprofile');
            }
            if (!is_enrolled($context, $userid)) {
                print_error('notenrolledprofile');
            }
            if (groups_get_course_groupmode($course) == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $context)) {
                print_error('groupnotamember');
            }
        }

        $filename = array_pop($args);
        $filepath = $args ? '/'.implode('/', $args).'/' : '/';
        if (!$file = $fs->get_file($usercontext->id, 'user', 'profile', 0, $filepath, $filename) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 0, 0, true); // must force download - security!

    } else if ($filearea === 'backup' and $context->contextlevel == CONTEXT_USER) {
        require_login();

        if (isguestuser()) {
            send_file_not_found();
        }
        $userid = $context->instanceid;

        if ($USER->id != $userid) {
            send_file_not_found();
        }

        $filename = array_pop($args);
        $filepath = $args ? '/'.implode('/', $args).'/' : '/';
        if (!$file = $fs->get_file($context->id, 'user', 'backup', 0, $filepath, $filename) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 0, 0, true); // must force download - security!

    } else {
        send_file_not_found();
    }

// ========================================================================================================================
} else if ($component === 'coursecat') {
    if ($context->contextlevel != CONTEXT_COURSECAT) {
        send_file_not_found();
    }

    if ($filearea === 'description') {
        if ($CFG->forcelogin) {
            // no login necessary - unless login forced everywhere
            require_login();
        }

        $filename = array_pop($args);
        $filepath = $args ? '/'.implode('/', $args).'/' : '/';
        if (!$file = $fs->get_file($context->id, 'coursecat', 'description', 0, $filepath, $filename) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60, 0, $forcedownload);
    } else {
        send_file_not_found();
    }

// ========================================================================================================================
} else if ($component === 'course') {
    if ($context->contextlevel != CONTEXT_COURSE) {
        send_file_not_found();
    }

    if ($filearea === 'summary') {
        if ($CFG->forcelogin) {
            require_login();
        }

        $filename = array_pop($args);
        $filepath = $args ? '/'.implode('/', $args).'/' : '/';
        if (!$file = $fs->get_file($context->id, 'course', 'summary', 0, $filepath, $filename) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60, 0, $forcedownload);

    } else if ($filearea === 'section') {
        if ($CFG->forcelogin) {
            require_login($course);
        } else if ($course->id != SITEID) {
            require_login($course);
        }

        $sectionid = (int)array_shift($args);

        if (!$section = $DB->get_record('course_sections', array('id'=>$sectionid, 'course'=>$course->id))) {
            send_file_not_found();
        }

        if ($course->numsections < $section->section) {
            if (!has_capability('moodle/course:update', $context)) {
                // block access to unavailable sections if can not edit course
                send_file_not_found();
            }
        }

        $filename = array_pop($args);
        $filepath = $args ? '/'.implode('/', $args).'/' : '/';
        if (!$file = $fs->get_file($context->id, 'course', 'section', $sectionid, $filepath, $filename) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60, 0, $forcedownload);

    } else {
        send_file_not_found();
    }

} else if ($component === 'group') {
    if ($context->contextlevel != CONTEXT_COURSE) {
        send_file_not_found();
    }

    require_course_login($course, true, null, false);

    $groupid = (int)array_shift($args);

    $group = $DB->get_record('groups', array('id'=>$groupid, 'courseid'=>$course->id), '*', MUST_EXIST);
    if (($course->groupmodeforce and $course->groupmode == SEPARATEGROUPS) and !has_capability('moodle/site:accessallgroups', $context) and !groups_is_member($group->id, $USER->id)) {
        // do not allow access to separate group info if not member or teacher
        send_file_not_found();
    }

    if ($filearea === 'description') {

        require_login($course);

        $filename = array_pop($args);
        $filepath = $args ? '/'.implode('/', $args).'/' : '/';
        if (!$file = $fs->get_file($context->id, 'group', 'description', $group->id, $filepath, $filename) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60, 0, $forcedownload);

    } else if ($filearea === 'icon') {
        $filename = array_pop($args);

        if ($filename !== 'f1' and $filename !== 'f2') {
            send_file_not_found();
        }
        if (!$file = $fs->get_file($context->id, 'group', 'icon', $group->id, '/', $filename.'.png')) {
            if (!$file = $fs->get_file($context->id, 'group', 'icon', $group->id, '/', $filename.'.jpg')) {
                send_file_not_found();
            }
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60);

    } else {
        send_file_not_found();
    }

} else if ($component === 'grouping') {
    if ($context->contextlevel != CONTEXT_COURSE) {
        send_file_not_found();
    }

    require_login($course);

    $groupingid = (int)array_shift($args);

    // note: everybody has access to grouping desc images for now
    if ($filearea === 'description') {

        $filename = array_pop($args);
        $filepath = $args ? '/'.implode('/', $args).'/' : '/';
        if (!$file = $fs->get_file($context->id, 'grouping', 'description', $groupingid, $filepath, $filename) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60, 0, $forcedownload);

    } else {
        send_file_not_found();
    }

// ========================================================================================================================
} else if ($component === 'backup') {
    if ($filearea === 'course' and $context->contextlevel == CONTEXT_COURSE) {
        require_login($course);
        require_capability('moodle/backup:downloadfile', $context);

        $filename = array_pop($args);
        $filepath = $args ? '/'.implode('/', $args).'/' : '/';
        if (!$file = $fs->get_file($context->id, 'backup', 'course', 0, $filepath, $filename) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 0, 0, $forcedownload);

    } else if ($filearea === 'section' and $context->contextlevel == CONTEXT_COURSE) {
        require_login($course);
        require_capability('moodle/backup:downloadfile', $context);

        $sectionid = (int)array_shift($args);

        $filename = array_pop($args);
        $filepath = $args ? '/'.implode('/', $args).'/' : '/';
        if (!$file = $fs->get_file($context->id, 'backup', 'section', $sectionid, $filepath, $filename) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close();
        send_stored_file($file, 60*60, 0, $forcedownload);

    } else if ($filearea === 'activity' and $context->contextlevel == CONTEXT_MODULE) {
        require_login($course, false, $cm);
        require_capability('moodle/backup:downloadfile', $context);

        $filename = array_pop($args);
        $filepath = $args ? '/'.implode('/', $args).'/' : '/';
        if (!$file = $fs->get_file($context->id, 'backup', 'activity', 0, $filepath, $filename) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close();
        send_stored_file($file, 60*60, 0, $forcedownload);

    } else if ($filearea === 'automated' and $context->contextlevel == CONTEXT_COURSE) {
        // Backup files that were generated by the automated backup systems.

        require_login($course);
        require_capability('moodle/site:config', $context);

        $filename = array_pop($args);
        $filepath = $args ? '/'.implode('/', $args).'/' : '/';
        if (!$file = $fs->get_file($context->id, 'backup', 'automated', 0, $filepath, $filename) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 0, 0, $forcedownload);

    } else {
        send_file_not_found();
    }

// ========================================================================================================================
} else if ($component === 'question') {
    require_once($CFG->libdir . '/questionlib.php');
    question_pluginfile($course, $context, 'question', $filearea, $args, $forcedownload);
    send_file_not_found();

// ========================================================================================================================
} else if (strpos($component, 'mod_') === 0) {
    $modname = substr($component, 4);
    if (!file_exists("$CFG->dirroot/mod/$modname/lib.php")) {
        send_file_not_found();
    }
    require_once("$CFG->dirroot/mod/$modname/lib.php");

    if ($context->contextlevel == CONTEXT_MODULE) {
        if ($cm->modname !== $modname) {
            // somebody tries to gain illegal access, cm type must match the component!
            send_file_not_found();
        }
    }

    if ($filearea === 'intro') {
        if (!plugin_supports('mod', $modname, FEATURE_MOD_INTRO, true)) {
            send_file_not_found();
        }
        require_course_login($course, true, $cm);

        // all users may access it
        $filename = array_pop($args);
        $filepath = $args ? '/'.implode('/', $args).'/' : '/';
        if (!$file = $fs->get_file($context->id, 'mod_'.$modname, 'intro', 0, $filepath, $filename) or $file->is_directory()) {
            send_file_not_found();
        }

        $lifetime = isset($CFG->filelifetime) ? $CFG->filelifetime : 86400;

        // finally send the file
        send_stored_file($file, $lifetime, 0);
    }

    $filefunction = $component.'_pluginfile';
    $filefunctionold = $modname.'_pluginfile';
    if (function_exists($filefunction)) {
        // if the function exists, it must send the file and terminate. Whatever it returns leads to "not found"
        $filefunction($course, $cm, $context, $filearea, $args, $forcedownload);
    } else if (function_exists($filefunctionold)) {
        // if the function exists, it must send the file and terminate. Whatever it returns leads to "not found"
        $filefunctionold($course, $cm, $context, $filearea, $args, $forcedownload);
    }

    send_file_not_found();

// ========================================================================================================================
} else if (strpos($component, 'block_') === 0) {
    $blockname = substr($component, 6);
    // note: no more class methods in blocks please, that is ....
    if (!file_exists("$CFG->dirroot/blocks/$blockname/lib.php")) {
        send_file_not_found();
    }
    require_once("$CFG->dirroot/blocks/$blockname/lib.php");

    if ($context->contextlevel == CONTEXT_BLOCK) {
        $birecord = $DB->get_record('block_instances', array('id'=>$context->instanceid), '*',MUST_EXIST);
        if ($birecord->blockname !== $blockname) {
            // somebody tries to gain illegal access, cm type must match the component!
            send_file_not_found();
        }
    } else {
        $birecord = null;
    }

    $filefunction = $component.'_pluginfile';
    if (function_exists($filefunction)) {
        // if the function exists, it must send the file and terminate. Whatever it returns leads to "not found"
        $filefunction($course, $birecord, $context, $filearea, $args, $forcedownload);
    }

    send_file_not_found();

} else if (strpos($component, '_') === false) {
    // all core subsystems have to be specified above, no more guessing here!
    send_file_not_found();

} else {
    // try to serve general plugin file in arbitrary context
    $dir = get_component_directory($component);
    if (!file_exists("$dir/lib.php")) {
        send_file_not_found();
    }
    include_once("$dir/lib.php");

    $filefunction = $component.'_pluginfile';
    if (function_exists($filefunction)) {
        // if the function exists, it must send the file and terminate. Whatever it returns leads to "not found"
        $filefunction($course, $cm, $context, $filearea, $args, $forcedownload);
    }

    send_file_not_found();
}
