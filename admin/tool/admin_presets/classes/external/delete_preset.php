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

namespace tool_admin_presets\external;

use core_adminpresets\manager;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;

/**
 * External function tool_admin_presets_delete_preset
 *
 * @package    tool_admin_presets
 * @copyright  2024 David Carrillo <davidmc@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_preset extends external_api {

    /**
     * Describes the parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT),
        ]);
    }

    /**
     * External function to delete custom presets.
     *
     * @param int $id
     */
    public static function execute(int $id): void {
        // Parameter validation.
        [
            'id' => $id,
        ] = self::validate_parameters(self::execute_parameters(), [
            'id' => $id,
        ]);

        // Validate context.
        $context = \context_system::instance();
        self::validate_context($context);

        require_capability('moodle/site:config', $context);

        (new manager())->delete_preset($id);
    }

    /**
     * Describes the data returned from the external function.
     */
    public static function execute_returns(): void {
    }
}
