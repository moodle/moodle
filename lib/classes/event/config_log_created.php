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
 * Config log created.
 *
 * @package    core
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Event class for when an admin config log is created.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - string name: name of config setting
 *      - string plugin: name of plugin
 *      - string oldvalue: previous value
 *      - string value: new value
 * }
 *
 * @package    core
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class config_log_created extends base {

    /**
     * Initialise required event data properties.
     */
    protected function init() {
        $this->data['objecttable'] = 'config_log';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventconfiglogcreated');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        $name = $this->other['name'];
        $plugin = isset($this->other['plugin']) ? $this->other['plugin'] : 'core';
        $value = isset($this->other['value']) ? $this->other['value'] : 'Not set';
        $oldvalue = isset($this->other['oldvalue']) ? $this->other['oldvalue'] : 'Not set';
        return "The user with id '$this->userid' changed the config setting '$name' for component '$plugin' " .
               "from '$oldvalue' to '$value'.";
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/admin/index.php');
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['name'])) {
            throw new \coding_exception('The \'name\' value must be set in other.');
        }
        if (!array_key_exists('plugin', $this->other)) {
            throw new \coding_exception('The \'plugin\' value must be set in other.');
        }
        if (!array_key_exists('oldvalue', $this->other)) {
            throw new \coding_exception('The \'oldvalue\' value must be set in other.');
        }
        if (!array_key_exists('value', $this->other)) {
            throw new \coding_exception('The \'value\' value must be set in other.');
        }
    }

    public static function get_objectid_mapping() {
        // Config log is not mappable.
        return array('db' => 'config_log', 'restore' => base::NOT_MAPPED);
    }

    public static function get_other_mapping() {
        return false;
    }
}
