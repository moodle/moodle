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
 * Contains event class for displaying a calendar event.
 *
 * @package   core_calendar
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/calendar/lib.php");

use \core_calendar\local\event\entities\action_event_interface;
use \core_calendar\local\event\container;
use \core_course\external\course_summary_exporter;
use \renderer_base;

/**
 * Class for displaying a calendar event.
 *
 * @package   core_calendar
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_exporter extends event_exporter_base {

    /**
     * Return the list of additional properties.
     *
     * @return array
     */
    protected static function define_other_properties() {
        $values = parent::define_other_properties();

        $values['url'] = ['type' => PARAM_URL];
        $values['action'] = [
            'type' => event_action_exporter::read_properties_definition(),
            'optional' => true,
        ];

        return $values;
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {
        $values = parent::get_other_values($output);

        global $CFG;
        require_once($CFG->dirroot.'/course/lib.php');

        $event = $this->event;
        $context = $this->related['context'];
        if ($moduleproxy = $event->get_course_module()) {
            $modulename = $moduleproxy->get('modname');
            $moduleid = $moduleproxy->get('id');
            $url = new \moodle_url(sprintf('/mod/%s/view.php', $modulename), ['id' => $moduleid]);

            // Build edit event url for action events.
            $params = array('update' => $moduleid, 'return' => true, 'sesskey' => sesskey());
            $editurl = new \moodle_url('/course/mod.php', $params);
            $values['editurl'] = $editurl->out(false);
        } else if ($event->get_type() == 'category') {
            $url = $event->get_category()->get_proxied_instance()->get_view_link();
        } else if ($event->get_type() == 'course') {
            $url = \course_get_url($this->related['course'] ?: SITEID);
        } else {
            // TODO MDL-58866 We do not have any way to find urls for events outside of course modules.
            $url = \course_get_url($this->related['course'] ?: SITEID);
        }
        $values['url'] = $url->out(false);

        // Override default formatted time to make sure the date portion of the time is always rendered.
        $legacyevent = container::get_event_mapper()->from_event_to_legacy_event($event);
        $values['formattedtime'] = calendar_format_event_time($legacyevent, time(), null, false);

        if ($event instanceof action_event_interface) {
            $actionrelated = [
                'context' => $context,
                'event' => $event
            ];
            $actionexporter = new event_action_exporter($event->get_action(), $actionrelated);
            $values['action'] = $actionexporter->export($output);
        }



        return $values;
    }
}
