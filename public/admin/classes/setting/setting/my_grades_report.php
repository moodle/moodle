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
 * Provides a selection of grade reports to be used for "grades".
 *
 * @copyright 2015 Adrian Greeve <adrian@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_admin\setting\setting;

class my_grades_report extends \core_admin\setting\setting\configselect {

    /**
     * Calls parent::__construct with specific arguments.
     */
    public function __construct() {
        parent::__construct('grade_mygrades_report', new \lang_string('mygrades', 'grades'),
                new \lang_string('mygrades_desc', 'grades'), 'overview', null);
    }

    /**
     * Loads an array of choices for the configselect control.
     *
     * @return bool always returns true.
     */
    public function load_choices() {
        global $CFG; // Remove this line and behold the horror of behat test failures!
        $this->choices = array();
        foreach (\core_component::get_plugin_list('gradereport') as $plugin => $plugindir) {
            if (file_exists($plugindir . '/lib.php')) {
                require_once($plugindir . '/lib.php');
                // Check to see if the class exists. Check the correct plugin convention first.
                if (class_exists('gradereport_' . $plugin)) {
                    $classname = 'gradereport_' . $plugin;
                } else if (class_exists('grade_report_' . $plugin)) {
                    // We are using the old plugin naming convention.
                    $classname = 'grade_report_' . $plugin;
                } else {
                    continue;
                }
                if ($classname::supports_mygrades()) {
                    $this->choices[$plugin] = get_string('pluginname', 'gradereport_' . $plugin);
                }
            }
        }
        // Add an option to specify an external url.
        $this->choices['external'] = get_string('externalurl', 'grades');
        return true;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(my_grades_report::class, \admin_setting_my_grades_report::class);
