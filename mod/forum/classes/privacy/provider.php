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
 * Privacy Subsystem implementation for mod_forum.
 *
 * @package    mod_forum
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\privacy;

use core_grades\component_gradeitem as gradeitem;
use \core_privacy\local\request\userlist;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\approved_userlist;
use \core_privacy\local\request\deletion_criteria;
use \core_privacy\local\request\writer;
use \core_privacy\local\request\helper as request_helper;
use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\transform;
use tool_dataprivacy\context_instance;

defined('MOODLE_INTERNAL') || die();

/**
 * Implementation of the privacy subsystem plugin provider for the forum activity module.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    // This plugin has data.
    \core_privacy\local\metadata\provider,

    // This plugin currently implements the original plugin\provider interface.
    \core_privacy\local\request\plugin\provider,

    // This plugin is capable of determining which users have data within it.
    \core_privacy\local\request\core_userlist_provider,

    // This plugin has some sitewide user preferences to export.
    \core_privacy\local\request\user_preference_provider
{

    use subcontext_info;

    /**
     * Returns meta data about this system.
     *
     * @param   collection     $items The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $items) : collection {
        // The 'forum' table does not store any specific user data.
        $items->add_database_table('forum_digests', [
            'forum' => 'privacy:metadata:forum_digests:forum',
            'userid' => 'privacy:metadata:forum_digests:userid',
            'maildigest' => 'privacy:metadata:forum_digests:maildigest',
        ], 'privacy:metadata:forum_digests');

        // The 'forum_discussions' table stores the metadata about each forum discussion.
        $items->add_database_table('forum_discussions', [
            'name' => 'privacy:metadata:forum_discussions:name',
            'userid' => 'privacy:metadata:forum_discussions:userid',
            'assessed' => 'privacy:metadata:forum_discussions:assessed',
            'timemodified' => 'privacy:metadata:forum_discussions:timemodified',
            'usermodified' => 'privacy:metadata:forum_discussions:usermodified',
        ], 'privacy:metadata:forum_discussions');

        // The 'forum_discussion_subs' table stores information about which discussions a user is subscribed to.
        $items->add_database_table('forum_discussion_subs', [
            'discussionid' => 'privacy:metadata:forum_discussion_subs:discussionid',
            'preference' => 'privacy:metadata:forum_discussion_subs:preference',
            'userid' => 'privacy:metadata:forum_discussion_subs:userid',
        ], 'privacy:metadata:forum_discussion_subs');

        // The 'forum_posts' table stores the metadata about each forum discussion.
        $items->add_database_table('forum_posts', [
            'discussion' => 'privacy:metadata:forum_posts:discussion',
            'parent' => 'privacy:metadata:forum_posts:parent',
            'created' => 'privacy:metadata:forum_posts:created',
            'modified' => 'privacy:metadata:forum_posts:modified',
            'subject' => 'privacy:metadata:forum_posts:subject',
            'message' => 'privacy:metadata:forum_posts:message',
            'userid' => 'privacy:metadata:forum_posts:userid',
            'privatereplyto' => 'privacy:metadata:forum_posts:privatereplyto',
        ], 'privacy:metadata:forum_posts');

        // The 'forum_queue' table contains user data, but it is only a temporary cache of other data.
        // We should not need to export it as it does not allow profiling of a user.

        // The 'forum_read' table stores data about which forum posts have been read by each user.
        $items->add_database_table('forum_read', [
            'userid' => 'privacy:metadata:forum_read:userid',
            'discussionid' => 'privacy:metadata:forum_read:discussionid',
            'postid' => 'privacy:metadata:forum_read:postid',
            'firstread' => 'privacy:metadata:forum_read:firstread',
            'lastread' => 'privacy:metadata:forum_read:lastread',
        ], 'privacy:metadata:forum_read');

        // The 'forum_subscriptions' table stores information about which forums a user is subscribed to.
        $items->add_database_table('forum_subscriptions', [
            'userid' => 'privacy:metadata:forum_subscriptions:userid',
            'forum' => 'privacy:metadata:forum_subscriptions:forum',
        ], 'privacy:metadata:forum_subscriptions');

        // The 'forum_subscriptions' table stores information about which forums a user is subscribed to.
        $items->add_database_table('forum_track_prefs', [
            'userid' => 'privacy:metadata:forum_track_prefs:userid',
            'forumid' => 'privacy:metadata:forum_track_prefs:forumid',
        ], 'privacy:metadata:forum_track_prefs');

        // The 'forum_queue' table stores temporary data that is not exported/deleted.
        $items->add_database_table('forum_queue', [
            'userid' => 'privacy:metadata:forum_queue:userid',
            'discussionid' => 'privacy:metadata:forum_queue:discussionid',
            'postid' => 'privacy:metadata:forum_queue:postid',
            'timemodified' => 'privacy:metadata:forum_queue:timemodified'
        ], 'privacy:metadata:forum_queue');

        // The 'forum_grades' table stores grade data.
        $items->add_database_table('forum_grades', [
            'userid' => 'privacy:metadata:forum_grades:userid',
            'forum' => 'privacy:metadata:forum_grades:forum',
            'grade' => 'privacy:metadata:forum_grades:grade',
        ], 'privacy:metadata:forum_grades');

        // Forum posts can be tagged and rated.
        $items->link_subsystem('core_tag', 'privacy:metadata:core_tag');
        $items->link_subsystem('core_rating', 'privacy:metadata:core_rating');

        // There are several user preferences.
        $items->add_user_preference('maildigest', 'privacy:metadata:preference:maildigest');
        $items->add_user_preference('autosubscribe', 'privacy:metadata:preference:autosubscribe');
        $items->add_user_preference('trackforums', 'privacy:metadata:preference:trackforums');
        $items->add_user_preference('markasreadonnotification', 'privacy:metadata:preference:markasreadonnotification');
        $items->add_user_preference('forum_discussionlistsortorder',
            'privacy:metadata:preference:forum_discussionlistsortorder');

        return $items;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * In the case of forum, that is any forum where the user has made any post, rated any content, or has any preferences.
     *
     * @param   int         $userid     The user to search.
     * @return  contextlist $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : \core_privacy\local\request\contextlist {
        $contextlist = new \core_privacy\local\request\contextlist();

        $params = [
            'modname'       => 'forum',
            'contextlevel'  => CONTEXT_MODULE,
            'userid'        => $userid,
        ];

        // Discussion creators.
        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {forum} f ON f.id = cm.instance
                  JOIN {forum_discussions} d ON d.forum = f.id
                 WHERE d.userid = :userid
        ";
        $contextlist->add_from_sql($sql, $params);

        // Post authors.
        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {forum} f ON f.id = cm.instance
                  JOIN {forum_discussions} d ON d.forum = f.id
                  JOIN {forum_posts} p ON p.discussion = d.id
                 WHERE p.userid = :userid
        ";
        $contextlist->add_from_sql($sql, $params);

        // Forum digest records.
        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {forum} f ON f.id = cm.instance
                  JOIN {forum_digests} dig ON dig.forum = f.id
                 WHERE dig.userid = :userid
        ";
        $contextlist->add_from_sql($sql, $params);

        // Forum subscriptions.
        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {forum} f ON f.id = cm.instance
                  JOIN {forum_subscriptions} sub ON sub.forum = f.id
                 WHERE sub.userid = :userid
        ";
        $contextlist->add_from_sql($sql, $params);

        // Discussion subscriptions.
        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {forum} f ON f.id = cm.instance
                  JOIN {forum_discussion_subs} dsub ON dsub.forum = f.id
                 WHERE dsub.userid = :userid
        ";
        $contextlist->add_from_sql($sql, $params);

        // Discussion tracking preferences.
        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {forum} f ON f.id = cm.instance
                  JOIN {forum_track_prefs} pref ON pref.forumid = f.id
                 WHERE pref.userid = :userid
        ";
        $contextlist->add_from_sql($sql, $params);

        // Discussion read records.
        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {forum} f ON f.id = cm.instance
                  JOIN {forum_read} hasread ON hasread.forumid = f.id
                 WHERE hasread.userid = :userid
        ";
        $contextlist->add_from_sql($sql, $params);

        // Rating authors.
        $ratingsql = \core_rating\privacy\provider::get_sql_join('rat', 'mod_forum', 'post', 'p.id', $userid, true);
        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {forum} f ON f.id = cm.instance
                  JOIN {forum_discussions} d ON d.forum = f.id
                  JOIN {forum_posts} p ON p.discussion = d.id
                  {$ratingsql->join}
                 WHERE {$ratingsql->userwhere}
        ";
        $params += $ratingsql->params;
        $contextlist->add_from_sql($sql, $params);

        // Forum grades.
        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {forum} f ON f.id = cm.instance
                  JOIN {forum_grades} fg ON fg.forum = f.id
                 WHERE fg.userid = :userid
        ";
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users within a specific context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!is_a($context, \context_module::class)) {
            return;
        }

        $params = [
            'instanceid'    => $context->instanceid,
            'modulename'    => 'forum',
        ];

        // Discussion authors.
        $sql = "SELECT d.userid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {forum} f ON f.id = cm.instance
                  JOIN {forum_discussions} d ON d.forum = f.id
                 WHERE cm.id = :instanceid";
        $userlist->add_from_sql('userid', $sql, $params);

        // Forum authors.
        $sql = "SELECT p.userid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {forum} f ON f.id = cm.instance
                  JOIN {forum_discussions} d ON d.forum = f.id
                  JOIN {forum_posts} p ON d.id = p.discussion
                 WHERE cm.id = :instanceid";
        $userlist->add_from_sql('userid', $sql, $params);

        // Forum post ratings.
        $sql = "SELECT p.id
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {forum} f ON f.id = cm.instance
                  JOIN {forum_discussions} d ON d.forum = f.id
                  JOIN {forum_posts} p ON d.id = p.discussion
                 WHERE cm.id = :instanceid";
        \core_rating\privacy\provider::get_users_in_context_from_sql($userlist, 'rat', 'mod_forum', 'post', $sql, $params);

        // Forum Digest settings.
        $sql = "SELECT dig.userid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {forum} f ON f.id = cm.instance
                  JOIN {forum_digests} dig ON dig.forum = f.id
                 WHERE cm.id = :instanceid";
        $userlist->add_from_sql('userid', $sql, $params);

        // Forum Subscriptions.
        $sql = "SELECT sub.userid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {forum} f ON f.id = cm.instance
                  JOIN {forum_subscriptions} sub ON sub.forum = f.id
                 WHERE cm.id = :instanceid";
        $userlist->add_from_sql('userid', $sql, $params);

        // Discussion subscriptions.
        $sql = "SELECT dsub.userid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {forum} f ON f.id = cm.instance
                  JOIN {forum_discussion_subs} dsub ON dsub.forum = f.id
                 WHERE cm.id = :instanceid";
        $userlist->add_from_sql('userid', $sql, $params);

        // Read Posts.
        $sql = "SELECT hasread.userid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {forum} f ON f.id = cm.instance
                  JOIN {forum_read} hasread ON hasread.forumid = f.id
                 WHERE cm.id = :instanceid";
        $userlist->add_from_sql('userid', $sql, $params);

        // Tracking Preferences.
        $sql = "SELECT pref.userid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {forum} f ON f.id = cm.instance
                  JOIN {forum_track_prefs} pref ON pref.forumid = f.id
                 WHERE cm.id = :instanceid";
        $userlist->add_from_sql('userid', $sql, $params);

        // Forum grades.
        $sql = "SELECT fg.userid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {forum} f ON f.id = cm.instance
                  JOIN {forum_grades} fg ON fg.forum = f.id
                 WHERE cm.id = :instanceid";
        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Store all user preferences for the plugin.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {
        $user = \core_user::get_user($userid);

        switch ($user->maildigest) {
            case 1:
                $digestdescription = get_string('emaildigestcomplete');
                break;
            case 2:
                $digestdescription = get_string('emaildigestsubjects');
                break;
            case 0:
            default:
                $digestdescription = get_string('emaildigestoff');
                break;
        }
        writer::export_user_preference('mod_forum', 'maildigest', $user->maildigest, $digestdescription);

        switch ($user->autosubscribe) {
            case 0:
                $subscribedescription = get_string('autosubscribeno');
                break;
            case 1:
            default:
                $subscribedescription = get_string('autosubscribeyes');
                break;
        }
        writer::export_user_preference('mod_forum', 'autosubscribe', $user->autosubscribe, $subscribedescription);

        switch ($user->trackforums) {
            case 0:
                $trackforumdescription = get_string('trackforumsno');
                break;
            case 1:
            default:
                $trackforumdescription = get_string('trackforumsyes');
                break;
        }
        writer::export_user_preference('mod_forum', 'trackforums', $user->trackforums, $trackforumdescription);

        $markasreadonnotification = get_user_preferences('markasreadonnotification', null, $user->id);
        if (null !== $markasreadonnotification) {
            switch ($markasreadonnotification) {
                case 0:
                    $markasreadonnotificationdescription = get_string('markasreadonnotificationno', 'mod_forum');
                    break;
                case 1:
                default:
                    $markasreadonnotificationdescription = get_string('markasreadonnotificationyes', 'mod_forum');
                    break;
            }
            writer::export_user_preference('mod_forum', 'markasreadonnotification', $markasreadonnotification,
                    $markasreadonnotificationdescription);
        }

        $vaultfactory = \mod_forum\local\container::get_vault_factory();
        $discussionlistvault = $vaultfactory->get_discussions_in_forum_vault();
        $discussionlistsortorder = get_user_preferences('forum_discussionlistsortorder',
            $discussionlistvault::SORTORDER_LASTPOST_DESC);
        switch ($discussionlistsortorder) {
            case $discussionlistvault::SORTORDER_LASTPOST_DESC:
                $discussionlistsortorderdescription = get_string('discussionlistsortbylastpostdesc',
                    'mod_forum');
                break;
            case $discussionlistvault::SORTORDER_LASTPOST_ASC:
                $discussionlistsortorderdescription = get_string('discussionlistsortbylastpostasc',
                    'mod_forum');
                break;
            case $discussionlistvault::SORTORDER_CREATED_DESC:
                $discussionlistsortorderdescription = get_string('discussionlistsortbycreateddesc',
                    'mod_forum');
                break;
            case $discussionlistvault::SORTORDER_CREATED_ASC:
                $discussionlistsortorderdescription = get_string('discussionlistsortbycreatedasc',
                    'mod_forum');
                break;
            case $discussionlistvault::SORTORDER_REPLIES_DESC:
                $discussionlistsortorderdescription = get_string('discussionlistsortbyrepliesdesc',
                    'mod_forum');
                break;
            case $discussionlistvault::SORTORDER_REPLIES_ASC:
                $discussionlistsortorderdescription = get_string('discussionlistsortbyrepliesasc',
                    'mod_forum');
                break;
        }
        writer::export_user_preference('mod_forum', 'forum_discussionlistsortorder',
            $discussionlistsortorder, $discussionlistsortorderdescription);
    }


    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist)) {
            return;
        }

        $user = $contextlist->get_user();
        $userid = $user->id;

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);
        $params = $contextparams;

        // Digested forums.
        $sql = "SELECT
                    c.id AS contextid,
                    dig.maildigest AS maildigest
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid
                  JOIN {forum} f ON f.id = cm.instance
                  JOIN {forum_digests} dig ON dig.forum = f.id
                 WHERE (
                    dig.userid = :userid AND
                    c.id {$contextsql}
                )
        ";
        $params['userid'] = $userid;
        $digests = $DB->get_records_sql_menu($sql, $params);

        // Forum subscriptions.
        $sql = "SELECT
                    c.id AS contextid,
                    sub.userid AS subscribed
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid
                  JOIN {forum} f ON f.id = cm.instance
                  JOIN {forum_subscriptions} sub ON sub.forum = f.id
                 WHERE (
                    sub.userid = :userid AND
                    c.id {$contextsql}
                )
        ";
        $params['userid'] = $userid;
        $subscriptions = $DB->get_records_sql_menu($sql, $params);

        // Tracked forums.
        $sql = "SELECT
                    c.id AS contextid,
                    pref.userid AS tracked
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid
                  JOIN {forum} f ON f.id = cm.instance
                  JOIN {forum_track_prefs} pref ON pref.forumid = f.id
                 WHERE (
                    pref.userid = :userid AND
                    c.id {$contextsql}
                )
        ";
        $params['userid'] = $userid;
        $tracked = $DB->get_records_sql_menu($sql, $params);

        // Forum grades.
        $sql = "SELECT
                    c.id AS contextid,
                    fg.grade AS grade,
                    f.grade_forum AS gradetype
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid
                  JOIN {forum} f ON f.id = cm.instance
                  JOIN {forum_grades} fg ON fg.forum = f.id
                 WHERE (
                    fg.userid = :userid AND
                    c.id {$contextsql}
                )
        ";
        $params['userid'] = $userid;
        $grades = $DB->get_records_sql_menu($sql, $params);

        $sql = "SELECT
                    c.id AS contextid,
                    f.*,
                    cm.id AS cmid
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid
                  JOIN {forum} f ON f.id = cm.instance
                 WHERE (
                    c.id {$contextsql}
                )
        ";

        $params += $contextparams;

        // Keep a mapping of forumid to contextid.
        $mappings = [];

        $forums = $DB->get_recordset_sql($sql, $params);
        foreach ($forums as $forum) {
            $mappings[$forum->id] = $forum->contextid;

            $context = \context::instance_by_id($mappings[$forum->id]);

            // Store the main forum data.
            $data = request_helper::get_context_data($context, $user);
            writer::with_context($context)
                ->export_data([], $data);
            request_helper::export_context_files($context, $user);

            // Store relevant metadata about this forum instance.
            if (isset($digests[$forum->contextid])) {
                static::export_digest_data($userid, $forum, $digests[$forum->contextid]);
            }
            if (isset($subscriptions[$forum->contextid])) {
                static::export_subscription_data($userid, $forum, $subscriptions[$forum->contextid]);
            }
            if (isset($tracked[$forum->contextid])) {
                static::export_tracking_data($userid, $forum, $tracked[$forum->contextid]);
            }
            if (isset($grades[$forum->contextid])) {
                static::export_grading_data($userid, $forum, $grades[$forum->contextid]);
            }
        }
        $forums->close();

        if (!empty($mappings)) {
            // Store all discussion data for this forum.
            static::export_discussion_data($userid, $mappings);

            // Store all post data for this forum.
            static::export_all_posts($userid, $mappings);
        }
    }

    /**
     * Store all information about all discussions that we have detected this user to have access to.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     * @param   array       $mappings A list of mappings from forumid => contextid.
     * @return  array       Which forums had data written for them.
     */
    protected static function export_discussion_data(int $userid, array $mappings) {
        global $DB;

        // Find all of the discussions, and discussion subscriptions for this forum.
        list($foruminsql, $forumparams) = $DB->get_in_or_equal(array_keys($mappings), SQL_PARAMS_NAMED);
        $sql = "SELECT
                    d.*,
                    g.name as groupname,
                    dsub.preference
                  FROM {forum} f
                  JOIN {forum_discussions} d ON d.forum = f.id
             LEFT JOIN {groups} g ON g.id = d.groupid
             LEFT JOIN {forum_discussion_subs} dsub ON dsub.discussion = d.id AND dsub.userid = :dsubuserid
             LEFT JOIN {forum_posts} p ON p.discussion = d.id
                 WHERE f.id ${foruminsql}
                   AND (
                        d.userid    = :discussionuserid OR
                        p.userid    = :postuserid OR
                        dsub.id IS NOT NULL
                   )
        ";

        $params = [
            'postuserid'        => $userid,
            'discussionuserid'  => $userid,
            'dsubuserid'        => $userid,
        ];
        $params += $forumparams;

        // Keep track of the forums which have data.
        $forumswithdata = [];

        $discussions = $DB->get_recordset_sql($sql, $params);
        foreach ($discussions as $discussion) {
            // No need to take timestart into account as the user has some involvement already.
            // Ignore discussion timeend as it should not block access to user data.
            $forumswithdata[$discussion->forum] = true;
            $context = \context::instance_by_id($mappings[$discussion->forum]);

            // Store related metadata for this discussion.
            static::export_discussion_subscription_data($userid, $context, $discussion);

            $discussiondata = (object) [
                'name' => format_string($discussion->name, true),
                'pinned' => transform::yesno((bool) $discussion->pinned),
                'timemodified' => transform::datetime($discussion->timemodified),
                'usermodified' => transform::datetime($discussion->usermodified),
                'creator_was_you' => transform::yesno($discussion->userid == $userid),
            ];

            // Store the discussion content.
            writer::with_context($context)
                ->export_data(static::get_discussion_area($discussion), $discussiondata);

            // Forum discussions do not have any files associately directly with them.
        }

        $discussions->close();

        return $forumswithdata;
    }

    /**
     * Store all information about all posts that we have detected this user to have access to.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     * @param   array       $mappings A list of mappings from forumid => contextid.
     * @return  array       Which forums had data written for them.
     */
    protected static function export_all_posts(int $userid, array $mappings) {
        global $DB;

        // Find all of the posts, and post subscriptions for this forum.
        list($foruminsql, $forumparams) = $DB->get_in_or_equal(array_keys($mappings), SQL_PARAMS_NAMED);
        $ratingsql = \core_rating\privacy\provider::get_sql_join('rat', 'mod_forum', 'post', 'p.id', $userid);
        $sql = "SELECT
                    p.discussion AS id,
                    f.id AS forumid,
                    d.name,
                    d.groupid
                  FROM {forum} f
                  JOIN {forum_discussions} d ON d.forum = f.id
                  JOIN {forum_posts} p ON p.discussion = d.id
             LEFT JOIN {forum_read} fr ON fr.postid = p.id AND fr.userid = :readuserid
            {$ratingsql->join}
                 WHERE f.id ${foruminsql} AND
                (
                    p.userid = :postuserid OR
                    p.privatereplyto = :privatereplyrecipient OR
                    fr.id IS NOT NULL OR
                    {$ratingsql->userwhere}
                )
              GROUP BY f.id, p.discussion, d.name, d.groupid
        ";

        $params = [
            'postuserid'    => $userid,
            'readuserid'    => $userid,
            'privatereplyrecipient' => $userid,
        ];
        $params += $forumparams;
        $params += $ratingsql->params;

        $discussions = $DB->get_records_sql($sql, $params);
        foreach ($discussions as $discussion) {
            $context = \context::instance_by_id($mappings[$discussion->forumid]);
            static::export_all_posts_in_discussion($userid, $context, $discussion);
        }
    }

    /**
     * Store all information about all posts that we have detected this user to have access to.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     * @param   \context    $context The instance of the forum context.
     * @param   \stdClass   $discussion The discussion whose data is being exported.
     */
    protected static function export_all_posts_in_discussion(int $userid, \context $context, \stdClass $discussion) {
        global $DB, $USER;

        $discussionid = $discussion->id;

        // Find all of the posts, and post subscriptions for this forum.
        $ratingsql = \core_rating\privacy\provider::get_sql_join('rat', 'mod_forum', 'post', 'p.id', $userid);
        $sql = "SELECT
                    p.*,
                    d.forum AS forumid,
                    fr.firstread,
                    fr.lastread,
                    fr.id AS readflag,
                    rat.id AS hasratings
                    FROM {forum_discussions} d
                    JOIN {forum_posts} p ON p.discussion = d.id
               LEFT JOIN {forum_read} fr ON fr.postid = p.id AND fr.userid = :readuserid
            {$ratingsql->join} AND {$ratingsql->userwhere}
                   WHERE d.id = :discussionid
                     AND (
                            p.privatereplyto = 0
                         OR p.privatereplyto = :privatereplyrecipient
                         OR p.userid = :privatereplyauthor
                     )
        ";

        $params = [
            'discussionid'  => $discussionid,
            'readuserid'    => $userid,
            'privatereplyrecipient' => $userid,
            'privatereplyauthor' => $userid,
        ];
        $params += $ratingsql->params;

        // Keep track of the forums which have data.
        $structure = (object) [
            'children' => [],
        ];

        $posts = $DB->get_records_sql($sql, $params);
        foreach ($posts as $post) {
            $post->hasdata = (isset($post->hasdata)) ? $post->hasdata : false;
            $post->hasdata = $post->hasdata || !empty($post->hasratings);
            $post->hasdata = $post->hasdata || $post->readflag;
            $post->hasdata = $post->hasdata || ($post->userid == $USER->id);
            $post->hasdata = $post->hasdata || ($post->privatereplyto == $USER->id);

            if (0 == $post->parent) {
                $structure->children[$post->id] = $post;
            } else {
                if (empty($posts[$post->parent]->children)) {
                    $posts[$post->parent]->children = [];
                }
                $posts[$post->parent]->children[$post->id] = $post;
            }

            // Set all parents.
            if ($post->hasdata) {
                $curpost = $post;
                while ($curpost->parent != 0) {
                    $curpost = $posts[$curpost->parent];
                    $curpost->hasdata = true;
                }
            }
        }

        $discussionarea = static::get_discussion_area($discussion);
        $discussionarea[] = get_string('posts', 'mod_forum');
        static::export_posts_in_structure($userid, $context, $discussionarea, $structure);
    }

    /**
     * Export all posts in the provided structure.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     * @param   \context    $context The instance of the forum context.
     * @param   array       $parentarea The subcontext of the parent.
     * @param   \stdClass   $structure The post structure and all of its children
     */
    protected static function export_posts_in_structure(int $userid, \context $context, $parentarea, \stdClass $structure) {
        foreach ($structure->children as $post) {
            if (!$post->hasdata) {
                // This tree has no content belonging to the user. Skip it and all children.
                continue;
            }

            $postarea = array_merge($parentarea, static::get_post_area($post));

            // Store the post content.
            static::export_post_data($userid, $context, $postarea, $post);

            if (isset($post->children)) {
                // Now export children of this post.
                static::export_posts_in_structure($userid, $context, $postarea, $post);
            }
        }
    }

    /**
     * Export all data in the post.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     * @param   \context    $context The instance of the forum context.
     * @param   array       $postarea The subcontext of the parent.
     * @param   \stdClass   $post The post structure and all of its children
     */
    protected static function export_post_data(int $userid, \context $context, $postarea, $post) {
        // Store related metadata.
        static::export_read_data($userid, $context, $postarea, $post);

        $postdata = (object) [
            'subject' => format_string($post->subject, true),
            'created' => transform::datetime($post->created),
            'modified' => transform::datetime($post->modified),
            'author_was_you' => transform::yesno($post->userid == $userid),
        ];

        if (!empty($post->privatereplyto)) {
            $postdata->privatereply = transform::yesno(true);
        }

        $postdata->message = writer::with_context($context)
            ->rewrite_pluginfile_urls($postarea, 'mod_forum', 'post', $post->id, $post->message);

        $postdata->message = format_text($postdata->message, $post->messageformat, (object) [
            'para'    => false,
            'trusted' => $post->messagetrust,
            'context' => $context,
        ]);

        writer::with_context($context)
            // Store the post.
            ->export_data($postarea, $postdata)

            // Store the associated files.
            ->export_area_files($postarea, 'mod_forum', 'post', $post->id);

        if ($post->userid == $userid) {
            // Store all ratings against this post as the post belongs to the user. All ratings on it are ratings of their content.
            \core_rating\privacy\provider::export_area_ratings($userid, $context, $postarea, 'mod_forum', 'post', $post->id, false);

            // Store all tags against this post as the tag belongs to the user.
            \core_tag\privacy\provider::export_item_tags($userid, $context, $postarea, 'mod_forum', 'forum_posts', $post->id);

            // Export all user data stored for this post from the plagiarism API.
            $coursecontext = $context->get_course_context();
            \core_plagiarism\privacy\provider::export_plagiarism_user_data($userid, $context, $postarea, [
                    'cmid' => $context->instanceid,
                    'course' => $coursecontext->instanceid,
                    'forum' => $post->forumid,
                    'discussionid' => $post->discussion,
                    'postid' => $post->id,
                ]);
        }

        // Check for any ratings that the user has made on this post.
        \core_rating\privacy\provider::export_area_ratings($userid,
                $context,
                $postarea,
                'mod_forum',
                'post',
                $post->id,
                $userid,
                true
            );
    }

    /**
     * Store data about daily digest preferences
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     * @param   \stdClass   $forum The forum whose data is being exported.
     * @param   int         $maildigest The mail digest setting for this forum.
     * @return  bool        Whether any data was stored.
     */
    protected static function export_digest_data(int $userid, \stdClass $forum, int $maildigest) {
        if (null !== $maildigest) {
            // The user has a specific maildigest preference for this forum.
            $a = (object) [
                'forum' => format_string($forum->name, true),
            ];

            switch ($maildigest) {
                case 0:
                    $a->type = get_string('emaildigestoffshort', 'mod_forum');
                    break;
                case 1:
                    $a->type = get_string('emaildigestcompleteshort', 'mod_forum');
                    break;
                case 2:
                    $a->type = get_string('emaildigestsubjectsshort', 'mod_forum');
                    break;
            }

            writer::with_context(\context_module::instance($forum->cmid))
                ->export_metadata([], 'digestpreference', $maildigest,
                    get_string('privacy:digesttypepreference', 'mod_forum', $a));

            return true;
        }

        return false;
    }

    /**
     * Store data about whether the user subscribes to forum.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     * @param   \stdClass   $forum The forum whose data is being exported.
     * @param   int         $subscribed if the user is subscribed
     * @return  bool        Whether any data was stored.
     */
    protected static function export_subscription_data(int $userid, \stdClass $forum, int $subscribed) {
        if (null !== $subscribed) {
            // The user is subscribed to this forum.
            writer::with_context(\context_module::instance($forum->cmid))
                ->export_metadata([], 'subscriptionpreference', 1, get_string('privacy:subscribedtoforum', 'mod_forum'));

            return true;
        }

        return false;
    }

    /**
     * Store data about whether the user subscribes to this particular discussion.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     * @param   \context_module $context The instance of the forum context.
     * @param   \stdClass   $discussion The discussion whose data is being exported.
     * @return  bool        Whether any data was stored.
     */
    protected static function export_discussion_subscription_data(int $userid, \context_module $context, \stdClass $discussion) {
        $area = static::get_discussion_area($discussion);
        if (null !== $discussion->preference) {
            // The user has a specific subscription preference for this discussion.
            $a = (object) [];

            switch ($discussion->preference) {
                case \mod_forum\subscriptions::FORUM_DISCUSSION_UNSUBSCRIBED:
                    $a->preference = get_string('unsubscribed', 'mod_forum');
                    break;
                default:
                    $a->preference = get_string('subscribed', 'mod_forum');
                    break;
            }

            writer::with_context($context)
                ->export_metadata(
                    $area,
                    'subscriptionpreference',
                    $discussion->preference,
                    get_string('privacy:discussionsubscriptionpreference', 'mod_forum', $a)
                );

            return true;
        }

        return true;
    }

    /**
     * Store forum read-tracking data about a particular forum.
     *
     * This is whether a forum has read-tracking enabled or not.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     * @param   \stdClass   $forum The forum whose data is being exported.
     * @param   int         $tracke if the user is subscribed
     * @return  bool        Whether any data was stored.
     */
    protected static function export_tracking_data(int $userid, \stdClass $forum, int $tracked) {
        if (null !== $tracked) {
            // The user has a main preference to track all forums, but has opted out of this one.
            writer::with_context(\context_module::instance($forum->cmid))
                ->export_metadata([], 'trackreadpreference', 0, get_string('privacy:readtrackingdisabled', 'mod_forum'));

            return true;
        }

        return false;
    }

    protected static function export_grading_data(int $userid, \stdClass $forum, int $grade) {
        global $USER;
        if (null !== $grade) {
            $context = \context_module::instance($forum->cmid);
            $exportpath = array_merge([],
                [get_string('privacy:metadata:forum_grades', 'mod_forum')]);
            $gradingmanager = get_grading_manager($context, 'mod_forum', 'forum');
            $controller = $gradingmanager->get_active_controller();

            // Check for advanced grading and retrieve that information.
            if (isset($controller)) {
                $gradeduser = \core_user::get_user($userid);
                // Fetch the gradeitem instance.
                $gradeitem = gradeitem::instance($controller->get_component(), $context, $controller->get_area());
                $grade = $gradeitem->get_grade_for_user($gradeduser, $USER);
                $controllercontext = $controller->get_context();
                \core_grading\privacy\provider::export_item_data($controllercontext, $grade->id, $exportpath);
            } else {
                self::export_grade_data($grade, $context, $forum, $exportpath);
            }
            // The user has a grade for this forum.
            writer::with_context(\context_module::instance($forum->cmid))
                ->export_metadata($exportpath, 'gradingenabled', 1, get_string('privacy:metadata:forum_grades:grade', 'mod_forum'));

            return true;
        }

        return false;
    }

    protected static function export_grade_data(int $grade, \context $context, \stdClass $forum, array $path) {
        $gradedata = (object)[
            'forum' => $forum->name,
            'grade' => $grade,
        ];

        writer::with_context($context)
            ->export_data($path, $gradedata);
    }

    /**
     * Store read-tracking information about a particular forum post.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     * @param   \context_module $context The instance of the forum context.
     * @param   array       $postarea The subcontext for this post.
     * @param   \stdClass   $post The post whose data is being exported.
     * @return  bool        Whether any data was stored.
     */
    protected static function export_read_data(int $userid, \context_module $context, array $postarea, \stdClass $post) {
        if (null !== $post->firstread) {
            $a = (object) [
                'firstread' => $post->firstread,
                'lastread'  => $post->lastread,
            ];

            writer::with_context($context)
                ->export_metadata(
                    $postarea,
                    'postread',
                    (object) [
                        'firstread' => $post->firstread,
                        'lastread' => $post->lastread,
                    ],
                    get_string('privacy:postwasread', 'mod_forum', $a)
                );

            return true;
        }

        return false;
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param   context                 $context   The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        // Check that this is a context_module.
        if (!$context instanceof \context_module) {
            return;
        }

        // Get the course module.
        if (!$cm = get_coursemodule_from_id('forum', $context->instanceid)) {
            return;
        }

        $forumid = $cm->instance;

        $DB->delete_records('forum_track_prefs', ['forumid' => $forumid]);
        $DB->delete_records('forum_subscriptions', ['forum' => $forumid]);
        $DB->delete_records('forum_grades', ['forum' => $forumid]);
        $DB->delete_records('forum_read', ['forumid' => $forumid]);
        $DB->delete_records('forum_digests', ['forum' => $forumid]);

        // Delete advanced grading information.
        $gradingmanager = get_grading_manager($context, 'mod_forum', 'forum');
        $controller = $gradingmanager->get_active_controller();
        if (isset($controller)) {
            \core_grading\privacy\provider::delete_instance_data($context);
        }

        $DB->delete_records('forum_grades', ['forum' => $forumid]);

        // Delete all discussion items.
        $DB->delete_records_select(
            'forum_queue',
            "discussionid IN (SELECT id FROM {forum_discussions} WHERE forum = :forum)",
            [
                'forum' => $forumid,
            ]
        );

        $DB->delete_records_select(
            'forum_posts',
            "discussion IN (SELECT id FROM {forum_discussions} WHERE forum = :forum)",
            [
                'forum' => $forumid,
            ]
        );

        $DB->delete_records('forum_discussion_subs', ['forum' => $forumid]);
        $DB->delete_records('forum_discussions', ['forum' => $forumid]);

        // Delete all files from the posts.
        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'mod_forum', 'post');
        $fs->delete_area_files($context->id, 'mod_forum', 'attachment');

        // Delete all ratings in the context.
        \core_rating\privacy\provider::delete_ratings($context, 'mod_forum', 'post');

        // Delete all Tags.
        \core_tag\privacy\provider::delete_item_tags($context, 'mod_forum', 'forum_posts');
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        $user = $contextlist->get_user();
        $userid = $user->id;
        foreach ($contextlist as $context) {
            // Get the course module.
            $cm = $DB->get_record('course_modules', ['id' => $context->instanceid]);
            $forum = $DB->get_record('forum', ['id' => $cm->instance]);

            $DB->delete_records('forum_track_prefs', [
                'forumid' => $forum->id,
                'userid' => $userid,
            ]);
            $DB->delete_records('forum_subscriptions', [
                'forum' => $forum->id,
                'userid' => $userid,
            ]);
            $DB->delete_records('forum_read', [
                'forumid' => $forum->id,
                'userid' => $userid,
            ]);

            $DB->delete_records('forum_digests', [
                'forum' => $forum->id,
                'userid' => $userid,
            ]);

            // Delete all discussion items.
            $DB->delete_records_select(
                'forum_queue',
                "userid = :userid AND discussionid IN (SELECT id FROM {forum_discussions} WHERE forum = :forum)",
                [
                    'userid' => $userid,
                    'forum' => $forum->id,
                ]
            );

            $DB->delete_records('forum_discussion_subs', [
                'forum' => $forum->id,
                'userid' => $userid,
            ]);

            // Handle any advanced grading method data first.
            $grades = $DB->get_records('forum_grades', ['forum' => $forum->id, 'userid' => $user->id]);
            $gradingmanager = get_grading_manager($context, 'forum_grades', 'forum');
            $controller = $gradingmanager->get_active_controller();
            foreach ($grades as $grade) {
                // Delete advanced grading information.
                if (isset($controller)) {
                    \core_grading\privacy\provider::delete_instance_data($context, $grade->id);
                }
            }
            // Advanced grading methods have been cleared, lets clear our module now.
            $DB->delete_records('forum_grades', [
                'forum' => $forum->id,
                'userid' => $userid,
            ]);

            // Do not delete discussion or forum posts.
            // Instead update them to reflect that the content has been deleted.
            $postsql = "userid = :userid AND discussion IN (SELECT id FROM {forum_discussions} WHERE forum = :forum)";
            $postidsql = "SELECT fp.id FROM {forum_posts} fp WHERE {$postsql}";
            $postparams = [
                'forum' => $forum->id,
                'userid' => $userid,
            ];

            // Update the subject.
            $DB->set_field_select('forum_posts', 'subject', '', $postsql, $postparams);

            // Update the message and its format.
            $DB->set_field_select('forum_posts', 'message', '', $postsql, $postparams);
            $DB->set_field_select('forum_posts', 'messageformat', FORMAT_PLAIN, $postsql, $postparams);

            // Mark the post as deleted.
            $DB->set_field_select('forum_posts', 'deleted', 1, $postsql, $postparams);

            // Note: Do _not_ delete ratings of other users. Only delete ratings on the users own posts.
            // Ratings are aggregate fields and deleting the rating of this post will have an effect on the rating
            // of any post.
            \core_rating\privacy\provider::delete_ratings_select($context, 'mod_forum', 'post',
                    "IN ($postidsql)", $postparams);

            // Delete all Tags.
            \core_tag\privacy\provider::delete_item_tags_select($context, 'mod_forum', 'forum_posts',
                    "IN ($postidsql)", $postparams);

            // Delete all files from the posts.
            $fs = get_file_storage();
            $fs->delete_area_files_select($context->id, 'mod_forum', 'post', "IN ($postidsql)", $postparams);
            $fs->delete_area_files_select($context->id, 'mod_forum', 'attachment', "IN ($postidsql)", $postparams);
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();
        $cm = $DB->get_record('course_modules', ['id' => $context->instanceid]);
        $forum = $DB->get_record('forum', ['id' => $cm->instance]);

        list($userinsql, $userinparams) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
        $params = array_merge(['forumid' => $forum->id], $userinparams);

        $DB->delete_records_select('forum_track_prefs', "forumid = :forumid AND userid {$userinsql}", $params);
        $DB->delete_records_select('forum_subscriptions', "forum = :forumid AND userid {$userinsql}", $params);
        $DB->delete_records_select('forum_read', "forumid = :forumid AND userid {$userinsql}", $params);
        $DB->delete_records_select(
            'forum_queue',
            "userid {$userinsql} AND discussionid IN (SELECT id FROM {forum_discussions} WHERE forum = :forumid)",
            $params
        );
        $DB->delete_records_select('forum_discussion_subs', "forum = :forumid AND userid {$userinsql}", $params);

        // Do not delete discussion or forum posts.
        // Instead update them to reflect that the content has been deleted.
        $postsql = "userid {$userinsql} AND discussion IN (SELECT id FROM {forum_discussions} WHERE forum = :forumid)";
        $postidsql = "SELECT fp.id FROM {forum_posts} fp WHERE {$postsql}";

        // Update the subject.
        $DB->set_field_select('forum_posts', 'subject', '', $postsql, $params);

        // Update the subject and its format.
        $DB->set_field_select('forum_posts', 'message', '', $postsql, $params);
        $DB->set_field_select('forum_posts', 'messageformat', FORMAT_PLAIN, $postsql, $params);

        // Mark the post as deleted.
        $DB->set_field_select('forum_posts', 'deleted', 1, $postsql, $params);

        // Note: Do _not_ delete ratings of other users. Only delete ratings on the users own posts.
        // Ratings are aggregate fields and deleting the rating of this post will have an effect on the rating
        // of any post.
        \core_rating\privacy\provider::delete_ratings_select($context, 'mod_forum', 'post', "IN ($postidsql)", $params);

        // Delete all Tags.
        \core_tag\privacy\provider::delete_item_tags_select($context, 'mod_forum', 'forum_posts', "IN ($postidsql)", $params);

        // Delete all files from the posts.
        $fs = get_file_storage();
        $fs->delete_area_files_select($context->id, 'mod_forum', 'post', "IN ($postidsql)", $params);
        $fs->delete_area_files_select($context->id, 'mod_forum', 'attachment', "IN ($postidsql)", $params);

        list($sql, $params) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
        $params['forum'] = $forum->id;
        // Delete advanced grading information.
        $grades = $DB->get_records_select('forum_grades', "forum = :forum AND userid $sql", $params);
        $gradeids = array_keys($grades);
        $gradingmanager = get_grading_manager($context, 'mod_forum', 'forum');
        $controller = $gradingmanager->get_active_controller();
        if (isset($controller)) {
            // Careful here, if no gradeids are provided then all data is deleted for the context.
            if (!empty($gradeids)) {
                \core_grading\privacy\provider::delete_data_for_instances($context, $gradeids);
            }
        }
    }
}
