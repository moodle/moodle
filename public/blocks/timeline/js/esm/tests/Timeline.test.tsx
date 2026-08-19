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
 * Jest tests for the root Timeline React component.
 *
 * @module     block_timeline/tests/Timeline
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {render, screen, waitFor, fireEvent} from '@testing-library/react';
import Timeline from '../src/Timeline';
import type {TimelineProps} from '../src/common/types';

// Mock child views to isolate Timeline state logic.
jest.mock('../src/views/DatesView', () => ({
    __esModule: true,
    'default': ({offsets}: {offsets: {filteroverdue: boolean}}) => (
        <div data-testid="dates-view" data-filteroverdue={String(offsets.filteroverdue)} />
    ),
}));
jest.mock('../src/views/CoursesView', () => ({
    __esModule: true,
    'default': () => <div data-testid="courses-view" />,
}));
jest.mock('../src/nav/DayFilter', () => ({
    __esModule: true,
    'default': ({activeFilter, onChange}: {activeFilter: string; onChange: (f: string) => void}) => (
        <button data-testid="day-filter" data-active={activeFilter} onClick={() => onChange('overdue')}>
            {activeFilter}
        </button>
    ),
}));
jest.mock('../src/nav/ViewSelector', () => ({
    __esModule: true,
    'default': ({activeOrder, onChange}: {activeOrder: string; onChange: (o: string) => void}) => (
        <button data-testid="view-selector" data-active={activeOrder} onClick={() => onChange('sortbycourses')}>
            {activeOrder}
        </button>
    ),
}));
jest.mock('../src/nav/Search', () => ({
    __esModule: true,
    'default': ({onSearch}: {onSearch: (v: string) => void}) => (
        <input data-testid="search" onChange={(e) => onSearch(e.target.value)} />
    ),
}));
const defaultProps: TimelineProps = {
    midnight: 1000000,
    filter: 'next30days',
    order: 'sortbydates',
    limit: 5,
    nocoursesurl: '/nocourses.png',
    noeventsurl: '/noevents.png',
    hasenrolledcourses: true,
};

describe('Timeline', () => {
    it('renders the DatesView by default when order is sortbydates', () => {
        render(<Timeline {...defaultProps} />);
        expect(screen.getByTestId('dates-view')).toBeInTheDocument();
        expect(screen.queryByTestId('courses-view')).not.toBeInTheDocument();
    });

    it('renders the CoursesView when order is sortbycourses', () => {
        render(<Timeline {...defaultProps} order="sortbycourses" />);
        expect(screen.getByTestId('courses-view')).toBeInTheDocument();
        expect(screen.queryByTestId('dates-view')).not.toBeInTheDocument();
    });

    it('switches to CoursesView when ViewSelector fires sortbycourses', async() => {
        render(<Timeline {...defaultProps} />);
        expect(screen.getByTestId('dates-view')).toBeInTheDocument();

        fireEvent.click(screen.getByTestId('view-selector'));

        expect(screen.getByTestId('courses-view')).toBeInTheDocument();
        expect(screen.queryByTestId('dates-view')).not.toBeInTheDocument();
    });

    it('passes filteroverdue=true to DatesView when filter is overdue', async() => {
        render(<Timeline {...defaultProps} />);

        fireEvent.click(screen.getByTestId('day-filter'));

        await waitFor(() => {
            const datesView = screen.getByTestId('dates-view');
            expect(datesView.getAttribute('data-filteroverdue')).toBe('true');
        });
    });

});
