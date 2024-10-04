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

namespace qbank_viewquestiontext\external;

use context_system;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use qbank_viewquestiontext\output\question_text_format;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/editlib.php');

/**
 * External function for setting the question text format.
 *
 * @package   qbank_viewquestiontext
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class set_question_text_format extends external_api {
    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'format' => new external_value(PARAM_INT, 'Format for the question text', VALUE_REQUIRED),
        ]);
    }

    /**
     * Returns description of method result value.
     */
    public static function execute_returns(): void {
    }

    /**
     * Save the question text format preference for the current user.
     *
     * @param int $format Format for the question text.
     */
    public static function execute(int $format): void {
        [
            'format' => $format,
        ] = self::validate_parameters(
            self::execute_parameters(),
            [
                'format' => $format,
            ]
        );

        if (!in_array($format, [question_text_format::OFF, question_text_format::PLAIN, question_text_format::FULL])) {
            throw new \invalid_parameter_exception('$format must be one of question_text_format::OFF, ::PLAIN or ::FULL.');
        }

        $context = context_system::instance();
        self::validate_context($context);

        \question_set_or_get_user_preference('qbshowtext', $format, 0, new \moodle_url('/'));
    }
}
