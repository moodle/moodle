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
 * Delete note comment.
 *
 * @package    mod_board
 * @copyright  2022 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class delete_comment extends external_api {
    /**
     * Describes the external function arguments.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'commentid' => new external_value(PARAM_INT, 'The comment id'),
        ]);
    }

    /**
     * Execute function.
     *
     * @param int $commentid The id of the comment.
     * @return array of results
     */
    public static function execute(int $commentid): array {
        global $DB, $USER;

        [
            'commentid' => $commentid,
        ] = self::validate_parameters(self::execute_parameters(), [
            'commentid' => $commentid,
        ]);

        $comment = $DB->get_record('board_comments', ['id' => $commentid], '*', MUST_EXIST);
        $note = board::get_note($comment->noteid, MUST_EXIST);

        $context = board::can_view_note($note);
        if (!$context) {
            return [
                'id' => $comment->id,
                'warnings' => [
                    [
                        'item' => $comment->id,
                        'warningcode' => 'errorcommentnotdeleted',
                        'message' => 'The comment could not be deleted.',
                    ],
                ],
            ];
        }
        self::validate_context($context);
        require_capability('mod/board:view', $context);

        $candelete = false;
        if ($comment->userid == $USER->id && has_capability('mod/board:postcomment', $context)) {
            $candelete = true;
        } else if (has_capability('mod/board:deleteallcomments', $context)) {
            $candelete = true;
        }
        if (!$candelete) {
            return [
                'id' => $comment->id,
                'warnings' => [
                    [
                        'item' => $comment->id,
                        'warningcode' => 'errorcommentnotdeleted',
                        'message' => 'The comment could not be deleted.',
                    ],
                ],
            ];
        }

        comment::delete($comment->id);

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
