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

namespace block_ai_chat\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * Class delete_conversation.
 *
 * @package    block_ai_chat
 * @copyright  2024 Tobias Garske, ISB Bayern
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_conversation extends external_api {

    /**
     * Describes the parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'contextid' => new external_value(PARAM_INT, 'Course contextid.', VALUE_REQUIRED),
            'userid' => new external_value(PARAM_INT, 'Userid.', VALUE_REQUIRED),
            'conversationid' => new external_value(PARAM_INT, 'Conversationid / Itemid.', VALUE_REQUIRED),
        ]);
    }

    /**
     * Execute the service.
     *
     * @param int $contextid
     * @param int $userid
     * @param int $conversationid
     * @return array
     * @throws invalid_parameter_exception
     * @throws dml_exception
     */
    public static function execute(int $contextid, int $userid, int $conversationid): array {
        global $USER;

        self::validate_parameters(self::execute_parameters(), [
            'contextid' => $contextid,
            'userid' => $userid,
            'conversationid' => $conversationid,
        ]);
        self::validate_context(\core\context_helper::instance_by_id($contextid));
        require_capability('local/ai_manager:use', \context::instance_by_id($contextid));

        // Check userid and USER-id ?
        // Delete conversation.
        $response = \local_ai_manager\ai_manager_utils::mark_log_entries_as_deleted(
            'block_ai_chat', $contextid, $USER->id, $conversationid
        );
        // Maybe response missing?

        return ['result' => true];
    }

    /**
     * Describes the return structure of the service..
     *
     * @return external_single_structure the return structure
     */
    public static function execute_returns() {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'Removed successfully.'),
        ]);
    }
}

