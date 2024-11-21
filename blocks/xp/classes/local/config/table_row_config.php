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
 * Table row config.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\config;

use coding_exception;
use moodle_database;
use stdClass;

/**
 * Table row config.
 *
 * Stores the config in a row in a table. Only keys present in the
 * database result or in the defaults are accepted.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class table_row_config implements config {

    /** @var moodle_database The DB. */
    protected $db;
    /** @var string The table. */
    protected $table;
    /** @var array The conditions. */
    protected $conditions;
    /** @var config The config holding the defaults. */
    protected $defaults;
    /** @var bool Use defaults when null, if possible. */
    protected $defaulstifnull;
    /** @var stdClass The table row. */
    protected $record;
    /** @var array Reserved keys. */
    protected $reservedkeys;

    /**
     * Constructor.
     *
     * @param moodle_database $db The DB.
     * @param string $table The table.
     * @param config $defaults The defaults.
     * @param array $conditions The conditions to find the record.
     * @param bool $defaulstifnull Whether to use the defaults when the value is null.
     */
    public function __construct(moodle_database $db, $table, config $defaults, array $conditions = [], $defaulstifnull = false) {
        $this->db = $db;
        $this->table = $table;
        $this->conditions = $conditions;
        $this->defaults = $defaults;
        $this->defaulstifnull = $defaulstifnull;
        $this->reservedkeys = array_flip(array_merge(['id'], array_keys($conditions)));

        // Quick validation.
        foreach ($this->reservedkeys as $key => $unused) {
            if ($defaults->has($key)) {
                throw new coding_exception('Found conflict in defaults values and conditions at key: ' . $key);
            }
        }
    }

    /**
     * Get a value.
     *
     * @param string $name The name.
     * @return mixed
     */
    public function get($name) {
        if (array_key_exists($name, $this->reservedkeys)) {
            throw new coding_exception('Invalid config name: ' . $name);
        }
        if (!$this->has($name)) {
            throw new coding_exception('Unknown config: ' . $name);
        }

        $value = $this->record->{$name};
        if ($value === null && $this->defaulstifnull && $this->defaults->has($name)) {
            return $this->defaults->get($name);
        }
        return $value;
    }

    /**
     * Get all config.
     *
     * @return array
     */
    public function get_all() {
        $this->load();
        $data = (array) $this->record;
        unset($data['id']);
        $data = array_diff_key($data, $this->reservedkeys);

        if ($this->defaulstifnull) {
            foreach ($data as $key => $value) {
                if ($value === null && $this->defaults->has($key)) {
                    $data[$key] = $this->defaults->get($key);
                }
            }
        }

        return $data;
    }

    /**
     * Do we have the config?
     *
     * @param string $name The config name.
     * @return bool
     */
    public function has($name) {
        $this->load();
        return property_exists($this->record, $name);
    }

    /**
     * Load the configuration.
     *
     * @return void
     */
    protected function load() {
        if (!$this->record) {
            $record = $this->db->get_record($this->table, $this->conditions);
            if (!$record) {
                $record = $this->conditions;
                $record += $this->defaults->get_all();
                $record['id'] = 0;
                $record = (object) $record;
            }
            $this->record = $record;
        }
    }

    /**
     * Saves the config.
     *
     * @return void
     */
    protected function save() {
        if (!$this->record) {
            throw new coding_exception('The config must be loaded first.');
        }

        if (!$this->record->id) {
            $this->record->id = $this->db->insert_record($this->table, $this->record);
        } else {
            $this->db->update_record($this->table, $this->record);
        }
    }

    /**
     * Set a value.
     *
     * @param string $name Name of the config.
     * @param mixed $value The value.
     */
    public function set($name, $value) {
        $this->set_without_save($name, $value);
        $this->save();
    }

    /**
     * Internal set method.
     *
     * @param string $name The config name.
     * @param mixed $value The value.
     */
    protected function set_without_save($name, $value) {
        if (array_key_exists($name, $this->reservedkeys)) {
            throw new coding_exception('Invalid config name: ' . $name);
        } else if (!is_scalar($value)) {
            throw new coding_exception('Value for config is not scalar: ' . $value);
        }

        if (!$this->has($name)) {
            throw new coding_exception('Unknown config: ' . $name);
        }
        $this->record->{$name} = $value;
    }

    /**
     * Set many.
     *
     * @param array $values Keys are config names, and values are values.
     * @throws coding_exception When a value is not scalar.
     */
    public function set_many(array $values) {
        foreach ($values as $key => $value) {
            $this->set_without_save($key, $value);
        }
        $this->save();
    }
}
