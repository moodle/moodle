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
 * Jest tests for the EventListItem component.
 *
 * @module     block_timeline/tests/EventListItem
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {act, render, screen, waitFor} from '@testing-library/react';
import EventListItem from '../src/views/EventListItem';
import type {CalendarEvent} from '../src/common/types';

// EventListItem resolves its "Overdue" label asynchronously on mount (getString());
// flush that microtask so every test sees a settled render, not just the ones that
// specifically assert on the label.
async function renderItem(...args: Parameters<typeof render>): Promise<ReturnType<typeof render>> {
    let result!: ReturnType<typeof render>;
    await act(async() => {
        result = render(...args);
    });
    return result;
}

// Mock the design system (ESM-only export, unresolvable by Jest's CJS resolver) and
// ActivityIcon (its own icon-resolution logic is covered by ActivityIcon.test.tsx).
jest.mock('@moodlehq/design-system', () => ({
    Badge: (props: {label: string; variant: string}) => (
        <span data-testid="badge" data-variant={props.variant}>{props.label}</span>
    ),
}), {virtual: true});
jest.mock('@moodle/lms/block_timeline/views/ActivityIcon', () => ({
    __esModule: true,
    ActivityIcon: () => <span data-testid="activity-icon" />,
}), {virtual: true});

function makeEvent(overrides: Partial<CalendarEvent> = {}): CalendarEvent {
    return {
        id: 1,
        name: 'Quiz 1',
        timesort: Date.UTC(2026, 0, 1, 9, 30) / 1000,
        timeusermidnight: Date.UTC(2026, 0, 1) / 1000,
        formattedday: 'Thursday, 1 January 2026',
        overdue: false,
        eventtype: 'due',
        url: '/mod/quiz/view.php?id=1',
        modulename: 'quiz',
        activityname: 'Quiz 1',
        activitystr: 'Quiz is due',
        course: {id: 2, fullname: 'Course 2', fullnamedisplay: 'Course 2', viewurl: '/course/2'},
        ...overrides,
    };
}

describe('EventListItem', () => {
    it('renders the activity name as a link to the event url', async() => {
        await renderItem(<EventListItem event={makeEvent()} />);
        const link = screen.getByRole('link', {name: 'Quiz 1'});
        expect(link).toHaveAttribute('href', '/mod/quiz/view.php?id=1');
    });

    it('shows the course name when not in courseview mode', async() => {
        await renderItem(<EventListItem event={makeEvent()} />);
        expect(screen.getByText(/Course 2/)).toBeInTheDocument();
    });

    it('hides the course name in courseview mode', async() => {
        await renderItem(<EventListItem event={makeEvent()} courseview />);
        expect(screen.queryByText(/Course 2/)).not.toBeInTheDocument();
    });

    it('renders the icon container only when the event has an icon', async() => {
        const {rerender} = await renderItem(<EventListItem event={makeEvent()} />);
        expect(screen.queryByTestId('activity-icon')).not.toBeInTheDocument();

        await act(async() => {
            rerender(<EventListItem event={makeEvent({
                icon: {key: 'monologo', component: 'mod_quiz', alttext: 'Quiz', iconurl: '/quiz.svg', iconclass: '', purpose: ''},
            })} />);
        });
        expect(screen.getByTestId('activity-icon')).toBeInTheDocument();
    });

    it('shows an overdue badge when the event is overdue', async() => {
        (globalThis as any).mockString('overdue', 'block_timeline', 'Overdue');
        await renderItem(<EventListItem event={makeEvent({overdue: true})} />);

        await waitFor(() => {
            const badge = screen.getByTestId('badge');
            expect(badge).toHaveAttribute('data-variant', 'danger');
            expect(badge).toHaveTextContent('Overdue');
        });
    });

    it('does not show an overdue badge when the event is not overdue', async() => {
        await renderItem(<EventListItem event={makeEvent({overdue: false})} />);
        expect(screen.queryByTestId('badge')).not.toBeInTheDocument();
    });

    it('shows the action button with an item-count badge when showitemcount is true', async() => {
        await renderItem(<EventListItem event={makeEvent({
            action: {name: 'View', url: '/action', itemcount: 3, actionable: true, showitemcount: true},
        })} />);

        expect(screen.getByRole('link', {name: 'View'})).toBeInTheDocument();
        expect(screen.getByTestId('badge')).toHaveTextContent('3');
    });

    it('does not render the action button when the action is not actionable', async() => {
        await renderItem(<EventListItem event={makeEvent({
            action: {name: 'View', url: '/action', itemcount: 3, actionable: false, showitemcount: true},
        })} />);

        expect(screen.queryByRole('link', {name: 'View'})).not.toBeInTheDocument();
    });
});
