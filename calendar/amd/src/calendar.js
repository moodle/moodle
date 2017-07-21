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
 * A javascript module to calendar events.
 *
 * @module     core_calendar/calendar
 * @package    core_calendar
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/str', 'core/templates', 'core/notification', 'core/custom_interaction_events',
        'core/modal_factory', 'core_calendar/summary_modal', 'core/modal_events', 'core_calendar/calendar_repository'],
    function($, Ajax, Str, Templates, Notification, CustomEvents, ModalFactory, SummaryModal, ModalEvents, CalendarRepository) {

        var SELECTORS = {
            ROOT: "[data-region='calendar']",
            EVENT_LINK: "[data-action='view-event']",
        };

        /**
         * Get the event type lang string.
         *
         * @param {String} eventType The event type.
         * @return {promise} The lang string promise.
         */
        var getEventType = function(eventType) {
            var lang = 'type' + eventType;
            return Str.get_string(lang, 'core_calendar').then(function(langStr) {
                return langStr;
            });
        };

        /**
         * Get the event source.
         *
         * @param {Object} subscription The event subscription object.
         * @return {promise} The lang string promise.
         */
        var getEventSource = function(subscription) {
            return Str.get_string('subsource', 'core_calendar', subscription).then(function(langStr) {
                if (subscription.url) {
                    return '<a href="' + subscription.url + '">' + langStr + '</a>';
                }
                return langStr;
            });
        };

        /**
         * Render the event summary modal.
         *
         * @param {Number} eventId The calendar event id.
         */
        var renderEventSummaryModal = function(eventId) {
            // Calendar repository promise.
            CalendarRepository.getEventById(eventId).then(function(getEventResponse) {
                if (!getEventResponse.event) {
                    throw new Error('Error encountered while trying to fetch calendar event with ID: ' + eventId);
                }
                var eventData = getEventResponse.event;
                var eventTypePromise = getEventType(eventData.eventtype);

                // If the calendar event has event source, get the source's language string/link.
                if (eventData.displayeventsource) {
                    eventData.subscription = JSON.parse(eventData.subscription);
                    var eventSourceParams = {
                        url: eventData.subscription.url,
                        name: eventData.subscription.name
                    };
                    var eventSourcePromise = getEventSource(eventSourceParams);

                    // Return event data with event type and event source info.
                    return $.when(eventTypePromise, eventSourcePromise).then(function(eventType, eventSource) {
                        eventData.eventtype = eventType;
                        eventData.source = eventSource;
                        return eventData;
                    });
                }

                // Return event data with event type info.
                return eventTypePromise.then(function(eventType) {
                    eventData.eventtype = eventType;
                    return eventData;
                });

            }).then(function(eventData) {
                // Build the modal parameters from the event data.
                var modalParams = {
                    title: eventData.name,
                    type: SummaryModal.TYPE,
                    body: Templates.render('core_calendar/event_summary_body', eventData)
                };
                if (!eventData.caneditevent) {
                    modalParams.footer = '';
                }
                // Create the modal.
                return ModalFactory.create(modalParams);

            }).done(function(modal) {
                // Handle hidden event.
                modal.getRoot().on(ModalEvents.hidden, function() {
                    // Destroy when hidden.
                    modal.destroy();
                });

                // Finally, render the modal!
                modal.show();

            }).fail(Notification.exception);
        };

        /**
         * Register event listeners for the module.
         */
        var registerEventListeners = function() {
            // Bind click events to event links.
            $(SELECTORS.EVENT_LINK).click(function(e) {
                e.preventDefault();
                var eventId = $(this).attr('data-event-id');
                renderEventSummaryModal(eventId);
            });
        };

        return {
            init: function() {
                registerEventListeners();
            }
        };
    });
