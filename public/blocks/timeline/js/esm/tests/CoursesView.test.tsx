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
 * Jest tests for the CoursesView drain-until-visible course pagination logic.
 *
 * @module     block_timeline/tests/CoursesView
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import type {ReactNode} from 'react';
import {act, render, screen, waitFor, fireEvent} from '@testing-library/react';
import CoursesView from '../src/views/CoursesView';
import type {CalendarEvent, FilterOffsets} from '../src/common/types';

jest.mock('@moodlehq/design-system', () => ({
    Button: (props: {
        label: string; onClick: () => void; disabled?: boolean; 'data-action': string; endIcon?: ReactNode;
    }) => (
        <button data-action={props['data-action']} disabled={props.disabled} onClick={props.onClick}>
            {props.label}
            {props.endIcon}
        </button>
    ),
}), {virtual: true});

jest.mock('@moodle/lms/block_timeline/views/EventListItem', () => ({
    __esModule: true,
    'default': ({event}: {event: CalendarEvent}) => <div data-testid="event-item">{event.name}</div>,
}), {virtual: true});

const mockGetEnrolledCourses = jest.fn();
const mockGetEventsByCourses = jest.fn();
const mockGetEventsByCourse = jest.fn();
const mockGetFormattedDays = jest.fn();
jest.mock('../src/repository', () => ({
    getEnrolledCourses: (...args: unknown[]) => mockGetEnrolledCourses(...args),
    getEventsByCourses: (...args: unknown[]) => mockGetEventsByCourses(...args),
    getEventsByCourse: (...args: unknown[]) => mockGetEventsByCourse(...args),
    getFormattedDays: (...args: unknown[]) => mockGetFormattedDays(...args),
}));

const MIDNIGHT = 1_700_000_000;
const OFFSETS: FilterOffsets = {daysoffset: -400, dayslimit: null, filteroverdue: false};

interface Course {
    id: number;
    fullname: string;
    shortname: string;
    viewurl: string;
    courseimage: string;
}

function makeCourse(id: number): Course {
    return {id, fullname: `Course ${id}`, shortname: `C${id}`, viewurl: `/course/${id}`, courseimage: ''};
}

function makeEvent(id: number, courseId: number): CalendarEvent {
    return {
        id,
        name: `Event ${id}`,
        timesort: MIDNIGHT,
        timeusermidnight: MIDNIGHT,
        formattedday: '',
        overdue: false,
        eventtype: 'due',
        url: `/event/${id}`,
        modulename: 'quiz',
        course: {
            id: courseId,
            fullname: `Course ${courseId}`,
            fullnamedisplay: `Course ${courseId}`,
            viewurl: `/course/${courseId}`,
        },
    };
}

/** Mimics core_course_get_enrolled_courses_by_timeline_classification pagination. */
function mockCoursePages(allCourses: Course[]) {
    mockGetEnrolledCourses.mockImplementation(async({limit, offset}: {limit: number; offset: number}) => {
        const courses = allCourses.slice(offset, offset + limit);
        return {courses, nextoffset: offset + courses.length};
    });
}

/** Mimics core_calendar_get_action_events_by_courses grouping. */
function mockEventsByCourseId(eventsByCourseId: Record<number, CalendarEvent[]>) {
    mockGetEventsByCourses.mockImplementation(async({courseids}: {courseids: number[]}) => ({
        groupedbycourse: courseids.map(courseid => ({courseid, events: eventsByCourseId[courseid] ?? []})),
    }));
}

const defaultProps = {
    midnight: MIDNIGHT,
    offsets: OFFSETS,
    searchvalue: '',
    nocoursesurl: '/nocourses.png',
    noeventsurl: '/noevents.png',
    hasenrolledcourses: true,
};

async function renderView(props: Partial<typeof defaultProps> = {}) {
    let result!: ReturnType<typeof render>;
    await act(async() => {
        result = render(<CoursesView {...defaultProps} {...props} />);
    });
    return result;
}

beforeEach(() => {
    mockGetEnrolledCourses.mockReset();
    mockGetEventsByCourses.mockReset();
    mockGetEventsByCourse.mockReset();
    mockGetFormattedDays.mockReset();
    (globalThis as any).mockString('noevents', 'block_timeline', 'No activities require action');
    (globalThis as any).mockString('nocoursesinprogress', 'block_timeline', 'No in-progress courses');
    (globalThis as any).mockString('moreactivities', 'block_timeline', 'Show more activities');
    (globalThis as any).mockString('morecourses', 'block_timeline', 'Show more courses');
    mockGetFormattedDays.mockImplementation(async(timestamps: number[]) =>
        new Map(timestamps.map(ts => [ts, 'Thursday, 1 January 2026'])));
});

describe('CoursesView', () => {
    it('hides a course whose events are all filtered out, keeping only visible courses', async() => {
        mockCoursePages([makeCourse(1), makeCourse(2)]);
        mockEventsByCourseId({"1": [makeEvent(1, 1)], "2": []});

        await renderView();

        expect(screen.getByText('Course 1')).toBeInTheDocument();
        expect(screen.queryByText('Course 2')).not.toBeInTheDocument();
    });

    it('drains a second WS page when the first page has no visible courses', async() => {
        // Page 1 (offset 0, limit 3) returns 3 courses -> hasMorePhp=true, keep first 2, both empty.
        // Page 2 (offset 2, limit 3) returns 1 course -> hasMorePhp=false, has events.
        mockCoursePages([makeCourse(1), makeCourse(2), makeCourse(3), makeCourse(4)]);
        mockEventsByCourseId({"1": [], "2": [], "3": [makeEvent(10, 3)], "4": [makeEvent(11, 4)]});

        await renderView();

        expect(mockGetEnrolledCourses).toHaveBeenCalledTimes(2);
        expect(screen.getByText('Course 3')).toBeInTheDocument();
    });

    it('shows the no-in-progress-courses empty state when there are no enrolled courses', async() => {
        mockCoursePages([]);
        mockEventsByCourseId({});

        await renderView({hasenrolledcourses: false});

        expect(screen.getByText('No in-progress courses')).toBeInTheDocument();
    });

    it('shows the no-events empty state when courses exist but none have visible events', async() => {
        mockCoursePages([makeCourse(1)]);
        mockEventsByCourseId({"1": []});

        await renderView({hasenrolledcourses: true});

        expect(screen.getByText('No activities require action')).toBeInTheDocument();
    });

    it('appends the preloaded next batch when "Show more courses" is clicked', async() => {
        // 4 courses, 2 per page: batch1=[1,2] shown immediately, batch2=[3,4] preloaded.
        mockCoursePages([makeCourse(1), makeCourse(2), makeCourse(3), makeCourse(4)]);
        mockEventsByCourseId({
            "1": [makeEvent(1, 1)], "2": [makeEvent(2, 2)], "3": [makeEvent(3, 3)], "4": [makeEvent(4, 4)],
        });

        await renderView();

        expect(screen.getByText('Course 1')).toBeInTheDocument();
        expect(screen.getByText('Course 2')).toBeInTheDocument();
        expect(screen.queryByText('Course 3')).not.toBeInTheDocument();

        await act(async() => {
            fireEvent.click(screen.getByRole('button', {name: 'Show more courses'}));
        });

        await waitFor(() => {
            expect(screen.getByText('Course 3')).toBeInTheDocument();
            expect(screen.getByText('Course 4')).toBeInTheDocument();
        });
    });

    it('appends more events for a single course when its "Show more activities" is clicked', async() => {
        // 7 events on first load (EVENTS_PER_PAGE=6 + 1 sentinel) so hasMore is true.
        const firstPage = Array.from({length: 7}, (_, i) => makeEvent(i + 1, 1));
        mockCoursePages([makeCourse(1)]);
        mockEventsByCourseId({"1": firstPage});
        mockGetEventsByCourse.mockResolvedValue({events: [makeEvent(100, 1)]});

        await renderView();

        expect(screen.getAllByTestId('event-item')).toHaveLength(6);

        await act(async() => {
            fireEvent.click(screen.getByRole('button', {name: 'Show more activities'}));
        });

        await waitFor(() => {
            expect(mockGetEventsByCourse).toHaveBeenCalledWith(expect.objectContaining({courseid: 1}));
            expect(screen.getAllByTestId('event-item')).toHaveLength(7);
        });
    });

    it('keeps "Show more courses" visible but disabled (no spinner) while the following batch pre-loads', async() => {
        // The clicked batch is already pre-loaded, so the reveal is instant — there is
        // nothing to show a spinner for. Matching legacy, the button stays visible and
        // merely disables while the next batch pre-loads in the background, which is
        // what prevents a double-click from re-appending the same batch.
        const courses = [1, 2, 3, 4, 5, 6].map(makeCourse);
        mockCoursePages(courses);
        mockEventsByCourseId(Object.fromEntries(courses.map(c => [String(c.id), [makeEvent(c.id, c.id)]])));

        await renderView();

        // Hold the following page's fetch pending so the in-between state is observable.
        let resolveNextPage!: (value: {courses: Course[]; nextoffset: number}) => void;
        mockGetEnrolledCourses.mockImplementation(() => new Promise(resolve => {
            resolveNextPage = resolve;
        }));

        const button = screen.getByRole('button', {name: 'Show more courses'});
        fireEvent.click(button);

        expect(screen.getByText('Course 3')).toBeInTheDocument();
        expect(screen.getByText('Course 4')).toBeInTheDocument();
        expect(button).toBeInTheDocument();
        expect(button).toBeDisabled();
        expect(button.querySelector('.spinner-border')).not.toBeInTheDocument();

        await act(async() => {
            resolveNextPage({courses: [makeCourse(5), makeCourse(6)], nextoffset: 6});
        });

        await waitFor(() => expect(button).not.toBeDisabled());
        expect(screen.getAllByText('Course 3')).toHaveLength(1);
        expect(screen.getAllByText('Course 4')).toHaveLength(1);
    });

    it('shows the loading spinner inside the per-course "Show more activities" button while loading', async() => {
        const firstPage = Array.from({length: 7}, (_, i) => makeEvent(i + 1, 1));
        mockCoursePages([makeCourse(1)]);
        mockEventsByCourseId({"1": firstPage});
        mockGetEventsByCourse.mockResolvedValue({events: [makeEvent(100, 1)]});

        await renderView();

        const button = screen.getByRole('button', {name: 'Show more activities'});
        expect(button.querySelector('.spinner-border')).not.toBeInTheDocument();

        fireEvent.click(button);

        const spinner = button.querySelector('.spinner-border');
        expect(spinner).toBeInTheDocument();
        expect(spinner!.previousSibling?.textContent).toBe('Show more activities');

        await waitFor(() => expect(button.querySelector('.spinner-border')).not.toBeInTheDocument());
    });
});
