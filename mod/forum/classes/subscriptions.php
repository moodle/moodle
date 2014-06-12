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
 * Forum subscription manager.
 *
 * @package    mod_forum
 * @copyright  2014 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum;

defined('MOODLE_INTERNAL') || die();

/**
 * Forum subscription manager.
 *
 * @copyright  2014 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class subscriptions {
    /**
     * The subscription cache for forums.
     *
     * The first level key is the user ID
     * The second level is the forum ID
     * The Value then is bool for subscribed of not.
     *
     * @var array[] An array of arrays.
     */
    protected static $forumcache = array();

    /**
     * The list of forums which have been wholly retrieved for the forum subscription cache.
     *
     * This allows for prior caching of an entire forum to reduce the
     * number of DB queries in a subscription check loop.
     *
     * @var bool[]
     */
    protected static $fetchedforums = array();

    /**
     * Whether a user is subscribed to this forum.
     *
     * @param int $userid The user ID
     * @param \stdClass $forum The record of the forum to test
     * @return boolean
     */
    private static function is_subscribed($userid, $forum) {
        // If forum is force subscribed and has allowforcesubscribe, then user is subscribed.
        $cm = get_coursemodule_from_instance('forum', $forum->id);
        if ($cm && self::is_forcesubscribed($forum) &&
                has_capability('mod/forum:allowforcesubscribe', \context_module::instance($cm->id), $userid)) {
            return true;
        }

        return self::fetch_subscription_cache($forum->id, $userid);
    }

    /**
     * Helper to determine whether a forum has it's subscription mode set
     * to forced subscription.
     *
     * @param \stdClass $forum The record of the forum to test
     * @return bool
     */
    public static function is_forcesubscribed($forum) {
        return ($forum->forcesubscribe == FORUM_FORCESUBSCRIBE);
    }

    /**
     * Helper to determine whether a forum has it's subscription mode set to disabled.
     *
     * @param \stdClass $forum The record of the forum to test
     * @return bool
     */
    public static function subscription_disabled($forum) {
        return ($forum->forcesubscribe == FORUM_DISALLOWSUBSCRIBE);
    }

    /**
     * Helper to determine whether the specified forum can be subscribed to.
     *
     * @param \stdClass $forum The record of the forum to test
     * @return bool
     */
    public static function is_subscribable($forum) {
        return (!\mod_forum\subscriptions::is_forcesubscribed($forum) &&
                !\mod_forum\subscriptions::subscription_disabled($forum));
    }

    /**
     * Set the forum subscription mode.
     *
     * By default when called without options, this is set to FORUM_FORCESUBSCRIBE.
     *
     * @param \stdClass $forum The record of the forum to set
     * @param int $status The new subscription state
     * @return bool
     */
    public static function set_subscription_mode($forumid, $status = 1) {
        global $DB;
        return $DB->set_field("forum", "forcesubscribe", $status, array("id" => $forumid));
    }

    /**
     * Returns the current subscription mode for the forum.
     *
     * @param \stdClass $forum The record of the forum to set
     * @return int The forum subscription mode
     */
    public static function get_subscription_mode($forum) {
        return $forum->forcesubscribe;
    }

    /**
     * Returns an array of forums that the current user is subscribed to and is allowed to unsubscribe from
     *
     * @return array An array of unsubscribable forums
     */
    public static function get_unsubscribable_forums() {
        global $USER, $DB;

        // Get courses that $USER is enrolled in and can see.
        $courses = enrol_get_my_courses();
        if (empty($courses)) {
            return array();
        }

        $courseids = array();
        foreach($courses as $course) {
            $courseids[] = $course->id;
        }
        list($coursesql, $courseparams) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'c');

        // get all forums from the user's courses that they are subscribed to and which are not set to forced
        $sql = "SELECT f.id, cm.id as cm, cm.visible
                FROM {forum} f
                    JOIN {course_modules} cm ON cm.instance = f.id
                    JOIN {modules} m ON m.name = :modulename AND m.id = cm.module
                    LEFT JOIN {forum_subscriptions} fs ON (fs.forum = f.id AND fs.userid = :userid)
                WHERE f.forcesubscribe <> :forcesubscribe AND fs.id IS NOT NULL
                    AND cm.course $coursesql";
        $params = array_merge($courseparams, array(
            'modulename'=>'forum',
            'userid' => $USER->id,
            'forcesubscribe' => FORUM_FORCESUBSCRIBE,
        ));
        $forums = $DB->get_recordset_sql($sql, $params);

        $unsubscribableforums = array(); // Array to return
        foreach($forums as $forum) {
            if (empty($forum->visible)) {
                // The forum is hidden - check if the user can view the forum.
                $context = context_module::instance($forum->cm);
                if (!has_capability('moodle/course:viewhiddenactivities', $context)) {
                    // The user can't see the hidden forum to cannot unsubscribe.
                    continue;
                }
            }

            $unsubscribableforums[] = $forum;
        }
        $forums->close();

        return $unsubscribableforums;
    }

    /**
     * Get the list of potential subscribers to a forum.
     *
     * @param context_module $context the forum context.
     * @param integer $groupid the id of a group, or 0 for all groups.
     * @param string $fields the list of fields to return for each user. As for get_users_by_capability.
     * @param string $sort sort order. As for get_users_by_capability.
     * @return array list of users.
     */
    public static function get_potential_subscribers($context, $groupid, $fields, $sort = '') {
        global $DB;

        // Only active enrolled users or everybody on the frontpage.
        list($esql, $params) = get_enrolled_sql($context, 'mod/forum:allowforcesubscribe', $groupid, true);
        if (!$sort) {
            list($sort, $sortparams) = users_order_by_sql('u');
            $params = array_merge($params, $sortparams);
        }

        $sql = "SELECT $fields
                FROM {user} u
                JOIN ($esql) je ON je.id = u.id
            ORDER BY $sort";

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Fetch the forum subscription data for the specified userid and forum.
     *
     * @param int $forumid The forum to retrieve a cache for
     * @param int $userid The user ID
     * @return boolean
     */
    public static function fetch_subscription_cache($forumid, $userid) {
        if (isset(self::$forumcache[$userid]) && isset(self::$forumcache[$userid][$forumid])) {
            return self::$forumcache[$userid][$forumid];
        }
        self::fill_subscription_cache($forumid, $userid);

        if (!isset(self::$forumcache[$userid]) || !isset(self::$forumcache[$userid][$forumid])) {
            return false;
        }

        return self::$forumcache[$userid][$forumid];
    }

    /**
     * Fill the forum subscription data for the specified userid and forum.
     *
     * If the userid is not specified, then all subscription data for that forum is fetched in a single query and used
     * for subsequent lookups without requiring further database queries.
     *
     * @param int $forumid The forum to retrieve a cache for
     * @param int $userid The user ID
     * @return void
     */
    public static function fill_subscription_cache($forumid, $userid = null) {
        global $DB;

        if (!isset(self::$fetchedforums[$forumid])) {
            // This forum has not been fetched as a whole.
            if (isset($userid)) {
                if (!isset(self::$forumcache[$userid])) {
                    self::$forumcache[$userid] = array();
                }

                if (!isset(self::$forumcache[$userid][$forumid])) {
                    if ($DB->record_exists('forum_subscriptions', array(
                        'userid' => $userid,
                        'forum' => $forumid,
                    ))) {
                        self::$forumcache[$userid][$forumid] = true;
                    } else {
                        self::$forumcache[$userid][$forumid] = false;
                    }
                }
            } else {
                $subscriptions = $DB->get_recordset('forum_subscriptions', array(
                    'forum' => $forumid,
                ), '', 'id, userid');
                foreach ($subscriptions as $id => $data) {
                    if (!isset(self::$forumcache[$data->userid])) {
                        self::$forumcache[$data->userid] = array();
                    }
                    self::$forumcache[$data->userid][$forumid] = true;
                }
                self::$fetchedforums[$forumid] = true;
                $subscriptions->close();
            }
        }
    }

    /**
     * Fill the forum subscription data for all forums that the specified userid can subscribe to in the specified course.
     *
     * @param int $courseid The course to retrieve a cache for
     * @param int $userid The user ID
     * @return void
     */
    public static function fill_subscription_cache_for_course($courseid, $userid) {
        global $DB;

        if (!isset(self::$forumcache[$userid])) {
            self::$forumcache[$userid] = array();
        }

        $sql = "SELECT
                    f.id AS forumid,
                    s.id AS subscriptionid
                FROM {forum} f
                LEFT JOIN {forum_subscriptions} s ON (s.forum = f.id AND s.userid = :userid)
                WHERE f.course = :course
                AND f.forcesubscribe <> :subscriptionforced";

        $subscriptions = $DB->get_recordset_sql($sql, array(
            'course' => $courseid,
            'userid' => $userid,
            'subscriptionforced' => FORUM_FORCESUBSCRIBE,
        ));

        foreach ($subscriptions as $id => $data) {
            self::$forumcache[$userid][$id] = !empty($data->subscriptionid);
        }
        $subscriptions->close();
    }

    /**
     * Returns a list of user objects who are subscribed to this forum.
     *
     * @param stdClass $forum The forum record.
     * @param int $groupid The group id if restricting subscriptions to a group of users, or 0 for all.
     * @param context_module $context the forum context, to save re-fetching it where possible.
     * @param string $fields requested user fields (with "u." table prefix).
     * @return array list of users.
     */
    public static function fetch_subscribed_users($forum, $groupid = 0, $context = null, $fields = null) {
        global $CFG, $DB;

        if (empty($fields)) {
            $allnames = get_all_user_name_fields(true, 'u');
            $fields ="u.id,
                      u.username,
                      $allnames,
                      u.maildisplay,
                      u.mailformat,
                      u.maildigest,
                      u.imagealt,
                      u.email,
                      u.emailstop,
                      u.city,
                      u.country,
                      u.lastaccess,
                      u.lastlogin,
                      u.picture,
                      u.timezone,
                      u.theme,
                      u.lang,
                      u.trackforums,
                      u.mnethostid";
        }

        // Retrieve the forum context if it wasn't specified.
        $context = forum_get_context($forum->id, $context);

        if (self::is_forcesubscribed($forum)) {
            $results = \mod_forum\subscriptions::get_potential_subscribers($context, $groupid, $fields, "u.email ASC");

        } else {
            // Only active enrolled users or everybody on the frontpage.
            list($esql, $params) = get_enrolled_sql($context, '', $groupid, true);
            $params['forumid'] = $forum->id;

            $sql = "SELECT $fields
                    FROM {user} u
                    JOIN ($esql) je ON je.id = u.id
                    JOIN {forum_subscriptions} s ON s.userid = u.id
                    WHERE
                        s.forum = :forumid
                    ORDER BY u.email ASC";
            $results = $DB->get_records_sql($sql, $params);
        }

        // Guest user should never be subscribed to a forum.
        unset($results[$CFG->siteguest]);

        return $results;
    }

    /**
     * Reset the forum cache.
     *
     * This cache is used to reduce the number of database queries when
     * checking forum subscription states.
     */
    public static function reset_forum_cache() {
        self::$forumcache = array();
        self::$fetchedforums = array();
    }

    /**
     * Adds user to the subscriber list.
     *
     * @param int $userid The ID of the user to subscribe
     * @param \stdClass $forum The forum record for this forum.
     * @param \context_module|null $context Module context, may be omitted if not known or if called for the current
     *      module set in page.
     * @return bool|int Returns true if the user is already subscribed, or the forum_subscriptions ID if the user was
     *     successfully subscribed.
     */
    public static function subscribe_user($userid, $forum, $context = null) {
        global $DB;

        if (self::is_subscribed($userid, $forum)) {
            return true;
        }

        $sub = new \stdClass();
        $sub->userid  = $userid;
        $sub->forum = $forum->id;

        $result = $DB->insert_record("forum_subscriptions", $sub);

        // Reset the cache for this forum.
        self::$forumcache[$userid][$forum->id] = true;

        $context = forum_get_context($forum->id, $context);
        $params = array(
            'context' => $context,
            'objectid' => $result,
            'relateduserid' => $userid,
            'other' => array('forumid' => $forum->id),

        );
        $event  = event\subscription_created::create($params);
        $event->trigger();

        return $result;
    }

    /**
     * Removes user from the subscriber list
     *
     * @param int $userid The ID of the user to unsubscribe
     * @param \stdClass $forum The forum record for this forum.
     * @param \context_module|null $context Module context, may be omitted if not known or if called for the current
     *     module set in page.
     * @return boolean Always returns true.
     */
    public static function unsubscribe_user($userid, $forum, $context = null) {
        global $DB;

        $sqlparams = array(
            'userid' => $userid,
            'forum' => $forum->id,
        );
        $DB->delete_records('forum_digests', $sqlparams);

        if ($forumsubscription = $DB->get_record('forum_subscriptions', $sqlparams)) {
            $DB->delete_records('forum_subscriptions', array('id' => $forumsubscription->id));

            // Reset the cache for this forum.
            self::$forumcache[$userid][$forum->id] = false;

            $context = forum_get_context($forum->id, $context);
            $params = array(
                'context' => $context,
                'objectid' => $forumsubscription->id,
                'relateduserid' => $userid,
                'other' => array('forumid' => $forum->id),

            );
            $event = event\subscription_deleted::create($params);
            $event->add_record_snapshot('forum_subscriptions', $forumsubscription);
            $event->trigger();
        }

        return true;
    }

}
