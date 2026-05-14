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
 * Tests for the String module, which provides an ESM wrapper around the AMD core/str module for loading Moodle language strings.
 *
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {render, screen, act} from '@testing-library/react';
import String, {getString, getStrings, getRequestedStrings, cacheStrings} from '@moodle/lms/core/String';
import {requireAsync} from '@moodle/lms/core/amd';

describe('@moodle/lms/core/String', () => {
    describe('getString', () => {
        it('returns the default value for an unmocked string', async() => {
            await expect(getString('other', 'core')).resolves.toBe('[other, core]');
        });

        it('returns the default value using core component when no component is specified', async() => {
            await expect(getString('save')).resolves.toBe('[save, core]');
        });

        it('returns the mocked value when a string has been mocked', async() => {
            mockString('pluginname', 'mod_forum', 'Forum');
            mockString('submit', 'core', 'Submit');

            await expect(getString('pluginname', 'mod_forum')).resolves.toBe('Forum');
            await expect(getString('submit', 'core')).resolves.toBe('Submit');
        });

        it('returns the default for strings not matching any mock', async() => {
            mockString('submit', 'core', 'Submit');

            await expect(getString('cancel', 'core')).resolves.toBe('[cancel, core]');
        });

        it('returns a stable promise reference for the same key', () => {
            const promise1 = getString('stable', 'core');
            const promise2 = getString('stable', 'core');

            expect(promise1).toBe(promise2);
        });
    });

    describe('getRequestedStrings', () => {
        beforeEach(() => {
            // Restore original implementation to test the actual batching logic, which is mocked in globalSetup.
            getRequestedStrings.mockRestore();

            requireAsync.mockImplementation((moduleName) => {
                if (moduleName === 'core/ajax') {
                    return Promise.resolve({
                        call: (
                            requests,
                        ) => {
                            return requests.map(({args}) => {
                                return Promise.resolve(`[${args.stringid}, ${args.component || 'core'}]`);
                            });
                        },
                    });
                }
                return Promise.reject(new Error(`Module not found: ${moduleName}`));
            });
        });

        it('returns individual promises for each request', async() => {
            mockString('one', 'core', 'One');

            const promises = getRequestedStrings([
                {key: 'one', component: 'core'},
                {key: 'two', component: 'core'},
            ]);

            expect(promises).toHaveLength(2);
            await expect(promises[0]).resolves.toBe('One');
            await expect(promises[1]).resolves.toBe('[two, core]');
        });

        it('deduplicates requests for the same string', async() => {
            const promises = getRequestedStrings([
                {key: 'dup', component: 'core'},
                {key: 'dup', component: 'core'},
            ]);

            const [first, second] = await Promise.all(promises);
            expect(first).toBe('[dup, core]');
            expect(second).toBe('[dup, core]');
        });

        it('defaults component to core', async() => {
            mockString('yes', 'core', 'Yes');

            const promises = getRequestedStrings([{key: 'yes'}]);
            await expect(promises[0]).resolves.toBe('Yes');
        });

        it('defaults component to core when component is an empty string', async() => {
            mockString('yes', 'core', 'Yes');

            const promises = getRequestedStrings([{key: 'yes', component: ''}]);
            await expect(promises[0]).resolves.toBe('Yes');
        });
    });

    describe('getStrings', () => {
        it('returns all strings in a single promise', async() => {
            mockString('yes', 'core', 'Yes');
            mockString('no', 'core', 'No');

            const result = await getStrings([
                {key: 'yes', component: 'core'},
                {key: 'no', component: 'core'},
            ]);

            expect(result).toEqual(['Yes', 'No']);
        });

        it('returns defaults for unmocked strings', async() => {
            const result = await getStrings([
                {key: 'missing', component: 'core'},
            ]);

            expect(result).toEqual(['[missing, core]']);
        });

        it('handles a mix of cached and uncached strings', async() => {
            mockString('cached', 'core', 'Cached Value');

            const result = await getStrings([
                {key: 'cached', component: 'core'},
                {key: 'uncached', component: 'core'},
            ]);

            expect(result).toEqual(['Cached Value', '[uncached, core]']);
        });
    });

    describe('cacheStrings', () => {
        beforeEach(() => {
            // Restore original implementation to test the actual batching logic, which is mocked in globalSetup.
            getRequestedStrings.mockRestore();
        });

        it('makes subsequent getString calls resolve from cache', async() => {
            cacheStrings([
                {key: 'precached', component: 'mod_forum', value: 'Pre-cached'},
            ]);

            await expect(getString('precached', 'mod_forum')).resolves.toBe('Pre-cached');
        });

        it('does not overwrite existing cached values', async() => {
            mockString('existing', 'core', 'Original');

            cacheStrings([
                {key: 'existing', component: 'core', value: 'New Value'},
            ]);

            await expect(getString('existing', 'core')).resolves.toBe('Original');
        });

        it('defaults component to core', async() => {
            cacheStrings([
                {key: 'defaultcomp', value: 'Default Component'},
            ]);

            await expect(getString('defaultcomp', 'core')).resolves.toBe('Default Component');
        });
    });

    describe('<String> component', () => {
        it('renders the resolved string', async() => {
            mockString('greeting', 'core', 'Hello World');

            await act(async() => {
                render(<String identifier="greeting" component="core" />);
            });

            expect(screen.getByText('Hello World')).toBeInTheDocument();
        });

        it('renders the default fallback while suspended', async() => {
            mockPendingString('loading', 'mod_quiz');

            await act(async() => {
                render(<String identifier="loading" component="mod_quiz" />);
            });

            expect(screen.getByText('loading, mod_quiz')).toBeInTheDocument();
        });

        it('renders custom children as fallback while suspended', async() => {
            mockPendingString('loading', 'core');

            await act(async() => {
                render(
                    <String identifier="loading" component="core">
                        <span>Loading...</span>
                    </String>,
                );
            });

            expect(screen.getByText('Loading...')).toBeInTheDocument();
        });

        it('defaults component to core', async() => {
            mockString('save', 'core', 'Save');

            await act(async() => {
                render(<String identifier="save" />);
            });

            expect(screen.getByText('Save')).toBeInTheDocument();
        });
    });
});
