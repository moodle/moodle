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
 * File containing the field_value_validators class.
 *
 * @package    tool_uploaduser
 * @copyright  2019 Mathew May
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_uploaduser\local;

defined('MOODLE_INTERNAL') || die();

/**
 * Field validator class.
 *
 * @package    tool_uploaduser
 * @copyright  2019 Mathew May
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class field_value_validators {

    /**
     * List of valid and compatible themes.
     *
     * @return array
     */
    protected static $themescache;

    /**
     * Validates the value provided for the theme field.
     *
     * @param string $value The value for the theme field.
     * @return array Contains the validation status and message.
     */
    public static function validate_theme($value) {
        global $CFG;

        $status = 'normal';
        $message = '';

        // Validate if user themes are allowed.
        if (!$CFG->allowuserthemes) {
            $status = 'warning';
            $message = get_string('userthemesnotallowed', 'tool_uploaduser');
        } else {
            // Cache list of themes if not yet set.
            if (!isset(self::$themescache)) {
                self::$themescache = get_list_of_themes();
            }

            // Check if we have a valid theme.
            if (empty($value)) {
                $status = 'warning';
                $message = get_string('notheme', 'tool_uploaduser');
            } else if (!isset(self::$themescache[$value])) {
                $status = 'warning';
                $message = get_string('invalidtheme', 'tool_uploaduser', s($value));
            }
        }

        return [$status, $message];
    }
}
