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
 * Subscribe to or unsubscribe from a forum.
 *
 * @package mod-forum
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once("lib.php");

$id = required_param('id',PARAM_INT);      // The forum to subscribe or unsubscribe to
$mode = optional_param('mode',false,PARAM_INT);  // Force everyone to be subscribed to this forum?
$user = optional_param('user',0,PARAM_INT);

$url = new moodle_url('/mod/forum/subscribe.php', array('id'=>$id));
if ($mode !== '') {
    $url->param('force', $mode);
}
if ($user !== 0) {
    $url->param('user', $user);
}
$PAGE->set_url($url);

if (! $forum = $DB->get_record("forum", array("id" => $id))) {
    print_error('invalidforumid', 'forum');
}

if (! $course = $DB->get_record("course", array("id" => $forum->course))) {
    print_error('invalidcoursemodule');
}

$cm = get_coursemodule_from_instance("forum", $forum->id, $course->id, false, MUST_EXIST);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

if ($user) {
    if (!has_capability('mod/forum:managesubscriptions', $context)) {
        print_error('nopermissiontosubscribe', 'forum');
    }
    if (!$user = $DB->get_record("user", array("id" => $user))) {
        print_error('invaliduserid');
    }
} else {
    $user = $USER;
}

if (isset($cm->groupmode) && empty($course->groupmodeforce)) {
    $groupmode =  $cm->groupmode;
} else {
    $groupmode = $course->groupmode;
}
if ($groupmode && !forum_is_subscribed($user->id, $forum) && !has_capability('moodle/site:accessallgroups', $context)) {
    if (!groups_get_all_groups($course->id, $USER->id)) {
        print_error('cannotsubscribe', 'forum');
    }
}

require_login($course->id, false, $cm);

if (!is_enrolled($context)) {   // Guests and visitors can't subscribe - only enrolled
    $PAGE->set_title($course->shortname);
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();
    echo $OUTPUT->confirm(get_string('noguestsubscribe', 'forum').'<br /><br />'.get_string('liketologin'),
                 get_login_url(), new moodle_url('/mod/forum/view.php', array('f'=>$id)));
    echo $OUTPUT->footer();
    exit;
}

$returnto = optional_param('backtoindex',0,PARAM_INT)
    ? "index.php?id=".$course->id
    : "view.php?f=$id";

if ($mode !== false && has_capability('mod/forum:managesubscriptions', $context)) {
    switch ($mode) {
        case FORUM_CHOOSESUBSCRIBE : // 0
            forum_forcesubscribe($forum->id, 0);
            redirect($returnto, get_string("everyonecannowchoose", "forum"), 1);
            break;
        case FORUM_FORCESUBSCRIBE : // 1
            forum_forcesubscribe($forum->id, 1);
            redirect($returnto, get_string("everyoneisnowsubscribed", "forum"), 1);
            break;
        case FORUM_INITIALSUBSCRIBE : // 2
            forum_forcesubscribe($forum->id, 2);
            redirect($returnto, get_string("everyoneisnowsubscribed", "forum"), 1);
            break;
        case FORUM_DISALLOWSUBSCRIBE : // 3
            forum_forcesubscribe($forum->id, 3);
            redirect($returnto, get_string("noonecansubscribenow", "forum"), 1);
            break;
        default:
            print_error(get_string('invalidforcesubscribe', 'forum'));
    }
}

if (forum_is_forcesubscribed($forum)) {
    redirect($returnto, get_string("everyoneisnowsubscribed", "forum"), 1);
}

$info->name  = fullname($user);
$info->forum = format_string($forum->name);

if (forum_is_subscribed($user->id, $forum->id)) {
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
        print_error('cannotsubscribe', 'forum', $_SERVER["HTTP_REFERER"]);
    }
    if (forum_subscribe($user->id, $forum->id) ) {
        add_to_log($course->id, "forum", "subscribe", "view.php?f=$forum->id", $forum->id, $cm->id);
        redirect($returnto, get_string("nowsubscribed", "forum", $info), 1);
    } else {
        print_error('cannotsubscribe', 'forum', $_SERVER["HTTP_REFERER"]);
    }
}

