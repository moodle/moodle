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
 * Event lister.
 *
 * Lists the events to use for the rule_event.
 *
 * @package    block_xp
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\rule;

use DirectoryIterator;
use cache;
use core_collator;
use core_component;
use core_plugin_manager;
use block_xp\local\config\config;

/**
 * Event lister class.
 *
 * @package    block_xp
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_lister {

    /** @var cache The cache store. */
    protected $cache;
    /** @var bool Whether is used site site. */
    protected $forwholesite;

    /**
     * Constructor.
     *
     * @param config $config The global config.
     */
    public function __construct(config $config) {
        $this->cache = cache::make('block_xp', 'ruleevent_eventslist');
        $this->forwholesite = $config->get('context') == CONTEXT_SYSTEM;
    }

    /**
     * Construct the list of events.
     *
     * @return array Where keys are translated component names, and their values are
     *               associative arrays of event class and their name.
     */
    protected function construct_events_list() {
        $list = [];

        $coreevents = $this->get_core_events();
        $list[] = [get_string('coresystem') => array_reduce(array_keys($coreevents), function($carry, $prefix) use ($coreevents) {
            return array_merge($carry, array_reduce($coreevents[$prefix], function($carry, $eventclass) use ($prefix) {
                $infos = self::get_event_infos($eventclass);
                if ($infos) {
                    $carry[$infos['eventname']] = get_string('colon', 'block_xp', (object) [
                        'a' => $prefix,
                        'b' => $infos['name'],
                    ]);
                }
                return $carry;
            }, []));
        }, []), ];

        // Get module events.
        $list = array_merge($list, self::get_events_list_from_plugintype('mod'));

        return $list;
    }

    /**
     * Get the core events.
     *
     * @return array The keys are translated subsystem names, the values are the classes.
     */
    protected function get_core_events() {
        global $CFG;

        // Add some system events.
        $eventclasses = [get_string('course') => [
            '\\core\\event\\course_viewed',
        ], ];

        return $eventclasses;
    }

    /**
     * Get the events list.
     *
     * @return array Array of array of array. Example:
     *               $list = [
     *                   'Component Name' => [
     *                       'className' => 'Event name',
     *                       'className2' => 'Event name 2',
     *                   ],
     *                   'Component Name 2' => [
     *                       ...
     *                   ]
     *               ];
     */
    final public function get_events_list() {
        $key = 'list';
        if (true || false === ($list = $this->cache->get($key))) {
            $list = $this->construct_events_list();
            $this->cache->set($key, $list);
        }
        return $list;
    }

    /**
     * Get the events classes from a component.
     *
     * @param string $component The component.
     * @return Array of classes. Those may not be relevant (abstract, invalid, ...)
     */
    public static function get_event_classes_from_component($component) {
        $directory = core_component::get_component_directory($component);
        $plugindirectory = $directory . '/classes/event';
        if (!is_dir($plugindirectory)) {
            return [];
        }

        $eventclasses = [];
        $diriter = new DirectoryIterator($plugindirectory);
        foreach ($diriter as $file) {
            if ($file->isDot() || $file->isDir()) {
                continue;
            }

            // It's a good idea to use the leading slashes because the event's property
            // 'eventname' includes them as well, so for consistency sake... Also we do
            // not check if the class exists because that would cause the class to be
            // autoloaded which would potentially trigger debugging messages when
            // it is deprecated.
            $name = substr($file->getFileName(), 0, -4);
            $classname = '\\' . $component . '\\event\\' . $name;
            $eventclasses[] = $classname;
        }
        return $eventclasses;
    }

    /**
     * Return the info about an event.
     *
     * The key 'name' is added to contain the readable name of the event.
     * It is done here because debugging is turned off and some events use
     * deprecated strings.
     *
     * We also add the key 'isdeprecated' which indicates whether the event
     * is obsolete or not.
     *
     * @param  string $class The name of the event class.
     * @return array|false
     */
    public static function get_event_infos($class) {
        global $CFG;
        $infos = false;

        // We need to disable debugging as some events can be deprecated.
        $debuglevel = $CFG->debug;
        $debugdisplay = $CFG->debugdisplay;
        $debugusers = $CFG->debugusers ?? '';
        $CFG->debugusers = '';
        set_debugging(0, false);

        // Check that the event exists, and is not an abstract event.
        try {
            if (method_exists($class, 'get_static_info')) {
                $ref = new \ReflectionClass($class);
                if (!$ref->isAbstract()) {
                    $infos = $class::get_static_info();
                    $hasinfomethod = method_exists($class, 'get_name_with_info');
                    $infos['name'] = $hasinfomethod ? $class::get_name_with_info() : $class::get_name();
                    $infos['isdeprecated'] = method_exists($class, 'is_deprecated') ? $class::is_deprecated() : false;
                }
            }
        } catch (\Exception $e) {
            // Capture all exceptions to ensure we're not breaking the page, and resetting the debugging parameters.
            $infos = $infos; // Make the codechecker happy.
        }

        // Restore debugging.
        $CFG->debugusers = $debugusers;
        set_debugging($debuglevel, $debugdisplay);

        return $infos;
    }


    /**
     * Get the events list from a plugin.
     *
     * From 3.1 we could be using core_component::get_component_classes_in_namespace().
     *
     * @param string $component The plugin's component name.
     * @return array
     */
    protected static function get_events_list_from_plugin($component) {
        $directory = core_component::get_component_directory($component);
        $plugindirectory = $directory . '/classes/event';
        if (!is_dir($plugindirectory)) {
            return [];
        }

        // Get the plugin's events.
        $eventclasses = static::get_event_classes_from_component($component);

        $pluginmanager = core_plugin_manager::instance();
        $plugininfo = $pluginmanager->get_plugin_info($component);

        // Reduce to the participating, non-deprecated event.
        $events = array_reduce($eventclasses, function($carry, $class) use ($plugininfo) {
            $infos = self::get_event_infos($class);
            if (empty($infos)) {
                // Skip rare case where infos aren't found.
                return $carry;
            } else if ($infos['edulevel'] != \core\event\base::LEVEL_PARTICIPATING) {
                // Skip events that are not of level 'participating'.
                return $carry;
            }

            $carry[$infos['eventname']] = get_string('colon', 'block_xp', [
                'a' => $plugininfo->displayname,
                'b' => $infos['name'],
            ]);
            return $carry;
        }, []);

        // Order alphabetically.
        core_collator::asort($events, core_collator::SORT_NATURAL);

        return $events;
    }

    /**
     * Get events from plugin type.
     *
     * @param string $plugintype Plugin type.
     * @return array
     */
    protected static function get_events_list_from_plugintype($plugintype) {
        $list = [];

        // Loop over each plugin of the type.
        $pluginlist = core_component::get_plugin_list($plugintype);
        foreach ($pluginlist as $plugin => $directory) {
            $component = $plugintype . '_' . $plugin;
            $events = self::get_events_list_from_plugin($component);

            // If we found events for this plugin, we add them to the list.
            if (!empty($events)) {
                $pluginmanager = core_plugin_manager::instance();
                $plugininfo = $pluginmanager->get_plugin_info($component);
                $list[] = [$plugininfo->displayname => $events];
            }
        }

        return $list;
    }

}
