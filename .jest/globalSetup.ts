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
 * Global setup for Jest tests, providing utilities for mocking AMD modules and strings.
 *
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {requireAsync, requireManyAsync} from '@moodle/lms/core/amd';
import {resetStringCache} from '@moodle/lms/core/String';

declare global {
    function mockAmdModule(moduleName: string, module: object): void;
    function mockString(identifier: string, component: string, resolved: string): void;
    function mockPendingString(identifier: string, component: string): void;
    /** Keys passed to `M.util.js_pending()` since the last reset. */
    var pendingStack: string[];
    /** Keys passed to `M.util.js_complete()` since the last reset. */
    var completeStack: string[];
}

const mockedModules = new Map<string, unknown>();
const stringMap = new Map<string, string>();

/**
 * @var pendingStringSet - A set of string keys that should return a never-resolving promise.
 */
const pendingStringSet = new Set<string>();

/**
 * @var pendingStack - Tracks keys passed to `M.util.js_pending()` in the current test.
 */
const pendingStack: string[] = [];

/**
 * @var completeStack - Tracks keys passed to `M.util.js_complete()` in the current test.
 */
const completeStack: string[] = [];

(globalThis as any).pendingStack = pendingStack;
(globalThis as any).completeStack = completeStack;

// Provide the global M object with cfg defaults and tracked js_pending/js_complete mocks.
const defaultCfg = {
    wwwroot: 'https://example.com',
    apibase: 'https://example.com',
    homeurl: '/',
    sesskey: 'test-sesskey',
    sessiontimeout: 7200,
    sessiontimeoutwarning: 1200,
    themerev: 1,
    slasharguments: 1,
    theme: 'boost',
    iconsystemmodule: 'core/icon_system_fontawesome',
    jsrev: -1,
    admin: 'admin',
    svgicons: true,
    usertimezone: 'Australia/Perth',
    language: 'en',
    courseId: 0,
    courseContextId: 0,
    contextid: 1,
    contextInstanceId: 0,
    langrev: 1,
    templaterev: 1,
    siteId: 1,
    userId: 2,
    deprecationignorelist: [],
    traceId: 'test-trace-id',
    developerdebug: true,
};

(globalThis as any).M = {
    cfg: {...defaultCfg},
    util: {
        js_pending: jest.fn((key: string) => {
            pendingStack.push(key);
        }),
        js_complete: jest.fn((key: string) => {
            completeStack.push(key);
        }),
    },
};

// Mock the global functions for mocking AMD modules and strings, making them available in all test files.

jest.mock('@moodle/lms/core/amd');

beforeEach(() => {
    pendingStack.length = 0;
    completeStack.length = 0;
    Object.assign((globalThis as any).M.cfg, defaultCfg);

    resetStringCache();

    // Provide a mock implementation for requireAsync to return mocked modules when requested.
    // If a module is not mocked, it throws an error to indicate an unexpected call.
    (requireAsync as jest.Mock).mockImplementation((name: string) => {
        if (mockedModules.has(name)) {
            return Promise.resolve(mockedModules.get(name));
        }
        throw new Error(`Unexpected call to requireAsync with module name: ${name}`);
    });

    (requireManyAsync as jest.Mock).mockImplementation((names: string[]) => {
        const modules = names.map(name => {
            if (mockedModules.has(name)) {
                return mockedModules.get(name);
            }
            throw new Error(`Unexpected call to requireManyAsync with module name: ${name}`);
        });
        return Promise.resolve(modules);
    });

    (global as any).mockAmdModule = (moduleName: string, module: object) => {
        mockedModules.set(moduleName, module);
    };

    // Register a default core/str mock. Returns '[identifier, component]' for unmocked strings.
    (global as any).mockAmdModule('core/str', {
        // eslint-disable-next-line camelcase
        get_string: jest.fn((identifier: string, component?: string) => {
            const key = `${component}:${identifier}`;
            if (stringMap.has(key)) {
                return Promise.resolve(stringMap.get(key));
            }
            return Promise.resolve(`[${identifier}, ${component}]`);
        }),
    });

    (global as any).mockString = (identifier: string, component: string, resolved: string): void => {
        stringMap.set(`${component}:${identifier}`, resolved);
    };
});

afterEach(() => {
    mockedModules.clear();
    stringMap.clear();
});
