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
 * A javascript module to handler calendar view changes.
 *
 * @module     core_calendar/view_manager
 * @package    core_calendar
 * @copyright  2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'core/templates',
    'core/str',
    'core/notification',
    'core_calendar/repository',
    'core_calendar/events',
    'core_calendar/selectors',
    'core/modal_factory',
    'core/modal_events',
    'core_calendar/summary_modal',
], function(
    $,
    Templates,
    Str,
    Notification,
    CalendarRepository,
    CalendarEvents,
    CalendarSelectors,
    ModalFactory,
    ModalEvents,
    SummaryModal
) {

        /**
         * Register event listeners for the module.
         *
         * @param {object} root The root element.
         */
        var registerEventListeners = function(root) {
            root = $(root);

            // Bind click events to event links.
            root.on('click', CalendarSelectors.links.eventLink, function(e) {
                var target = $(e.target);
                var eventId = null;

                var eventLink;
                if (target.is(CalendarSelectors.actions.viewEvent)) {
                    eventLink = target;
                } else {
                    eventLink = target.closest(CalendarSelectors.actions.viewEvent);
                }

                if (eventLink.length) {
                    eventId = eventLink.data('eventId');
                } else {
                    eventId = target.find(CalendarSelectors.actions.viewEvent).data('eventId');
                }

                if (eventId) {
                    // A link was found. Show the modal.

                    e.preventDefault();
                    // We've handled the event so stop it from bubbling
                    // and causing the day click handler to fire.
                    e.stopPropagation();

                    renderEventSummaryModal(eventId);
                }
            });


            root.on('click', CalendarSelectors.links.navLink, function(e) {
                var wrapper = root.find(CalendarSelectors.wrapper);
                var view = wrapper.data('view');
                var courseId = wrapper.data('courseid');
                var categoryId = wrapper.data('categoryid');
                var link = $(e.currentTarget);

                if (view === 'month') {
                    changeMonth(root, link.attr('href'), link.data('year'), link.data('month'), courseId, categoryId);
                    e.preventDefault();
                } else if (view === 'day') {
                    changeDay(root, link.attr('href'), link.data('year'), link.data('month'), link.data('day'),
                        courseId, categoryId);
                    e.preventDefault();
                }

            });
        };

        /**
         * Refresh the month content.
         *
         * @param {object} root The root element.
         * @param {Number} year Year
         * @param {Number} month Month
         * @param {Number} courseid The id of the course whose events are shown
         * @param {Number} categoryid The id of the category whose events are shown
         * @param {object} target The element being replaced. If not specified, the calendarwrapper is used.
         * @return {promise}
         */
        var refreshMonthContent = function(root, year, month, courseid, categoryid, target) {
            startLoading(root);

            target = target || root.find(CalendarSelectors.wrapper);

            M.util.js_pending([root.get('id'), year, month, courseid].join('-'));
            var includenavigation = root.data('includenavigation');
            return CalendarRepository.getCalendarMonthData(year, month, courseid, categoryid, includenavigation)
                .then(function(context) {
                    return Templates.render(root.attr('data-template'), context);
                })
                .then(function(html, js) {
                    return Templates.replaceNode(target, html, js);
                })
                .then(function() {
                    $('body').trigger(CalendarEvents.viewUpdated);
                    return;
                })
                .always(function() {
                    M.util.js_complete([root.get('id'), year, month, courseid].join('-'));
                    return stopLoading(root);
                })
                .fail(Notification.exception);
        };

        /**
         * Handle changes to the current calendar view.
         *
         * @param {object} root The container element
         * @param {String} url The calendar url to be shown
         * @param {Number} year Year
         * @param {Number} month Month
         * @param {Number} courseid The id of the course whose events are shown
         * @param {Number} categoryid The id of the category whose events are shown
         * @return {promise}
         */
        var changeMonth = function(root, url, year, month, courseid, categoryid) {
            return refreshMonthContent(root, year, month, courseid, categoryid)
                .then(function() {
                    if (url.length && url !== '#') {
                        window.history.pushState({}, '', url);
                    }
                    return arguments;
                })
                .then(function() {
                    $('body').trigger(CalendarEvents.monthChanged, [year, month, courseid, categoryid]);
                    return arguments;
                });
        };

        /**
         * Reload the current month view data.
         *
         * @param {object} root The container element.
         * @param {Number} courseId The course id.
         * @param {Number} categoryId The id of the category whose events are shown
         * @return {promise}
         */
        var reloadCurrentMonth = function(root, courseId, categoryId) {
            var year = root.find(CalendarSelectors.wrapper).data('year');
            var month = root.find(CalendarSelectors.wrapper).data('month');

            if (typeof courseId === 'undefined') {
                courseId = root.find(CalendarSelectors.wrapper).data('courseid');
            }

            if (typeof categoryId === 'undefined') {
                categoryId = root.find(CalendarSelectors.wrapper).data('categoryid');
            }

            return refreshMonthContent(root, year, month, courseId, categoryId);
        };


        /**
         * Refresh the day content.
         *
         * @param {object} root The root element.
         * @param {Number} year Year
         * @param {Number} month Month
         * @param {Number} day Day
         * @param {Number} courseid The id of the course whose events are shown
         * @param {Number} categoryId The id of the category whose events are shown
         * @param {object} target The element being replaced. If not specified, the calendarwrapper is used.
         * @return {promise}
         */
        var refreshDayContent = function(root, year, month, day, courseid, categoryId, target) {
            startLoading(root);

            target = target || root.find(CalendarSelectors.wrapper);

            M.util.js_pending([root.get('id'), year, month, day, courseid, categoryId].join('-'));
            var includenavigation = root.data('includenavigation');
            return CalendarRepository.getCalendarDayData(year, month, day, courseid, categoryId, includenavigation)
                .then(function(context) {
                    return Templates.render(root.attr('data-template'), context);
                })
                .then(function(html, js) {
                    return Templates.replaceNode(target, html, js);
                })
                .then(function() {
                    $('body').trigger(CalendarEvents.viewUpdated);
                    return;
                })
                .always(function() {
                    M.util.js_complete([root.get('id'), year, month, day, courseid, categoryId].join('-'));
                    return stopLoading(root);
                })
                .fail(Notification.exception);
        };

        /**
         * Reload the current day view data.
         *
         * @param {object} root The container element.
         * @param {Number} courseId The course id.
         * @param {Number} categoryId The id of the category whose events are shown
         * @return {promise}
         */
        var reloadCurrentDay = function(root, courseId, categoryId) {
            var wrapper = root.find(CalendarSelectors.wrapper);
            var year = wrapper.data('year');
            var month = wrapper.data('month');
            var day = wrapper.data('day');

            if (!courseId) {
                courseId = root.find(CalendarSelectors.wrapper).data('courseid');
            }

            if (typeof categoryId === 'undefined') {
                categoryId = root.find(CalendarSelectors.wrapper).data('categoryid');
            }

            return refreshDayContent(root, year, month, day, courseId, categoryId);
        };

        /**
         * Handle changes to the current calendar view.
         *
         * @param {object} root The root element.
         * @param {String} url The calendar url to be shown
         * @param {Number} year Year
         * @param {Number} month Month
         * @param {Number} day Day
         * @param {Number} courseId The id of the course whose events are shown
         * @param {Number} categoryId The id of the category whose events are shown
         * @return {promise}
         */
        var changeDay = function(root, url, year, month, day, courseId, categoryId) {
            return refreshDayContent(root, year, month, day, courseId, categoryId)
                .then(function() {
                    if (url.length && url !== '#') {
                        window.history.pushState({}, '', url);
                    }
                    return arguments;
                })
                .then(function() {
                    $('body').trigger(CalendarEvents.dayChanged, [year, month, day, courseId, categoryId]);
                    return arguments;
                });
        };

        /**
         * Set the element state to loading.
         *
         * @param {object} root The container element
         * @method startLoading
         */
        var startLoading = function(root) {
            var loadingIconContainer = root.find(CalendarSelectors.containers.loadingIcon);

            loadingIconContainer.removeClass('hidden');
        };

        /**
         * Remove the loading state from the element.
         *
         * @param {object} root The container element
         * @method stopLoading
         */
        var stopLoading = function(root) {
            var loadingIconContainer = root.find(CalendarSelectors.containers.loadingIcon);

            loadingIconContainer.addClass('hidden');
        };

        /**
         * Reload the current month view data.
         *
         * @param {object} root The container element.
         * @param {Number} courseId The course id.
         * @param {Number} categoryId The id of the category whose events are shown
         * @return {promise}
         */
        var reloadCurrentUpcoming = function(root, courseId, categoryId) {
            startLoading(root);

            var target = root.find(CalendarSelectors.wrapper);

            if (typeof courseId === 'undefined') {
                courseId = root.find(CalendarSelectors.wrapper).data('courseid');
            }

            if (typeof categoryId === 'undefined') {
                categoryId = root.find(CalendarSelectors.wrapper).data('categoryid');
            }

            return CalendarRepository.getCalendarUpcomingData(courseId, categoryId)
                .then(function(context) {
                    return Templates.render(root.attr('data-template'), context);
                })
                .then(function(html, js) {
                    return Templates.replaceNode(target, html, js);
                })
                .then(function() {
                    $('body').trigger(CalendarEvents.viewUpdated);
                    return;
                })
                .always(function() {
                    return stopLoading(root);
                })
                .fail(Notification.exception);
        };

        /**
         * Convert the given event type into one of either user, site,
         * group, category, or course.
         *
         * @param {String} eventType The calendar event type
         * @return {String}
         */
        var normaliseEventType = function(eventType) {
            switch (eventType) {
                case 'user':
                    return 'user';
                case 'site':
                    return 'site';
                case 'group':
                    return 'group';
                case 'category':
                    return 'category';
                default:
                    return 'course';
            }
        };

        /**
         * Get the CSS class to apply for the given event type.
         *
         * @param {String} eventType The calendar event type
         * @return {String}
         */
        var getEventTypeClassFromType = function(eventType) {
            return 'calendar_event_' + normaliseEventType(eventType);
        };

        /**
         * Render the event summary modal.
         *
         * @param {Number} eventId The calendar event id.
         */
        var renderEventSummaryModal = function(eventId) {
            var typeClass = '';

            // Calendar repository promise.
            CalendarRepository.getEventById(eventId).then(function(getEventResponse) {
                if (!getEventResponse.event) {
                    throw new Error('Error encountered while trying to fetch calendar event with ID: ' + eventId);
                }
                var eventData = getEventResponse.event;
                typeClass = getEventTypeClassFromType(eventData.eventtype);

                return getEventType(eventData.eventtype).then(function(eventType) {
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
                        headerclasses: typeClass,
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
         * Get the event type lang string.
         *
         * @param {String} eventType The event type.
         * @return {promise} The lang string promise.
         */
        var getEventType = function(eventType) {
            var lang = 'type' + normaliseEventType(eventType);
            return Str.get_string(lang, 'core_calendar').then(function(langStr) {
                return langStr;
            });
        };

        return {
            init: function(root) {
                registerEventListeners(root);
            },
            reloadCurrentMonth: reloadCurrentMonth,
            changeMonth: changeMonth,
            refreshMonthContent: refreshMonthContent,
            reloadCurrentDay: reloadCurrentDay,
            changeDay: changeDay,
            refreshDayContent: refreshDayContent,
            reloadCurrentUpcoming: reloadCurrentUpcoming
        };
    });
