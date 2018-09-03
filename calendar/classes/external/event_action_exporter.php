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
 * Contains event class for displaying a calendar event's action.
 *
 * @package   core_calendar
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\external;

defined('MOODLE_INTERNAL') || die();

use core\external\exporter;
use core_calendar\local\event\entities\action_interface;
use core_calendar\local\event\container;
use renderer_base;

/**
 * Class for displaying a calendar event's action.
 *
 * @package   core_calendar
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_action_exporter extends exporter {

    /**
     * Constructor.
     *
     * @param action_interface $action The action object.
     * @param array $related Related data.
     */
    public function __construct(action_interface $action, $related = []) {
        $data = new \stdClass();
        $data->name = $action->get_name();
        $data->url = $action->get_url()->out(true);
        $data->itemcount = $action->get_item_count();
        $data->actionable = $action->is_actionable();

        parent::__construct($data, $related);
    }

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'name' => ['type' => PARAM_TEXT],
            'url' => ['type' => PARAM_URL],
            'itemcount' => ['type' => PARAM_INT],
            'actionable' => ['type' => PARAM_BOOL]
        ];
    }

    /**
     * Return the list of additional properties.
     *
     * @return array
     */
    protected static function define_other_properties() {
        return [
            'showitemcount' => ['type' => PARAM_BOOL, 'default' => false]
        ];
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {
        $event = $this->related['event'];

        if (!$event->get_course_module()) {
            // TODO MDL-58866 Only activity modules currently support this callback.
            return ['showitemcount' => false];
        }
        $modulename = $event->get_course_module()->get('modname');
        $component = 'mod_' . $modulename;
        $showitemcountcallback = 'core_calendar_event_action_shows_item_count';
        $mapper = container::get_event_mapper();
        $calevent = $mapper->from_event_to_legacy_event($event);
        $params = [$calevent, $this->data->itemcount];
        $showitemcount = component_callback($component, $showitemcountcallback, $params, false);

        // Prepare other values data.
        $data = [
            'showitemcount' => $showitemcount
        ];
        return $data;
    }

    /**
     * Returns a list of objects that are related.
     *
     * @return array
     */
    protected static function define_related() {
        return [
            'context' => 'context',
            'event' => '\\core_calendar\\local\\event\\entities\\event_interface'
        ];
    }
}
