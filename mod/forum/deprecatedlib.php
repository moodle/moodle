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
 * @deprecated since Moodle 3.0 use \mod_forum\output\forum_post_email instead
 */
function forum_make_mail_text() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. Please use '
        . '\mod_forum\output\forum_post_email');
}

/**
 * @deprecated since Moodle 3.0 use \mod_forum\output\forum_post_email instead
 */
function forum_make_mail_html() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. Please use '
        . '\mod_forum\output\forum_post_email');
}

/**
 * @deprecated since Moodle 3.0 use \mod_forum\output\forum_post_email instead
 */
function forum_make_mail_post() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. Please use '
        . '\mod_forum\output\forum_post_email');
}

/**
 * @deprecated since Moodle 3.7
 */
function forum_cron_minimise_user_record() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. It has not been replaced.');
}

/**
 * @deprecated since Moodle 3.7
 */
function forum_cron() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. Please use the forum tasks');
}

/**
 * @deprecated since Moodle 3.7
 */
function forum_print_discussion() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. ' .
        'please use \mod_forum\local\renderers\discussion instead');
}


/**
 * @deprecated since Moodle 3.7
 */
function forum_post_nesting_cache() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. ' .
        'please use \mod_forum\local\renderers\posts instead');
}

/**
 * @deprecated since Moodle 3.7
 */
function forum_should_start_post_nesting() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. ' .
        'please use \mod_forum\local\renderers\posts instead');
}

/**
 * @deprecated since Moodle 3.7
 */
function forum_should_end_post_nesting() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. ' .
        'please use \mod_forum\local\renderers\posts instead');
}

/**
 * @deprecated since Moodle 3.7
 */
function forum_print_post_start() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. ' .
        'please use \mod_forum\local\renderers\posts instead');
}

/**
 * @deprecated since Moodle 3.7
 */
function forum_print_post_end() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. ' .
        'please use \mod_forum\local\renderers\posts instead');
}

/**
 * @deprecated since Moodle 3.7
 */
function forum_print_post() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. ' .
        'please use \mod_forum\local\renderers\posts instead');
}

/**
 * @deprecated since Moodle 3.7
 */
function forum_print_posts_flat() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. ' .
        'please use \mod_forum\local\renderers\posts instead');
}

/**
 * @deprecated since Moodle 3.7
 */
function forum_print_posts_threaded() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. ' .
        'please use \mod_forum\local\renderers\posts instead');
}

/**
 * @deprecated since Moodle 3.7
 */
function forum_print_posts_nested() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. ' .
        'please use \mod_forum\local\renderers\posts instead');
}

/**
 * @deprecated since Moodle 3.7
 */
function forum_print_latest_discussions() {
    throw new coding_exception(__FUNCTION__ . '() can not be used any more. ');
}

/**
 * @deprecated since Moodle 3.7
 */
function forum_count_replies() {
    throw new coding_exception(__FUNCTION__ . ' has been removed. Please use get_reply_count_for_post_id_in_discussion_id in
    the post vault.');
}

/**
 * @deprecated since Moodle 3.8
 */
function forum_scale_used() {
    throw new coding_exception('forum_scale_used() can not be used anymore. Plugins can implement ' .
        '<modname>_scale_used_anywhere, all implementations of <modname>_scale_used are now ignored');
}

/**
 * @deprecated since Moodle 3.8
 */
function forum_get_user_grades() {
    throw new \coding_exception('forum_get_user_grades() is deprecated and no longer used. '  .
        'Please use rating_manager::get_user_grades() instead.');
}

/**
 * @deprecated since Moodle 3.11
 */
function forum_get_completion_state() {
    $completionclass = \mod_forum\completion\custom_completion::class;
    throw new coding_exception(__FUNCTION__ . "() has been removed, please use the '{$completionclass}' class instead");
}

/**
 * @deprecated since Moodle 4.0
 */
#[\core\attribute\deprecated(
    since: '4.0',
    reason: 'The \'Manage subscribers\' button has been replaced with tertiary navigation.',
    mdl: 'MDL-73415',
    final: true
)]
function forum_update_subscriptions_button(): void {
    \core\deprecation::emit_deprecation_if_present(__FUNCTION__);
}

/**
 * This function prints the overview of a discussion in the forum listing.
 * It needs some discussion information and some post information, these
 * happen to be combined for efficiency in the $post parameter by the function
 * that calls this one: forum_print_latest_discussions()
 *
 * @deprecated since Moodle 4.3
 */
function forum_print_discussion_header() {
    throw new \coding_exception('forum_print_discussion_header has been deprecated');
}
