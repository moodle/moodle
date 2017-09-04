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
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
            'jquery',
            'core/ajax',
            'core/str',
            'core/templates',
            'core/notification',
            'core/custom_interaction_events',
            'core/modal_events',
            'core/modal_factory',
            'core_calendar/modal_event_form',
            'core_calendar/summary_modal',
            'core_calendar/repository',
            'core_calendar/events',
            'core_calendar/view_manager'
        ],
        function(
            $,
            Ajax,
            Str,
            Templates,
            Notification,
            CustomEvents,
            ModalEvents,
            ModalFactory,
            ModalEventForm,
            SummaryModal,
            CalendarRepository,
            CalendarEvents,
            CalendarViewManager
        ) {

    var SELECTORS = {
        ROOT: "[data-region='calendar']",
        DAY: "[data-region='day']",
        EVENT_ITEM: "[data-region='event-item']",
        EVENT_LINK: "[data-action='view-event']",
        NEW_EVENT_BUTTON: "[data-action='new-event-button']",
        DAY_CONTENT: "[data-region='day-content']",
        LOADING_ICON: '.loading-icon',
        VIEW_DAY_LINK: "[data-action='view-day-link']",
        CALENDAR_MONTH_WRAPPER: ".calendarwrapper",
        COURSE_SELECTOR: 'select[name="course"]'
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
                body: Templates.render('core_calendar/event_summary_body', eventData),
                templateContext: {
                    canedit: eventData.canedit,
                    candelete: eventData.candelete,
                    isactionevent: eventData.isactionevent,
                    url: eventData.url
                }
            };

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
     * Handler for the drag and drop move event. Provides a loading indicator
     * while the request is sent to the server to update the event start date.
     *
     * Triggers a eventMoved calendar javascript event if the event was successfully
     * updated.
     *
     * @param {event} e The calendar move event
     * @param {int} eventId The event id being moved
     * @param {object|null} originElement The jQuery element for where the event is moving from
     * @param {object} destinationElement The jQuery element for where the event is moving to
     */
    var handleMoveEvent = function(e, eventId, originElement, destinationElement) {
        var originTimestamp = null;
        var destinationTimestamp = destinationElement.attr('data-day-timestamp');

        if (originElement) {
            originTimestamp = originElement.attr('data-day-timestamp');
        }

        // If the event has actually changed day.
        if (!originElement || originTimestamp != destinationTimestamp) {
            Templates.render('core/loading', {})
                .then(function(html, js) {
                    // First we show some loading icons in each of the days being affected.
                    destinationElement.find(SELECTORS.DAY_CONTENT).addClass('hidden');
                    Templates.appendNodeContents(destinationElement, html, js);

                    if (originElement) {
                        originElement.find(SELECTORS.DAY_CONTENT).addClass('hidden');
                        Templates.appendNodeContents(originElement, html, js);
                    }
                    return;
                })
                .then(function() {
                    // Send a request to the server to make the change.
                    return CalendarRepository.updateEventStartDay(eventId, destinationTimestamp);
                })
                .then(function() {
                    // If the update was successful then broadcast an event letting the calendar
                    // know that an event has been moved.
                    $('body').trigger(CalendarEvents.eventMoved, [eventId, originElement, destinationElement]);
                    return;
                })
                .always(function() {
                    // Always remove the loading icons regardless of whether the update
                    // request was successful or not.
                    var destinationLoadingElement = destinationElement.find(SELECTORS.LOADING_ICON);
                    destinationElement.find(SELECTORS.DAY_CONTENT).removeClass('hidden');
                    Templates.replaceNode(destinationLoadingElement, '', '');

                    if (originElement) {
                        var originLoadingElement = originElement.find(SELECTORS.LOADING_ICON);
                        originElement.find(SELECTORS.DAY_CONTENT).removeClass('hidden');
                        Templates.replaceNode(originLoadingElement, '', '');
                    }
                    return;
                })
                .fail(Notification.exception);
        }
    };

    /**
     * Create the event form modal for creating new events and
     * editing existing events.
     *
     * @method registerEventFormModal
     * @param {object} root The calendar root element
     * @return {object} The create modal promise
     */
    var registerEventFormModal = function(root) {
        var newEventButton = root.find(SELECTORS.NEW_EVENT_BUTTON);
        var contextId = newEventButton.attr('data-context-id');

        return ModalFactory.create(
            {
                type: ModalEventForm.TYPE,
                large: true,
                templateContext: {
                    contextid: contextId
                }
            },
            [root, SELECTORS.NEW_EVENT_BUTTON]
        );
    };

    /**
     * Listen to and handle any calendar events fired by the calendar UI.
     *
     * @method registerCalendarEventListeners
     * @param {object} root The calendar root element
     * @param {object} eventFormModalPromise A promise reolved with the event form modal
     */
    var registerCalendarEventListeners = function(root, eventFormModalPromise) {
        var body = $('body'),
            courseId = $(root).find(SELECTORS.CALENDAR_MONTH_WRAPPER).data('courseid');

        body.on(CalendarEvents.created, function() {
            CalendarViewManager.reloadCurrentMonth(root);
        });
        body.on(CalendarEvents.deleted, function() {
            CalendarViewManager.reloadCurrentMonth(root);
        });
        body.on(CalendarEvents.updated, function() {
            CalendarViewManager.reloadCurrentMonth(root);
        });
        body.on(CalendarEvents.editActionEvent, function(e, url) {
            // Action events needs to be edit directly on the course module.
            window.location.assign(url);
        });
        // Handle the event fired by the drag and drop code.
        body.on(CalendarEvents.moveEvent, handleMoveEvent);
        // When an event is successfully moved we should updated the UI.
        body.on(CalendarEvents.eventMoved, function() {
            CalendarViewManager.reloadCurrentMonth(root);
        });

        eventFormModalPromise.then(function(modal) {
            // When something within the calendar tells us the user wants
            // to edit an event then show the event form modal.
            body.on(CalendarEvents.editEvent, function(e, eventId) {
                modal.setEventId(eventId);
                modal.show();
            });
            modal.setCourseId(courseId);
            return;
        });
    };

    /**
     * Register event listeners for the module.
     */
    var registerEventListeners = function() {
        var root = $(SELECTORS.ROOT);

        // Bind click events to event links.
        root.on('click', SELECTORS.EVENT_ITEM, function(e) {
            e.preventDefault();
            // We've handled the event so stop it from bubbling
            // and causing the day click handler to fire.
            e.stopPropagation();

            var target = $(e.target);
            var eventId = null;

            if (target.is(SELECTORS.EVENT_LINK)) {
                eventId = target.attr('data-event-id');
            } else {
                eventId = target.find(SELECTORS.EVENT_LINK).attr('data-event-id');
            }

            renderEventSummaryModal(eventId);
        });

        root.on('change', SELECTORS.COURSE_SELECTOR, function() {
            var selectElement = $(this);
            var courseId = selectElement.val();
            CalendarViewManager.reloadCurrentMonth(root, courseId)
                .then(function() {
                    // We need to get the selector again because the content has changed.
                    return root.find(SELECTORS.COURSE_SELECTOR).val(courseId);
                })
                .fail(Notification.exception);
        });

        var eventFormPromise = registerEventFormModal(root);
        registerCalendarEventListeners(root, eventFormPromise);

        // Bind click events to calendar days.
        root.on('click', SELECTORS.DAY, function(e) {
            var target = $(e.target);

            if (!target.is(SELECTORS.VIEW_DAY_LINK)) {
                var startTime = $(this).attr('data-new-event-timestamp');
                eventFormPromise.then(function(modal) {
                    modal.setStartTime(startTime);
                    modal.show();
                    return;
                });

                e.preventDefault();
            }
        });
    };

    return {
        init: function() {
            CalendarViewManager.init();
            registerEventListeners();
        }
    };
});
