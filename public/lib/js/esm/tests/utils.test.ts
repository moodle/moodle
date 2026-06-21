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
 * Tests for the core/utils ESM module.
 *
 * @copyright  2026 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {
    debounce,
    getNormalisedComponent,
    throttle,
} from '@moodle/lms/core/utils';

describe('core/utils', () => {
    beforeEach(() => {
        jest.useFakeTimers();
    });

    afterEach(() => {
        jest.useRealTimers();
    });

    describe('throttle', () => {
        it('runs immediately on first call', () => {
            const fn = jest.fn();
            const throttled = throttle(fn, 100);

            throttled('first');

            expect(fn).toHaveBeenCalledTimes(1);
            expect(fn).toHaveBeenCalledWith('first');
        });

        it('coalesces multiple calls during cooldown into one trailing call', () => {
            const fn = jest.fn();
            const throttled = throttle(fn, 100);

            throttled('first');
            throttled('second');
            throttled('third');

            expect(fn).toHaveBeenCalledTimes(1);
            jest.advanceTimersByTime(100);
            expect(fn).toHaveBeenCalledTimes(2);
            expect(fn).toHaveBeenLastCalledWith('third');
        });
    });

    describe('debounce', () => {
        it('runs only once after the wait period', () => {
            const fn = jest.fn();
            const debounced = debounce(fn, 100);

            debounced('a');
            debounced('b');

            expect(fn).not.toHaveBeenCalled();
            jest.advanceTimersByTime(99);
            expect(fn).not.toHaveBeenCalled();
            jest.advanceTimersByTime(1);
            expect(fn).toHaveBeenCalledTimes(1);
            expect(fn).toHaveBeenCalledWith('b');
        });

        it('adds a cancel function when cancel option is enabled', () => {
            const fn = jest.fn();
            const debounced = debounce(fn, 100, {cancel: true});

            expect(typeof debounced.cancel).toBe('function');

            debounced('a');
            debounced.cancel?.();
            jest.advanceTimersByTime(100);

            expect(fn).not.toHaveBeenCalled();
        });

        it('registers and resolves pending when pending option is enabled', async() => {
            const fn = jest.fn();
            const debounced = debounce(fn, 100, {pending: true});

            debounced('a');

            expect(pendingStack).toContain('core/utils:debounce');
            expect(completeStack).toHaveLength(0);

            jest.advanceTimersByTime(100);
            // Flush the microtask queue twice to allow the debounce's promise chain to
            // complete. process.nextTick cannot be used here because jest.useFakeTimers()
            // fakes it; Promise microtasks are unaffected by fake timers.
            await Promise.resolve();
            await Promise.resolve();

            expect(fn).toHaveBeenCalledTimes(1);
            expect(completeStack).toContain('core/utils:debounce');
        });
    });

    describe('getNormalisedComponent', () => {
        it('returns core for empty, moodle, and core values', () => {
            expect(getNormalisedComponent('')).toBe('core');
            expect(getNormalisedComponent('moodle')).toBe('core');
            expect(getNormalisedComponent('core')).toBe('core');
        });

        it('returns non-core component names unchanged', () => {
            expect(getNormalisedComponent('mod_forum')).toBe('mod_forum');
        });
    });
});
