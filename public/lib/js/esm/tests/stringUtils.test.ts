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
 * Tests that the string-fetching utilities are usable directly from stringUtils.ts,
 * without importing the React-dependent String.tsx module.
 *
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getString, getStrings, cacheStrings, getRequestedStrings, getComponentStrings} from '@moodle/lms/core/stringUtils';
import Fetch from '@moodle/lms/core/fetch';

describe('@moodle/lms/core/stringUtils', () => {
    beforeEach(() => {
        // Restore the real implementation — the global mock only simulates a
        // simplified stringMap lookup, not the M.str/promiseCache logic under test here.
        (getRequestedStrings as jest.Mock).mockRestore();
    });

    it('resolves a single string without importing the React String component', async() => {
        mockString('greeting', 'core', 'Hello World');

        await expect(getString('greeting', 'core')).resolves.toBe('Hello World');
    });

    it('treats a null component as core, as untyped AMD callers pass it', async() => {
        mockString('greeting', 'core', 'Hello World');

        await expect(getStrings([
            {key: 'greeting', component: null},
        ])).resolves.toEqual(['Hello World']);
    });

    it('resolves a batch of strings', async() => {
        mockString('yes', 'core', 'Yes');
        mockString('no', 'core', 'No');

        await expect(getStrings([
            {key: 'yes', component: 'core'},
            {key: 'no', component: 'core'},
        ])).resolves.toEqual(['Yes', 'No']);
    });

    it('serves pre-cached strings without a fetch', async() => {
        cacheStrings([
            {key: 'precached', component: 'mod_forum', value: 'Pre-cached'},
        ]);

        await expect(getString('precached', 'mod_forum')).resolves.toBe('Pre-cached');
    });

    it('fetches uncached strings from the langstring REST API, batched via the Fetch batcher', async() => {
        const performGetSpy = jest.spyOn(Fetch.getBatcher(), 'performGet');
        performGetSpy.mockResolvedValue({
            json: () => Promise.resolve({strings: {'core/uncached': 'Fetched value'}}),
        } as unknown as Response);

        await expect(getString('uncached', 'core')).resolves.toBe('Fetched value');

        expect(performGetSpy).toHaveBeenCalledWith(
            'core',
            '/strings/en/core/uncached',
            {cachekey: 1},
        );
    });

    it('passes the language revision as the cache key when fetching uncached strings', async() => {
        (globalThis as any).M.cfg.langrev = 2;

        const performGetSpy = jest.spyOn(Fetch.getBatcher(), 'performGet');
        performGetSpy.mockResolvedValue({
            json: () => Promise.resolve({strings: {'core/revisioned': 'Revisioned value'}}),
        } as unknown as Response);

        await expect(getString('revisioned', 'core')).resolves.toBe('Revisioned value');

        expect(performGetSpy).toHaveBeenCalledWith(
            'core',
            '/strings/en/core/revisioned',
            {cachekey: 2},
        );

        (globalThis as any).M.cfg.langrev = 1;
    });

    it('fetches and caches all strings for a component via getComponentStrings', async() => {
        const performGetSpy = jest.spyOn(Fetch, 'performGet');
        performGetSpy.mockResolvedValue({
            json: () => Promise.resolve({
                strings: {
                    'mod_forum/modulename': 'Forum',
                    'mod_forum/modulenameplural': 'Forums',
                },
            }),
        } as unknown as Response);
        (globalThis as any).M.cfg.langrev = 2;

        await getComponentStrings('mod_forum', 'en');

        expect(performGetSpy).toHaveBeenCalledWith(
            'core',
            '/strings/en/mod_forum',
            {cachekey: 2},
        );

        (globalThis as any).M.cfg.langrev = 1;

        await expect(getString('modulename', 'mod_forum')).resolves.toBe('Forum');
        await expect(getString('modulenameplural', 'mod_forum')).resolves.toBe('Forums');
    });

    it('resolves to null when getComponentStrings fails to fetch', async() => {
        const performGetSpy = jest.spyOn(Fetch, 'performGet');
        performGetSpy.mockRejectedValue(new Error('Network error'));
        (globalThis as any).M.cfg.langrev = 2;

        await expect(getComponentStrings('mod_forum', 'en')).resolves.toBeNull();

        (globalThis as any).M.cfg.langrev = 1;
    });

    describe('params', () => {
        it('substitutes a single numeric param into {$a}', async() => {
            mockString('itemcount', 'core', 'You have {$a} items');

            await expect(getString('itemcount', 'core', 5)).resolves.toBe('You have 5 items');
        });

        it('substitutes object params into named {$a->key} placeholders', async() => {
            mockString('greeting', 'core', 'Hello {$a->name}, you are {$a->age} years old');

            await expect(getString('greeting', 'core', {name: 'Bob', age: 30}))
                .resolves.toBe('Hello Bob, you are 30 years old');
        });

        it('leaves placeholders unchanged when no params are provided', async() => {
            mockString('unfilled', 'core', 'Value: {$a}');

            await expect(getString('unfilled', 'core')).resolves.toBe('Value: {$a}');
        });

        it('leaves placeholders unchanged when params is explicitly null', async() => {
            mockString('nullparam', 'core', 'Static text');

            await expect(getString('nullparam', 'core', null)).resolves.toBe('Static text');
        });

        it('treats different params as distinct cache entries for the same key', async() => {
            mockString('perishable', 'core', 'Hi {$a}');

            await expect(getString('perishable', 'core', 'First')).resolves.toBe('Hi First');
            await expect(getString('perishable', 'core', 'Second')).resolves.toBe('Hi Second');
        });
    });
});
