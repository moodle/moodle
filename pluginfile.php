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
 * @package    moodlecore
 * @subpackage file
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// disable moodle specific debug messages and any errors in output
define('NO_DEBUG_DISPLAY', true);

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

if (count($args) == 0) { // always at least user id
    print_error('invalidarguments');
}

$contextid = (int)array_shift($args);
$filearea = array_shift($args);

if (!$context = get_context_instance_by_id($contextid)) {
    send_file_not_found();
}
$fs = get_file_storage();

// If the file is a Flash file and that the user flash player is outdated return a flash upgrader MDL-20841
$mimetype = mimeinfo('type', $args[count($args)-1]);
if (!empty($CFG->excludeoldflashclients) && $mimetype == 'application/x-shockwave-flash'&& !empty($SESSION->flashversion)) {
    $userplayerversion = explode('.', $SESSION->flashversion);
    $requiredplayerversion = explode('.', $CFG->excludeoldflashclients);
    $sendflashupgrader = true;
}
if (!empty($sendflashupgrader) && (($userplayerversion[0] <  $requiredplayerversion[0]) ||
        ($userplayerversion[0] == $requiredplayerversion[0] && $userplayerversion[1] < $requiredplayerversion[1]) ||
        ($userplayerversion[0] == $requiredplayerversion[0] && $userplayerversion[1] == $requiredplayerversion[1]
         && $userplayerversion[2] < $requiredplayerversion[2]))) {
        $path = $CFG->dirroot."/lib/flashdetect/flashupgrade.swf";  // Alternate content asking user to upgrade Flash
        $filename = "flashupgrade.swf";
        $lifetime = 0;  // Do not cache
        send_file($path, $filename, $lifetime, 0, false, false, $mimetype);

} else if ($context->contextlevel == CONTEXT_SYSTEM) {
    if ($filearea === 'blog_attachment' || $filearea === 'blog_post') {

        if (empty($CFG->bloglevel)) {
            print_error('siteblogdisable', 'blog');
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
        $entryid = (int)array_shift($args);
        if (!$entry = $DB->get_record('post', array('module'=>'blog', 'id'=>$entryid))) {
            send_file_not_found();
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

        //TODO: implement shared course and shared group access

        $relativepath = '/'.implode('/', $args);
        $fullpath = $context->id.$filearea.$entryid.$relativepath;

        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            send_file_not_found();
        }

        send_stored_file($file, 10*60, 0, true); // download MUST be forced - security!
    } else if ($filearea === 'grade_outcome' || $filearea === 'grade_scale') { // CONTEXT_SYSTEM
        if ($CFG->forcelogin) {
            require_login();
        }

        $fullpath = $context->id.$filearea.implode('/', $args);

        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60, 0, $forcedownload); // TODO: change timeout?

    } else if ($filearea === 'tag_description') { // CONTEXT_SYSTEM

        // All tag descriptions are going to be public but we still need to respect forcelogin
        if ($CFG->forcelogin) {
            require_login();
        }

        $fullpath = $context->id.$filearea.implode('/', $args);

        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60, 0, true); // TODO: change timeout?

    } else if ($filearea === 'calendar_event_description') { // CONTEXT_SYSTEM

        // All events here are public the one requirement is that we respect forcelogin
        if ($CFG->forcelogin) {
            require_login();
        }

        $fullpath = $context->id.$filearea.implode('/', $args);

        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60, 0, $forcedownload); // TODO: change timeout?

    } else {
        send_file_not_found();
    }

} else if ($context->contextlevel == CONTEXT_USER) {

    if ($filearea === 'calendar_event_description') { // CONTEXT_USER

        // Must be logged in, if they are not then they obviously can't be this user
        require_login();

        // Don't want guests here, potentially saves a DB call
        if (isguestuser()) {
            send_file_not_found();
        }

        // Get the event if from the args array
        $eventid = array_shift($args);
        if ((int)$eventid <= 0) {
            send_file_not_found();
        }

        // Load the event from the database
        $event = $DB->get_record('event', array('id'=>(int)$eventid));
        // Check that we got an event and that it's userid is that of the user
        if (!$event || $event->userid !== $USER->id) {
            send_file_not_found();
        }

        // Get the file and serve if succesfull
        $fullpath = $context->id.$filearea.$eventid.'/'.implode('/', $args);
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60, 0, $forcedownload); // TODO: change timeout?
    } else if ($filearea === 'user_profile') { // CONTEXT_USER

        if ($CFG->forcelogin) {
            require_login();
        }

        $userid = array_shift($args);
        if ((int)$userid <= 0) {
            send_file_not_found();
        }

        if (!empty($CFG->forceloginforprofiles)) {
            require_login();
            if (isguestuser()) {
                send_file_not_found();
            }

            if ($USER->id !== $userid) {
                $usercontext = get_context_instance(CONTEXT_USER, $userid);
                // The browsing user is not the current user
                if (!has_coursecontact_role($userid) && !has_capability('moodle/user:viewdetails', $usercontext)) {
                    send_file_not_found();
                }

                $canview = false;
                if (has_capability('moodle/user:viewdetails', $usercontext)) {
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
        }

        $fullpath = $context->id.$filearea.$userid.'/'.implode('/', $args);

        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60, 0, true);
    }

    send_file_not_found();


} else if ($context->contextlevel == CONTEXT_COURSECAT) {
    if ($filearea == 'coursecat_intro') {
        if ($CFG->forcelogin) {
            // no login necessary - unless login forced everywhere
            require_login();
        }

        $relativepath = '/'.implode('/', $args);
        $fullpath = $context->id.'coursecat_intro0'.$relativepath;

        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->get_filename() == '.') {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60, 0, $forcedownload);
    } else if ($filearea == 'category_description') {
        if ($CFG->forcelogin) {
            // no login necessary - unless login forced everywhere
            require_login();
        }
        $itemid = (int)array_shift($args);

        $relativepath = '/'.implode('/', $args);
        $fullpath = $context->id.'category_description'.$itemid.$relativepath;

        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->get_filename() == '.') {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60, 0, $forcedownload);
    } else {
        send_file_not_found();
    }


} else if ($context->contextlevel == CONTEXT_COURSE) {
    if (!$course = $DB->get_record('course', array('id'=>$context->instanceid))) {
        print_error('invalidcourseid');
    }

    if ($filearea === 'course_backup') {
        require_login($course);
        require_capability('moodle/backup:downloadfile', $context);

        $relativepath = '/'.implode('/', $args);
        $fullpath = $context->id.'course_backup0'.$relativepath;

        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 0, 0, true);

    } else if ($filearea === 'course_summary') {
        if ($CFG->forcelogin) {
            require_login();
        }

        $relativepath = '/'.implode('/', $args);
        $fullpath = $context->id.'course_summary0'.$relativepath;

        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60, 0, $forcedownload); // TODO: change timeout?

    } else if ($filearea === 'course_grade_tree_feedback') {

        if ($CFG->forcelogin || $course->id !== SITEID) {
            require_login($course);
        }

        $fullpath = $context->id.$filearea.implode('/', $args);

        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60, 0, $forcedownload); // TODO: change timeout?

    } else if ($filearea === 'calendar_event_description') { // CONTEXT_COURSE

        // This is for content used in course and group events

        // Respect forcelogin and require login unless this is the site.... it probably
        // should NEVER be the site
        if ($CFG->forcelogin || $course->id !== SITEID) {
            require_login($course);
        }

        // Must be able to at least view the course
        if (!is_enrolled($context) and !is_viewing($context)) {
            send_file_not_found();
        }

        // Get the event id
        $eventid = array_shift($args);
        if ((int)$eventid <= 0) {
            send_file_not_found();
        }

        // Load the event from the database we need to check whether it is
        // a) valid
        // b) a group event
        // Group events use the course context (there is no group context)
        $event = $DB->get_record('event', array('id'=>(int)$eventid));
        if (!$event || $event->userid !== $USER->id) {
            send_file_not_found();
        }

        // If its a group event require either membership of manage groups capability
        if ($event->eventtype === 'group' && !has_capability('moodle/course:managegroups', $context) && !groups_is_member($event->groupid, $USER->id)) {
            send_file_not_found();
        }

        // If we get this far we can serve the file
        $fullpath = $context->id.$filearea.$eventid.'/'.implode('/', $args);
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60, 0, $forcedownload); // TODO: change timeout?

    } else if ($filearea === 'course_section') {
        if ($CFG->forcelogin) {
            require_login($course);
        } else if ($course->id !== SITEID) {
            require_login($course);
        }

        $sectionid = (int)array_shift($args);

        if ($course->numsections < $sectionid) {
            if (!has_capability('moodle/course:update', $context)) {
                // disable access to invisible sections if can not edit course
                // this is going to break some ugly hacks, but is necessary
                send_file_not_found();
            }
        }

        $relativepath = '/'.implode('/', $args);
        $fullpath = $context->id.'course_section'.$sectionid.$relativepath;

        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 60*60, 0, false); // TODO: change timeout?

    } else if ($filearea === 'section_backup') {
        require_login($course);
        require_capability('moodle/backup:downloadfile', $context);

        $sectionid = (int)array_shift($args);

        $relativepath = '/'.implode('/', $args);
        $fullpath = $context->id.'section_backup'.$sectionid.$relativepath;

        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close();
        send_stored_file($file, 60*60, 0, false);

    } else if ($filearea === 'user_profile') {
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

            if (!has_coursecontact_role($userid) and !has_capability('moodle/user:viewdetails', $usercontext)) {
                print_error('usernotavailable');
            }
            if (!has_capability('moodle/user:viewdetails', $context) &&
                !has_capability('moodle/user:viewdetails', $usercontext)) {
                print_error('cannotviewprofile');
            }
            if (!is_enrolled($context, $userid)) {
                print_error('notenrolledprofile');
            }
            if (groups_get_course_groupmode($course) == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $context)) {
                print_error('groupnotamember');
            }
        }

        $relativepath = '/'.implode('/', $args);
        $fullpath = $usercontext->id.'user_profile0'.$relativepath;

        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close(); // unlock session during fileserving
        send_stored_file($file, 0, 0, true); // must force download - security!

    } else {
        send_file_not_found();
    }

} else if ($context->contextlevel == CONTEXT_MODULE) {

    if (!$coursecontext = get_context_instance_by_id(get_parent_contextid($context))) {
        send_file_not_found();
    }

    if (!$course = $DB->get_record('course', array('id'=>$coursecontext->instanceid))) {
        send_file_not_found();
    }
    $modinfo = get_fast_modinfo($course);
    if (empty($modinfo->cms[$context->instanceid])) {
        send_file_not_found();
    }

    $cminfo = $modinfo->cms[$context->instanceid];
    $modname = $cminfo->modname;
    $libfile = "$CFG->dirroot/mod/$modname/lib.php";
    if (!file_exists($libfile)) {
        send_file_not_found();
    }

    require_once($libfile);
    if ($filearea === $modname.'_intro') {
        if (!plugin_supports('mod', $modname, FEATURE_MOD_INTRO, true)) {
            send_file_not_found();
        }
        if (!$cm = get_coursemodule_from_instance($modname, $cminfo->instance, $course->id)) {
            send_file_not_found();
        }
        require_course_login($course, true, $cm);

        if (!$cminfo->uservisible) {
            send_file_not_found();
        }
        // all users may access it
        $relativepath = '/'.implode('/', $args);
        $fullpath = $context->id.$filearea.'0'.$relativepath;

        $fs = get_file_storage();
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            send_file_not_found();
        }

        $lifetime = isset($CFG->filelifetime) ? $CFG->filelifetime : 86400;

        // finally send the file
        send_stored_file($file, $lifetime, 0);
    } else if ($filearea === 'activity_backup') {
        require_login($course);
        require_capability('moodle/backup:downloadfile', $context);

        $relativepath = '/'.implode('/', $args);
        $fullpath = $context->id.'activity_backup0'.$relativepath;

        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            send_file_not_found();
        }

        session_get_instance()->write_close();
        send_stored_file($file, 60*60, 0, false);
    }

    $filefunction = $modname.'_pluginfile';
    if (function_exists($filefunction)) {
        // if the function exists, it must send the file and terminate. Whatever it returns leads to "not found"
        $filefunction($course, $cminfo, $context, $filearea, $args, $forcedownload);
    }

    send_file_not_found();

} else if ($context->contextlevel == CONTEXT_BLOCK) {

    if (!$context = get_context_instance_by_id($contextid)) {
        send_file_not_found();
    }
    $birecord = $DB->get_record('block_instances', array('id'=>$context->instanceid), '*',MUST_EXIST);
    $blockinstance = block_instance($birecord->blockname, $birecord);

    if (strpos(get_class($blockinstance), $filearea) !== 0) {
        send_file_not_found();
    }

    $itemid = array_shift($args);
    $filename = array_pop($args);
    $filepath = '/'.join('/', $args);

    if (method_exists($blockinstance, 'send_file')) {
        $blockinstance->send_file($context, $filearea, $itemid, $filepath, $filename);
    }

    send_file_not_found();

} else {
    send_file_not_found();
}
