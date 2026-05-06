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
 * Selection of plugins that can work as H5P libraries handlers
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2020 Sara Arjona <sara@moodle.com>
 */
namespace core_admin\setting\setting;

class h5plib_handler_select extends \core_admin\setting\setting\configselect {

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

        $this->choices = \core_h5p\local\library\autoloader::get_all_handlers();
        foreach ($this->choices as $name => $class) {
            $this->choices[$name] = new \lang_string('sitepolicyhandlerplugin', 'core_admin',
                ['name' => new \lang_string('pluginname', $name), 'component' => $name]);
        }

        return true;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(h5plib_handler_select::class, \admin_settings_h5plib_handler_select::class);
