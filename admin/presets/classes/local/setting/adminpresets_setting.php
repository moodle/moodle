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

namespace core_adminpresets\local\setting;

use admin_setting;
use moodle_exception;
use stdClass;

/**
 * Admin tool presets plugin to load some settings.
 *
 * @package          core_adminpresets
 * @copyright        2021 Pimenko <support@pimenko.com><pimenko.com>
 * @author           Jordan Kesraoui | Sylvain Revenu | Pimenko based on David Monllaó <david.monllao@urv.cat> code
 * @license          http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class adminpresets_setting {

    /**
     * @var admin_setting
     */
    protected $settingdata;

    /**
     * @var delegation
     */
    protected $delegation;

    /**
     * The setting DB value
     *
     * @var mixed
     */
    protected $value;

    /**
     * Stores the visible value of the setting DB value
     *
     * @var string
     */
    protected $visiblevalue;

    /**
     * For multiple value settings, used to look for the other values
     *
     * @var string
     */
    protected $attributes = false;

    /**
     * To store the setting attributes
     *
     * @var array
     */
    protected $attributesvalues;

    /**
     * Stores the setting data and the selected value
     *
     * @param admin_setting $settingdata admin_setting subclass
     * @param mixed $dbsettingvalue Actual value
     */
    public function __construct(admin_setting $settingdata, $dbsettingvalue) {
        $this->settingdata = $settingdata;
        $this->delegation = new delegation();

        if ($this->settingdata->plugin == '') {
            $this->settingdata->plugin = 'none';
        }

        // Applies specific children behaviors.
        $this->set_behaviors();
        $this->apply_behaviors();

        // Cleaning value.
        $this->set_value($dbsettingvalue);
    }

    /**
     * Each class can overwrite this method to specify extra processes
     */
    protected function set_behaviors() {
    }

    /**
     * Applies the children class specific behaviors
     *
     * See delegation class for the available extra behaviors
     */
    protected function apply_behaviors() {
        if (!empty($this->behaviors)) {

            foreach ($this->behaviors as $behavior => $arguments) {

                // The arguments of the behavior depends on the caller.
                $methodname = 'extra_' . $behavior;
                $this->delegation->{$methodname}($arguments);
            }
        }
    }

    /**
     * Gets the setting value.
     *
     * @return mixed The setting value
     */
    public function get_value() {
        return $this->value;
    }

    /**
     * Sets the setting value cleaning it
     *
     * Child classes should overwrite method to clean more acurately
     *
     * @param mixed $value Setting value
     * @return mixed Returns false if wrong param value
     */
    protected function set_value($value) {
        $this->value = $value;
        $this->set_visiblevalue();
    }

    public function get_visiblevalue() {
        return $this->visiblevalue;
    }

    /**
     * Sets the visible name for the setting selected value
     *
     * In most cases the child classes will overwrite
     */
    protected function set_visiblevalue() {
        $this->visiblevalue = $this->value;
    }

    public function get_attributes() {
        return $this->attributes;
    }

    public function get_attributes_values() {
        return $this->attributesvalues;
    }

    public function get_settingdata() {
        return $this->settingdata;
    }

    public function set_attribute_value($name, $value) {
        $this->attributesvalues[$name] = $value;
    }

    /**
     * Saves the setting attributes values
     *
     * @return     array        Array of inserted ids (in config_log)
     */
    public function save_attributes_values() {
        // Plugin name or null.
        $plugin = $this->settingdata->plugin;
        if ($plugin == 'none' || $plugin == '') {
            $plugin = null;
        }

        if (!$this->attributesvalues) {
            return false;
        }

        // To store inserted ids.
        $ids = [];
        foreach ($this->attributesvalues as $name => $value) {

            // Getting actual setting.
            $actualsetting = get_config($plugin, $name);

            // If it's the actual setting get off.
            if ($value == $actualsetting) {
                return false;
            }

            if ($id = $this->save_value($name, $value)) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    /**
     * Stores the setting into database, logs the change and returns the config_log inserted id
     *
     * @param bool $name Setting name to store.
     * @param mixed $value Setting value to store.
     * @return int|false config_log inserted id or false whenever the new value is the same as old value.
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function save_value($name = false, $value = null) {
        // Object values if no arguments.
        if ($value === null) {
            $value = $this->value;
        }
        if (!$name) {
            $name = $this->settingdata->name;
        }

        // Plugin name or null.
        $plugin = $this->settingdata->plugin;
        if ($plugin == 'none' || $plugin == '') {
            $plugin = null;
        }

        // Getting the actual value.
        $actualvalue = get_config($plugin, $name);

        // If it's the same it's not necessary.
        if ($actualvalue == $value) {
            return false;
        }

        set_config($name, $value, $plugin);

        return $this->to_log($plugin, $name, $value, $actualvalue);
    }

    /**
     * Copy of config_write method of the admin_setting class
     *
     * @param string $plugin
     * @param string $name
     * @param mixed $value
     * @param mixed $actualvalue
     * @return  integer The stored config_log id
     */
    protected function to_log($plugin, $name, $value, $actualvalue) {
        global $DB, $USER;

        // Log the change (pasted from admin_setting class).
        $log = new stdClass();
        $log->userid = during_initial_install() ? 0 : $USER->id; // 0 as user id during install.
        $log->timemodified = time();
        $log->plugin = $plugin;
        $log->name = $name;
        $log->value = $value;
        $log->oldvalue = $actualvalue;

        // Getting the inserted config_log id.
        if (!$id = $DB->insert_record('config_log', $log)) {
            throw new moodle_exception('errorinserting', 'core_adminpresets');
        }

        return $id;
    }
}
