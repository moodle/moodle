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

use \core\external\exporter;
use \core_calendar\local\event\container;
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
class event_exporter_base extends exporter {

    /**
     * @var event_interface $event
     */
    protected $event;

    /**
     * Constructor.
     *
     * @param event_interface $event
     * @param array $related The related data.
     */
    public function __construct(event_interface $event, $related = []) {
        $this->event = $event;

        $starttimestamp = $event->get_times()->get_start_time()->getTimestamp();
        $endtimestamp = $event->get_times()->get_end_time()->getTimestamp();
        $groupid = $event->get_group() ? $event->get_group()->get('id') : null;
        $userid = $event->get_user() ? $event->get_user()->get('id') : null;

        $data = new \stdClass();
        $data->id = $event->get_id();
        $data->name = $event->get_name();
        $data->description = $event->get_description()->get_value();
        $data->descriptionformat = $event->get_description()->get_format();
        $data->groupid = $groupid;
        $data->userid = $userid;
        $data->eventtype = $event->get_type();
        $data->timestart = $starttimestamp;
        $data->timeduration = $endtimestamp - $starttimestamp;
        $data->timesort = $event->get_times()->get_sort_time()->getTimestamp();
        $data->visible = $event->is_visible() ? 1 : 0;
        $data->timemodified = $event->get_times()->get_modified_time()->getTimestamp();

        if ($repeats = $event->get_repeats()) {
            $data->repeatid = $repeats->get_id();
        }

        if ($cm = $event->get_course_module()) {
            $data->modulename = $cm->get('modname');
            $data->instance = $cm->get('id');
        }

        parent::__construct($data, $related);
    }

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => ['type' => PARAM_INT],
            'name' => ['type' => PARAM_TEXT],
            'description' => [
                'type' => PARAM_RAW,
                'optional' => true,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'descriptionformat' => [
                'type' => PARAM_INT,
                'optional' => true,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'groupid' => [
                'type' => PARAM_INT,
                'optional' => true,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'userid' => [
                'type' => PARAM_INT,
                'optional' => true,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'repeatid' => [
                'type' => PARAM_INT,
                'optional' => true,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'modulename' => [
                'type' => PARAM_TEXT,
                'optional' => true,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'instance' => [
                'type' => PARAM_INT,
                'optional' => true,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'eventtype' => ['type' => PARAM_TEXT],
            'timestart' => ['type' => PARAM_INT],
            'timeduration' => ['type' => PARAM_INT],
            'timesort' => ['type' => PARAM_INT],
            'visible' => ['type' => PARAM_INT],
            'timemodified' => ['type' => PARAM_INT],
        ];
    }

    /**
     * Return the list of additional properties.
     *
     * @return array
     */
    protected static function define_other_properties() {
        return [
            'icon' => [
                'type' => event_icon_exporter::read_properties_definition(),
            ],
            'course' => [
                'type' => course_summary_exporter::read_properties_definition(),
                'optional' => true,
            ],
            'canedit' => [
                'type' => PARAM_BOOL
            ],
            'candelete' => [
                'type' => PARAM_BOOL
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
        $values = [];
        $event = $this->event;
        $legacyevent = container::get_event_mapper()->from_event_to_legacy_event($event);
        $context = $this->related['context'];
        $timesort = $event->get_times()->get_sort_time()->getTimestamp();
        $iconexporter = new event_icon_exporter($event, ['context' => $context]);

        $values['icon'] = $iconexporter->export($output);

        if ($course = $this->related['course']) {
            $coursesummaryexporter = new course_summary_exporter($course, ['context' => $context]);
            $values['course'] = $coursesummaryexporter->export($output);
        }

        $values['canedit'] = calendar_edit_event_allowed($legacyevent, true);
        $values['candelete'] = calendar_delete_event_allowed($legacyevent);

        return $values;
    }

    /**
     * Returns a list of objects that are related.
     *
     * @return array
     */
    protected static function define_related() {
        return [
            'context' => 'context',
            'course' => 'stdClass?',
        ];
    }
}
