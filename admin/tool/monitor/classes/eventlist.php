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
 * Event documentation
 *
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_monitor;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for returning event information.
 *
 * @since      Moodle 2.8
 * @package    tool_monitor
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class eventlist {
    /**
     * Return all of the core event files.
     *
     * @return array Core events.
     */
    protected static function get_core_eventlist() {
        global $CFG;

        // Disable developer debugging as deprecated events will fire warnings.
        // Setup backup variables to restore the following settings back to what they were when we are finished.
        $debuglevel          = $CFG->debug;
        $debugdisplay        = $CFG->debugdisplay;
        $debugdeveloper      = $CFG->debugdeveloper;
        $CFG->debug          = 0;
        $CFG->debugdisplay   = false;
        $CFG->debugdeveloper = false;

        $eventinformation = array();
        $directory = $CFG->libdir . '/classes/event';
        $files = self::get_file_list($directory);

        // Remove exceptional events that will cause problems being displayed.
        if (isset($files['unknown_logged'])) {
            unset($files['unknown_logged']);
        }
        foreach ($files as $file => $location) {
            $classname = '\\core\\event\\' . $file;
            // Check to see if this is actually a valid event.
            if (method_exists($classname, 'get_static_info')) {
                $ref = new \ReflectionClass($classname);
                // Ignore abstracts.
                if (!$ref->isAbstract() && $file != 'manager') {
                    $eventinformation[$classname] = $classname::get_name_with_info();
                }
            }
        }
        // Now enable developer debugging as event information has been retrieved.
        $CFG->debug          = $debuglevel;
        $CFG->debugdisplay   = $debugdisplay;
        $CFG->debugdeveloper = $debugdeveloper;
        return $eventinformation;
    }

    /**
     * This function returns an array of all events for the plugins of the system.
     *
     * @param bool $withoutcomponent Return an eventlist without associated components.
     *
     * @return array A list of events from all plug-ins.
     */
    protected static function get_non_core_eventlist($withoutcomponent = false) {
        global $CFG;
        // Disable developer debugging as deprecated events will fire warnings.
        // Setup backup variables to restore the following settings back to what they were when we are finished.
        $debuglevel          = $CFG->debug;
        $debugdisplay        = $CFG->debugdisplay;
        $debugdeveloper      = $CFG->debugdeveloper;
        $CFG->debug          = 0;
        $CFG->debugdisplay   = false;
        $CFG->debugdeveloper = false;

        $noncorepluginlist = array();
        $plugintypes = \core_component::get_plugin_types();
        foreach ($plugintypes as $plugintype => $notused) {
            $pluginlist = \core_component::get_plugin_list($plugintype);
            foreach ($pluginlist as $plugin => $directory) {
                $plugindirectory = $directory . '/classes/event';
                foreach (self::get_file_list($plugindirectory) as $eventname => $notused) {
                    $fullpluginname = $plugintype . '_' . $plugin;
                    $plugineventname = '\\' . $fullpluginname . '\\event\\' . $eventname;
                    // Check that this is actually an event.
                    if (method_exists($plugineventname, 'get_static_info')  && $fullpluginname !== 'tool_monitor') { // No selfie here.
                        $ref = new \ReflectionClass($plugineventname);
                        if (!$ref->isAbstract() && $fullpluginname !== 'logstore_legacy') {
                            if ($withoutcomponent) {
                                $noncorepluginlist[$plugineventname] = $plugineventname::get_name_with_info();
                            } else {
                                $noncorepluginlist[$fullpluginname][$plugineventname] = $plugineventname::get_name_with_info();
                            }
                        }
                    }
                }
            }
        }

        // Now enable developer debugging as event information has been retrieved.
        $CFG->debug          = $debuglevel;
        $CFG->debugdisplay   = $debugdisplay;
        $CFG->debugdeveloper = $debugdeveloper;

        return $noncorepluginlist;
    }

    /**
     * Returns a list of files with a full directory path in a specified directory.
     *
     * @param string $directory location of files.
     * @return array full location of files from the specified directory.
     */
    protected static function get_file_list($directory) {
        global $CFG;
        $directoryroot = $CFG->dirroot;
        $finalfiles = array();
        if (is_dir($directory)) {
            if ($handle = opendir($directory)) {
                $files = scandir($directory);
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..') {
                        // Ignore the file if it is external to the system.
                        if (strrpos($directory, $directoryroot) !== false) {
                            $location = substr($directory, strlen($directoryroot));
                            $name = substr($file, 0, -4);
                            $finalfiles[$name] = $location  . '/' . $file;
                        }
                    }
                }
            }
        }
        return $finalfiles;
    }

    /**
     * Get a list of events present in the system.
     *
     * @param bool $withoutcomponent Return an eventlist without associated components.
     *
     * @return array list of events present in the system.
     */
    public static function get_all_eventlist($withoutcomponent = false) {
        if ($withoutcomponent) {
            $return = array_merge(self::get_core_eventlist(), self::get_non_core_eventlist($withoutcomponent));
            array_multisort($return, SORT_NATURAL);
        } else {
            $return = array_merge(array('core' => self::get_core_eventlist()),
                    self::get_non_core_eventlist($withoutcomponent = false));
        }
        return $return;
    }

    /**
     * Return list of plugins that have events.
     *
     * @param array $eventlist a list of events present in the system {@link eventlist::get_all_eventlist}.
     *
     * @return array list of plugins with human readable name.
     */
    public static function get_plugin_list($eventlist = array()) {
        if (empty($eventlist)) {
            $eventlist = self::get_all_eventlist();
        }
        $plugins = array_keys($eventlist);
        $return = array();
        foreach ($plugins as $plugin) {
            if ($plugin === 'core') {
                $return[$plugin] = get_string('core', 'tool_monitor');
            } else if (get_string_manager()->string_exists('pluginname', $plugin)) {
                $return[$plugin] = get_string('pluginname', $plugin);
            } else {
                $return[$plugin] = $plugin;
            }
        }

        return $return;
    }

    /**
     * validate if the given event belongs to the given plugin.
     *
     * @param string $plugin Frankenstyle name of the plugin.
     * @param string $eventname Full qualified event name.
     * @param array $eventlist List of events generated by {@link eventlist::get_all_eventlist}
     *
     * @return bool Returns true if the selected event belongs to the selected plugin, false otherwise.
     */
    public static function validate_event_plugin($plugin, $eventname, $eventlist = array()) {
        if (empty($eventlist)) {
            $eventlist = self::get_all_eventlist();
        }
        if (isset($eventlist[$plugin][$eventname])) {
            return true;
        }

        return false;
    }
}
