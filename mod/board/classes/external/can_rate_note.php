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
 * Can user rate note?
 *
 * @package    mod_board
 * @copyright  2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class can_rate_note extends external_api {
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
     * @return bool
     */
    public static function execute(int $id): array {
        global $DB, $USER;

        // Validate received parameters.
        [
            'id' => $id,
        ] = self::validate_parameters(self::execute_parameters(), [
            'id' => $id,
        ]);

        $note = board::get_note($id);
        if (!$note) {
            return ['canrate' => false, 'hasrated' => false];
        }
        $context = board::context_for_column($note->columnid);

        // Request and permission validation.
        self::validate_context($context);
        require_capability('mod/board:view', $context);

        $canrate = note::can_rate($note->id);
        $hasrated = $DB->record_exists('board_note_ratings', ['userid' => $USER->id, 'noteid' => $id]);

        return ['canrate' => $canrate, 'hasrated' => $hasrated];
    }

    /**
     * Describes the external function result.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'canrate' => new external_value(PARAM_BOOL, 'The user can rate the note'),
            'hasrated' => new external_value(PARAM_BOOL, 'The user has rated the note'),
        ]);
    }
}
