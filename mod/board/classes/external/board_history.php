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

/**
 * Provides the board history.
 *
 * @package    mod_board
 * @copyright  2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class board_history extends external_api {
    /**
     * Describes the external function arguments.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT, 'The board id', VALUE_REQUIRED),
            'ownerid' => new external_value(PARAM_INT, 'The board ownerid', VALUE_REQUIRED),
            'groupid' => new external_value(PARAM_INT, 'The board ownerid', VALUE_REQUIRED),
            'since' => new external_value(PARAM_INT, 'The last historyid', VALUE_REQUIRED),
        ]);
    }

    /**
     * Execute function.
     *
     * @param int $id
     * @param int $ownerid
     * @param int $groupid
     * @param int|null $since
     * @return array
     */
    public static function execute(int $id, int $ownerid, int $groupid, ?int $since): array {
        global $DB;

        // Validate received parameters.
        [
            'id' => $id,
            'ownerid' => $ownerid,
            'groupid' => $groupid,
            'since' => $since,
        ] = self::validate_parameters(self::execute_parameters(), [
            'id' => $id,
            'ownerid' => $ownerid,
            'groupid' => $groupid,
            'since' => $since,
        ]);

        $board = board::get_board($id, MUST_EXIST);
        $context = board::context_for_board($board);

        // Request and permission validation.
        self::validate_context($context);
        require_capability('mod/board:view', $context);

        if ($board->singleusermode != board::SINGLEUSER_DISABLED) {
            if (!$ownerid) {
                return [];
            }
            if (!board::can_view_owner($board, $ownerid)) {
                return [];
            }
        }

        if ($board->singleusermode != board::SINGLEUSER_DISABLED) {
            // Groups are not used in single-user-mode apart from user selection.
            $groupid = 0;
        } else {
            $cm = board::coursemodule_for_board($board);
            $groupmode = groups_get_activity_groupmode($cm);
            if ($groupmode == NOGROUPS) {
                $groupid = 0;
            } else if ($groupmode == SEPARATEGROUPS) {
                if ($groupid) {
                    board::require_access_for_group($board, $groupid);
                } else {
                    // Only managers can see in "All groups".
                    if (!has_capability('mod/board:manageboard', $context)) {
                        return [];
                    }
                }
            }
        }

        board::clear_history();

        $condition = "boardid = :boardid";
        $params = ['boardid' => $board->id];

        if ($since !== null) {
            $condition .= " AND id > :since";
            $params['since'] = $since;
        }
        if ($groupid) {
            // NOTE: this will not work for non-group posts.
            $condition .= " AND groupid=:groupid";
            $params['groupid'] = $groupid;
        }
        if ($board->singleusermode == board::SINGLEUSER_PUBLIC || $board->singleusermode == board::SINGLEUSER_PRIVATE) {
            $condition .= " AND (ownerid=:ownerid OR ownerid=0)"; // Value 0 is used for global actions.
            $params['ownerid'] = $ownerid;
        }

        return $DB->get_records_select('board_history', $condition, $params);
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
                    'id' => new external_value(PARAM_INT, 'id'),
                    'boardid' => new external_value(PARAM_INT, 'boardid'),
                    'action' => new external_value(PARAM_TEXT, 'action'),
                    'userid' => new external_value(PARAM_INT, 'userid'),
                    'content' => new external_value(PARAM_RAW, 'content'),
                ]
            )
        );
    }
}
