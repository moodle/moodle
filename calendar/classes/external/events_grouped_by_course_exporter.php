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
 * Contains event class for displaying a list of calendar events grouped by course id.
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
 * Class for displaying a list of calendar events grouped by course id.
 *
 * This class uses the events relateds cache in order to get the related
 * data for exporting an event without having to naively hit the database
 * for each event.
 *
 * @package   core_calendar
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class events_grouped_by_course_exporter extends exporter {

    /**
     * @var array $events An array of event_interface objects
     *                    grouped and index by course id.
     */
    protected $eventsbycourse;

    /**
     * Constructor.
     *
     * @param array $eventsbycourse An array of event_interface objects
     * @param array $related An array of related objects
     */
    public function __construct(array $eventsbycourse, $related = []) {
        $this->eventsbycourse = $eventsbycourse;
        parent::__construct([], $related);
    }

    /**
     * Return the list of additional properties.
     *
     * @return array
     */
    protected static function define_other_properties() {
        return [
            'groupedbycourse' => [
                'type' => events_same_course_exporter::read_properties_definition(),
                'multiple' => true,
                'default' => [],
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

        foreach ($this->eventsbycourse as $courseid => $events) {
            $eventsexporter = new events_same_course_exporter(
                $courseid, $events, ['cache' => $cache]);
            $return['groupedbycourse'][] = $eventsexporter->export($output);
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
