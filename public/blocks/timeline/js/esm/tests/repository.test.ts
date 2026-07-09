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
 * Jest tests for the Timeline block repository module.
 *
 * @module     block_timeline/tests/repository
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getTimelineEvents, getCoursesWithEvents} from '../src/repository';

// Mock for fetchOne(request) -> Promise<T> — single request, single response.
const mockFetchOne = jest.fn();
jest.mock('@moodle/lms/core/ajax', () => ({
    fetchOne: (...args: unknown[]) => mockFetchOne(...args),
}));

describe('repository', () => {
    beforeEach(() => {
        mockFetchOne.mockClear();
    });

    describe('getTimelineEvents', () => {
        it('calls fetchOne with correct methodname and returns events', async() => {
            const events = [{id: 1, name: 'Event 1'}];
            mockFetchOne.mockResolvedValue({events});

            const result = await getTimelineEvents({timesortfrom: 1000, limitnum: 5});

            expect(mockFetchOne).toHaveBeenCalledTimes(1);
            const request = mockFetchOne.mock.calls[0][0];
            expect(request.methodname).toBe('block_timeline_get_timeline_events');
            expect(request.args.timesortfrom).toBe(1000);
            expect(request.args.limitnum).toBe(5);
            expect(result.events).toEqual(events);
        });

        it('applies correct defaults for missing optional args', async() => {
            mockFetchOne.mockResolvedValue({events: []});

            await getTimelineEvents({});

            const {args} = mockFetchOne.mock.calls[0][0];
            expect(args.timesortfrom).toBe(0);
            expect(args.timesortto).toBeNull();
            expect(args.aftereventid).toBe(0);
            expect(args.limitnum).toBe(20);
            expect(args.searchvalue).toBeNull();
        });

        it('passes searchvalue when provided', async() => {
            mockFetchOne.mockResolvedValue({events: []});

            await getTimelineEvents({searchvalue: 'quiz'});

            expect(mockFetchOne.mock.calls[0][0].args.searchvalue).toBe('quiz');
        });

        it('propagates rejection from fetchOne', async() => {
            mockFetchOne.mockRejectedValue(new Error('Network error'));
            await expect(getTimelineEvents({})).rejects.toThrow('Network error');
        });
    });

    describe('getCoursesWithEvents', () => {
        it('calls fetchOne with correct methodname and returns courses', async() => {
            const response = {courses: [], nextoffset: 0, morecoursesavailable: false};
            mockFetchOne.mockResolvedValue(response);

            const result = await getCoursesWithEvents({limit: 2, offset: 0});

            expect(mockFetchOne).toHaveBeenCalledTimes(1);
            const request = mockFetchOne.mock.calls[0][0];
            expect(request.methodname).toBe('block_timeline_get_courses_with_events');
            expect(request.args.limit).toBe(2);
            expect(request.args.offset).toBe(0);
            expect(result).toEqual(response);
        });

        it('applies correct defaults for missing optional args', async() => {
            mockFetchOne.mockResolvedValue({courses: [], nextoffset: 0, morecoursesavailable: false});

            await getCoursesWithEvents({});

            const {args} = mockFetchOne.mock.calls[0][0];
            expect(args.starttime).toBeNull();
            expect(args.endtime).toBeNull();
            expect(args.limit).toBe(2);
            expect(args.offset).toBe(0);
            expect(args.searchvalue).toBeNull();
        });

        it('passes starttime and endtime when provided', async() => {
            mockFetchOne.mockResolvedValue({courses: [], nextoffset: 0, morecoursesavailable: false});

            await getCoursesWithEvents({starttime: 1000, endtime: 2000});

            const {args} = mockFetchOne.mock.calls[0][0];
            expect(args.starttime).toBe(1000);
            expect(args.endtime).toBe(2000);
        });
    });
});
