<?php
// This file is part of the Brickfield board plugin
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
 * Privacy API implementation for the Brickfield board plugin.
 *
 * @package    mod_board
 * @category   privacy
 * @copyright  2021 Brickfield Education Labs, https://www.brickfield.ie
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_board\privacy;

use \core_privacy\local\request\userlist;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\approved_userlist;
use \core_privacy\local\request\deletion_criteria;
use \core_privacy\local\request\writer;
use \core_privacy\local\request\helper as request_helper;
use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\transform;
use tool_dataprivacy\context_instance;

/**
 * Implementation of the privacy subsystem plugin provider for the Brickfield board module.
 *
 * @copyright  2021 Brickfield Education Labs, https://www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements

    // This plugin has data.
    \core_privacy\local\metadata\provider,

    // This plugin currently implements the original plugin\provider interface.
    \core_privacy\local\request\plugin\provider,

    // This plugin is capable of determining which users have data within it.
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection     $items The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $items) : collection {
        // The 'board' table does not store any specific user data.
        $items->add_database_table('board_notes', [
            'columnid' => 'privacy:metadata:board_notes:columnid',
            'userid' => 'privacy:metadata:board_notes:userid',
            'heading' => 'privacy:metadata:board_notes:heading',
            'content' => 'privacy:metadata:board_notes:content',
            'info' => 'privacy:metadata:board_notes:info',
            'url' => 'privacy:metadata:board_notes:url',
            'timecreated' => 'privacy:metadata:board_notes:timecreated',
        ], 'privacy:metadata:board_notes');

        // The 'board_history' table stores the metadata about each board update.
        $items->add_database_table('board_history', [
            'boardid' => 'privacy:metadata:board_history:boardid',
            'userid' => 'privacy:metadata:board_history:userid',
            'action' => 'privacy:metadata:board_history:action',
            'content' => 'privacy:metadata:board_history:content',
            'timecreated' => 'privacy:metadata:board_history:timecreated',
        ], 'privacy:metadata:board_history');

        // The 'board_note_ratings' table stores information about which notes a user has rated.
        $items->add_database_table('board_note_ratings', [
            'noteid' => 'privacy:metadata:board_note_ratings:noteid',
            'userid' => 'privacy:metadata:board_note_ratings:userid',
            'timecreated' => 'privacy:metadata:board_note_ratings:timecreated',
        ], 'privacy:metadata:board_note_ratings');

        // The 'board_comments' table stores comments a user has added to a note.
        $items->add_database_table('board_comments', [
            'noteid' => 'privacy:metadata:board_comments:noteid',
            'userid' => 'privacy:metadata:board_comments:userid',
            'content' => 'privacy:metadata:board_comments:content',
            'timecreated' => 'privacy:metadata:board_comments:timecreated',
        ], 'privacy:metadata:board_comments');

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
            'modname'       => 'board',
            'contextlevel'  => CONTEXT_MODULE,
            'userid'        => $userid,
        ];

        // Board notes.
        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {board} b ON b.id = cm.instance
                  JOIN {board_columns} bc ON bc.boardid = b.id
                  JOIN {board_notes} n ON n.columnid = bc.id
                 WHERE n.userid = :userid
        ";
        $contextlist->add_from_sql($sql, $params);

        // Board update history.
        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {board} b ON b.id = cm.instance
                  JOIN {board_history} h ON h.boardid = b.id
                 WHERE h.userid = :userid
        ";
        $contextlist->add_from_sql($sql, $params);

        // Board note ratings.
        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {board} b ON b.id = cm.instance
                  JOIN {board_columns} bc ON bc.boardid = b.id
                  JOIN {board_notes} n ON n.columnid = bc.id
                  JOIN {board_note_ratings} r ON r.noteid = n.id
                 WHERE r.userid = :userid
        ";
        $contextlist->add_from_sql($sql, $params);

        // Board comments.
        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {board} b ON b.id = cm.instance
                  JOIN {board_columns} bc ON bc.boardid = b.id
                  JOIN {board_notes} n ON n.columnid = bc.id
                  JOIN {board_comments} bcm ON bcm.noteid = n.id
                 WHERE bcm.userid = :userid
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
            'modulename'    => 'board',
        ];

        // Note / post authors.
        $sql = "SELECT n.userid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {board} b ON b.id = cm.instance
                  JOIN {board_columns} bc ON bc.boardid = b.id
                  JOIN {board_notes} n ON n.columnid = bc.id
                 WHERE cm.id = :instanceid";
        $userlist->add_from_sql('userid', $sql, $params);

        // Board updates.
        $sql = "SELECT h.userid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {board} b ON b.id = cm.instance
                  JOIN {board_history} h ON h.boardid = b.id
                 WHERE cm.id = :instanceid";
        $userlist->add_from_sql('userid', $sql, $params);

        // Board note ratings.
        $sql = "SELECT r.userid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {board} b ON b.id = cm.instance
                  JOIN {board_columns} bc ON bc.boardid = b.id
                  JOIN {board_notes} n ON n.columnid = bc.id
                  JOIN {board_note_ratings} r ON r.noteid = n.id
                 WHERE cm.id = :instanceid";
        $userlist->add_from_sql('userid', $sql, $params);

        // Board comments.
        $sql = "SELECT bcm.userid
                  FROM {course_modules} cm
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modulename
                  JOIN {board} b ON b.id = cm.instance
                  JOIN {board_columns} bc ON bc.boardid = b.id
                  JOIN {board_notes} n ON n.columnid = bc.id
                  JOIN {board_comments} bcm ON bcm.noteid = n.id
                 WHERE cm.id = :instanceid";
        $userlist->add_from_sql('userid', $sql, $params);
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

        $sql = "SELECT
                    c.id AS contextid,
                    b.*,
                    cm.id AS cmid
                  FROM {context} c
                  JOIN {course_modules} cm ON cm.id = c.instanceid
                  JOIN {board} b ON b.id = cm.instance
                 WHERE (
                    c.id {$contextsql}
                )
        ";

        // Keep a mapping of boardid to contextid.
        $mappings = [];

        $boards = $DB->get_recordset_sql($sql, $params);
        foreach ($boards as $board) {
            $mappings[$board->id] = $board->contextid;

            $context = \context::instance_by_id($mappings[$board->id]);

            // Store the main board data.
            $data = request_helper::get_context_data($context, $user);
            writer::with_context($context)
                ->export_data([], $data);
            request_helper::export_context_files($context, $user);
        }

        if (!empty($mappings)) {
            // Store all notes data for this board.
            static::export_notes_data($userid, $mappings);

            // Store all history data for this board.
            static::export_boardhistory_data($userid, $mappings);

            // Store all note ratings data for this board.
            static::export_ratings_data($userid, $mappings);

            // Store all comments for this board.
            static::export_comments_data($userid, $mappings);
        }

        $boards->close();
    }

    /**
     * Store all information about all notes that we have detected this user to have access to.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     * @param   array       $mappings A list of mappings from boardid => contextid.
     * @return  array       Which boards had data written for them.
     */
    protected static function export_notes_data(int $userid, array $mappings) {
        global $DB;

        // Find all of the notes for this board.
        list($boardinsql, $boardparams) = $DB->get_in_or_equal(array_keys($mappings), SQL_PARAMS_NAMED);
        $sql = "SELECT
                    n.*,
                    b.id AS boardid, bc.id AS columnid
                  FROM {board} b
                  JOIN {board_columns} bc ON bc.boardid = b.id
                  JOIN {board_notes} n ON n.columnid = bc.id
                 WHERE b.id {$boardinsql}
                   AND (
                        n.userid    = :noteuserid
                   )
        ";

        $params = [
            'noteuserid'  => $userid,
        ];
        $params += $boardparams;

        // Keep track of the boards which have data.
        $boardswithdata = [];

        $notes = $DB->get_recordset_sql($sql, $params);
        foreach ($notes as $note) {
            // Ignore board postby time as it should not block access to user data.
            $boardswithdata[$note->boardid] = true;
            $context = \context::instance_by_id($mappings[$note->boardid]);

            $notedata = (object) [
                'heading' => format_string($note->heading, true),
                'content' => format_string($note->content, true),
                'mediatype' => format_string($note->type, true),
                'mediainfo' => format_string($note->info, true),
                'mediaurl' => format_string($note->url, true),
                'deleted' => ($note->deleted) ? get_string('yes') : get_string('no'),
                'timecreated' => transform::datetime($note->timecreated),
            ];

            $notearea = static::get_export_area($note);

            // Store the note content.
            writer::with_context($context)
                ->export_data($notearea, $notedata)

                // Store the associated image files.
                ->export_area_files($notearea, 'mod_board', 'images', $note->id);
        }

        $notes->close();

        return $boardswithdata;
    }

    /**
     * Retrieve information about a specific note or rating for privacy export.
     *
     * @param   stdClass    $note The note from which to compile the export data.
     * @param   string      $exportarea The area being compiled for the export data.
     * @return  array       Further note export data.
     */
    protected static function get_export_area(\stdClass $note, string $exportarea = 'posts') : Array {
        $pathparts = [];

        $parts = [
            $note->id,
            ];

        // Just need either heading, or content, or info for note 'title'.
        $notetitle = static::get_note_title($note);
        $notetitle = str_replace('/', '', $notetitle);
        // Remove URL extras from title, breaks the SAR export data.
        $notetitlesplit = explode('?', $notetitle, 2);
        $notetitle = $notetitlesplit[0];
        $parts[] = format_string($notetitle);

        $notename = implode('-', $parts);

        $pathparts[] = get_string($exportarea, 'mod_board');
        $pathparts[] = $notename;

        return $pathparts;
    }

    /**
     * Store all information about all boardhistory that we have detected this user to have done.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     * @param   array       $mappings A list of mappings from boardid => contextid.
     * @return  array       Which boards had history written for them.
     */
    protected static function export_boardhistory_data(int $userid, array $mappings) {
        global $DB;

        // Board history records are only temporary, defining them as such.

        $boardswithdata = [];
        $historyinfo = get_string('historyinfo', 'mod_board');

        foreach ($mappings as $boardid => $value) {
            $boardswithdata[$boardid] = true;
            $context = \context::instance_by_id($value);

            writer::with_context($context)
                ->export_data([get_string('history', 'mod_board')], (object)['info' => $historyinfo]);
        }

        return $boardswithdata;
    }

    /**
     * Store all information about all ratings that we have detected this user to have done.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     * @param   array       $mappings A list of mappings from boardid => contextid.
     * @return  array       Which boards had ratings for them.
     */
    protected static function export_ratings_data(int $userid, array $mappings) {
        global $DB;

        // Find all of the ratings for these boards.
        list($boardinsql, $boardparams) = $DB->get_in_or_equal(array_keys($mappings), SQL_PARAMS_NAMED);
        $sql = "SELECT
                    n.*,
                    b.id AS boardid, bc.id AS columnid
                  FROM {board} b
                  JOIN {board_columns} bc ON bc.boardid = b.id
                  JOIN {board_notes} n ON n.columnid = bc.id
                  JOIN {board_note_ratings} r ON r.noteid = n.id
                 WHERE b.id {$boardinsql}
                   AND (
                        r.userid    = :userid
                   )
        ";

        $params = [
            'userid'  => $userid,
        ];
        $params += $boardparams;

        // Keep track of the boards which have data.
        $boardswithdata = [];

        $ratings = $DB->get_recordset_sql($sql, $params);
        foreach ($ratings as $rating) {
            $boardswithdata[$rating->boardid] = true;
            $context = \context::instance_by_id($mappings[$rating->boardid]);

            $ratingdata = (object) [
                'rating given' => transform::yesno(1), // If exists, rating given.
                'note id' => format_string($rating->id, true),
                'notetitle' => format_string(static::get_note_title($rating)),
                'timecreated' => transform::datetime($rating->timecreated),
            ];

            $ratingarea = static::get_export_area($rating, 'ratings');

            // Store the ratings content.
            writer::with_context($context)
                ->export_data($ratingarea, $ratingdata);

        }

        $ratings->close();

        return $boardswithdata;
    }

    /**
     * Store all information about comments that we have detected this user to have added.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     * @param   array       $mappings A list of mappings from boardid => contextid.
     * @return  array       Which boards had comments for them.
     */
    protected static function export_comments_data(int $userid, array $mappings) {
        global $DB;

        // Find all of the comments for these boards.
        list($boardinsql, $boardparams) = $DB->get_in_or_equal(array_keys($mappings), SQL_PARAMS_NAMED);
        $sql = "SELECT
                    bcm.id AS commentid, bcm.deleted AS cdeleted, n.*,
                    b.id AS boardid, bc.id AS columnid, bcm.content AS comment
                  FROM {board} b
                  JOIN {board_columns} bc ON bc.boardid = b.id
                  JOIN {board_notes} n ON n.columnid = bc.id
                  JOIN {board_comments} bcm ON bcm.noteid = n.id
                 WHERE b.id {$boardinsql}
                   AND (
                        bcm.userid    = :userid
                   )
              ORDER BY b.id, bc.id, n.id, bcm.id
        ";

        $params = [
            'userid'  => $userid,
        ];
        $params += $boardparams;

        // Keep track of the notes which have comments.
        $boardswithdata = [];

        $commentdata = [];
        $commentsstring = trim(get_string('comments', 'mod_board', ''));
        $commentstring = get_string('comment', 'mod_board');
        $comments = $DB->get_recordset_sql($sql, $params);
        foreach ($comments as $comment) {
            $boardswithdata[$comment->boardid] = true;
            $context = \context::instance_by_id($mappings[$comment->boardid]);

            $commentdata = (object) [
                'comment' => format_string($comment->comment, true),
                'note id' => format_string($comment->id, true),
                'notetitle' => format_string(static::get_note_title($comment)),
                'deleted' => ($comment->cdeleted) ? get_string('yes') : get_string('no'),
                'timecreated' => transform::datetime($comment->timecreated),
            ];
            $commentarea = [];
            $commentarea[] = $commentsstring;
            $commentarea[] = $commentstring . ' ' . $comment->commentid;
            // Store the comments content.
            writer::with_context($context)->export_data($commentarea, (object) $commentdata);
        }

        $comments->close();

        return $boardswithdata;
    }

    /**
     * Retrieve information about a specific note title for privacy export.
     *
     * @param   stdClass    $note The note from which to compile the export data.
     * @return  string      An identifiable note title for export data.
     */
    protected static function get_note_title(\stdClass $note) : String {
        $notetitle = '';

        // Just need either heading, or content, or info for note 'title'.
        if (empty($note->heading)) {
            if (!empty($note->content)) {
                $notetitle = substr($note->content, 0, 20);
            } else {
                $notetitle = $note->info;
            }
        } else {
            $notetitle = $note->heading;
        }

        if (empty($notetitle)) {
            $notetitle = get_string('noname', 'mod_board');
        }

        return $notetitle;
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
        if (!$cm = get_coursemodule_from_id('board', $context->instanceid)) {
            return;
        }

        $boardid = $cm->instance;

        $columnids = $DB->get_fieldset_select(
            'board_columns', 'id',
            "boardid = :boardid)",
            ['boardid' => $boardid]
        );

        list($columnsinsql, $columnsinparams) = $DB->get_in_or_equal($columnids, SQL_PARAMS_NAMED);
        $noteids = $DB->get_fieldset_select(
            'board_notes', 'id',
            "columnid {$columnsinsql}",
            $columnsinparams
        );

        list($notesinsql, $notesinparams) = $DB->get_in_or_equal($noteids, SQL_PARAMS_NAMED);

        // Delete all board notes.
        $DB->delete_records_select(
            'board_notes',
            "id {$notesinsql}",
            $notesinparams
        );

        // Delete all board note ratings.
        $DB->delete_records_select(
            'board_note_ratings',
            "noteid {$notesinsql}",
            $notesinparams
        );

        // Delete all board comments.
        $DB->delete_records_select(
            'board_comments',
            "noteid {$notesinsql}",
            $notesinparams
        );

        $DB->delete_records('board_history', ['boardid' => $boardid]);

        // Delete all image files from the notes.
        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'mod_board', 'images', $notesinsql, $notesinparams);
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
            $board = $DB->get_record('board', ['id' => $cm->instance]);

            $columnids = $DB->get_fieldset_select(
                'board_columns', 'id',
                "boardid = :boardid",
                ['boardid' => $board->id]
            );

            list($columnsinsql, $columnsinparams) = $DB->get_in_or_equal($columnids, SQL_PARAMS_NAMED);
            $noteids = $DB->get_fieldset_select(
                'board_notes', 'id',
                "columnid {$columnsinsql}",
                $columnsinparams
            );

            list($notesinsql, $notesinparams) = $DB->get_in_or_equal($noteids, SQL_PARAMS_NAMED);
            $notesparams = array_merge(['userid' => $userid], $notesinparams);

            // Delete all board notes.
            $DB->delete_records_select(
                'board_notes',
                "userid = :userid AND id {$notesinsql}",
                $notesparams
            );

            // Delete all board note ratings.
            $DB->delete_records_select(
                'board_note_ratings',
                "userid = :userid AND noteid {$notesinsql}",
                $notesparams
            );

            // Delete all board comments.
            $DB->delete_records_select(
                'board_comments',
                "userid = :userid AND noteid {$notesinsql}",
                $notesparams
            );

            $DB->delete_records('board_history', [
                'boardid' => $board->id,
                'userid' => $userid,
            ]);

            // Delete all image files from the notes.
            $fs = get_file_storage();
            $fs->delete_area_files_select($context->id, 'mod_board', 'images', $notesinsql, $notesinparams);
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
        $board = $DB->get_record('board', ['id' => $cm->instance]);

        list($userinsql, $userinparams) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
        $params = array_merge(['boardid' => $board->id], $userinparams);

        $columnids = $DB->get_fieldset_select(
            'board_columns', 'id',
            "boardid = :boardid)",
            ['boardid' => $board->id]
        );

        list($columnsinsql, $columnsinparams) = $DB->get_in_or_equal($columnids, SQL_PARAMS_NAMED);
        $noteids = $DB->get_fieldset_select(
            'board_notes', 'id',
            "columnid {$columnsinsql}",
            $columnsinparams
        );

        $notesparams = array_merge(['boardid' => $board->id], $userinparams);
        list($notesinsql, $notesinparams) = $DB->get_in_or_equal($noteids, SQL_PARAMS_NAMED);
        $notesparams = array_merge($userinparams, $notesinparams);
        $DB->delete_records_select(
            'board_notes',
            "userid {$userinsql} AND id {$notesinsql}",
            $notesparams
        );
        $DB->delete_records_select(
            'board_note_ratings',
            "userid {$userinsql} AND noteid {$notesinsql}",
            $notesparams
        );
        $DB->delete_records_select(
            'board_comments',
            "userid {$userinsql} AND noteid {$notesinsql}",
            $notesparams
        );
        $DB->delete_records_select('board_history', "boardid = :boardid AND userid {$userinsql}", $params);

        // Delete all image files from the posts.
        $fs = get_file_storage();
        $fs->delete_area_files_select($context->id, 'mod_board', 'images', $notesinsql, $notesinparams);

    }

}
