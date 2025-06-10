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
 * General purpose utility class.
 *
 * @package local_onenote
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft Open Technologies, Inc. (http://msopentech.com/)
 */

namespace local_onenote;

defined('MOODLE_INTERNAL') || die();

/**
 * General purpose utility class.
 */
class utils {
    /**
     * Convert to a string.
     *
     * @param mixed $val The string to convert
     * @return string A string representation.
     */
    public static function tostring($val) {
        if (is_scalar($val)) {
            if (is_bool($val)) {
                return '(bool)' . (string) (int) $val;
            } else {
                return '(' . gettype($val) . ')' . (string) $val;
            }
        } else if (is_null($val)) {
            return '(null)';
        } else {
            return print_r($val, true);
        }
    }

    /**
     * Record a debug message.
     *
     * @param string $message The debug message to log.
     * @param string $where
     * @param string $debugdata
     */
    public static function debug($message, $where = '', $debugdata = null) {
        if (class_exists('\local_o365\utils')) {
            \local_o365\utils::debug($message, $where, $debugdata);
        }
    }
}
