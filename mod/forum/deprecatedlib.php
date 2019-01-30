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
 * @package   mod_forum
 * @copyright 2014 Andrew Robert Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Deprecated a very long time ago.

/**
 * @deprecated since Moodle 1.1 - please do not use this function any more.
 */
function forum_count_unrated_posts() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more.');
}


// Since Moodle 1.5.

/**
 * @deprecated since Moodle 1.5 - please do not use this function any more.
 */
function forum_tp_count_discussion_read_records() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more.');
}

/**
 * @deprecated since Moodle 1.5 - please do not use this function any more.
 */
function forum_get_user_discussions() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more.');
}


// Since Moodle 1.6.

/**
 * @deprecated since Moodle 1.6 - please do not use this function any more.
 */
function forum_tp_count_forum_posts() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more.');
}

/**
 * @deprecated since Moodle 1.6 - please do not use this function any more.
 */
function forum_tp_count_forum_read_records() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more.');
}


// Since Moodle 1.7.

/**
 * @deprecated since Moodle 1.7 - please do not use this function any more.
 */
function forum_get_open_modes() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more.');
}


// Since Moodle 1.9.

/**
 * @deprecated since Moodle 1.9 MDL-13303 - please do not use this function any more.
 */
function forum_get_child_posts() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more.');
}

/**
 * @deprecated since Moodle 1.9 MDL-13303 - please do not use this function any more.
 */
function forum_get_discussion_posts() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more.');
}


// Since Moodle 2.0.

/**
 * @deprecated since Moodle 2.0 MDL-21657 - please do not use this function any more.
 */
function forum_get_ratings() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more.');
}

/**
 * @deprecated since Moodle 2.0 MDL-14632 - please do not use this function any more.
 */
function forum_get_tracking_link() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more.');
}

/**
 * @deprecated since Moodle 2.0 MDL-14113 - please do not use this function any more.
 */
function forum_tp_count_discussion_unread_posts() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more.');
}

/**
 * @deprecated since Moodle 2.0 MDL-23479 - please do not use this function any more.
 */
function forum_convert_to_roles() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more.');
}

/**
 * @deprecated since Moodle 2.0 MDL-14113 - please do not use this function any more.
 */
function forum_tp_get_read_records() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more.');
}

/**
 * @deprecated since Moodle 2.0 MDL-14113 - please do not use this function any more.
 */
function forum_tp_get_discussion_read_records() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more.');
}

// Deprecated in 2.3.

/**
 * @deprecated since Moodle 2.3 MDL-33166 - please do not use this function any more.
 */
function forum_user_enrolled() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more.');
}


// Deprecated in 2.4.

/**
 * @deprecated since Moodle 2.4 use forum_user_can_see_post() instead
 */
function forum_user_can_view_post() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more.');
}


// Deprecated in 2.6.

/**
 * FORUM_TRACKING_ON - deprecated alias for FORUM_TRACKING_FORCED.
 * @deprecated since 2.6
 */
define('FORUM_TRACKING_ON', 2);

/**
 * @deprecated since Moodle 2.6
 * @see shorten_text()
 */
function forum_shorten_post($message) {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. '
        . 'Please use shorten_text($message, $CFG->forum_shortpost) instead.');
}

// Deprecated in 2.8.

/**
 * @deprecated since Moodle 2.8 use \mod_forum\subscriptions::is_subscribed() instead
 */
function forum_is_subscribed() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more.');
}

/**
 * @deprecated since Moodle 2.8 use \mod_forum\subscriptions::subscribe_user() instead
 */
function forum_subscribe() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. Please use '
        . \mod_forum\subscriptions::class . '::subscribe_user() instead');
}

/**
 * @deprecated since Moodle 2.8 use \mod_forum\subscriptions::unsubscribe_user() instead
 */
function forum_unsubscribe() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. Please use '
        . \mod_forum\subscriptions::class . '::unsubscribe_user() instead');
}

/**
 * @deprecated since Moodle 2.8 use \mod_forum\subscriptions::fetch_subscribed_users() instead
  */
function forum_subscribed_users() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. Please use '
        . \mod_forum\subscriptions::class . '::fetch_subscribed_users() instead');
}

/**
 * Determine whether the forum is force subscribed.
 *
 * @deprecated since Moodle 2.8 use \mod_forum\subscriptions::is_forcesubscribed() instead
 */
function forum_is_forcesubscribed($forum) {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. Please use '
        . \mod_forum\subscriptions::class . '::is_forcesubscribed() instead');
}

/**
 * @deprecated since Moodle 2.8 use \mod_forum\subscriptions::set_subscription_mode() instead
 */
function forum_forcesubscribe($forumid, $value = 1) {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. Please use '
        . \mod_forum\subscriptions::class . '::set_subscription_mode() instead');
}

/**
 * @deprecated since Moodle 2.8 use \mod_forum\subscriptions::get_subscription_mode() instead
 */
function forum_get_forcesubscribed($forum) {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. Please use '
        . \mod_forum\subscriptions::class . '::set_subscription_mode() instead');
}

/**
 * @deprecated since Moodle 2.8 use \mod_forum\subscriptions::is_subscribed in combination wtih
 * \mod_forum\subscriptions::fill_subscription_cache_for_course instead.
 */
function forum_get_subscribed_forums() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. Please use '
        . \mod_forum\subscriptions::class . '::is_subscribed(), and '
        . \mod_forum\subscriptions::class . '::fill_subscription_cache_for_course() instead');
}

/**
 * @deprecated since Moodle 2.8 use \mod_forum\subscriptions::get_unsubscribable_forums() instead
 */
function forum_get_optional_subscribed_forums() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. Please use '
        . \mod_forum\subscriptions::class . '::get_unsubscribable_forums() instead');
}

/**
 * @deprecated since Moodle 2.8 use \mod_forum\subscriptions::get_potential_subscribers() instead
 */
function forum_get_potential_subscribers() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. Please use '
        . \mod_forum\subscriptions::class . '::get_potential_subscribers() instead');
}

/**
 * Builds and returns the body of the email notification in plain text.
 *
 * @uses CONTEXT_MODULE
 * @param object $course
 * @param object $cm
 * @param object $forum
 * @param object $discussion
 * @param object $post
 * @param object $userfrom
 * @param object $userto
 * @param boolean $bare
 * @param string $replyaddress The inbound address that a user can reply to the generated e-mail with. [Since 2.8].
 * @return string The email body in plain text format.
 * @deprecated since Moodle 3.0 use \mod_forum\output\forum_post_email instead
 */
function forum_make_mail_text($course, $cm, $forum, $discussion, $post, $userfrom, $userto, $bare = false, $replyaddress = null) {
    global $PAGE;
    $renderable = new \mod_forum\output\forum_post_email(
        $course,
        $cm,
        $forum,
        $discussion,
        $post,
        $userfrom,
        $userto,
        forum_user_can_post($forum, $discussion, $userto, $cm, $course)
        );

    $modcontext = context_module::instance($cm->id);
    $renderable->viewfullnames = has_capability('moodle/site:viewfullnames', $modcontext, $userto->id);

    if ($bare) {
        $renderer = $PAGE->get_renderer('mod_forum', 'emaildigestfull', 'textemail');
    } else {
        $renderer = $PAGE->get_renderer('mod_forum', 'email', 'textemail');
    }

    debugging("forum_make_mail_text() has been deprecated, please use the \mod_forum\output\forum_post_email renderable instead.",
            DEBUG_DEVELOPER);

    return $renderer->render($renderable);
}

/**
 * Builds and returns the body of the email notification in html format.
 *
 * @param object $course
 * @param object $cm
 * @param object $forum
 * @param object $discussion
 * @param object $post
 * @param object $userfrom
 * @param object $userto
 * @param string $replyaddress The inbound address that a user can reply to the generated e-mail with. [Since 2.8].
 * @return string The email text in HTML format
 * @deprecated since Moodle 3.0 use \mod_forum\output\forum_post_email instead
 */
function forum_make_mail_html($course, $cm, $forum, $discussion, $post, $userfrom, $userto, $replyaddress = null) {
    return forum_make_mail_post($course,
        $cm,
        $forum,
        $discussion,
        $post,
        $userfrom,
        $userto,
        forum_user_can_post($forum, $discussion, $userto, $cm, $course)
    );
}

/**
 * Given the data about a posting, builds up the HTML to display it and
 * returns the HTML in a string.  This is designed for sending via HTML email.
 *
 * @param object $course
 * @param object $cm
 * @param object $forum
 * @param object $discussion
 * @param object $post
 * @param object $userfrom
 * @param object $userto
 * @param bool $ownpost
 * @param bool $reply
 * @param bool $link
 * @param bool $rate
 * @param string $footer
 * @return string
 * @deprecated since Moodle 3.0 use \mod_forum\output\forum_post_email instead
 */
function forum_make_mail_post($course, $cm, $forum, $discussion, $post, $userfrom, $userto,
                              $ownpost=false, $reply=false, $link=false, $rate=false, $footer="") {
    global $PAGE;
    $renderable = new \mod_forum\output\forum_post_email(
        $course,
        $cm,
        $forum,
        $discussion,
        $post,
        $userfrom,
        $userto,
        $reply);

    $modcontext = context_module::instance($cm->id);
    $renderable->viewfullnames = has_capability('moodle/site:viewfullnames', $modcontext, $userto->id);

    // Assume that this is being used as a standard forum email.
    $renderer = $PAGE->get_renderer('mod_forum', 'email', 'htmlemail');

    debugging("forum_make_mail_post() has been deprecated, please use the \mod_forum\output\forum_post_email renderable instead.",
            DEBUG_DEVELOPER);

    return $renderer->render($renderable);
}

/**
 * Removes properties from user record that are not necessary for sending post notifications.
 *
 * @param stdClass $user
 * @return void, $user parameter is modified
 * @deprecated since Moodle 3.7
 */
function forum_cron_minimise_user_record(stdClass $user) {
    debugging("forum_cron_minimise_user_record() has been deprecated and has not been replaced.",
            DEBUG_DEVELOPER);

    // We store large amount of users in one huge array,
    // make sure we do not store info there we do not actually need
    // in mail generation code or messaging.

    unset($user->institution);
    unset($user->department);
    unset($user->address);
    unset($user->city);
    unset($user->url);
    unset($user->currentlogin);
    unset($user->description);
    unset($user->descriptionformat);
}

/**
 * Function to be run periodically according to the scheduled task.
 *
 * Finds all posts that have yet to be mailed out, and mails them out to all subscribers as well as other maintance
 * tasks.
 *
 * @deprecated since Moodle 3.7
 */
function forum_cron() {
    debugging("forum_cron() has been deprecated and replaced with new tasks. Please uses these instead.",
            DEBUG_DEVELOPER);
}
