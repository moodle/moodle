<?php
// This file is part of a plugin for Moodle - http://moodle.org/
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
 * Moodleoverflow readtracking manager.
 *
 * @package   mod_moodleoverflow
 * @copyright 2017 Kennet Winter <k_wint10@uni-muenster.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_moodleoverflow;

use moodle_exception;

/**
 * Static methods for managing the tracking of read posts and discussions.
 *
 * @package   mod_moodleoverflow
 * @copyright 2017 Kennet Winter <k_wint10@uni-muenster.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class readtracking {

    /**
     * Determine if a user can track moodleoverflows and optionally a particular moodleoverflow instance.
     * Checks the site settings and the moodleoverflow settings (if requested).
     *
     * @param object $moodleoverflow
     *
     * @return boolean
     * */
    public static function moodleoverflow_can_track_moodleoverflows($moodleoverflow = null) {
        global $USER;

        // Check if readtracking is disabled for the module.
        if (!get_config('moodleoverflow', 'trackreadposts')) {
            return false;
        }

        // Guests are not allowed to track moodleoverflows.
        if (isguestuser($USER) OR empty($USER->id)) {
            return false;
        }

        // If no specific moodleoverflow is submitted, check the modules basic settings.
        if (is_null($moodleoverflow)) {
            if (get_config('moodleoverflow', 'allowforcedreadtracking')) {
                // Since we can force tracking, assume yes without a specific forum.
                return true;
            } else {
                // User tracks moodleoverflows by default.
                return true;
            }
        }
        // Check the settings of the moodleoverflow instance.
        $allowed = ($moodleoverflow->trackingtype == MOODLEOVERFLOW_TRACKING_OPTIONAL);
        $forced  = ($moodleoverflow->trackingtype == MOODLEOVERFLOW_TRACKING_FORCED);

        return ($allowed || $forced);
    }

    /**
     * Tells whether a specific moodleoverflow is tracked by the user.
     *
     * @param object      $moodleoverflow
     * @param object|null $user
     *
     * @return bool
     */
    public static function moodleoverflow_is_tracked($moodleoverflow, $user = null) {
        global $USER, $DB;

        // Get the user.
        if (is_null($user)) {
            $user = $USER;
        }

        // Guests cannot track a moodleoverflow.
        if (isguestuser($USER) OR empty($USER->id)) {
            return false;
        }

        // Check if the moodleoverflow can be generally tracked.
        if (!self::moodleoverflow_can_track_moodleoverflows($moodleoverflow)) {
            return false;
        }

        // Check the settings of the moodleoverflow instance.
        $allowed = ($moodleoverflow->trackingtype == MOODLEOVERFLOW_TRACKING_OPTIONAL);
        $forced  = ($moodleoverflow->trackingtype == MOODLEOVERFLOW_TRACKING_FORCED);

        // Check the preferences of the user.
        $userpreference = $DB->get_record('moodleoverflow_tracking',
            array('userid' => $user->id, 'moodleoverflowid' => $moodleoverflow->id));

        // Return the boolean.
        if (get_config('moodleoverflow', 'allowforcedreadtracking')) {
            return ($forced || ($allowed && $userpreference === false));
        } else {
            return (($allowed || $forced) && $userpreference === false);
        }
    }

    /**
     * Marks a specific moodleoverflow instance as read by a specific user.
     *
     * @param object $cm
     * @param null   $userid
     */
    public static function moodleoverflow_mark_moodleoverflow_read($cm, $userid = null) {
        global $USER;

        // If no user is submitted, use the current one.
        if (!isset($userid)) {
            $userid = $USER->id;
        }

        // Get all the discussions with unread messages in this moodleoverflow instance.
        $discussions = moodleoverflow_get_discussions_unread($cm);

        // Iterate through all of this discussions.
        foreach ($discussions as $discussionid => $amount) {

            // Mark the discussion as read.
            if (!self::moodleoverflow_mark_discussion_read($discussionid, $userid)) {
                throw new moodle_exception('markreadfailed', 'moodleoverflow');

                return false;
            }
        }

        return true;
    }

    /**
     * Marks a specific discussion as read by a specific user.
     *
     * @param int  $discussionid
     * @param null $userid
     */
    public static function moodleoverflow_mark_discussion_read($discussionid, $userid = null) {
        global $USER;

        // Get all posts.
        $posts = moodleoverflow_get_all_discussion_posts($discussionid, true);

        // If no user is submitted, use the current one.
        if (!isset($userid)) {
            $userid = $USER->id;
        }

        // Iterate through all posts of the discussion.
        foreach ($posts as $post) {

            // Ignore already read posts.
            if (!is_null($post->postread)) {
                continue;
            }

            // Mark the post as read.
            if (!self::moodleoverflow_mark_post_read($userid, $post)) {
                throw new moodle_exception('markreadfailed', 'moodleoverflow');

                return false;
            }
        }

        // The discussion has been marked as read.
        return true;
    }

    /**
     * Marks a specific post as read by a specific user.
     *
     * @param int    $userid
     * @param object $post
     *
     * @return bool
     */
    public static function moodleoverflow_mark_post_read($userid, $post) {

        // If the post is older than the limit.
        if (self::moodleoverflow_is_old_post($post)) {
            return true;
        }

        // Create a new read record.
        return self::moodleoverflow_add_read_record($userid, $post->id);
    }

    /**
     * Checks if a post is older than the limit.
     *
     * @param object $post
     *
     * @return bool
     */
    public static function moodleoverflow_is_old_post($post) {

        // Transform objects into arrays.
        $post = (array) $post;

        // Get the current time.
        $currenttimestamp = time();

        // Calculate the time, where older posts are considered read.
        $oldposttimestamp = $currenttimestamp - (get_config('moodleoverflow', 'oldpostdays') * 24 * 3600);

        // Return if the post is newer than that time.
        return ($post['modified'] < $oldposttimestamp);
    }

    /**
     * Mark a post as read by a user.
     *
     * @param int $userid
     * @param int $postid
     *
     * @return bool
     */
    public static function moodleoverflow_add_read_record($userid, $postid) {
        global $DB;

        // Get the current time and the cutoffdate.
        $now        = time();
        $cutoffdate = $now - (get_config('moodleoverflow', 'oldpostdays') * 24 * 3600);

        // Check for read records for this user an this post.
        $oldrecord = $DB->get_record('moodleoverflow_read', array('postid' => $postid, 'userid' => $userid));
        if (!$oldrecord) {

            // If there are no old records, create a new one.
            $sql = "INSERT INTO {moodleoverflow_read} (userid, postid, discussionid, moodleoverflowid, firstread, lastread)
                 SELECT ?, p.id, p.discussion, d.moodleoverflow, ?, ?
                   FROM {moodleoverflow_posts} p
                        JOIN {moodleoverflow_discussions} d ON d.id = p.discussion
                  WHERE p.id = ? AND p.modified >= ?";

            return $DB->execute($sql, array($userid, $now, $now, $postid, $cutoffdate));
        }

        // Else update the existing one.
        $sql = "UPDATE {moodleoverflow_read}
                   SET lastread = ?
                 WHERE userid = ? AND postid = ?";

        return $DB->execute($sql, array($now, $userid, $userid));
    }

    /**
     * Deletes read record for the specified index.
     * At least one parameter must be specified.
     *
     * @param int $userid
     * @param int $postid
     * @param int $discussionid
     * @param int $overflowid
     *
     * @return bool
     */
    public static function moodleoverflow_delete_read_records($userid = -1, $postid = -1, $discussionid = -1, $overflowid = -1) {
        global $DB;

        // Initiate variables.
        $params = array();
        $select = '';

        // Create the sql-Statement depending on the submitted parameters.
        if ($userid > -1) {
            if ($select != '') {
                $select .= ' AND ';
            }
            $select   .= 'userid = ?';
            $params[] = $userid;
        }
        if ($postid > -1) {
            if ($select != '') {
                $select .= ' AND ';
            }
            $select   .= 'postid = ?';
            $params[] = $postid;
        }
        if ($discussionid > -1) {
            if ($select != '') {
                $select .= ' AND ';
            }
            $select   .= 'discussionid = ?';
            $params[] = $discussionid;
        }
        if ($overflowid > -1) {
            if ($select != '') {
                $select .= ' AND ';
            }
            $select   .= 'moodleoverflowid = ?';
            $params[] = $overflowid;
        }

        // Check if at least one parameter was specified.
        if ($select == '') {
            return false;
        } else {
            return $DB->delete_records_select('moodleoverflow_read', $select, $params);
        }
    }

    /**
     * Deletes all read records that are related to posts that are older than the cutoffdate.
     * This function is only called by the modules cronjob.
     */
    public static function moodleoverflow_clean_read_records() {
        global $DB;

        // Stop if there cannot be old posts.
        if (!get_config('moodleoverflow', 'oldpostdays')) {
            return;
        }

        // Find the timestamp for records older than allowed.
        $cutoffdate = time() - (get_config('moodleoverflow', 'oldpostdays') * 24 * 60 * 60);

        // Find the timestamp of the oldest read record.
        // This will speedup the delete query.
        $sql = "SELECT MIN(p.modified) AS first
                FROM {moodleoverflow_posts} p
                JOIN {moodleoverflow_read} r ON r.postid = p.id";

        // If there is no old read record, end this method.
        if (!$first = $DB->get_field_sql($sql)) {
            return;
        }

        // Delete the old read tracking information between that timestamp and the cutoffdate.
        $sql = "DELETE
                FROM {moodleoverflow_read}
                WHERE postid IN (SELECT p.id
                                 FROM {moodleoverflow_posts} p
                                 WHERE p.modified >= ? AND p.modified < ?)";
        $DB->execute($sql, array($first, $cutoffdate));
    }

    /**
     * Stop to track a moodleoverflow instance.
     *
     * @param int $moodleoverflowid The moodleoverflow ID
     * @param int $userid           The user ID
     *
     * @return bool Whether the deletion was successful
     */
    public static function moodleoverflow_stop_tracking($moodleoverflowid, $userid = null) {
        global $USER, $DB;

        // Set the user.
        if (is_null($userid)) {
            $userid = $USER->id;
        }

        // Check if the user already stopped to track the moodleoverflow.
        $params    = array('userid' => $userid, 'moodleoverflowid' => $moodleoverflowid);
        $isstopped = $DB->record_exists('moodleoverflow_tracking', $params);

        // Stop tracking the moodleoverflow if not already stopped.
        if (!$isstopped) {

            // Create the tracking object.
            $tracking                   = new \stdClass();
            $tracking->userid           = $userid;
            $tracking->moodleoverflowid = $moodleoverflowid;

            // Insert into the database.
            $DB->insert_record('moodleoverflow_tracking', $params);
        }

        // Delete all connected read records.
        $deletion = self::moodleoverflow_delete_read_records($userid, -1, -1, $moodleoverflowid);

        // Return whether the deletion was successful.
        return $deletion;
    }

    /**
     * Start to track a moodleoverflow instance.
     *
     * @param int $moodleoverflowid The moodleoverflow ID
     * @param int $userid           The user ID
     *
     * @return bool Whether the deletion was successful
     */
    public static function moodleoverflow_start_tracking($moodleoverflowid, $userid = null) {
        global $USER, $DB;

        // Get the current user.
        if (is_null($userid)) {
            $userid = $USER->id;
        }

        // Delete the tracking setting of this user for this moodleoverflow.
        return $DB->delete_records('moodleoverflow_tracking', array('userid' => $userid, 'moodleoverflowid' => $moodleoverflowid));
    }

    /**
     * Get a list of forums not tracked by the user.
     *
     * @param int $userid   The user ID
     * @param int $courseid The course ID
     *
     * @return array Array with untracked moodleoverflows
     */
    public static function get_untracked_moodleoverflows($userid, $courseid) {
        global $DB;

        // Check whether readtracking may be forced.
        if (get_config('moodleoverflow', 'allowforcedreadtracking')) {

            // Create a part of a sql-statement.
            $trackingsql = "AND (m.trackingtype = " . MOODLEOVERFLOW_TRACKING_OFF . "
                            OR (m.trackingtype = " . MOODLEOVERFLOW_TRACKING_OPTIONAL . " AND mt.id IS NOT NULL))";
        } else {
            // Readtracking may be forced.

            // Create another sql-statement.
            $trackingsql = "AND (m.trackingtype = " . MOODLEOVERFLOW_TRACKING_OFF .
                " OR ((m.trackingtype = " . MOODLEOVERFLOW_TRACKING_OPTIONAL .
                " OR m.trackingtype = " . MOODLEOVERFLOW_TRACKING_FORCED . ") AND mt.id IS NOT NULL))";
        }

        // Create the sql-queryx.
        $sql = "SELECT m.id
                  FROM {moodleoverflow} m
             LEFT JOIN {moodleoverflow_tracking} mt ON (mt.moodleoverflowid = m.id AND mt.userid = ?)
                 WHERE m.course = ? $trackingsql";

        // Get all untracked moodleoverflows from the database.
        $moodleoverflows = $DB->get_records_sql($sql, array($userid, $courseid, $userid));

        // Check whether there are no untracked moodleoverflows.
        if (!$moodleoverflows) {
            return array();
        }

        // Loop through all moodleoverflows.
        foreach ($moodleoverflows as $moodleoverflow) {
            $moodleoverflows[$moodleoverflow->id] = $moodleoverflow;
        }

        // Return all untracked moodleoverflows.
        return $moodleoverflows;
    }

    /**
     * Get number of unread posts in a moodleoverflow instance.
     *
     * @param object    $cm
     * @param \stdClass $course The course the moodleoverflow is in
     *
     * @return int|mixed
     */
    public static function moodleoverflow_count_unread_posts_moodleoverflow($cm, $course) {
        global $CFG, $DB, $USER;

        // Create a cache.
        static $readcache = array();

        // Get the moodleoverflow ids.
        $moodleoverflowid = $cm->instance;

        // Check whether the cache is already set.
        if (!isset($readcache[$course->id])) {

            // Create a cache for the course.
            $readcache[$course->id] = array();

            // Count the unread posts in the course.
            $counts = self::moodleoverflow_count_unread_posts_course($USER->id, $course->id);
            if ($counts) {

                // Loop through all unread posts.
                foreach ($counts as $count) {
                    $readcache[$course->id][$count->id] = $count->unread;
                }
            }
        }

        // Check whether there are no unread post for this moodleoverflow.
        if (empty($readcache[$course->id][$moodleoverflowid])) {
            return 0;
        }

        // Require the course library.
        require_once($CFG->dirroot . '/course/lib.php');

        // Get the current timestamp and the cutoffdate.
        $now        = round(time(), -2);
        $cutoffdate = $now - (get_config('moodleoverflow', 'oldpostdays') * 24 * 60 * 60);

        // Define a sql-query.
        $params = array($USER->id, $moodleoverflowid, $cutoffdate);
        $sql    = "SELECT COUNT(p.id)
                  FROM {moodleoverflow_posts} p
                  JOIN {moodleoverflow_discussions} d ON p.discussion = d.id
             LEFT JOIN {moodleoverflow_read} r ON (r.postid = p.id AND r.userid = ?)
                 WHERE d.moodleoverflow = ? AND p.modified >= ? AND r.id IS NULL";

        // Return the number of unread posts per moodleoverflow.
        return $DB->get_field_sql($sql, $params);
    }

    /**
     * Get an array of unread posts within a course.
     *
     * @param int $userid   The user ID
     * @param int $courseid The course ID
     *
     * @return array Array of unread posts within a course
     */
    public static function moodleoverflow_count_unread_posts_course($userid, $courseid) {
        global $DB;

        // Get the current timestamp and calculate the cutoffdate.
        $now        = round(time(), -2);
        $cutoffdate = $now - (get_config('moodleoverflow', 'oldpostdays') * 24 * 60 * 60);

        // Set parameters for the sql-query.
        $params = array($userid, $userid, $courseid, $cutoffdate, $userid);

        // Check if forced readtracking is allowed.
        if (get_config('moodleoverflow', 'allowforcedreadtracking')) {
            $trackingsql = "AND (m.trackingtype = " . MOODLEOVERFLOW_TRACKING_FORCED .
                " OR (m.trackingtype = " . MOODLEOVERFLOW_TRACKING_OPTIONAL . " AND tm.id IS NULL))";
        } else {
            $trackingsql = "AND ((m.trackingtype = " . MOODLEOVERFLOW_TRACKING_OPTIONAL . " OR m.trackingtype = " .
                MOODLEOVERFLOW_TRACKING_FORCED . ") AND tm.id IS NULL)";
        }

        // Define the sql-query.
        $sql = "SELECT m.id, COUNT(p.id) AS unread
                  FROM {moodleoverflow_posts} p
                  JOIN {moodleoverflow_discussions} d ON d.id = p.discussion
                  JOIN {moodleoverflow} m ON m.id = d.moodleoverflow
                  JOIN {course} c ON c.id = m.course
             LEFT JOIN {moodleoverflow_read} r ON (r.postid = p.id AND r.userid = ?)
             LEFT JOIN {moodleoverflow_tracking} tm ON (tm.userid = ? AND tm.moodleoverflowid = m.id)
                 WHERE m.course = ? AND p.modified >= ? AND r.id IS NULL $trackingsql
              GROUP BY m.id";

        // Get the amount of unread post within a course.
        $return = $DB->get_records_sql($sql, $params);
        if ($return) {
            return $return;
        }

        // Else return nothing.
        return array();
    }
}
