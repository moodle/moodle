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
 * Provides an entity to transform from and to JSON content.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\local;

/**
 * Provides an entity to transform from and to JSON content.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class jsonizer {
    /**
     * Transforms the given $data into a valid JSON string.
     *
     * @param mixed $data may be a string, an array, among other types.
     * @return string
     */
    public static function to_json(mixed $data): string {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    /**
     * Transforms into an associative array the given JSON content.
     *
     * @param string $data
     * @return mixed may be null when the content is not a valid JSON string; otherwise, an associative array.
     */
    public static function from_json(string $data): mixed {
        return json_decode($data, true);
    }

    /**
     * Ensures the $data is a valid JSON expression and formats it in human-readable format.
     *
     * @param string $data
     * @return mixed a JSON string in human-readable format; or null or "null" on error.
     */
    public static function format(string $data): mixed {
        return self::to_json(self::from_json($data));
    }
}
