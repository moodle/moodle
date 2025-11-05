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

namespace mod_board\local;

use mod_board\board;
use stdClass;

/**
 * Comment helper class.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class comment {
    /**
     * Add a comment.
     *
     * @param int $noteid
     * @param string $content
     * @param int|null $userid
     * @return stdClass
     */
    public static function create(int $noteid, string $content, ?int $userid = null): stdClass {
        global $DB, $USER;

        $note = board::get_note($noteid, MUST_EXIST);
        $column = board::get_column($note->columnid, MUST_EXIST);
        $board = board::get_board($column->boardid, MUST_EXIST);
        $context = board::context_for_board($board);

        $content = clean_param(html_to_text($content, 5000, false), PARAM_TEXT);

        $comment = (object)[
            'noteid' => $note->id,
            'content' => $content,
            'userid' => $userid ?? $USER->id,
            'timecreated' => time(),
            'deleted' => 0,
        ];
        $id = $DB->insert_record('board_comments', $comment);

        $comment = $DB->get_record('board_comments', ['id' => $id], '*', MUST_EXIST);

        $event = \mod_board\event\add_comment::create_from_comment($comment, $note, $column, $board, $context);
        $event->trigger();

        return $comment;
    }

    /**
     * Delete a comment.
     *
     * @param int $commentid
     * @return void
     */
    public static function delete(int $commentid): void {

        global $DB;

        $comment = $DB->get_record('board_comments', ['id' => $commentid]);
        if (!$comment || $comment->deleted) {
            return;
        }

        $DB->set_field('board_comments', 'deleted', 1, ['id' => $comment->id]);

        $note = board::get_note($comment->noteid);
        if (!$note) {
            return;
        }
        $column = board::get_column($note->columnid, MUST_EXIST);
        $board = board::get_board($column->boardid, MUST_EXIST);
        $context = board::context_for_board($board);

        $event = \mod_board\event\delete_comment::create_from_comment($comment, $note, $column, $board, $context);
        $event->trigger();
    }
}
