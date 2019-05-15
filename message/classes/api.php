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
 * Contains class used to return information to display for the message area.
 *
 * @package    core_message
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_message;

use core_favourites\local\entity\favourite;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/messagelib.php');

/**
 * Class used to return information to display for the message area.
 *
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /**
     * The action for reading a message.
     */
    const MESSAGE_ACTION_READ = 1;

    /**
     * The action for deleting a message.
     */
    const MESSAGE_ACTION_DELETED = 2;

    /**
     * The action for reading a message.
     */
    const CONVERSATION_ACTION_MUTED = 1;

    /**
     * The privacy setting for being messaged by anyone within courses user is member of.
     */
    const MESSAGE_PRIVACY_COURSEMEMBER = 0;

    /**
     * The privacy setting for being messaged only by contacts.
     */
    const MESSAGE_PRIVACY_ONLYCONTACTS = 1;

    /**
     * The privacy setting for being messaged by anyone on the site.
     */
    const MESSAGE_PRIVACY_SITE = 2;

    /**
     * An individual conversation.
     */
    const MESSAGE_CONVERSATION_TYPE_INDIVIDUAL = 1;

    /**
     * A group conversation.
     */
    const MESSAGE_CONVERSATION_TYPE_GROUP = 2;

    /**
     * A self conversation.
     */
    const MESSAGE_CONVERSATION_TYPE_SELF = 3;

    /**
     * The state for an enabled conversation area.
     */
    const MESSAGE_CONVERSATION_ENABLED = 1;

    /**
     * The state for a disabled conversation area.
     */
    const MESSAGE_CONVERSATION_DISABLED = 0;

    /**
     * Handles searching for messages in the message area.
     *
     * @param int $userid The user id doing the searching
     * @param string $search The string the user is searching
     * @param int $limitfrom
     * @param int $limitnum
     * @return array
     */
    public static function search_messages($userid, $search, $limitfrom = 0, $limitnum = 0) {
        global $DB;

        // Get the user fields we want.
        $ufields = \user_picture::fields('u', array('lastaccess'), 'userfrom_id', 'userfrom_');
        $ufields2 = \user_picture::fields('u2', array('lastaccess'), 'userto_id', 'userto_');
        // Add the uniqueid column to make each row unique and avoid SQL errors.
        $uniqueidsql = $DB->sql_concat('m.id', "'_'", 'm.useridfrom', "'_'", 'mcm.userid');

        $sql = "SELECT $uniqueidsql AS uniqueid, m.id, m.useridfrom, mcm.userid as useridto, m.subject, m.fullmessage,
                       m.fullmessagehtml, m.fullmessageformat, m.smallmessage, m.conversationid, m.timecreated, 0 as isread,
                       $ufields, mub.id as userfrom_blocked, $ufields2, mub2.id as userto_blocked
                  FROM {messages} m
            INNER JOIN {user} u
                    ON u.id = m.useridfrom
            INNER JOIN {message_conversations} mc
                    ON mc.id = m.conversationid
            INNER JOIN {message_conversation_members} mcm
                    ON mcm.conversationid = m.conversationid
            INNER JOIN {user} u2
                    ON u2.id = mcm.userid
             LEFT JOIN {message_users_blocked} mub
                    ON (mub.blockeduserid = u.id AND mub.userid = ?)
             LEFT JOIN {message_users_blocked} mub2
                    ON (mub2.blockeduserid = u2.id AND mub2.userid = ?)
             LEFT JOIN {message_user_actions} mua
                    ON (mua.messageid = m.id AND mua.userid = ? AND mua.action = ?)
                 WHERE (m.useridfrom = ? OR mcm.userid = ?)
                   AND (m.useridfrom != mcm.userid OR mc.type = ?)
                   AND u.deleted = 0
                   AND u2.deleted = 0
                   AND mua.id is NULL
                   AND " . $DB->sql_like('smallmessage', '?', false) . "
              ORDER BY timecreated DESC";

        $params = array($userid, $userid, $userid, self::MESSAGE_ACTION_DELETED, $userid, $userid,
            self::MESSAGE_CONVERSATION_TYPE_SELF, '%' . $search . '%');

        // Convert the messages into searchable contacts with their last message being the message that was searched.
        $conversations = array();
        if ($messages = $DB->get_records_sql($sql, $params, $limitfrom, $limitnum)) {
            foreach ($messages as $message) {
                $prefix = 'userfrom_';
                if ($userid == $message->useridfrom) {
                    $prefix = 'userto_';
                    // If it from the user, then mark it as read, even if it wasn't by the receiver.
                    $message->isread = true;
                }
                $blockedcol = $prefix . 'blocked';
                $message->blocked = $message->$blockedcol ? 1 : 0;

                $message->messageid = $message->id;
                // To avoid duplicate messages, only add the message if it hasn't been added previously.
                if (!array_key_exists($message->messageid, $conversations)) {
                    $conversations[$message->messageid] = helper::create_contact($message, $prefix);
                }
            }
            // Remove the messageid keys (to preserve the expected type).
            $conversations = array_values($conversations);
        }

        return $conversations;
    }

    /**
     * Handles searching for user in a particular course in the message area.
     *
     * TODO: This function should be removed once the related web service goes through final deprecation.
     * The related web service is data_for_messagearea_search_users_in_course.
     * Followup: MDL-63261
     *
     * @param int $userid The user id doing the searching
     * @param int $courseid The id of the course we are searching in
     * @param string $search The string the user is searching
     * @param int $limitfrom
     * @param int $limitnum
     * @return array
     */
    public static function search_users_in_course($userid, $courseid, $search, $limitfrom = 0, $limitnum = 0) {
        global $DB;

        // Get all the users in the course.
        list($esql, $params) = get_enrolled_sql(\context_course::instance($courseid), '', 0, true);
        $sql = "SELECT u.*, mub.id as isblocked
                  FROM {user} u
                  JOIN ($esql) je
                    ON je.id = u.id
             LEFT JOIN {message_users_blocked} mub
                    ON (mub.blockeduserid = u.id AND mub.userid = :userid)
                 WHERE u.deleted = 0";
        // Add more conditions.
        $fullname = $DB->sql_fullname();
        $sql .= " AND u.id != :userid2
                  AND " . $DB->sql_like($fullname, ':search', false) . "
             ORDER BY " . $DB->sql_fullname();
        $params = array_merge(array('userid' => $userid, 'userid2' => $userid, 'search' => '%' . $search . '%'), $params);

        // Convert all the user records into contacts.
        $contacts = array();
        if ($users = $DB->get_records_sql($sql, $params, $limitfrom, $limitnum)) {
            foreach ($users as $user) {
                $user->blocked = $user->isblocked ? 1 : 0;
                $contacts[] = helper::create_contact($user);
            }
        }

        return $contacts;
    }

    /**
     * Handles searching for user in the message area.
     *
     * TODO: This function should be removed once the related web service goes through final deprecation.
     * The related web service is data_for_messagearea_search_users.
     * Followup: MDL-63261
     *
     * @param int $userid The user id doing the searching
     * @param string $search The string the user is searching
     * @param int $limitnum
     * @return array
     */
    public static function search_users($userid, $search, $limitnum = 0) {
        global $CFG, $DB;

        // Used to search for contacts.
        $fullname = $DB->sql_fullname();
        $ufields = \user_picture::fields('u', array('lastaccess'));

        // Users not to include.
        $excludeusers = array($userid, $CFG->siteguest);
        list($exclude, $excludeparams) = $DB->get_in_or_equal($excludeusers, SQL_PARAMS_NAMED, 'param', false);

        // Ok, let's search for contacts first.
        $contacts = array();
        $sql = "SELECT $ufields, mub.id as isuserblocked
                  FROM {user} u
                  JOIN {message_contacts} mc
                    ON u.id = mc.contactid
             LEFT JOIN {message_users_blocked} mub
                    ON (mub.userid = :userid2 AND mub.blockeduserid = u.id)
                 WHERE mc.userid = :userid
                   AND u.deleted = 0
                   AND u.confirmed = 1
                   AND " . $DB->sql_like($fullname, ':search', false) . "
                   AND u.id $exclude
              ORDER BY " . $DB->sql_fullname();
        if ($users = $DB->get_records_sql($sql, array('userid' => $userid, 'userid2' => $userid,
                'search' => '%' . $search . '%') + $excludeparams, 0, $limitnum)) {
            foreach ($users as $user) {
                $user->blocked = $user->isuserblocked ? 1 : 0;
                $contacts[] = helper::create_contact($user);
            }
        }

        // Now, let's get the courses.
        // Make sure to limit searches to enrolled courses.
        $enrolledcourses = enrol_get_my_courses(array('id', 'cacherev'));
        $courses = array();
        // Really we want the user to be able to view the participants if they have the capability
        // 'moodle/course:viewparticipants' or 'moodle/course:enrolreview', but since the search_courses function
        // only takes required parameters we can't. However, the chance of a user having 'moodle/course:enrolreview' but
        // *not* 'moodle/course:viewparticipants' are pretty much zero, so it is not worth addressing.
        if ($arrcourses = \core_course_category::search_courses(array('search' => $search), array('limit' => $limitnum),
                array('moodle/course:viewparticipants'))) {
            foreach ($arrcourses as $course) {
                if (isset($enrolledcourses[$course->id])) {
                    $data = new \stdClass();
                    $data->id = $course->id;
                    $data->shortname = $course->shortname;
                    $data->fullname = $course->fullname;
                    $courses[] = $data;
                }
            }
        }

        // Let's get those non-contacts. Toast them gears boi.
        // Note - you can only block contacts, so these users will not be blocked, so no need to get that
        // extra detail from the database.
        $noncontacts = array();
        $sql = "SELECT $ufields
                  FROM {user} u
                 WHERE u.deleted = 0
                   AND u.confirmed = 1
                   AND " . $DB->sql_like($fullname, ':search', false) . "
                   AND u.id $exclude
                   AND u.id NOT IN (SELECT contactid
                                      FROM {message_contacts}
                                     WHERE userid = :userid)
              ORDER BY " . $DB->sql_fullname();
        if ($users = $DB->get_records_sql($sql,  array('userid' => $userid, 'search' => '%' . $search . '%') + $excludeparams,
                0, $limitnum)) {
            foreach ($users as $user) {
                $noncontacts[] = helper::create_contact($user);
            }
        }

        return array($contacts, $courses, $noncontacts);
    }

    /**
     * Handles searching for user.
     *
     * @param int $userid The user id doing the searching
     * @param string $search The string the user is searching
     * @param int $limitfrom
     * @param int $limitnum
     * @return array
     */
    public static function message_search_users(int $userid, string $search, int $limitfrom = 0, int $limitnum = 20) : array {
        global $CFG, $DB;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new \moodle_exception('disabled', 'message');
        }

        // Used to search for contacts.
        $fullname = $DB->sql_fullname();

        // Users not to include.
        $excludeusers = array($CFG->siteguest);
        if (!$selfconversation = self::get_self_conversation($userid)) {
            // Userid should only be excluded when she hasn't a self-conversation.
            $excludeusers[] = $userid;
        }
        list($exclude, $excludeparams) = $DB->get_in_or_equal($excludeusers, SQL_PARAMS_NAMED, 'param', false);

        $params = array('search' => '%' . $DB->sql_like_escape($search) . '%', 'userid1' => $userid, 'userid2' => $userid);

        // Ok, let's search for contacts first.
        $sql = "SELECT u.id
                  FROM {user} u
                  JOIN {message_contacts} mc
                    ON (u.id = mc.contactid AND mc.userid = :userid1) OR (u.id = mc.userid AND mc.contactid = :userid2)
                 WHERE u.deleted = 0
                   AND u.confirmed = 1
                   AND " . $DB->sql_like($fullname, ':search', false) . "
                   AND u.id $exclude
              ORDER BY " . $DB->sql_fullname();
        $foundusers = $DB->get_records_sql_menu($sql, $params + $excludeparams, $limitfrom, $limitnum);

        $contacts = [];
        if (!empty($foundusers)) {
            $contacts = helper::get_member_info($userid, array_keys($foundusers));
            foreach ($contacts as $memberuserid => $memberinfo) {
                $contacts[$memberuserid]->conversations = self::get_conversations_between_users($userid, $memberuserid, 0, 1000);
            }
        }

        // Let's get those non-contacts.
        // Because we can't achieve all the required visibility checks in SQL, we'll iterate through the non-contact records
        // and stop once we have enough matching the 'visible' criteria.
        // TODO: MDL-63983 - Improve the performance of non-contact searches when site-wide messaging is disabled (default).

        // Use a local generator to achieve this iteration.
        $getnoncontactusers = function ($limitfrom = 0, $limitnum = 0) use($fullname, $exclude, $params, $excludeparams) {
            global $DB;
            $sql = "SELECT u.*
                  FROM {user} u
                 WHERE u.deleted = 0
                   AND u.confirmed = 1
                   AND " . $DB->sql_like($fullname, ':search', false) . "
                   AND u.id $exclude
                   AND NOT EXISTS (SELECT mc.id
                                     FROM {message_contacts} mc
                                    WHERE (mc.userid = u.id AND mc.contactid = :userid1)
                                       OR (mc.userid = :userid2 AND mc.contactid = u.id))
              ORDER BY " . $DB->sql_fullname();
            while ($records = $DB->get_records_sql($sql, $params + $excludeparams, $limitfrom, $limitnum)) {
                yield $records;
                $limitfrom += $limitnum;
            }
        };

        // Fetch in batches of $limitnum * 2 to improve the chances of matching a user without going back to the DB.
        // The generator cannot function without a sensible limiter, so set one if this is not set.
        $batchlimit = ($limitnum == 0) ? 20 : $limitnum;

        // We need to make the offset param work with the generator.
        // Basically, if we want to get say 10 records starting at the 40th record, we need to see 50 records and return only
        // those after the 40th record. We can never pass the method's offset param to the generator as we need to manage the
        // position within those valid records ourselves.
        // See MDL-63983 dealing with performance improvements to this area of code.
        $noofvalidseenrecords = 0;
        $returnedusers = [];
        foreach ($getnoncontactusers(0, $batchlimit) as $users) {
            foreach ($users as $id => $user) {
                // User visibility checks: only return users who are visible to the user performing the search.
                // Which visibility check to use depends on the 'messagingallusers' (site wide messaging) setting:
                // - If enabled, return matched users whose profiles are visible to the current user anywhere (site or course).
                // - If disabled, only return matched users whose course profiles are visible to the current user.
                $userdetails = \core_message\helper::search_get_user_details($user);

                // Return the user only if the searched field is returned.
                // Otherwise it means that the $USER was not allowed to search the returned user.
                if (!empty($userdetails) and !empty($userdetails['fullname'])) {
                    // We know we've matched, but only save the record if it's within the offset area we need.
                    if ($limitfrom == 0) {
                        // No offset specified, so just save.
                        $returnedusers[$id] = $user;
                    } else {
                        // There is an offset in play.
                        // If we've passed enough records already (> offset value), then we can save this one.
                        if ($noofvalidseenrecords >= $limitfrom) {
                            $returnedusers[$id] = $user;
                        }
                    }
                    if (count($returnedusers) == $limitnum) {
                        break 2;
                    }
                    $noofvalidseenrecords++;
                }
            }
        }
        $foundusers = $returnedusers;

        $noncontacts = [];
        if (!empty($foundusers)) {
            $noncontacts = helper::get_member_info($userid, array_keys($foundusers));
            foreach ($noncontacts as $memberuserid => $memberinfo) {
                if ($memberuserid !== $userid) {
                    $noncontacts[$memberuserid]->conversations = self::get_conversations_between_users($userid, $memberuserid, 0,
                        1000);
                } else {
                    $noncontacts[$memberuserid]->conversations[$selfconversation->id] = $selfconversation;
                }
            }
        }

        return array(array_values($contacts), array_values($noncontacts));
    }

    /**
     * Gets extra fields, like image url and subname for any conversations linked to components.
     *
     * The subname is like a subtitle for the conversation, to compliment it's name.
     * The imageurl is the location of the image for the conversation, as might be seen on a listing of conversations for a user.
     *
     * @param array $conversations a list of conversations records.
     * @return array the array of subnames, index by conversation id.
     * @throws \coding_exception
     * @throws \dml_exception
     */
    protected static function get_linked_conversation_extra_fields(array $conversations) : array {
        global $DB, $PAGE;

        $renderer = $PAGE->get_renderer('core');

        $linkedconversations = [];
        foreach ($conversations as $conversation) {
            if (!is_null($conversation->component) && !is_null($conversation->itemtype)) {
                $linkedconversations[$conversation->component][$conversation->itemtype][$conversation->id]
                    = $conversation->itemid;
            }
        }
        if (empty($linkedconversations)) {
            return [];
        }

        // TODO: MDL-63814: Working out the subname for linked conversations should be done in a generic way.
        // Get the itemid, but only for course group linked conversation for now.
        $extrafields = [];
        if (!empty($linkeditems = $linkedconversations['core_group']['groups'])) { // Format: [conversationid => itemid].
            // Get the name of the course to which the group belongs.
            list ($groupidsql, $groupidparams) = $DB->get_in_or_equal(array_values($linkeditems), SQL_PARAMS_NAMED, 'groupid');
            $sql = "SELECT g.*, c.shortname as courseshortname
                      FROM {groups} g
                      JOIN {course} c
                        ON g.courseid = c.id
                     WHERE g.id $groupidsql";
            $courseinfo = $DB->get_records_sql($sql, $groupidparams);
            foreach ($linkeditems as $convid => $groupid) {
                if (array_key_exists($groupid, $courseinfo)) {
                    $group = $courseinfo[$groupid];
                    // Subname.
                    $extrafields[$convid]['subname'] = format_string($courseinfo[$groupid]->courseshortname);

                    // Imageurl.
                    $extrafields[$convid]['imageurl'] = $renderer->image_url('g/g1')->out(false); // default image.
                    if ($url = get_group_picture_url($group, $group->courseid, true)) {
                        $extrafields[$convid]['imageurl'] = $url->out(false);
                    }
                }
            }
        }
        return $extrafields;
    }


    /**
     * Returns the contacts and their conversation to display in the contacts area.
     *
     * ** WARNING **
     * It is HIGHLY recommended to use a sensible limit when calling this function. Trying
     * to retrieve too much information in a single call will cause performance problems.
     * ** WARNING **
     *
     * This function has specifically been altered to break each of the data sets it
     * requires into separate database calls. This is to avoid the performance problems
     * observed when attempting to join large data sets (e.g. the message tables and
     * the user table).
     *
     * While it is possible to gather the data in a single query, and it may even be
     * more efficient with a correctly tuned database, we have opted to trade off some of
     * the benefits of a single query in order to ensure this function will work on
     * most databases with default tunings and with large data sets.
     *
     * @param int $userid The user id
     * @param int $limitfrom
     * @param int $limitnum
     * @param int $type the type of the conversation, if you wish to filter to a certain type (see api constants).
     * @param bool $favourites whether to include NO favourites (false) or ONLY favourites (true), or null to ignore this setting.
     * @param bool $mergeself whether to include self-conversations (true) or ONLY private conversations (false)
     *             when private conversations are requested.
     * @return array the array of conversations
     * @throws \moodle_exception
     */
    public static function get_conversations($userid, $limitfrom = 0, $limitnum = 20, int $type = null,
            bool $favourites = null, bool $mergeself = false) {
        global $DB;

        if (!is_null($type) && !in_array($type, [self::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
                self::MESSAGE_CONVERSATION_TYPE_GROUP, self::MESSAGE_CONVERSATION_TYPE_SELF])) {
            throw new \moodle_exception("Invalid value ($type) for type param, please see api constants.");
        }

        self::lazy_create_self_conversation($userid);

        // We need to know which conversations are favourites, so we can either:
        // 1) Include the 'isfavourite' attribute on conversations (when $favourite = null and we're including all conversations)
        // 2) Restrict the results to ONLY those conversations which are favourites (when $favourite = true)
        // 3) Restrict the results to ONLY those conversations which are NOT favourites (when $favourite = false).
        $service = \core_favourites\service_factory::get_service_for_user_context(\context_user::instance($userid));
        $favouriteconversations = $service->find_favourites_by_type('core_message', 'message_conversations');
        $favouriteconversationids = array_column($favouriteconversations, 'itemid');
        if ($favourites && empty($favouriteconversationids)) {
            return []; // If we are aiming to return ONLY favourites, and we have none, there's nothing more to do.
        }

        // CONVERSATIONS AND MOST RECENT MESSAGE.
        // Include those conversations with messages first (ordered by most recent message, desc), then add any conversations which
        // don't have messages, such as newly created group conversations.
        // Because we're sorting by message 'timecreated', those conversations without messages could be at either the start or the
        // end of the results (behaviour for sorting of nulls differs between DB vendors), so we use the case to presort these.

        // If we need to return ONLY favourites, or NO favourites, generate the SQL snippet.
        $favouritesql = "";
        $favouriteparams = [];
        if (null !== $favourites && !empty($favouriteconversationids)) {
            list ($insql, $favouriteparams) =
                    $DB->get_in_or_equal($favouriteconversationids, SQL_PARAMS_NAMED, 'favouriteids', $favourites);
            $favouritesql = " AND mc.id {$insql} ";
        }

        // If we need to restrict type, generate the SQL snippet.
        $typesql = "";
        $typeparams = [];
        if (!is_null($type)) {
            if ($mergeself && $type == self::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL) {
                // When $megerself is set to true, the self-conversations are returned also with the private conversations.
                $typesql = " AND (mc.type = :convtype1 OR mc.type = :convtype2) ";
                $typeparams = ['convtype1' => $type, 'convtype2' => self::MESSAGE_CONVERSATION_TYPE_SELF];
            } else {
                $typesql = " AND mc.type = :convtype ";
                $typeparams = ['convtype' => $type];
            }
        }

        $sql = "SELECT m.id as messageid, mc.id as id, mc.name as conversationname, mc.type as conversationtype, m.useridfrom,
                       m.smallmessage, m.fullmessage, m.fullmessageformat, m.fullmessagetrust, m.fullmessagehtml, m.timecreated,
                       mc.component, mc.itemtype, mc.itemid, mc.contextid, mca.action as ismuted
                  FROM {message_conversations} mc
            INNER JOIN {message_conversation_members} mcm
                    ON (mcm.conversationid = mc.id AND mcm.userid = :userid3)
            LEFT JOIN (
                          SELECT m.conversationid, MAX(m.id) AS messageid
                            FROM {messages} m
                      INNER JOIN (
                                      SELECT m.conversationid, MAX(m.timecreated) as maxtime
                                        FROM {messages} m
                                  INNER JOIN {message_conversation_members} mcm
                                          ON mcm.conversationid = m.conversationid
                                   LEFT JOIN {message_user_actions} mua
                                          ON (mua.messageid = m.id AND mua.userid = :userid AND mua.action = :action)
                                       WHERE mua.id is NULL
                                         AND mcm.userid = :userid2
                                    GROUP BY m.conversationid
                                 ) maxmessage
                               ON maxmessage.maxtime = m.timecreated AND maxmessage.conversationid = m.conversationid
                         GROUP BY m.conversationid
                       ) lastmessage
                    ON lastmessage.conversationid = mc.id
            LEFT JOIN {messages} m
                   ON m.id = lastmessage.messageid
            LEFT JOIN {message_conversation_actions} mca
                   ON (mca.conversationid = mc.id AND mca.userid = :userid4 AND mca.action = :convaction)
                WHERE mc.id IS NOT NULL
                  AND mc.enabled = 1 $typesql $favouritesql
              ORDER BY (CASE WHEN m.timecreated IS NULL THEN 0 ELSE 1 END) DESC, m.timecreated DESC, id DESC";

        $params = array_merge($favouriteparams, $typeparams, ['userid' => $userid, 'action' => self::MESSAGE_ACTION_DELETED,
            'userid2' => $userid, 'userid3' => $userid, 'userid4' => $userid, 'convaction' => self::CONVERSATION_ACTION_MUTED]);
        $conversationset = $DB->get_recordset_sql($sql, $params, $limitfrom, $limitnum);

        $conversations = [];
        $selfconversations = []; // Used to track conversations with one's self.
        $members = [];
        $individualmembers = [];
        $groupmembers = [];
        $selfmembers = [];
        foreach ($conversationset as $conversation) {
            $conversations[$conversation->id] = $conversation;
            $members[$conversation->id] = [];
        }
        $conversationset->close();

        // If there are no conversations found, then return early.
        if (empty($conversations)) {
            return [];
        }

        // COMPONENT-LINKED CONVERSATION FIELDS.
        // Conversations linked to components may have extra information, such as:
        // - subname: Essentially a subtitle for the conversation. So you'd have "name: subname".
        // - imageurl: A URL to the image for the linked conversation.
        // For now, this is ONLY course groups.
        $convextrafields = self::get_linked_conversation_extra_fields($conversations);

        // MEMBERS.
        // Ideally, we want to get 1 member for each conversation, but this depends on the type and whether there is a recent
        // message or not.
        //
        // For 'individual' type conversations between 2 users, regardless of who sent the last message,
        // we want the details of the other member in the conversation (i.e. not the current user).
        //
        // For 'group' type conversations, we want the details of the member who sent the last message, if there is one.
        // This can be the current user or another group member, but for groups without messages, this will be empty.
        //
        // For 'self' type conversations, we want the details of the current user.
        //
        // This also means that if type filtering is specified and only group conversations are returned, we don't need this extra
        // query to get the 'other' user as we already have that information.

        // Work out which members we have already, and which ones we might need to fetch.
        // If all the last messages were from another user, then we don't need to fetch anything further.
        foreach ($conversations as $conversation) {
            if ($conversation->conversationtype == self::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL) {
                if (!is_null($conversation->useridfrom) && $conversation->useridfrom != $userid) {
                    $members[$conversation->id][$conversation->useridfrom] = $conversation->useridfrom;
                    $individualmembers[$conversation->useridfrom] = $conversation->useridfrom;
                } else {
                    $individualconversations[] = $conversation->id;
                }
            } else if ($conversation->conversationtype == self::MESSAGE_CONVERSATION_TYPE_GROUP) {
                // If we have a recent message, the sender is our member.
                if (!is_null($conversation->useridfrom)) {
                    $members[$conversation->id][$conversation->useridfrom] = $conversation->useridfrom;
                    $groupmembers[$conversation->useridfrom] = $conversation->useridfrom;
                }
            } else if ($conversation->conversationtype == self::MESSAGE_CONVERSATION_TYPE_SELF) {
                $selfconversations[$conversation->id] = $conversation->id;
                $members[$conversation->id][$userid] = $userid;
                $selfmembers[$userid] = $userid;
            }
        }
        // If we need to fetch any member information for any of the individual conversations.
        // This is the case if any of the individual conversations have a recent message sent by the current user.
        if (!empty($individualconversations)) {
            list ($icidinsql, $icidinparams) = $DB->get_in_or_equal($individualconversations, SQL_PARAMS_NAMED, 'convid');
            $indmembersql = "SELECT mcm.id, mcm.conversationid, mcm.userid
                        FROM {message_conversation_members} mcm
                       WHERE mcm.conversationid $icidinsql
                       AND mcm.userid != :userid
                       ORDER BY mcm.id";
            $indmemberparams = array_merge($icidinparams, ['userid' => $userid]);
            $conversationmembers = $DB->get_records_sql($indmembersql, $indmemberparams);

            foreach ($conversationmembers as $mid => $member) {
                $members[$member->conversationid][$member->userid] = $member->userid;
                $individualmembers[$member->userid] = $member->userid;
            }
        }

        // We could fail early here if we're sure that:
        // a) we have no otherusers for all the conversations (users may have been deleted)
        // b) we're sure that all conversations are individual (1:1).

        // We need to pull out the list of users info corresponding to the memberids in the conversations.This
        // needs to be done in a separate query to avoid doing a join on the messages tables and the user
        // tables because on large sites these tables are massive which results in extremely slow
        // performance (typically due to join buffer exhaustion).
        if (!empty($individualmembers) || !empty($groupmembers) || !empty($selfmembers)) {
            // Now, we want to remove any duplicates from the group members array. For individual members we will
            // be doing a more extensive call as we want their contact requests as well as privacy information,
            // which is not necessary for group conversations.
            $diffgroupmembers = array_diff($groupmembers, $individualmembers);

            $individualmemberinfo = helper::get_member_info($userid, $individualmembers, true, true);
            $groupmemberinfo = helper::get_member_info($userid, $diffgroupmembers);
            $selfmemberinfo = helper::get_member_info($userid, $selfmembers);

            // Don't use array_merge, as we lose array keys.
            $memberinfo = $individualmemberinfo + $groupmemberinfo + $selfmemberinfo;

            if (empty($memberinfo)) {
                return [];
            }

            // Update the members array with the member information.
            $deletedmembers = [];
            foreach ($members as $convid => $memberarr) {
                foreach ($memberarr as $key => $memberid) {
                    if (array_key_exists($memberid, $memberinfo)) {
                        // If the user is deleted, remember that.
                        if ($memberinfo[$memberid]->isdeleted) {
                            $deletedmembers[$convid][] = $memberid;
                        }

                        $members[$convid][$key] = clone $memberinfo[$memberid];

                        if ($conversations[$convid]->conversationtype == self::MESSAGE_CONVERSATION_TYPE_GROUP) {
                            // Remove data we don't need for group.
                            $members[$convid][$key]->requirescontact = null;
                            $members[$convid][$key]->canmessage = null;
                            $members[$convid][$key]->contactrequests = [];
                        }
                    } else { // Remove all members and individual conversations where we could not get the member's information.
                        unset($members[$convid][$key]);

                        // If the conversation is an individual conversation, then we should remove it from the list.
                        if ($conversations[$convid]->conversationtype == self::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL) {
                            unset($conversations[$convid]);
                        }
                    }
                }
            }
        }

        // MEMBER COUNT.
        $cids = array_column($conversations, 'id');
        list ($cidinsql, $cidinparams) = $DB->get_in_or_equal($cids, SQL_PARAMS_NAMED, 'convid');
        $membercountsql = "SELECT conversationid, count(DISTINCT userid) AS membercount
                             FROM {message_conversation_members} mcm
                            WHERE mcm.conversationid $cidinsql
                         GROUP BY mcm.conversationid";
        $membercounts = $DB->get_records_sql($membercountsql, $cidinparams);

        // UNREAD MESSAGE COUNT.
        // Finally, let's get the unread messages count for this user so that we can add it
        // to the conversation. Remember we need to ignore the messages the user sent.
        $unreadcountssql = 'SELECT m.conversationid, count(m.id) as unreadcount
                              FROM {messages} m
                        INNER JOIN {message_conversations} mc
                                ON mc.id = m.conversationid
                        INNER JOIN {message_conversation_members} mcm
                                ON m.conversationid = mcm.conversationid
                         LEFT JOIN {message_user_actions} mua
                                ON (mua.messageid = m.id AND mua.userid = ? AND
                                   (mua.action = ? OR mua.action = ?))
                             WHERE mcm.userid = ?
                               AND m.useridfrom != ?
                               AND mua.id is NULL
                          GROUP BY m.conversationid';
        $unreadcounts = $DB->get_records_sql($unreadcountssql, [$userid, self::MESSAGE_ACTION_READ, self::MESSAGE_ACTION_DELETED,
            $userid, $userid]);

        // For the self-conversations, get the total number of messages (to know if the conversation is new or it has been emptied).
        $selfmessagessql = "SELECT COUNT(m.id)
                              FROM {messages} m
                        INNER JOIN {message_conversations} mc
                                ON mc.id = m.conversationid
                             WHERE mc.type = ? AND convhash = ?";
        $selfmessagestotal = $DB->count_records_sql(
            $selfmessagessql,
            [self::MESSAGE_CONVERSATION_TYPE_SELF, helper::get_conversation_hash([$userid])]
        );

        // Because we'll be calling format_string on each conversation name and passing contexts, we preload them here.
        // This warms the cache and saves potentially hitting the DB once for each context fetch below.
        \context_helper::preload_contexts_by_id(array_column($conversations, 'contextid'));

        // Now, create the final return structure.
        $arrconversations = [];
        foreach ($conversations as $conversation) {
            // Do not include any individual which do not contain a recent message for the user.
            // This happens if the user has deleted all messages.
            // Exclude the self-conversations with messages but without a recent message because the user has deleted all them.
            // Self-conversations without any message should be included, to display them first time they are created.
            // Group conversations with deleted users or no messages are always returned.
            if ($conversation->conversationtype == self::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL && empty($conversation->messageid) ||
                   ($conversation->conversationtype == self::MESSAGE_CONVERSATION_TYPE_SELF && empty($conversation->messageid)
                    && $selfmessagestotal > 0)) {
                continue;
            }

            $conv = new \stdClass();
            $conv->id = $conversation->id;

            // Name should be formatted and depends on the context the conversation resides in.
            // If not set, the context is always context_user.
            if (is_null($conversation->contextid)) {
                $convcontext = \context_user::instance($userid);
                // We'll need to check the capability to delete messages for all users in context system when contextid is null.
                $contexttodeletemessageforall = \context_system::instance();
            } else {
                $convcontext = \context::instance_by_id($conversation->contextid);
                $contexttodeletemessageforall = $convcontext;
            }
            $conv->name = format_string($conversation->conversationname, true, ['context' => $convcontext]);

            $conv->subname = $convextrafields[$conv->id]['subname'] ?? null;
            $conv->imageurl = $convextrafields[$conv->id]['imageurl'] ?? null;
            $conv->type = $conversation->conversationtype;
            $conv->membercount = $membercounts[$conv->id]->membercount;
            $conv->isfavourite = in_array($conv->id, $favouriteconversationids);
            $conv->isread = isset($unreadcounts[$conv->id]) ? false : true;
            $conv->unreadcount = isset($unreadcounts[$conv->id]) ? $unreadcounts[$conv->id]->unreadcount : null;
            $conv->ismuted = $conversation->ismuted ? true : false;
            $conv->members = $members[$conv->id];

            // Add the most recent message information.
            $conv->messages = [];
            // Add if the user has to allow delete messages for all users in the conversation.
            $conv->candeletemessagesforallusers = has_capability('moodle/site:deleteanymessage',  $contexttodeletemessageforall);
            if ($conversation->smallmessage) {
                $msg = new \stdClass();
                $msg->id = $conversation->messageid;
                $msg->text = message_format_message_text($conversation);
                $msg->useridfrom = $conversation->useridfrom;
                $msg->timecreated = $conversation->timecreated;
                $conv->messages[] = $msg;
            }

            $arrconversations[] = $conv;
        }
        return $arrconversations;
    }

    /**
     * Returns all conversations between two users
     *
     * @param int $userid1 One of the user's id
     * @param int $userid2 The other user's id
     * @param int $limitfrom
     * @param int $limitnum
     * @return array
     * @throws \dml_exception
     */
    public static function get_conversations_between_users(int $userid1, int $userid2,
                                                           int $limitfrom = 0, int $limitnum = 20) : array {

        global $DB;

        if ($userid1 == $userid2) {
            return array();
        }

        // Get all conversation where both user1 and user2 are members.
        // TODO: Add subname value. Waiting for definite table structure.
        $sql = "SELECT mc.id, mc.type, mc.name, mc.timecreated
                  FROM {message_conversations} mc
            INNER JOIN {message_conversation_members} mcm1
                    ON mc.id = mcm1.conversationid
            INNER JOIN {message_conversation_members} mcm2
                    ON mc.id = mcm2.conversationid
                 WHERE mcm1.userid = :userid1
                   AND mcm2.userid = :userid2
                   AND mc.enabled != 0
              ORDER BY mc.timecreated DESC";

        return $DB->get_records_sql($sql, array('userid1' => $userid1, 'userid2' => $userid2), $limitfrom, $limitnum);
    }

    /**
     * Return a conversation.
     *
     * @param int $userid The user id to get the conversation for
     * @param int $conversationid The id of the conversation to fetch
     * @param bool $includecontactrequests Should contact requests be included between members
     * @param bool $includeprivacyinfo Should privacy info be included between members
     * @param int $memberlimit Limit number of members to load
     * @param int $memberoffset Offset members by this amount
     * @param int $messagelimit Limit number of messages to load
     * @param int $messageoffset Offset the messages
     * @param bool $newestmessagesfirst Order messages by newest first
     * @return \stdClass
     */
    public static function get_conversation(
        int $userid,
        int $conversationid,
        bool $includecontactrequests = false,
        bool $includeprivacyinfo = false,
        int $memberlimit = 0,
        int $memberoffset = 0,
        int $messagelimit = 0,
        int $messageoffset = 0,
        bool $newestmessagesfirst = true
    ) {
        global $USER, $DB;

        $systemcontext = \context_system::instance();
        $canreadallmessages = has_capability('moodle/site:readallmessages', $systemcontext);
        if (($USER->id != $userid) && !$canreadallmessages) {
            throw new \moodle_exception('You do not have permission to perform this action.');
        }

        $conversation = $DB->get_record('message_conversations', ['id' => $conversationid]);
        if (!$conversation) {
            return null;
        }

        // Get the context of the conversation. This will be used to check whether the conversation is a favourite.
        // This will be either 'user' (for individual conversations) or, in the case of linked conversations,
        // the context stored in the record.
        $userctx = \context_user::instance($userid);
        $conversationctx = empty($conversation->contextid) ? $userctx : \context::instance_by_id($conversation->contextid);

        $isconversationmember = $DB->record_exists(
            'message_conversation_members',
            [
                'conversationid' => $conversationid,
                'userid' => $userid
            ]
        );

        if (!$isconversationmember && !$canreadallmessages) {
            throw new \moodle_exception('You do not have permission to view this conversation.');
        }

        $members = self::get_conversation_members(
            $userid,
            $conversationid,
            $includecontactrequests,
            $includeprivacyinfo,
            $memberoffset,
            $memberlimit
        );
        if ($conversation->type != self::MESSAGE_CONVERSATION_TYPE_SELF) {
            // Strip out the requesting user to match what get_conversations does, except for self-conversations.
            $members = array_filter($members, function($member) use ($userid) {
                return $member->id != $userid;
            });
        }

        $messages = self::get_conversation_messages(
            $userid,
            $conversationid,
            $messageoffset,
            $messagelimit,
            $newestmessagesfirst ? 'timecreated DESC' : 'timecreated ASC'
        );

        $service = \core_favourites\service_factory::get_service_for_user_context(\context_user::instance($userid));
        $isfavourite = $service->favourite_exists('core_message', 'message_conversations', $conversationid, $conversationctx);

        $convextrafields = self::get_linked_conversation_extra_fields([$conversation]);
        $subname = isset($convextrafields[$conversationid]) ? $convextrafields[$conversationid]['subname'] : null;
        $imageurl = isset($convextrafields[$conversationid]) ? $convextrafields[$conversationid]['imageurl'] : null;

        $unreadcountssql = 'SELECT count(m.id)
                              FROM {messages} m
                        INNER JOIN {message_conversations} mc
                                ON mc.id = m.conversationid
                         LEFT JOIN {message_user_actions} mua
                                ON (mua.messageid = m.id AND mua.userid = ? AND
                                   (mua.action = ? OR mua.action = ?))
                             WHERE m.conversationid = ?
                               AND m.useridfrom != ?
                               AND mua.id is NULL';
        $unreadcount = $DB->count_records_sql(
            $unreadcountssql,
            [
                $userid,
                self::MESSAGE_ACTION_READ,
                self::MESSAGE_ACTION_DELETED,
                $conversationid,
                $userid
            ]
        );

        $membercount = $DB->count_records('message_conversation_members', ['conversationid' => $conversationid]);

        $ismuted = false;
        if ($DB->record_exists('message_conversation_actions', ['userid' => $userid,
                'conversationid' => $conversationid, 'action' => self::CONVERSATION_ACTION_MUTED])) {
            $ismuted = true;
        }

        // Get the context of the conversation. This will be used to check if the user can delete all messages in the conversation.
        $deleteallcontext = empty($conversation->contextid) ? $systemcontext : \context::instance_by_id($conversation->contextid);

        return (object) [
            'id' => $conversation->id,
            'name' => $conversation->name,
            'subname' => $subname,
            'imageurl' => $imageurl,
            'type' => $conversation->type,
            'membercount' => $membercount,
            'isfavourite' => $isfavourite,
            'isread' => empty($unreadcount),
            'unreadcount' => $unreadcount,
            'ismuted' => $ismuted,
            'members' => $members,
            'messages' => $messages['messages'],
            'candeletemessagesforallusers' => has_capability('moodle/site:deleteanymessage', $deleteallcontext)
        ];
    }

    /**
     * Mark a conversation as a favourite for the given user.
     *
     * @param int $conversationid the id of the conversation to mark as a favourite.
     * @param int $userid the id of the user to whom the favourite belongs.
     * @return favourite the favourite object.
     * @throws \moodle_exception if the user or conversation don't exist.
     */
    public static function set_favourite_conversation(int $conversationid, int $userid) : favourite {
        global $DB;

        if (!self::is_user_in_conversation($userid, $conversationid)) {
            throw new \moodle_exception("Conversation doesn't exist or user is not a member");
        }
        // Get the context for this conversation.
        $conversation = $DB->get_record('message_conversations', ['id' => $conversationid]);
        $userctx = \context_user::instance($userid);
        if (empty($conversation->contextid)) {
            // When the conversation hasn't any contextid value defined, the favourite will be added to the user context.
            $conversationctx = $userctx;
        } else {
            // If the contextid is defined, the favourite will be added there.
            $conversationctx = \context::instance_by_id($conversation->contextid);
        }

        $ufservice = \core_favourites\service_factory::get_service_for_user_context($userctx);

        if ($favourite = $ufservice->get_favourite('core_message', 'message_conversations', $conversationid, $conversationctx)) {
            return $favourite;
        } else {
            return $ufservice->create_favourite('core_message', 'message_conversations', $conversationid, $conversationctx);
        }
    }

    /**
     * Unset a conversation as a favourite for the given user.
     *
     * @param int $conversationid the id of the conversation to unset as a favourite.
     * @param int $userid the id to whom the favourite belongs.
     * @throws \moodle_exception if the favourite does not exist for the user.
     */
    public static function unset_favourite_conversation(int $conversationid, int $userid) {
        global $DB;

        // Get the context for this conversation.
        $conversation = $DB->get_record('message_conversations', ['id' => $conversationid]);
        $userctx = \context_user::instance($userid);
        if (empty($conversation->contextid)) {
            // When the conversation hasn't any contextid value defined, the favourite will be added to the user context.
            $conversationctx = $userctx;
        } else {
            // If the contextid is defined, the favourite will be added there.
            $conversationctx = \context::instance_by_id($conversation->contextid);
        }

        $ufservice = \core_favourites\service_factory::get_service_for_user_context($userctx);
        $ufservice->delete_favourite('core_message', 'message_conversations', $conversationid, $conversationctx);
    }

    /**
     * Returns the contacts to display in the contacts area.
     *
     * TODO: This function should be removed once the related web service goes through final deprecation.
     * The related web service is data_for_messagearea_contacts.
     * Followup: MDL-63261
     *
     * @param int $userid The user id
     * @param int $limitfrom
     * @param int $limitnum
     * @return array
     */
    public static function get_contacts($userid, $limitfrom = 0, $limitnum = 0) {
        global $DB;

        $contactids = [];
        $sql = "SELECT mc.*
                  FROM {message_contacts} mc
                 WHERE mc.userid = ? OR mc.contactid = ?
              ORDER BY timecreated DESC";
        if ($contacts = $DB->get_records_sql($sql, [$userid, $userid], $limitfrom, $limitnum)) {
            foreach ($contacts as $contact) {
                if ($userid == $contact->userid) {
                    $contactids[] = $contact->contactid;
                } else {
                    $contactids[] = $contact->userid;
                }
            }
        }

        if (!empty($contactids)) {
            list($insql, $inparams) = $DB->get_in_or_equal($contactids);

            $sql = "SELECT u.*, mub.id as isblocked
                      FROM {user} u
                 LEFT JOIN {message_users_blocked} mub
                        ON u.id = mub.blockeduserid
                     WHERE u.id $insql";
            if ($contacts = $DB->get_records_sql($sql, $inparams)) {
                $arrcontacts = [];
                foreach ($contacts as $contact) {
                    $contact->blocked = $contact->isblocked ? 1 : 0;
                    $arrcontacts[] = helper::create_contact($contact);
                }

                return $arrcontacts;
            }
        }

        return [];
    }

    /**
     * Get the contacts for a given user.
     *
     * @param int $userid
     * @param int $limitfrom
     * @param int $limitnum
     * @return array An array of contacts
     */
    public static function get_user_contacts(int $userid, int $limitfrom = 0, int $limitnum = 0) {
        global $DB;

        $sql = "SELECT *
                  FROM {message_contacts} mc
                 WHERE mc.userid = ? OR mc.contactid = ?
              ORDER BY timecreated DESC, id ASC";
        if ($contacts = $DB->get_records_sql($sql, [$userid, $userid], $limitfrom, $limitnum)) {
            $userids = [];
            foreach ($contacts as $contact) {
                if ($contact->userid == $userid) {
                    $userids[] = $contact->contactid;
                } else {
                    $userids[] = $contact->userid;
                }
            }
            return helper::get_member_info($userid, $userids);
        }

        return [];
    }

    /**
     * Returns the contacts count.
     *
     * @param int $userid The user id
     * @return array
     */
    public static function count_contacts(int $userid) : int {
        global $DB;

        $sql = "SELECT COUNT(id)
                  FROM {message_contacts}
                 WHERE userid = ? OR contactid = ?";
        return $DB->count_records_sql($sql, [$userid, $userid]);
    }

    /**
     * Returns the an array of the users the given user is in a conversation
     * with who are a contact and the number of unread messages.
     *
     * @param int $userid The user id
     * @param int $limitfrom
     * @param int $limitnum
     * @return array
     */
    public static function get_contacts_with_unread_message_count($userid, $limitfrom = 0, $limitnum = 0) {
        global $DB;

        $userfields = \user_picture::fields('u', array('lastaccess'));
        $unreadcountssql = "SELECT $userfields, count(m.id) as messagecount
                              FROM {message_contacts} mc
                        INNER JOIN {user} u
                                ON (u.id = mc.contactid OR u.id = mc.userid)
                         LEFT JOIN {messages} m
                                ON ((m.useridfrom = mc.contactid OR m.useridfrom = mc.userid) AND m.useridfrom != ?)
                         LEFT JOIN {message_conversation_members} mcm
                                ON mcm.conversationid = m.conversationid AND mcm.userid = ? AND mcm.userid != m.useridfrom
                         LEFT JOIN {message_user_actions} mua
                                ON (mua.messageid = m.id AND mua.userid = ? AND mua.action = ?)
                         LEFT JOIN {message_users_blocked} mub
                                ON (mub.userid = ? AND mub.blockeduserid = u.id)
                             WHERE mua.id is NULL
                               AND mub.id is NULL
                               AND (mc.userid = ? OR mc.contactid = ?)
                               AND u.id != ?
                               AND u.deleted = 0
                          GROUP BY $userfields";

        return $DB->get_records_sql($unreadcountssql, [$userid, $userid, $userid, self::MESSAGE_ACTION_READ,
            $userid, $userid, $userid, $userid], $limitfrom, $limitnum);
    }

    /**
     * Returns the an array of the users the given user is in a conversation
     * with who are not a contact and the number of unread messages.
     *
     * @param int $userid The user id
     * @param int $limitfrom
     * @param int $limitnum
     * @return array
     */
    public static function get_non_contacts_with_unread_message_count($userid, $limitfrom = 0, $limitnum = 0) {
        global $DB;

        $userfields = \user_picture::fields('u', array('lastaccess'));
        $unreadcountssql = "SELECT $userfields, count(m.id) as messagecount
                              FROM {user} u
                        INNER JOIN {messages} m
                                ON m.useridfrom = u.id
                        INNER JOIN {message_conversation_members} mcm
                                ON mcm.conversationid = m.conversationid
                         LEFT JOIN {message_user_actions} mua
                                ON (mua.messageid = m.id AND mua.userid = ? AND mua.action = ?)
                         LEFT JOIN {message_contacts} mc
                                ON (mc.userid = ? AND mc.contactid = u.id)
                         LEFT JOIN {message_users_blocked} mub
                                ON (mub.userid = ? AND mub.blockeduserid = u.id)
                             WHERE mcm.userid = ?
                               AND mcm.userid != m.useridfrom
                               AND mua.id is NULL
                               AND mub.id is NULL
                               AND mc.id is NULL
                               AND u.deleted = 0
                          GROUP BY $userfields";

        return $DB->get_records_sql($unreadcountssql, [$userid, self::MESSAGE_ACTION_READ, $userid, $userid, $userid],
            $limitfrom, $limitnum);
    }

    /**
     * Returns the messages to display in the message area.
     *
     * TODO: This function should be removed once the related web service goes through final deprecation.
     * The related web service is data_for_messagearea_messages.
     * Followup: MDL-63261
     *
     * @param int $userid the current user
     * @param int $otheruserid the other user
     * @param int $limitfrom
     * @param int $limitnum
     * @param string $sort
     * @param int $timefrom the time from the message being sent
     * @param int $timeto the time up until the message being sent
     * @return array
     */
    public static function get_messages($userid, $otheruserid, $limitfrom = 0, $limitnum = 0,
            $sort = 'timecreated ASC', $timefrom = 0, $timeto = 0) {

        if (!empty($timefrom)) {
            // Get the conversation between userid and otheruserid.
            $userids = [$userid, $otheruserid];
            if (!$conversationid = self::get_conversation_between_users($userids)) {
                // This method was always used for individual conversations.
                $conversation = self::create_conversation(self::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL, $userids);
                $conversationid = $conversation->id;
            }

            // Check the cache to see if we even need to do a DB query.
            $cache = \cache::make('core', 'message_time_last_message_between_users');
            $key = helper::get_last_message_time_created_cache_key($conversationid);
            $lastcreated = $cache->get($key);

            // The last known message time is earlier than the one being requested so we can
            // just return an empty result set rather than having to query the DB.
            if ($lastcreated && $lastcreated < $timefrom) {
                return [];
            }
        }

        $arrmessages = array();
        if ($messages = helper::get_messages($userid, $otheruserid, 0, $limitfrom, $limitnum,
                                             $sort, $timefrom, $timeto)) {
            $arrmessages = helper::create_messages($userid, $messages);
        }

        return $arrmessages;
    }

    /**
     * Returns the messages for the defined conversation.
     *
     * @param  int $userid The current user.
     * @param  int $convid The conversation where the messages belong. Could be an object or just the id.
     * @param  int $limitfrom Return a subset of records, starting at this point (optional).
     * @param  int $limitnum Return a subset comprising this many records in total (optional, required if $limitfrom is set).
     * @param  string $sort The column name to order by including optionally direction.
     * @param  int $timefrom The time from the message being sent.
     * @param  int $timeto The time up until the message being sent.
     * @return array of messages
     */
    public static function get_conversation_messages(int $userid, int $convid, int $limitfrom = 0, int $limitnum = 0,
        string $sort = 'timecreated ASC', int $timefrom = 0, int $timeto = 0) : array {

        if (!empty($timefrom)) {
            // Check the cache to see if we even need to do a DB query.
            $cache = \cache::make('core', 'message_time_last_message_between_users');
            $key = helper::get_last_message_time_created_cache_key($convid);
            $lastcreated = $cache->get($key);

            // The last known message time is earlier than the one being requested so we can
            // just return an empty result set rather than having to query the DB.
            if ($lastcreated && $lastcreated < $timefrom) {
                return [];
            }
        }

        $messages = helper::get_conversation_messages($userid, $convid, 0, $limitfrom, $limitnum, $sort, $timefrom, $timeto);
        return helper::format_conversation_messages($userid, $convid, $messages);
    }

    /**
     * Returns the most recent message between two users.
     *
     * TODO: This function should be removed once the related web service goes through final deprecation.
     * The related web service is data_for_messagearea_get_most_recent_message.
     * Followup: MDL-63261
     *
     * @param int $userid the current user
     * @param int $otheruserid the other user
     * @return \stdClass|null
     */
    public static function get_most_recent_message($userid, $otheruserid) {
        // We want two messages here so we get an accurate 'blocktime' value.
        if ($messages = helper::get_messages($userid, $otheruserid, 0, 0, 2, 'timecreated DESC')) {
            // Swap the order so we now have them in historical order.
            $messages = array_reverse($messages);
            $arrmessages = helper::create_messages($userid, $messages);
            return array_pop($arrmessages);
        }

        return null;
    }

    /**
     * Returns the most recent message in a conversation.
     *
     * @param int $convid The conversation identifier.
     * @param int $currentuserid The current user identifier.
     * @return \stdClass|null The most recent message.
     */
    public static function get_most_recent_conversation_message(int $convid, int $currentuserid = 0) {
        global $USER;

        if (empty($currentuserid)) {
            $currentuserid = $USER->id;
        }

        if ($messages = helper::get_conversation_messages($currentuserid, $convid, 0, 0, 1, 'timecreated DESC')) {
            $convmessages = helper::format_conversation_messages($currentuserid, $convid, $messages);
            return array_pop($convmessages['messages']);
        }

        return null;
    }

    /**
     * Returns the profile information for a contact for a user.
     *
     * TODO: This function should be removed once the related web service goes through final deprecation.
     * The related web service is data_for_messagearea_get_profile.
     * Followup: MDL-63261
     *
     * @param int $userid The user id
     * @param int $otheruserid The id of the user whose profile we want to view.
     * @return \stdClass
     */
    public static function get_profile($userid, $otheruserid) {
        global $CFG, $PAGE;

        require_once($CFG->dirroot . '/user/lib.php');

        $user = \core_user::get_user($otheruserid, '*', MUST_EXIST);

        // Create the data we are going to pass to the renderable.
        $data = new \stdClass();
        $data->userid = $otheruserid;
        $data->fullname = fullname($user);
        $data->city = '';
        $data->country = '';
        $data->email = '';
        $data->isonline = null;
        // Get the user picture data - messaging has always shown these to the user.
        $userpicture = new \user_picture($user);
        $userpicture->size = 1; // Size f1.
        $data->profileimageurl = $userpicture->get_url($PAGE)->out(false);
        $userpicture->size = 0; // Size f2.
        $data->profileimageurlsmall = $userpicture->get_url($PAGE)->out(false);

        $userfields = user_get_user_details($user, null, array('city', 'country', 'email', 'lastaccess'));
        if ($userfields) {
            if (isset($userfields['city'])) {
                $data->city = $userfields['city'];
            }
            if (isset($userfields['country'])) {
                $data->country = $userfields['country'];
            }
            if (isset($userfields['email'])) {
                $data->email = $userfields['email'];
            }
            if (isset($userfields['lastaccess'])) {
                $data->isonline = helper::is_online($userfields['lastaccess']);
            }
        }

        $data->isblocked = self::is_blocked($userid, $otheruserid);
        $data->iscontact = self::is_contact($userid, $otheruserid);

        return $data;
    }

    /**
     * Checks if a user can delete messages they have either received or sent.
     *
     * @param int $userid The user id of who we want to delete the messages for (this may be done by the admin
     *  but will still seem as if it was by the user)
     * @param int $conversationid The id of the conversation
     * @return bool Returns true if a user can delete the conversation, false otherwise.
     */
    public static function can_delete_conversation(int $userid, int $conversationid = null) : bool {
        global $USER;

        if (is_null($conversationid)) {
            debugging('\core_message\api::can_delete_conversation() now expects a \'conversationid\' to be passed.',
                DEBUG_DEVELOPER);
            return false;
        }

        $systemcontext = \context_system::instance();

        if (has_capability('moodle/site:deleteanymessage', $systemcontext)) {
            return true;
        }

        if (!self::is_user_in_conversation($userid, $conversationid)) {
            return false;
        }

        if (has_capability('moodle/site:deleteownmessage', $systemcontext) &&
                $USER->id == $userid) {
            return true;
        }

        return false;
    }

    /**
     * Deletes a conversation.
     *
     * This function does not verify any permissions.
     *
     * @deprecated since 3.6
     * @param int $userid The user id of who we want to delete the messages for (this may be done by the admin
     *  but will still seem as if it was by the user)
     * @param int $otheruserid The id of the other user in the conversation
     * @return bool
     */
    public static function delete_conversation($userid, $otheruserid) {
        debugging('\core_message\api::delete_conversation() is deprecated, please use ' .
            '\core_message\api::delete_conversation_by_id() instead.', DEBUG_DEVELOPER);

        $conversationid = self::get_conversation_between_users([$userid, $otheruserid]);

        // If there is no conversation, there is nothing to do.
        if (!$conversationid) {
            return true;
        }

        self::delete_conversation_by_id($userid, $conversationid);

        return true;
    }

    /**
     * Deletes a conversation for a specified user.
     *
     * This function does not verify any permissions.
     *
     * @param int $userid The user id of who we want to delete the messages for (this may be done by the admin
     *  but will still seem as if it was by the user)
     * @param int $conversationid The id of the other user in the conversation
     */
    public static function delete_conversation_by_id(int $userid, int $conversationid) {
        global $DB, $USER;

        // Get all messages belonging to this conversation that have not already been deleted by this user.
        $sql = "SELECT m.*
                 FROM {messages} m
           INNER JOIN {message_conversations} mc
                   ON m.conversationid = mc.id
            LEFT JOIN {message_user_actions} mua
                   ON (mua.messageid = m.id AND mua.userid = ? AND mua.action = ?)
                WHERE mua.id is NULL
                  AND mc.id = ?
             ORDER BY m.timecreated ASC";
        $messages = $DB->get_records_sql($sql, [$userid, self::MESSAGE_ACTION_DELETED, $conversationid]);

        // Ok, mark these as deleted.
        foreach ($messages as $message) {
            $mua = new \stdClass();
            $mua->userid = $userid;
            $mua->messageid = $message->id;
            $mua->action = self::MESSAGE_ACTION_DELETED;
            $mua->timecreated = time();
            $mua->id = $DB->insert_record('message_user_actions', $mua);

            \core\event\message_deleted::create_from_ids($userid, $USER->id,
                $message->id, $mua->id)->trigger();
        }
    }

    /**
     * Returns the count of unread conversations (collection of messages from a single user) for
     * the given user.
     *
     * @param \stdClass $user the user who's conversations should be counted
     * @return int the count of the user's unread conversations
     */
    public static function count_unread_conversations($user = null) {
        global $USER, $DB;

        if (empty($user)) {
            $user = $USER;
        }

        $sql = "SELECT COUNT(DISTINCT(m.conversationid))
                  FROM {messages} m
            INNER JOIN {message_conversations} mc
                    ON m.conversationid = mc.id
            INNER JOIN {message_conversation_members} mcm
                    ON mc.id = mcm.conversationid
             LEFT JOIN {message_user_actions} mua
                    ON (mua.messageid = m.id AND mua.userid = ? AND mua.action = ?)
                 WHERE mcm.userid = ?
                   AND mc.enabled = ?
                   AND mcm.userid != m.useridfrom
                   AND mua.id is NULL";

        return $DB->count_records_sql($sql, [$user->id, self::MESSAGE_ACTION_READ, $user->id,
            self::MESSAGE_CONVERSATION_ENABLED]);
    }

    /**
     * Checks if a user can mark all messages as read.
     *
     * @param int $userid The user id of who we want to mark the messages for
     * @param int $conversationid The id of the conversation
     * @return bool true if user is permitted, false otherwise
     * @since 3.6
     */
    public static function can_mark_all_messages_as_read(int $userid, int $conversationid) : bool {
        global $USER;

        $systemcontext = \context_system::instance();

        if (has_capability('moodle/site:readallmessages', $systemcontext)) {
            return true;
        }

        if (!self::is_user_in_conversation($userid, $conversationid)) {
            return false;
        }

        if ($USER->id == $userid) {
            return true;
        }

        return false;
    }

    /**
     * Returns the count of conversations (collection of messages from a single user) for
     * the given user.
     *
     * @param int $userid The user whose conversations should be counted.
     * @return array the array of conversations counts, indexed by type.
     */
    public static function get_conversation_counts(int $userid) : array {
        global $DB;
        self::lazy_create_self_conversation($userid);

        // Some restrictions we need to be aware of:
        // - Individual conversations containing soft-deleted user must be counted.
        // - Individual conversations containing only deleted messages must NOT be counted.
        // - Self-conversations with 0 messages must be counted.
        // - Self-conversations containing only deleted messages must NOT be counted.
        // - Group conversations with 0 messages must be counted.
        // - Linked conversations which are disabled (enabled = 0) must NOT be counted.
        // - Any type of conversation can be included in the favourites count, however, the type counts and the favourites count
        // are mutually exclusive; any conversations which are counted in favourites cannot be counted elsewhere.

        // First, ask the favourites service to give us the join SQL for favourited conversations,
        // so we can include favourite information in the query.
        $usercontext = \context_user::instance($userid);
        $favservice = \core_favourites\service_factory::get_service_for_user_context($usercontext);
        list($favsql, $favparams) = $favservice->get_join_sql_by_type('core_message', 'message_conversations', 'fav', 'mc.id');

        $sql = "SELECT mc.type, fav.itemtype, COUNT(DISTINCT mc.id) as count, MAX(maxvisibleconvmessage.convid) as maxconvidmessage
                  FROM {message_conversations} mc
            INNER JOIN {message_conversation_members} mcm
                    ON mcm.conversationid = mc.id
             LEFT JOIN (
                              SELECT m.conversationid as convid, MAX(m.timecreated) as maxtime
                                FROM {messages} m
                          INNER JOIN {message_conversation_members} mcm
                                  ON mcm.conversationid = m.conversationid
                           LEFT JOIN {message_user_actions} mua
                                  ON (mua.messageid = m.id AND mua.userid = :userid AND mua.action = :action)
                               WHERE mua.id is NULL
                                 AND mcm.userid = :userid2
                            GROUP BY m.conversationid
                       ) maxvisibleconvmessage
                    ON maxvisibleconvmessage.convid = mc.id
               $favsql
                 WHERE mcm.userid = :userid3
                   AND mc.enabled = :enabled
                   AND (
                          (mc.type = :individualtype AND maxvisibleconvmessage.convid IS NOT NULL) OR
                          (mc.type = :grouptype) OR
                          (mc.type = :selftype)
                       )
              GROUP BY mc.type, fav.itemtype
              ORDER BY mc.type ASC";

        $params = [
            'userid' => $userid,
            'userid2' => $userid,
            'userid3' => $userid,
            'userid4' => $userid,
            'userid5' => $userid,
            'action' => self::MESSAGE_ACTION_DELETED,
            'enabled' => self::MESSAGE_CONVERSATION_ENABLED,
            'individualtype' => self::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            'grouptype' => self::MESSAGE_CONVERSATION_TYPE_GROUP,
            'selftype' => self::MESSAGE_CONVERSATION_TYPE_SELF,
        ] + $favparams;

        // Assemble the return array.
        $counts = [
            'favourites' => 0,
            'types' => [
                self::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 0,
                self::MESSAGE_CONVERSATION_TYPE_GROUP => 0,
                self::MESSAGE_CONVERSATION_TYPE_SELF => 0
            ]
        ];

        // For the self-conversations, get the total number of messages (to know if the conversation is new or it has been emptied).
        $selfmessagessql = "SELECT COUNT(m.id)
                              FROM {messages} m
                        INNER JOIN {message_conversations} mc
                                ON mc.id = m.conversationid
                             WHERE mc.type = ? AND convhash = ?";
        $selfmessagestotal = $DB->count_records_sql(
            $selfmessagessql,
            [self::MESSAGE_CONVERSATION_TYPE_SELF, helper::get_conversation_hash([$userid])]
        );

        $countsrs = $DB->get_recordset_sql($sql, $params);
        foreach ($countsrs as $key => $val) {
            // Empty self-conversations with deleted messages should be excluded.
            if ($val->type == self::MESSAGE_CONVERSATION_TYPE_SELF && empty($val->maxconvidmessage) && $selfmessagestotal > 0) {
                continue;
            }
            if (!empty($val->itemtype)) {
                $counts['favourites'] += $val->count;
                continue;
            }
            $counts['types'][$val->type] = $val->count;
        }
        $countsrs->close();

        return $counts;
    }

    /**
     * Marks all messages being sent to a user in a particular conversation.
     *
     * If $conversationdid is null then it marks all messages as read sent to $userid.
     *
     * @param int $userid
     * @param int|null $conversationid The conversation the messages belong to mark as read, if null mark all
     */
    public static function mark_all_messages_as_read($userid, $conversationid = null) {
        global $DB;

        $messagesql = "SELECT m.*
                         FROM {messages} m
                   INNER JOIN {message_conversations} mc
                           ON mc.id = m.conversationid
                   INNER JOIN {message_conversation_members} mcm
                           ON mcm.conversationid = mc.id
                    LEFT JOIN {message_user_actions} mua
                           ON (mua.messageid = m.id AND mua.userid = ? AND mua.action = ?)
                        WHERE mua.id is NULL
                          AND mcm.userid = ?
                          AND m.useridfrom != ?";
        $messageparams = [];
        $messageparams[] = $userid;
        $messageparams[] = self::MESSAGE_ACTION_READ;
        $messageparams[] = $userid;
        $messageparams[] = $userid;
        if (!is_null($conversationid)) {
            $messagesql .= " AND mc.id = ?";
            $messageparams[] = $conversationid;
        }

        $messages = $DB->get_recordset_sql($messagesql, $messageparams);
        foreach ($messages as $message) {
            self::mark_message_as_read($userid, $message);
        }
        $messages->close();
    }

    /**
     * Marks all notifications being sent from one user to another user as read.
     *
     * If the from user is null then it marks all notifications as read sent to the to user.
     *
     * @param int $touserid the id of the message recipient
     * @param int|null $fromuserid the id of the message sender, null if all messages
     * @return void
     */
    public static function mark_all_notifications_as_read($touserid, $fromuserid = null) {
        global $DB;

        $notificationsql = "SELECT n.*
                              FROM {notifications} n
                             WHERE useridto = ?
                               AND timeread is NULL";
        $notificationsparams = [$touserid];
        if (!empty($fromuserid)) {
            $notificationsql .= " AND useridfrom = ?";
            $notificationsparams[] = $fromuserid;
        }

        $notifications = $DB->get_recordset_sql($notificationsql, $notificationsparams);
        foreach ($notifications as $notification) {
            self::mark_notification_as_read($notification);
        }
        $notifications->close();
    }

    /**
     * Marks ALL messages being sent from $fromuserid to $touserid as read.
     *
     * Can be filtered by type.
     *
     * @deprecated since 3.5
     * @param int $touserid the id of the message recipient
     * @param int $fromuserid the id of the message sender
     * @param string $type filter the messages by type, either MESSAGE_TYPE_NOTIFICATION, MESSAGE_TYPE_MESSAGE or '' for all.
     * @return void
     */
    public static function mark_all_read_for_user($touserid, $fromuserid = 0, $type = '') {
        debugging('\core_message\api::mark_all_read_for_user is deprecated. Please either use ' .
            '\core_message\api::mark_all_notifications_read_for_user or \core_message\api::mark_all_messages_read_for_user',
            DEBUG_DEVELOPER);

        $type = strtolower($type);

        $conversationid = null;
        $ignoremessages = false;
        if (!empty($fromuserid)) {
            $conversationid = self::get_conversation_between_users([$touserid, $fromuserid]);
            if (!$conversationid) { // If there is no conversation between the users then there are no messages to mark.
                $ignoremessages = true;
            }
        }

        if (!empty($type)) {
            if ($type == MESSAGE_TYPE_NOTIFICATION) {
                self::mark_all_notifications_as_read($touserid, $fromuserid);
            } else if ($type == MESSAGE_TYPE_MESSAGE) {
                if (!$ignoremessages) {
                    self::mark_all_messages_as_read($touserid, $conversationid);
                }
            }
        } else { // We want both.
            self::mark_all_notifications_as_read($touserid, $fromuserid);
            if (!$ignoremessages) {
                self::mark_all_messages_as_read($touserid, $conversationid);
            }
        }
    }

    /**
     * Returns message preferences.
     *
     * @param array $processors
     * @param array $providers
     * @param \stdClass $user
     * @return \stdClass
     * @since 3.2
     */
    public static function get_all_message_preferences($processors, $providers, $user) {
        $preferences = helper::get_providers_preferences($providers, $user->id);
        $preferences->userdefaultemail = $user->email; // May be displayed by the email processor.

        // For every processors put its options on the form (need to get function from processor's lib.php).
        foreach ($processors as $processor) {
            $processor->object->load_data($preferences, $user->id);
        }

        // Load general messaging preferences.
        $preferences->blocknoncontacts = self::get_user_privacy_messaging_preference($user->id);
        $preferences->mailformat = $user->mailformat;
        $preferences->mailcharset = get_user_preferences('mailcharset', '', $user->id);

        return $preferences;
    }

    /**
     * Count the number of users blocked by a user.
     *
     * @param \stdClass $user The user object
     * @return int the number of blocked users
     */
    public static function count_blocked_users($user = null) {
        global $USER, $DB;

        if (empty($user)) {
            $user = $USER;
        }

        $sql = "SELECT count(mub.id)
                  FROM {message_users_blocked} mub
                 WHERE mub.userid = :userid";
        return $DB->count_records_sql($sql, array('userid' => $user->id));
    }

    /**
     * Determines if a user is permitted to send another user a private message.
     * If no sender is provided then it defaults to the logged in user.
     *
     * @param \stdClass $recipient The user object.
     * @param \stdClass|null $sender The user object.
     * @return bool true if user is permitted, false otherwise.
     */
    public static function can_post_message($recipient, $sender = null) {
        global $USER;

        if (is_null($sender)) {
            // The message is from the logged in user, unless otherwise specified.
            $sender = $USER;
        }

        $systemcontext = \context_system::instance();
        if (!has_capability('moodle/site:sendmessage', $systemcontext, $sender)) {
            return false;
        }

        if (has_capability('moodle/site:readallmessages', $systemcontext, $sender->id)) {
            return true;
        }

        // Check if the recipient can be messaged by the sender.
        return (self::can_contact_user($recipient->id, $sender->id));
    }

    /**
     * Determines if a user is permitted to send a message to a given conversation.
     * If no sender is provided then it defaults to the logged in user.
     *
     * @param int $userid the id of the user on which the checks will be applied.
     * @param int $conversationid the id of the conversation we wish to check.
     * @return bool true if the user can send a message to the conversation, false otherwise.
     * @throws \moodle_exception
     */
    public static function can_send_message_to_conversation(int $userid, int $conversationid) : bool {
        global $DB;

        $systemcontext = \context_system::instance();
        if (!has_capability('moodle/site:sendmessage', $systemcontext, $userid)) {
            return false;
        }

        if (!self::is_user_in_conversation($userid, $conversationid)) {
            return false;
        }

        // User can post messages and is in the conversation, but we need to check the conversation type to
        // know whether or not to check the user privacy settings via can_contact_user().
        $conversation = $DB->get_record('message_conversations', ['id' => $conversationid], '*', MUST_EXIST);
        if ($conversation->type == self::MESSAGE_CONVERSATION_TYPE_GROUP ||
            $conversation->type == self::MESSAGE_CONVERSATION_TYPE_SELF) {
            return true;
        } else if ($conversation->type == self::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL) {
            // Get the other user in the conversation.
            $members = self::get_conversation_members($userid, $conversationid);
            $otheruser = array_filter($members, function($member) use($userid) {
                return $member->id != $userid;
            });
            $otheruser = reset($otheruser);

            return self::can_contact_user($otheruser->id, $userid);
        } else {
            throw new \moodle_exception("Invalid conversation type '$conversation->type'.");
        }
    }

    /**
     * Send a message from a user to a conversation.
     *
     * This method will create the basic eventdata and delegate to message creation to message_send.
     * The message_send() method is responsible for event data that is specific to each recipient.
     *
     * @param int $userid the sender id.
     * @param int $conversationid the conversation id.
     * @param string $message the message to send.
     * @param int $format the format of the message to send.
     * @return \stdClass the message created.
     * @throws \coding_exception
     * @throws \moodle_exception if the user is not permitted to send a message to the conversation.
     */
    public static function send_message_to_conversation(int $userid, int $conversationid, string $message,
                                                        int $format) : \stdClass {
        global $DB, $PAGE;

        if (!self::can_send_message_to_conversation($userid, $conversationid)) {
            throw new \moodle_exception("User $userid cannot send a message to conversation $conversationid");
        }

        $eventdata = new \core\message\message();
        $eventdata->courseid         = 1;
        $eventdata->component        = 'moodle';
        $eventdata->name             = 'instantmessage';
        $eventdata->userfrom         = \core_user::get_user($userid);
        $eventdata->convid           = $conversationid;

        if ($format == FORMAT_HTML) {
            $eventdata->fullmessagehtml  = $message;
            // Some message processors may revert to sending plain text even if html is supplied,
            // so we keep both plain and html versions if we're intending to send html.
            $eventdata->fullmessage = html_to_text($eventdata->fullmessagehtml);
        } else {
            $eventdata->fullmessage      = $message;
            $eventdata->fullmessagehtml  = '';
        }

        $eventdata->fullmessageformat = $format;
        $eventdata->smallmessage = $message; // Store the message unfiltered. Clean up on output.

        $eventdata->timecreated     = time();
        $eventdata->notification    = 0;
        // Custom data for event.
        $customdata = [
            'actionbuttons' => [
                'send' => get_string('send', 'message'),
            ],
            'placeholders' => [
                'send' => get_string('writeamessage', 'message'),
            ],
        ];

        $conv = $DB->get_record('message_conversations', ['id' => $conversationid]);
        if ($conv->type == self::MESSAGE_CONVERSATION_TYPE_GROUP) {
            $convextrafields = self::get_linked_conversation_extra_fields([$conv]);
            // Conversation image.
            $imageurl = isset($convextrafields[$conv->id]) ? $convextrafields[$conv->id]['imageurl'] : null;
            if ($imageurl) {
                $customdata['notificationiconurl'] = $imageurl;
            }
            // Conversation name.
            if (is_null($conv->contextid)) {
                $convcontext = \context_user::instance($userid);
            } else {
                $convcontext = \context::instance_by_id($conv->contextid);
            }
            $customdata['conversationname'] = format_string($conv->name, true, ['context' => $convcontext]);
        } else if ($conv->type == self::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL) {
            $userpicture = new \user_picture($eventdata->userfrom);
            $customdata['notificationiconurl'] = $userpicture->get_url($PAGE)->out(false);
        }
        $eventdata->customdata = $customdata;

        $messageid = message_send($eventdata);

        $messagerecord = $DB->get_record('messages', ['id' => $messageid], 'id, useridfrom, fullmessage,
                timecreated, fullmessagetrust');
        $message = (object) [
            'id' => $messagerecord->id,
            'useridfrom' => $messagerecord->useridfrom,
            'text' => $messagerecord->fullmessage,
            'timecreated' => $messagerecord->timecreated,
            'fullmessagetrust' => $messagerecord->fullmessagetrust
        ];
        return $message;
    }

    /**
     * Get the messaging preference for a user.
     * If the user has not any messaging privacy preference:
     * - When $CFG->messagingallusers = false the default user preference is MESSAGE_PRIVACY_COURSEMEMBER.
     * - When $CFG->messagingallusers = true the default user preference is MESSAGE_PRIVACY_SITE.
     *
     * @param  int    $userid The user identifier.
     * @return int    The default messaging preference.
     */
    public static function get_user_privacy_messaging_preference(int $userid) : int {
        global $CFG, $USER;

        // When $CFG->messagingallusers is enabled, default value for the messaging preference will be "Anyone on the site";
        // otherwise, the default value will be "My contacts and anyone in my courses".
        if (empty($CFG->messagingallusers)) {
            $defaultprefvalue = self::MESSAGE_PRIVACY_COURSEMEMBER;
        } else {
            $defaultprefvalue = self::MESSAGE_PRIVACY_SITE;
        }
        if ($userid == $USER->id) {
            $user = $USER;
        } else {
            $user = $userid;
        }
        $privacypreference = get_user_preferences('message_blocknoncontacts', $defaultprefvalue, $user);

        // When the $CFG->messagingallusers privacy setting is disabled, MESSAGE_PRIVACY_SITE is
        // also disabled, so it has to be replaced to MESSAGE_PRIVACY_COURSEMEMBER.
        if (empty($CFG->messagingallusers) && $privacypreference == self::MESSAGE_PRIVACY_SITE) {
            $privacypreference = self::MESSAGE_PRIVACY_COURSEMEMBER;
        }

        return $privacypreference;
    }

    /**
     * Checks if the recipient is allowing messages from users that aren't a
     * contact. If not then it checks to make sure the sender is in the
     * recipient's contacts.
     *
     * @deprecated since 3.6
     * @param \stdClass $recipient The user object.
     * @param \stdClass|null $sender The user object.
     * @return bool true if $sender is blocked, false otherwise.
     */
    public static function is_user_non_contact_blocked($recipient, $sender = null) {
        debugging('\core_message\api::is_user_non_contact_blocked() is deprecated', DEBUG_DEVELOPER);

        global $USER, $CFG;

        if (is_null($sender)) {
            // The message is from the logged in user, unless otherwise specified.
            $sender = $USER;
        }

        $privacypreference = self::get_user_privacy_messaging_preference($recipient->id);
        switch ($privacypreference) {
            case self::MESSAGE_PRIVACY_SITE:
                if (!empty($CFG->messagingallusers)) {
                    // Users can be messaged without being contacts or members of the same course.
                    break;
                }
                // When the $CFG->messagingallusers privacy setting is disabled, continue with the next
                // case, because MESSAGE_PRIVACY_SITE is replaced to MESSAGE_PRIVACY_COURSEMEMBER.
            case self::MESSAGE_PRIVACY_COURSEMEMBER:
                // Confirm the sender and the recipient are both members of the same course.
                if (enrol_sharing_course($recipient, $sender)) {
                    // All good, the recipient and the sender are members of the same course.
                    return false;
                }
            case self::MESSAGE_PRIVACY_ONLYCONTACTS:
                // True if they aren't contacts (they can't send a message because of the privacy settings), false otherwise.
                return !self::is_contact($sender->id, $recipient->id);
        }

        return false;
    }

    /**
     * Checks if the recipient has specifically blocked the sending user.
     *
     * Note: This function will always return false if the sender has the
     * readallmessages capability at the system context level.
     *
     * @deprecated since 3.6
     * @param int $recipientid User ID of the recipient.
     * @param int $senderid User ID of the sender.
     * @return bool true if $sender is blocked, false otherwise.
     */
    public static function is_user_blocked($recipientid, $senderid = null) {
        debugging('\core_message\api::is_user_blocked is deprecated and should not be used.',
            DEBUG_DEVELOPER);

        global $USER;

        if (is_null($senderid)) {
            // The message is from the logged in user, unless otherwise specified.
            $senderid = $USER->id;
        }

        $systemcontext = \context_system::instance();
        if (has_capability('moodle/site:readallmessages', $systemcontext, $senderid)) {
            return false;
        }

        if (self::is_blocked($recipientid, $senderid)) {
            return true;
        }

        return false;
    }

    /**
     * Get specified message processor, validate corresponding plugin existence and
     * system configuration.
     *
     * @param string $name  Name of the processor.
     * @param bool $ready only return ready-to-use processors.
     * @return mixed $processor if processor present else empty array.
     * @since Moodle 3.2
     */
    public static function get_message_processor($name, $ready = false) {
        global $DB, $CFG;

        $processor = $DB->get_record('message_processors', array('name' => $name));
        if (empty($processor)) {
            // Processor not found, return.
            return array();
        }

        $processor = self::get_processed_processor_object($processor);
        if ($ready) {
            if ($processor->enabled && $processor->configured) {
                return $processor;
            } else {
                return array();
            }
        } else {
            return $processor;
        }
    }

    /**
     * Returns weather a given processor is enabled or not.
     * Note:- This doesn't check if the processor is configured or not.
     *
     * @param string $name Name of the processor
     * @return bool
     */
    public static function is_processor_enabled($name) {

        $cache = \cache::make('core', 'message_processors_enabled');
        $status = $cache->get($name);

        if ($status === false) {
            $processor = self::get_message_processor($name);
            if (!empty($processor)) {
                $cache->set($name, $processor->enabled);
                return $processor->enabled;
            } else {
                return false;
            }
        }

        return $status;
    }

    /**
     * Set status of a processor.
     *
     * @param \stdClass $processor processor record.
     * @param 0|1 $enabled 0 or 1 to set the processor status.
     * @return bool
     * @since Moodle 3.2
     */
    public static function update_processor_status($processor, $enabled) {
        global $DB;
        $cache = \cache::make('core', 'message_processors_enabled');
        $cache->delete($processor->name);
        return $DB->set_field('message_processors', 'enabled', $enabled, array('id' => $processor->id));
    }

    /**
     * Given a processor object, loads information about it's settings and configurations.
     * This is not a public api, instead use @see \core_message\api::get_message_processor()
     * or @see \get_message_processors()
     *
     * @param \stdClass $processor processor object
     * @return \stdClass processed processor object
     * @since Moodle 3.2
     */
    public static function get_processed_processor_object(\stdClass $processor) {
        global $CFG;

        $processorfile = $CFG->dirroot. '/message/output/'.$processor->name.'/message_output_'.$processor->name.'.php';
        if (is_readable($processorfile)) {
            include_once($processorfile);
            $processclass = 'message_output_' . $processor->name;
            if (class_exists($processclass)) {
                $pclass = new $processclass();
                $processor->object = $pclass;
                $processor->configured = 0;
                if ($pclass->is_system_configured()) {
                    $processor->configured = 1;
                }
                $processor->hassettings = 0;
                if (is_readable($CFG->dirroot.'/message/output/'.$processor->name.'/settings.php')) {
                    $processor->hassettings = 1;
                }
                $processor->available = 1;
            } else {
                print_error('errorcallingprocessor', 'message');
            }
        } else {
            $processor->available = 0;
        }
        return $processor;
    }

    /**
     * Retrieve users blocked by $user1
     *
     * @param int $userid The user id of the user whos blocked users we are returning
     * @return array the users blocked
     */
    public static function get_blocked_users($userid) {
        global $DB;

        $userfields = \user_picture::fields('u', array('lastaccess'));
        $blockeduserssql = "SELECT $userfields
                              FROM {message_users_blocked} mub
                        INNER JOIN {user} u
                                ON u.id = mub.blockeduserid
                             WHERE u.deleted = 0
                               AND mub.userid = ?
                          GROUP BY $userfields
                          ORDER BY u.firstname ASC";
        return $DB->get_records_sql($blockeduserssql, [$userid]);
    }

    /**
     * Mark a single message as read.
     *
     * @param int $userid The user id who marked the message as read
     * @param \stdClass $message The message
     * @param int|null $timeread The time the message was marked as read, if null will default to time()
     */
    public static function mark_message_as_read($userid, $message, $timeread = null) {
        global $DB;

        if (is_null($timeread)) {
            $timeread = time();
        }

        $mua = new \stdClass();
        $mua->userid = $userid;
        $mua->messageid = $message->id;
        $mua->action = self::MESSAGE_ACTION_READ;
        $mua->timecreated = $timeread;
        $mua->id = $DB->insert_record('message_user_actions', $mua);

        // Get the context for the user who received the message.
        $context = \context_user::instance($userid, IGNORE_MISSING);
        // If the user no longer exists the context value will be false, in this case use the system context.
        if ($context === false) {
            $context = \context_system::instance();
        }

        // Trigger event for reading a message.
        $event = \core\event\message_viewed::create(array(
            'objectid' => $mua->id,
            'userid' => $userid, // Using the user who read the message as they are the ones performing the action.
            'context' => $context,
            'relateduserid' => $message->useridfrom,
            'other' => array(
                'messageid' => $message->id
            )
        ));
        $event->trigger();
    }

    /**
     * Mark a single notification as read.
     *
     * @param \stdClass $notification The notification
     * @param int|null $timeread The time the message was marked as read, if null will default to time()
     */
    public static function mark_notification_as_read($notification, $timeread = null) {
        global $DB;

        if (is_null($timeread)) {
            $timeread = time();
        }

        if (is_null($notification->timeread)) {
            $updatenotification = new \stdClass();
            $updatenotification->id = $notification->id;
            $updatenotification->timeread = $timeread;

            $DB->update_record('notifications', $updatenotification);

            // Trigger event for reading a notification.
            \core\event\notification_viewed::create_from_ids(
                $notification->useridfrom,
                $notification->useridto,
                $notification->id
            )->trigger();
        }
    }

    /**
     * Checks if a user can delete a message.
     *
     * @param int $userid the user id of who we want to delete the message for (this may be done by the admin
     *  but will still seem as if it was by the user)
     * @param int $messageid The message id
     * @return bool Returns true if a user can delete the message, false otherwise.
     */
    public static function can_delete_message($userid, $messageid) {
        global $DB, $USER;

        $systemcontext = \context_system::instance();

        $conversationid = $DB->get_field('messages', 'conversationid', ['id' => $messageid], MUST_EXIST);

        if (has_capability('moodle/site:deleteanymessage', $systemcontext)) {
            return true;
        }

        if (!self::is_user_in_conversation($userid, $conversationid)) {
            return false;
        }

        if (has_capability('moodle/site:deleteownmessage', $systemcontext) &&
                $USER->id == $userid) {
            return true;
        }

        return false;
    }

    /**
     * Deletes a message.
     *
     * This function does not verify any permissions.
     *
     * @param int $userid the user id of who we want to delete the message for (this may be done by the admin
     *  but will still seem as if it was by the user)
     * @param int $messageid The message id
     * @return bool
     */
    public static function delete_message($userid, $messageid) {
        global $DB, $USER;

        if (!$DB->record_exists('messages', ['id' => $messageid])) {
            return false;
        }

        // Check if the user has already deleted this message.
        if (!$DB->record_exists('message_user_actions', ['userid' => $userid,
                'messageid' => $messageid, 'action' => self::MESSAGE_ACTION_DELETED])) {
            $mua = new \stdClass();
            $mua->userid = $userid;
            $mua->messageid = $messageid;
            $mua->action = self::MESSAGE_ACTION_DELETED;
            $mua->timecreated = time();
            $mua->id = $DB->insert_record('message_user_actions', $mua);

            // Trigger event for deleting a message.
            \core\event\message_deleted::create_from_ids($userid, $USER->id,
                $messageid, $mua->id)->trigger();

            return true;
        }

        return false;
    }

    /**
     * Returns the conversation between two users.
     *
     * @param array $userids
     * @return int|bool The id of the conversation, false if not found
     */
    public static function get_conversation_between_users(array $userids) {
        global $DB;

        $conversations = self::get_individual_conversations_between_users([$userids]);
        $conversation = $conversations[0];

        if ($conversation) {
            return $conversation->id;
        }

        return false;
    }

    /**
     * Returns the conversations between sets of users.
     *
     * The returned array of results will be in the same order as the requested
     * arguments, null will be returned if there is no conversation for that user
     * pair.
     *
     * For example:
     * If we have 6 users with ids 1, 2, 3, 4, 5, 6 where only 2 conversations
     * exist. One between 1 and 2 and another between 5 and 6.
     *
     * Then if we call:
     * $conversations = get_individual_conversations_between_users([[1,2], [3,4], [5,6]]);
     *
     * The conversations array will look like:
     * [<conv_record>, null, <conv_record>];
     *
     * Where null is returned for the pairing of [3, 4] since no record exists.
     *
     * @param array $useridsets An array of arrays where the inner array is the set of user ids
     * @return stdClass[] Array of conversation records
     */
    public static function get_individual_conversations_between_users(array $useridsets) : array {
        global $DB;

        if (empty($useridsets)) {
            return [];
        }

        $hashes = array_map(function($userids) {
            return  helper::get_conversation_hash($userids);
        }, $useridsets);

        list($inorequalsql, $params) = $DB->get_in_or_equal($hashes);
        array_unshift($params, self::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL);
        $where = "type = ? AND convhash ${inorequalsql}";
        $conversations = array_fill(0, count($hashes), null);
        $records = $DB->get_records_select('message_conversations', $where, $params);

        foreach (array_values($records) as $record) {
            $index = array_search($record->convhash, $hashes);
            if ($index !== false) {
                $conversations[$index] = $record;
            }
        }

        return $conversations;
    }

    /**
     * Returns the self conversation for a user.
     *
     * @param int $userid The user id to get the self-conversations
     * @return \stdClass|false The self-conversation object or false if it doesn't exist
     * @since Moodle 3.7
     */
    public static function get_self_conversation(int $userid) {
        global $DB;
        self::lazy_create_self_conversation($userid);

        $conditions = [
            'type' => self::MESSAGE_CONVERSATION_TYPE_SELF,
            'convhash' => helper::get_conversation_hash([$userid])
        ];
        return $DB->get_record('message_conversations', $conditions);
    }

    /**
     * Creates a conversation between two users.
     *
     * @deprecated since 3.6
     * @param array $userids
     * @return int The id of the conversation
     */
    public static function create_conversation_between_users(array $userids) {
        debugging('\core_message\api::create_conversation_between_users is deprecated, please use ' .
            '\core_message\api::create_conversation instead.', DEBUG_DEVELOPER);

        // This method was always used for individual conversations.
        $conversation = self::create_conversation(self::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL, $userids);

        return $conversation->id;
    }

    /**
     * Creates a conversation with selected users and messages.
     *
     * @param int $type The type of conversation
     * @param int[] $userids The array of users to add to the conversation
     * @param string|null $name The name of the conversation
     * @param int $enabled Determines if the conversation is created enabled or disabled
     * @param string|null $component Defines the Moodle component which the conversation belongs to, if any
     * @param string|null $itemtype Defines the type of the component
     * @param int|null $itemid The id of the component
     * @param int|null $contextid The id of the context
     * @return \stdClass
     */
    public static function create_conversation(int $type, array $userids, string $name = null,
            int $enabled = self::MESSAGE_CONVERSATION_ENABLED, string $component = null,
            string $itemtype = null, int $itemid = null, int $contextid = null) {

        global $DB;

        $validtypes = [
            self::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            self::MESSAGE_CONVERSATION_TYPE_GROUP,
            self::MESSAGE_CONVERSATION_TYPE_SELF
        ];

        if (!in_array($type, $validtypes)) {
            throw new \moodle_exception('An invalid conversation type was specified.');
        }

        // Sanity check.
        if ($type == self::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL) {
            if (count($userids) > 2) {
                throw new \moodle_exception('An individual conversation can not have more than two users.');
            }
            if ($userids[0] == $userids[1]) {
                throw new \moodle_exception('Trying to create an individual conversation instead of a self conversation.');
            }
        } else if ($type == self::MESSAGE_CONVERSATION_TYPE_SELF) {
            if (count($userids) != 1) {
                throw new \moodle_exception('A self conversation can not have more than one user.');
            }
        }

        $conversation = new \stdClass();
        $conversation->type = $type;
        $conversation->name = $name;
        $conversation->convhash = null;
        if ($type == self::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL || $type == self::MESSAGE_CONVERSATION_TYPE_SELF) {
            $conversation->convhash = helper::get_conversation_hash($userids);

            // Don't blindly create a conversation between 2 users if there is already one present - return that.
            // This stops us making duplicate self and individual conversations, which is invalid.
            if ($record = $DB->get_record('message_conversations', ['convhash' => $conversation->convhash])) {
                return $record;
            }
        }
        $conversation->component = $component;
        $conversation->itemtype = $itemtype;
        $conversation->itemid = $itemid;
        $conversation->contextid = $contextid;
        $conversation->enabled = $enabled;
        $conversation->timecreated = time();
        $conversation->timemodified = $conversation->timecreated;
        $conversation->id = $DB->insert_record('message_conversations', $conversation);

        // Add users to this conversation.
        $arrmembers = [];
        foreach ($userids as $userid) {
            $member = new \stdClass();
            $member->conversationid = $conversation->id;
            $member->userid = $userid;
            $member->timecreated = time();
            $member->id = $DB->insert_record('message_conversation_members', $member);

            $arrmembers[] = $member;
        }

        $conversation->members = $arrmembers;

        return $conversation;
    }

    /**
     * Checks if a user can create a group conversation.
     *
     * @param int $userid The id of the user attempting to create the conversation
     * @param \context $context The context they are creating the conversation from, most likely course context
     * @return bool
     */
    public static function can_create_group_conversation(int $userid, \context $context) : bool {
        global $CFG;

        // If we can't message at all, then we can't create a conversation.
        if (empty($CFG->messaging)) {
            return false;
        }

        // We need to check they have the capability to create the conversation.
        return has_capability('moodle/course:creategroupconversations', $context, $userid);
    }

    /**
     * Checks if a user can create a contact request.
     *
     * @param int $userid The id of the user who is creating the contact request
     * @param int $requesteduserid The id of the user being requested
     * @return bool
     */
    public static function can_create_contact(int $userid, int $requesteduserid) : bool {
        global $CFG;

        // If we can't message at all, then we can't create a contact.
        if (empty($CFG->messaging)) {
            return false;
        }

        // If we can message anyone on the site then we can create a contact.
        if ($CFG->messagingallusers) {
            return true;
        }

        // We need to check if they are in the same course.
        return enrol_sharing_course($userid, $requesteduserid);
    }

    /**
     * Handles creating a contact request.
     *
     * @param int $userid The id of the user who is creating the contact request
     * @param int $requesteduserid The id of the user being requested
     * @return \stdClass the request
     */
    public static function create_contact_request(int $userid, int $requesteduserid) : \stdClass {
        global $DB, $PAGE;

        $request = new \stdClass();
        $request->userid = $userid;
        $request->requesteduserid = $requesteduserid;
        $request->timecreated = time();

        $request->id = $DB->insert_record('message_contact_requests', $request);

        // Send a notification.
        $userfrom = \core_user::get_user($userid);
        $userfromfullname = fullname($userfrom);
        $userto = \core_user::get_user($requesteduserid);
        $url = new \moodle_url('/message/pendingcontactrequests.php');

        $subject = get_string('messagecontactrequestsnotificationsubject', 'core_message', $userfromfullname);
        $fullmessage = get_string('messagecontactrequestsnotification', 'core_message', $userfromfullname);

        $message = new \core\message\message();
        $message->courseid = SITEID;
        $message->component = 'moodle';
        $message->name = 'messagecontactrequests';
        $message->notification = 1;
        $message->userfrom = $userfrom;
        $message->userto = $userto;
        $message->subject = $subject;
        $message->fullmessage = text_to_html($fullmessage);
        $message->fullmessageformat = FORMAT_HTML;
        $message->fullmessagehtml = $fullmessage;
        $message->smallmessage = '';
        $message->contexturl = $url->out(false);
        $userpicture = new \user_picture($userfrom);
        $userpicture->includetoken = $userto->id; // Generate an out-of-session token for the user receiving the message.
        $message->customdata = [
            'notificationiconurl' => $userpicture->get_url($PAGE)->out(false),
            'actionbuttons' => [
                'accept' => get_string_manager()->get_string('accept', 'moodle', null, $userto->lang),
                'reject' => get_string_manager()->get_string('reject', 'moodle', null, $userto->lang),
            ],
        ];

        message_send($message);

        return $request;
    }


    /**
     * Handles confirming a contact request.
     *
     * @param int $userid The id of the user who created the contact request
     * @param int $requesteduserid The id of the user confirming the request
     */
    public static function confirm_contact_request(int $userid, int $requesteduserid) {
        global $DB;

        if ($request = $DB->get_record('message_contact_requests', ['userid' => $userid,
                'requesteduserid' => $requesteduserid])) {
            self::add_contact($userid, $requesteduserid);

            $DB->delete_records('message_contact_requests', ['id' => $request->id]);
        }
    }

    /**
     * Handles declining a contact request.
     *
     * @param int $userid The id of the user who created the contact request
     * @param int $requesteduserid The id of the user declining the request
     */
    public static function decline_contact_request(int $userid, int $requesteduserid) {
        global $DB;

        if ($request = $DB->get_record('message_contact_requests', ['userid' => $userid,
                'requesteduserid' => $requesteduserid])) {
            $DB->delete_records('message_contact_requests', ['id' => $request->id]);
        }
    }

    /**
     * Handles returning the contact requests for a user.
     *
     * This also includes the user data necessary to display information
     * about the user.
     *
     * It will not include blocked users.
     *
     * @param int $userid
     * @param int $limitfrom
     * @param int $limitnum
     * @return array The list of contact requests
     */
    public static function get_contact_requests(int $userid, int $limitfrom = 0, int $limitnum = 0) : array {
        global $DB;

        $sql = "SELECT mcr.userid
                  FROM {message_contact_requests} mcr
             LEFT JOIN {message_users_blocked} mub
                    ON (mub.userid = ? AND mub.blockeduserid = mcr.userid)
                 WHERE mcr.requesteduserid = ?
                   AND mub.id is NULL
              ORDER BY mcr.timecreated ASC";
        if ($contactrequests = $DB->get_records_sql($sql, [$userid, $userid], $limitfrom, $limitnum)) {
            $userids = array_keys($contactrequests);
            return helper::get_member_info($userid, $userids);
        }

        return [];
    }

    /**
     * Returns the number of contact requests the user has received.
     *
     * @param int $userid The ID of the user we want to return the number of received contact requests for
     * @return int The count
     */
    public static function get_received_contact_requests_count(int $userid) : int {
        global $DB;
        $sql = "SELECT COUNT(mcr.id)
                  FROM {message_contact_requests} mcr
             LEFT JOIN {message_users_blocked} mub
                    ON mub.userid = mcr.requesteduserid AND mub.blockeduserid = mcr.userid
                 WHERE mcr.requesteduserid = :requesteduserid
                   AND mub.id IS NULL";
        $params = ['requesteduserid' => $userid];
        return $DB->count_records_sql($sql, $params);
    }

    /**
     * Handles adding a contact.
     *
     * @param int $userid The id of the user who requested to be a contact
     * @param int $contactid The id of the contact
     */
    public static function add_contact(int $userid, int $contactid) {
        global $DB;

        $messagecontact = new \stdClass();
        $messagecontact->userid = $userid;
        $messagecontact->contactid = $contactid;
        $messagecontact->timecreated = time();
        $messagecontact->id = $DB->insert_record('message_contacts', $messagecontact);

        $eventparams = [
            'objectid' => $messagecontact->id,
            'userid' => $userid,
            'relateduserid' => $contactid,
            'context' => \context_user::instance($userid)
        ];
        $event = \core\event\message_contact_added::create($eventparams);
        $event->add_record_snapshot('message_contacts', $messagecontact);
        $event->trigger();
    }

    /**
     * Handles removing a contact.
     *
     * @param int $userid The id of the user who is removing a user as a contact
     * @param int $contactid The id of the user to be removed as a contact
     */
    public static function remove_contact(int $userid, int $contactid) {
        global $DB;

        if ($contact = self::get_contact($userid, $contactid)) {
            $DB->delete_records('message_contacts', ['id' => $contact->id]);

            $event = \core\event\message_contact_removed::create(array(
                'objectid' => $contact->id,
                'userid' => $userid,
                'relateduserid' => $contactid,
                'context' => \context_user::instance($userid)
            ));
            $event->add_record_snapshot('message_contacts', $contact);
            $event->trigger();
        }
    }

    /**
     * Handles blocking a user.
     *
     * @param int $userid The id of the user who is blocking
     * @param int $usertoblockid The id of the user being blocked
     */
    public static function block_user(int $userid, int $usertoblockid) {
        global $DB;

        $blocked = new \stdClass();
        $blocked->userid = $userid;
        $blocked->blockeduserid = $usertoblockid;
        $blocked->timecreated = time();
        $blocked->id = $DB->insert_record('message_users_blocked', $blocked);

        // Trigger event for blocking a contact.
        $event = \core\event\message_user_blocked::create(array(
            'objectid' => $blocked->id,
            'userid' => $userid,
            'relateduserid' => $usertoblockid,
            'context' => \context_user::instance($userid)
        ));
        $event->add_record_snapshot('message_users_blocked', $blocked);
        $event->trigger();
    }

    /**
     * Handles unblocking a user.
     *
     * @param int $userid The id of the user who is unblocking
     * @param int $usertounblockid The id of the user being unblocked
     */
    public static function unblock_user(int $userid, int $usertounblockid) {
        global $DB;

        if ($blockeduser = $DB->get_record('message_users_blocked',
                ['userid' => $userid, 'blockeduserid' => $usertounblockid])) {
            $DB->delete_records('message_users_blocked', ['id' => $blockeduser->id]);

            // Trigger event for unblocking a contact.
            $event = \core\event\message_user_unblocked::create(array(
                'objectid' => $blockeduser->id,
                'userid' => $userid,
                'relateduserid' => $usertounblockid,
                'context' => \context_user::instance($userid)
            ));
            $event->add_record_snapshot('message_users_blocked', $blockeduser);
            $event->trigger();
        }
    }

    /**
     * Checks if users are already contacts.
     *
     * @param int $userid The id of one of the users
     * @param int $contactid The id of the other user
     * @return bool Returns true if they are a contact, false otherwise
     */
    public static function is_contact(int $userid, int $contactid) : bool {
        global $DB;

        $sql = "SELECT id
                  FROM {message_contacts} mc
                 WHERE (mc.userid = ? AND mc.contactid = ?)
                    OR (mc.userid = ? AND mc.contactid = ?)";
        return $DB->record_exists_sql($sql, [$userid, $contactid, $contactid, $userid]);
    }

    /**
     * Returns the row in the database table message_contacts that represents the contact between two people.
     *
     * @param int $userid The id of one of the users
     * @param int $contactid The id of the other user
     * @return mixed A fieldset object containing the record, false otherwise
     */
    public static function get_contact(int $userid, int $contactid) {
        global $DB;

        $sql = "SELECT mc.*
                  FROM {message_contacts} mc
                 WHERE (mc.userid = ? AND mc.contactid = ?)
                    OR (mc.userid = ? AND mc.contactid = ?)";
        return $DB->get_record_sql($sql, [$userid, $contactid, $contactid, $userid]);
    }

    /**
     * Checks if a user is already blocked.
     *
     * @param int $userid
     * @param int $blockeduserid
     * @return bool Returns true if they are a blocked, false otherwise
     */
    public static function is_blocked(int $userid, int $blockeduserid) : bool {
        global $DB;

        return $DB->record_exists('message_users_blocked', ['userid' => $userid, 'blockeduserid' => $blockeduserid]);
    }

    /**
     * Get contact requests between users.
     *
     * @param int $userid The id of the user who is creating the contact request
     * @param int $requesteduserid The id of the user being requested
     * @return \stdClass[]
     */
    public static function get_contact_requests_between_users(int $userid, int $requesteduserid) : array {
        global $DB;

        $sql = "SELECT *
                  FROM {message_contact_requests} mcr
                 WHERE (mcr.userid = ? AND mcr.requesteduserid = ?)
                    OR (mcr.userid = ? AND mcr.requesteduserid = ?)";
        return $DB->get_records_sql($sql, [$userid, $requesteduserid, $requesteduserid, $userid]);
    }

    /**
     * Checks if a contact request already exists between users.
     *
     * @param int $userid The id of the user who is creating the contact request
     * @param int $requesteduserid The id of the user being requested
     * @return bool Returns true if a contact request exists, false otherwise
     */
    public static function does_contact_request_exist(int $userid, int $requesteduserid) : bool {
        global $DB;

        $sql = "SELECT id
                  FROM {message_contact_requests} mcr
                 WHERE (mcr.userid = ? AND mcr.requesteduserid = ?)
                    OR (mcr.userid = ? AND mcr.requesteduserid = ?)";
        return $DB->record_exists_sql($sql, [$userid, $requesteduserid, $requesteduserid, $userid]);
    }

    /**
     * Checks if a user is already in a conversation.
     *
     * @param int $userid The id of the user we want to check if they are in a group
     * @param int $conversationid The id of the conversation
     * @return bool Returns true if a contact request exists, false otherwise
     */
    public static function is_user_in_conversation(int $userid, int $conversationid) : bool {
        global $DB;

        return $DB->record_exists('message_conversation_members', ['conversationid' => $conversationid,
            'userid' => $userid]);
    }

    /**
     * Checks if the sender can message the recipient.
     *
     * @param int $recipientid
     * @param int $senderid
     * @return bool true if recipient hasn't blocked sender and sender can contact to recipient, false otherwise.
     */
    protected static function can_contact_user(int $recipientid, int $senderid) : bool {
        if (has_capability('moodle/site:messageanyuser', \context_system::instance(), $senderid) ||
            $recipientid == $senderid) {
            // The sender has the ability to contact any user across the entire site or themselves.
            return true;
        }

        // The initial value of $cancontact is null to indicate that a value has not been determined.
        $cancontact = null;

        if (self::is_blocked($recipientid, $senderid)) {
            // The recipient has specifically blocked this sender.
            $cancontact = false;
        }

        $sharedcourses = null;
        if (null === $cancontact) {
            // There are three user preference options:
            // - Site: Allow anyone not explicitly blocked to contact me;
            // - Course members: Allow anyone I am in a course with to contact me; and
            // - Contacts: Only allow my contacts to contact me.
            //
            // The Site option is only possible when the messagingallusers site setting is also enabled.

            $privacypreference = self::get_user_privacy_messaging_preference($recipientid);
            if (self::MESSAGE_PRIVACY_SITE === $privacypreference) {
                // The user preference is to allow any user to contact them.
                // No need to check anything else.
                $cancontact = true;
            } else {
                // This user only allows their own contacts, and possibly course peers, to contact them.
                // If the users are contacts then we can avoid the more expensive shared courses check.
                $cancontact = self::is_contact($senderid, $recipientid);

                if (!$cancontact && self::MESSAGE_PRIVACY_COURSEMEMBER === $privacypreference) {
                    // The users are not contacts and the user allows course member messaging.
                    // Check whether these two users share any course together.
                    $sharedcourses = enrol_get_shared_courses($recipientid, $senderid, true);
                    $cancontact = (!empty($sharedcourses));
                }
            }
        }

        if (false === $cancontact) {
            // At the moment the users cannot contact one another.
            // Check whether the messageanyuser capability applies in any of the shared courses.
            // This is intended to allow teachers to message students regardless of message settings.

            // Note: You cannot use empty($sharedcourses) here because this may be an empty array.
            if (null === $sharedcourses) {
                $sharedcourses = enrol_get_shared_courses($recipientid, $senderid, true);
            }

            foreach ($sharedcourses as $course) {
                // Note: enrol_get_shared_courses will preload any shared context.
                if (has_capability('moodle/site:messageanyuser', \context_course::instance($course->id), $senderid)) {
                    $cancontact = true;
                    break;
                }
            }
        }

        return $cancontact;
    }

    /**
     * Add some new members to an existing conversation.
     *
     * @param array $userids User ids array to add as members.
     * @param int $convid The conversation id. Must exists.
     * @throws \dml_missing_record_exception If convid conversation doesn't exist
     * @throws \dml_exception If there is a database error
     * @throws \moodle_exception If trying to add a member(s) to a non-group conversation
     */
    public static function add_members_to_conversation(array $userids, int $convid) {
        global $DB;

        $conversation = $DB->get_record('message_conversations', ['id' => $convid], '*', MUST_EXIST);

        // We can only add members to a group conversation.
        if ($conversation->type != self::MESSAGE_CONVERSATION_TYPE_GROUP) {
            throw new \moodle_exception('You can not add members to a non-group conversation.');
        }

        // Be sure we are not trying to add a non existing user to the conversation. Work only with existing users.
        list($useridcondition, $params) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $existingusers = $DB->get_fieldset_select('user', 'id', "id $useridcondition", $params);

        // Be sure we are not adding a user is already member of the conversation. Take all the members.
        $memberuserids = array_values($DB->get_records_menu(
            'message_conversation_members', ['conversationid' => $convid], 'id', 'id, userid')
        );

        // Work with existing new members.
        $members = array();
        $newuserids = array_diff($existingusers, $memberuserids);
        foreach ($newuserids as $userid) {
            $member = new \stdClass();
            $member->conversationid = $convid;
            $member->userid = $userid;
            $member->timecreated = time();
            $members[] = $member;
        }

        $DB->insert_records('message_conversation_members', $members);
    }

    /**
     * Remove some members from an existing conversation.
     *
     * @param array $userids The user ids to remove from conversation members.
     * @param int $convid The conversation id. Must exists.
     * @throws \dml_exception
     * @throws \moodle_exception If trying to remove a member(s) from a non-group conversation
     */
    public static function remove_members_from_conversation(array $userids, int $convid) {
        global $DB;

        $conversation = $DB->get_record('message_conversations', ['id' => $convid], '*', MUST_EXIST);

        if ($conversation->type != self::MESSAGE_CONVERSATION_TYPE_GROUP) {
            throw new \moodle_exception('You can not remove members from a non-group conversation.');
        }

        list($useridcondition, $params) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $params['convid'] = $convid;

        $DB->delete_records_select('message_conversation_members',
            "conversationid = :convid AND userid $useridcondition", $params);
    }

    /**
     * Count conversation members.
     *
     * @param int $convid The conversation id.
     * @return int Number of conversation members.
     * @throws \dml_exception
     */
    public static function count_conversation_members(int $convid) : int {
        global $DB;

        return $DB->count_records('message_conversation_members', ['conversationid' => $convid]);
    }

    /**
     * Checks whether or not a conversation area is enabled.
     *
     * @param string $component Defines the Moodle component which the area was added to.
     * @param string $itemtype Defines the type of the component.
     * @param int $itemid The id of the component.
     * @param int $contextid The id of the context.
     * @return bool Returns if a conversation area exists and is enabled, false otherwise
     */
    public static function is_conversation_area_enabled(string $component, string $itemtype, int $itemid, int $contextid) : bool {
        global $DB;

        return $DB->record_exists('message_conversations',
            [
                'itemid' => $itemid,
                'contextid' => $contextid,
                'component' => $component,
                'itemtype' => $itemtype,
                'enabled' => self::MESSAGE_CONVERSATION_ENABLED
            ]
        );
    }

    /**
     * Get conversation by area.
     *
     * @param string $component Defines the Moodle component which the area was added to.
     * @param string $itemtype Defines the type of the component.
     * @param int $itemid The id of the component.
     * @param int $contextid The id of the context.
     * @return \stdClass
     */
    public static function get_conversation_by_area(string $component, string $itemtype, int $itemid, int $contextid) {
        global $DB;

        return $DB->get_record('message_conversations',
            [
                'itemid' => $itemid,
                'contextid' => $contextid,
                'component' => $component,
                'itemtype'  => $itemtype
            ]
        );
    }

    /**
     * Enable a conversation.
     *
     * @param int $conversationid The id of the conversation.
     * @return void
     */
    public static function enable_conversation(int $conversationid) {
        global $DB;

        $conversation = new \stdClass();
        $conversation->id = $conversationid;
        $conversation->enabled = self::MESSAGE_CONVERSATION_ENABLED;
        $conversation->timemodified = time();
        $DB->update_record('message_conversations', $conversation);
    }

    /**
     * Disable a conversation.
     *
     * @param int $conversationid The id of the conversation.
     * @return void
     */
    public static function disable_conversation(int $conversationid) {
        global $DB;

        $conversation = new \stdClass();
        $conversation->id = $conversationid;
        $conversation->enabled = self::MESSAGE_CONVERSATION_DISABLED;
        $conversation->timemodified = time();
        $DB->update_record('message_conversations', $conversation);
    }

    /**
     * Update the name of a conversation.
     *
     * @param int $conversationid The id of a conversation.
     * @param string $name The main name of the area
     * @return void
     */
    public static function update_conversation_name(int $conversationid, string $name) {
        global $DB;

        if ($conversation = $DB->get_record('message_conversations', array('id' => $conversationid))) {
            if ($name <> $conversation->name) {
                $conversation->name = $name;
                $conversation->timemodified = time();
                $DB->update_record('message_conversations', $conversation);
            }
        }
    }

    /**
     * Returns a list of conversation members.
     *
     * @param int $userid The user we are returning the conversation members for, used by helper::get_member_info.
     * @param int $conversationid The id of the conversation
     * @param bool $includecontactrequests Do we want to include contact requests with this data?
     * @param bool $includeprivacyinfo Do we want to include privacy requests with this data?
     * @param int $limitfrom
     * @param int $limitnum
     * @return array
     */
    public static function get_conversation_members(int $userid, int $conversationid, bool $includecontactrequests = false,
                                                    bool $includeprivacyinfo = false, int $limitfrom = 0,
                                                    int $limitnum = 0) : array {
        global $DB;

        if ($members = $DB->get_records('message_conversation_members', ['conversationid' => $conversationid],
                'timecreated ASC, id ASC', 'userid', $limitfrom, $limitnum)) {
            $userids = array_keys($members);
            $members = helper::get_member_info($userid, $userids, $includecontactrequests, $includeprivacyinfo);

            return $members;
        }

        return [];
    }

    /**
     * Get the unread counts for all conversations for the user, sorted by type, and including favourites.
     *
     * @param int $userid the id of the user whose conversations we'll check.
     * @return array the unread counts for each conversation, indexed by type.
     */
    public static function get_unread_conversation_counts(int $userid) : array {
        global $DB;

        // Get all conversations the user is in, and check unread.
        $unreadcountssql = 'SELECT conv.id, conv.type, indcounts.unreadcount
                              FROM {message_conversations} conv
                        INNER JOIN (
                                      SELECT m.conversationid, count(m.id) as unreadcount
                                        FROM {messages} m
                                  INNER JOIN {message_conversations} mc
                                          ON mc.id = m.conversationid
                                  INNER JOIN {message_conversation_members} mcm
                                          ON m.conversationid = mcm.conversationid
                                   LEFT JOIN {message_user_actions} mua
                                          ON (mua.messageid = m.id AND mua.userid = ? AND
                                             (mua.action = ? OR mua.action = ?))
                                       WHERE mcm.userid = ?
                                         AND m.useridfrom != ?
                                         AND mua.id is NULL
                                    GROUP BY m.conversationid
                                   ) indcounts
                                ON indcounts.conversationid = conv.id
                             WHERE conv.enabled = 1';

        $unreadcounts = $DB->get_records_sql($unreadcountssql, [$userid, self::MESSAGE_ACTION_READ, self::MESSAGE_ACTION_DELETED,
            $userid, $userid]);

        // Get favourites, so we can track these separately.
        $service = \core_favourites\service_factory::get_service_for_user_context(\context_user::instance($userid));
        $favouriteconversations = $service->find_favourites_by_type('core_message', 'message_conversations');
        $favouriteconvids = array_flip(array_column($favouriteconversations, 'itemid'));

        // Assemble the return array.
        $counts = ['favourites' => 0, 'types' => [
            self::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 0,
            self::MESSAGE_CONVERSATION_TYPE_GROUP => 0,
            self::MESSAGE_CONVERSATION_TYPE_SELF => 0
        ]];
        foreach ($unreadcounts as $convid => $info) {
            if (isset($favouriteconvids[$convid])) {
                $counts['favourites']++;
                continue;
            }
            $counts['types'][$info->type]++;
        }

        return $counts;
    }

    /**
     * Handles muting a conversation.
     *
     * @param int $userid The id of the user
     * @param int $conversationid The id of the conversation
     */
    public static function mute_conversation(int $userid, int $conversationid) : void {
        global $DB;

        $mutedconversation = new \stdClass();
        $mutedconversation->userid = $userid;
        $mutedconversation->conversationid = $conversationid;
        $mutedconversation->action = self::CONVERSATION_ACTION_MUTED;
        $mutedconversation->timecreated = time();

        $DB->insert_record('message_conversation_actions', $mutedconversation);
    }

    /**
     * Handles unmuting a conversation.
     *
     * @param int $userid The id of the user
     * @param int $conversationid The id of the conversation
     */
    public static function unmute_conversation(int $userid, int $conversationid) : void {
        global $DB;

        $DB->delete_records('message_conversation_actions',
            [
                'userid' => $userid,
                'conversationid' => $conversationid,
                'action' => self::CONVERSATION_ACTION_MUTED
            ]
        );
    }

    /**
     * Checks whether a conversation is muted or not.
     *
     * @param int $userid The id of the user
     * @param int $conversationid The id of the conversation
     * @return bool Whether or not the conversation is muted or not
     */
    public static function is_conversation_muted(int $userid, int $conversationid) : bool {
        global $DB;

        return $DB->record_exists('message_conversation_actions',
            [
                'userid' => $userid,
                'conversationid' => $conversationid,
                'action' => self::CONVERSATION_ACTION_MUTED
            ]
        );
    }

    /**
     * Completely removes all related data in the DB for a given conversation.
     *
     * @param int $conversationid The id of the conversation
     */
    public static function delete_all_conversation_data(int $conversationid) {
        global $DB;

        $conv = $DB->get_record('message_conversations', ['id' => $conversationid], 'id, contextid');
        $convcontext = !empty($conv->contextid) ? \context::instance_by_id($conv->contextid) : null;

        $DB->delete_records('message_conversations', ['id' => $conversationid]);
        $DB->delete_records('message_conversation_members', ['conversationid' => $conversationid]);
        $DB->delete_records('message_conversation_actions', ['conversationid' => $conversationid]);

        // Now, go through and delete any messages and related message actions for the conversation.
        if ($messages = $DB->get_records('messages', ['conversationid' => $conversationid])) {
            $messageids = array_keys($messages);

            list($insql, $inparams) = $DB->get_in_or_equal($messageids);
            $DB->delete_records_select('message_user_actions', "messageid $insql", $inparams);

            // Delete the messages now.
            $DB->delete_records('messages', ['conversationid' => $conversationid]);
        }

        // Delete all favourite records for all users relating to this conversation.
        $service = \core_favourites\service_factory::get_service_for_component('core_message');
        $service->delete_favourites_by_type_and_item('message_conversations', $conversationid, $convcontext);
    }

    /**
     * Checks if a user can delete a message for all users.
     *
     * @param int $userid the user id of who we want to delete the message for all users
     * @param int $messageid The message id
     * @return bool Returns true if a user can delete the message for all users, false otherwise.
     */
    public static function can_delete_message_for_all_users(int $userid, int $messageid) : bool {
        global $DB;

        $sql = "SELECT mc.id, mc.contextid
                  FROM {message_conversations} mc
            INNER JOIN {messages} m
                    ON mc.id = m.conversationid
                 WHERE m.id = :messageid";
        $conversation = $DB->get_record_sql($sql, ['messageid' => $messageid]);

        if (!empty($conversation->contextid)) {
            return has_capability('moodle/site:deleteanymessage',
                \context::instance_by_id($conversation->contextid), $userid);
        }

        return has_capability('moodle/site:deleteanymessage', \context_system::instance(), $userid);
    }
    /**
     * Delete a message for all users.
     *
     * This function does not verify any permissions.
     *
     * @param int $messageid The message id
     * @return void
     */
    public static function delete_message_for_all_users(int $messageid) {
        global $DB, $USER;

        if (!$DB->record_exists('messages', ['id' => $messageid])) {
            return false;
        }

        // Get all members in the conversation where the message belongs.
        $membersql = "SELECT mcm.id, mcm.userid
                        FROM {message_conversation_members} mcm
                  INNER JOIN {messages} m
                          ON mcm.conversationid = m.conversationid
                       WHERE m.id = :messageid";
        $params = [
            'messageid' => $messageid
        ];
        $members = $DB->get_records_sql($membersql, $params);
        if ($members) {
            foreach ($members as $member) {
                self::delete_message($member->userid, $messageid);
            }
        }
    }

    /**
     * Create a self conversation for a user, only if one doesn't already exist.
     *
     * @param int $userid the user to whom the conversation belongs.
     */
    protected static function lazy_create_self_conversation(int $userid) : void {
        global $DB;
        // Check if the self-conversation for this user exists.
        // If not, create and star it for the user.
        // Don't use the API methods here, as they in turn may rely on
        // lazy creation and we'll end up with recursive loops of doom.
        $conditions = [
            'type' => self::MESSAGE_CONVERSATION_TYPE_SELF,
            'convhash' => helper::get_conversation_hash([$userid])
        ];
        if (empty($DB->get_record('message_conversations', $conditions))) {
            $selfconversation = self::create_conversation(self::MESSAGE_CONVERSATION_TYPE_SELF, [$userid]);
            self::set_favourite_conversation($selfconversation->id, $userid);
        }
    }
}
