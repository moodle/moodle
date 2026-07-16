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
 * Shared calendar utilities for the Timeline block.
 *
 * @module     block_timeline/common/utils
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import type {CalendarEvent, FilterOffsets} from './types';

/** Number of seconds in a day, used to convert day offsets into timestamp ranges. */
export const SECONDS_IN_DAY = 86400;

/** Events grouped under a single day, keyed by that day's midnight timestamp. */
export interface DayGroup {
    dayTimestamp: number;
    events: CalendarEvent[];
}

/**
 * Derive WS starttime and endtime from midnight and filter offsets.
 *
 * @param midnight start-of-day timestamp the offsets are relative to.
 * @param offsets day offset/limit pair for the active filter.
 */
export function computeTimeRange(midnight: number, offsets: FilterOffsets): {starttime: number; endtime: number | null} {
    return {
        starttime: midnight + offsets.daysoffset * SECONDS_IN_DAY,
        endtime:   offsets.dayslimit !== null
            ? midnight + offsets.dayslimit * SECONDS_IN_DAY
            : null,
    };
}

/**
 * Group a flat event list into per-day buckets sorted ascending by day timestamp.
 *
 * @param events events to group.
 */
export function groupByDay(events: CalendarEvent[]): DayGroup[] {
    const map = new Map<number, CalendarEvent[]>();
    for (const event of events) {
        const day = event.timeusermidnight;
        if (!map.has(day)) {
            map.set(day, []);
        }
        map.get(day)!.push(event);
    }
    return Array.from(map.entries())
        .sort(([a], [b]) => a - b)
        .map(([dayTimestamp, evts]) => ({dayTimestamp, events: evts}));
}

/**
 * Client-side event filter matching the original event_list.js behaviour:
 * open/opensubmission events due at or before midnight are excluded, and
 * when filteroverdue is true only events with event.overdue=true pass.
 *
 * @param events events to filter.
 * @param midnight start-of-day timestamp used to exclude not-yet-due open events.
 * @param filteroverdue whether only overdue events should be kept.
 */
export function filterEvents(events: CalendarEvent[], midnight: number, filteroverdue: boolean): CalendarEvent[] {
    return events.filter(event => {
        if (event.eventtype === 'open' || event.eventtype === 'opensubmission') {
            return event.timeusermidnight > midnight;
        }
        return !filteroverdue || event.overdue;
    });
}
