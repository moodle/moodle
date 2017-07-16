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
        'core/modal_factory', 'core_calendar/summary_modal', 'core_calendar/calendar_repository'],
    function($, Ajax, Str, Templates, Notification, CustomEvents, ModalFactory, SummaryModal, CalendarRepository) {

        var SELECTORS = {
            ROOT: "[data-region='calendar']",
            EVENT_LINK: "[data-action='view-event']",
        };

        var modalPromise = null;

        /**
         * Get the event type lang string.
         *
         * @param {String} eventType The event type.
         * @return {String} The lang string of the event type.
         */
        var getEventType = function(eventType) {
            var lang = 'type' + eventType;
            return Str.get_string(lang, 'core_calendar').then(function(langStr) {
                return langStr;
            }).fail(Notification.exception);
        };

        /**
         * Render the event summary modal.
         *
         * @param {Number} eventId The calendar event id.
         * @return {promise} The summary modal promise.
         */
        var renderEventSummaryModal = function(eventId) {

            var promise = CalendarRepository.getEventById(eventId);

            return promise.then(function(result) {
                if (!result.event) {
                    promise.fail(Notification.exception);
                } else {
                    return result.event;
                }
            }).then(function(eventdata) {
                return getEventType(eventdata.eventtype).then(function(langStr) {
                    eventdata.eventtype = langStr;
                    return eventdata;
                });
            }).then(function(eventdata) {
                return modalPromise.done(function(modal) {
                    modal.setTitle(eventdata.name);
                    modal.setBody(Templates.render('core_calendar/event_summary_body', eventdata));
                    // Hide edit and delete buttons if I don't have permission.
                    if (eventdata.caneditevent == false) {
                        modal.setFooter('');
                    }

                    modal.show();
                });
            });
        };

        /**
         * Register event listeners for the module.
         *
         * @param {object} root The root element.
         */
        var registerEventListeners = function(root) {
            root = $(root);

            var loading = false;
            root.on('click', SELECTORS.EVENT_LINK, function(e) {
                if (!loading) {
                    loading = true;
                    e.preventDefault();

                    var eventElement = $(e.target).closest(SELECTORS.EVENT_LINK);
                    var eventId = eventElement.attr('data-event-id');

                    renderEventSummaryModal(eventId).done(function() {
                        loading = false;
                    });
                }
            });
        };

        return {
            init: function() {
                modalPromise = ModalFactory.create({
                    type: SummaryModal.TYPE
                });

                registerEventListeners(SELECTORS.ROOT);
            }
        };
    });
