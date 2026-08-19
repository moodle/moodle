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
 * Data-access layer for the Timeline block.
 *
 * All AJAX calls live here — views only ever talk to this module, never to
 * @moodle/lms/core/ajax directly. Every call wraps an existing core_calendar
 * or core_course web service; block_timeline defines none of its own.
 *
 * @module     block_timeline/repository
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {fetchOne, fetchMany} from '@moodle/lms/core/ajax';
import {getString} from '@moodle/lms/core/stringUtils';
import config from '@moodle/lms/core/config';
import type {CalendarEvent, Course} from './common/types';

/** Options for fetching timeline events (dates view). */
export interface GetTimelineEventsArgs {
    timesortfrom?: number;
    timesortto?: number | null;
    aftereventid?: number;
    limitnum?: number;
    searchvalue?: string | null;
}

/** Response from core_calendar_get_action_events_by_timesort. */
export interface TimelineEventsResponse {
    events: CalendarEvent[];
}

/**
 * Fetch action events for the timeline dates view.
 *
 * Calls core_calendar_get_action_events_by_timesort.
 *
 * @param args time range, pagination and search filters.
 */
export const getTimelineEvents = (args: GetTimelineEventsArgs): Promise<TimelineEventsResponse> => {
    return fetchOne<TimelineEventsResponse>({
        methodname: 'core_calendar_get_action_events_by_timesort',
        args: {
            timesortfrom: args.timesortfrom ?? 0,
            timesortto:   args.timesortto ?? null,
            aftereventid: args.aftereventid ?? 0,
            limitnum:     args.limitnum ?? 20,
            searchvalue:  args.searchvalue ?? null,
        },
    });
};

/** Options for fetching enrolled courses for the courses view. */
export interface GetEnrolledCoursesArgs {
    limit?: number;
    offset?: number;
    searchvalue?: string | null;
}

/** Response from core_course_get_enrolled_courses_by_timeline_classification. */
export interface EnrolledCoursesResponse {
    courses: Course[];
    nextoffset: number;
}

/**
 * Fetch enrolled courses (all classifications, excluding user-hidden ones) for the courses view.
 *
 * Calls core_course_get_enrolled_courses_by_timeline_classification with
 * classification 'all' — the same classification the legacy Timeline block
 * used, so every non-hidden enrolled course is eligible, not just
 * "in progress" ones.
 *
 * @param args pagination and search filters.
 */
export const getEnrolledCourses = (args: GetEnrolledCoursesArgs): Promise<EnrolledCoursesResponse> => {
    return fetchOne<EnrolledCoursesResponse>({
        methodname: 'core_course_get_enrolled_courses_by_timeline_classification',
        args: {
            classification: 'all',
            limit:          args.limit ?? 2,
            offset:         args.offset ?? 0,
            sort:           'fullname ASC',
            searchvalue:    args.searchvalue ?? null,
        },
    });
};

/** Options for fetching action events across multiple courses. */
export interface GetEventsByCoursesArgs {
    courseids: number[];
    timesortfrom?: number | null;
    timesortto?: number | null;
    limitnum?: number;
    searchvalue?: string | null;
}

/** A single course's events, as returned within groupedbycourse. */
export interface EventsByCourseGroup {
    courseid: number;
    events: CalendarEvent[];
}

/** Response from core_calendar_get_action_events_by_courses. */
export interface EventsByCoursesResponse {
    groupedbycourse: EventsByCourseGroup[];
}

/**
 * Fetch action events for a set of courses, grouped by course id.
 *
 * Calls core_calendar_get_action_events_by_courses.
 *
 * @param args course ids, time range and search filters.
 */
export const getEventsByCourses = (args: GetEventsByCoursesArgs): Promise<EventsByCoursesResponse> => {
    return fetchOne<EventsByCoursesResponse>({
        methodname: 'core_calendar_get_action_events_by_courses',
        args: {
            courseids:    args.courseids,
            timesortfrom: args.timesortfrom ?? null,
            timesortto:   args.timesortto ?? null,
            limitnum:     args.limitnum ?? 10,
            searchvalue:  args.searchvalue ?? null,
        },
    });
};

/** Options for fetching more events for a single course. */
export interface GetEventsByCourseArgs {
    courseid: number;
    timesortfrom: number;
    timesortto?: number | null;
    aftereventid?: number;
    limitnum?: number;
    searchvalue?: string | null;
}

/** Response from core_calendar_get_action_events_by_course. */
export interface EventsByCourseResponse {
    events: CalendarEvent[];
    firstid?: number;
    lastid?: number;
}

/**
 * Fetch more action events for a single course (for per-course "Show more activities").
 *
 * Calls core_calendar_get_action_events_by_course.
 *
 * @param args course id, time range, pagination and search filters.
 */
export const getEventsByCourse = (args: GetEventsByCourseArgs): Promise<EventsByCourseResponse> => {
    return fetchOne<EventsByCourseResponse>({
        methodname: 'core_calendar_get_action_events_by_course',
        args: {
            courseid:     args.courseid,
            timesortfrom: args.timesortfrom,
            timesortto:   args.timesortto ?? null,
            aftereventid: args.aftereventid ?? 0,
            limitnum:     args.limitnum ?? 20,
            searchvalue:  args.searchvalue ?? null,
        },
    });
};

/**
 * Persist a single user preference via the core_user_update_user_preferences WS.
 * Failures are intentionally swallowed — the preference will revert on next page load.
 *
 * @param name preference key to update.
 * @param value new preference value.
 */
export const setUserPreference = (name: string, value: string): void => {
    fetchOne({
        methodname: 'core_user_update_user_preferences',
        args: {preferences: [{type: name, value}]},
    }).catch(() => undefined);
};

/**
 * Fetch server-formatted date strings for the given timestamps, using a langconfig
 * strftime format string.
 *
 * Calls core_get_user_dates so the format respects the site language and the
 * user's timezone, matching the server-side userdate() output the deleted
 * block-specific web services used to embed directly in their responses.
 * None of the core calendar/course services return formatted date strings,
 * so every fetch path enriches its events with this afterwards.
 *
 * @param timestamps timestamps to format.
 * @param formatStringKey langconfig string identifier naming the strftime format to use.
 */
async function getFormattedTimestamps(timestamps: number[], formatStringKey: string): Promise<Map<number, string>> {
    const unique = [...new Set(timestamps)];
    if (unique.length === 0) {
        return new Map();
    }
    const format = await getString(formatStringKey, 'langconfig');
    const [result] = await fetchMany<{dates: string[]}>([{
        methodname: 'core_get_user_dates',
        args: {
            contextid: config.contextid ?? 1,
            timestamps: unique.map(ts => ({timestamp: ts, format})),
        },
    }]);
    return new Map(unique.map((ts, i) => [ts, result.dates[i]]));
}

/**
 * Fetch server-formatted day strings (no time) for the given midnight timestamps.
 *
 * @param timestamps midnight timestamps to format, one per distinct day.
 */
export const getFormattedDays = (timestamps: number[]): Promise<Map<number, string>> =>
    getFormattedTimestamps(timestamps, 'strftimedaydate');

/**
 * Fetch server-formatted full date+time strings for the given event timestamps.
 *
 * Used for the event link's accessible name, matching legacy's use of the
 * strftimedatetime format in event-list-item.mustache's ariaeventlistitem string.
 *
 * @param timestamps event timesort timestamps to format.
 */
export const getFormattedEventDateTimes = (timestamps: number[]): Promise<Map<number, string>> =>
    getFormattedTimestamps(timestamps, 'strftimedatetime');
