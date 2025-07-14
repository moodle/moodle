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
 * Contains event class for displaying the day on month view.
 *
 * @package   core_calendar
 * @copyright 2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar\external;

defined('MOODLE_INTERNAL') || die();

use renderer_base;

/**
 * Class for displaying the day on month view.
 *
 * @package   core_calendar
 * @copyright 2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class week_day_exporter extends day_exporter {

    /**
     * Constructor.
     *
     * @param \calendar_information $calendar The calendar information for the period being displayed
     * @param mixed $data Either an stdClass or an array of values.
     * @param array $related Related objects.
     */
    public function __construct(\calendar_information $calendar, $data, $related) {
        parent::__construct($calendar, $data, $related);
        // Fix the url for today to be based on the today timestamp
        // rather than the calendar_information time set in the parent
        // constructor.
        $this->url->param('time', $this->data[0]);
    }

    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_properties() {
        $return = parent::define_properties();
        $return = array_merge($return, [
            // These are additional params.
            'istoday' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
            'isweekend' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
        ]);

        return $return;
    }
    /**
     * Return the list of additional properties.
     *
     * @return array
     */
    protected static function define_other_properties() {
        $return = parent::define_other_properties();
        $return = array_merge($return, [
            'popovertitle' => [
                'type' => PARAM_RAW,
                'default' => '',
            ],
            'daytitle' => [
                'type' => PARAM_RAW,
            ]
        ]);

        return $return;
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {
        $return = parent::get_other_values($output);

        if ($popovertitle = $this->get_popover_title()) {
            $return['popovertitle'] = $popovertitle;
        }

        $return['daytitle'] = $this->get_day_title();

        return $return;
    }

    /**
     * Returns a list of objects that are related.
     *
     * @return array
     */
    protected static function define_related() {
        return [
            'events' => '\core_calendar\local\event\entities\event_interface[]',
            'cache' => '\core_calendar\external\events_related_objects_cache',
            'type' => '\core_calendar\type_base',
        ];
    }

    /**
     * Get the title for this popover.
     *
     * @return string
     */
    protected function get_popover_title() {
        $title = null;

        $userdate = userdate($this->data[0], get_string('strftimedayshort'));
        if (count($this->related['events'])) {
            $title = get_string('eventsfor', 'calendar', $userdate);
        } else if ($this->data['istoday']) {
            $title = $userdate;
        }

        if ($this->data['istoday']) {
            $title = get_string('todayplustitle', 'calendar', $userdate);
        }

        return $title;
    }

    /**
     * Get the title for this day.
     *
     * @return string
     */
    protected function get_day_title(): string {
        $userdate = userdate($this->data[0], get_string('strftimedayshort'));

        $numevents = count($this->related['events']);
        if ($numevents == 1) {
            $title = get_string('dayeventsone', 'calendar', $userdate);
        } else if ($numevents) {
            $title = get_string('dayeventsmany', 'calendar', ['num' => $numevents, 'day' => $userdate]);
        } else {
            $title = get_string('dayeventsnone', 'calendar', $userdate);
        }

        return $title;
    }
}
