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
 * Displays a post, and all the posts below it.
 * If no post is given, displays all posts in a discussion
 *
 * @package   mod_forum
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$d      = required_param('d', PARAM_INT);                // Discussion ID
$parent = optional_param('parent', 0, PARAM_INT);        // If set, then display this post and all children.
$mode   = optional_param('mode', 0, PARAM_INT);          // If set, changes the layout of the thread
$move   = optional_param('move', 0, PARAM_INT);          // If set, moves this discussion to another forum
$mark   = optional_param('mark', '', PARAM_ALPHA);       // Used for tracking read posts if user initiated.
$postid = optional_param('postid', 0, PARAM_INT);        // Used for tracking read posts if user initiated.
$pin    = optional_param('pin', -1, PARAM_INT);          // If set, pin or unpin this discussion.

$url = new moodle_url('/mod/forum/discuss.php', array('d'=>$d));
if ($parent !== 0) {
    $url->param('parent', $parent);
}
$PAGE->set_url($url);

$vaultfactory = mod_forum\local\container::get_vault_factory();
$discussionvault = $vaultfactory->get_discussion_vault();
$discussion = $discussionvault->get_from_id($d);

if (!$discussion) {
    throw new \moodle_exception('Unable to find discussion with id ' . $discussionid);
}

$forumvault = $vaultfactory->get_forum_vault();
$forum = $forumvault->get_from_id($discussion->get_forum_id());

if (!$forum) {
    throw new \moodle_exception('Unable to find forum with id ' . $discussion->get_forum_id());
}

$course = $forum->get_course_record();
$cm = $forum->get_course_module_record();

require_course_login($course, true, $cm);

$managerfactory = mod_forum\local\container::get_manager_factory();
$capabilitymanager = $managerfactory->get_capability_manager($forum);
$urlfactory = mod_forum\local\container::get_url_factory();

// Make sure we can render.
if (!$capabilitymanager->can_view_discussions($USER)) {
    throw new moodle_exception('noviewdiscussionspermission', 'mod_forum');
}

$datamapperfactory = mod_forum\local\container::get_legacy_data_mapper_factory();
$forumdatamapper = $datamapperfactory->get_forum_data_mapper();
$forumrecord = $forumdatamapper->to_legacy_object($forum);
$discussiondatamapper = $datamapperfactory->get_discussion_data_mapper();
$discussionrecord = $discussiondatamapper->to_legacy_object($discussion);
$discussionviewurl = $urlfactory->get_discussion_view_url_from_discussion($discussion);

// move this down fix for MDL-6926
require_once($CFG->dirroot . '/mod/forum/lib.php');

$modcontext = $forum->get_context();

if (
    !empty($CFG->enablerssfeeds) &&
    !empty($CFG->forum_enablerssfeeds) &&
    $forum->get_rss_type() &&
    $forum->get_rss_articles()
) {
    require_once("$CFG->libdir/rsslib.php");

    $rsstitle = format_string(
        $course->shortname,
        true,
        ['context' => context_course::instance($course->id)]
    );
    $rsstitle .= ': ' . format_string($forum->get_name());
    rss_add_http_header($modcontext, 'mod_forum', $forumrecord, $rsstitle);
}

// Move discussion if requested.
if ($move > 0 && confirm_sesskey()) {
    $forumid = $forum->get_id();
    $discussionid = $discussion->get_id();
    $return = $discussionviewurl->out(false);

    if (!$forumto = $DB->get_record('forum', ['id' => $move])) {
        print_error('cannotmovetonotexist', 'forum', $return);
    }

    if (!$capabilitymanager->can_move_discussions($USER)) {
        if ($forum->get_type() == 'single') {
            print_error('cannotmovefromsingleforum', 'forum', $return);
        } else {
            print_error('nopermissions', 'error', $return, get_capability_string('mod/forum:movediscussions'));
        }
    }

    if ($forumto->type == 'single') {
        print_error('cannotmovetosingleforum', 'forum', $return);
    }

    // Get target forum cm and check it is visible to current user.
    $modinfo = get_fast_modinfo($course);
    $forums = $modinfo->get_instances_of('forum');
    if (!array_key_exists($forumto->id, $forums)) {
        print_error('cannotmovetonotfound', 'forum', $return);
    }

    $cmto = $forums[$forumto->id];
    if (!$cmto->uservisible) {
        print_error('cannotmovenotvisible', 'forum', $return);
    }

    $destinationctx = context_module::instance($cmto->id);
    require_capability('mod/forum:startdiscussion', $destinationctx);

    if (!forum_move_attachments($discussionrecord, $forumid, $forumto->id)) {
        echo $OUTPUT->notification("Errors occurred while moving attachment directories - check your file permissions");
    }
    // For each subscribed user in this forum and discussion, copy over per-discussion subscriptions if required.
    $discussiongroup = $discussion->get_group_id() == -1 ? 0 : $discussion->get_group_id();
    $potentialsubscribers = \mod_forum\subscriptions::fetch_subscribed_users(
        $forumrecord,
        $discussiongroup,
        $modcontext,
        'u.id',
        true
    );

    // Pre-seed the subscribed_discussion caches.
    // Firstly for the forum being moved to.
    \mod_forum\subscriptions::fill_subscription_cache($forumto->id);
    // And also for the discussion being moved.
    \mod_forum\subscriptions::fill_subscription_cache($forumid);
    $subscriptionchanges = [];
    $subscriptiontime = time();
    foreach ($potentialsubscribers as $subuser) {
        $userid = $subuser->id;
        $targetsubscription = \mod_forum\subscriptions::is_subscribed($userid, $forumto, null, $cmto);
        $discussionsubscribed = \mod_forum\subscriptions::is_subscribed($userid, $forumrecord, $discussionid);
        $forumsubscribed = \mod_forum\subscriptions::is_subscribed($userid, $forumrecord);

        if ($forumsubscribed && !$discussionsubscribed && $targetsubscription) {
            // The user has opted out of this discussion and the move would cause them to receive notifications again.
            // Ensure they are unsubscribed from the discussion still.
            $subscriptionchanges[$userid] = \mod_forum\subscriptions::FORUM_DISCUSSION_UNSUBSCRIBED;
        } else if (!$forumsubscribed && $discussionsubscribed && !$targetsubscription) {
            // The user has opted into this discussion and would otherwise not receive the subscription after the move.
            // Ensure they are subscribed to the discussion still.
            $subscriptionchanges[$userid] = $subscriptiontime;
        }
    }

    $DB->set_field('forum_discussions', 'forum', $forumto->id, ['id' => $discussionid]);
    $DB->set_field('forum_read', 'forumid', $forumto->id, ['discussionid' => $discussionid]);

    // Delete the existing per-discussion subscriptions and replace them with the newly calculated ones.
    $DB->delete_records('forum_discussion_subs', ['discussion' => $discussionid]);
    $newdiscussion = clone $discussionrecord;
    $newdiscussion->forum = $forumto->id;
    foreach ($subscriptionchanges as $userid => $preference) {
        if ($preference != \mod_forum\subscriptions::FORUM_DISCUSSION_UNSUBSCRIBED) {
            // Users must have viewdiscussion to a discussion.
            if (has_capability('mod/forum:viewdiscussion', $destinationctx, $userid)) {
                \mod_forum\subscriptions::subscribe_user_to_discussion($userid, $newdiscussion, $destinationctx);
            }
        } else {
            \mod_forum\subscriptions::unsubscribe_user_from_discussion($userid, $newdiscussion, $destinationctx);
        }
    }

    $params = [
        'context' => $destinationctx,
        'objectid' => $discussionid,
        'other' => [
            'fromforumid' => $forumid,
            'toforumid' => $forumto->id,
        ]
    ];
    $event = \mod_forum\event\discussion_moved::create($params);
    $event->add_record_snapshot('forum_discussions', $discussionrecord);
    $event->add_record_snapshot('forum', $forumrecord);
    $event->add_record_snapshot('forum', $forumto);
    $event->trigger();

    // Delete the RSS files for the 2 forums to force regeneration of the feeds
    require_once($CFG->dirroot . '/mod/forum/rsslib.php');
    forum_rss_delete_file($forumrecord);
    forum_rss_delete_file($forumto);

    redirect($return . '&move=-1&sesskey=' . sesskey());
}
// Pin or unpin discussion if requested.
if ($pin !== -1 && confirm_sesskey()) {
    if (!$capabilitymanager->can_pin_discussions($USER)) {
        print_error('nopermissions', 'error', $return, get_capability_string('mod/forum:pindiscussions'));
    }

    $params = ['context' => $modcontext, 'objectid' => $discussion->get_id(), 'other' => ['forumid' => $forum->get_id()]];

    switch ($pin) {
        case FORUM_DISCUSSION_PINNED:
            // Pin the discussion and trigger discussion pinned event.
            forum_discussion_pin($modcontext, $forumrecord, $discussionrecord);
            break;
        case FORUM_DISCUSSION_UNPINNED:
            // Unpin the discussion and trigger discussion unpinned event.
            forum_discussion_unpin($modcontext, $forumrecord, $discussionrecord);
            break;
        default:
            echo $OUTPUT->notification("Invalid value when attempting to pin/unpin discussion");
            break;
    }

    redirect($discussionviewurl->out(false));
}

// Trigger discussion viewed event.
forum_discussion_view($modcontext, $forumrecord, $discussionrecord);

unset($SESSION->fromdiscussion);

$saveddisplaymode = get_user_preferences('forum_displaymode', $CFG->forum_displaymode);

if ($mode) {
    $displaymode = $mode;
} else {
    $displaymode = $saveddisplaymode;
}

if (get_user_preferences('forum_useexperimentalui', false)) {
    if ($displaymode == FORUM_MODE_NESTED) {
        $displaymode = FORUM_MODE_NESTED_V2;
    }
} else {
    if ($displaymode == FORUM_MODE_NESTED_V2) {
        $displaymode = FORUM_MODE_NESTED;
    }
}

if ($displaymode != $saveddisplaymode) {
    set_user_preference('forum_displaymode', $displaymode);
}

if ($parent) {
    // If flat AND parent, then force nested display this time
    if ($displaymode == FORUM_MODE_FLATOLDEST or $displaymode == FORUM_MODE_FLATNEWEST) {
        $displaymode = FORUM_MODE_NESTED;
    }
} else {
    $parent = $discussion->get_first_post_id();
}

$postvault = $vaultfactory->get_post_vault();
if (!$post = $postvault->get_from_id($parent)) {
    print_error("notexists", 'forum', "$CFG->wwwroot/mod/forum/view.php?f={$forum->get_id()}");
}

if (!$capabilitymanager->can_view_post($USER, $discussion, $post)) {
    print_error('noviewdiscussionspermission', 'forum', "$CFG->wwwroot/mod/forum/view.php?id={$forum->get_id()}");
}

$istracked = forum_tp_is_tracked($forumrecord, $USER);
if ($mark == 'read'|| $mark == 'unread') {
    if ($CFG->forum_usermarksread && forum_tp_can_track_forums($forumrecord) && $istracked) {
        if ($mark == 'read') {
            forum_tp_add_read_record($USER->id, $postid);
        } else {
            // unread
            forum_tp_delete_read_records($USER->id, $postid);
        }
    }
}

$searchform = forum_search_form($course);

$forumnode = $PAGE->navigation->find($cm->id, navigation_node::TYPE_ACTIVITY);
if (empty($forumnode)) {
    $forumnode = $PAGE->navbar;
} else {
    $forumnode->make_active();
}
$node = $forumnode->add(format_string($discussion->get_name()), $discussionviewurl);
$node->display = false;
if ($node && $post->get_id() != $discussion->get_first_post_id()) {
    $node->add(format_string($post->get_subject()), $PAGE->url);
}

$isnestedv2displaymode = $displaymode == FORUM_MODE_NESTED_V2;
$PAGE->set_title("$course->shortname: " . format_string($discussion->get_name()));
$PAGE->set_heading($course->fullname);
if ($isnestedv2displaymode) {
    $PAGE->add_body_class('nested-v2-display-mode reset-style');
    $settingstrigger = $OUTPUT->render_from_template('mod_forum/settings_drawer_trigger', null);
    $PAGE->add_header_action($settingstrigger);
} else {
    $PAGE->set_button(forum_search_form($course));
}

echo $OUTPUT->header();
if (!$isnestedv2displaymode) {
    echo $OUTPUT->heading(format_string($forum->get_name()), 2);
    echo $OUTPUT->heading(format_string($discussion->get_name()), 3, 'discussionname');
}

$rendererfactory = mod_forum\local\container::get_renderer_factory();
$discussionrenderer = $rendererfactory->get_discussion_renderer($forum, $discussion, $displaymode);
$orderpostsby = $displaymode == FORUM_MODE_FLATNEWEST ? 'created DESC' : 'created ASC';
$replies = $postvault->get_replies_to_post($USER, $post, $capabilitymanager->can_view_any_private_reply($USER), $orderpostsby);

if ($move == -1 and confirm_sesskey()) {
    $forumname = format_string($forum->get_name(), true);
    echo $OUTPUT->notification(get_string('discussionmoved', 'forum', $forumname), 'notifysuccess');
}

echo $discussionrenderer->render($USER, $post, $replies);
echo $OUTPUT->footer();

if ($istracked && !$CFG->forum_usermarksread) {
    if ($displaymode == FORUM_MODE_THREADED) {
        forum_tp_add_read_record($USER->id, $post->get_id());
    } else {
        $postids = array_map(function($post) {
            return $post->get_id();
        }, array_merge([$post], array_values($replies)));
        forum_tp_mark_posts_read($USER, $postids);
    }
}
