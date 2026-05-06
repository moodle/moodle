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
 * Multiselect for current modules
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_admin\setting\setting;

class configmultiselect_modules extends \core_admin\setting\setting\configmultiselect {
    private $excludesystem;

    /**
     * Calls parent::__construct - note array $choices is not required
     *
     * @param string $name setting name
     * @param string $visiblename localised setting name
     * @param string $description setting description
     * @param array $defaultsetting a plain array of default module ids
     * @param bool $excludesystem If true, excludes modules with 'system' archetype
     */
    public function __construct($name, $visiblename, $description, $defaultsetting = array(),
            $excludesystem = true) {
        parent::__construct($name, $visiblename, $description, $defaultsetting, null);
        $this->excludesystem = $excludesystem;
    }

    /**
     * Loads an array of current module choices
     *
     * @return bool always return true
     */
    public function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = array();

        global $CFG, $DB;
        $records = $DB->get_records('modules', array('visible'=>1), 'name');
        foreach ($records as $record) {
            // Exclude modules if the code doesn't exist
            if (file_exists("$CFG->dirroot/mod/$record->name/lib.php")) {
                // Also exclude system modules (if specified)
                if (!($this->excludesystem &&
                        plugin_supports('mod', $record->name, FEATURE_MOD_ARCHETYPE) ===
                        MOD_ARCHETYPE_SYSTEM)) {
                    $this->choices[$record->id] = $record->name;
                }
            }
        }
        return true;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(configmultiselect_modules::class, \admin_setting_configmultiselect_modules::class);
