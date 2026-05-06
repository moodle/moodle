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
 * Admin setting that is a list of installed filter plugins.
 *
 * @copyright 2015 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_admin\setting\setting;

class pickfilters extends \admin_setting_configmulticheckbox {

    /**
     * Constructor
     *
     * @param string $name unique ascii name, either 'mysetting' for settings
     *      that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised name
     * @param string $description localised long description
     * @param array $default the default. E.g. array('urltolink' => 1, 'emoticons' => 1)
     */
    public function __construct($name, $visiblename, $description, $default) {
        if (empty($default)) {
            $default = array();
        }
        $this->load_choices();
        foreach ($default as $plugin) {
            if (!isset($this->choices[$plugin])) {
                unset($default[$plugin]);
            }
        }
        parent::__construct($name, $visiblename, $description, $default, null);
    }

    public function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = array();

        foreach (\core_component::get_plugin_list('filter') as $plugin => $unused) {
            $this->choices[$plugin] = filter_get_name($plugin);
        }
        return true;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(pickfilters::class, \admin_setting_pickfilters::class);
