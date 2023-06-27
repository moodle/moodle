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

/**
 * This is the external API for this component.
 *
 * @package    block_timestat
 * @copyright  2022 Jorge C. {}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_timestat;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/blocks/timestat/locallib.php');

use context;
use core_course\external\course_summary_exporter;
use dml_exception;
use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use invalid_parameter_exception;
use moodle_exception;

/**
 * This is the external API for this component.
 *
 * @copyright  2020 Mathew May {@link https://mathew.solutions}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {

    /**
     * update_register_parameters
     *
     * @return external_function_parameters
     */
    public static function update_register_parameters(): external_function_parameters {
        return new external_function_parameters(
                array(
                        'timespent' => new external_value(PARAM_INT),
                        'contextid' => new external_value(PARAM_INT),
                )
        );
    }

    /**
     *
     * Update the register to save the timespent in a specific log.
     *
     * @param int $timespent The user time spent
     * @param int $contextid The log id
     * @return array
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public static function update_register(int $timespent, int $contextid): array {
        global $DB, $USER;

        $context = context::instance_by_id($contextid);
        self::validate_context($context);
        
        $params = self::validate_parameters(
                self::update_register_parameters(),
                ['timespent' => $timespent, 'contextid' => $contextid]
        );
        $log = block_timestat_get_user_last_log_by_contextid($contextid);
        if ($log->userid !== $USER->id) {
            throw new moodle_exception('You are not allowed to update this log');
        }
        $recordtimestat = $DB->get_record('block_timestat', array('log_id' => $log->id));

        if (!$recordtimestat) {
            $recordbt = new \stdClass();
            $recordbt->log_id = $log->id;
            $recordbt->timespent = $params['timespent'];
            $DB->insert_record('block_timestat', $recordbt);
            return [];
        }
        $recordtimestat->timespent = $params['timespent'];
        $DB->update_record('block_timestat', $recordtimestat);
        return [];
    }

    /**
     * update_register_returns.
     *
     * @return \external_description
     */
    public static function update_register_returns() {
        return new external_single_structure([]);
    }
}
