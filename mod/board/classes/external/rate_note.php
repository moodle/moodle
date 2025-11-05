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
use mod_board\local\note;

/**
 * Rate bord note.
 *
 * @package    mod_board
 * @copyright  2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class rate_note extends external_api {
    /**
     * Describes the external function arguments.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT, 'The note id', VALUE_REQUIRED),
        ]);
    }

    /**
     * Execute function.
     *
     * @param int $id
     * @return array
     */
    public static function execute(int $id): array {
        global $DB;
        // Validate received parameters.
        [
            'id' => $id,
        ] = self::validate_parameters(self::execute_parameters(), [
            'id' => $id,
        ]);

        $note = board::get_note($id);
        if (!$note) {
            return ['status' => false, 'rating' => 0, 'historyid' => 0];
        }
        $column = board::get_column($note->columnid, MUST_EXIST);
        $board = board::get_board($column->boardid, MUST_EXIST);
        $context = board::context_for_board($board->id);

        // Request and permission validation.
        self::validate_context($context);
        require_capability('mod/board:view', $context);

        if (!note::can_rate($note->id)) {
            return ['status' => false, 'rating' => 0, 'historyid' => 0];
        }

        $historyid = note::rate($id);
        $rating = note::get_rating($note->id);

        return ['status' => true, 'rating' => $rating, 'historyid' => $historyid];
    }

    /**
     * Describes the external function result.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'status' => new external_value(PARAM_BOOL, 'The rate status'),
            'rating' => new external_value(PARAM_INT, 'The new rating id'),
            'historyid' => new external_value(PARAM_INT, 'The last history id'),
        ]);
    }
}
