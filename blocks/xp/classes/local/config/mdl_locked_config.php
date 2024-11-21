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
 * Moodle locked config.
 *
 * @package    block_xp
 * @copyright  2020 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\config;

/**
 * Moodle locked config.
 *
 * This interfaces the locked flags that can be set on Moodle admin settings. This
 * object abstracts away the fact that values are stored under the setting key
 * appended with '_locked', we only need to know the config name here.
 *
 * The keys of expected locked settings must be provided, and then getting
 * their config will return whether they are in a locked state. As for other
 * config, an error will be triggered if the setting is not declared as lockable.
 *
 * @package    block_xp
 * @copyright  2020 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mdl_locked_config extends mdl_config {

    /**
     * Constructor.
     *
     * @param string $component The component.
     * @param string[] $keys The setting names that are lockable.
     */
    public function __construct($component, array $keys) {
        parent::__construct($component, new static_config(array_reduce($keys, function($carry, $key) {
            return array_merge($carry, ["{$key}_locked" => false]);
        }, [])));
    }

    /**
     * Get a value.
     *
     * @param string $name The name.
     * @return mixed
     */
    public function get($name) {
        return (bool) parent::get($name . '_locked');
    }

    /**
     * Get all config.
     *
     * @return array
     */
    public function get_all() {
        $all = parent::get_all();
        return array_reduce(array_keys($all), function($carry, $key) use ($all) {
            $carry[substr($key, 0, -7)] = (bool) $all[$key];
            return $carry;
        }, []);
    }

    /**
     * Whether we have that config.
     *
     * @param string $name The config name.
     * @return bool
     */
    public function has($name) {
        return parent::has($name . '_locked');
    }

    /**
     * Set a value.
     *
     * @param string $name Name of the config.
     * @param mixed $value The value.
     */
    public function set($name, $value) {
        parent::set($name . '_locked', $value);
    }

}
