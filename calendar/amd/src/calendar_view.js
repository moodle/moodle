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
 * This module is responsible for handle calendar day and upcoming view.
 *
 * @module     core_calendar/calendar
 * @package    core_calendar
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
        'jquery',
        'core/str',
        'core/notification',
        'core_calendar/selectors',
        'core_calendar/events',
        'core_calendar/view_manager',
        'core_calendar/repository',
        'core/modal_factory',
        'core_calendar/modal_event_form',
        'core/modal_events',
        'core_calendar/crud'
    ],
    function(
        $,
        Str,
        Notification,
        CalendarSelectors,
        CalendarEvents,
        CalendarViewManager,
        CalendarRepository,
        ModalFactory,
        ModalEventForm,
        ModalEvents,
        CalendarCrud
    ) {

        var registerEventListeners = function(root) {
            var body = $('body'),
                deleteLink = $(CalendarSelectors.deleteLink),
                newEventButton = $(CalendarSelectors.newEventButton),
                courseId = root.data('courseid');

            var eventFormPromise = CalendarCrud.registerEventFormModal(root, newEventButton);

            CalendarCrud.registerRemove(deleteLink);

            root.on('click', CalendarSelectors.deleteLink, function(e) {
                e.preventDefault();

                var target = $(e.currentTarget),
                    eventId = target.data('event-id'),
                    eventTitle = target.data('title'),
                    eventCount = target.data('event-event-count');

                CalendarCrud.confirmDeletion(eventId, eventTitle, eventCount).then(function(deleteModalPromise) {
                    body.on(CalendarEvents.deleted, function() {
                        // Close the dialogue on delete.
                        deleteModalPromise.hide();
                        CalendarViewManager.reloadCurrentUpcoming(root, courseId);
                    }.bind(this));
                });
            });

            root.on('click', CalendarSelectors.editLink, function(e) {
                e.preventDefault();
                var target = $(e.currentTarget);

                eventFormPromise.then(function(modal) {
                    // When something within the calendar tells us the user wants
                    // to edit an event then show the event form modal.
                    var eventId = target.data('event-id');
                    if (eventId) {
                        modal.setEventId(eventId);
                    }
                    modal.show();
                    return;
                }).fail(Notification.exception);
            });

            root.on('change', CalendarSelectors.courseSelector, function() {
                var selectElement = $(this);
                var courseId = selectElement.val();
                CalendarViewManager.reloadCurrentUpcoming(root, courseId)
                    .then(function() {
                        // We need to get the selector again because the content has changed.
                        return root.find(CalendarSelectors.courseSelector).val(courseId);
                    })
                    .fail(Notification.exception);
            });

            body.on(CalendarEvents.filterChanged, function(e, data) {
                var daysWithEvent = root.find(CalendarSelectors.eventType[data.type]);
                if (data.hidden == true) {
                    daysWithEvent.addClass('hidden');
                } else {
                    daysWithEvent.removeClass('hidden');
                }
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
