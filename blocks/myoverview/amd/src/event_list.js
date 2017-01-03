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
 * Javascript to load and render the list of calendar events for a
 * given day range.
 *
 * @module     block_myoverview/event_list
 * @package    block_myoverview
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/notification', 'core/templates',
        'block_myoverview/calendar_events_repository'],
        function($, Notification, Templates, CalendarEventsRepository) {

    return {
        /**
         * Retrieve a list of calendar events, render and append them to the end of the
         * existing list. The events will be loaded based on the set of data attributes
         * on the root element.
         *
         * @method load
         * @param {object} The root element of the event list
         * @param {promise} A jquery promise
         */
        load: function(root) {
            root = $(root);
            var start = +root.attr('data-start-day'),
                end = +root.attr('data-end-day'),
                limit = +root.attr('data-limit'),
                offset = +root.attr('data-offset');

            // Don't load twice.
            if (root.hasClass('loading')) {
                return $.Deferred().resolve();
            }

            root.addClass('loading');

            // Request data from the server.
            return CalendarEventsRepository.query_for_user_by_days(
                start, end, limit, offset
            ).then(function(calendarEvents) {
                // Increment the offset by the number of events returned.
                root.attr('data-offset', offset + calendarEvents.length);

                if (calendarEvents.length) {
                    // Render the events.
                    return Templates.render(
                        'block_myoverview/event-list-items',
                        {events: calendarEvents}
                    ).done(function(html, js) {
                        Templates.appendNodeContents(root, html, js);
                    });
                }
            }).fail(
                Notification.exception
            ).always(function() {
                root.removeClass('loading');
            });
        }
    };
});
