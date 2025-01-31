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
 * Functions for managing and manipulating question filter conditions
 *
 * @package   core_question
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\local\bank;

use core\output\datafilter;
use qbank_deletequestion\hidden_condition;
use qbank_managecategories\category_condition;

/**
 * Static methods for parsing and formatting data related to filter conditions.
 */
class filter_condition_manager {

    /**
     * Extract parameters from args list.
     *
     * @param array $args
     * @return array the param and extra param
     */
    public static function extract_parameters_from_fragment_args(array $args): array {
        $params = [];
        if (array_key_exists('filter', $args)) {
            $params['filter'] = json_decode($args['filter'], true);
        }
        if (array_key_exists('cmid', $args)) {
            $params['cmid'] = $args['cmid'];
        }
        if (array_key_exists('courseid', $args)) {
            $params['courseid'] = $args['courseid'];
        }
        $params['jointype'] = $args['jointype'] ?? condition::JOINTYPE_DEFAULT;
        $params['qpage'] = $args['qpage'] ?? 0;
        $params['qperpage'] = $args['qperpage'] ?? 100;
        $params['sortdata'] = json_decode($args['sortdata'] ?? '', true);
        $extraparams = json_decode($args['extraparams'] ?? '', true);

        return [$params, $extraparams];
    }

    /**
     * List of condition classes
     *
     * @return condition[] condition classes: [condition_key] = class
     */
    public static function get_condition_classes(): array {
        $classes = [];
        $plugins = \core_component::get_plugin_list_with_class('qbank', 'plugin_feature', 'plugin_feature.php');
        foreach ($plugins as $componentname => $plugin) {
            if (\core\plugininfo\qbank::is_plugin_enabled($componentname)) {
                $pluginentrypointobject = new $plugin();
                $conditions = $pluginentrypointobject->get_question_filters();
                foreach ($conditions as $condition) {
                    $classes[$condition->get_condition_key()] = $condition->get_condition_class();
                }
            }
        }
        return $classes;
    }

    /**
     * Given a JSON-encoded "filter" URL param, create or replace the category filter with the provided category.
     *
     * @param string $filterparam The json-encoded filter param from the URL, containing the list of filters.
     * @param int $newcategoryid The new ID to set for the "category" filter condition's value.
     * @return string JSON-encoded filter param with the new category.
     */
    public static function update_filter_param_to_category(string $filterparam, int $newcategoryid): string {
        $returnfilters = json_decode($filterparam, true);
        if (!$returnfilters) {
            $returnfilters = [
                'category' => [
                    'name' => 'category',
                    'jointype' => \core_question\local\bank\condition::JOINTYPE_DEFAULT,
                ]
            ];
        }
        $returnfilters['category']['values'] = [$newcategoryid];
        return json_encode($returnfilters);
    }

    /**
     * Unpack filteroptions passed in a request's filter param if required.
     *
     * Filteroptions are passed via AJAX as an array of {name:, value:} pairs for compatibility with external functions.
     *
     * @param array $filters List of filters, each optionally including an array of filteroptions.
     * @return array The input array, with filteroptions unpacked from [{name:, value:}, ...] to [name => value, ...].
     */
    public static function unpack_filteroptions_param(array $filters): array {
        foreach ($filters as $name => $filter) {
            if (!empty($filter['filteroptions']) && isset(reset($filter['filteroptions'])['name'])) {
                $unpacked = [];
                foreach ($filter['filteroptions'] as $filteroption) {
                    $unpacked[$filteroption['name']] = $filteroption['value'];
                }
                $filters[$name]['filteroptions'] = $unpacked;
            }
        }
        return $filters;
    }

    /**
     * Provide a category-context string to get a default filter array for the category.
     *
     * @param string $catstring in format '1,2' or 'categoryid,contextid'
     * @return array
     */
    public static function get_default_filter(string $catstring): array {
        $filter  = [];
        [$validcatid, $contextid] = category_condition::validate_category_param($catstring);
        if (!is_null($validcatid)) {
            $category = category_condition::get_category_record($validcatid, $contextid);
            $filter['category'] = [
                'jointype' => condition::JOINTYPE_DEFAULT,
                'values' => [$category->id],
                'filteroptions' => ['includesubcategories' =>
                    get_user_preferences('qbank_managecategories_includesubcategories_filter_default', false)],
            ];
        }
        $filter['hidden'] = [
            'jointype' => condition::JOINTYPE_DEFAULT,
            'values' => [0],
        ];

        return $filter;
    }

    /**
     * Filter out invalid values from the filterconditions array,
     *
     * @param array $filterconditions
     * @return array
     * @throws \dml_exception
     */
    public static function filter_invalid_values(array $filterconditions): array {

        $classes = self::get_condition_classes();
        foreach ($classes as $class) {
            $condition = new $class();
            $filterconditions = $condition->filter_invalid_values($filterconditions);
        }

        return $filterconditions;

    }

}
