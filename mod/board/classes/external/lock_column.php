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

namespace mod_board\external;

use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_api;
use core_external\external_single_structure;
use mod_board\board;
use mod_board\local\column;

/**
 * Lock board column.
 *
 * @package    mod_board
 * @copyright  2022 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class lock_column extends external_api {
    /**
     * Describes the external function arguments.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT, 'The column id', VALUE_REQUIRED),
            'status' => new external_value(PARAM_BOOL, 'True to lock the column', VALUE_REQUIRED),
        ]);
    }

    /**
     * Execute function.
     *
     * @param int $id
     * @param bool $status
     * @return array
     */
    public static function execute(int $id, bool $status): array {
        // Validate received parameters.
        [
            'id' => $id,
            'status' => $status,
        ] = self::validate_parameters(self::execute_parameters(), [
            'id' => $id,
            'status' => $status,
        ]);

        $column = board::get_column($id);
        if (!$column) {
            return [
                'status' => false,
                'historyid' => 0,
            ];
        }

        // Request and permission validation.
        $context = board::context_for_column($column);
        self::validate_context($context);
        require_capability('mod/board:view', $context);
        require_capability('mod/board:manageboard', $context);

        $historyid = column::lock($column->id, $status);

        return ['status' => true, 'historyid' => $historyid];
    }

    /**
     * Describes the external function result.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'status' => new external_value(PARAM_BOOL, 'The lock status'),
            'historyid' => new external_value(PARAM_INT, 'The last history id'),
        ]);
    }
}
