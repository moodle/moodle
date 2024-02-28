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
 * Customfield component provider class
 *
 * @package   core_customfield
 * @copyright 2018 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_customfield\privacy;

defined('MOODLE_INTERNAL') || die();

use core_customfield\data_controller;
use core_customfield\handler;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\writer;
use core_privacy\manager;

/**
 * Class provider
 *
 * Customfields API does not directly store userid and does not perform any export or delete functionality by itself
 *
 * However this class defines several functions that can be utilized by components that use customfields API to
 * export/delete user data.
 *
 * @package core_customfield
 * @copyright 2018 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        // Customfield store data.
        \core_privacy\local\metadata\provider,

        // The customfield subsystem stores data on behalf of other components.
        \core_privacy\local\request\subsystem\plugin_provider,
        \core_privacy\local\request\shared_userlist_provider  {

    /**
     * Return the fields which contain personal data.
     *
     * @param collection $collection a reference to the collection to use to store the metadata.
     * @return collection the updated collection of metadata items.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'customfield_data',
            [
                'fieldid' => 'privacy:metadata:customfield_data:fieldid',
                'instanceid' => 'privacy:metadata:customfield_data:instanceid',
                'intvalue' => 'privacy:metadata:customfield_data:intvalue',
                'decvalue' => 'privacy:metadata:customfield_data:decvalue',
                'shortcharvalue' => 'privacy:metadata:customfield_data:shortcharvalue',
                'charvalue' => 'privacy:metadata:customfield_data:charvalue',
                'value' => 'privacy:metadata:customfield_data:value',
                'valueformat' => 'privacy:metadata:customfield_data:valueformat',
                'valuetrust' => 'privacy:metadata:customfield_data:valuetrust',
                'timecreated' => 'privacy:metadata:customfield_data:timecreated',
                'timemodified' => 'privacy:metadata:customfield_data:timemodified',
                'contextid' => 'privacy:metadata:customfield_data:contextid',
            ],
            'privacy:metadata:customfield_data'
        );

        // Link to subplugins.
        $collection->add_plugintype_link('customfield', [], 'privacy:metadata:customfieldpluginsummary');

        $collection->link_subsystem('core_files', 'privacy:metadata:filepurpose');

        return $collection;
    }

    /**
     * Returns contexts that have customfields data
     *
     * To be used in implementations of core_user_data_provider::get_contexts_for_userid
     * Caller needs to transfer the $userid to the select subqueries for
     * customfield_category->itemid and/or customfield_data->instanceid
     *
     * @param string $component
     * @param string $area
     * @param string $itemidstest subquery for selecting customfield_category->itemid
     * @param string $instanceidstest subquery for selecting customfield_data->instanceid
     * @param array $params array of named parameters
     * @return contextlist
     */
    public static function get_customfields_data_contexts(string $component, string $area,
            string $itemidstest = 'IS NOT NULL', string $instanceidstest = 'IS NOT NULL', array $params = []): contextlist {

        $sql = "SELECT d.contextid FROM {customfield_category} c
            JOIN {customfield_field} f ON f.categoryid = c.id
            JOIN {customfield_data} d ON d.fieldid = f.id AND d.instanceid $instanceidstest
            WHERE c.component = :cfcomponent AND c.area = :cfarea AND c.itemid $itemidstest";

        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, self::get_params($component, $area, $params));

        return $contextlist;
    }

    /**
     * Returns contexts that have customfields configuration (categories and fields)
     *
     * To be used in implementations of core_user_data_provider::get_contexts_for_userid in cases when user is
     * an owner of the fields configuration
     * Caller needs to transfer the $userid to the select subquery for customfield_category->itemid
     *
     * @param string $component
     * @param string $area
     * @param string $itemidstest subquery for selecting customfield_category->itemid
     * @param array $params array of named parameters for itemidstest subquery
     * @return contextlist
     */
    public static function get_customfields_configuration_contexts(string $component, string $area,
            string $itemidstest = 'IS NOT NULL', array $params = []): contextlist {

        $sql = "SELECT c.contextid FROM {customfield_category} c
            WHERE c.component = :cfcomponent AND c.area = :cfarea AND c.itemid $itemidstest";
        $params['component'] = $component;
        $params['area'] = $area;

        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, self::get_params($component, $area, $params));

        return $contextlist;

    }

    /**
     * Exports customfields data
     *
     * To be used in implementations of core_user_data_provider::export_user_data
     * Caller needs to transfer the $userid to the select subqueries for
     * customfield_category->itemid and/or customfield_data->instanceid
     *
     * @param approved_contextlist $contextlist
     * @param string $component
     * @param string $area
     * @param string $itemidstest subquery for selecting customfield_category->itemid
     * @param string $instanceidstest subquery for selecting customfield_data->instanceid
     * @param array $params array of named parameters for itemidstest and instanceidstest subqueries
     * @param array $subcontext subcontext to use in context_writer::export_data, if null (default) the
     *     "Custom fields data" will be used;
     *     the data id will be appended to the subcontext array.
     */
    public static function export_customfields_data(approved_contextlist $contextlist, string $component, string $area,
                string $itemidstest = 'IS NOT NULL', string $instanceidstest = 'IS NOT NULL', array $params = [],
                array $subcontext = null) {
        global $DB;

        // This query is very similar to api::get_instances_fields_data() but also works for multiple itemids
        // and has a context filter.
        list($contextidstest, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED, 'cfctx');
        $sql = "SELECT d.*, f.type AS fieldtype, f.name as fieldname, f.shortname as fieldshortname, c.itemid
            FROM {customfield_category} c
            JOIN {customfield_field} f ON f.categoryid = c.id
            JOIN {customfield_data} d ON d.fieldid = f.id AND d.instanceid $instanceidstest AND d.contextid $contextidstest
            WHERE c.component = :cfcomponent AND c.area = :cfarea AND c.itemid $itemidstest
            ORDER BY c.itemid, c.sortorder, f.sortorder";
        $params = self::get_params($component, $area, $params) + $contextparams;
        $records = $DB->get_recordset_sql($sql, $params);

        if ($subcontext === null) {
            $subcontext = [get_string('customfielddata', 'core_customfield')];
        }

        /** @var handler $handler */
        $handler = null;
        $fields = null;
        foreach ($records as $record) {
            if (!$handler || $handler->get_itemid() != $record->itemid) {
                $handler = handler::get_handler($component, $area, $record->itemid);
                $fields = $handler->get_fields();
            }
            $field = (object)['type' => $record->fieldtype, 'shortname' => $record->fieldshortname, 'name' => $record->fieldname];
            unset($record->itemid, $record->fieldtype, $record->fieldshortname, $record->fieldname);
            try {
                $field = array_key_exists($record->fieldid, $fields) ? $fields[$record->fieldid] : null;
                $data = data_controller::create(0, $record, $field);
                self::export_customfield_data($data, array_merge($subcontext, [$record->id]));
            } catch (\Exception $e) {
                // We store some data that we can not initialise controller for. We still need to export it.
                self::export_customfield_data_unknown($record, $field, array_merge($subcontext, [$record->id]));
            }
        }
        $records->close();
    }

    /**
     * Deletes customfields data
     *
     * To be used in implementations of core_user_data_provider::delete_data_for_user
     * Caller needs to transfer the $userid to the select subqueries for
     * customfield_category->itemid and/or customfield_data->instanceid
     *
     * @param approved_contextlist $contextlist
     * @param string $component
     * @param string $area
     * @param string $itemidstest subquery for selecting customfield_category->itemid
     * @param string $instanceidstest subquery for selecting customfield_data->instanceid
     * @param array $params array of named parameters for itemidstest and instanceidstest subqueries
     */
    public static function delete_customfields_data(approved_contextlist $contextlist, string $component, string $area,
            string $itemidstest = 'IS NOT NULL', string $instanceidstest = 'IS NOT NULL', array $params = []) {
        global $DB;

        list($contextidstest, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED, 'cfctx');
        $sql = "SELECT d.id
            FROM {customfield_category} c
            JOIN {customfield_field} f ON f.categoryid = c.id
            JOIN {customfield_data} d ON d.fieldid = f.id AND d.instanceid $instanceidstest AND d.contextid $contextidstest
            WHERE c.component = :cfcomponent AND c.area = :cfarea AND c.itemid $itemidstest";
        $params = self::get_params($component, $area, $params) + $contextparams;

        self::before_delete_data('IN (' . $sql . ') ', $params);

        $DB->execute("DELETE FROM {customfield_data}
            WHERE instanceid $instanceidstest
            AND contextid $contextidstest
            AND fieldid IN (SELECT f.id
                FROM {customfield_category} c
                JOIN {customfield_field} f ON f.categoryid = c.id
                WHERE c.component = :cfcomponent AND c.area = :cfarea AND c.itemid $itemidstest)", $params);
    }

    /**
     * Deletes customfields configuration (categories and fields) and all relevant data
     *
     * To be used in implementations of core_user_data_provider::delete_data_for_user in cases when user is
     * an owner of the fields configuration and it is considered user information (quite unlikely situtation but we never
     * know what customfields API can be used for)
     *
     * Caller needs to transfer the $userid to the select subquery for customfield_category->itemid
     *
     * @param approved_contextlist $contextlist
     * @param string $component
     * @param string $area
     * @param string $itemidstest subquery for selecting customfield_category->itemid
     * @param array $params array of named parameters for itemidstest subquery
     */
    public static function delete_customfields_configuration(approved_contextlist $contextlist, string $component, string $area,
            string $itemidstest = 'IS NOT NULL', array $params = []) {
        global $DB;

        list($contextidstest, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED, 'cfctx');
        $params = self::get_params($component, $area, $params) + $contextparams;

        $categoriesids = $DB->get_fieldset_sql("SELECT c.id
            FROM {customfield_category} c
            WHERE c.component = :cfcomponent AND c.area = :cfarea AND c.itemid $itemidstest AND c.contextid $contextidstest",
            $params);

        self::delete_categories($contextlist->get_contextids(), $categoriesids);
    }

    /**
     * Deletes all customfields configuration (categories and fields) and all relevant data for the given category context
     *
     * To be used in implementations of core_user_data_provider::delete_data_for_all_users_in_context
     *
     * @param string $component
     * @param string $area
     * @param \context $context
     */
    public static function delete_customfields_configuration_for_context(string $component, string $area, \context $context) {
        global $DB;
        $categoriesids = $DB->get_fieldset_sql("SELECT c.id
            FROM {customfield_category} c
            JOIN {context} ctx ON ctx.id = c.contextid AND ctx.path LIKE :ctxpath
            WHERE c.component = :cfcomponent AND c.area = :cfarea",
            self::get_params($component, $area, ['ctxpath' => $context->path]));

        self::delete_categories([$context->id], $categoriesids);
    }

    /**
     * Deletes all customfields data for the given context
     *
     * To be used in implementations of core_user_data_provider::delete_data_for_all_users_in_context
     *
     * @param string $component
     * @param string $area
     * @param \context $context
     */
    public static function delete_customfields_data_for_context(string $component, string $area, \context $context) {
        global $DB;

        $sql = "SELECT d.id
            FROM {customfield_category} c
            JOIN {customfield_field} f ON f.categoryid = c.id
            JOIN {customfield_data} d ON d.fieldid = f.id
            JOIN {context} ctx ON ctx.id = d.contextid AND ctx.path LIKE :ctxpath
            WHERE c.component = :cfcomponent AND c.area = :cfarea";
        $params = self::get_params($component, $area, ['ctxpath' => $context->path . '%']);

        self::before_delete_data('IN (' . $sql . ') ', $params);

        $DB->execute("DELETE FROM {customfield_data}
            WHERE fieldid IN (SELECT f.id
                FROM {customfield_category} c
                JOIN {customfield_field} f ON f.categoryid = c.id
                WHERE c.component = :cfcomponent AND c.area = :cfarea)
            AND contextid IN (SELECT id FROM {context} WHERE path LIKE :ctxpath)",
            $params);
    }

    /**
     * Checks that $params is an associative array and adds parameters for component and area
     *
     * @param string $component
     * @param string $area
     * @param array $params
     * @return array
     * @throws \coding_exception
     */
    protected static function get_params(string $component, string $area, array $params): array {
        if (!empty($params) && (array_keys($params) === range(0, count($params) - 1))) {
            // Argument $params is not an associative array.
            throw new \coding_exception('Argument $params must be an associative array!');
        }
        return $params + ['cfcomponent' => $component, 'cfarea' => $area];
    }

    /**
     * Delete custom fields categories configurations, all their fields and data
     *
     * @param array $contextids
     * @param array $categoriesids
     */
    protected static function delete_categories(array $contextids, array $categoriesids) {
        global $DB;

        if (!$categoriesids) {
            return;
        }

        list($categoryidstest, $catparams) = $DB->get_in_or_equal($categoriesids, SQL_PARAMS_NAMED, 'cfcat');
        $datasql = "SELECT d.id FROM {customfield_data} d JOIN {customfield_field} f ON f.id = d.fieldid " .
            "WHERE f.categoryid $categoryidstest";
        $fieldsql = "SELECT f.id AS fieldid FROM {customfield_field} f WHERE f.categoryid $categoryidstest";

        self::before_delete_data("IN ($datasql)", $catparams);
        self::before_delete_fields($categoryidstest, $catparams);

        $DB->execute('DELETE FROM {customfield_data} WHERE fieldid IN (' . $fieldsql . ')', $catparams);
        $DB->execute("DELETE FROM {customfield_field} WHERE categoryid $categoryidstest", $catparams);
        $DB->execute("DELETE FROM {customfield_category} WHERE id $categoryidstest", $catparams);

    }

    /**
     * Executes callbacks from the customfield plugins to delete anything related to the data records (usually files)
     *
     * @param string $dataidstest
     * @param array $params
     */
    protected static function before_delete_data(string $dataidstest, array $params) {
        global $DB;
        // Find all field types and all contexts for each field type.
        $records = $DB->get_recordset_sql("SELECT ff.type, dd.contextid
            FROM {customfield_data} dd
            JOIN {customfield_field} ff ON ff.id = dd.fieldid
            WHERE dd.id $dataidstest
            GROUP BY ff.type, dd.contextid",
            $params);

        $fieldtypes = [];
        foreach ($records as $record) {
            $fieldtypes += [$record->type => []];
            $fieldtypes[$record->type][] = $record->contextid;
        }
        $records->close();

        // Call plugin callbacks to delete data customfield_provider::before_delete_data().
        foreach ($fieldtypes as $fieldtype => $contextids) {
            $classname = manager::get_provider_classname_for_component('customfield_' . $fieldtype);
            if (class_exists($classname) && is_subclass_of($classname, customfield_provider::class)) {
                component_class_callback($classname, 'before_delete_data', [$dataidstest, $params, $contextids]);
            }
        }
    }

    /**
     * Executes callbacks from the plugins to delete anything related to the fields (usually files)
     *
     * Also deletes description files
     *
     * @param string $categoryidstest
     * @param array $params
     */
    protected static function before_delete_fields(string $categoryidstest, array $params) {
        global $DB;
        // Find all field types and contexts.
        $fieldsql = "SELECT f.id AS fieldid FROM {customfield_field} f WHERE f.categoryid $categoryidstest";
        $records = $DB->get_recordset_sql("SELECT f.type, c.contextid
            FROM {customfield_field} f
            JOIN {customfield_category} c ON c.id = f.categoryid
            WHERE c.id $categoryidstest",
            $params);

        $contexts = [];
        $fieldtypes = [];
        foreach ($records as $record) {
            $contexts[$record->contextid] = $record->contextid;
            $fieldtypes += [$record->type => []];
            $fieldtypes[$record->type][] = $record->contextid;
        }
        $records->close();

        // Delete description files.
        foreach ($contexts as $contextid) {
            get_file_storage()->delete_area_files_select($contextid, 'core_customfield', 'description',
                " IN ($fieldsql) ", $params);
        }

        // Call plugin callbacks to delete fields customfield_provider::before_delete_fields().
        foreach ($fieldtypes as $type => $contextids) {
            $classname = manager::get_provider_classname_for_component('customfield_' . $type);
            if (class_exists($classname) && is_subclass_of($classname, customfield_provider::class)) {
                component_class_callback($classname, 'before_delete_fields',
                    [" IN ($fieldsql) ", $params, $contextids]);
            }
        }
        $records->close();
    }

    /**
     * Exports one instance of custom field data
     *
     * @param data_controller $data
     * @param array $subcontext subcontext to pass to content_writer::export_data
     */
    public static function export_customfield_data(data_controller $data, array $subcontext) {
        $context = $data->get_context();

        $exportdata = $data->to_record();
        $exportdata->fieldtype = $data->get_field()->get('type');
        $exportdata->fieldshortname = $data->get_field()->get('shortname');
        $exportdata->fieldname = $data->get_field()->get_formatted_name();
        $exportdata->timecreated = \core_privacy\local\request\transform::datetime($exportdata->timecreated);
        $exportdata->timemodified = \core_privacy\local\request\transform::datetime($exportdata->timemodified);
        unset($exportdata->contextid);
        // Use the "export_value" by default for the 'value' attribute, however the plugins may override it in their callback.
        $exportdata->value = $data->export_value();

        $classname = manager::get_provider_classname_for_component('customfield_' . $data->get_field()->get('type'));
        if (class_exists($classname) && is_subclass_of($classname, customfield_provider::class)) {
            component_class_callback($classname, 'export_customfield_data', [$data, $exportdata, $subcontext]);
        } else {
            // Custom field plugin does not implement customfield_provider, just export default value.
            writer::with_context($context)->export_data($subcontext, $exportdata);
        }
    }

    /**
     * Export data record of unknown type when we were not able to create instance of data_controller
     *
     * @param \stdClass $record record from db table {customfield_data}
     * @param \stdClass $field field record with at least fields type, shortname, name
     * @param array $subcontext
     */
    protected static function export_customfield_data_unknown(\stdClass $record, \stdClass $field, array $subcontext) {
        $context = \context::instance_by_id($record->contextid);

        $record->fieldtype = $field->type;
        $record->fieldshortname = $field->shortname;
        $record->fieldname = format_string($field->name);
        $record->timecreated = \core_privacy\local\request\transform::datetime($record->timecreated);
        $record->timemodified = \core_privacy\local\request\transform::datetime($record->timemodified);
        unset($record->contextid);
        $record->value = format_text($record->value, $record->valueformat, [
            'context' => $context,
            'trusted' => $record->valuetrust,
        ]);
        writer::with_context($context)->export_data($subcontext, $record);
    }
}
