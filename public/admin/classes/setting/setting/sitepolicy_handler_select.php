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
 * Selection of plugins that can work as site policy handlers
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2018 Marina Glancy
 */
namespace core_admin\setting\setting;

class sitepolicy_handler_select extends \admin_setting_configselect {

    /**
     * Constructor
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting'
     *        for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting
     */
    public function __construct($name, $visiblename, $description, $defaultsetting = '') {
        parent::__construct($name, $visiblename, $description, $defaultsetting, null);
    }

    /**
     * Lazy-load the available choices for the select box
     */
    public function load_choices() {
        if (during_initial_install()) {
            return false;
        }
        if (is_array($this->choices)) {
            return true;
        }

        $this->choices = ['' => new \lang_string('sitepolicyhandlercore', 'core_admin')];
        $manager = new \core_privacy\local\sitepolicy\manager();
        $plugins = $manager->get_all_handlers();
        foreach ($plugins as $pname => $unused) {
            $this->choices[$pname] = new \lang_string('sitepolicyhandlerplugin', 'core_admin',
                ['name' => new \lang_string('pluginname', $pname), 'component' => $pname]);
        }

        return true;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(sitepolicy_handler_select::class, \admin_settings_sitepolicy_handler_select::class);
