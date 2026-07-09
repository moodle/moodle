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

namespace block_timeline\external;

use core_calendar\external\events_related_objects_cache;
use core_calendar\external\event_exporter;
use core_calendar\local\api as local_api;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * External function to fetch timeline events for the dates view.
 *
 * @package    block_timeline
 * @copyright  2026 Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_timeline_events extends external_api {
    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'timesortfrom' => new external_value(PARAM_INT, 'Events on or after this timestamp', VALUE_DEFAULT, 0),
            'timesortto'   => new external_value(PARAM_INT, 'Events on or before this timestamp', VALUE_DEFAULT, null),
            'aftereventid' => new external_value(PARAM_INT, 'Return events after this event id', VALUE_DEFAULT, 0),
            'limitnum'     => new external_value(PARAM_INT, 'Maximum number of events to return (1–50)', VALUE_DEFAULT, 20),
            'searchvalue'  => new external_value(PARAM_RAW, 'Optional search string', VALUE_DEFAULT, null),
        ]);
    }

    /**
     * Fetch calendar action events for the timeline dates view.
     *
     * @param int $timesortfrom Events on or after this timestamp.
     * @param int|null $timesortto Events on or before this timestamp.
     * @param int $aftereventid Return events after this event id.
     * @param int $limitnum Maximum number of events to return.
     * @param string|null $searchvalue Optional search string.
     * @return array
     */
    public static function execute(
        int $timesortfrom = 0,
        ?int $timesortto = null,
        int $aftereventid = 0,
        int $limitnum = 20,
        ?string $searchvalue = null
    ): array {
        global $PAGE, $USER;

        $params = self::validate_parameters(self::execute_parameters(), [
            'timesortfrom' => $timesortfrom,
            'timesortto'   => $timesortto,
            'aftereventid' => $aftereventid,
            'limitnum'     => $limitnum,
            'searchvalue'  => $searchvalue,
        ]);

        if (!isloggedin() || isguestuser()) {
            throw new \require_login_exception('You must be logged in to view timeline events.');
        }
        $context = \context_user::instance($USER->id);
        self::validate_context($context);

        $aftereventid = empty($params['aftereventid']) ? null : $params['aftereventid'];
        $searchvalue  = isset($params['searchvalue']) ? clean_param($params['searchvalue'], PARAM_TEXT) : null;

        $events = local_api::get_action_events_by_timesort(
            $params['timesortfrom'],
            $params['timesortto'],
            $aftereventid,
            $params['limitnum'],
            true,
            $USER,
            $searchvalue
        );

        $renderer = $PAGE->get_renderer('core_calendar');
        $cache    = new events_related_objects_cache($events);

        $exported = [];
        foreach ($events as $event) {
            $relatedobjects = [
                'context' => $cache->get_context($event),
                'course'  => $cache->get_course($event),
            ];
            $exporter = new event_exporter($event, $relatedobjects);
            $exportedevent = (array) $exporter->export($renderer);
            $exportedevent['formattedday'] = userdate(
                $exportedevent['timeusermidnight'],
                get_string('strftimedaydate', 'langconfig')
            );
            $exported[] = $exportedevent;
        }

        return ['events' => $exported];
    }

    /**
     * Returns description of method result value.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        $eventstructure = event_exporter::get_read_structure();
        $eventstructure->keys['formattedday'] = new external_value(PARAM_TEXT, 'Day formatted for display using the site language');
        return new external_single_structure([
            'events' => new external_multiple_structure(
                $eventstructure,
                'List of calendar action events'
            ),
        ]);
    }
}
