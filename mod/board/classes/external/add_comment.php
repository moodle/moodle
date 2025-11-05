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
use core_external\external_warnings;
use mod_board\board;
use mod_board\local\comment;

/**
 * Add note comment.
 *
 * @package    mod_board
 * @copyright  2022 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class add_comment extends external_api {
    /**
     * Describes the external function arguments.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'noteid' => new external_value(PARAM_INT, 'id of note'),
                'content' => new external_value(PARAM_RAW, 'content of comment'),
            ]
        );
    }

    /**
     * Execute function.
     *
     * @param int $noteid The id of the note.
     * @param string $content The content of the comment.
     * @return array of results
     */
    public static function execute(int $noteid, string $content): array {
        [
            'noteid' => $noteid,
            'content' => $content,
        ] = self::validate_parameters(self::execute_parameters(), [
            'noteid' => $noteid,
            'content' => $content,
        ]);

        $note = board::get_note($noteid);
        if (!$note) {
            return [
                'count' => '',
                'id' => 0,
                'warnings' => [],
            ];
        }

        $context = board::can_view_note($note);
        if (!$context) {
            return [
                'count' => '',
                'id' => 0,
                'warnings' => [],
            ];
        }
        self::validate_context($context);
        require_capability('mod/board:view', $context);

        if (!has_capability('mod/board:postcomment', $context)) {
            return [
                'count' => '',
                'id' => 0,
                'warnings' => [],
            ];
        }

        $comment = comment::create($note->id, $content);

        return [
            'id' => $comment->id,
            'warnings' => [],
        ];
    }

    /**
     * Describes the external function result.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure(
            [
                'id' => new external_value(PARAM_INT, 'The comment id.'),
                'warnings' => new external_warnings(),
            ]
        );
    }
}
