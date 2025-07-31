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
 * Class for returning system event information.
 *
 * @package   report_eventlist
 * @copyright 2014 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_eventlist_list_generator {

    /**
     * Convenience method. Returns all of the core events either with or without details.
     *
     * @param bool $detail True will return details, but no abstract classes, False will return all events, but no details.
     * @return array All events.
     */
    public static function get_all_events_list($detail = true) {
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
        ];

        $eventinformation = [];

        $events = core_component::get_component_classes_in_namespace(null, 'event');
        foreach (array_keys($events) as $event) {
            // We need to filter all classes that extend event base, or the base class itself.
            if (is_a($event, \core\event\base::class, true) && !in_array($event, $eventsignore)) {
                if ($detail) {
                    $reflectionclass = new ReflectionClass($event);
                    if (!$reflectionclass->isAbstract()) {
                        $eventinformation = self::format_data($eventinformation, "\\{$event}");
                    }
                } else {
                    $parts = explode('\\', $event);
                    $eventinformation["\\{$event}"] = array_shift($parts);
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
     * @deprecated since 4.0 use {@see get_all_events_list} instead
     */
    #[\core\attribute\deprecated('::get_all_events_list', since: '4.0', mdl: 'MDL-72498', final: true)]
    public static function get_core_events_list() {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);
    }

    /**
     * Returns the appropriate string for the CRUD character.
     *
     * @param string $crudcharacter The CRUD character.
     * @return string get_string for the specific CRUD character.
     */
    public static function get_crud_string($crudcharacter) {
        switch ($crudcharacter) {
            case 'c':
                return get_string('create', 'report_eventlist');
                break;

            case 'u':
                return get_string('update', 'report_eventlist');
                break;

            case 'd':
                return get_string('delete', 'report_eventlist');
                break;

            case 'r':
            default:
                return get_string('read', 'report_eventlist');
                break;
        }
    }

    /**
     * Returns the appropriate string for the event education level.
     *
     * @param int $edulevel Takes either the edulevel constant or string.
     * @return string get_string for the specific education level.
     */
    public static function get_edulevel_string($edulevel) {
        switch ($edulevel) {
            case \core\event\base::LEVEL_PARTICIPATING:
                return get_string('participating', 'report_eventlist');
                break;

            case \core\event\base::LEVEL_TEACHING:
                return get_string('teaching', 'report_eventlist');
                break;

            case \core\event\base::LEVEL_OTHER:
            default:
                return get_string('other', 'report_eventlist');
                break;
        }
    }

    /**
     * @deprecated since 4.0 use {@see get_all_events_list} instead
     */
    #[\core\attribute\deprecated('::get_all_events_list', since: '4.0', mdl: 'MDL-72498', final: true)]
    public static function get_non_core_event_list() {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);
    }

    /**
     * Get the full list of observers for the system.
     *
     * @return array An array of observers in the system.
     */
    public static function get_observer_list() {
        $events = \core\event\manager::get_all_observers();
        foreach ($events as $key => $observers) {
            foreach ($observers as $observerskey => $observer) {
                $events[$key][$observerskey]->parentplugin =
                        \core_plugin_manager::instance()->get_parent_of_subplugin($observer->plugintype);
            }
        }
        return $events;
    }

    /**
     * Returns the event data list section with url links and other formatting.
     *
     * @param array $eventdata The event data list section.
     * @param string $eventfullpath Full path to the events for this plugin / subplugin.
     * @return array The event data list section with additional formatting.
     */
    private static function format_data($eventdata, $eventfullpath) {
        // Get general event information.
        $eventdata[$eventfullpath] = $eventfullpath::get_static_info();
        // Create a link for further event detail.
        $url = new \moodle_url('eventdetail.php', array('eventname' => $eventfullpath));
        $link = \html_writer::link($url, $eventfullpath::get_name_with_info());
        $eventdata[$eventfullpath]['fulleventname'] = \html_writer::span($link);
        $eventdata[$eventfullpath]['fulleventname'] .= \html_writer::empty_tag('br');
        $eventdata[$eventfullpath]['fulleventname'] .= \html_writer::span($eventdata[$eventfullpath]['eventname'],
                'report-eventlist-name');

        $eventdata[$eventfullpath]['crud'] = self::get_crud_string($eventdata[$eventfullpath]['crud']);
        $eventdata[$eventfullpath]['edulevel'] = self::get_edulevel_string($eventdata[$eventfullpath]['edulevel']);

        // Mess around getting since information.
        $ref = new \ReflectionClass($eventdata[$eventfullpath]['eventname']);
        $eventdocbloc = $ref->getDocComment();
        $sincepattern = "/since\s*Moodle\s([0-9]+.[0-9]+)/i";
        preg_match($sincepattern, $eventdocbloc, $result);
        if (isset($result[1])) {
            $eventdata[$eventfullpath]['since'] = $result[1];
        } else {
            $eventdata[$eventfullpath]['since'] = null;
        }

        // Human readable plugin information to go with the component.
        $pluginstring = explode('\\', $eventfullpath);
        if ($pluginstring[1] !== 'core') {
            $component = $eventdata[$eventfullpath]['component'];
            $manager = get_string_manager();
            if ($manager->string_exists('pluginname', $pluginstring[1])) {
                $eventdata[$eventfullpath]['component'] = \html_writer::span(get_string('pluginname', $pluginstring[1]));
            }
        }

        // Raw event data to be used to sort the "Event name" column.
        $eventdata[$eventfullpath]['raweventname'] = $eventfullpath::get_name_with_info() . ' ' . $eventdata[$eventfullpath]['eventname'];

        // Unset information that is not currently required.
        unset($eventdata[$eventfullpath]['action']);
        unset($eventdata[$eventfullpath]['target']);
        return $eventdata;
    }
}
