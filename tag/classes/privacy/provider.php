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
 * Privacy Subsystem implementation for core_tag.
 *
 * @package    core_tag
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_tag\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use core_privacy\local\request\userlist;
use core_privacy\local\request\approved_userlist;

/**
 * Privacy Subsystem implementation for core_tag.
 *
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        // Tags store user data.
        \core_privacy\local\metadata\provider,

        // The tag subsystem provides data to other components.
        \core_privacy\local\request\subsystem\plugin_provider,

        // This plugin is capable of determining which users have data within it.
        \core_privacy\local\request\core_userlist_provider,

        // The tag subsystem may have data that belongs to this user.
        \core_privacy\local\request\plugin\provider,

        \core_privacy\local\request\shared_userlist_provider
    {

    /**
     * Returns meta data about this system.
     *
     * @param   collection     $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        // The table 'tag' contains data that a user has entered.
        // It is currently linked with a userid, but this field will hopefulyl go away.
        // Note: The userid is not necessarily 100% accurate. See MDL-61555.
        $collection->add_database_table('tag', [
                'name' => 'privacy:metadata:tag:name',
                'rawname' => 'privacy:metadata:tag:rawname',
                'description' => 'privacy:metadata:tag:description',
                'flag' => 'privacy:metadata:tag:flag',
                'timemodified' => 'privacy:metadata:tag:timemodified',
                'userid' => 'privacy:metadata:tag:userid',
            ], 'privacy:metadata:tag');

        // The table 'tag_instance' contains user data.
        // It links the user of a specific tag, to the item which is tagged.
        // In some cases the userid who 'owns' the tag is also stored.
        $collection->add_database_table('tag_instance', [
                'tagid' => 'privacy:metadata:taginstance:tagid',
                'ordering' => 'privacy:metadata:taginstance:ordering',
                'timecreated' => 'privacy:metadata:taginstance:timecreated',
                'timemodified' => 'privacy:metadata:taginstance:timemodified',
                'tiuserid' => 'privacy:metadata:taginstance:tiuserid',
            ], 'privacy:metadata:taginstance');

        // The table 'tag_area' does not contain any specific user data.
        // It links components and item types to collections and describes how they can be associated.

        // The table 'tag_coll' does not contain any specific user data.
        // It describes a list of tag collections configured by the administrator.

        // The table 'tag_correlation' does not contain any user data.
        // It is a cache for other data already stored.

        return $collection;
    }

    /**
     * Store all tags which match the specified component, itemtype, and itemid.
     *
     * In most situations you will want to specify $onlyuser as false.
     * This will fetch only tags where the user themselves set the tag, or where tags are a shared resource.
     *
     * If you specify $onlyuser as true, only the tags created by that user will be included.
     *
     * @param   int         $userid The user whose information is to be exported
     * @param   \context    $context The context to export for
     * @param   array       $subcontext The subcontext within the context to export this information
     * @param   string      $component The component to fetch data from
     * @param   string      $itemtype The itemtype that the data was exported in within the component
     * @param   int         $itemid The itemid within that tag
     * @param   bool        $onlyuser Whether to only export ratings that the current user has made, or all tags
     */
    public static function export_item_tags(
        int $userid,
        \context $context,
        array $subcontext,
        string $component,
        string $itemtype,
        int $itemid,
        bool $onlyuser = false
    ) {
        global $DB;

        // Ignore mdl_tag.userid here because it only reflects the user who originally created the tag.
        $sql = "SELECT
                    t.rawname
                  FROM {tag} t
            INNER JOIN {tag_instance} ti ON ti.tagid = t.id
                 WHERE ti.component = :component
                   AND ti.itemtype = :itemtype
                   AND ti.itemid = :itemid
                   ";

        if ($onlyuser) {
            $sql .= "AND ti.tiuserid = :userid";
        } else {
            $sql .= "AND (ti.tiuserid = 0 OR ti.tiuserid = :userid)";
        }

        $params = [
            'component' => $component,
            'itemtype' => $itemtype,
            'itemid' => $itemid,
            'userid' => $userid,
        ];

        if ($tags = $DB->get_fieldset_sql($sql, $params)) {
            $writer = \core_privacy\local\request\writer::with_context($context)
                ->export_related_data($subcontext, 'tags', $tags);
        }
    }

    /**
     * Deletes all tag instances for given context, component, itemtype, itemid
     *
     * In most situations you will want to specify $userid as null. Per-user tag instances
     * are possible in Tags API, however there are no components or standard plugins that actually use them.
     *
     * @param   \context    $context The context to export for
     * @param   string      $component Tagarea component
     * @param   string      $itemtype Tagarea item type
     * @param   int         $itemid The itemid within that component and itemtype (optional)
     * @param   int         $userid Only delete tag instances made by this user, per-user tags must be enabled for the tagarea
     */
    public static function delete_item_tags(\context $context, $component, $itemtype,
            $itemid = null, $userid = null) {
        global $DB;
        $params = ['contextid' => $context->id, 'component' => $component, 'itemtype' => $itemtype];
        if ($itemid) {
            $params['itemid'] = $itemid;
        }
        if ($userid) {
            $params['tiuserid'] = $userid;
        }
        $DB->delete_records('tag_instance', $params);
    }

    /**
     * Deletes all tag instances for given context, component, itemtype using subquery for itemids
     *
     * In most situations you will want to specify $userid as null. Per-user tag instances
     * are possible in Tags API, however there are no components or standard plugins that actually use them.
     *
     * @param   \context    $context The context to export for
     * @param   string      $component Tagarea component
     * @param   string      $itemtype Tagarea item type
     * @param   string      $itemidstest an SQL fragment that the itemid must match. Used
     *      in the query like WHERE itemid $itemidstest. Must use named parameters,
     *      and may not use named parameters called contextid, component or itemtype.
     * @param array $params any query params used by $itemidstest.
     */
    public static function delete_item_tags_select(\context $context, $component, $itemtype,
                                            $itemidstest, $params = []) {
        global $DB;
        $params += ['contextid' => $context->id, 'component' => $component, 'itemtype' => $itemtype];
        $DB->delete_records_select('tag_instance',
            'contextid = :contextid AND component = :component AND itemtype = :itemtype AND itemid ' . $itemidstest,
            $params);
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int         $userid     The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();
        $contextlist->add_from_sql("SELECT c.id
                  FROM {context} c
                  JOIN {tag} t ON t.userid = :userid
                 WHERE contextlevel = :contextlevel",
            ['userid' => $userid, 'contextlevel' => CONTEXT_SYSTEM]);
        return $contextlist;
    }

    /**
     * Get the list of users within a specific context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_system) {
            return;
        }

        $sql = "SELECT userid
                  FROM {tag}";

        $userlist->add_from_sql('userid', $sql, []);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;
        $context = \context_system::instance();
        if (!$contextlist->count() || !in_array($context->id, $contextlist->get_contextids())) {
            return;
        }

        $user = $contextlist->get_user();
        $sql = "SELECT id, userid, tagcollid, name, rawname, isstandard, description, descriptionformat, flag, timemodified
            FROM {tag} WHERE userid = ?";
        $rs = $DB->get_recordset_sql($sql, [$user->id]);
        foreach ($rs as $record) {
            $subcontext = [get_string('tags', 'tag'), $record->id];
            $tag = (object)[
                'id' => $record->id,
                'userid' => transform::user($record->userid),
                'name' => $record->name,
                'rawname' => $record->rawname,
                'isstandard' => transform::yesno($record->isstandard),
                'description' => writer::with_context($context)->rewrite_pluginfile_urls($subcontext,
                    'tag', 'description', $record->id, strval($record->description)),
                'descriptionformat' => $record->descriptionformat,
                'flag' => $record->flag,
                'timemodified' => transform::datetime($record->timemodified),

            ];
            writer::with_context($context)->export_data($subcontext, $tag);
            writer::with_context($context)->export_area_files($subcontext, 'tag', 'description', $record->id);
        }
        $rs->close();
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * We do not delete tag instances in this method - this should be done by the components that define tagareas.
     * We only delete tags themselves in case of system context.
     *
     * @param context $context   The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;
        // Tags can only be defined in system context.
        if ($context->id == \context_system::instance()->id) {
            $DB->delete_records('tag_instance');
            $DB->delete_records('tag', []);
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if ($context instanceof \context_system) {
            // Do not delete tags themselves in case they are used by somebody else.
            // If the user is the only one using the tag, it will be automatically deleted anyway during the
            // next cron cleanup.
            list($usersql, $userparams) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
            $DB->set_field_select('tag', 'userid', 0, "userid {$usersql}", $userparams);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        $context = \context_system::instance();
        if (!$contextlist->count() || !in_array($context->id, $contextlist->get_contextids())) {
            return;
        }

        // Do not delete tags themselves in case they are used by somebody else.
        // If the user is the only one using the tag, it will be automatically deleted anyway during the next cron cleanup.
        $DB->set_field_select('tag', 'userid', 0, 'userid = ?', [$contextlist->get_user()->id]);
    }
}
