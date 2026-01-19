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

namespace theme_moove\api;

use core_external\external_api;
use core_external\external_value;
use core_external\external_single_structure;
use core_external\external_function_parameters;

/**
 * MyLearning external api class.
 *
 * @package    theme_moove
 * @copyright  2020 Willian Mano - http://conecti.me
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mylearning extends external_api {
    /**
     * Get my learning parameters
     *
     * @return external_function_parameters
     */
    public static function get_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * Get my learning method
     *
     * @return array
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     */
    public static function get() {
        global $PAGE;

        $context = \core\context\system::instance();
        $PAGE->set_context($context);

        $mylearning = new \theme_moove\util\mylearning();

        $courses = $mylearning->get_last_accessed_courses(3);

        return [
            'courses' => json_encode($courses),
        ];
    }

    /**
     * Get my learning return fields
     *
     * @return external_single_structure
     */
    public static function get_returns() {
        return new external_single_structure([
            'courses' => new external_value(PARAM_TEXT, 'Return courses'),
        ]);
    }
}
