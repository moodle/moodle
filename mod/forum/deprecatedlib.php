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
 * How many posts by other users are unrated by a given user in the given discussion?
 *
 * @param int $discussionid
 * @param int $userid
 * @return mixed
 * @deprecated since Moodle 1.1 - please do not use this function any more.
 */
function forum_count_unrated_posts($discussionid, $userid) {
    global $CFG, $DB;
    debugging('forum_count_unrated_posts() is deprecated and will not be replaced.', DEBUG_DEVELOPER);

    $sql = "SELECT COUNT(*) as num
              FROM {forum_posts}
             WHERE parent > 0
               AND discussion = :discussionid
               AND userid <> :userid";
    $params = array('discussionid' => $discussionid, 'userid' => $userid);
    $posts = $DB->get_record_sql($sql, $params);
    if ($posts) {
        $sql = "SELECT count(*) as num
                  FROM {forum_posts} p,
                       {rating} r
                 WHERE p.discussion = :discussionid AND
                       p.id = r.itemid AND
                       r.userid = userid AND
                       r.component = 'mod_forum' AND
                       r.ratingarea = 'post'";
        $rated = $DB->get_record_sql($sql, $params);
        if ($rated) {
            if ($posts->num > $rated->num) {
                return $posts->num - $rated->num;
            } else {
                return 0;    // Just in case there was a counting error
            }
        } else {
            return $posts->num;
        }
    } else {
        return 0;
    }
}


// Since Moodle 1.5.

/**
 * Returns the count of records for the provided user and discussion.
 *
 * @global object
 * @global object
 * @param int $userid
 * @param int $discussionid
 * @return bool
 * @deprecated since Moodle 1.5 - please do not use this function any more.
 */
function forum_tp_count_discussion_read_records($userid, $discussionid) {
    debugging('forum_tp_count_discussion_read_records() is deprecated and will not be replaced.', DEBUG_DEVELOPER);

    global $CFG, $DB;

    $cutoffdate = isset($CFG->forum_oldpostdays) ? (time() - ($CFG->forum_oldpostdays*24*60*60)) : 0;

    $sql = 'SELECT COUNT(DISTINCT p.id) '.
           'FROM {forum_discussions} d '.
           'LEFT JOIN {forum_read} r ON d.id = r.discussionid AND r.userid = ? '.
           'LEFT JOIN {forum_posts} p ON p.discussion = d.id '.
                'AND (p.modified < ? OR p.id = r.postid) '.
           'WHERE d.id = ? ';

    return ($DB->count_records_sql($sql, array($userid, $cutoffdate, $discussionid)));
}

/**
 * Get all discussions started by a particular user in a course (or group)
 *
 * @global object
 * @global object
 * @param int $courseid
 * @param int $userid
 * @param int $groupid
 * @return array
 * @deprecated since Moodle 1.5 - please do not use this function any more.
 */
function forum_get_user_discussions($courseid, $userid, $groupid=0) {
    debugging('forum_get_user_discussions() is deprecated and will not be replaced.', DEBUG_DEVELOPER);

    global $CFG, $DB;
    $params = array($courseid, $userid);
    if ($groupid) {
        $groupselect = " AND d.groupid = ? ";
        $params[] = $groupid;
    } else  {
        $groupselect = "";
    }

    $allnames = get_all_user_name_fields(true, 'u');
    return $DB->get_records_sql("SELECT p.*, d.groupid, $allnames, u.email, u.picture, u.imagealt,
                                   f.type as forumtype, f.name as forumname, f.id as forumid
                              FROM {forum_discussions} d,
                                   {forum_posts} p,
                                   {user} u,
                                   {forum} f
                             WHERE d.course = ?
                               AND p.discussion = d.id
                               AND p.parent = 0
                               AND p.userid = u.id
                               AND u.id = ?
                               AND d.forum = f.id $groupselect
                          ORDER BY p.created DESC", $params);
}


// Since Moodle 1.6.

/**
 * Returns the count of posts for the provided forum and [optionally] group.
 * @global object
 * @global object
 * @param int $forumid
 * @param int|bool $groupid
 * @return int
 * @deprecated since Moodle 1.6 - please do not use this function any more.
 */
function forum_tp_count_forum_posts($forumid, $groupid=false) {
    debugging('forum_tp_count_forum_posts() is deprecated and will not be replaced.', DEBUG_DEVELOPER);

    global $CFG, $DB;
    $params = array($forumid);
    $sql = 'SELECT COUNT(*) '.
           'FROM {forum_posts} fp,{forum_discussions} fd '.
           'WHERE fd.forum = ? AND fp.discussion = fd.id';
    if ($groupid !== false) {
        $sql .= ' AND (fd.groupid = ? OR fd.groupid = -1)';
        $params[] = $groupid;
    }
    $count = $DB->count_records_sql($sql, $params);


    return $count;
}

/**
 * Returns the count of records for the provided user and forum and [optionally] group.
 * @global object
 * @global object
 * @param int $userid
 * @param int $forumid
 * @param int|bool $groupid
 * @return int
 * @deprecated since Moodle 1.6 - please do not use this function any more.
 */
function forum_tp_count_forum_read_records($userid, $forumid, $groupid=false) {
    debugging('forum_tp_count_forum_read_records() is deprecated and will not be replaced.', DEBUG_DEVELOPER);

    global $CFG, $DB;

    $cutoffdate = time() - ($CFG->forum_oldpostdays*24*60*60);

    $groupsel = '';
    $params = array($userid, $forumid, $cutoffdate);
    if ($groupid !== false) {
        $groupsel = "AND (d.groupid = ? OR d.groupid = -1)";
        $params[] = $groupid;
    }

    $sql = "SELECT COUNT(p.id)
              FROM  {forum_posts} p
                    JOIN {forum_discussions} d ON d.id = p.discussion
                    LEFT JOIN {forum_read} r   ON (r.postid = p.id AND r.userid= ?)
              WHERE d.forum = ?
                    AND (p.modified < $cutoffdate OR (p.modified >= ? AND r.id IS NOT NULL))
                    $groupsel";

    return $DB->get_field_sql($sql, $params);
}


// Since Moodle 1.7.

/**
 * Returns array of forum open modes.
 *
 * @return array
 * @deprecated since Moodle 1.7 - please do not use this function any more.
 */
function forum_get_open_modes() {
    debugging('forum_get_open_modes() is deprecated and will not be replaced.', DEBUG_DEVELOPER);
    return array();
}


// Since Moodle 1.9.

/**
 * Gets posts with all info ready for forum_print_post
 * We pass forumid in because we always know it so no need to make a
 * complicated join to find it out.
 *
 * @global object
 * @global object
 * @param int $parent
 * @param int $forumid
 * @return array
 * @deprecated since Moodle 1.9 MDL-13303 - please do not use this function any more.
 */
function forum_get_child_posts($parent, $forumid) {
    debugging('forum_get_child_posts() is deprecated.', DEBUG_DEVELOPER);

    global $CFG, $DB;

    $allnames = get_all_user_name_fields(true, 'u');
    return $DB->get_records_sql("SELECT p.*, $forumid AS forum, $allnames, u.email, u.picture, u.imagealt
                              FROM {forum_posts} p
                         LEFT JOIN {user} u ON p.userid = u.id
                             WHERE p.parent = ?
                          ORDER BY p.created ASC", array($parent));
}

/**
 * Gets posts with all info ready for forum_print_post
 * We pass forumid in because we always know it so no need to make a
 * complicated join to find it out.
 *
 * @global object
 * @global object
 * @return mixed array of posts or false
 * @deprecated since Moodle 1.9 MDL-13303 - please do not use this function any more.
 */
function forum_get_discussion_posts($discussion, $sort, $forumid) {
    debugging('forum_get_discussion_posts() is deprecated.', DEBUG_DEVELOPER);

    global $CFG, $DB;

    $allnames = get_all_user_name_fields(true, 'u');
    return $DB->get_records_sql("SELECT p.*, $forumid AS forum, $allnames, u.email, u.picture, u.imagealt
                              FROM {forum_posts} p
                         LEFT JOIN {user} u ON p.userid = u.id
                             WHERE p.discussion = ?
                               AND p.parent > 0 $sort", array($discussion));
}


// Since Moodle 2.0.

/**
 * Returns a list of ratings for a particular post - sorted.
 *
 * @param stdClass $context
 * @param int $postid
 * @param string $sort
 * @return array Array of ratings or false
 * @deprecated since Moodle 2.0 MDL-21657 - please do not use this function any more.
 */
function forum_get_ratings($context, $postid, $sort = "u.firstname ASC") {
    debugging('forum_get_ratings() is deprecated.', DEBUG_DEVELOPER);
    $options = new stdClass;
    $options->context = $context;
    $options->component = 'mod_forum';
    $options->ratingarea = 'post';
    $options->itemid = $postid;
    $options->sort = "ORDER BY $sort";

    $rm = new rating_manager();
    return $rm->get_all_ratings_for_item($options);
}

/**
 * Generate and return the track or no track link for a forum.
 *
 * @global object
 * @global object
 * @global object
 * @param object $forum the forum. Fields used are $forum->id and $forum->forcesubscribe.
 * @param array $messages
 * @param bool $fakelink
 * @return string
 * @deprecated since Moodle 2.0 MDL-14632 - please do not use this function any more.
 */
function forum_get_tracking_link($forum, $messages=array(), $fakelink=true) {
    debugging('forum_get_tracking_link() is deprecated.', DEBUG_DEVELOPER);

    global $CFG, $USER, $PAGE, $OUTPUT;

    static $strnotrackforum, $strtrackforum;

    if (isset($messages['trackforum'])) {
         $strtrackforum = $messages['trackforum'];
    }
    if (isset($messages['notrackforum'])) {
         $strnotrackforum = $messages['notrackforum'];
    }
    if (empty($strtrackforum)) {
        $strtrackforum = get_string('trackforum', 'forum');
    }
    if (empty($strnotrackforum)) {
        $strnotrackforum = get_string('notrackforum', 'forum');
    }

    if (forum_tp_is_tracked($forum)) {
        $linktitle = $strnotrackforum;
        $linktext = $strnotrackforum;
    } else {
        $linktitle = $strtrackforum;
        $linktext = $strtrackforum;
    }

    $link = '';
    if ($fakelink) {
        $PAGE->requires->js('/mod/forum/forum.js');
        $PAGE->requires->js_function_call('forum_produce_tracking_link', Array($forum->id, $linktext, $linktitle));
        // use <noscript> to print button in case javascript is not enabled
        $link .= '<noscript>';
    }
    $url = new moodle_url('/mod/forum/settracking.php', array(
            'id' => $forum->id,
            'sesskey' => sesskey(),
        ));
    $link .= $OUTPUT->single_button($url, $linktext, 'get', array('title'=>$linktitle));

    if ($fakelink) {
        $link .= '</noscript>';
    }

    return $link;
}

/**
 * Returns the count of records for the provided user and discussion.
 *
 * @global object
 * @global object
 * @param int $userid
 * @param int $discussionid
 * @return int
 * @deprecated since Moodle 2.0 MDL-14113 - please do not use this function any more.
 */
function forum_tp_count_discussion_unread_posts($userid, $discussionid) {
    debugging('forum_tp_count_discussion_unread_posts() is deprecated.', DEBUG_DEVELOPER);
    global $CFG, $DB;

    $cutoffdate = isset($CFG->forum_oldpostdays) ? (time() - ($CFG->forum_oldpostdays*24*60*60)) : 0;

    $sql = 'SELECT COUNT(p.id) '.
           'FROM {forum_posts} p '.
           'LEFT JOIN {forum_read} r ON r.postid = p.id AND r.userid = ? '.
           'WHERE p.discussion = ? '.
                'AND p.modified >= ? AND r.id is NULL';

    return $DB->count_records_sql($sql, array($userid, $discussionid, $cutoffdate));
}

/**
 * Converts a forum to use the Roles System
 *
 * @deprecated since Moodle 2.0 MDL-23479 - please do not use this function any more.
 */
function forum_convert_to_roles() {
    debugging('forum_convert_to_roles() is deprecated and will not be replaced.', DEBUG_DEVELOPER);
}

/**
 * Returns all records in the 'forum_read' table matching the passed keys, indexed
 * by userid.
 *
 * @global object
 * @param int $userid
 * @param int $postid
 * @param int $discussionid
 * @param int $forumid
 * @return array
 * @deprecated since Moodle 2.0 MDL-14113 - please do not use this function any more.
 */
function forum_tp_get_read_records($userid=-1, $postid=-1, $discussionid=-1, $forumid=-1) {
    debugging('forum_tp_get_read_records() is deprecated and will not be replaced.', DEBUG_DEVELOPER);

    global $DB;
    $select = '';
    $params = array();

    if ($userid > -1) {
        if ($select != '') $select .= ' AND ';
        $select .= 'userid = ?';
        $params[] = $userid;
    }
    if ($postid > -1) {
        if ($select != '') $select .= ' AND ';
        $select .= 'postid = ?';
        $params[] = $postid;
    }
    if ($discussionid > -1) {
        if ($select != '') $select .= ' AND ';
        $select .= 'discussionid = ?';
        $params[] = $discussionid;
    }
    if ($forumid > -1) {
        if ($select != '') $select .= ' AND ';
        $select .= 'forumid = ?';
        $params[] = $forumid;
    }

    return $DB->get_records_select('forum_read', $select, $params);
}

/**
 * Returns all read records for the provided user and discussion, indexed by postid.
 *
 * @global object
 * @param inti $userid
 * @param int $discussionid
 * @deprecated since Moodle 2.0 MDL-14113 - please do not use this function any more.
 */
function forum_tp_get_discussion_read_records($userid, $discussionid) {
    debugging('forum_tp_get_discussion_read_records() is deprecated and will not be replaced.', DEBUG_DEVELOPER);

    global $DB;
    $select = 'userid = ? AND discussionid = ?';
    $fields = 'postid, firstread, lastread';
    return $DB->get_records_select('forum_read', $select, array($userid, $discussionid), '', $fields);
}

// Deprecated in 2.3.

/**
 * This function gets run whenever user is enrolled into course
 *
 * @deprecated since Moodle 2.3 MDL-33166 - please do not use this function any more.
 * @param stdClass $cp
 * @return void
 */
function forum_user_enrolled($cp) {
    debugging('forum_user_enrolled() is deprecated. Please use forum_user_role_assigned instead.', DEBUG_DEVELOPER);
    global $DB;

    // NOTE: this has to be as fast as possible - we do not want to slow down enrolments!
    //       Originally there used to be 'mod/forum:initialsubscriptions' which was
    //       introduced because we did not have enrolment information in earlier versions...

    $sql = "SELECT f.id
              FROM {forum} f
         LEFT JOIN {forum_subscriptions} fs ON (fs.forum = f.id AND fs.userid = :userid)
             WHERE f.course = :courseid AND f.forcesubscribe = :initial AND fs.id IS NULL";
    $params = array('courseid'=>$cp->courseid, 'userid'=>$cp->userid, 'initial'=>FORUM_INITIALSUBSCRIBE);

    $forums = $DB->get_records_sql($sql, $params);
    foreach ($forums as $forum) {
        \mod_forum\subscriptions::subscribe_user($cp->userid, $forum);
    }
}


// Deprecated in 2.4.

/**
 * Checks to see if a user can view a particular post.
 *
 * @deprecated since Moodle 2.4 use forum_user_can_see_post() instead
 *
 * @param object $post
 * @param object $course
 * @param object $cm
 * @param object $forum
 * @param object $discussion
 * @param object $user
 * @return boolean
 */
function forum_user_can_view_post($post, $course, $cm, $forum, $discussion, $user=null){
    debugging('forum_user_can_view_post() is deprecated. Please use forum_user_can_see_post() instead.', DEBUG_DEVELOPER);
    return forum_user_can_see_post($forum, $discussion, $post, $user, $cm);
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
    throw new coding_exception('forum_shorten_post() can not be used any more. Please use shorten_text($message, $CFG->forum_shortpost) instead.');
}

// Deprecated in 2.8.

/**
 * @global object
 * @param int $userid
 * @param object $forum
 * @return bool
 * @deprecated since Moodle 2.8 use \mod_forum\subscriptions::is_subscribed() instead
 */
function forum_is_subscribed($userid, $forum) {
    global $DB;
    debugging("forum_is_subscribed() has been deprecated, please use \\mod_forum\\subscriptions::is_subscribed() instead.",
            DEBUG_DEVELOPER);

    // Note: The new function does not take an integer form of forum.
    if (is_numeric($forum)) {
        $forum = $DB->get_record('forum', array('id' => $forum));
    }

    return mod_forum\subscriptions::is_subscribed($userid, $forum);
}

/**
 * Adds user to the subscriber list
 *
 * @param int $userid
 * @param int $forumid
 * @param context_module|null $context Module context, may be omitted if not known or if called for the current module set in page.
 * @param boolean $userrequest Whether the user requested this change themselves. This has an effect on whether
 * discussion subscriptions are removed too.
 * @deprecated since Moodle 2.8 use \mod_forum\subscriptions::subscribe_user() instead
 */
function forum_subscribe($userid, $forumid, $context = null, $userrequest = false) {
    global $DB;
    debugging("forum_subscribe() has been deprecated, please use \\mod_forum\\subscriptions::subscribe_user() instead.",
            DEBUG_DEVELOPER);

    // Note: The new function does not take an integer form of forum.
    $forum = $DB->get_record('forum', array('id' => $forumid));
    \mod_forum\subscriptions::subscribe_user($userid, $forum, $context, $userrequest);
}

/**
 * Removes user from the subscriber list
 *
 * @param int $userid
 * @param int $forumid
 * @param context_module|null $context Module context, may be omitted if not known or if called for the current module set in page.
 * @param boolean $userrequest Whether the user requested this change themselves. This has an effect on whether
 * discussion subscriptions are removed too.
 * @deprecated since Moodle 2.8 use \mod_forum\subscriptions::unsubscribe_user() instead
 */
function forum_unsubscribe($userid, $forumid, $context = null, $userrequest = false) {
    global $DB;
    debugging("forum_unsubscribe() has been deprecated, please use \\mod_forum\\subscriptions::unsubscribe_user() instead.",
            DEBUG_DEVELOPER);

    // Note: The new function does not take an integer form of forum.
    $forum = $DB->get_record('forum', array('id' => $forumid));
    \mod_forum\subscriptions::unsubscribe_user($userid, $forum, $context, $userrequest);
}

/**
 * Returns list of user objects that are subscribed to this forum.
 *
 * @param stdClass $course the course
 * @param stdClass $forum the forum
 * @param int $groupid group id, or 0 for all.
 * @param context_module $context the forum context, to save re-fetching it where possible.
 * @param string $fields requested user fields (with "u." table prefix)
 * @param boolean $considerdiscussions Whether to take discussion subscriptions and unsubscriptions into consideration.
 * @return array list of users.
 * @deprecated since Moodle 2.8 use \mod_forum\subscriptions::fetch_subscribed_users() instead
  */
function forum_subscribed_users($course, $forum, $groupid = 0, $context = null, $fields = null) {
    debugging("forum_subscribed_users() has been deprecated, please use \\mod_forum\\subscriptions::fetch_subscribed_users() instead.",
            DEBUG_DEVELOPER);

    \mod_forum\subscriptions::fetch_subscribed_users($forum, $groupid, $context, $fields);
}

/**
 * Determine whether the forum is force subscribed.
 *
 * @param object $forum
 * @return bool
 * @deprecated since Moodle 2.8 use \mod_forum\subscriptions::is_forcesubscribed() instead
 */
function forum_is_forcesubscribed($forum) {
    debugging("forum_is_forcesubscribed() has been deprecated, please use \\mod_forum\\subscriptions::is_forcesubscribed() instead.",
            DEBUG_DEVELOPER);

    global $DB;
    if (!isset($forum->forcesubscribe)) {
       $forum = $DB->get_field('forum', 'forcesubscribe', array('id' => $forum));
    }

    return \mod_forum\subscriptions::is_forcesubscribed($forum);
}

/**
 * Set the subscription mode for a forum.
 *
 * @param int $forumid
 * @param mixed $value
 * @return bool
 * @deprecated since Moodle 2.8 use \mod_forum\subscriptions::set_subscription_mode() instead
 */
function forum_forcesubscribe($forumid, $value = 1) {
    debugging("forum_forcesubscribe() has been deprecated, please use \\mod_forum\\subscriptions::set_subscription_mode() instead.",
            DEBUG_DEVELOPER);

    return \mod_forum\subscriptions::set_subscription_mode($forumid, $value);
}

/**
 * Get the current subscription mode for the forum.
 *
 * @param int|stdClass $forumid
 * @param mixed $value
 * @return bool
 * @deprecated since Moodle 2.8 use \mod_forum\subscriptions::get_subscription_mode() instead
 */
function forum_get_forcesubscribed($forum) {
    debugging("forum_get_forcesubscribed() has been deprecated, please use \\mod_forum\\subscriptions::get_subscription_mode() instead.",
            DEBUG_DEVELOPER);

    global $DB;
    if (!isset($forum->forcesubscribe)) {
       $forum = $DB->get_field('forum', 'forcesubscribe', array('id' => $forum));
    }

    return \mod_forum\subscriptions::get_subscription_mode($forumid, $value);
}

/**
 * Get a list of forums in the specified course in which a user can change
 * their subscription preferences.
 *
 * @param stdClass $course The course from which to find subscribable forums.
 * @return array
 * @deprecated since Moodle 2.8 use \mod_forum\subscriptions::is_subscribed in combination wtih
 * \mod_forum\subscriptions::fill_subscription_cache_for_course instead.
 */
function forum_get_subscribed_forums($course) {
    debugging("forum_get_subscribed_forums() has been deprecated, please see " .
              "\\mod_forum\\subscriptions::is_subscribed::() " .
              " and \\mod_forum\\subscriptions::fill_subscription_cache_for_course instead.",
              DEBUG_DEVELOPER);

    global $USER, $CFG, $DB;
    $sql = "SELECT f.id
              FROM {forum} f
                   LEFT JOIN {forum_subscriptions} fs ON (fs.forum = f.id AND fs.userid = ?)
             WHERE f.course = ?
                   AND f.forcesubscribe <> ".FORUM_DISALLOWSUBSCRIBE."
                   AND (f.forcesubscribe = ".FORUM_FORCESUBSCRIBE." OR fs.id IS NOT NULL)";
    if ($subscribed = $DB->get_records_sql($sql, array($USER->id, $course->id))) {
        foreach ($subscribed as $s) {
            $subscribed[$s->id] = $s->id;
        }
        return $subscribed;
    } else {
        return array();
    }
}

/**
 * Returns an array of forums that the current user is subscribed to and is allowed to unsubscribe from
 *
 * @return array An array of unsubscribable forums
 * @deprecated since Moodle 2.8 use \mod_forum\subscriptions::get_unsubscribable_forums() instead
 */
function forum_get_optional_subscribed_forums() {
    debugging("forum_get_optional_subscribed_forums() has been deprecated, please use \\mod_forum\\subscriptions::get_unsubscribable_forums() instead.",
            DEBUG_DEVELOPER);

    return \mod_forum\subscriptions::get_unsubscribable_forums();
}

/**
 * Get the list of potential subscribers to a forum.
 *
 * @param object $forumcontext the forum context.
 * @param integer $groupid the id of a group, or 0 for all groups.
 * @param string $fields the list of fields to return for each user. As for get_users_by_capability.
 * @param string $sort sort order. As for get_users_by_capability.
 * @return array list of users.
 * @deprecated since Moodle 2.8 use \mod_forum\subscriptions::get_potential_subscribers() instead
 */
function forum_get_potential_subscribers($forumcontext, $groupid, $fields, $sort = '') {
    debugging("forum_get_potential_subscribers() has been deprecated, please use \\mod_forum\\subscriptions::get_potential_subscribers() instead.",
            DEBUG_DEVELOPER);

    \mod_forum\subscriptions::get_potential_subscribers($forumcontext, $groupid, $fields, $sort);
}
