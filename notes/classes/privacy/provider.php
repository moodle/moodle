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
 * Privacy Subsystem implementation for core_notes.
 *
 * @package    core_notes
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_notes\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/notes/lib.php');

/**
 * Implementation of the privacy subsystem plugin provider for core_notes.
 *
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\provider, \core_privacy\local\request\plugin\provider {

    /**
     * Return the fields which contain personal data.
     *
     * @param collection $items a reference to the collection to use to store the metadata.
     * @return collection the updated collection of metadata items.
     */
    public static function get_metadata(collection $items) {
        // The core_notes components utilises the shared mdl_post table.
        $items->add_database_table(
            'post',
            [
                'content' => 'privacy:metadata:core_notes:content',
                'courseid' => 'privacy:metadata:core_notes:courseid',
                'created' => 'privacy:metadata:core_notes:created',
                'lastmodified' => 'privacy:metadata:core_notes:lastmodified',
                'publishstate' => 'privacy:metadata:core_notes:publishstate',
                'userid' => 'privacy:metadata:core_notes:userid'
            ],
            'privacy:metadata:core_notes'
        );

        return $items;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid the userid.
     * @return contextlist the list of contexts containing user info for the user.
     */
    public static function get_contexts_for_userid($userid) {
        global $DB;

        $contextlist = new contextlist();

        $publishstates = [
            NOTES_STATE_PUBLIC,
            NOTES_STATE_SITE
        ];
        list($publishstatesql, $publishstateparams) = $DB->get_in_or_equal($publishstates, SQL_PARAMS_NAMED);

        // Retrieve all the Course contexts associated with notes written by the user, and also written about the user.
        // Only notes written about the user that are public or site wide will be exported.
        $sql = "SELECT c.id
                  FROM {context} c
            INNER JOIN {post} p ON p.courseid = c.instanceid AND c.contextlevel = :contextcoursewrittenby
                 WHERE p.module = 'notes'
                   AND p.usermodified = :usermodified
                 UNION
                SELECT c.id
                  FROM {context} c
            INNER JOIN {post} p ON p.courseid = c.instanceid AND c.contextlevel = :contextcoursewrittenfor
                 WHERE p.module = 'notes'
                   AND p.userid = :userid
                   AND p.publishstate {$publishstatesql}";

        $params = [
            'contextcoursewrittenby'  => CONTEXT_COURSE,
            'usermodified'            => $userid,
            'contextcoursewrittenfor' => CONTEXT_COURSE,
            'userid'                  => $userid
        ];
        $params += $publishstateparams;

        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Export personal data for the given approved_contextlist.
     * User and context information is contained within the contextlist.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for export.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        // Export all notes written by and written about the user, and organize it by the associated Course context(s).
        $sql = "SELECT p.courseid as courseid,
                       p.content as content,
                       p.publishstate as publishstate,
                       p.userid as userid,
                       p.usermodified as usermodified,
                       p.created as datecreated,
                       p.lastmodified as datemodified
                  FROM {context} c
            INNER JOIN {post} p ON p.courseid = c.instanceid AND c.contextlevel = :contextcourse
                 WHERE p.module = 'notes'
                   AND (p.usermodified = :usermodified OR p.userid = :userid)
                   AND c.id {$contextsql}";

        $params = [
            'contextcourse' => CONTEXT_COURSE,
            'usermodified'  => $userid,
            'userid'        => $userid
        ];
        $params += $contextparams;

        $notes = $DB->get_recordset_sql($sql, $params);
        foreach ($notes as $note) {
            $contextcourse = \context_course::instance($note->courseid);

            // The exported notes will be organized in {Course Context}/Notes/{publishstate}/usernote-{userid}.json.
            $subcontext = [
                get_string('notes', 'notes'),
                $note->publishstate
            ];

            $name = 'usernote-' . transform::user($note->userid);

            $notecontent = (object) [
               'content' => $note->content,
               'publishstate' => $note->publishstate,
               'userid' => transform::user($note->userid),
               'usermodified' => transform::user($note->usermodified),
               'datecreated' => transform::datetime($note->datecreated),
               'datemodified' => transform::datetime($note->datemodified)
            ];

            writer::with_context($contextcourse)->export_related_data($subcontext, $name, $notecontent);
        }
        $notes->close();
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context the context to delete in.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context->contextlevel != CONTEXT_COURSE) {
            return;
        }

        $DB->delete_records('post', ['module' => 'notes', 'courseid' => $context->instanceid]);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for deletion.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {
            $conditions = [
                'module'        => 'notes',
                'courseid'      => $context->instanceid,
                'usermodified'  => $userid
            ];

            $DB->delete_records('post', $conditions);
        }
    }
}
