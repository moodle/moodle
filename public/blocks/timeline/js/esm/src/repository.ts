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
 * Data-access layer for the Timeline block — wraps block_timeline and core_calendar web services.
 *
 * @module     block_timeline/repository
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {fetchOne} from '@moodle/lms/core/ajax';
import type {CalendarEvent, CourseWithEvents} from './types';

/** Options for fetching timeline events (dates view). */
export interface GetTimelineEventsArgs {
    timesortfrom?: number;
    timesortto?: number | null;
    aftereventid?: number;
    limitnum?: number;
    searchvalue?: string | null;
}

/** Options for fetching courses with events (courses view). */
export interface GetCoursesWithEventsArgs {
    starttime?: number | null;
    endtime?: number | null;
    limit?: number;
    offset?: number;
    searchvalue?: string | null;
}

/** Response from block_timeline_get_timeline_events. */
export interface TimelineEventsResponse {
    events: CalendarEvent[];
}

/** Response from block_timeline_get_courses_with_events. */
export interface CoursesWithEventsResponse {
    courses: CourseWithEvents[];
    nextoffset: number;
    morecoursesavailable: boolean;
}

/**
 * Fetch action events for the timeline dates view.
 *
 * Calls block_timeline_get_timeline_events.
 */
export const getTimelineEvents = (args: GetTimelineEventsArgs): Promise<TimelineEventsResponse> => {
    return fetchOne<TimelineEventsResponse>({
        methodname: 'block_timeline_get_timeline_events',
        args: {
            timesortfrom: args.timesortfrom ?? 0,
            timesortto:   args.timesortto ?? null,
            aftereventid: args.aftereventid ?? 0,
            limitnum:     args.limitnum ?? 20,
            searchvalue:  args.searchvalue ?? null,
        },
    });
};

/**
 * Fetch enrolled in-progress courses with their action events for the courses view.
 *
 * Calls block_timeline_get_courses_with_events.
 */
export const getCoursesWithEvents = (args: GetCoursesWithEventsArgs): Promise<CoursesWithEventsResponse> => {
    return fetchOne<CoursesWithEventsResponse>({
        methodname: 'block_timeline_get_courses_with_events',
        args: {
            starttime:   args.starttime ?? null,
            endtime:     args.endtime ?? null,
            limit:       args.limit ?? 2,
            offset:      args.offset ?? 0,
            searchvalue: args.searchvalue ?? null,
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
