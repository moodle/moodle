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
 * Mapped config.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\config;

/**
 * Mapped config.
 *
 * The sole purpose of this implementation is to map the keys of an underlying
 * config object to something else. So for instance, if the proxies object contains
 * the key 'mouse', we can simulate that it is named 'souris' with the following:
 *
 *   $config = new static_config(['cat' => 1, 'mouse' => 2]);
 *   $mapped = new mapped_config($config, ['souris' => 'mouse']);
 *
 * Note that unmapped keys are deferred to the proxied object, a missing mapping
 * does not block anything, so do not use mappings to restrict the access to
 * a certain amount of objects.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mapped_config extends proxy_config {

    /** @var array The mappings. */
    private $mappings;

    /**
     * Constructor.
     *
     * @param config $config The configuration.
     * @param array $mappings The mappings.
     */
    public function __construct(config $config, array $mappings) {
        parent::__construct($config);
        $this->mappings = $mappings;
    }

    /**
     * Get a value.
     *
     * @param string $name The name.
     * @return mixed
     */
    public function get($name) {
        return parent::get($this->get_name($name));
    }

    /**
     * Get the final name.
     *
     * @param string $name The name requested.
     * @return string The name mapped.
     */
    private function get_name($name) {
        if (isset($this->mappings[$name])) {
            return $this->mappings[$name];
        }
        return $name;
    }

    /**
     * Get all config.
     *
     * @return array
     */
    public function get_all() {
        $mappings = array_flip($this->mappings);
        $items = parent::get_all();
        return array_reduce(array_keys($items), function($carry, $key) use ($items, $mappings) {
            $newkey = isset($mappings[$key]) ? $mappings[$key] : $key;
            $carry[$newkey] = $items[$key];
            return $carry;
        });
    }

    /**
     * Whether we have that config.
     *
     * @param string $name The config name.
     * @return bool
     */
    public function has($name) {
        return parent::has($this->get_name($name));
    }

    /**
     * Set a value.
     *
     * @param string $name Name of the config.
     * @param mixed $value The value.
     */
    public function set($name, $value) {
        return parent::set($this->get_name($name), $value);
    }

    /**
     * Set many.
     *
     * @param array $values Keys are config names, and values are values.
     */
    public function set_many(array $values) {
        $values = array_reduce(array_keys($values), function($carry, $key) use ($values) {
            $carry[$this->get_name($key)] = $values[$key];
            return $carry;
        }, []);
        return parent::set_many($values);
    }

}
