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
use mod_board\local\note;

/**
 * Returns bord configuration.
 *
 * @package    mod_board
 * @copyright  2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class get_configuration extends external_api {
    /**
     * Describes the external function arguments.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT, 'The board id', VALUE_REQUIRED),
            'ownerid' => new external_value(PARAM_INT, 'The ownerid in separate user mode', VALUE_REQUIRED),
            'groupid' => new external_value(PARAM_INT, 'The groupid in grpup mode', VALUE_REQUIRED),
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
        global $USER, $DB;

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
        $context = board::context_for_board($board);

        // Request and permission validation.
        self::validate_context($context);
        require_capability('mod/board:view', $context);

        $config = get_config('mod_board');

        $forcereadonly = false;

        if ($board->singleusermode == board::SINGLEUSER_DISABLED) {
            if ($ownerid) {
                debugging('ownerid must be used only in single-user modes', DEBUG_DEVELOPER);
                $ownerid = 0;
            }
            $cm = board::coursemodule_for_board($board);
            $groupmode = groups_get_activity_groupmode($cm);
            if ($groupmode == SEPARATEGROUPS) {
                if (!$groupid) {
                    // No posting for All groups in separate groups mode for now,
                    // students would see comments and ratings from other groups.
                    $forcereadonly = true;
                }
            } else if ($groupmode == VISIBLEGROUPS) {
                if (!$groupid && !has_capability('mod/board:manageboard', $context)) {
                    // Only managers can post for All groups.
                    $forcereadonly = true;
                }
            } else {
                $groupid = 0;
            }
        } else {
            if (!$ownerid) {
                debugging('ownerid is required in single-user modes', DEBUG_DEVELOPER);
                $ownerid = $USER->id;
            }
            // Groups are not used in single-user-mode apart from user selection.
            $groupid = 0;
        }

        $settings = [
            'board' => $board,
            'contextid' => $context->id,
            'isEditor' => board::board_is_editor($board),
            'usersCanEdit' => (string)(int)board::board_users_can_edit($board),
            'userId' => $USER->id,
            'ownerId' => $ownerid,
            'groupId' => $groupid,
            'readonly' => ($forcereadonly || board::board_readonly($board, $groupid) || !board::can_post($board, $ownerid)),
            'columnicon' => $config->new_column_icon,
            'noteicon' => $config->new_note_icon,
            'post_max_length' => $config->post_max_length,
            'history_refresh' => $config->history_refresh,
            'ratingenabled' => board::board_rating_enabled($board),
            'hideheaders' => board::board_hide_headers($board),
            'sortby' => $board->sortby,
            'colours' => board::get_column_colours(),
            'enableblanktarget' => $board->enableblanktarget,
        ];

        return [
            'settings' => json_encode($settings),
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
                'settings' => new external_value(PARAM_RAW, 'The board settings'),
                'warnings' => new external_warnings(),
            ]
        );
    }
}
