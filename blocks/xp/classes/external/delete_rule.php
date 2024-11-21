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
class delete_rule extends external_api {

    /**
     * External function parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT),
        ]);
    }

    /**
     * External function.
     *
     * @param int $id The rule ID.
     * @return bool
     */
    public static function execute($id) {
        $params = self::validate_parameters(self::execute_parameters(), compact('id'));
        $id = $params['id'];

        // Retrieve the rule.
        $db = di::get('db');
        $rule = $db->get_record('block_xp_rule', ['id' => $id], '*', MUST_EXIST);

        // Resolve the world.
        $worldfactory = di::get('context_world_factory');
        $world = $worldfactory->get_world_from_context(\context::instance_by_id($rule->contextid));
        $context = $world->get_context(); // Ensure that we get the real context.
        self::validate_context($context);

        // Permission checks.
        $perms = $world->get_access_permissions();
        $perms->require_manage();

        // In the future, we will keep the rule if it has logs.
        $db->delete_records('block_xp_rule', ['id' => $id]);

        return true;
    }

    /**
     * External function return values.
     *
     * @return external_value
     */
    public static function execute_returns() {
        return new external_value(PARAM_BOOL);
    }

}
