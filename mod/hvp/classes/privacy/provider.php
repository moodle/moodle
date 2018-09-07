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
 * Privacy Subsystem implementation for H5P.
 */

namespace mod_hvp\privacy;

use \core_privacy\local\request\writer;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\deletion_criteria;
use \core_privacy\local\metadata\collection;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Subsystem implementation for H5P.
 */
class provider implements
    // This plugin has data.
    \core_privacy\local\metadata\provider,

    // This plugin currently implements the original plugin_provider interface.
    \core_privacy\local\request\plugin\provider {

    use \core_privacy\local\legacy_polyfill;

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param collection $items The collection to add metadata to.
     *
     * @return collection The array of metadata
     */
    public static function _get_metadata(collection $items) {
        // Stores files using the Moodle file api.
        $items->add_subsystem_link(
            'core_files',
            [],
            'privacy:metadata:core_files'
        );

        // Stores grades using the Moodle gradebook api.
        $items->add_subsystem_link(
            'core_grades',
            [],
            'privacy:metadata:core_grades'
        );

        // Content user data table.
        $items->add_database_table('hvp_content_user_data', [
            'id'                       => 'privacy:metadata:hvp_content_user_data:id',
            'user_id'                  => 'privacy:metadata:hvp_content_user_data:user_id',
            'hvp_id'                   => 'privacy:metadata:hvp_content_user_data:hvp_id',
            'sub_content_id'           => 'privacy:metadata:hvp_content_user_data:sub_content_id',
            'data_id'                  => 'privacy:metadata:hvp_content_user_data:data_id',
            'data'                     => 'privacy:metadata:hvp_content_user_data:data',
            'preloaded'                => 'privacy:metadata:hvp_content_user_data:preloaded',
            'delete_on_content_change' => 'privacy:metadata:hvp_content_user_data:delete_on_content_change',
        ],
            'privacy:metadata:hvp_content_user_data'
        );

        // Events table.
        $items->add_database_table('hvp_events', [
            'id'              => 'privacy:metadata:hvp_events:id',
            'user_id'         => 'privacy:metadata:hvp_events:user_id',
            'created_at'      => 'privacy:metadata:hvp_events:created_at',
            'type'            => 'privacy:metadata:hvp_events:type',
            'sub_type'        => 'privacy:metadata:hvp_events:sub_type',
            'content_id'      => 'privacy:metadata:hvp_events:content_id',
            'content_title'   => 'privacy:metadata:hvp_events:content_title',
            'library_name'    => 'privacy:metadata:hvp_events:library_name',
            'library_version' => 'privacy:metadata:hvp_events:library_version',
        ], 'privacy:metadata:hvp_events');

        // Xapi results table.
        $items->add_database_table('hvp_xapi_results', [
            'id'                        => 'privacy:metadata:hvp_xapi_results:id',
            'content_id'                => 'privacy:metadata:hvp_xapi_results:content_id',
            'user_id'                   => 'privacy:metadata:hvp_xapi_results:user_id',
            'parent_id'                 => 'privacy:metadata:hvp_xapi_results:parent_id',
            'interaction_type'          => 'privacy:metadata:hvp_xapi_results:interaction_type',
            'description'               => 'privacy:metadata:hvp_xapi_results:description',
            'correct_responses_pattern' => 'privacy:metadata:hvp_xapi_results:correct_responses_pattern',
            'response'                  => 'privacy:metadata:hvp_xapi_results:response',
            'additionals'               => 'privacy:metadata:hvp_xapi_results:additionals',
            'raw_score'                 => 'privacy:metadata:hvp_xapi_results:raw_score',
            'max_score'                 => 'privacy:metadata:hvp_xapi_results:max_score',
        ], 'privacy:metadata:hvp_xapi_results');

        return $items;
    }

    /**
     * Get the list of contexts where the specified user has attempted a quiz, or been involved with manual marking
     * and/or grading of a quiz.
     *
     * @param int $userid The user to search.
     *
     * @return contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function _get_contexts_for_userid($userid) {
        $contextlist = new contextlist();

        // Context for content_user_data.
        $cudsql = "
          SELECT
            c.id
          FROM {course_modules} cm
            INNER JOIN {modules} m ON m.id = cm.module
            INNER JOIN {context} c ON c.instanceid = cm.id
            INNER JOIN {hvp} h ON h.id = cm.instance
            INNER JOIN {hvp_content_user_data} d ON h.id = d.hvp_id
          WHERE m.name = 'hvp'
                AND c.contextlevel = :contextlevel
                AND d.user_id = :userid
        ";

        $cudparams = array(
            'contextlevel' => CONTEXT_MODULE,
            'userid'       => $userid,
        );

        $contextlist->add_from_sql($cudsql, $cudparams);

        // Context for xapi results.
        $xapisql = "
          SELECT
            c.id
          FROM {course_modules} cm
            INNER JOIN {modules} m ON m.id = cm.module
            INNER JOIN {context} c ON c.instanceid = cm.id
            INNER JOIN {hvp} h ON h.id = cm.instance
            INNER JOIN {hvp_xapi_results} x ON x.content_id = h.id
          WHERE m.name = 'hvp'
                AND c.contextlevel = :contextlevel
                AND x.user_id = :userid
        ";

        $xapiparams = array(
            'contextlevel' => CONTEXT_MODULE,
            'userid'       => $userid,
        );

        $contextlist->add_from_sql($xapisql, $xapiparams);

        // Table hvp_events note:
        // H5P events are not tied to instance ids, thus we cannot determine
        // their context ids, they are considered to be user level context
        // actions.

        return $contextlist;
    }

    /**
     * Export all user data for the specified user, in the specified contexts, using the supplied
     * exporter instance.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function _export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (!count($contextlist)) {
            return;
        }

        $user   = $contextlist->get_user();
        $userid = $user->id;
        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $cud = self::get_exportable_content_user_data($contextsql, $contextparams, $userid);
        $xapi = self::get_exportable_xapi_results($contextsql, $contextparams, $userid);

        // Export data with context.
        foreach ($contextlist->get_contexts() as $context) {
            $h5pdata = \core_privacy\local\request\helper::get_context_data($context, $contextlist->get_user());
            \core_privacy\local\request\helper::export_context_files($context, $contextlist->get_user());

            // Add content user data.
            if (!empty($cud[$context->id]) && !empty($cud[$context->id]->data)) {
                $h5pdata->contentuserdata = $cud[$context->id]->data;
            }

            // Add xAPI data.
            if (!empty($xapi[$context->id])) {
                $h5pdata->xapiresults = $xapi[$context->id];
            }

            writer::with_context($context)->export_data([], $h5pdata);
        }

        // Write H5PEvents to subcontext of the user context.
        $usercontext = \context_user::instance($userid);
        $h5pevents = self::get_exportable_events($userid);
        writer::with_context($usercontext)->export_data(['H5PEvents'], $h5pevents);
    }

    /**
     * Get exportable content user data for a given context and user
     *
     * @param $contextsql
     * @param $contextparams
     * @param $userid
     *
     * @return array Exportable and writable content user data
     * @throws \dml_exception
     */
    protected static function get_exportable_content_user_data($contextsql, $contextparams, $userid) {
        global $DB;

        $cudsql = "
          SELECT
            c.id as contextid,
            cm.id as cmid,
            h.id,
            h.name,
            cud.data
          FROM {context} c
            INNER JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
            INNER JOIN {modules} m ON m.id = cm.module AND m.name = :modname
            INNER JOIN {hvp} h ON h.id = cm.instance
            INNER JOIN {hvp_content_user_data} cud ON cud.hvp_id = h.id
          WHERE cud.user_id = :userid
          AND c.id {$contextsql}
        ";

        $cudparams = [
            'contextlevel' => CONTEXT_MODULE,
            'modname'      => 'hvp',
            'userid'       => $userid,
        ];
        $cudparams += $contextparams;

        $cudresult = $DB->get_recordset_sql($cudsql, $cudparams);
        $cud       = [];
        foreach ($cudresult as $record) {
            $cud[$record->contextid] = $record;
        }
        $cudresult->close();

        return $cud;
    }

    /**
     * Get exportable xapi results from context and user id
     *
     * @param $contextsql
     * @param $contextparams
     * @param $userid
     *
     * @return array Exportable and writable xapi results
     * @throws \dml_exception
     */
    protected static function get_exportable_xapi_results($contextsql, $contextparams, $userid) {
        global $DB;

        $xapisql = "
          SELECT
            c.id as contextid,
            cm.id as cmid,
            h.id,
            h.name,
            x.content_id,
            x.parent_id,
            x.description,
            x.response,
            x.additionals,
            x.raw_score,
            x.max_score
          FROM {context} c
            INNER JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
            INNER JOIN {modules} m ON m.id = cm.module AND m.name = :modname
            INNER JOIN {hvp} h ON h.id = cm.instance
            INNER JOIN {hvp_xapi_results} x ON x.content_id = h.id
          WHERE x.user_id = :userid
            AND c.id {$contextsql}
        ";

        $xapiparams = [
            'contextlevel' => CONTEXT_MODULE,
            'modname'      => 'hvp',
            'userid'       => $userid,
        ];
        $xapiparams += $contextparams;

        $xapiresult = $DB->get_recordset_sql($xapisql, $xapiparams);
        $xapi       = [];
        foreach ($xapiresult as $record) {
            $h5pxapi = (object) array();
            if (!empty($record->content_id)) {
                $h5pxapi->content_id = $record->content_id;
            }

            if (!empty($record->parent_id)) {
                $h5pxapi->parent_id = $record->parent_id;
            }

            if (!empty($record->description)) {
                $h5pxapi->description = $record->description;
            }

            if (!empty($record->response)) {
                $h5pxapi->response = $record->response;
            }

            if (!empty($record->additionals)) {
                $h5pxapi->additionals = $record->additionals;
            }

            if (!empty($record->raw_score)) {
                $h5pxapi->raw_score = $record->raw_score;
            }

            if (!empty($record->max_score)) {
                $h5pxapi->max_score = $record->max_score;
            }

            $xapi[$record->contextid][] = $h5pxapi;
        }
        $xapiresult->close();

        return $xapi;
    }

    /**
     * Get exportable H5P events from user id
     *
     * @param $userid
     *
     * @return object Exportable and writable H5P events
     * @throws \dml_exception
     */
    protected static function get_exportable_events($userid) {
        global $DB;

        $eventssql = "
          SELECT *
          FROM {hvp_events}
          WHERE user_id = :userid
        ";

        $eventsparams = [
            'userid' => $userid,
        ];

        $h5peventsresults = $DB->get_recordset_sql($eventssql, $eventsparams);
        $h5pevents        = (object) [
            'events' => [],
        ];
        foreach ($h5peventsresults as $event) {
            $h5pevents->events[] = $event;
        }
        $h5peventsresults->close();

        return $h5pevents;
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context The specific context to delete data for.
     */
    public static function _delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context->contextlevel == CONTEXT_USER) {
            // Delete all H5P events.
            $DB->delete_records('hvp_events');
            return;
        } else if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }

        $cm = get_coursemodule_from_id('hvp', $context->instanceid);
        if (!$cm) {
            return;
        }

        // Delete content user data.
        $DB->delete_records('hvp_content_user_data', [
            'hvp_id' => $cm->instance,
        ]);

        // Delete xAPI results.
        $DB->delete_records('hvp_xapi_results', [
            'content_id' => $cm->instance,
        ]);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function _delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        $count = $contextlist->count();
        if (empty($count)) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            $cm = get_coursemodule_from_id('hvp', $context->instanceid);

            if (!$cm) {
                continue;
            }

            // Delete content user data.
            $DB->delete_records('hvp_content_user_data', [
                'hvp_id'  => $cm->instance,
                'user_id' => $userid,
            ]);

            // Delete xAPI results.
            $DB->delete_records('hvp_xapi_results', [
                'content_id' => $cm->instance,
                'user_id'    => $userid,
            ]);
        }

        // Delete H5P events.
        $DB->delete_records('hvp_events', [
            'user_id' => $userid,
        ]);
    }
}
