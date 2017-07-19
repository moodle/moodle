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

use \core\external\exporter;
use \core_calendar\local\event\entities\event_interface;
use \core_calendar\local\event\entities\action_event_interface;
use \core_course\external\course_summary_exporter;
use \renderer_base;

/**
 * Class for displaying a calendar event.
 *
 * @package   core_calendar
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class calendar_event_exporter extends event_exporter_base {

    /**
     * Return the list of additional properties.
     *
     * @return array
     */
    protected static function define_other_properties() {
        return [
            'url' => ['type' => PARAM_URL],
            'icon' => [
                'type' => event_icon_exporter::read_properties_definition(),
            ],
            'course' => [
                'type' => course_summary_exporter::read_properties_definition(),
                'optional' => true,
            ]
        ];
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {
        $values = parent::get_other_values($output);

        $eventid = $this->event->get_id();

        $url = new \moodle_url($this->related['daylink'], [], "event_{$eventid}");
        $values['url'] = $url->out(false);

        return $values;
    }

    /**
     * Returns a list of objects that are related.
     *
     * @return array
     */
    protected static function define_related() {
        $related = parent::define_related();
        $related['daylink'] = \moodle_url::class;

        return $related;
    }
}
