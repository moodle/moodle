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
 * @property-read bool alwaysrollback Proceed with the merge but rollback it at the very last moment.
 * @property-read bool debugdb Show database debug output.
 */
class db_config {
    /** @var string[] list of valid keys for the root setting names. */
    public static array $validkeys = [
        'gathering' => 'gathering',
        'exceptions' => 'exceptions',
        'compoundindexes' => 'compoundindexes',
        'userfieldnames' => 'userfieldnames',
        'tablemergers' => 'tablemergers',
        'alwaysrollback' => 'alwaysrollback',
        'debugdb' => 'debugdb',
    ];
    /** @var array current database-related settings. */
    protected array $settings;

    /**
     * Builds the instance with these initial set of settings.
     *
     * @param array $settings initial list of database-related settings.
     */
    public function __construct(array $settings = []) {
        $this->settings = [];
        $this->add_raw($settings);
    }

    /**
     * Appends additional settings provided its name and value.
     * @param string $name root setting name.
     * @param mixed $value value for the setting.
     * @return void
     */
    public function add(string $name, mixed $value): void {
        $this->add_raw([$name => $value]);
    }

    /**
     * Appends the given settings.
     *
     * @param array $settings
     * @return void
     */
    public function add_raw(array $settings): void {
        $this->settings = array_replace_recursive($settings, $this->settings);
        foreach ($this->settings as $name => $value) {
            if (!isset(self::$validkeys[$name])) {
                unset($this->settings[$name]);
            }
        }
    }

    /**
     * Allows to aggregate current settings with the provided by the other $config.
     *
     * @param self $config settings to merge with the current settings.
     * @return void
     */
    public function merge_with(self $config): void {
        $this->add_raw($config->settings);
    }

    /**
     * Provides the value of the root setting name.
     *
     * @param string $name root setting name.
     * @return mixed If $name is not a valid key or is not set yet, it will return null.
     *  Finally, when matches, it returns the given value.
     */
    public function __get(string $name): mixed {
        if (!isset($this->settings[$name])) {
            return null;
        }
        return $this->settings[$name];
    }

    /**
     * Checks whether the list of database-related settings is empty.
     *
     * Only valid setting names are checked.
     *
     * @return bool true when there is no valid setting name set; false otherwise.
     */
    public function empty(): bool {
        return empty($this->settings);
    }

    /**
     * Prevents setting any value to this instance.
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set(string $name, mixed $value): void {
    }

    /**
     * Tells the JSON representation of these settings.
     *
     * @return string the JSON representation of these settings.
     */
    public function to_json(): string {
        return jsonizer::to_json($this->settings);
    }
}
