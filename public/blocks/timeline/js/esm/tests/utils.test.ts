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
 * Jest tests for the Timeline block shared calendar utilities.
 *
 * @module     block_timeline/tests/utils
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {computeTimeRange, groupByDay, filterEvents, SECONDS_IN_DAY} from '../src/common/utils';
import type {CalendarEvent} from '../src/common/types';

const MIDNIGHT = 1_700_000_000;

function makeEvent(overrides: Partial<CalendarEvent>): CalendarEvent {
    return {
        id: 1,
        name: 'Event',
        timesort: MIDNIGHT,
        timeusermidnight: MIDNIGHT,
        formattedday: 'Monday, 1 January 2026',
        overdue: false,
        eventtype: 'due',
        url: '/event',
        modulename: 'assign',
        course: {id: 1, fullname: 'Course 1', fullnamedisplay: 'Course 1', viewurl: '/course/1'},
        ...overrides,
    };
}

describe('computeTimeRange', () => {
    it('adds daysoffset to midnight for starttime', () => {
        const {starttime} = computeTimeRange(MIDNIGHT, {daysoffset: -7, dayslimit: null, filteroverdue: false});
        expect(starttime).toBe(MIDNIGHT - 7 * SECONDS_IN_DAY);
    });

    it('returns null endtime when dayslimit is null', () => {
        const {endtime} = computeTimeRange(MIDNIGHT, {daysoffset: 0, dayslimit: null, filteroverdue: false});
        expect(endtime).toBeNull();
    });

    it('adds dayslimit to midnight for endtime when set', () => {
        const {endtime} = computeTimeRange(MIDNIGHT, {daysoffset: 0, dayslimit: 30, filteroverdue: false});
        expect(endtime).toBe(MIDNIGHT + 30 * SECONDS_IN_DAY);
    });
});

describe('groupByDay', () => {
    it('groups events sharing a timeusermidnight into one bucket', () => {
        const events = [
            makeEvent({id: 1, timeusermidnight: MIDNIGHT}),
            makeEvent({id: 2, timeusermidnight: MIDNIGHT}),
            makeEvent({id: 3, timeusermidnight: MIDNIGHT + SECONDS_IN_DAY}),
        ];

        const groups = groupByDay(events);

        expect(groups).toHaveLength(2);
        expect(groups[0].events.map(e => e.id)).toEqual([1, 2]);
        expect(groups[1].events.map(e => e.id)).toEqual([3]);
    });

    it('sorts groups ascending by day timestamp regardless of input order', () => {
        const events = [
            makeEvent({id: 1, timeusermidnight: MIDNIGHT + 2 * SECONDS_IN_DAY}),
            makeEvent({id: 2, timeusermidnight: MIDNIGHT}),
        ];

        const groups = groupByDay(events);

        expect(groups.map(g => g.dayTimestamp)).toEqual([MIDNIGHT, MIDNIGHT + 2 * SECONDS_IN_DAY]);
    });

    it('returns an empty array for no events', () => {
        expect(groupByDay([])).toEqual([]);
    });
});

describe('filterEvents', () => {
    it('excludes open events not yet due', () => {
        const events = [
            makeEvent({id: 1, eventtype: 'open', timeusermidnight: MIDNIGHT}),
            makeEvent({id: 2, eventtype: 'open', timeusermidnight: MIDNIGHT + SECONDS_IN_DAY}),
        ];

        const result = filterEvents(events, MIDNIGHT, false);

        expect(result.map(e => e.id)).toEqual([2]);
    });

    it('excludes opensubmission events not yet due, same as open', () => {
        const events = [makeEvent({id: 1, eventtype: 'opensubmission', timeusermidnight: MIDNIGHT})];

        expect(filterEvents(events, MIDNIGHT, false)).toEqual([]);
    });

    it('keeps all non-open events when filteroverdue is false', () => {
        const events = [
            makeEvent({id: 1, eventtype: 'due', overdue: false}),
            makeEvent({id: 2, eventtype: 'due', overdue: true}),
        ];

        expect(filterEvents(events, MIDNIGHT, false).map(e => e.id)).toEqual([1, 2]);
    });

    it('keeps only overdue events when filteroverdue is true', () => {
        const events = [
            makeEvent({id: 1, eventtype: 'due', overdue: false}),
            makeEvent({id: 2, eventtype: 'due', overdue: true}),
        ];

        expect(filterEvents(events, MIDNIGHT, true).map(e => e.id)).toEqual([2]);
    });
});
