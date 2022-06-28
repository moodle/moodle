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
 * Moodleoverflow subscription manager.
 *
 * This file is created by borrowing code from the mod_forum module.
 *
 * @package   mod_moodleoverflow
 * @copyright 2017 Kennet Winter <k_wint10@uni-muenster.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_moodleoverflow;

/**
 * Moodleoverflow subscription manager.
 *
 * @package   mod_moodleoverflow
 * @copyright 2017 Kennet Winter <k_wint10@uni-muenster.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class subscriptions {

    /**
     * The status value for an unsubscribed discussion.
     *
     * @var int
     */
    const MOODLEOVERFLOW_DISCUSSION_UNSUBSCRIBED = -1;

    /**
     * The subscription cache for moodleoverflows.
     *
     * The first level key is the user ID
     * The second level is the moodleoverflow ID
     * The Value then is bool for subscribed of not.
     *
     * @var array[] An array of arrays.
     */
    protected static $moodleoverflowcache = array();

    /**
     * The list of moodleoverflows which have been wholly retrieved for the subscription cache.
     *
     * This allows for prior caching of an entire moodleoverflow to reduce the
     * number of DB queries in a subscription check loop.
     *
     * @var bool[]
     */
    protected static $fetchedmoodleoverflows = array();

    /**
     * The subscription cache for moodleoverflow discussions.
     *
     * The first level key is the user ID
     * The second level is the moodleoverflow ID
     * The third level key is the discussion ID
     * The value is then the users preference (int)
     *
     * @var array[]
     */
    protected static $discussioncache = array();

    /**
     * The list of moodleoverflows which have been wholly retrieved for the discussion subscription cache.
     *
     * This allows for prior caching of an entire moodleoverflows to reduce the
     * number of DB queries in a subscription check loop.
     *
     * @var bool[]
     */
    protected static $fetcheddiscussions = array();

    /**
     * Returns whether a user is subscribed to this moodleoverflow or a specific discussion within the moodleoverflow.
     *
     * If a discussion is specified then report whether the user is subscribed to posts to this
     * particular discussion, taking into account the moodleoverflow preference.
     * If it is not specified then considere only the moodleoverflows preference.
     *
     * @param int    $userid
     * @param object $moodleoverflow
     * @param null   $discussionid
     *
     * @return bool
     */
    public static function is_subscribed($userid, $moodleoverflow, $discussionid = null) {

        // Is the user forced to be subscribed to the moodleoverflow?
        if (self::is_forcesubscribed($moodleoverflow)) {
            return true;
        }

        // Check the moodleoverflow instance if no discussionid is submitted.
        if (is_null($discussionid)) {
            return self::is_subscribed_to_moodleoverflow($userid, $moodleoverflow);
        }

        // The subscription details for the discussion needs to be checked.
        $subscriptions = self::fetch_discussion_subscription($moodleoverflow->id, $userid);

        // Check if there is a record for the discussion.
        if (isset($subscriptions[$discussionid])) {
            return ($subscriptions[$discussionid]) != self::MOODLEOVERFLOW_DISCUSSION_UNSUBSCRIBED;
        }

        // Return whether the user is subscribed to the forum.
        return self::is_subscribed_to_moodleoverflow($userid, $moodleoverflow);
    }

    /**
     * Helper to determine whether a moodleoverflow has it's subscription mode set to forced.
     *
     * @param object $moodleoverflow The record of the moodleoverflow to test
     *
     * @return bool
     */
    public static function is_forcesubscribed($moodleoverflow) {
        return ($moodleoverflow->forcesubscribe == MOODLEOVERFLOW_FORCESUBSCRIBE);
    }

    /**
     * Whether a user is subscribed to this moodloverflow.
     *
     * @param int    $userid         The user ID
     * @param object $moodleoverflow The record of the moodleoverflow to test
     *
     * @return boolean
     */
    private static function is_subscribed_to_moodleoverflow($userid, $moodleoverflow) {
        return self::fetch_subscription_cache($moodleoverflow->id, $userid);
    }

    /**
     * Fetch the moodleoverflow subscription data for the specified userid an moodleoverflow.
     *
     * @param int $moodleoverflowid The forum to retrieve a cache for
     * @param int $userid           The user ID
     *
     * @return boolean
     */
    public static function fetch_subscription_cache($moodleoverflowid, $userid) {

        // If the cache is already filled, return the result.
        if (isset(self::$moodleoverflowcache[$userid]) AND isset(self::$moodleoverflowcache[$userid][$moodleoverflowid])) {
            return self::$moodleoverflowcache[$userid][$moodleoverflowid];
        }

        // Refill the cache.
        self::fill_subscription_cache($moodleoverflowid, $userid);

        // Catch empty results.
        if (!isset(self::$moodleoverflowcache[$userid]) OR !isset(self::$moodleoverflowcache[$userid][$moodleoverflowid])) {
            return false;
        }

        // Else return the subscription state.
        return self::$moodleoverflowcache[$userid][$moodleoverflowid];
    }

    /**
     * Fill the moodleoverflow subscription data for the specified userid an moodleoverflow.
     *
     * If the userid is not specified, then all subscription data for that moodleoverflow is fetched
     * in a single query and is used for subsequent lookups without requiring further database queries.
     *
     * @param int  $moodleoverflowid The moodleoverflow to retrieve a cache for
     * @param null $userid           The user ID
     */
    public static function fill_subscription_cache($moodleoverflowid, $userid = null) {
        global $DB;

        // Check if the moodleoverflow has not been fetched as a whole.
        if (!isset(self::$fetchedmoodleoverflows[$moodleoverflowid])) {

            // Is a specified user requested?
            if (isset($userid)) {

                // Create the cache for the user.
                if (!isset(self::$moodleoverflowcache[$userid])) {
                    self::$moodleoverflowcache[$userid] = array();
                }

                // Check if the user is subscribed to the moodleoverflow.
                if (!isset(self::$moodleoverflowcache[$userid][$moodleoverflowid])) {

                    // Request to the database.
                    $params = array('userid' => $userid, 'moodleoverflow' => $moodleoverflowid);
                    if ($DB->record_exists('moodleoverflow_subscriptions', $params)) {
                        self::$moodleoverflowcache[$userid][$moodleoverflowid] = true;
                    } else {
                        self::$moodleoverflowcache[$userid][$moodleoverflowid] = false;
                    }
                }

            } else { // The request is not connected to a specific user.

                // Request all records.
                $params        = array('moodleoverflow' => $moodleoverflowid);
                $subscriptions = $DB->get_recordset('moodleoverflow_subscriptions', $params, '', 'id, userid');

                // Loop through the records.
                foreach ($subscriptions as $id => $data) {

                    // Create a new record if necessary.
                    if (!isset(self::$moodleoverflowcache[$data->userid])) {
                        self::$moodleoverflowcache[$data->userid] = array();
                    }

                    // Mark the subscription state.
                    self::$moodleoverflowcache[$data->userid][$moodleoverflowid] = true;
                }

                // Mark the moodleoverflow as fetched.
                self::$fetchedmoodleoverflows[$moodleoverflowid] = true;
                $subscriptions->close();
            }
        }
    }


    /**
     * This is returned as an array of discussions for that moodleoverflow which contain the preference in a stdClass.
     *
     * @param int  $moodleoverflowid The moodleoverflow ID
     * @param null $userid           The user ID
     *
     * @return array of stClass objects
     */
    public static function fetch_discussion_subscription($moodleoverflowid, $userid = null) {

        // Fill the discussion cache.
        self::fill_discussion_subscription_cache($moodleoverflowid, $userid);

        // Create an array, if there is no record.
        if (!isset(self::$discussioncache[$userid]) OR !isset(self::$discussioncache[$userid][$moodleoverflowid])) {
            return array();
        }

        // Return the cached subscription state.
        return self::$discussioncache[$userid][$moodleoverflowid];
    }

    /**
     * Fill the discussion subscription data for the specified user ID and moodleoverflow.
     *
     * If the user ID is not specified, all discussion subscription data for that moodleoverflow is
     * fetched in a single query and is used for subsequent lookups without requiring further database queries.
     *
     * @param int  $moodleoverflowid The moodleoverflow ID
     * @param null $userid           The user ID
     */
    public static function fill_discussion_subscription_cache($moodleoverflowid, $userid = null) {
        global $DB;

        // Check if the discussions of this moodleoverflow has been fetched as a whole.
        if (!isset(self::$fetcheddiscussions[$moodleoverflowid])) {

            // Check if data for a specific user is requested.
            if (isset($userid)) {

                // Create a new record if necessary.
                if (!isset(self::$discussioncache[$userid])) {
                    self::$discussioncache[$userid] = array();
                }

                // Check if the moodleoverflow instance is already cached.
                if (!isset(self::$discussioncache[$userid][$moodleoverflowid])) {

                    // Get all records.
                    $params        = array('userid' => $userid, 'moodleoverflow' => $moodleoverflowid);
                    $subscriptions = $DB->get_recordset('moodleoverflow_discuss_subs', $params,
                        null, 'id, discussion, preference');

                    // Loop through all of these and add them to the discussion cache.
                    foreach ($subscriptions as $id => $data) {
                        self::add_to_discussion_cache($moodleoverflowid, $userid, $data->discussion, $data->preference);
                    }

                    // Close the record set.
                    $subscriptions->close();
                }

            } else {
                // No user ID is submitted.

                // Get all records.
                $params        = array('moodleoverflow' => $moodleoverflowid);
                $subscriptions = $DB->get_recordset('moodleoverflow_discuss_subs', $params,
                    null, 'id, userid, discussion, preference');

                // Loop throuch all of them and add them to the discussion cache.
                foreach ($subscriptions as $id => $data) {
                    self::add_to_discussion_cache($moodleoverflowid, $data->userid, $data->discussion, $data->preference);
                }

                // Mark the discussions as fetched and close the recordset.
                self::$fetcheddiscussions[$moodleoverflowid] = true;
                $subscriptions->close();
            }
        }
    }

    /**
     * Add the specified discussion and the users preference to the discussion subscription cache.
     *
     * @param int $moodleoverflowid The moodleoverflow ID
     * @param int $userid           The user ID
     * @param int $discussion       The discussion ID
     * @param int $preference       The preference to store
     */
    private static function add_to_discussion_cache($moodleoverflowid, $userid, $discussion, $preference) {

        // Create a new array for the user if necessary.
        if (!isset(self::$discussioncache[$userid])) {
            self::$discussioncache[$userid] = array();
        }

        // Create a new array for the moodleoverflow if necessary.
        if (!isset(self::$discussioncache[$userid][$moodleoverflowid])) {
            self::$discussioncache[$userid][$moodleoverflowid] = array();
        }

        // Save the users preference for that discussion in this array.
        self::$discussioncache[$userid][$moodleoverflowid][$discussion] = $preference;
    }

    /**
     * Determines whether a moodleoverflow has it's subscription mode set to disabled.
     *
     * @param object $moodleoverflow The moodleoverflow ID
     *
     * @return bool
     */
    public static function subscription_disabled($moodleoverflow) {
        return ($moodleoverflow->forcesubscribe == MOODLEOVERFLOW_DISALLOWSUBSCRIBE);
    }

    /**
     * Checks wheter the specified moodleoverflow can be subscribed to.
     *
     * @param object $moodleoverflow The moodleoverflow ID
     *
     * @return boolean
     */
    public static function is_subscribable($moodleoverflow) {

        // Check if the user is an authenticated user.
        $authenticated = (isloggedin() AND !isguestuser());

        // Check if subscriptions are disabled for the moodleoverflow.
        $disabled = self::subscription_disabled($moodleoverflow);

        // Check if the moodleoverflow forces the user to be subscribed.
        $forced = self::is_forcesubscribed($moodleoverflow);

        // Return the result.
        return ($authenticated AND !$forced AND !$disabled);
    }

    /**
     * Set the moodleoverflow subscription mode.
     *
     * By default when called without options, this is set to MOODLEOVERFLOW_FORCESUBSCRIBE.
     *
     * @param int $moodleoverflowid The moodleoverflow ID
     * @param int $status           The new subscrription status
     *
     * @return bool
     */
    public static function set_subscription_mode($moodleoverflowid, $status = 1) {
        global $DB;

        // Change the value in the database.
        return $DB->set_field('moodleoverflow', 'forcesubscribe', $status, array('id' => $moodleoverflowid));
    }

    /**
     * Returns the current subscription mode for the moodleoverflow.
     *
     * @param object $moodleoverflow The moodleoverflow record
     *
     * @return int The moodleoverflow subscription mode
     */
    public static function get_subscription_mode($moodleoverflow) {
        return $moodleoverflow->forcesubscribe;
    }

    /**
     * Returns an array of moodleoverflow that the current user is subscribed to and is allowed to unsubscribe from.
     *
     * @return array Array of unsubscribable moodleoverflows
     */
    public static function get_unsubscribable_moodleoverflows() {
        global $USER, $DB;

        // Get courses that the current user is enrolled to.
        $courses = enrol_get_my_courses();
        if (empty($courses)) {
            return array();
        }

        // Get the IDs of all that courses.
        $courseids = array();
        foreach ($courses as $course) {
            $courseids[] = $course->id;
        }

        // Get a list of all moodleoverflows the user is connected to.
        list($coursesql, $courseparams) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'c');

        // Find all moodleoverflows from the user's courses that they are subscribed to and which are not set to forced.
        // It is possible for users to be subscribed to a moodleoveflow in subscriptions disallowed mode so they must be
        // listed here so that they can be unsubscribed from.
        $sql             = "SELECT m.id, cm.id as cm, m.course
                FROM {moodleoverflow} m
                JOIN {course_modules} cm ON cm.instance = m.id
                JOIN {modules} mo ON mo.name = :modulename AND mo.id = cm.module
                LEFT JOIN {moodleoverflow_subscriptions} ms ON (ms.moodleoverflow = m.id AND ms.userid = :userid)
                WHERE m.forcesubscribe <> :forcesubscribe AND ms.id IS NOT NULL AND cm.course $coursesql";
        $params          = array('modulename' => 'moodleoverflow',
                                 'userid' => $USER->id,
                                 'forcesubscribe' => MOODLEOVERFLOW_FORCESUBSCRIBE);
        $mergedparams    = array_merge($courseparams, $params);
        $moodleoverflows = $DB->get_recordset_sql($sql, $mergedparams);

        // Loop through all of the results and add them to an array.
        $unsubscribablemoodleoverflows = array();
        foreach ($moodleoverflows as $moodleoverflow) {
            $unsubscribablemoodleoverflows[] = $moodleoverflow;
        }
        $moodleoverflows->close();

        // Return the array.
        return $unsubscribablemoodleoverflows;
    }

    /**
     * Get the list of potential subscribers to a moodleoverflow.
     *
     * @param \context_module $context The moodleoverflow context.
     * @param string          $fields  The list of fields to return for each user.
     * @param string          $sort    Sort order.
     *
     * @return array List of users.
     */
    public static function get_potential_subscribers($context, $fields, $sort = '') {
        global $DB;

        // Only enrolled users can subscribe.
        list($esql, $params) = get_enrolled_sql($context);

        // Default ordering of the list.
        if (!$sort) {
            list($sort, $sortparams) = users_order_by_sql('u');
            $params = array_merge($params, $sortparams);
        }

        // Fetch results from the database.
        $sql = "SELECT $fields
                FROM {user} u
                JOIN ($esql) je ON je.id = u.id
                ORDER BY $sort";

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Fill the moodleoverflow subscription data for all moodleoverflow that the user can subscribe to in a spevific course.
     *
     * @param int $courseid The course ID
     * @param int $userid   The user ID
     */
    public static function fill_subscription_cache_for_course($courseid, $userid) {
        global $DB;

        // Create an array for the user if necessary.
        if (!isset(self::$moodleoverflowcache[$userid])) {
            self::$moodleoverflowcache[$userid] = array();
        }

        // Fetch a record set for all moodleoverflowids and their subscription id.
        $sql           = "SELECT m.id AS moodleoverflowid,s.id AS subscriptionid
                  FROM {moodleoverflow} m
             LEFT JOIN {moodleoverflow_subscriptions} s ON (s.moodleoverflow = m.id AND s.userid = :userid)
                 WHERE m.course = :course AND m.forcesubscribe <> :subscriptionforced";
        $params        = array(
            'userid'             => $userid,
            'course'             => $courseid,
            'subscriptionforced' => MOODLEOVERFLOW_FORCESUBSCRIBE,
        );
        $subscriptions = $DB->get_recordset_sql($sql, $params);

        // Loop through all records.
        foreach ($subscriptions as $id => $data) {
            self::$moodleoverflowcache[$userid][$id] = !empty($data->subscriptionid);
        }

        // Close the recordset.
        $subscriptions->close();
    }

    /**
     * Returns a list of user object who are subscribed to this moodleoverflow.
     *
     * @param stdClass        $moodleoverflow     The moodleoverflow record
     * @param \context_module $context            The moodleoverflow context
     * @param string          $fields             Requested user fields
     * @param boolean         $includediscussions Whether to take discussion subscriptions into consideration
     *
     * @return array list of users
     */
    public static function get_subscribed_users($moodleoverflow, $context, $fields = null, $includediscussions = false) {
        global $CFG, $DB;

        // Default fields if none are submitted.
        if (empty($fields)) {
            if ($CFG->branch >= 311) {
                $allnames = \core_user\fields::for_name()->get_sql('u', false, '', '', false)->selects;
            } else {
                $allnames = get_all_user_name_fields(true, 'u');
            }
            $fields   = "u.id, u.username, $allnames, u.maildisplay, u.mailformat, u.maildigest,
                u.imagealt, u.email, u.emailstop, u.city, u.country, u.lastaccess, u.lastlogin,
                u.picture, u.timezone, u.theme, u.lang, u.trackforums, u.mnethostid";
        }

        // Check if the user is forced to e subscribed to a moodleoverflow.
        if (self::is_forcesubscribed($moodleoverflow)) {

            // Find the list of potential subscribers.
            $results = self::get_potential_subscribers($context, $fields, 'u.email ASC');

        } else {

            // Only enrolled users can subscribe to a moodleoverflow.
            list($esql, $params) = get_enrolled_sql($context, '', 0, true);
            $params['moodleoverflowid'] = $moodleoverflow->id;

            // Check discussion subscriptions as well?
            if ($includediscussions) {

                // Determine more params.
                $params['smoodleoverflowid']  = $moodleoverflow->id;
                $params['dsmoodleoverflowid'] = $moodleoverflow->id;
                $params['unsubscribed']       = self::MOODLEOVERFLOW_DISCUSSION_UNSUBSCRIBED;

                // SQL-statement to fetch all needed fields from the database.
                $sql = "SELECT $fields
                        FROM (
                            SELECT userid FROM {moodleoverflow_subscriptions} s
                            WHERE s.moodleoverflow = :smoodleoverflowid
                            UNION
                            SELECT userid FROM {moodleoverflow_discuss_subs} ds
                            WHERE ds.moodleoverflow = :dsmoodleoverflowid AND ds.preference <> :unsubscribed
                        ) subscriptions
                        JOIN {user} u ON u.id = subscriptions.userid
                        JOIN ($esql) je ON je.id = u.id
                        ORDER BY u.email ASC";

            } else {
                // Dont include the discussion subscriptions.

                // SQL-statement to fetch all needed fields from the database.
                $sql = "SELECT $fields
                        FROM {user} u
                        JOIN ($esql) je ON je.id = u.id
                        JOIN {moodleoverflow_subscriptions} s ON s.userid = u.id
                        WHERE s.moodleoverflow = :moodleoverflowid
                        ORDER BY u.email ASC";
            }

            // Fetch the data.
            $results = $DB->get_records_sql($sql, $params);
        }

        // Remove all guest users from the results. They should never be subscribed to a moodleoverflow.
        unset($results[$CFG->siteguest]);

        // Apply the activity module avaiability restrictions.
        $cm      = get_coursemodule_from_instance('moodleoverflow', $moodleoverflow->id, $moodleoverflow->course);
        $modinfo = get_fast_modinfo($moodleoverflow->course);
        $info    = new \core_availability\info_module($modinfo->get_cm($cm->id));
        $results = $info->filter_user_list($results);

        // Return all subscribed users.
        return $results;
    }

    /**
     * Reset the discussion cache.
     *
     * This cache is used to reduce the number of database queries
     * when checking moodleoverflow discussion subscriptions states.
     */
    public static function reset_discussion_cache() {

        // Reset the discussion cache.
        self::$discussioncache = array();

        // Reset the fetched discussions.
        self::$fetcheddiscussions = array();
    }

    /**
     * Reset the moodleoverflow cache.
     *
     * This cache is used to reduce the number of database queries
     * when checking moodleoverflow subscription states.
     */
    public static function reset_moodleoverflow_cache() {

        // Reset the cache.
        self::$moodleoverflowcache = array();

        // Reset the fetched moodleoverflows.
        self::$fetchedmoodleoverflows = array();
    }

    /**
     * Adds user to the subscriber list.
     *
     * @param int             $userid         The user ID
     * @param \stdClass       $moodleoverflow The moodleoverflow record
     * @param \context_module $context        The module context
     * @param bool            $userrequest    Whether the user requested this change themselves.
     *
     * @return bool|int Returns true if the user is already subscribed or the subscription id if successfully subscribed.
     */
    public static function subscribe_user($userid, $moodleoverflow, $context, $userrequest = false) {
        global $DB;

        // Check if the user is already subscribed.
        if (self::is_subscribed($userid, $moodleoverflow)) {
            return true;
        }

        // Create a new subscription object.
        $sub                 = new \stdClass();
        $sub->userid         = $userid;
        $sub->moodleoverflow = $moodleoverflow->id;

        // Insert the record into the database.
        $result = $DB->insert_record('moodleoverflow_subscriptions', $sub);

        // If the subscription was requested by the user, remove all records for the discussions within this moodleoverflow.
        if ($userrequest) {

            // Delete all those discussion subscriptions.
            $params = array(
                'userid'           => $userid,
                'moodleoverflowid' => $moodleoverflow->id,
                'preference'       => self::MOODLEOVERFLOW_DISCUSSION_UNSUBSCRIBED);
            $where  = 'userid = :userid AND moodleoverflow = :moodleoverflowid AND preference <> :preference';
            $DB->delete_records_select('moodleoverflow_discuss_subs', $where, $params);

            // Reset the subscription caches for this moodleoverflow.
            // We know that there were previously entries and there aren't any more.
            if (isset(self::$discussioncache[$userid]) AND isset(self::$discussioncache[$userid][$moodleoverflow->id])) {
                foreach (self::$discussioncache[$userid][$moodleoverflow->id] as $discussionid => $preference) {
                    if ($preference != self::MOODLEOVERFLOW_DISCUSSION_UNSUBSCRIBED) {
                        unset(self::$discussioncache[$userid][$moodleoverflow->id][$discussionid]);
                    }
                }
            }
        }

        // Reset the cache for this moodleoverflow.
        self::$moodleoverflowcache[$userid][$moodleoverflow->id] = true;

        // Trigger an subscription created event.
        $params = array(
            'context'       => $context,
            'objectid'      => $result,
            'relateduserid' => $userid,
            'other'         => array('moodleoverflowid' => $moodleoverflow->id),
        );
        $event  = event\subscription_created::create($params);
        $event->trigger();

        // Return the subscription ID.
        return $result;
    }

    /**
     * Removes user from the subscriber list.
     *
     * @param int             $userid         The user ID.
     * @param \stdClass       $moodleoverflow The moodleoverflow record
     * @param \context_module $context        The module context
     * @param boolean         $userrequest    Whether the user requested this change themselves.
     *
     * @return bool Always returns true
     */
    public static function unsubscribe_user($userid, $moodleoverflow, $context, $userrequest = null) {
        global $DB;

        // Check if there is a subscription record.
        $params = array('userid' => $userid, 'moodleoverflow' => $moodleoverflow->id);
        if ($subscription = $DB->get_record('moodleoverflow_subscriptions', $params)) {

            // Delete this record.
            $DB->delete_records('moodleoverflow_subscriptions', array('id' => $subscription->id));

            // Was the unsubscription requested by the user?
            if ($userrequest) {

                // Delete the discussion subscriptions as well.
                $params = array(
                    'userid'         => $userid,
                    'moodleoverflow' => $moodleoverflow->id,
                    'preference'     => self::MOODLEOVERFLOW_DISCUSSION_UNSUBSCRIBED,
                );
                $DB->delete_records('moodleoverflow_discuss_subs', $params);

                // Update the discussion cache.
                if (isset(self::$discussioncache[$userid]) AND isset(self::$discussioncache[$userid][$moodleoverflow->id])) {
                    self::$discussioncache[$userid][$moodleoverflow->id] = array();
                }
            }

            // Reset the cache for this moodleoverflow.
            self::$moodleoverflowcache[$userid][$moodleoverflow->id] = false;

            // Trigger an subscription deletion event.
            $params = array(
                'context'       => $context,
                'objectid'      => $subscription->id,
                'relateduserid' => $userid,
                'other'         => array('moodleoverflowid' => $moodleoverflow->id),
            );
            $event  = event\subscription_deleted::create($params);
            $event->add_record_snapshot('moodleoverflow_subscriptions', $subscription);
            $event->trigger();
        }

        // The unsubscription was successful.
        return true;
    }

    /**
     * Subscribes the user to the specified discussion.
     *
     * @param int             $userid     The user ID
     * @param \stdClass       $discussion The discussion record
     * @param \context_module $context    The module context
     *
     * @return bool Whether a change was made
     */
    public static function subscribe_user_to_discussion($userid, $discussion, $context) {
        global $DB;

        // Check if the user is already subscribed to the discussion.
        $params       = array('userid' => $userid, 'discussion' => $discussion->id);
        $subscription = $DB->get_record('moodleoverflow_discuss_subs', $params);

        // Dont continue if the user is already subscribed.
        if ($subscription AND $subscription->preference != self::MOODLEOVERFLOW_DISCUSSION_UNSUBSCRIBED) {
            return false;
        }

        // Check if the user is already subscribed to the moodleoverflow.
        $params = array('userid' => $userid, 'moodleoverflow' => $discussion->moodleoverflow);
        if ($DB->record_exists('moodleoverflow_subscriptions', $params)) {

            // Check if the user is unsubscribed from the discussion.
            if ($subscription AND $subscription->preference == self::MOODLEOVERFLOW_DISCUSSION_UNSUBSCRIBED) {

                // Delete the discussion preference.
                $DB->delete_records('moodleoverflow_discuss_subs', array('id' => $subscription->id));
                unset(self::$discussioncache[$userid][$discussion->moodleoverflow][$discussion->id]);

            } else {
                // The user is already subscribed to the forum.
                return false;
            }

        } else {
            // The user is not subscribed to the moodleoverflow.

            // Check if there is already a subscription to the discussion.
            if ($subscription) {

                // Update the existing record.
                $subscription->preference = time();
                $DB->update_record('moodleoverflow_discuss_subs', $subscription);

            } else {
                // Else a new record needs to be created.
                $subscription                 = new \stdClass();
                $subscription->userid         = $userid;
                $subscription->moodleoverflow = $discussion->moodleoverflow;
                $subscription->discussion     = $discussion->id;
                $subscription->preference     = time();

                // Insert the subscription record into the database.
                $subscription->id = $DB->insert_record('moodleoverflow_discuss_subs', $subscription);
                self::$discussioncache[$userid][$discussion->moodleoverflow][$discussion->id] = $subscription->preference;
            }
        }

        // Create a discussion subscription created event.
        $params = array(
            'context'       => $context,
            'objectid'      => $subscription->id,
            'relateduserid' => $userid,
            'other'         => array('moodleoverflowid' => $discussion->moodleoverflow, 'discussion' => $discussion->id),
        );
        $event  = event\discussion_subscription_created::create($params);
        $event->trigger();

        // The subscription was successful.
        return true;
    }

    /**
     * Unsubscribes the user from the specified discussion.
     *
     * @param int             $userid     The user ID
     * @param \stdClass       $discussion The discussion record
     * @param \context_module $context    The context module
     *
     * @return bool Whether a change was made
     */
    public static function unsubscribe_user_from_discussion($userid, $discussion, $context) {
        global $DB;

        // Check the users subscription preference for this discussion.
        $params       = array('userid' => $userid, 'discussion' => $discussion->id);
        $subscription = $DB->get_record('moodleoverflow_discuss_subs', $params);

        // If the user not already subscribed to the discussion, do not continue.
        if ($subscription AND $subscription->preference == self::MOODLEOVERFLOW_DISCUSSION_UNSUBSCRIBED) {
            return false;
        }

        // Check if the user is subscribed to the moodleoverflow.
        $params = array('userid' => $userid, 'moodleoverflow' => $discussion->moodleoverflow);
        if (!$DB->record_exists('moodleoverflow_subscriptions', $params)) {

            // Check if the user isn't subscribed to the moodleoverflow.
            if ($subscription AND $subscription->preference != self::MOODLEOVERFLOW_DISCUSSION_UNSUBSCRIBED) {

                // Delete the discussion subscription.
                $DB->delete_records('moodleoverflow_discuss_subs', array('id' => $subscription->id));
                unset(self::$discussioncache[$userid][$discussion->moodleoverflow][$discussion->id]);

            } else {
                // Else the user is not subscribed to the moodleoverflow.

                // Nothing has to be done here.
                return false;
            }

        } else {
            // There is an subscription record for this moodleoverflow.

            // Check whether an subscription record for this discussion.
            if ($subscription) {

                // Update the existing record.
                $subscription->preference = self::MOODLEOVERFLOW_DISCUSSION_UNSUBSCRIBED;
                $DB->update_record('moodleoverflow_discuss_subs', $subscription);

            } else {
                // There is no record.

                // Create a new discussion subscription record.
                $subscription                 = new \stdClass();
                $subscription->userid         = $userid;
                $subscription->moodleoverflow = $discussion->moodleoverflow;
                $subscription->discussion     = $discussion->id;
                $subscription->preference     = self::MOODLEOVERFLOW_DISCUSSION_UNSUBSCRIBED;

                // Insert the discussion subscription record into the database.
                $subscription->id = $DB->insert_record('moodleoverflow_discuss_subs', $subscription);
            }

            // Update the cache.
            self::$discussioncache[$userid][$discussion->moodleoverflow][$discussion->id] = $subscription->preference;
        }

        // Trigger an discussion subscription deletetion event.
        $params = array(
            'context'       => $context,
            'objectid'      => $subscription->id,
            'relateduserid' => $userid,
            'other'         => array('moodleoverflowid' => $discussion->moodleoverflow, 'discussion' => $discussion->id),
        );
        $event  = event\discussion_subscription_deleted::create($params);
        $event->trigger();

        // The user was successfully unsubscribed from the discussion.
        return true;
    }

    /**
     * Generate and return the subscribe or unsubscribe link for a moodleoverflow.
     *
     * @param object $moodleoverflow the moodleoverflow. Fields used are $moodleoverflow->id and $moodleoverflow->forcesubscribe.
     * @param object $context        the context object for this moodleoverflow.
     * @param array  $messages       text used for the link in its various states
     *                               (subscribed, unsubscribed, forcesubscribed or cantsubscribe).
     *                               Any strings not passed in are taken from the $defaultmessages array
     *                               at the top of the function.
     *
     * @return string
     */
    public static function moodleoverflow_get_subscribe_link($moodleoverflow, $context, $messages = array()) {
        global $USER, $OUTPUT;

        // Define strings.
        $defaultmessages = array(
            'subscribed'      => get_string('unsubscribe', 'moodleoverflow'),
            'unsubscribed'    => get_string('subscribe', 'moodleoverflow'),
            'forcesubscribed' => get_string('everyoneissubscribed', 'moodleoverflow'),
            'cantsubscribe'   => get_string('disallowsubscribe', 'moodleoverflow'),
        );

        // Combine strings the submitted messages.
        $messages = $messages + $defaultmessages;

        // Check whether the user is forced to be subscribed to the moodleoverflow.
        $isforced   = self::is_forcesubscribed($moodleoverflow);
        $isdisabled = self::subscription_disabled($moodleoverflow);

        // Return messages depending on the subscription state.
        if ($isforced) {
            return $messages['forcesubscribed'];
        } else if ($isdisabled AND !has_capability('mod/moodleoverflow:managesubscriptions', $context)) {
            return $messages['cantsubscribe'];
        } else {

            // The user needs to be enrolled.
            if (!is_enrolled($context, $USER, '', true)) {
                return '';
            }

            // Check whether the user is subscribed.
            $issubscribed = self::is_subscribed($USER->id, $moodleoverflow);

            // Define the text of the link depending on the subscription state.
            if ($issubscribed) {
                $linktext  = $messages['subscribed'];
                $linktitle = get_string('subscribestop', 'moodleoverflow');
            } else {
                $linktext  = $messages['unsubscribed'];
                $linktitle = get_string('subscribestart', 'moodleoverflow');
            }

            // Create an options array.
            $options                = array();
            $options['id']          = $moodleoverflow->id;
            $options['sesskey']     = sesskey();
            $options['returnurl']   = 0;
            $options['backtoindex'] = 1;

            // Return the link to subscribe the user.
            $url = new \moodle_url('/mod/moodleoverflow/subscribe.php', $options);

            return $OUTPUT->single_button($url, $linktext, 'get', array('title' => $linktitle));
        }
    }

    /**
     * Given a new post, subscribes the user to the thread the post was posted in.
     *
     * @param object $fromform       The submitted form
     * @param \stdClass       $moodleoverflow The moodleoverflow record
     * @param \stdClass       $discussion     The discussion record
     * @param \context_course $modulecontext  The context of the module
     *
     * @return bool
     */
    public static function moodleoverflow_post_subscription($fromform, $moodleoverflow, $discussion, $modulecontext) {
        global $USER;

        // Check for some basic information.
        $force    = self::is_forcesubscribed($moodleoverflow);
        $disabled = self::subscription_disabled($moodleoverflow);

        // Do not continue if the user is already forced to be subscribed.
        if ($force) {
            return false;
        }

        // Do not continue if subscriptions are disabled.
        if ($disabled) {

            // If the user is subscribed, unsubscribe him.
            $subscribed    = self::is_subscribed($USER->id, $moodleoverflow);
            $coursecontext = \context_course::instance($moodleoverflow->course);
            $canmanage     = has_capability('moodle/course:manageactivities', $coursecontext, $USER->id);
            if ($subscribed AND !$canmanage) {
                self::unsubscribe_user($USER->id, $moodleoverflow, $modulecontext);
            }

            // Do not continue.
            return false;
        }

        // Subscribe the user to the discussion.
        self::subscribe_user_to_discussion($USER->id, $discussion, $modulecontext);

        return true;
    }

    /**
     * Return the markup for the discussion subscription toggling icon.
     *
     * @param object $moodleoverflow The forum moodleoverflow.
     * @param int    $discussionid   The discussion to create an icon for.
     *
     * @return string The generated markup.
     */
    public static function get_discussion_subscription_icon($moodleoverflow, $discussionid) {
        global $OUTPUT, $PAGE, $USER;

        // Set the url to return to.
        $returnurl = $PAGE->url->out();

        // Check if the discussion is subscrived.
        $status = self::is_subscribed($USER->id, $moodleoverflow, $discussionid);

        // Create a link to subscribe or unsubscribe to the discussion.
        $array            = array(
            'sesskey'   => sesskey(),
            'id'        => $moodleoverflow->id,
            'd'         => $discussionid,
            'returnurl' => $returnurl,
        );
        $subscriptionlink = new \moodle_url('/mod/moodleoverflow/subscribe.php', $array);

        // Create an icon to unsubscribe.
        if ($status) {

            // Create the icon.
            $string = get_string('clicktounsubscribe', 'moodleoverflow');
            $output = $OUTPUT->pix_icon('subscribed', $string, 'mod_moodleoverflow');

            // Return the link.
            $array = array(
                'title'                 => get_string('clicktounsubscribe', 'moodleoverflow'),
                'class'                 => 'discussiontoggle iconsmall',
                'data-moodleoverflowid' => $moodleoverflow->id,
                'data-discussionid'     => $discussionid,
                'data-includetext'      => false,
            );

            return \html_writer::link($subscriptionlink, $output, $array);
        }

        // Create an icon to subscribe.
        $string = get_string('clicktosubscribe', 'moodleoverflow');
        $output = $OUTPUT->pix_icon('unsubscribed', $string, 'mod_moodleoverflow');

        // Return the link.
        $array = array(
            'title'                 => get_string('clicktosubscribe', 'moodleoverflow'),
            'class'                 => 'discussiontoggle iconsmall',
            'data-moodleoverflowid' => $moodleoverflow->id,
            'data-discussionid'     => $discussionid,
            'data-includetext'      => false,
        );

        return \html_writer::link($subscriptionlink, $output, $array);
    }
}
