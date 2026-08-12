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
 * Jest tests for the DatesView day-grouping and lazy-load pagination logic.
 *
 * @module     block_timeline/tests/DatesView
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import type {ReactNode} from 'react';
import {act, render, screen, waitFor, fireEvent} from '@testing-library/react';
import DatesView from '../src/views/DatesView';
import type {CalendarEvent, FilterOffsets} from '../src/common/types';

jest.mock('@moodlehq/design-system', () => ({
    Button: (props: {label: string; onClick: () => void; 'data-action': string; endIcon?: ReactNode}) => (
        <button data-action={props['data-action']} onClick={props.onClick}>
            {props.label}
            {props.endIcon}
        </button>
    ),
}), {virtual: true});

jest.mock('@moodle/lms/block_timeline/views/EventListItem', () => ({
    __esModule: true,
    'default': ({event}: {event: CalendarEvent}) => <div data-testid="event-item">{event.name}</div>,
}), {virtual: true});

const mockGetTimelineEvents = jest.fn();
const mockGetFormattedDays = jest.fn();
const mockGetFormattedEventDateTimes = jest.fn();
jest.mock('../src/repository', () => ({
    getTimelineEvents: (...args: unknown[]) => mockGetTimelineEvents(...args),
    getFormattedDays: (...args: unknown[]) => mockGetFormattedDays(...args),
    getFormattedEventDateTimes: (...args: unknown[]) => mockGetFormattedEventDateTimes(...args),
}));

const MIDNIGHT = 1_700_000_000;
const DAY = 86400;
const OFFSETS: FilterOffsets = {daysoffset: -400, dayslimit: null, filteroverdue: false};

function makeEvent(id: number, dayTimestamp: number): CalendarEvent {
    return {
        id,
        name: `Event ${id}`,
        timesort: dayTimestamp,
        timeusermidnight: dayTimestamp,
        formattedday: '',
        formatteddatetime: '',
        overdue: false,
        eventtype: 'due',
        url: `/event/${id}`,
        modulename: 'quiz',
        course: {id: 1, fullname: 'Course 1', fullnamedisplay: 'Course 1', viewurl: '/course/1'},
    };
}

const defaultProps = {
    midnight: MIDNIGHT,
    offsets: OFFSETS,
    searchvalue: '',
    nocoursesurl: '/nocourses.png',
    noeventsurl: '/noevents.png',
    hasenrolledcourses: true,
    limit: 5,
};

async function renderView(props: Partial<typeof defaultProps> = {}) {
    let result!: ReturnType<typeof render>;
    await act(async() => {
        result = render(<DatesView {...defaultProps} {...props} />);
    });
    return result;
}

beforeEach(() => {
    mockGetTimelineEvents.mockReset();
    mockGetFormattedDays.mockReset();
    mockGetFormattedEventDateTimes.mockReset();
    (globalThis as any).mockString('nocoursesinprogress', 'block_timeline', 'No in-progress courses');
    (globalThis as any).mockString('noevents', 'block_timeline', 'No activities require action');
    (globalThis as any).mockString('moreactivities', 'block_timeline', 'Show more activities');
    mockGetFormattedDays.mockImplementation(async(timestamps: number[]) =>
        new Map(timestamps.map(ts => [ts, `Day ${ts}`])));
    mockGetFormattedEventDateTimes.mockImplementation(async(timestamps: number[]) =>
        new Map(timestamps.map(ts => [ts, `DateTime ${ts}`])));
});

describe('DatesView', () => {
    it('requests limit+1 events on first load and pops the sentinel when there are more', async() => {
        const events = Array.from({length: 6}, (_, i) => makeEvent(i + 1, MIDNIGHT));
        mockGetTimelineEvents.mockResolvedValue({events});

        await renderView({limit: 5});

        expect(mockGetTimelineEvents).toHaveBeenCalledWith(expect.objectContaining({limitnum: 6}));
        expect(screen.getAllByTestId('event-item')).toHaveLength(5);
        expect(screen.getByRole('button', {name: 'Show more activities'})).toBeInTheDocument();
    });

    it('does not show "Show more" when all events fit within the limit', async() => {
        mockGetTimelineEvents.mockResolvedValue({events: [makeEvent(1, MIDNIGHT)]});

        await renderView({limit: 5});

        expect(screen.queryByRole('button', {name: 'Show more activities'})).not.toBeInTheDocument();
    });

    it('shows the loading spinner inside the "Show more activities" button, after the label, while loading', async() => {
        const first = Array.from({length: 6}, (_, i) => makeEvent(i + 1, MIDNIGHT));
        mockGetTimelineEvents
            .mockResolvedValueOnce({events: first})
            .mockResolvedValueOnce({events: [makeEvent(100, MIDNIGHT)]});

        await renderView({limit: 5});

        const button = screen.getByRole('button', {name: 'Show more activities'});
        expect(button.querySelector('.spinner-border')).not.toBeInTheDocument();

        fireEvent.click(button);

        // Spinner is a child of the button (endIcon), rendered after the label text node.
        const spinner = button.querySelector('.spinner-border');
        expect(spinner).toBeInTheDocument();
        expect(spinner!.previousSibling?.textContent).toBe('Show more activities');

        await waitFor(() => expect(button.querySelector('.spinner-border')).not.toBeInTheDocument());
    });

    it('groups events under separate days when timeusermidnight differs', async() => {
        mockGetTimelineEvents.mockResolvedValue({
            events: [makeEvent(1, MIDNIGHT), makeEvent(2, MIDNIGHT + DAY)],
        });

        await renderView({limit: 5});

        expect(screen.getByText('Day 1700000000')).toBeInTheDocument();
        expect(screen.getByText(`Day ${MIDNIGHT + DAY}`)).toBeInTheDocument();
    });

    it('merges a "Show more" continuation into the last day when it shares the same timestamp', async() => {
        // Limit=2 requests limitnum=3; 3 same-day events means the 3rd is the sentinel
        // (popped, not shown), leaving hasMore=true after only 2 are displayed.
        mockGetTimelineEvents
            .mockResolvedValueOnce({events: [makeEvent(1, MIDNIGHT), makeEvent(2, MIDNIGHT), makeEvent(3, MIDNIGHT)]})
            .mockResolvedValueOnce({events: [makeEvent(4, MIDNIGHT)]});

        await renderView({limit: 2});
        expect(screen.getAllByTestId('event-item')).toHaveLength(2);

        await act(async() => {
            fireEvent.click(screen.getByRole('button', {name: 'Show more activities'}));
        });

        await waitFor(() => {
            // Still a single day group (merged), now containing both the original and the new event.
            expect(screen.getAllByText(/^Day /)).toHaveLength(1);
            expect(screen.getAllByTestId('event-item')).toHaveLength(3);
        });
    });

    it('appends a new day group when "Show more" returns events on a different day', async() => {
        const first = Array.from({length: 6}, (_, i) => makeEvent(i + 1, MIDNIGHT));
        mockGetTimelineEvents
            .mockResolvedValueOnce({events: first})
            .mockResolvedValueOnce({events: [makeEvent(200, MIDNIGHT + DAY)]});

        await renderView({limit: 5});

        await act(async() => {
            fireEvent.click(screen.getByRole('button', {name: 'Show more activities'}));
        });

        await waitFor(() => {
            expect(screen.getAllByText(/^Day /)).toHaveLength(2);
        });
    });

    it('pages from the last loaded event id on "Show more"', async() => {
        const first = Array.from({length: 6}, (_, i) => makeEvent(i + 1, MIDNIGHT));
        mockGetTimelineEvents
            .mockResolvedValueOnce({events: first})
            .mockResolvedValueOnce({events: [makeEvent(100, MIDNIGHT)]});

        await renderView({limit: 5});

        await act(async() => {
            fireEvent.click(screen.getByRole('button', {name: 'Show more activities'}));
        });

        await waitFor(() => {
            expect(mockGetTimelineEvents).toHaveBeenLastCalledWith(expect.objectContaining({aftereventid: 5}));
        });
    });

    it('shows the no-courses empty state when the user has no enrolled courses', async() => {
        mockGetTimelineEvents.mockResolvedValue({events: []});

        await renderView({hasenrolledcourses: false});

        expect(screen.getByText('No in-progress courses')).toBeInTheDocument();
    });

    it('shows the no-events empty state when enrolled but nothing is due', async() => {
        mockGetTimelineEvents.mockResolvedValue({events: []});

        await renderView({hasenrolledcourses: true});

        expect(screen.getByText('No activities require action')).toBeInTheDocument();
    });
});
