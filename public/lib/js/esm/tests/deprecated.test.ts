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
 * Tests for the core/deprecated ESM module.
 *
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import emitDeprecation from '@moodle/lms/core/deprecated';

// Spy on console.error so tests don't pollute output and can assert calls.
// eslint-disable-next-line no-empty-function
const consoleError = jest.spyOn(console, 'error').mockImplementation(() => {});

afterAll(() => {
    consoleError.mockRestore();
});

/** Mock returned by requireAsync('core/notification'). */
const mockNotificationAlert = jest.fn();

beforeEach(() => {
    consoleError.mockClear();
    mockNotificationAlert.mockClear();
    mockAmdModule('core/notification', {alert: mockNotificationAlert});

    // Ensure developer debug is on so canEmit() returns true by default.
    (globalThis as any).M.cfg.developerdebug = true;
});

describe('@moodle/lms/core/deprecated', () => {

    // ── Validation ─────────────────────────────────────────────────────────

    describe('argument validation', () => {
        it('throws when no replacement, reason or mdl is supplied', () => {
            expect(() => emitDeprecation('myFn')).toThrow(
                'You must provide at least one of replacement, reason or mdl',
            );
        });

        it('does not throw when only replacement is supplied', () => {
            expect(() => emitDeprecation('myFn', {replacement: 'newFn'})).not.toThrow(
                'You must provide at least one of replacement, reason or mdl',
            );
        });

        it('does not throw when only reason is supplied', () => {
            expect(() => emitDeprecation('myFn', {reason: 'Removed.'})).not.toThrow(
                'You must provide at least one of replacement, reason or mdl',
            );
        });

        it('does not throw when only mdl is supplied', () => {
            expect(() => emitDeprecation('myFn', {mdl: 'MDL-12345'})).not.toThrow(
                'You must provide at least one of replacement, reason or mdl',
            );
        });
    });

    // ── Plain-text message ─────────────────────────────────────────────────

    describe('console message', () => {
        it('includes "Deprecation:" prefix', () => {
            emitDeprecation('myFn', {replacement: 'newFn'});
            expect(consoleError).toHaveBeenCalledWith(expect.stringContaining('Deprecation:'));
        });

        it('includes the thing name', () => {
            emitDeprecation('myFn', {replacement: 'newFn'});
            expect(consoleError).toHaveBeenCalledWith(expect.stringContaining('myFn'));
        });

        it('includes the replacement', () => {
            emitDeprecation('myFn', {replacement: 'newFn'});
            expect(consoleError).toHaveBeenCalledWith(expect.stringContaining('newFn'));
        });

        it('includes the since version', () => {
            emitDeprecation('myFn', {replacement: 'newFn', since: '5.0'});
            expect(consoleError).toHaveBeenCalledWith(expect.stringContaining('since 5.0'));
        });

        it('includes the reason', () => {
            emitDeprecation('myFn', {reason: 'No longer needed.'});
            expect(consoleError).toHaveBeenCalledWith(expect.stringContaining('No longer needed.'));
        });

        it('includes the mdl reference', () => {
            emitDeprecation('myFn', {mdl: 'MDL-12345'});
            expect(consoleError).toHaveBeenCalledWith(expect.stringContaining('MDL-12345'));
        });

        it('uses alternativeNotice instead of default phrasing', () => {
            emitDeprecation('myFn', {alternativeNotice: 'Custom notice text', replacement: 'newFn'});
            const msg: string = consoleError.mock.calls[0][0];
            expect(msg).toContain('Custom notice text');
            expect(msg).not.toContain('myFn has been deprecated');
        });
    });

    // ── Toast notification ─────────────────────────────────────────────────

    describe('toast notification', () => {
        afterEach(() => {
            document.body.classList.remove('behat-site');
        });

        it('calls notification.alert when developerdebug is true', async() => {
            emitDeprecation('myFn', {replacement: 'newFn'});
            await Promise.resolve(); // Flush microtasks
            expect(mockNotificationAlert).toHaveBeenCalledWith(
                'Deprecation Warning',
                expect.stringContaining('myFn'),
                expect.any(Promise),
            );
        });

        it('calls notification.alert on a behat site even without developerdebug', async() => {
            (globalThis as any).M.cfg.developerdebug = false;
            document.body.classList.add('behat-site');

            emitDeprecation('myFn', {replacement: 'newFn'});
            await Promise.resolve();
            expect(mockNotificationAlert).toHaveBeenCalled();
        });

        it('does not call notification.alert when developerdebug is false and not behat', async() => {
            (globalThis as any).M.cfg.developerdebug = false;

            emitDeprecation('myFn', {replacement: 'newFn'});
            await Promise.resolve();
            expect(mockNotificationAlert).not.toHaveBeenCalled();
        });

        it('does not call notification.alert when thing is in the ignore list', async() => {
            (globalThis as any).M.cfg.deprecationignorelist = ['myFn'];

            emitDeprecation('myFn', {replacement: 'newFn'});
            await Promise.resolve();
            expect(mockNotificationAlert).not.toHaveBeenCalled();
        });

        it('still logs to console when thing is ignored', () => {
            (globalThis as any).M.cfg.deprecationignorelist = ['myFn'];

            emitDeprecation('myFn', {replacement: 'newFn'});
            expect(consoleError).toHaveBeenCalled();
        });

        it('does not call notification.alert when emit=false', async() => {
            emitDeprecation('myFn', {replacement: 'newFn', emit: false});
            await Promise.resolve();
            expect(mockNotificationAlert).not.toHaveBeenCalled();
        });

        it('HTML message contains the thing name', async() => {
            emitDeprecation('myFn', {replacement: 'newFn'});
            await Promise.resolve();
            const html: string = mockNotificationAlert.mock.calls[0][1];
            expect(html).toContain('myFn');
        });

        it('HTML message contains an MDL link', async() => {
            emitDeprecation('myFn', {mdl: 'MDL-12345'});
            await Promise.resolve();
            const html: string = mockNotificationAlert.mock.calls[0][1];
            expect(html).toContain('href="https://moodle.atlassian.net/browse/MDL-12345"');
        });
    });

    // ── Final deprecation ──────────────────────────────────────────────────

    describe('final deprecation', () => {
        it('throws an Error', () => {
            expect(() => emitDeprecation('myFn', {replacement: 'newFn', "final": true}))
                .toThrow('myFn has been deprecated');
        });

        it('still calls notification.alert before throwing', async() => {
            try {
                emitDeprecation('myFn', {replacement: 'newFn', "final": true});
            } catch {
                // Expected
            }
            await Promise.resolve();
            expect(mockNotificationAlert).toHaveBeenCalled();
        });

        it('calls notification.alert even when developerdebug is false', async() => {
            (globalThis as any).M.cfg.developerdebug = false;
            try {
                emitDeprecation('myFn', {replacement: 'newFn', "final": true});
            } catch {
                // Expected
            }
            await Promise.resolve();
            expect(mockNotificationAlert).toHaveBeenCalled();
        });

        it('does not call console.error (throws instead)', () => {
            try {
                emitDeprecation('myFn', {replacement: 'newFn', "final": true});
            } catch {
                // Expected
            }
            expect(consoleError).not.toHaveBeenCalled();
        });
    });
});
