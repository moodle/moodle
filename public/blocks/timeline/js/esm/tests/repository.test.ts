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

import {getTimelineEvents, getEnrolledCourses, getEventsByCourses} from '../src/repository';

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
            expect(request.methodname).toBe('core_calendar_get_action_events_by_timesort');
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

    describe('getEnrolledCourses', () => {
        it('calls fetchOne with correct methodname, classification, and sort', async() => {
            const response = {courses: [], nextoffset: 0};
            mockFetchOne.mockResolvedValue(response);

            const result = await getEnrolledCourses({limit: 3, offset: 0});

            expect(mockFetchOne).toHaveBeenCalledTimes(1);
            const request = mockFetchOne.mock.calls[0][0];
            expect(request.methodname).toBe('core_course_get_enrolled_courses_by_timeline_classification');
            expect(request.args.classification).toBe('all');
            expect(request.args.sort).toBe('fullname ASC');
            expect(request.args.limit).toBe(3);
            expect(request.args.offset).toBe(0);
            expect(result).toEqual(response);
        });

        it('applies correct defaults for missing optional args', async() => {
            mockFetchOne.mockResolvedValue({courses: [], nextoffset: 0});

            await getEnrolledCourses({});

            const {args} = mockFetchOne.mock.calls[0][0];
            expect(args.limit).toBe(2);
            expect(args.offset).toBe(0);
            expect(args.searchvalue).toBeNull();
        });
    });

    describe('getEventsByCourses', () => {
        it('calls fetchOne with correct methodname and courseids', async() => {
            const response = {groupedbycourse: [{courseid: 5, events: []}]};
            mockFetchOne.mockResolvedValue(response);

            const result = await getEventsByCourses({courseids: [5, 6], limitnum: 7});

            expect(mockFetchOne).toHaveBeenCalledTimes(1);
            const request = mockFetchOne.mock.calls[0][0];
            expect(request.methodname).toBe('core_calendar_get_action_events_by_courses');
            expect(request.args.courseids).toEqual([5, 6]);
            expect(request.args.limitnum).toBe(7);
            expect(result).toEqual(response);
        });
    });
});
