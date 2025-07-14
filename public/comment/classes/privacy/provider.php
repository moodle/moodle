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
 * Privacy class for requesting user data.
 *
 * @package    core_comment
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_comment\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\transform;
use \core_privacy\local\request\userlist;

/**
 * Privacy class for requesting user data.
 *
 * @package    core_comment
 * @copyright  2018 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\subsystem\plugin_provider,
        \core_privacy\local\request\shared_userlist_provider
    {

    /**
     * Returns meta data about this system.
     *
     * @param   collection     $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('comments', [
                'content' => 'privacy:metadata:comment:content',
                'timecreated' => 'privacy:metadata:comment:timecreated',
                'userid' => 'privacy:metadata:comment:userid',
            ], 'privacy:metadata:comment');

        return $collection;
    }

    /**
     * Writes user data to the writer for the user to download.
     *
     * @param  \context $context The context to export data for.
     * @param  string $component The component that is calling this function
     * @param  string $commentarea The comment area related to the component
     * @param  int    $itemid An identifier for a group of comments
     * @param  array  $subcontext The sub-context in which to export this data
     * @param  bool   $onlyforthisuser  Only return the comments this user made.
     */
    public static function export_comments(\context $context, string $component, string $commentarea, int $itemid,
                                           array $subcontext, bool $onlyforthisuser = true) {
        global $USER, $DB;
        $params = [
            'contextid' => $context->id,
            'component' => $component,
            'commentarea' => $commentarea,
            'itemid' => $itemid
        ];
        $sql = "SELECT c.id, c.content, c.format, c.timecreated, c.userid
                  FROM {comments} c
                 WHERE c.contextid = :contextid AND
                       c.commentarea = :commentarea AND
                       c.itemid = :itemid AND
                       (c.component IS NULL OR c.component = :component)";
        if ($onlyforthisuser) {
            $sql .= " AND c.userid = :userid";
            $params['userid'] = $USER->id;
        }
        $sql .= " ORDER BY c.timecreated DESC";

        $rs = $DB->get_recordset_sql($sql, $params);
        $comments = [];
        foreach ($rs as $record) {
            if ($record->userid != $USER->id) {
                // Clean HTML in comments that were added by other users.
                $comment = ['content' => format_text($record->content, $record->format, ['context' => $context])];
            } else {
                // Export comments made by this user as they are stored.
                $comment = ['content' => $record->content, 'contentformat' => $record->format];
            }
            $comment += [
                'time' => transform::datetime($record->timecreated),
                'userid' => transform::user($record->userid),
            ];
            $comments[] = (object)$comment;
        }
        $rs->close();

        if (!empty($comments)) {
            $subcontext[] = get_string('commentsubcontext', 'core_comment');
            \core_privacy\local\request\writer::with_context($context)
                ->export_data($subcontext, (object) [
                    'comments' => $comments,
                ]);
        }
    }

    /**
     * Deletes all comments for a specified context, component, and commentarea.
     *
     * @param  \context $context Details about which context to delete comments for.
     * @param  string $component Component to delete.
     * @param  string $commentarea Comment area to delete.
     * @param  int $itemid The item ID for use with deletion.
     */
    public static function delete_comments_for_all_users(\context $context, string $component, ?string $commentarea = null,
            ?int $itemid = null) {
        global $DB;
        $params = [
            'contextid' => $context->id,
            'component' => $component
        ];
        if (isset($commentarea)) {
            $params['commentarea'] = $commentarea;
        }
        if (isset($itemid)) {
            $params['itemid'] = $itemid;
        }
        $DB->delete_records('comments', $params);
    }

    /**
     * Deletes all comments for a specified context, component, and commentarea.
     *
     * @param  \context $context Details about which context to delete comments for.
     * @param  string $component Component to delete.
     * @param  string $commentarea Comment area to delete.
     * @param  string $itemidstest an SQL fragment that the itemid must match. Used
     *      in the query like WHERE itemid $itemidstest. Must use named parameters,
     *      and may not use named parameters called contextid, component or commentarea.
     * @param array $params any query params used by $itemidstest.
     */
    public static function delete_comments_for_all_users_select(\context $context, string $component, string $commentarea,
            $itemidstest, $params = []) {
        global $DB;
        $params += ['contextid' => $context->id, 'component' => $component, 'commentarea' => $commentarea];
        $DB->delete_records_select('comments',
            'contextid = :contextid AND component = :component AND commentarea = :commentarea AND itemid ' . $itemidstest,
            $params);
    }

    /**
     * Deletes all records for a user from a list of approved contexts.
     *
     * @param  \core_privacy\local\request\approved_contextlist $contextlist Contains the user ID and a list of contexts to be
     * deleted from.
     * @param  string $component Component to delete from.
     * @param  string $commentarea Area to delete from.
     * @param  int $itemid The item id to delete from.
     */
    public static function delete_comments_for_user(\core_privacy\local\request\approved_contextlist $contextlist,
            string $component, ?string $commentarea = null, ?int $itemid = null) {
        global $DB;

        $userid = $contextlist->get_user()->id;
        $contextids = implode(',', $contextlist->get_contextids());
        $params = [
            'userid' => $userid,
            'component' => $component,
        ];
        $areasql = '';
        if (isset($commentarea)) {
            $params['commentarea'] = $commentarea;
            $areasql = 'AND commentarea = :commentarea';
        }
        $itemsql = '';
        if (isset($itemid)) {
            $params['itemid'] = $itemid;
            $itemsql = 'AND itemid = :itemid';
        }
        list($insql, $inparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);
        $params += $inparams;

        $select = "userid = :userid AND component = :component $areasql $itemsql AND contextid $insql";
        $DB->delete_records_select('comments', $select, $params);
    }

    /**
     * Deletes all records for a context from a list of approved users.
     *
     * @param  \core_privacy\local\request\approved_userlist $userlist Contains the list of users and
     * a context to be deleted from.
     * @param  string $component Component to delete from.
     * @param  string $commentarea Area to delete from.
     * @param  int $itemid The item id to delete from.
     */
    public static function delete_comments_for_users(\core_privacy\local\request\approved_userlist $userlist,
            string $component, ?string $commentarea = null, ?int $itemid = null) {
        global $DB;

        $context = $userlist->get_context();
        $params = [
            'contextid' => $context->id,
            'component' => $component,
        ];
        $areasql = '';
        if (isset($commentarea)) {
            $params['commentarea'] = $commentarea;
            $areasql = 'AND commentarea = :commentarea';
        }
        $itemsql = '';
        if (isset($itemid)) {
            $params['itemid'] = $itemid;
            $itemsql = 'AND itemid = :itemid';
        }
        list($insql, $inparams) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
        $params += $inparams;

        $select = "contextid = :contextid AND component = :component {$areasql} {$itemsql} AND userid {$insql}";
        $DB->delete_records_select('comments', $select, $params);
    }

    /**
     * Add the list of users who have commented in the specified constraints.
     *
     * @param   userlist    $userlist The userlist to add the users to.
     * @param   string      $alias An alias prefix to use for comment selects to avoid interference with your own sql.
     * @param   string      $component The component to check.
     * @param   string      $area The comment area to check.
     * @param   int         $contextid The context id.
     * @param   string      $insql The SQL to use in a sub-select for the itemid query.
     * @param   array       $params The params required for the insql.
     */
    public static function get_users_in_context_from_sql(
                userlist $userlist, string $alias, string $component, string $area, ?int $contextid = null, string $insql = '',
                array $params = []) {

        if ($insql != '') {
            $insql = "AND {$alias}.itemid {$insql}";
        }
        $contextsql = '';
        if (isset($contextid)) {
            $contextsql = "AND {$alias}.contextid = :{$alias}contextid";
            $params["{$alias}contextid"] = $contextid;
        }

        // Comment authors.
        $sql = "SELECT {$alias}.userid
                  FROM {comments} {$alias}
                 WHERE {$alias}.component = :{$alias}component
                   AND {$alias}.commentarea = :{$alias}commentarea
                   $contextsql $insql";

        $params["{$alias}component"] = $component;
        $params["{$alias}commentarea"] = $area;

        $userlist->add_from_sql('userid', $sql, $params);
    }
}
