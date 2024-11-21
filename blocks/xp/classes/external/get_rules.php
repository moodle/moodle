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
 * External function.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\external;

use block_xp\di;

/**
 * External function.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_rules extends external_api {

    /**
     * External function parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'contextid' => new external_value(PARAM_INT),
            'types' => new external_multiple_structure(new external_value(PARAM_ALPHANUMEXT), '', VALUE_DEFAULT, []),
            'childcontextid' => new external_value(PARAM_INT, '', VALUE_DEFAULT, null),
        ]);
    }

    /**
     * External function.
     *
     * @param int $contextid The context ID.
     * @param string[] $types The types of rule.
     * @param int $childcontextid The child context ID.
     * @return object[]
     */
    public static function execute($contextid, $types, $childcontextid = null) {
        $params = self::validate_parameters(self::execute_parameters(), compact('contextid', 'types', 'childcontextid'));
        $contextid = $params['contextid'];
        $types = $params['types'];
        $childcontextid = $params['childcontextid'] ?? 0;

        // Pre-checks.
        $worldfactory = di::get('context_world_factory');
        $world = $worldfactory->get_world_from_context(\context::instance_by_id($contextid));
        $context = $world->get_context(); // Ensure that we get the real context.
        self::validate_context($context);

        // Permission checks.
        $perms = $world->get_access_permissions();
        $perms->require_manage();

        // Validate the child context.
        $childcontext = null;
        if ($childcontextid) {
            $childcontext = \context::instance_by_id($childcontextid);
            if (!$context->is_parent_of($childcontext, false)) {
                throw new \moodle_exception('invalidcontext');
            }
        }

        if (empty($types)) {
            return [];
        }

        $dictator = di::get('rule_dictator');
        $rules = $dictator->get_rules_of_types_in_context($context, $types, $childcontext);
        $rules = $dictator->sort_rules_by_priority($rules);

        $filterhandler = di::get('rule_filter_handler');
        $data = array_values(array_map(function($instance) use ($filterhandler) {
            $filter = $filterhandler->get_filter($instance->get_filter_name());
            $effectivectx = $instance->get_child_context() ?? $instance->get_context();
            $label = $filter ? $filter->get_label_for_config($instance->get_filter_config(), $effectivectx) : null;
            return [
                'id' => $instance->get_id(),
                'points' => $instance->get_points(),
                'typename' => $instance->get_type_name(),
                'filtername' => $instance->get_filter_name(),
                'label' => $label ?? get_string('unknownconditiona', 'block_xp', $instance->get_filter_name()),
            ];
        }, $rules));

        return $data;
    }

    /**
     * External function return values.
     *
     * @return external_value
     */
    public static function execute_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT),
                'points' => new external_value(PARAM_INT),
                'typename' => new external_value(PARAM_ALPHANUMEXT),
                'filtername' => new external_value(PARAM_ALPHANUMEXT),
                'label' => new external_value(PARAM_RAW),
            ])
        );
    }

}
