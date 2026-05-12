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
 * Tests for the core/pending ESM module.
 *
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Pending from '@moodle/lms/core/pending';

describe('core/pending', () => {
    describe('Pending.pending', () => {
        it('calls M.util.js_pending with the given key', () => {
            Pending.pending('test/static-pending');

            expect(pendingStack).toContain('test/static-pending');
        });
    });

    describe('Pending.complete', () => {
        it('calls M.util.js_complete with the given key', () => {
            Pending.complete('test/static-complete');

            expect(completeStack).toContain('test/static-complete');
        });
    });

    describe('constructor', () => {
        it('is thenable (duck-typed Promise)', () => {
            const pending = new Pending('test/constructor');

            expect(typeof pending.then).toBe('function');
            expect(typeof pending.catch).toBe('function');

            // Resolve to prevent unhandled rejection.
            pending.resolve();
        });

        it('calls js_pending with the given key', () => {
            const pending = new Pending('test/pending-key');

            expect(pendingStack).toContain('test/pending-key');

            pending.resolve();
        });

        it('uses default key when none is provided', () => {
            const pending = new Pending();

            expect(pendingStack).toContain('pendingPromise');

            pending.resolve();
        });

        it('has a resolve method', () => {
            const pending = new Pending('test/resolve');

            expect(typeof pending.resolve).toBe('function');

            pending.resolve();
        });

        it('has a reject method', () => {
            const pending = new Pending('test/reject');

            expect(typeof pending.reject).toBe('function');

            pending.resolve();
        });

        it('calls js_complete when resolved', async() => {
            const pending = new Pending('test/complete');

            pending.resolve('done');
            await pending;

            expect(completeStack).toContain('test/complete');
        });

        it('resolves with the value passed to resolve', async() => {
            const pending = new Pending('test/value');

            pending.resolve('hello');

            await expect(pending).resolves.toBe('hello');
        });

        it('rejects with the reason passed to reject', async() => {
            const pending = new Pending('test/rejection');

            const result = pending.catch((e: unknown) => e);
            pending.reject('failure');

            await expect(result).resolves.toBe('failure');
        });
    });

    describe('Pending.Promise', () => {
        it('calls js_pending with the given key', () => {
            Pending.Promise((resolve) => resolve('ok'), 'test/static-key');

            expect(pendingStack).toContain('test/static-key');
        });

        it('uses default key when none is provided', () => {
            Pending.Promise((resolve) => resolve('ok'));

            expect(pendingStack).toContain('pendingPromise');
        });

        it('returns a Promise', () => {
            const result = Pending.Promise((resolve) => resolve('ok'), 'test/returns-promise');

            expect(result).toBeInstanceOf(Promise);
        });

        it('resolves with the value from the executor', async() => {
            const result = Pending.Promise<string>((resolve) => resolve('value'), 'test/resolve-value');

            await expect(result).resolves.toBe('value');
        });

        it('calls js_complete after resolution', async() => {
            const result = Pending.Promise((resolve) => resolve('ok'), 'test/complete-static');

            await result;
            // Allow the .then() chain to flush.
            await new Promise((resolve) => process.nextTick(resolve));

            expect(completeStack).toContain('test/complete-static');
        });

        it('rejects when the executor calls reject', async() => {
            const result = Pending.Promise((_, reject) => reject('error'), 'test/reject-static');

            await expect(result).rejects.toBe('error');
        });

        it('does not call js_complete on rejection', async() => {
            const result = Pending.Promise((_, reject) => reject('error'), 'test/no-complete');

            await result.catch(() => {}); // eslint-disable-line no-empty-function
            await new Promise((resolve) => process.nextTick(resolve));

            expect(completeStack).toHaveLength(0);
        });

        it('passes resolve and reject to the executor', () => {
            const executorSpy = jest.fn();

            Pending.Promise(executorSpy, 'test/executor');

            expect(executorSpy).toHaveBeenCalledTimes(1);
            expect(typeof executorSpy.mock.calls[0][0]).toBe('function');
            expect(typeof executorSpy.mock.calls[0][1]).toBe('function');

            // Resolve to clean up.
            executorSpy.mock.calls[0][0]();
        });
    });
});
