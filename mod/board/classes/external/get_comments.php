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
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_warnings;
use mod_board\board;

/**
 * Returns note comments.
 *
 * @package    mod_board
 * @copyright  2022 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class get_comments extends external_api {
    /**
     * Describes the external function arguments.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'noteid' => new external_value(PARAM_INT, 'id of note'),
            ]
        );
    }

    /**
     * Execute function.
     *
     * @param int $noteid The id of the note.
     * @return array of results
     */
    public static function execute(int $noteid): array {
        global $DB, $USER;

        [
            'noteid' => $noteid,
        ] = self::validate_parameters(self::execute_parameters(), [
            'noteid' => $noteid,
        ]);

        $note = board::get_note($noteid, MUST_EXIST);
        $context = board::can_view_note($note);
        if (!$context) {
            throw new \invalid_parameter_exception('cannot access note');
        }

        self::validate_context($context);

        $canpost = has_capability('mod/board:postcomment', $context);
        $candeleteall = has_capability('mod/board:deleteallcomments', $context);

        $notes = $DB->get_records('board_comments', ['noteid' => $note->id, 'deleted' => 0], 'timecreated DESC, id DESC');
        $comments = [];
        foreach ($notes as $note) {
            $comment = (object)[];
            $comment->id = $note->id;
            $comment->noteid = $note->noteid;
            $comment->content = clean_param($note->content, PARAM_TEXT);
            $comment->candelete = (($canpost && $note->userid === $USER->id) || $candeleteall) ? true : false;
            $comment->date = userdate($note->timecreated);
            $comments[] = $comment;
        }

        $results = [
            'noteid' => $noteid,
            'commentcount' => count($comments),
            'canpost' => $canpost,
            'comments' => $comments,
            'warnings' => [],
        ];
        return $results;
    }

    /**
     * Describes the external function result.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure(
            [
                'canpost' => new external_value(PARAM_BOOL, 'can post comments'),
                'noteid' => new external_value(PARAM_INT, 'The note id.'),
                'commentcount' => new external_value(PARAM_INT, 'The number of comments.'),
                'comments' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'id' => new external_value(PARAM_INT, 'The comment id.'),
                            'noteid' => new external_value(PARAM_INT, 'The note id.'),
                            'candelete' => new external_value(PARAM_BOOL, 'Can delete the comment.'),
                            'date' => new external_value(PARAM_TEXT, 'The date of the comment.'),
                            'content' => new external_value(PARAM_RAW, 'The content of the comment.'),
                        ]
                    )
                ),
                'warnings' => new external_warnings(),
            ]
        );
    }
}
