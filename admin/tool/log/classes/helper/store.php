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
 * Helper trait store.
 *
 * @package    tool_log
 * @copyright  2014 onwards Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_log\helper;
defined('MOODLE_INTERNAL') || die();

/**
 * Helper trait store. Adds some helper methods for stores.
 *
 * @package    tool_log
 * @copyright  2014 onwards Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait store {

    /** @var \tool_log\log\manager $manager manager instance. */
    protected $manager;

    /** @var string $component Frankenstyle store name. */
    protected $component;

    /** @var string $store name of the store. */
    protected $store;


    /**
     * Setup store specific variables.
     *
     * @param \tool_log\log\manager $manager manager instance.
     */
    protected function helper_setup(\tool_log\log\manager $manager) {
        $this->manager = $manager;
        $called = get_called_class();
        $parts = explode('\\', $called);
        if (!isset($parts[0]) || strpos($parts[0], 'logstore_') !== 0) {
            throw new \coding_exception("Store $called doesn't define classes in correct namespaces.");
        }
        $this->component = $parts[0];
        $this->store = str_replace('logstore_', '', $this->store);
    }

    /**
     * Api to get plugin config
     *
     * @param string $name name of the config.
     * @param null|mixed $default default value to return.
     *
     * @return mixed|null return config value.
     */
    protected function get_config($name, $default = null) {
        $value = get_config($this->component, $name);
        if ($value !== false) {
            return $value;
        }
        return $default;
    }

}
