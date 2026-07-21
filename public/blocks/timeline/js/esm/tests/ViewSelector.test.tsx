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
 * Jest tests for the ViewSelector (sort-by-dates / sort-by-courses) dropdown.
 *
 * @module     block_timeline/tests/ViewSelector
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {act, render, screen, waitFor, fireEvent} from '@testing-library/react';
import ViewSelector from '../src/nav/ViewSelector';

async function renderSelector(...args: Parameters<typeof render>) {
    let result!: ReturnType<typeof render>;
    await act(async() => {
        result = render(...args);
    });
    return result;
}

// Every option shares the same mocked aria-label, which overrides its accessible
// name for role queries — so items are located by data-filtername instead.
function optionFor(container: HTMLElement, filtername: string): HTMLElement {
    const el = container.querySelector(`[data-filtername="${filtername}"]`);
    if (!el) {
        throw new Error(`No option found for filtername "${filtername}"`);
    }
    return el as HTMLElement;
}

beforeEach(() => {
    (globalThis as any).mockString('ariaviewselector', 'block_timeline', 'Sort by');
    (globalThis as any).mockString('ariaviewselectoroption', 'block_timeline', 'option');
    (globalThis as any).mockString('sortbydates', 'block_timeline', 'Sort by dates');
    (globalThis as any).mockString('sortbycourses', 'block_timeline', 'Sort by courses');
});

describe('ViewSelector', () => {
    it('shows the active order label in the toggle button', async() => {
        await renderSelector(<ViewSelector activeOrder="sortbydates" onChange={jest.fn()} />);

        await waitFor(() => {
            expect(screen.getByRole('button')).toHaveTextContent('Sort by dates');
        });
    });

    it('marks the active option with aria-current', async() => {
        const {container} = await renderSelector(<ViewSelector activeOrder="sortbycourses" onChange={jest.fn()} />);

        expect(optionFor(container, 'sortbycourses')).toHaveAttribute('aria-current', 'true');
        expect(optionFor(container, 'sortbydates')).not.toHaveAttribute('aria-current');
    });

    it('calls onChange with the clicked option\'s order name', async() => {
        const onChange = jest.fn();
        const {container} = await renderSelector(<ViewSelector activeOrder="sortbydates" onChange={onChange} />);

        fireEvent.click(optionFor(container, 'sortbycourses'));

        expect(onChange).toHaveBeenCalledWith('sortbycourses');
    });

    it('renders both sort options', async() => {
        const {container} = await renderSelector(<ViewSelector activeOrder="sortbydates" onChange={jest.fn()} />);

        expect(optionFor(container, 'sortbydates')).toHaveTextContent('Sort by dates');
        expect(optionFor(container, 'sortbycourses')).toHaveTextContent('Sort by courses');
    });
});
