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
 * Contains event class for displaying a list of calendar events.
 *
 * @package   core_calendar
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\external;

defined('MOODLE_INTERNAL') || die();

use \core\external\exporter;
use \renderer_base;

/**
 * Class for displaying a list of calendar events.
 *
 * This class uses the events relateds cache in order to get the related
 * data for exporting an event without having to naively hit the database
 * for each event.
 *
 * @package   core_calendar
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class events_exporter extends exporter {

    /**
     * @var array $events An array of event_interface objects.
     */
    protected $events;

    /**
     * Constructor.
     *
     * @param array $events An array of event_interface objects
     * @param array $related An array of related objects
     */
    public function __construct(array $events, $related = []) {
        $this->events = $events;
        parent::__construct([], $related);
    }

    /**
     * Return the list of additional properties.
     *
     * @return array
     */
    protected static function define_other_properties() {
        return [
            'events' => [
                'type' => event_exporter::read_properties_definition(),
                'multiple' => true,
            ],
            'firstid' => [
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'lastid' => [
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
        ];
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {
        $return = [];
        $cache = $this->related['cache'];

        $return['events'] = array_map(function($event) use ($cache, $output) {
            $context = $cache->get_context($event);
            $course = $cache->get_course($event);
            $exporter = new event_exporter($event, ['context' => $context, 'course' => $course]);

            return $exporter->export($output);
        }, $this->events);

        if ($count = count($return['events'])) {
            $return['firstid'] = $return['events'][0]->id;
            $return['lastid'] = $return['events'][$count - 1]->id;
        }

        return $return;
    }

    /**
     * Returns a list of objects that are related.
     *
     * @return array
     */
    protected static function define_related() {
        return [
            'cache' => 'core_calendar\external\events_related_objects_cache',
        ];
    }
}
