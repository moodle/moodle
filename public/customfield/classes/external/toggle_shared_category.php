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

declare(strict_types=1);

namespace core_customfield\external;

use core_customfield\event\shared_category_usage_disabled;
use core_customfield\event\shared_category_usage_enabled;
use core_customfield\handler;
use core_customfield\shared;
use core_external\external_api;
use core_external\external_value;
use core_external\external_function_parameters;

/**
 * External method for toggling shared categories
 *
 * @package     core_customfield
 * @copyright   2025 David Carrillo <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class toggle_shared_category extends external_api {
    /**
     * External method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'categoryid' => new external_value(PARAM_INT, 'Category ID'),
            'component' => new external_value(PARAM_COMPONENT, 'Component'),
            'area' => new external_value(PARAM_AREA, 'Area'),
            'itemid' => new external_value(PARAM_INT, 'Item ID'),
            'state' => new external_value(PARAM_BOOL, 'New state'),
        ]);
    }

    /**
     * External method execution
     *
     * @param int $categoryid
     * @param string $component
     * @param string $area
     * @param int $itemid
     * @param bool $state
     * @return bool
     */
    public static function execute(int $categoryid, string $component, string $area, int $itemid, bool $state): bool {
        [
            'categoryid' => $categoryid,
            'component' => $component,
            'area' => $area,
            'itemid' => $itemid,
            'state' => $state,
        ] = self::validate_parameters(self::execute_parameters(), [
            'categoryid' => $categoryid,
            'component' => $component,
            'area' => $area,
            'itemid' => $itemid,
            'state' => $state,
        ]);

        // Validate context.
        $context = \core\context\system::instance();
        self::validate_context($context);

        $handler = handler::get_handler($component, $area, $itemid);
        if (!$handler->can_configure()) {
            throw new \moodle_exception('nopermissions', 'error', '', get_string('customfield:configureshared', 'core_role'));
        }

        global $DB;

        $params = [
            'categoryid' => $categoryid,
            'component' => $component,
            'area' => $area,
            'itemid' => $itemid,
        ];

        if ($state) {
            $record = new shared(0, (object) $params);
            $record->create();
            shared_category_usage_enabled::create_from_object($record, $handler->get_configuration_context())->trigger();
        } else {
            $record = shared::get_record($params);
            $DB->delete_records('customfield_shared', $params);
            shared_category_usage_disabled::create_from_object($record, $handler->get_configuration_context())->trigger();
        }

        return true;
    }

    /**
     * External method return value
     *
     * @return external_value
     */
    public static function execute_returns(): external_value {
        return new external_value(PARAM_BOOL);
    }
}
