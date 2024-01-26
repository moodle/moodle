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
 * @copyright  2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';
import Templates from 'core/templates';
import Notification from 'core/notification';
import * as CalendarRepository from 'core_calendar/repository';
import CalendarEvents from 'core_calendar/events';
import * as CalendarSelectors from 'core_calendar/selectors';
import ModalEvents from 'core/modal_events';
import SummaryModal from 'core_calendar/summary_modal';
import CustomEvents from 'core/custom_interaction_events';
import {getString} from 'core/str';
import Pending from 'core/pending';
import {prefetchStrings} from 'core/prefetch';
import Url from 'core/url';
import Config from 'core/config';

/**
 * Limit number of events per day
 *
 */
const LIMIT_DAY_EVENTS = 5;

/**
 * Hide day events if more than 5.
 *
 */
export const foldDayEvents = () => {
    const root = $(CalendarSelectors.elements.monthDetailed);
    const days = root.find(CalendarSelectors.day);
    if (days.length === 0) {
        return;
    }
    days.each(function() {
        const dayContainer = $(this);
        const eventsSelector = `${CalendarSelectors.elements.dateContent} ul li[data-event-eventtype]`;
        const filteredEventsSelector = `${CalendarSelectors.elements.dateContent} ul li[data-event-filtered="true"]`;
        const moreEventsSelector = `${CalendarSelectors.elements.dateContent} [data-action="view-more-events"]`;
        const events = dayContainer.find(eventsSelector);
        if (events.length === 0) {
            return;
        }

        const filteredEvents = dayContainer.find(filteredEventsSelector);
        const numberOfFiltered = filteredEvents.length;
        const numberOfEvents = events.length - numberOfFiltered;

        let count = 1;
        events.each(function() {
            const event = $(this);
            const isNotFiltered = event.attr('data-event-filtered') !== 'true';
            const offset = (numberOfEvents === LIMIT_DAY_EVENTS) ? 0 : 1;
            if (isNotFiltered) {
                if (count > LIMIT_DAY_EVENTS - offset) {
                    event.attr('data-event-folded', 'true');
                    event.hide();
                } else {
                    event.attr('data-event-folded', 'false');
                    event.show();
                    count++;
                }
            } else {
                // It's being filtered out.
                event.attr('data-event-folded', 'false');
            }
        });

        const moreEventsLink = dayContainer.find(moreEventsSelector);
        if (numberOfEvents > LIMIT_DAY_EVENTS) {
            const numberOfHiddenEvents = numberOfEvents - LIMIT_DAY_EVENTS + 1;
            moreEventsLink.show();
            getString('moreevents', 'calendar', numberOfHiddenEvents).then(str => {
                const link = moreEventsLink.find('strong a');
                moreEventsLink.attr('data-event-folded', 'false');
                link.text(str);
                return str;
            }).catch(Notification.exception);
        } else {
            moreEventsLink.hide();
        }
    });
};

/**
 * Register and handle month calendar events.
 *
 * @param {string} pendingId pending id.
 */
export const registerEventListenersForMonthDetailed = (pendingId) => {
    const events = `${CalendarEvents.viewUpdated}`;
    $('body').on(events, function(e) {
        foldDayEvents(e);
    });
    foldDayEvents();
    $('body').on(CalendarEvents.filterChanged, function(e, data) {
        const root = $(CalendarSelectors.elements.monthDetailed);
        const pending = new Pending(pendingId);
        const target = root.find(CalendarSelectors.eventType[data.type]);
        const transitionPromise = $.Deferred();
        if (data.hidden) {
            transitionPromise.then(function() {
                target.attr('data-event-filtered', 'true');
                return target.hide().promise();
            }).fail();
        } else {
            transitionPromise.then(function() {
                target.attr('data-event-filtered', 'false');
                return target.show().promise();
            }).fail();
        }

        transitionPromise.then(function() {
            foldDayEvents();
            return;
        })
        .always(pending.resolve)
        .fail();

        transitionPromise.resolve();
    });
};

/**
 * Register event listeners for the module.
 *
 * @param {object} root The root element.
 */
const registerEventListeners = (root) => {
    root = $(root);

    // Bind click events to event links.
    root.on('click', CalendarSelectors.links.eventLink, (e) => {
        const target = e.target;
        let eventLink = null;
        let eventId = null;
        const pendingPromise = new Pending('core_calendar/view_manager:eventLink:click');

        if (target.matches(CalendarSelectors.actions.viewEvent)) {
            eventLink = target;
        } else {
            eventLink = target.closest(CalendarSelectors.actions.viewEvent);
        }

        if (eventLink) {
            eventId = eventLink.dataset.eventId;
        } else {
            eventId = target.querySelector(CalendarSelectors.actions.viewEvent).dataset.eventId;
        }

        if (eventId) {
            // A link was found. Show the modal.

            e.preventDefault();
            // We've handled the event so stop it from bubbling
            // and causing the day click handler to fire.
            e.stopPropagation();

            renderEventSummaryModal(eventId)
            .then(pendingPromise.resolve)
            .catch();
        } else {
            pendingPromise.resolve();
        }
    });

    root.on('click', CalendarSelectors.links.navLink, (e) => {
        const wrapper = root.find(CalendarSelectors.wrapper);
        const view = wrapper.data('view');
        const courseId = wrapper.data('courseid');
        const categoryId = wrapper.data('categoryid');
        const link = e.currentTarget;

        if (view === 'month' || view === 'monthblock') {
            changeMonth(root, link.href, link.dataset.year, link.dataset.month, courseId, categoryId, link.dataset.day);
            e.preventDefault();
        } else if (view === 'day') {
            changeDay(root, link.href, link.dataset.year, link.dataset.month, link.dataset.day, courseId, categoryId);
            e.preventDefault();
        }
    });

    const viewSelector = root.find(CalendarSelectors.viewSelector);
    CustomEvents.define(viewSelector, [CustomEvents.events.activate]);
    viewSelector.on(
        CustomEvents.events.activate,
        (e) => {
            e.preventDefault();

            const option = e.target;
            if (option.classList.contains('active')) {
                return;
            }

            const view = option.dataset.view,
                year = option.dataset.year,
                month = option.dataset.month,
                day = option.dataset.day,
                courseId = option.dataset.courseid,
                categoryId = option.dataset.categoryid;

            if (view == 'month') {
                refreshMonthContent(root, year, month, courseId, categoryId, root, 'core_calendar/calendar_month', day)
                    .then(() => {
                        updateUrl('?view=month&course=' + courseId);
                        return;
                    }).fail(Notification.exception);
            } else if (view == 'day') {
                refreshDayContent(root, year, month, day, courseId, categoryId, root, 'core_calendar/calendar_day')
                    .then(() => {
                        updateUrl('?view=day&course=' + courseId);
                        return;
                    }).fail(Notification.exception);
            } else if (view == 'upcoming') {
                reloadCurrentUpcoming(root, courseId, categoryId, root, 'core_calendar/calendar_upcoming')
                    .then(() => {
                        updateUrl('?view=upcoming&course=' + courseId);
                        return;
                    }).fail(Notification.exception);
            }
        }
    );
};

/**
 * Refresh the month content.
 *
 * @param {object} root The root element.
 * @param {number} year Year
 * @param {number} month Month
 * @param {number} courseId The id of the course whose events are shown
 * @param {number} categoryId The id of the category whose events are shown
 * @param {object} target The element being replaced. If not specified, the calendarwrapper is used.
 * @param {string} template The template to be rendered.
 * @param {number} day Day (optional)
 * @return {promise}
 */
export const refreshMonthContent = (root, year, month, courseId, categoryId, target = null, template = '', day = 1) => {
    startLoading(root);

    target = target || root.find(CalendarSelectors.wrapper);
    template = template || root.attr('data-template');
    M.util.js_pending([root.get('id'), year, month, courseId].join('-'));
    const includenavigation = root.data('includenavigation');
    const mini = root.data('mini');
    const viewMode = target.data('view');
    return CalendarRepository.getCalendarMonthData(year, month, courseId, categoryId, includenavigation, mini, day, viewMode)
        .then(context => {
            return Templates.render(template, context);
        })
        .then((html, js) => {
            return Templates.replaceNode(target, html, js);
        })
        .then(() => {
            document.querySelector('body').dispatchEvent(new CustomEvent(CalendarEvents.viewUpdated));
            return;
        })
        .always(() => {
            M.util.js_complete([root.get('id'), year, month, courseId].join('-'));
            return stopLoading(root);
        })
        .fail(Notification.exception);
};

/**
 * Handle changes to the current calendar view.
 *
 * @param {object} root The container element
 * @param {string} url The calendar url to be shown
 * @param {number} year Year
 * @param {number} month Month
 * @param {number} courseId The id of the course whose events are shown
 * @param {number} categoryId The id of the category whose events are shown
 * @param {number} day Day (optional)
 * @return {promise}
 */
export const changeMonth = (root, url, year, month, courseId, categoryId, day = 1) => {
    return refreshMonthContent(root, year, month, courseId, categoryId, null, '', day)
        .then((...args) => {
            if (url.length && url !== '#') {
                updateUrl(url);
            }
            return args;
        })
        .then((...args) => {
            $('body').trigger(CalendarEvents.monthChanged, [year, month, courseId, categoryId]);
            return args;
        });
};

/**
 * Reload the current month view data.
 *
 * @param {object} root The container element.
 * @param {number} courseId The course id.
 * @param {number} categoryId The id of the category whose events are shown
 * @return {promise}
 */
export const reloadCurrentMonth = (root, courseId = 0, categoryId = 0) => {
    const year = root.find(CalendarSelectors.wrapper).data('year');
    const month = root.find(CalendarSelectors.wrapper).data('month');
    const day = root.find(CalendarSelectors.wrapper).data('day');

    courseId = courseId || root.find(CalendarSelectors.wrapper).data('courseid');
    categoryId = categoryId || root.find(CalendarSelectors.wrapper).data('categoryid');

    return refreshMonthContent(root, year, month, courseId, categoryId, null, '', day).
        then((...args) => {
            $('body').trigger(CalendarEvents.courseChanged, [year, month, courseId, categoryId]);
            return args;
        });
};


/**
 * Refresh the day content.
 *
 * @param {object} root The root element.
 * @param {number} year Year
 * @param {number} month Month
 * @param {number} day Day
 * @param {number} courseId The id of the course whose events are shown
 * @param {number} categoryId The id of the category whose events are shown
 * @param {object} target The element being replaced. If not specified, the calendarwrapper is used.
 * @param {string} template The template to be rendered.
 *
 * @return {promise}
 */
export const refreshDayContent = (root, year, month, day, courseId, categoryId, target = null, template = '') => {
    startLoading(root);

    if (!target || target.length == 0){
        target = root.find(CalendarSelectors.wrapper);
    }
    template = template || root.attr('data-template');
    M.util.js_pending([root.get('id'), year, month, day, courseId, categoryId].join('-'));
    const includenavigation = root.data('includenavigation');
    return CalendarRepository.getCalendarDayData(year, month, day, courseId, categoryId, includenavigation)
        .then((context) => {
            context.viewingday = true;
            context.showviewselector = true;
            return Templates.render(template, context);
        })
        .then((html, js) => {
            return Templates.replaceNode(target, html, js);
        })
        .then(() => {
            document.querySelector('body').dispatchEvent(new CustomEvent(CalendarEvents.viewUpdated));
            return;
        })
        .always(() => {
            M.util.js_complete([root.get('id'), year, month, day, courseId, categoryId].join('-'));
            return stopLoading(root);
        })
        .fail(Notification.exception);
};

/**
 * Reload the current day view data.
 *
 * @param {object} root The container element.
 * @param {number} courseId The course id.
 * @param {number} categoryId The id of the category whose events are shown
 * @return {promise}
 */
export const reloadCurrentDay = (root, courseId = 0, categoryId = 0) => {
    const wrapper = root.find(CalendarSelectors.wrapper);
    const year = wrapper.data('year');
    const month = wrapper.data('month');
    const day = wrapper.data('day');

    courseId = courseId || root.find(CalendarSelectors.wrapper).data('courseid');
    categoryId = categoryId || root.find(CalendarSelectors.wrapper).data('categoryid');

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
export const changeDay = (root, url, year, month, day, courseId, categoryId) => {
    return refreshDayContent(root, year, month, day, courseId, categoryId)
        .then((...args) => {
            if (url.length && url !== '#') {
                updateUrl(url);
            }
            return args;
        })
        .then((...args) => {
            $('body').trigger(CalendarEvents.dayChanged, [year, month, courseId, categoryId]);
            return args;
        });
};

/**
 * Update calendar URL.
 *
 * @param {String} url The calendar url to be updated.
 */
export const updateUrl = (url) => {
    const viewingFullCalendar = document.getElementById(CalendarSelectors.fullCalendarView);

    // We want to update the url only if the user is viewing the full calendar.
    if (viewingFullCalendar) {
        window.history.pushState({}, '', url);
    }
};

/**
 * Set the element state to loading.
 *
 * @param {object} root The container element
 * @method startLoading
 */
const startLoading = (root) => {
    const loadingIconContainer = root.find(CalendarSelectors.containers.loadingIcon);

    loadingIconContainer.removeClass('hidden');
};

/**
 * Remove the loading state from the element.
 *
 * @param {object} root The container element
 * @method stopLoading
 */
const stopLoading = (root) => {
    const loadingIconContainer = root.find(CalendarSelectors.containers.loadingIcon);

    loadingIconContainer.addClass('hidden');
};

/**
 * Reload the current month view data.
 *
 * @param {object} root The container element.
 * @param {number} courseId The course id.
 * @param {number} categoryId The id of the category whose events are shown
 * @param {object} target The element being replaced. If not specified, the calendarwrapper is used.
 * @param {string} template The template to be rendered.
 * @return {promise}
 */
export const reloadCurrentUpcoming = (root, courseId = 0, categoryId = 0, target = null, template = '') => {
    startLoading(root);

    target = target || root.find(CalendarSelectors.wrapper);
    template = template || root.attr('data-template');
    courseId = courseId || root.find(CalendarSelectors.wrapper).data('courseid');
    categoryId = categoryId || root.find(CalendarSelectors.wrapper).data('categoryid');

    return CalendarRepository.getCalendarUpcomingData(courseId, categoryId)
        .then((context) => {
            context.viewingupcoming = true;
            context.showviewselector = true;
            return Templates.render(template, context);
        })
        .then((html, js) => {
            return Templates.replaceNode(target, html, js);
        })
        .then(() => {
            document.querySelector('body').dispatchEvent(new CustomEvent(CalendarEvents.viewUpdated));
            return;
        })
        .always(function() {
            return stopLoading(root);
        })
        .fail(Notification.exception);
};

/**
 * Get the CSS class to apply for the given event type.
 *
 * @param {string} eventType The calendar event type
 * @return {string}
 */
const getEventTypeClassFromType = (eventType) => {
    return 'calendar_event_' + eventType;
};

/**
 * Render the event summary modal.
 *
 * @param {Number} eventId The calendar event id.
 * @returns {Promise}
 */
const renderEventSummaryModal = (eventId) => {
    const pendingPromise = new Pending('core_calendar/view_manager:renderEventSummaryModal');

    // Calendar repository promise.
    return CalendarRepository.getEventById(eventId)
    .then((getEventResponse) => {
        if (!getEventResponse.event) {
            throw new Error('Error encountered while trying to fetch calendar event with ID: ' + eventId);
        }

        return getEventResponse.event;
    })
    .then(eventData => {
        // Build the modal parameters from the event data.
        const modalParams = {
            title: eventData.name,
            body: Templates.render('core_calendar/event_summary_body', eventData),
            templateContext: {
                canedit: eventData.canedit,
                candelete: eventData.candelete,
                headerclasses: getEventTypeClassFromType(eventData.normalisedeventtype),
                isactionevent: eventData.isactionevent,
                url: eventData.url,
                action: eventData.action
            }
        };

        // Create the modal.
        return SummaryModal.create(modalParams);
    })
    .then(modal => {
        // Handle hidden event.
        modal.getRoot().on(ModalEvents.hidden, function() {
            // Destroy when hidden.
            modal.destroy();
        });

        // Finally, render the modal!
        modal.show();

        return modal;
    })
    .then(modal => {
        pendingPromise.resolve();

        return modal;
    })
    .catch(Notification.exception);
};

export const init = (root, view) => {
    prefetchStrings('calendar', ['moreevents']);
    foldDayEvents();
    registerEventListeners(root, view);
    const calendarTable = root.find(CalendarSelectors.elements.monthDetailed);
    if (calendarTable.length) {
        const pendingId = `month-detailed-${calendarTable.id}-filterChanged`;
        registerEventListenersForMonthDetailed(calendarTable, pendingId);
    }
};

/**
 * Handles the change of course and updates the relevant elements on the page.
 *
 * @param {integer} courseId - The ID of the new course.
 * @param {string} courseName - The name of the new course.
 * @returns {Promise<void>} - A promise that resolves after the updates are applied.
 */
export const handleCourseChange = async(courseId, courseName) => {
    // Select the <ul> element inside the data-region="view-selector".
    const ulElement = document.querySelector(CalendarSelectors.viewSelector + ' ul');
    // Select all <li><a> elements within the <ul>.
    const liElements = ulElement.querySelectorAll('li a');
    // Loop through the selected elements and update the courseid.
    liElements.forEach(element => {
        element.setAttribute('data-courseid', courseId);
    });

    const calendar = await getString('calendar', 'calendar');
    const pageHeaderHeadingsElement = document.querySelector(CalendarSelectors.pageHeaderHeadings);
    const courseUrl = Url.relativeUrl('/course/view.php', {id: courseId});
    // Apply the page header text.
    if (courseId !== Config.siteId) {
        pageHeaderHeadingsElement.innerHTML = calendar + ': <a href="' + courseUrl + '">' + courseName + '</a>';
    } else {
        pageHeaderHeadingsElement.innerHTML = calendar;
    }
};