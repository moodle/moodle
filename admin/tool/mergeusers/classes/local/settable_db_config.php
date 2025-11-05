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
 * Desc.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mergeusers\local;

/**
 * Database-related settings to use while merging users.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @property-read array gathering Gathering instance to use for the CLI script.
 * @property-read array exceptions List of tables that are excluded from processing.
 * @property-read array compoundindexes List of tables with compound indexes, including both from database schema and also
 *  from PHP Moodle code.
 * @property-read array userfieldnames List of tables and "default" one, with the list of column names within that table
 *  to consider a user-related field. "default" table name applies to any table not listed explicitly on this list.
 * @property-read array tablemergers List of table mergers, and a "default" one. "default" table merger applies to any table
 *  not explicitly listed on this list.
 * @property bool alwaysrollback Proceed with the merge but rollback it at the very last moment.
 * @property bool debugdb Show database debug output.
 */
final class settable_db_config extends db_config {
    /** @var string[] list of settable keys. */
    public static array $settablekeys = [
        'alwaysrollback' => 'alwaysrollback',
        'debugdb' => 'debugdb',
    ];

    /**
     * Allows to overwrite (i.e., with the highest priority) the setting name with the given value.
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set(string $name, mixed $value): void {
        if (!isset(self::$settablekeys[$name])) {
            return;
        }
        $this->settings[$name] = $value;
    }
}
