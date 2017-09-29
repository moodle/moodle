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
 * A module to handle CRUD operations within the UI.
 *
 * @module     core_calendar/crud
 * @package    core_calendar
 * @copyright  2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'core/str',
    'core/notification',
    'core/custom_interaction_events',
    'core/modal',
    'core/modal_registry',
    'core/modal_factory',
    'core/modal_events',
    'core_calendar/modal_event_form',
    'core_calendar/repository',
    'core_calendar/events',
    'core_calendar/modal_delete',
    'core_calendar/selectors',
],
function(
    $,
    Str,
    Notification,
    CustomEvents,
    Modal,
    ModalRegistry,
    ModalFactory,
    ModalEvents,
    ModalEventForm,
    CalendarRepository,
    CalendarEvents,
    ModalDelete,
    CalendarSelectors
) {

    /**
     * Prepares the action for the summary modal's delete action.
     *
     * @param {Number} eventId The ID of the event.
     * @param {string} eventTitle The event title.
     * @param {Number} eventCount The number of events in the series.
     * @return {Promise}
     */
    function confirmDeletion(eventId, eventTitle, eventCount) {
        var deleteStrings = [
            {
                key: 'deleteevent',
                component: 'calendar'
            },
        ];

        eventCount = parseInt(eventCount, 10);
        var deletePromise;
        var isRepeatedEvent = eventCount > 1;
        if (isRepeatedEvent) {
            deleteStrings.push({
                key: 'confirmeventseriesdelete',
                component: 'calendar',
                param: {
                    name: eventTitle,
                    count: eventCount,
                },
            });

            deletePromise = ModalFactory.create(
                {
                    type: ModalDelete.TYPE
                }
            );
        } else {
            deleteStrings.push({
                key: 'confirmeventdelete',
                component: 'calendar',
                param: eventTitle
            });


            deletePromise = ModalFactory.create(
                {
                    type: ModalFactory.types.SAVE_CANCEL
                }
            );
        }

        deletePromise.then(function(deleteModal) {
            deleteModal.show();

            return;
        })
        .fail(Notification.exception);

        var stringsPromise = Str.get_strings(deleteStrings);

        var finalPromise = $.when(stringsPromise, deletePromise)
        .then(function(strings, deleteModal) {
            deleteModal.setTitle(strings[0]);
            deleteModal.setBody(strings[1]);
            if (!isRepeatedEvent) {
                deleteModal.setSaveButtonText(strings[0]);
            }

            deleteModal.getRoot().on(ModalEvents.save, function() {
                CalendarRepository.deleteEvent(eventId, false)
                    .then(function() {
                        $('body').trigger(CalendarEvents.deleted, [eventId, false]);
                        return;
                    })
                    .catch(Notification.exception);
            });

            deleteModal.getRoot().on(CalendarEvents.deleteAll, function() {
                CalendarRepository.deleteEvent(eventId, true)
                    .then(function() {
                        $('body').trigger(CalendarEvents.deleted, [eventId, true]);
                        return;
                    })
                    .catch(Notification.exception);
            });

            return deleteModal;
        })
        .fail(Notification.exception);

        return finalPromise;
    }

    /**
     * Create the event form modal for creating new events and
     * editing existing events.
     *
     * @method registerEventFormModal
     * @param {object} root The calendar root element
     * @param {object} newEventButton The new event button element
     * @return {object} The create modal promise
     */
    var registerEventFormModal = function(root, newEventButton) {

        var contextId = newEventButton.data('context-id');

        return ModalFactory.create(
            {
                type: ModalEventForm.TYPE,
                large: true,
                templateContext: {
                    contextid: contextId
                }
            }, [root, CalendarSelectors.newEventButton]
        );
    };
    /**
     * Register the listeners required to remove the event.
     *
     * @param   {jQuery} root
     */
    function registerRemove(root) {
        root.on('click', CalendarSelectors.actions.remove, function(e) {
            // Fetch the event title, count, and pass them into the new dialogue.
            var eventSource = $(this);
            var eventId = eventSource.data('eventId'),
                eventTitle = eventSource.data('eventTitle'),
                eventCount = eventSource.data('eventCount');
            confirmDeletion(eventId, eventTitle, eventCount);

            e.preventDefault();
        });
    }

    return {
        init: function(root) {
            registerEventFormModal(root, $(CalendarSelectors.newEventButton));
        },
        registerRemove: registerRemove,
        confirmDeletion: confirmDeletion,
        registerEventFormModal: registerEventFormModal
    };
});
