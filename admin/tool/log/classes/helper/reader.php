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
     * Function decodes the other field into an array using either PHP serialisation or JSON.
     *
     * Note that this does not rely on the config setting, it supports both formats, so you can
     * use it for data before/after making a change to the config setting.
     *
     * The return value is usually an array but it can also be null or a boolean or something.
     *
     * @param string $other Other value
     * @return mixed Decoded value
     */
    public static function decode_other(?string $other) {
        if ($other === 'N;' || preg_match('~^.:~', $other ?? '')) {
            return unserialize($other);
        } else {
            return json_decode($other ?? '', true);
        }
    }

    /**
     * Adds ID column to $sort to make sure events from one request
     * within 1 second are returned in the same order.
     *
     * @param string $sort
     * @return string sort string
     */
    protected static function tweak_sort_by_id($sort) {
        if (empty($sort)) {
            // Mysql does this - unlikely to be used in real life because $sort is always expected.
            $sort = "id ASC";
        } else if (stripos($sort, 'timecreated') === false) {
            $sort .= ", id ASC";
        } else if (stripos($sort, 'timecreated DESC') !== false) {
            $sort .= ", id DESC";
        } else {
            $sort .= ", id ASC";
        }

        return $sort;
    }
}
