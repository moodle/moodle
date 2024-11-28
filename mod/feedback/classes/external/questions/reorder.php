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

namespace mod_feedback\external\questions;

use core_external\external_api;
use core_external\external_value;
use core_external\external_function_parameters;
use context_module;

/**
 * External method for reordering feedback questions.
 *
 * @package     mod_feedback
 * @copyright   2024 Mikel Martín <mikel@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reorder extends external_api {

    /**
     * Describes the parameters for reorder.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'cmid' => new external_value(PARAM_INT, 'Feedback course module id'),
                'itemorder' => new external_value(PARAM_SEQUENCE, 'Feedback order by sequence of question item ids'),
            ]
        );
    }

    /**
     * External function to reorder feedback questions.
     *
     * @param int $cmid
     * @param string $itemorder
     * @return bool
     */
    public static function execute(int $cmid, string $itemorder): bool {
        global $DB;
        [
            'cmid' => $cmid,
            'itemorder' => $itemorder,
        ] = self::validate_parameters(self::execute_parameters(), [
            'cmid' => $cmid,
            'itemorder' => $itemorder,
        ]);

        $cm = get_coursemodule_from_id('feedback', $cmid, 0, false, MUST_EXIST);
        $feedback = $DB->get_record('feedback', ['id' => $cm->instance], '*', MUST_EXIST);
        $context = context_module::instance($cm->id);

        self::validate_context($context);
        require_capability('mod/feedback:edititems', $context);

        $itemlist = explode(',', trim($itemorder, ',')) ?: [];
        if (count($itemlist) > 0) {
            return feedback_ajax_saveitemorder($itemlist, $feedback);
        }

        return false;
    }

    /**
     * Describes the data returned from the external function.
     *
     * @return external_value
     */
    public static function execute_returns(): external_value {
        return new external_value(PARAM_BOOL, '', VALUE_REQUIRED);
    }
}
