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

namespace mod_lti\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use mod_lti\local\types_helper;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/lti/locallib.php');

/**
 * External function to toggle showinactivitychooser setting.
 *
 * @package    mod_lti
 * @copyright  2023 Ilya Tregubov <ilya.a.tregubov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class toggle_showinactivitychooser extends external_api {

    /**
     * Get parameter definition.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'tooltypeid' => new external_value(PARAM_INT, 'Tool type ID'),
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
            'showinactivitychooser' => new external_value(PARAM_BOOL, 'Show in activity chooser'),
        ]);
    }

    /**
     * Toggles showinactivitychooser setting.
     *
     * @param int $tooltypeid the id of the course external tool type.
     * @param int $courseid the id of the course we are in.
     * @param bool $showinactivitychooser Show in activity chooser setting.
     * @return bool true or false
     */
    public static function execute(int $tooltypeid, int $courseid, bool $showinactivitychooser): bool {
        [
            'tooltypeid' => $tooltypeid,
            'courseid' => $courseid,
            'showinactivitychooser' => $showinactivitychooser,
        ] = self::validate_parameters(self::execute_parameters(), [
            'tooltypeid' => $tooltypeid,
            'courseid' => $courseid,
            'showinactivitychooser' => $showinactivitychooser,
        ]);

        $context =  \core\context\course::instance($courseid);
        self::validate_context($context);
        return types_helper::override_type_showinactivitychooser($tooltypeid, $courseid, $context, $showinactivitychooser);
    }

    /**
     * Get service returns definition.
     *
     * @return external_value
     */
    public static function execute_returns(): external_value {
        return new external_value(PARAM_BOOL, 'Success');
    }
}
