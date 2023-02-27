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
 * @module     core_calendar/calendar_view
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

        var registerEventListeners = function(root, type) {
            var body = $('body');

            CalendarCrud.registerRemove(root);

            var reloadFunction = 'reloadCurrent' + type.charAt(0).toUpperCase() + type.slice(1);

            body.on(CalendarEvents.created, function() {
                CalendarViewManager[reloadFunction](root);
            });
            body.on(CalendarEvents.deleted, function() {
                CalendarViewManager[reloadFunction](root);
            });
            body.on(CalendarEvents.updated, function() {
                CalendarViewManager[reloadFunction](root);
            });

            root.on('change', CalendarSelectors.courseSelector, function() {
                var selectElement = $(this);
                var courseId = selectElement.val();
                CalendarViewManager[reloadFunction](root, courseId, null)
                    .then(function() {
                        // We need to get the selector again because the content has changed.
                        return root.find(CalendarSelectors.courseSelector).val(courseId);
                    })
                    .then(function() {
                        CalendarViewManager.updateUrl('?view=upcoming&course=' + courseId);
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
                CalendarViewManager.foldDayEvents(root);
            });

            var eventFormPromise = CalendarCrud.registerEventFormModal(root);
            CalendarCrud.registerEditListeners(root, eventFormPromise);
        };

        return {
            init: function(root, type) {
                root = $(root);

                CalendarViewManager.init(root, type);
                registerEventListeners(root, type);
            }
        };
    });
