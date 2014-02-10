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
 * Reader helper trait.
 *
 * @package    tool_log
 * @copyright  2014 onwards Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_log\helper;

defined('MOODLE_INTERNAL') || die();

/**
 * Reader helper trait.
 * \tool_log\helper\store must be included before using this trait.
 *
 * @package    tool_log
 * @copyright  2014 onwards Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @property string $component Frankenstyle plugin name initialised in store trait.
 * @property string $store short plugin name initialised in store trait.
 */
trait reader {
    /**
     * Default get name api.
     *
     * @return string name of the store.
     */
    public function get_name() {
        if (get_string_manager()->string_exists('pluginname', $this->component)) {
            return get_string('pluginname', $this->component);
        }
        return $this->store;
    }

    /**
     * Default get description method.
     *
     * @return string description of the store.
     */
    public function get_description() {
        if (get_string_manager()->string_exists('pluginname_desc', $this->component)) {
            return get_string('pluginname_desc', $this->component);
        }
        return $this->store;
    }

    /**
     * If the current user can access current store or not.
     *
     * @param \context $context
     *
     * @return bool
     */
    public function can_access(\context $context) {
        return has_capability('logstore/' . $this->store . ':read', $context);
    }
}
