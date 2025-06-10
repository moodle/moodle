<?php
// This file is part of the customcert module for Moodle - http://moodle.org/
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
 * Provides helper functionality.
 *
 * @package    mod_customcert
 * @copyright  2021 Mark Nelson <mdjnelson@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_customcert;

use core_user\fields;

/**
 * Class helper.
 *
 * Helper functionality for this module.
 *
 * @package    mod_customcert
 * @copyright  2021 Mark Nelson <mdjnelson@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * A centralised location for the all name fields.
     *
     * Returns a sql string snippet.
     *
     * @param string $tableprefix table query prefix to use in front of each field.
     * @return string All name fields.
     */
    public static function get_all_user_name_fields(string $tableprefix = ''): string {
        $alternatenames = [];
        foreach (fields::get_name_fields() as $field) {
            $alternatenames[$field] = $field;
        }

        if ($tableprefix) {
            foreach ($alternatenames as $key => $altname) {
                $alternatenames[$key] = $tableprefix . '.' . $altname;
            }
        }

        return implode(',', $alternatenames);
    }
}
