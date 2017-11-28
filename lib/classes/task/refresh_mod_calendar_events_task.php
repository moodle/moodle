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
 * Adhoc task that updates all of the existing calendar events for modules that implement the *_refresh_events() hook.
 *
 * @package    core
 * @copyright  2017 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\task;

use core_plugin_manager;

defined('MOODLE_INTERNAL') || die();

/**
 * Class that updates all of the existing calendar events for modules that implement the *_refresh_events() hook.
 *
 * Custom data accepted:
 * - plugins -> Array of plugin names that need to be refreshed. Optional.
 *
 * @package     core
 * @copyright   2017 Jun Pataleta
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class refresh_mod_calendar_events_task extends adhoc_task {

    /**
     * Run the task to refresh calendar events.
     */
    public function execute() {
        global $CFG;

        require_once($CFG->dirroot . '/course/lib.php');

        // Specific list of plugins that need to be refreshed. If not set, then all mod plugins will be refreshed.
        $pluginstorefresh = null;
        if (isset($this->get_custom_data()->plugins)) {
            $pluginstorefresh = $this->get_custom_data()->plugins;
        }

        // Is course id set?
        if (isset($this->get_custom_data()->courseid)) {
            $courseid = $this->get_custom_data()->courseid;
        } else {
            $courseid = 0;
        }

        $pluginmanager = core_plugin_manager::instance();
        $modplugins = $pluginmanager->get_plugins_of_type('mod');
        foreach ($modplugins as $plugin) {
            // Check if a specific list of plugins is defined and check if it contains the plugin that is currently being evaluated.
            if (!empty($pluginstorefresh) && !in_array($plugin->name, $pluginstorefresh)) {
                // This plugin is not in the list, move on to the next one.
                continue;
            }
            // Refresh events.
            mtrace('Refreshing events for ' . $plugin->name);
            course_module_bulk_update_calendar_events($plugin->name, $courseid);
        }
    }
}
