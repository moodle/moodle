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
 * Selection of grade report in user profiles
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_admin\setting\setting;

class grade_profilereport extends \admin_setting_configselect {
    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        parent::__construct('grade_profilereport', get_string('profilereport', 'grades'), get_string('profilereport_help', 'grades'), 'user', null);
    }

    /**
     * Loads an array of choices for the configselect control
     *
     * @return bool always return true
     */
    public function load_choices() {
        if (is_array($this->choices)) {
            return true;
        }
        $this->choices = array();

        global $CFG;
        require_once($CFG->libdir.'/gradelib.php');

        foreach (\core_component::get_plugin_list('gradereport') as $plugin => $plugindir) {
            if (file_exists($plugindir.'/lib.php')) {
                require_once($plugindir.'/lib.php');
                $functionname = 'grade_report_'.$plugin.'_profilereport';
                if (function_exists($functionname)) {
                    $this->choices[$plugin] = get_string('pluginname', 'gradereport_'.$plugin);
                }
            }
        }
        return true;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(grade_profilereport::class, \admin_setting_grade_profilereport::class);
