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
    /* @var {int|null} minTimestart The earliest valid timestart */
    var minTimestart = null;
    /* @var {int|null} maxTimestart The latest valid tiemstart */
    var maxTimestart = null;
    /* @var {string|null} minError Error message for min timestamp violation */
    var minError = null;
    /* @var {string|null} maxError Error message for max timestamp violation */
    var maxError = null;

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
     * Store the minimum timestart valid for an event being dragged.
     *
     * @param {int} timestamp The unix timstamp
     */
    var setMinTimestart = function(timestamp) {
        minTimestart = timestamp;
    };

    /**
     * Get the minimum valid timestart.
     *
     * @return {int|null}
     */
    var getMinTimestart = function() {
        return minTimestart;
    };

    /**
     * Check if a minimum timestamp is set.
     *
     * @return {bool}
     */
    var hasMinTimestart = function() {
        return minTimestart !== null;
    };

    /**
     * Store the maximum timestart valid for an event being dragged.
     *
     * @param {int} timestamp The unix timstamp
     */
    var setMaxTimestart = function(timestamp) {
        maxTimestart = timestamp;
    };

    /**
     * Get the maximum valid timestart.
     *
     * @return {int|null}
     */
    var getMaxTimestart = function() {
        return maxTimestart;
    };

    /**
     * Check if a maximum timestamp is set.
     *
     * @return {bool}
     */
    var hasMaxTimestart = function() {
        return maxTimestart !== null;
    };

    /**
     * Store the error string to display if trying to drag an event
     * earlier than the minimum allowed date.
     *
     * @param {string} message The error message
     */
    var setMinError = function(message) {
        minError = message;
    };

    /**
     * Get the error message for a minimum time start violation.
     *
     * @return {string|null}
     */
    var getMinError = function() {
        return minError;
    };

    /**
     * Store the error string to display if trying to drag an event
     * later than the maximum allowed date.
     *
     * @param {string} message The error message
     */
    var setMaxError = function(message) {
        maxError = message;
    };

    /**
     * Get the error message for a maximum time start violation.
     *
     * @return {string|null}
     */
    var getMaxError = function() {
        return maxError;
    };

    /**
     * Reset all of the stored values.
     */
    var clearAll = function() {
        setEventId(null);
        setDurationDays(null);
        setMinTimestart(null);
        setMaxTimestart(null);
        setMinError(null);
        setMaxError(null);
    };

    return {
        setEventId: setEventId,
        getEventId: getEventId,
        hasEventId: hasEventId,
        setDurationDays: setDurationDays,
        getDurationDays: getDurationDays,
        setMinTimestart: setMinTimestart,
        getMinTimestart: getMinTimestart,
        hasMinTimestart: hasMinTimestart,
        setMaxTimestart: setMaxTimestart,
        getMaxTimestart: getMaxTimestart,
        hasMaxTimestart: hasMaxTimestart,
        setMinError: setMinError,
        getMinError: getMinError,
        setMaxError: setMaxError,
        getMaxError: getMaxError,
        clearAll: clearAll
    };
});
