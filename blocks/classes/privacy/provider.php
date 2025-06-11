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
 * Data provider.
 *
 * @package    core_block
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_block\privacy;
defined('MOODLE_INTERNAL') || die();

use context;
use context_block;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;

/**
 * Data provider class.
 *
 * @package    core_block
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\subsystem\provider,
    \core_privacy\local\request\user_preference_provider,
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Returns metadata.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_user_preference('blockIDhidden', 'privacy:metadata:userpref:hiddenblock');
        $collection->add_user_preference('docked_block_instance_ID', 'privacy:metadata:userpref:dockedinstance');
        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): \core_privacy\local\request\contextlist {
        global $DB;
        $contextlist = new \core_privacy\local\request\contextlist();

        // Fetch the block instance IDs.
        $likehidden = $DB->sql_like('name', ':hidden', false, false);
        $likedocked = $DB->sql_like('name', ':docked', false, false);
        $sql = "userid = :userid AND ($likehidden OR $likedocked)";
        $params = [
            'userid' => $userid,
            'hidden' => 'block%hidden',
            'docked' => 'docked_block_instance_%',
        ];
        $prefs = $DB->get_fieldset_select('user_preferences', 'name', $sql, $params);

        $instanceids = array_unique(array_map(function($prefname) {
            if (preg_match('/^block(\d+)hidden$/', $prefname, $matches)) {
                return $matches[1];
            } else if (preg_match('/^docked_block_instance_(\d+)$/', $prefname, $matches)) {
                return $matches[1];
            }
            return 0;
        }, $prefs));

        // Find the context of the instances.
        if (!empty($instanceids)) {
            list($insql, $inparams) = $DB->get_in_or_equal($instanceids, SQL_PARAMS_NAMED);
            $sql = "
                SELECT ctx.id
                  FROM {context} ctx
                 WHERE ctx.instanceid $insql
                   AND ctx.contextlevel = :blocklevel";
            $params = array_merge($inparams, ['blocklevel' => CONTEXT_BLOCK]);
            $contextlist->add_from_sql($sql, $params);
        }

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   \core_privacy\local\request\userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(\core_privacy\local\request\userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();
        if ($context->contextlevel != CONTEXT_BLOCK) {
            return;
        }

        $params = ['docked' => 'docked_block_instance_' . $context->instanceid,
                   'hidden' => 'block' . $context->instanceid . 'hidden'];

        $sql = "SELECT userid
                  FROM {user_preferences}
                 WHERE name = :hidden OR name = :docked";
        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;
        $userid = $contextlist->get_user()->id;

        // Extract the block instance IDs.
        $instanceids = array_reduce($contextlist->get_contexts(), function($carry, $context) {
            if ($context->contextlevel == CONTEXT_BLOCK) {
                $carry[] = $context->instanceid;
            }
            return $carry;
        }, []);
        if (empty($instanceids)) {
            return;
        }

        // Query the blocks and their preferences.
        list($insql, $inparams) = $DB->get_in_or_equal($instanceids, SQL_PARAMS_NAMED);
        $hiddenkey = $DB->sql_concat("'block'", 'bi.id', "'hidden'");
        $dockedkey = $DB->sql_concat("'docked_block_instance_'", 'bi.id');
        $sql = "
            SELECT bi.id, h.value AS prefhidden, d.value AS prefdocked
              FROM {block_instances} bi
         LEFT JOIN {user_preferences} h
                ON h.userid = :userid1
               AND h.name = $hiddenkey
         LEFT JOIN {user_preferences} d
                ON d.userid = :userid2
               AND d.name = $dockedkey
             WHERE bi.id $insql
               AND (h.id IS NOT NULL
                OR d.id IS NOT NULL)";
        $params = array_merge($inparams, [
            'userid1' => $userid,
            'userid2' => $userid,
        ]);

        // Export all the things.
        $dockedstr = get_string('privacy:request:blockisdocked', 'core_block');
        $hiddenstr = get_string('privacy:request:blockishidden', 'core_block');
        $recordset = $DB->get_recordset_sql($sql, $params);
        foreach ($recordset as $record) {
            $context = context_block::instance($record->id);
            if ($record->prefdocked !== null) {
                writer::with_context($context)->export_user_preference(
                    'core_block',
                    'block_is_docked',
                    transform::yesno($record->prefdocked),
                    $dockedstr
                );
            }
            if ($record->prefhidden !== null) {
                writer::with_context($context)->export_user_preference(
                    'core_block',
                    'block_is_hidden',
                    transform::yesno($record->prefhidden),
                    $hiddenstr
                );
            }
        }
        $recordset->close();
    }

    /**
     * Export all user preferences for the plugin.
     *
     * @param int $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {
      // Our preferences aren't site-wide so they are exported in export_user_data.
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(context $context) {
        global $DB;
        if ($context->contextlevel != CONTEXT_BLOCK) {
            return;
        }

        // Delete the user preferences.
        $instanceid = $context->instanceid;
        $DB->delete_records_list('user_preferences', 'name', [
            "block{$instanceid}hidden",
            "docked_block_instance_{$instanceid}"
        ]);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        $userid = $contextlist->get_user()->id;
        $prefnames = array_reduce($contextlist->get_contexts(), function($carry, $context) {
            if ($context->contextlevel == CONTEXT_BLOCK) {
                $carry[] = "block{$context->instanceid}hidden";
                $carry[] = "docked_block_instance_{$context->instanceid}";
            }
            return $carry;
        }, []);

        if (empty($prefnames)) {
            return;
        }

        list($insql, $inparams) = $DB->get_in_or_equal($prefnames, SQL_PARAMS_NAMED);
        $sql = "userid = :userid AND name $insql";
        $params = array_merge($inparams, ['userid' => $userid]);
        $DB->delete_records_select('user_preferences', $sql, $params);
    }


    /**
     * Delete multiple users within a single context.
     *
     * @param \core_privacy\local\request\approved_userlist $userlist The approved context and user information to delete
     * information for.
     */
    public static function delete_data_for_users(\core_privacy\local\request\approved_userlist $userlist) {
        global $DB;
        $context = $userlist->get_context();
        if ($context->contextlevel != CONTEXT_BLOCK) {
            return;
        }

        list($insql, $params) = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
        $params['hidden'] = 'block' . $context->instanceid . 'hidden';
        $params['docked'] = 'docked_block_instance_' . $context->instanceid;

        $DB->delete_records_select('user_preferences', "(name = :hidden OR name = :docked) AND userid $insql", $params);
    }
}
