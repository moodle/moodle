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

declare(strict_types=1);

namespace mod_board\completion;

use core_completion\activity_custom_completion;

/**
 * Activity custom completion subclass for the board activity.
 *
 * Class for defining mod_board's custom completion rules and fetching the completion statuses
 * of the custom completion rules for a given board instance and a user.
 *
 * @package   mod_board
 * @copyright Brickfield Education Ltd.
 * @author    Bas Brands
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_completion extends activity_custom_completion {
    /**
     * Fetches the completion state for a given completion rule.
     *
     * @param string $rule The completion rule.
     * @return int The completion state.
     */
    public function get_state(string $rule): int {
        global $DB;

        $this->validate_rule($rule);

        $userid = $this->userid;
        $boardid = $this->cm->instance;

        if (!$board = $DB->get_record('board', ['id' => $boardid])) {
            throw new \moodle_exception('Unable to find board with id ' . $boardid);
        }

        $notescountparams = ['userid' => $userid, 'boardid' => $boardid];
        $notescountsql = "SELECT COUNT(*)
                           FROM {board_notes} bn
                           JOIN {board_columns} bc ON bn.columnid = bc.id
                          WHERE bn.userid = :userid
                            AND bc.boardid = :boardid";

        if ($rule == 'completionnotes') {
            $status = $board->completionnotes <= $DB->get_field_sql($notescountsql, $notescountparams);
        }

        return $status ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE;
    }

    /**
     * Fetch the list of custom completion rules that this module defines.
     *
     * @return array
     */
    public static function get_defined_custom_rules(): array {
        return [
            'completionnotes',
        ];
    }

    /**
     * Returns an associative array of the descriptions of custom completion rules.
     *
     * @return array
     */
    public function get_custom_rule_descriptions(): array {
        $completionnotes = $this->cm->customdata['customcompletionrules']['completionnotes'] ?? 0;

        return [
            'completionnotes' => get_string('completiondetail:notes', 'mod_board', $completionnotes),
        ];
    }

    /**
     * Returns an array of all completion rules, in the order they should be displayed to users.
     *
     * @return array
     */
    public function get_sort_order(): array {
        return [
            'completionview',
            'completionnotes',
        ];
    }
}
