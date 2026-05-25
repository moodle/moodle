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
 * Tests for the AMD module mocking utilities defined in .jest/globalSetup.ts.
 *
 * Note: We are unable to test the actual amd module loading here, as Jest's
 * module system and the AMD loader are separate and do not interact in a way
 * that allows for testing real module loading.
 *
 * Instead, we test that our mock implementations of requireAsync and
 * requireManyAsync behave as expected when fetching mocked modules and throw
 * errors for unmocked modules.
 *
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {requireAsync, requireManyAsync} from '@moodle/lms/core/amd';

describe('@moodle/lms/core/amd', () => {
    it('throws an error when fetching an unmocked module via requireAsync', () => {
        expect(() => {
            requireAsync('unmocked/module');
        }).toThrow('Unexpected call to requireAsync with module name: unmocked/module');
    });

    it('throws an error when fetching an unmocked module via requireManyAsync', () => {
        expect(() => {
            requireManyAsync(['unmocked/module']);
        }).toThrow('Unexpected call to requireManyAsync with module name: unmocked/module');
    });

    it('returns a mocked module registered via mockAmdModule', async() => {
        const myModule = {hello: 'world'};
        mockAmdModule('my/module', myModule);

        await expect(requireAsync('my/module')).resolves.toBe(myModule);
    });

    it('returns multiple mocked modules via requireManyAsync', async() => {
        const modA = {name: 'a'};
        const modB = {name: 'b'};
        mockAmdModule('my/modA', modA);
        mockAmdModule('my/modB', modB);

        await expect(requireManyAsync(['my/modA', 'my/modB'])).resolves.toEqual([modA, modB]);
    });
});
