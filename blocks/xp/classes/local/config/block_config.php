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
 * Block config.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\config;

use block_base;
use coding_exception;
use stdClass;

/**
 * Block config.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_config implements config {

    /** @var block_base The block instance. */
    private $bi;
    /** @var stdClass The reference to the block config object. */
    private $data;

    /**
     * Constructor.
     *
     * @param block_base $bi The block instance.
     */
    public function __construct(block_base $bi) {
        // This is a quirky check, but we wnat to make sure we do not override the instance config,
        // and one way to do that is to ensure that the developer loaded the block config.
        if (empty($bi->instance)) {
            throw new coding_exception('The block instance needs to be loaded.');
        }

        // Cater for empty config.
        if ($bi->config === null) {
            $bi->config = new stdClass();
        }

        // Keep a reference to the block, and its config object.
        $this->data = &$bi->config;
        $this->bi = $bi;
    }

    /**
     * Get a value.
     *
     * @param string $name The name.
     * @return mixed
     */
    public function get($name) {
        if (!$this->has($name)) {
            throw new coding_exception('Unknown config name.');
        }
        return $this->data->{$name};
    }

    /**
     * Get all config.
     *
     * @return array
     */
    public function get_all() {
        return (array) $this->data;
    }

    /**
     * Whether we have that config.
     *
     * @param string $name The config name.
     * @return bool
     */
    public function has($name) {
        return property_exists($this->data, $name);
    }

    /**
     * Commit the config.
     *
     * @return void
     */
    private function save() {
        // As we've got a reference to the block instance's config, committing the
        // config should work as intended. That also covers changes in that instance
        // while we're holding it here. Although that's unlikely to happen... and
        // not recommended for sure!
        $this->bi->instance_config_commit();
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
     * Set a value without committing it.
     *
     * @param string $name Name of the config.
     * @param mixed $value The value.
     */
    private function set_without_save($name, $value) {
        if (!is_scalar($value)) {
            throw new coding_exception('Value for config is not scalar: ' . $value);
        }
        $this->data->{$name} = $value;
    }

    /**
     * Set many.
     *
     * @param array $values Keys are config names, and values are values.
     */
    public function set_many(array $values) {
        foreach ($values as $name => $value) {
            $this->set_without_save($name, $value);
        }
        $this->save();
    }

}
