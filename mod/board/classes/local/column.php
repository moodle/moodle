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
 * Column helper class.
 *
 * @package    mod_board
 * @copyright  2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class column {
    /**
     * Adds a column to the board
     *
     * @param int $boardid
     * @param string $name
     * @return stdClass column record with extra historyid
     */
    public static function create(int $boardid, string $name): stdClass {
        global $DB, $USER;

        $board = board::get_board($boardid, MUST_EXIST);
        $context = board::context_for_board($board);
        $name = \core_text::substr($name, 0, board::LENGTH_COLNAME);

        $transaction = $DB->start_delegated_transaction();

        $maxsortorder = $DB->get_field('board_columns', 'MAX(sortorder)', ['boardid' => $board->id]);

        $columnid = $DB->insert_record('board_columns', [
            'boardid' => $board->id,
            'name' => $name,
            'sortorder' => $maxsortorder + 1,
        ]);
        $column = board::get_column($columnid, MUST_EXIST);

        $historyid = $DB->insert_record('board_history', [
            'boardid' => $board->id,
            'action' => 'add_column',
            'ownerid' => 0,
            'userid' => $USER->id,
            'content' => json_encode(['id' => $column->id, 'name' => note::format_plain_text($name)]),
            'timecreated' => time(),
        ]);
        $DB->set_field('board', 'historyid', $historyid, ['id' => $board->id]);
        $board->historyid = (string)$historyid;

        $transaction->allow_commit();

        $event = \mod_board\event\add_column::create_from_column($column, $board, $context);
        $event->trigger();

        board::clear_history();

        $column->historyid = $historyid;
        return $column;
    }

    /**
     * Updates the column.
     *
     * @param int $id
     * @param string $name
     * @return stdClass column record with extra historyid
     */
    public static function update(int $id, string $name): stdClass {
        global $DB, $USER;

        $column = board::get_column($id, MUST_EXIST);
        $board = board::get_board($column->boardid, MUST_EXIST);
        $context = board::context_for_board($board);

        $name = \core_text::substr($name, 0, board::LENGTH_COLNAME);

        $transaction = $DB->start_delegated_transaction();

        $DB->set_field('board_columns', 'name', $name, ['id' => $column->id]);
        $column->name = $name;

        $historyid = $DB->insert_record('board_history', [
            'boardid' => $column->boardid,
            'action' => 'update_column',
            'ownerid' => 0,
            'userid' => $USER->id,
            'content' => json_encode(['id' => $id, 'name' => note::format_plain_text($name)]),
            'timecreated' => time(),
        ]);
        $DB->set_field('board', 'historyid', $historyid, ['id' => $board->id]);
        $board->historyid = (string)$historyid;

        $transaction->allow_commit();

        $event = \mod_board\event\update_column::create_from_column($column, $board, $context);
        $event->trigger();

        board::clear_history();

        $column->historyid = $historyid;
        return $column;
    }

    /**
     * Deletes a column.
     *
     * @param int $id
     * @return int history id
     */
    public static function delete(int $id): int {
        global $DB, $USER;

        $column = board::get_column($id, MUST_EXIST);
        $board = board::get_board($column->boardid, MUST_EXIST);
        $context = board::context_for_board($board);

        $transaction = $DB->start_delegated_transaction();

        // There is no point in leaving records referencing non-existent columnid in database,
        // so delete all column notes and related data.
        $rs = $DB->get_recordset('board_notes', ['columnid' => $id]);
        foreach ($rs as $note) {
            $DB->delete_records('board_note_ratings', ['noteid' => $note->id]);
            $DB->delete_records('board_comments', ['noteid' => $note->id]);
            note::delete_files($note, $context);
        }
        $rs->close();
        $DB->delete_records('board_notes', ['columnid' => $id]);

        $DB->delete_records('board_columns', ['id' => $id]);
        $historyid = $DB->insert_record('board_history', [
            'boardid' => $board->id,
            'action' => 'delete_column',
            'ownerid' => 0,
            'content' => json_encode(['id' => $id]),
            'userid' => $USER->id,
            'timecreated' => time(),
        ]);
        $DB->set_field('board', 'historyid', $historyid, ['id' => $board->id]);
        $board->historyid = (string)$historyid;

        $transaction->allow_commit();

        $event = \mod_board\event\delete_column::create_from_column($column, $board, $context);
        $event->trigger();

        board::clear_history();

        return $historyid;
    }

    /**
     * Locks or unlock a columns
     *
     * @param int $id
     * @param bool $locked True to lock the column, false to unlock it.
     * @return int history id
     */
    public static function lock(int $id, bool $locked): int {
        global $DB, $USER;

        $boardid = $DB->get_field('board_columns', 'boardid', ['id' => $id]);

        $DB->set_field('board_columns', 'locked', $locked, ['id' => $id]);
        $historyid = $DB->insert_record('board_history', ['boardid' => $boardid, 'action' => 'lock_column',
            'content' => json_encode(['id' => $id, 'locked' => $locked]),
            'userid' => $USER->id, 'timecreated' => time()]);

        return $historyid;
    }

    /**
     * Moves a column to a new position.
     *
     * @param int $id the column id
     * @param int $sortorder the new sortorder
     * @return int history id
     */
    public static function move(int $id, int $sortorder): int {
        global $DB, $USER;

        $column = board::get_column($id, MUST_EXIST);

        $columns = $DB->get_records('board_columns', ['boardid' => $column->boardid], 'sortorder ASC, id ASC');
        board::repositionan_array_element($columns, $id, $sortorder);
        $sortorder = 1;
        $neworder = [];
        foreach ($columns as $c) {
            $c->sortorder = $sortorder++;
            $neworder[] = $c->id;
            $DB->update_record('board_columns', $c);
        }

        $historyid = $DB->insert_record('board_history', [
            'boardid' => $column->boardid,
            'action' => 'move_column',
            'content' => json_encode(['sortorder' => $neworder]),
            'userid' => $USER->id, 'timecreated' => time(),
        ]);

        return $historyid;
    }
}
