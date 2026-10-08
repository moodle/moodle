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
 * Jest tests for the ActivityDates component.
 *
 * @module     core_course/tests/ActivityDates
 * @copyright  2026 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {render, screen} from '@testing-library/react';
import ActivityDates from '../src/ActivityDates';

jest.mock('@moodle/lms/core_course/ActivityDate', () => ({
    __esModule: true,
    'default': ({label, value}: {label: string; value: string}) => <span data-testid="date">{label} {value}</span>,
}), {virtual: true});

describe('ActivityDates', () => {
    it('renders nothing when there are no dates', () => {
        render(<ActivityDates dates={[]} />);
        expect(screen.queryAllByTestId('date')).toHaveLength(0);
    });

    it('renders a badge for each date', () => {
        render(<ActivityDates dates={[
            {label: 'Opens:', value: 'Monday, 1 January 2024'},
            {label: 'Closes:', value: 'Tuesday, 2 January 2024'},
        ]} />);
        expect(screen.getAllByTestId('date').map(date => date.textContent)).toEqual([
            'Opens: Monday, 1 January 2024',
            'Closes: Tuesday, 2 January 2024',
        ]);
    });
});
