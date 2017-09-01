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
 * This module is the highest level module for the calendar. It is
 * responsible for initialising all of the components required for
 * the calendar to run. It also coordinates the interaction between
 * components by listening for and responding to different events
 * triggered within the calendar UI.
 *
 * @module     core_calendar/calendar
 * @package    core_calendar
 * @copyright  2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'core_calendar/selectors',
    'core_calendar/events',
    'core_calendar/view_manager',
],
function(
    $,
    CalendarSelectors,
    CalendarEvents,
    CalendarViewManager
) {

    var registerEventListeners = function(root) {
        $('body').on(CalendarEvents.filterChanged, function(e, data) {
            var daysWithEvent = root.find(CalendarSelectors.eventType[data.type]);

            daysWithEvent.toggleClass('calendar_event_' + data.type, !data.hidden);
        });
    };

    return {
        init: function(root) {
            root = $(root);

            CalendarViewManager.init(root);
            registerEventListeners(root);
        }
    };
});
