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
use mod_board\board;
use mod_board\local\note;

/**
 * Returns bord data.
 *
 * @package    mod_board
 * @copyright  2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class get_board extends external_api {
    /**
     * Describes the external function arguments.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT, 'The board id', VALUE_REQUIRED),
            'ownerid' => new external_value(PARAM_INT, 'The ownerid - 0 in normal mode', VALUE_REQUIRED),
            'groupid' => new external_value(PARAM_INT, 'The group id - 0 in single user mode', VALUE_REQUIRED),
        ]);
    }

    /**
     * Execute function.
     *
     * @param int $id
     * @param int $ownerid
     * @param int $groupid
     * @return array
     */
    public static function execute(int $id, int $ownerid, int $groupid): array {
        global $DB;

        // Validate received parameters.
        [
            'id' => $id,
            'ownerid' => $ownerid,
            'groupid' => $groupid,
        ] = self::validate_parameters(self::execute_parameters(), [
            'id' => $id,
            'ownerid' => $ownerid,
            'groupid' => $groupid,
        ]);

        $board = board::get_board($id, MUST_EXIST);
        $cm = board::coursemodule_for_board($board);
        $context = board::context_for_board($board);

        // Request and permission validation.
        self::validate_context($context);
        require_capability('mod/board:view', $context);

        if ($board->singleusermode == board::SINGLEUSER_DISABLED) {
            if ($ownerid) {
                debugging('ownerid must be used only in single-user modes', DEBUG_DEVELOPER);
            }
            $ownerid = null;

            $groupmode = groups_get_activity_groupmode($cm);
            if ($groupmode == NOGROUPS) {
                if ($groupid) {
                    debugging('groupid is not expected when group mode not used', DEBUG_DEVELOPER);
                    $groupid = 0;
                }
            } else if ($groupmode == SEPARATEGROUPS) {
                if ($groupid) {
                    board::require_access_for_group($board, $groupid);
                } else {
                    if (
                        !has_capability('moodle/site:accessallgroups', $context)
                        && !has_capability('mod/board:manageboard', $context)
                    ) {
                        return [];
                    }
                }
                // NOTE: in visible groups mode everybody can see everything, only posting is restricted to own group.
            }
        } else {
            if (!$ownerid) {
                debugging('ownerid is required in single-user modes', DEBUG_DEVELOPER);
                return [];
            }
            if (!board::can_view_owner($board, $ownerid)) {
                return [];
            }
            if ($groupid) {
                debugging('groupid is not expected in single-user modes', DEBUG_DEVELOPER);
                $groupid = 0;
            }
        }

        $hideheaders = board::board_hide_headers($board);

        $columns = $DB->get_records('board_columns', ['boardid' => $board->id], 'sortorder, id', '*');
        $columnindex = 0;

        foreach ($columns as $column) {
            if ($column->locked === null) {
                $column->locked = false;
            }
            if ($hideheaders) {
                $column->name = ++$columnindex;
            } else {
                $column->name = note::format_plain_text($column->name);
            }
            $params = ['columnid' => $column->id, 'deleted' => 0];
            if (!empty($groupid)) {
                $params['groupid'] = $groupid;
            }

            if ($ownerid) {
                $params['ownerid'] = $ownerid;
            }

            $notes = $DB->get_records('board_notes', $params);
            $column->notes = [];
            foreach ($notes as $note) {
                $note = note::format_for_display($note, $column, $board, $context);
                unset($note->deleted);
                unset($note->ownerid);
                unset($note->filename);
                $column->notes[$note->id] = $note;
            }
            unset($column->boardid);
        }

        board::clear_history();

        return $columns;
    }

    /**
     * Describes the external function result.
     *
     * @return external_multiple_structure
     */
    public static function execute_returns(): external_multiple_structure {
        return new external_multiple_structure(
            new external_single_structure(
                [
                    'id' => new external_value(PARAM_INT, 'column id'),
                    'name' => new external_value(PARAM_TEXT, 'column name'),
                    'locked' => new external_value(PARAM_BOOL, 'column locked'),
                    'notes' => new external_multiple_structure(
                        new external_single_structure(
                            [
                                'id' => new external_value(PARAM_INT, 'post id'),
                                'userid' => new external_value(PARAM_INT, 'user id'),
                                'identifier' => new external_value(PARAM_RAW, 'name used when referencing a note'),
                                'heading' => new external_value(PARAM_TEXT, 'post heading'),
                                'content' => new external_value(PARAM_RAW, 'post content'),
                                'type' => new external_value(PARAM_INT, 'type'),
                                'info' => new external_value(PARAM_TEXT, 'info'),
                                'url' => new external_value(PARAM_TEXT, 'url'),
                                'timecreated' => new external_value(PARAM_INT, 'timecreated'),
                                'rating' => new external_value(PARAM_INT, 'rating'),
                                'sortorder' => new external_value(PARAM_INT, 'note sort order'),
                            ]
                        )
                    ),
                ]
            )
        );
    }
}
