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
 * Subscribe to or unsubscribe from a forum or manage forum subscription mode
 *
 * This script can be used by either individual users to subscribe to or
 * unsubscribe from a forum (no 'mode' param provided), or by forum managers
 * to control the subscription mode (by 'mode' param).
 * This script can be called from a link in email so the sesskey is not
 * required parameter. However, if sesskey is missing, the user has to go
 * through a confirmation page that redirects the user back with the
 * sesskey.
 *
 * @package    mod
 * @subpackage forum
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->dirroot.'/mod/forum/lib.php');

$id      = required_param('id', PARAM_INT);             // the forum to subscribe or unsubscribe to
$mode    = optional_param('mode', null, PARAM_INT);     // the forum's subscription mode
$user    = optional_param('user', 0, PARAM_INT);        // userid of the user to subscribe, defaults to $USER
$sesskey = optional_param('sesskey', null, PARAM_RAW);  // sesskey

$url = new moodle_url('/mod/forum/subscribe.php', array('id'=>$id));
if (!is_null($mode)) {
    $url->param('mode', $mode);
}
if ($user !== 0) {
    $url->param('user', $user);
}
if (!is_null($sesskey)) {
    $url->param('sesskey', $sesskey);
}
$PAGE->set_url($url);

$forum   = $DB->get_record('forum', array('id' => $id), '*', MUST_EXIST);
$course  = $DB->get_record('course', array('id' => $forum->course), '*', MUST_EXIST);
$cm      = get_coursemodule_from_instance('forum', $forum->id, $course->id, false, MUST_EXIST);
$context = context_module::instance($cm->id);

if ($user) {
    require_sesskey();
    if (!has_capability('mod/forum:managesubscriptions', $context)) {
        print_error('nopermissiontosubscribe', 'forum');
    }
    $user = $DB->get_record('user', array('id' => $user), '*', MUST_EXIST);
} else {
    $user = $USER;
}

if (isset($cm->groupmode) && empty($course->groupmodeforce)) {
    $groupmode = $cm->groupmode;
} else {
    $groupmode = $course->groupmode;
}
if ($groupmode && !forum_is_subscribed($user->id, $forum) && !has_capability('moodle/site:accessallgroups', $context)) {
    if (!groups_get_all_groups($course->id, $USER->id)) {
        print_error('cannotsubscribe', 'forum');
    }
}

require_login($course, false, $cm);

if (is_null($mode) and !is_enrolled($context, $USER, '', true)) {   // Guests and visitors can't subscribe - only enrolled
    $PAGE->set_title($course->shortname);
    $PAGE->set_heading($course->fullname);
    if (isguestuser()) {
        echo $OUTPUT->header();
        echo $OUTPUT->confirm(get_string('subscribeenrolledonly', 'forum').'<br /><br />'.get_string('liketologin'),
                     get_login_url(), new moodle_url('/mod/forum/view.php', array('f'=>$id)));
        echo $OUTPUT->footer();
        exit;
    } else {
        // there should not be any links leading to this place, just redirect
        redirect(new moodle_url('/mod/forum/view.php', array('f'=>$id)), get_string('subscribeenrolledonly', 'forum'));
    }
}

$returnto = optional_param('backtoindex',0,PARAM_INT)
    ? "index.php?id=".$course->id
    : "view.php?f=$id";

if (!is_null($mode) and has_capability('mod/forum:managesubscriptions', $context)) {
    require_sesskey();
    switch ($mode) {
        case FORUM_CHOOSESUBSCRIBE : // 0
            forum_forcesubscribe($forum->id, FORUM_CHOOSESUBSCRIBE);
            redirect($returnto, get_string("everyonecannowchoose", "forum"), 1);
            break;
        case FORUM_FORCESUBSCRIBE : // 1
            forum_forcesubscribe($forum->id, FORUM_FORCESUBSCRIBE);
            redirect($returnto, get_string("everyoneisnowsubscribed", "forum"), 1);
            break;
        case FORUM_INITIALSUBSCRIBE : // 2
            forum_forcesubscribe($forum->id, FORUM_INITIALSUBSCRIBE);
            redirect($returnto, get_string("everyoneisnowsubscribed", "forum"), 1);
            break;
        case FORUM_DISALLOWSUBSCRIBE : // 3
            forum_forcesubscribe($forum->id, FORUM_DISALLOWSUBSCRIBE);
            redirect($returnto, get_string("noonecansubscribenow", "forum"), 1);
            break;
        default:
            print_error(get_string('invalidforcesubscribe', 'forum'));
    }
}

if (forum_is_forcesubscribed($forum)) {
    redirect($returnto, get_string("everyoneisnowsubscribed", "forum"), 1);
}

$info = new stdClass();
$info->name  = fullname($user);
$info->forum = format_string($forum->name);

if (forum_is_subscribed($user->id, $forum->id)) {
    if (is_null($sesskey)) {    // we came here via link in email
        $PAGE->set_title($course->shortname);
        $PAGE->set_heading($course->fullname);
        echo $OUTPUT->header();
        echo $OUTPUT->confirm(get_string('confirmunsubscribe', 'forum', format_string($forum->name)),
                new moodle_url($PAGE->url, array('sesskey' => sesskey())), new moodle_url('/mod/forum/view.php', array('f' => $id)));
        echo $OUTPUT->footer();
        exit;
    }
    require_sesskey();
    if (forum_unsubscribe($user->id, $forum->id)) {
        add_to_log($course->id, "forum", "unsubscribe", "view.php?f=$forum->id", $forum->id, $cm->id);
        redirect($returnto, get_string("nownotsubscribed", "forum", $info), 1);
    } else {
        print_error('cannotunsubscribe', 'forum', $_SERVER["HTTP_REFERER"]);
    }

} else {  // subscribe
    if ($forum->forcesubscribe == FORUM_DISALLOWSUBSCRIBE &&
                !has_capability('mod/forum:managesubscriptions', $context)) {
        print_error('disallowsubscribe', 'forum', $_SERVER["HTTP_REFERER"]);
    }
    if (!has_capability('mod/forum:viewdiscussion', $context)) {
        print_error('noviewdiscussionspermission', 'forum', $_SERVER["HTTP_REFERER"]);
    }
    if (is_null($sesskey)) {    // we came here via link in email
        $PAGE->set_title($course->shortname);
        $PAGE->set_heading($course->fullname);
        echo $OUTPUT->header();
        echo $OUTPUT->confirm(get_string('confirmsubscribe', 'forum', format_string($forum->name)),
                new moodle_url($PAGE->url, array('sesskey' => sesskey())), new moodle_url('/mod/forum/view.php', array('f' => $id)));
        echo $OUTPUT->footer();
        exit;
    }
    require_sesskey();
    forum_subscribe($user->id, $forum->id);
    add_to_log($course->id, "forum", "subscribe", "view.php?f=$forum->id", $forum->id, $cm->id);
    redirect($returnto, get_string("nowsubscribed", "forum", $info), 1);
}
