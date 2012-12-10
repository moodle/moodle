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
 * External calendar API
 *
 * @package    core_calendar
 * @category   external
 * @copyright  2012 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.5
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

/**
 * Calendar external functions
 *
 * @package    core_calendar
 * @category   external
 * @copyright  2012 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.5
 */
class core_calendar_external extends external_api {


    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function delete_calendar_events_parameters() {
        return new external_function_parameters(
                array('events' => new external_multiple_structure(
                        new external_single_structure(
                                array(
                                        'eventid' => new external_value(PARAM_INT, 'Event ID', VALUE_REQUIRED, '', NULL_NOT_ALLOWED),
                                        'repeat'  => new external_value(PARAM_BOOL, 'Delete comeplete series if repeated event')
                                ), 'List of events to delete'
                        )
                    )
                )
        );
    }

    /**
     * Delete Calendar events
     *
     * @param array $eventids A list of event ids with repeat flag to delete
     * @return null
     * @since Moodle 2.5
     */
    public static function delete_calendar_events($events) {
        global $CFG, $DB;
        require_once($CFG->dirroot."/calendar/lib.php");

        // Parameter validation.
        $params = self::validate_parameters(self:: delete_calendar_events_parameters(), array('events' => $events));

        $transaction = $DB->start_delegated_transaction();

        foreach ($params['events'] as $event) {
            $eventobj = calendar_event::load($event['eventid']);

            // Let's check if the user is allowed to delete an event.
            if (!calendar_edit_event_allowed($eventobj)) {
                throw new moodle_exception("nopermissions");
            }
            // Time to do the magic.
            $eventobj->delete($event['repeat']);
        }

        // Everything done smoothly, let's commit.
        $transaction->allow_commit();

        return null;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.5
     */
    public static function  delete_calendar_events_returns() {
        return null;
    }
}
