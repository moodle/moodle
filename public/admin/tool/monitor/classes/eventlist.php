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

namespace tool_monitor;

use core_collator;
use core_component;
use core_plugin_manager;
use ReflectionClass;

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
     * Get a list of events present in the system.
     *
     * @param bool $withoutcomponent Return an eventlist without associated components.
     *
     * @return array list of events present in the system.
     */
    public static function get_all_eventlist($withoutcomponent = false) {
        global $CFG;

        // Disable developer debugging as deprecated events will fire warnings.
        // Setup backup variables to restore the following settings back to what they were when we are finished.
        $debuglevel          = $CFG->debug;
        $debugdisplay        = $CFG->debugdisplay;
        $debugdeveloper      = $CFG->debugdeveloper;
        $CFG->debug          = 0;
        $CFG->debugdisplay   = false;
        $CFG->debugdeveloper = false;

        // List of exceptional events that will cause problems if displayed.
        $eventsignore = [
            \core\event\unknown_logged::class,
            \logstore_legacy\event\legacy_logged::class,
        ];

        $return = [];

        $events = core_component::get_component_classes_in_namespace(null, 'event');
        foreach (array_keys($events) as $event) {
            // We need to filter all classes that extend event base, or the base class itself.
            if (is_a($event, \core\event\base::class, true) && !in_array($event, $eventsignore)) {

                $reflectionclass = new ReflectionClass($event);
                if ($reflectionclass->isAbstract()) {
                    continue;
                }

                // We can't choose this component's own events.
                [$component] = explode('\\', $event);
                if ($component === 'tool_monitor') {
                    continue;
                }

                if ($withoutcomponent) {
                    $return["\\{$event}"] = $event::get_name_with_info();
                } else {
                    $return[$component]["\\{$event}"] = $event::get_name_with_info();
                }
            }
        }

        // Now enable developer debugging as event information has been retrieved.
        $CFG->debug          = $debuglevel;
        $CFG->debugdisplay   = $debugdisplay;
        $CFG->debugdeveloper = $debugdeveloper;

        if ($withoutcomponent) {
            array_multisort($return, SORT_NATURAL);
        }

        return $return;
    }

    /**
     * Return list of plugins that have events.
     *
     * @param array $eventlist a list of events present in the system {@link eventlist::get_all_eventlist}.
     * @return array list of plugins with human readable name, grouped by their type
     */
    public static function get_plugin_list($eventlist = array()) {
        $pluginmanager = core_plugin_manager::instance();

        if (empty($eventlist)) {
            $eventlist = self::get_all_eventlist();
        }

        $plugins = array_keys($eventlist);
        $return = array();
        foreach ($plugins as $plugin) {

            // Core sub-systems are grouped together and are denoted by a distinct lang string.
            if (strpos($plugin, 'core') === 0) {
                $plugintype = get_string('core', 'tool_monitor');
                $pluginname = get_string('coresubsystem', 'tool_monitor', $plugin);
            } else {
                [$type] = core_component::normalize_component($plugin);
                $plugintype = $pluginmanager->plugintype_name_plural($type);
                $pluginname = $pluginmanager->plugin_name($plugin);
            }

            $return[$plugintype][$plugin] = $pluginname;
        }

        // Sort returned components according to their type, followed by name.
        core_collator::ksort($return);
        array_walk($return, function(array &$componenttype) {
            core_collator::asort($componenttype);
        });

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
