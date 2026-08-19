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
 * Jest tests for the useAriaLabels hook.
 *
 * @module     block_timeline/tests/useAriaLabels
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {renderHook, waitFor} from '@testing-library/react';
import * as stringUtils from '@moodle/lms/core/stringUtils';
import {useAriaLabels} from '../src/common/useAriaLabels';

describe('useAriaLabels', () => {
    const getStringSpy = jest.spyOn(stringUtils, 'getString');


    it('fetches the button label from the given buttonKey', async() => {
        (globalThis as any).mockString('ariabuttonkey', 'block_timeline', 'Filter by date');

        const {result} = renderHook(() => useAriaLabels('ariabuttonkey', 'ariaitemkey', []));

        await waitFor(() => expect(result.current.buttonLabel).toBe('Filter by date'));
    });

    it('composes each option label into the item aria label via itemAriaKey', async() => {
        (globalThis as any).mockString('ariabuttonkey', 'block_timeline', 'Filter by date');
        (globalThis as any).mockString('overdue', 'block_timeline', 'Overdue');
        (globalThis as any).mockString('ariaitemkey', 'block_timeline', 'Filter option');

        const {result} = renderHook(() => useAriaLabels('ariabuttonkey', 'ariaitemkey', [
            {name: 'overdue', labelKey: 'overdue'},
        ]));

        // The mocked getRequestedStrings ignores the `param` value, so assert on the wiring
        // (which identifier/component/param getString was called with) rather than the
        // substituted output — substitution is getString's own responsibility, not this hook's.
        await waitFor(() => expect(result.current.itemLabels.overdue).toBe('Filter option'));
        expect(stringUtils.getString).toHaveBeenCalledWith('ariaitemkey', 'block_timeline', 'Overdue');
    });

    it('reads each option label from its own labelComponent when provided', async() => {
        (globalThis as any).mockString('ariabuttonkey', 'block_timeline', 'Sort');
        (globalThis as any).mockString('all', 'core', 'All');
        (globalThis as any).mockString('ariaitemkey', 'block_timeline', 'Sort option');

        renderHook(() => useAriaLabels('ariabuttonkey', 'ariaitemkey', [
            {name: 'all', labelKey: 'all', labelComponent: 'core'},
        ]));

        await waitFor(() => expect(stringUtils.getString).toHaveBeenCalledWith('all', 'core'));
    });
});
