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
 * Jest tests for the debounced Search input.
 *
 * @module     block_timeline/tests/Search
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {act, render, screen, fireEvent} from '@testing-library/react';
import Search from '../src/nav/Search';

async function renderSearch(...args: Parameters<typeof render>) {
    let result!: ReturnType<typeof render>;
    await act(async() => {
        result = render(...args);
    });
    return result;
}

beforeEach(() => {
    jest.useFakeTimers();
    (globalThis as any).mockString('searchevents', 'block_timeline', 'Search by activity type or name');
    (globalThis as any).mockString('clearsearch', 'core', 'Clear search');
});

afterEach(() => {
    jest.useRealTimers();
});

describe('Search', () => {
    it('calls onSearching(true) immediately on keystroke, before the debounce settles', async() => {
        const onSearching = jest.fn();
        await renderSearch(<Search onSearch={jest.fn()} onSearching={onSearching} />);

        fireEvent.change(screen.getByRole('textbox'), {target: {value: 'quiz'}});

        expect(onSearching).toHaveBeenCalledWith(true);
    });

    it('does not call onSearch until the debounce interval elapses', async() => {
        const onSearch = jest.fn();
        await renderSearch(<Search onSearch={onSearch} />);

        fireEvent.change(screen.getByRole('textbox'), {target: {value: 'quiz'}});
        expect(onSearch).not.toHaveBeenCalled();

        act(() => {
            jest.advanceTimersByTime(999);
        });
        expect(onSearch).not.toHaveBeenCalled();

        act(() => {
            jest.advanceTimersByTime(1);
        });
        expect(onSearch).toHaveBeenCalledWith('quiz');
    });

    it('calls onSearching(false) once the debounce settles', async() => {
        const onSearching = jest.fn();
        await renderSearch(<Search onSearch={jest.fn()} onSearching={onSearching} />);

        fireEvent.change(screen.getByRole('textbox'), {target: {value: 'quiz'}});
        act(() => {
            jest.advanceTimersByTime(1000);
        });

        expect(onSearching).toHaveBeenLastCalledWith(false);
    });

    it('resets the debounce timer on each keystroke, firing onSearch once with the latest value', async() => {
        const onSearch = jest.fn();
        await renderSearch(<Search onSearch={onSearch} />);
        const input = screen.getByRole('textbox');

        fireEvent.change(input, {target: {value: 'qu'}});
        act(() => {
            jest.advanceTimersByTime(600);
        });
        fireEvent.change(input, {target: {value: 'quiz'}});
        act(() => {
            jest.advanceTimersByTime(600);
        });
        expect(onSearch).not.toHaveBeenCalled();

        act(() => {
            jest.advanceTimersByTime(400);
        });
        expect(onSearch).toHaveBeenCalledTimes(1);
        expect(onSearch).toHaveBeenCalledWith('quiz');
    });

    it('hides the clear button when the input is empty', async() => {
        await renderSearch(<Search onSearch={jest.fn()} />);
        expect(screen.queryByRole('button')).not.toBeInTheDocument();
    });

    it('shows the clear button once there is a value, and clearing fires onSearch("") immediately', async() => {
        const onSearch = jest.fn();
        const onSearching = jest.fn();
        await renderSearch(<Search onSearch={onSearch} onSearching={onSearching} />);
        const input = screen.getByRole('textbox') as HTMLInputElement;

        fireEvent.change(input, {target: {value: 'quiz'}});
        const clearButton = screen.getByRole('button');

        fireEvent.click(clearButton);

        expect(input.value).toBe('');
        expect(onSearch).toHaveBeenCalledWith('');
        expect(onSearching).toHaveBeenLastCalledWith(false);
    });

    it('refocuses the input after clearing, since the clear button unmounts on click', async() => {
        await renderSearch(<Search onSearch={jest.fn()} />);
        const input = screen.getByRole('textbox');

        fireEvent.change(input, {target: {value: 'quiz'}});
        fireEvent.click(screen.getByRole('button'));

        expect(input).toHaveFocus();
    });

    it('does not fire a stale debounced onSearch after clearing', async() => {
        const onSearch = jest.fn();
        await renderSearch(<Search onSearch={onSearch} />);
        const input = screen.getByRole('textbox');

        fireEvent.change(input, {target: {value: 'quiz'}});
        fireEvent.click(screen.getByRole('button'));

        act(() => {
            jest.advanceTimersByTime(1000);
        });

        // Only the immediate clear call, no delayed 'quiz' call sneaking in afterwards.
        expect(onSearch).toHaveBeenCalledTimes(1);
        expect(onSearch).toHaveBeenCalledWith('');
    });
});
