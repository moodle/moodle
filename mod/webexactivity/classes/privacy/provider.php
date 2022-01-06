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
 * Privacy Subsystem implementation for mod_webexactivity.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2019 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_webexactivity\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\helper;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Subsystem implementation for mod_webexactivity.
 *
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2019 Oakland Universitym>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Return the fields which contain personal data.
     *
     * @param collection $items a reference to the collection to use to store the metadata.
     * @return collection the updated collection of metadata items.
     */
    public static function get_metadata(collection $items) : collection {
        $items->add_external_location_link(
            'webexhost',
            [
                'username' => 'privacy:metadata:username',
                'webexpassword' => 'privacy:metadata:webexpassword',
                'firstname' => 'privacy:metadata:firstname',
                'lastname' => 'privacy:metadata:lastname',
                'email' => 'privacy:metadata:email'
            ],
            'privacy:metadata:webexhost'
        );

        $items->add_external_location_link(
            'webexparticipant',
            [
                'firstname' => 'privacy:metadata:firstname',
                'lastname' => 'privacy:metadata:lastname',
                'email' => 'privacy:metadata:email'
            ],
            'privacy:metadata:webexparticipant'
        );


        $items->add_database_table(
            'webexactivity',
            [
                'hostwebexid' => 'privacy:metadata:webexactivity:hostwebexid',
                'password' => 'privacy:metadata:webexactivity:password',
                'timemodified' => 'privacy:metadata:timemodified'
            ],
            'privacy:metadata:webexactivity'
        );

        $items->add_database_table(
            'webexactivity_user',
            [
                'webexuserid' => 'privacy:metadata:webexactivity_user:webexuserid',
                'webexid' => 'privacy:metadata:webexactivity_user:webexid',
                'password' => 'privacy:metadata:webexpassword',
                'firstname' => 'privacy:metadata:firstname',
                'lastname' => 'privacy:metadata:lastname',
                'email' => 'privacy:metadata:email',
                'timemodified' => 'privacy:metadata:timemodified'
            ],
            'privacy:metadata:webexactivity_user'
        );

        $items->add_database_table(
            'webexactivity_recording',
            [
                'hostid' => 'privacy:metadata:webexactivity_recording:hostid',
                'timecreated' => 'privacy:metadata:timecreated',
                'timemodified' => 'privacy:metadata:timemodified'
            ],
            'privacy:metadata:webexactivity_recording'
        );

        return $items;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid the userid.
     * @return contextlist the list of contexts containing user info for the user.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $contextlist = new contextlist();

        // First add the user context if we have a user record for them.
        $params = ['userid' => $userid, 'contextuser' => CONTEXT_USER];
        $sql = "SELECT ctx.id
                  FROM {webexactivity_user} wxu
                  JOIN {context} ctx
                    ON ctx.instanceid = wxu.moodleuserid
                   AND ctx.contextlevel = :contextuser
                 WHERE wxu.moodleuserid = :userid";
        $contextlist->add_from_sql($sql, $params);

        // Now any module context that the user is a host in.
        $params = ['userid' => $userid, 'contextlevel' => CONTEXT_MODULE, 'modname' => 'webexactivity'];
        $sql = "SELECT ctx.id
                  FROM {context} ctx
            INNER JOIN {course_modules} cm
                    ON cm.id = ctx.instanceid
                   AND ctx.contextlevel = :contextlevel
            INNER JOIN {modules} m
                    ON m.id = cm.module
                   AND m.name = :modname
            INNER JOIN {webexactivity} wx
                    ON wx.id = cm.instance
            INNER JOIN {webexactivity_user} wxu
                    ON wx.hostwebexid = wxu.webexid
                 WHERE wxu.moodleuserid = :userid";
        $contextlist->add_from_sql($sql, $params);

        // And now any module context that the user has a recording in.
        $sql = "SELECT ctx.id
                  FROM {context} ctx
            INNER JOIN {course_modules} cm
                    ON cm.id = ctx.instanceid
                   AND ctx.contextlevel = :contextlevel
            INNER JOIN {modules} m
                    ON m.id = cm.module
                   AND m.name = :modname
            INNER JOIN {webexactivity} wx
                    ON wx.id = cm.instance
            INNER JOIN {webexactivity_recording} wxr
                    ON wxr.webexid = wx.id
            INNER JOIN {webexactivity_user} wxu
                    ON wxr.hostid = wxu.webexid
                 WHERE wxu.moodleuserid = :userid";

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if ($context->contextlevel == CONTEXT_USER) {
            // The user's personal information.
            $sql = "SELECT moodleuserid
                      FROM {webexactivity_user}
                    WHERE moodleuserid = :userid";
            $params = ['userid' => $context->instanceid];
            $userlist->add_from_sql('moodleuserid', $sql, $params);
        } else if ($context->contextlevel == CONTEXT_MODULE) {
            // Check the context for the user that hosts it.
            $params = ['contextid' => $context->id, 'contextlevel' => CONTEXT_MODULE, 'modname' => 'webexactivity'];
            $sql = "SELECT wxu.moodleuserid as muserid
                      FROM {context} ctx
                INNER JOIN {course_modules} cm
                        ON cm.id = ctx.instanceid
                       AND ctx.contextlevel = :contextlevel
                INNER JOIN {modules} m
                        ON m.id = cm.module
                       AND m.name = :modname
                INNER JOIN {webexactivity} wx
                        ON wx.id = cm.instance
                INNER JOIN {webexactivity_user} wxu
                        ON wx.hostwebexid = wxu.webexid
                     WHERE ctx.id = :contextid";
            $userlist->add_from_sql('muserid', $sql, $params);

            // Now any recordings they hosted.
            $sql = "SELECT wxu.moodleuserid as muserid
                      FROM {context} ctx
                INNER JOIN {course_modules} cm
                        ON cm.id = ctx.instanceid
                       AND ctx.contextlevel = :contextlevel
                INNER JOIN {modules} m
                        ON m.id = cm.module
                       AND m.name = :modname
                INNER JOIN {webexactivity} wx
                        ON wx.id = cm.instance
                INNER JOIN {webexactivity_recording} wxr
                        ON wxr.webexid = wx.id
                INNER JOIN {webexactivity_user} wxu
                        ON wxr.hostid = wxu.webexid
                     WHERE ctx.id = :contextid";
            $userlist->add_from_sql('muserid', $sql, $params);
        }

    }

    /**
     * Export personal data for the given approved_contextlist. User and context information is contained within the contextlist.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for export.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $user = $contextlist->get_user();

        foreach ($contextlist as $context) {
            if ($context->contextlevel == CONTEXT_USER) {
                if ($userdata = $DB->get_records('webexactivity_user', ['moodleuserid' => $user->id])) {
                    foreach ($userdata as $rec) {
                        // Seems like we shouldn't export this...
                        unset($rec->password);
                        writer::with_context($context)->export_data(['webexuser'], $rec);
                    }
                }

            } else if ($context->contextlevel == CONTEXT_MODULE) {
                $params = [
                    'contextid' => $context->id,
                    'contextlevel' => CONTEXT_MODULE,
                    'modname' => 'webexactivity',
                    'userid' => $user->id
                ];

                $sql = "SELECT wx.*
                          FROM {context} ctx
                    INNER JOIN {course_modules} cm
                            ON cm.id = ctx.instanceid
                           AND ctx.contextlevel = :contextlevel
                    INNER JOIN {modules} m
                            ON m.id = cm.module
                           AND m.name = :modname
                    INNER JOIN {webexactivity} wx
                            ON wx.id = cm.instance
                    INNER JOIN {webexactivity_user} wxu
                            ON wx.hostwebexid = wxu.webexid
                         WHERE ctx.id = :contextid
                           AND wxu.moodleuserid = :userid";

                if ($records = $DB->get_records_sql($sql, $params)) {
                    foreach ($records as $rec) {
                        unset($rec->hostkey);
                        unset($rec->password);
                        unset($rec->creatorwebexid);
                        writer::with_context($context)->export_data([], $rec);
                    }
                }

                $sql = "SELECT wxr.*
                          FROM {context} ctx
                    INNER JOIN {course_modules} cm
                            ON cm.id = ctx.instanceid
                           AND ctx.contextlevel = :contextlevel
                    INNER JOIN {modules} m
                            ON m.id = cm.module
                           AND m.name = :modname
                    INNER JOIN {webexactivity} wx
                            ON wx.id = cm.instance
                    INNER JOIN {webexactivity_recording} wxr
                            ON wxr.webexid = wx.id
                    INNER JOIN {webexactivity_user} wxu
                            ON wxr.hostid = wxu.webexid
                         WHERE ctx.id = :contextid
                           AND wxu.moodleuserid = :userid";

                if ($records = $DB->get_records_sql($sql, $params)) {
                    $seehidden = has_capability('mod/webexactivity:hostmeeting', $context, $user);
                    foreach ($records as $rec) {
                        if (!$rec->visible && !$seehidden) {
                            unset($rec->streamurl);
                            unset($rec->fileurl);
                        }

                        writer::with_context($context)->export_data(['recordings', $rec->id], $rec);
                    }
                }
            }
        }


        return;

    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context the context to delete in.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        debugging('The Webex Activity plugin does not currently support the deleting of user data. '.$path, DEBUG_DEVELOPER);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for deletion.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        debugging('The Webex Activity plugin does not currently support the deleting of user data. '.$path, DEBUG_DEVELOPER);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        debugging('The Webex Activity plugin does not currently support the deleting of user data. '.$path, DEBUG_DEVELOPER);
    }

}
