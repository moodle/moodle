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
 * A javascript module to store calendar drag and drop data.
 *
 * This module is unfortunately required because of the limitations
 * of the HTML5 drag and drop API and it's ability to provide data
 * between the different stages of the drag/drop lifecycle.
 *
 * @module     core_calendar/drag_drop_data_store
 * @package    core_calendar
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([], function() {
    /* @var {int|null} eventId The id of the event being dragged */
    var eventId = null;
    /* @var {int|null} durationDays How many days the event spans */
    var durationDays = null;

    /**
     * Store the id of the event being dragged.
     *
     * @param {int} id The event id
     */
    var setEventId = function(id) {
        eventId = id;
    };

    /**
     * Get the stored event id.
     *
     * @return {int|null}
     */
    var getEventId = function() {
        return eventId;
    };

    /**
     * Check if the store has an event id.
     *
     * @return {bool}
     */
    var hasEventId = function() {
        return eventId !== null;
    };

    /**
     * Store the duration (in days) of the event being dragged.
     *
     * @param {int} days Number of days the event spans
     */
    var setDurationDays = function(days) {
        durationDays = days;
    };

    /**
     * Get the stored number of days.
     *
     * @return {int|null}
     */
    var getDurationDays = function() {
        return durationDays;
    };

    /**
     * Reset all of the stored values.
     */
    var clearAll = function() {
        setEventId(null);
        setDurationDays(null);
    };

    return {
        setEventId: setEventId,
        getEventId: getEventId,
        hasEventId: hasEventId,
        setDurationDays: setDurationDays,
        getDurationDays: getDurationDays,
        clearAll: clearAll
    };
});
