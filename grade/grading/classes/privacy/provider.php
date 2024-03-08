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
 * @package    core_grading
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_grading\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\transform;
use \core_privacy\local\request\writer;
use \core_privacy\manager;

/**
 * Privacy class for requesting user data.
 *
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\subsystem\provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection     $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('grading_definitions', [
                'method' => 'privacy:metadata:grading_definitions:method',
                'areaid' => 'privacy:metadata:grading_definitions:areaid',
                'name' => 'privacy:metadata:grading_definitions:name',
                'description' => 'privacy:metadata:grading_definitions:description',
                'status' => 'privacy:metadata:grading_definitions:status',
                'copiedfromid' => 'privacy:metadata:grading_definitions:copiedfromid',
                'timecopied' => 'privacy:metadata:grading_definitions:timecopied',
                'timecreated' => 'privacy:metadata:grading_definitions:timecreated',
                'usercreated' => 'privacy:metadata:grading_definitions:usercreated',
                'timemodified' => 'privacy:metadata:grading_definitions:timemodified',
                'usermodified' => 'privacy:metadata:grading_definitions:usermodified',
                'options' => 'privacy:metadata:grading_definitions:options',
            ], 'privacy:metadata:grading_definitions');

        $collection->add_database_table('grading_instances', [
                'raterid' => 'privacy:metadata:grading_instances:raterid',
                'rawgrade' => 'privacy:metadata:grading_instances:rawgrade',
                'status' => 'privacy:metadata:grading_instances:status',
                'feedback' => 'privacy:metadata:grading_instances:feedback',
                'feedbackformat' => 'privacy:metadata:grading_instances:feedbackformat',
                'timemodified' => 'privacy:metadata:grading_instances:timemodified',
            ], 'privacy:metadata:grading_instances');

        // Link to subplugin.
        $collection->add_plugintype_link('gradingform', [], 'privacy:metadata:gradingformpluginsummary');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        $sql = "SELECT c.id
                  FROM {context} c
                  JOIN {grading_areas} a ON a.contextid = c.id
                  JOIN {grading_definitions} d ON d.areaid = a.id
             LEFT JOIN {grading_instances} i ON i.definitionid = d.id AND i.raterid = :raterid
                 WHERE c.contextlevel = :contextlevel
                   AND (d.usercreated = :usercreated OR d.usermodified = :usermodified OR i.id IS NOT NULL)";
        $params = [
            'usercreated' => $userid,
            'usermodified' => $userid,
            'raterid' => $userid,
            'contextlevel' => CONTEXT_MODULE
        ];
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(\core_privacy\local\request\userlist $userlist) {
        $context = $userlist->get_context();
        if ($context->contextlevel != CONTEXT_MODULE) {
            return;
        }

        $params = ['contextid' => $context->id];

        $sql = "SELECT d.usercreated, d.usermodified
                  FROM {grading_definitions} d
                  JOIN {grading_areas} a ON a.id = d.areaid
                  WHERE a.contextid = :contextid";
        $userlist->add_from_sql('usercreated', $sql, $params);
        $userlist->add_from_sql('usermodified', $sql, $params);

        $sql = "SELECT i.raterid
                  FROM {grading_definitions} d
                  JOIN {grading_areas} a ON a.id = d.areaid
                  JOIN {grading_instances} i ON i.definitionid = d.id
                  WHERE a.contextid = :contextid";
        $userlist->add_from_sql('raterid', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        // Remove contexts different from MODULE.
        $contexts = array_reduce($contextlist->get_contexts(), function($carry, $context) {
            if ($context->contextlevel == CONTEXT_MODULE) {
                $carry[] = $context;
            }
            return $carry;
        }, []);

        if (empty($contexts)) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        $subcontext = [get_string('gradingmethod', 'grading')];
        foreach ($contexts as $context) {
            // Export grading definitions created or modified on this context.
            self::export_definitions($context, $subcontext, $userid);
        }
    }

    /**
     * Export all user data related to a context and itemid.
     *
     * @param  \context $context    Context to export on.
     * @param  int      $itemid     Item ID to export on.
     * @param  array    $subcontext Directory location to export to.
     */
    public static function export_item_data(\context $context, int $itemid, array $subcontext) {
        global $DB;

        $sql = "SELECT gi.id AS instanceid, gd.id AS definitionid, gd.method
                  FROM {grading_areas} ga
                  JOIN {grading_definitions} gd ON gd.areaid = ga.id
                  JOIN {grading_instances} gi ON gi.definitionid = gd.id AND gi.itemid = :itemid
                 WHERE ga.contextid = :contextid";
        $params = [
            'itemid' => $itemid,
            'contextid' => $context->id,
        ];
        $records = $DB->get_recordset_sql($sql, $params);
        foreach ($records as $record) {
            $instancedata = manager::component_class_callback(
                "gradingform_{$record->method}",
                gradingform_provider_v2::class,
                'export_gradingform_instance_data',
                [$context, $record->instanceid, $subcontext]
            );
        }
        $records->close();
    }

    /**
     * Deletes all user data related to a context and possibly an itemid.
     *
     * @param  \context $context The context to delete on.
     * @param  int|null $itemid  An optional item ID to refine the deletion.
     */
    public static function delete_instance_data(\context $context, int $itemid = null) {
        if (is_null($itemid)) {
            self::delete_data_for_instances($context);
        } else {
            self::delete_data_for_instances($context, [$itemid]);
        }
    }

    /**
     * Deletes all user data related to a context and possibly itemids.
     *
     * @param  \context $context The context to delete on.
     * @param  array $itemids  An optional list of item IDs to refine the deletion.
     */
    public static function delete_data_for_instances(\context $context, array $itemids = []) {
        global $DB;
        $itemsql = '';
        $params = ['contextid' => $context->id];
        if (!empty($itemids)) {
            list($itemsql, $itemparams) = $DB->get_in_or_equal($itemids, SQL_PARAMS_NAMED);
            $params = array_merge($params, $itemparams);
            $itemsql = "AND itemid $itemsql";
        }
        $sql = "SELECT gi.id AS instanceid, gd.id, gd.method
                  FROM {grading_definitions} gd
                  JOIN {grading_instances} gi ON gi.definitionid = gd.id
                  JOIN {grading_areas} ga ON ga.id = gd.areaid
                 WHERE ga.contextid = :contextid $itemsql";
        $records = $DB->get_records_sql($sql, $params);
        if ($records) {
            $firstrecord = current($records);
            $method = $firstrecord->method;
            $instanceids = array_map(function($record) {
                return $record->instanceid;
            }, $records);
            manager::component_class_callback(
                "gradingform_{$method}",
                gradingform_provider_v2::class,
                'delete_gradingform_for_instances',
                [$instanceids]);
            // Delete grading_instances rows.
            $DB->delete_records_list('grading_instances', 'id', $instanceids);
        }
    }

    /**
     * Exports the data related to grading definitions within the specified context/subcontext.
     *
     * @param  \context         $context Context owner of the data.
     * @param  array            $subcontext Subcontext owner of the data.
     * @param  int              $userid The user whose information is to be exported.
     */
    protected static function export_definitions(\context $context, array $subcontext, int $userid = 0) {
        global $DB;

        $join = "JOIN {grading_areas} a ON a.id = d.areaid
                 JOIN {context} c ON a.contextid = c.id AND c.contextlevel = :contextlevel";
        $select = 'a.contextid = :contextid';
        $params = [
            'contextlevel' => CONTEXT_MODULE,
            'contextid'    => $context->id
        ];

        if (!empty($userid)) {
            $join .= ' LEFT JOIN {grading_instances} i ON i.definitionid = d.id AND i.raterid = :raterid';
            $select .= ' AND (usercreated = :usercreated
                OR usermodified = :usermodified OR i.id IS NOT NULL)';
            $params['usercreated'] = $userid;
            $params['usermodified'] = $userid;
            $params['raterid'] = $userid;
        }

        $sql = "SELECT gd.id,
                       gd.method,
                       gd.name,
                       gd.description,
                       gd.timecopied,
                       gd.timecreated,
                       gd.usercreated,
                       gd.timemodified,
                       gd.usermodified
                  FROM (
                        SELECT DISTINCT d.id
                                   FROM {grading_definitions} d
                                  $join
                                  WHERE $select
                  ) ids
                  JOIN {grading_definitions} gd ON gd.id = ids.id";
        $definitions = $DB->get_recordset_sql($sql, $params);
        $defdata = [];
        foreach ($definitions as $definition) {
            $tmpdata = [
                'method' => $definition->method,
                'name' => $definition->name,
                'description' => $definition->description,
                'timecreated' => transform::datetime($definition->timecreated),
                'usercreated' => transform::user($definition->usercreated),
                'timemodified' => transform::datetime($definition->timemodified),
                'usermodified' => transform::user($definition->usermodified),
            ];
            if (!empty($definition->timecopied)) {
                $tmpdata['timecopied'] = transform::datetime($definition->timecopied);
            }

            $defdata[] = (object) $tmpdata;

            // Export grading_instances information.
            self::export_grading_instances($context, $subcontext, $definition->id, $userid);
        }
        $definitions->close();

        if (!empty($defdata)) {
            $data = (object) [
                'definitions' => $defdata,
            ];

            writer::with_context($context)->export_data($subcontext, $data);
        }
    }

    /**
     * Exports the data related to grading instances within the specified definition.
     *
     * @param  \context         $context Context owner of the data.
     * @param  array            $subcontext Subcontext owner of the data.
     * @param  int              $definitionid The definition ID whose grading instance information is to be exported.
     * @param  int              $userid The user whose information is to be exported.
     */
    protected static function export_grading_instances(\context $context, array $subcontext, int $definitionid, int $userid = 0) {
        global $DB;

        $params = ['definitionid' => $definitionid];
        if (!empty($userid)) {
            $params['raterid'] = $userid;
        }
        $instances = $DB->get_recordset('grading_instances', $params);
        $instancedata = [];
        foreach ($instances as $instance) {
            // TODO: Get the status name (instead of the ID).
            $tmpdata = [
                'rawgrade' => $instance->rawgrade,
                'status' => $instance->status,
                'feedback' => $instance->feedback,
                'feedbackformat' => $instance->feedbackformat,
                'timemodified' => transform::datetime($instance->timemodified),
            ];
            $instancedata[] = (object) $tmpdata;
        }
        $instances->close();

        if (!empty($instancedata)) {
            $data = (object) [
                'instances' => $instancedata,
            ];

            writer::with_context($context)->export_related_data($subcontext, 'gradinginstances', $data);
        }
    }

    /**
     * No deletion of the advanced grading is done.
     *
     * @param \context $context the context to delete in.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        // The only information left to be deleted here is the grading definitions. Currently we are not deleting these.
    }

    /**
     * Deletion of data in this provider is only related to grades and so can not be
     * deleted for the creator of the advanced grade criteria.
     *
     * @param approved_contextlist $contextlist a list of contexts approved for deletion.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        // The only information left to be deleted here is the grading definitions. Currently we are not deleting these.
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(\core_privacy\local\request\approved_userlist $userlist) {
        // The only information left to be deleted here is the grading definitions. Currently we are not deleting these.
    }
}
