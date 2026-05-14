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
import * as String from '@moodle/lms/core/String';
import * as Ajax from '@moodle/lms/core/ajax';
import { AjaxOptions, AjaxRequest } from '@moodle/lms/core/ajax';

type expectRedirectArgs =
| { url: string; urlContains?: never }
| { url?: never; urlContains: string };

declare global {
    function mockAmdModule(moduleName: string, module: object): void;
    function mockString(identifier: string, component: string, resolved: string): void;
    function mockPendingString(identifier: string, component: string): void;
    function expectRedirect(args: expectRedirectArgs): void;
    /** Keys passed to `M.util.js_pending()` since the last reset. */
    var pendingStack: string[];
    /** Keys passed to `M.util.js_complete()` since the last reset. */
    var completeStack: string[];
}

/**
 * @var mockedModules - A map to store mocked AMD modules by their name.
 */
const mockedModules = new Map<string, any>();

/**
 * @var stringMap - A map to store mocked strings with keys in the format 'component:identifier'.
 */
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
// NOTE: The `M` global is initially created in `.jest/globalM.ts` (via `setupFiles`)
// so that `M.cfg` is available when hoisted `jest.mock()` factories trigger `requireActual`.
// Here we store the default cfg snapshot and replace the util functions with jest.fn() mocks.
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
    currentlogin: null,
    deprecationignorelist: [],
    traceId: 'test-trace-id',
    developerdebug: true,
};

// Replace the placeholder util functions with proper jest.fn() mocks.
(globalThis as any).M.util = {
    js_pending: jest.fn((key: string) => {
        pendingStack.push(key);
    }),
    js_complete: jest.fn((key: string) => {
        completeStack.push(key);
    }),
    get_string: jest.fn((key: string, component: string, params?: Record<string, unknown>) => {
        if (!(globalThis as any).M.str[component] || !(globalThis as any).M.str[component][key]) {
            return `[${key}, ${component}]`;
        }

        const stringValue = (globalThis as any).M.str[component][key];

        if (!params) {
            return stringValue;
        }

        const normaliseParameter = (param: unknown): string => {
            if (typeof param === 'string') {
                return param;
            }
            if (typeof param === 'number' || typeof param === 'boolean') {
                return globalThis.String(param);
            }
            return JSON.stringify(param);
        };

        if (['string', 'number'].includes(typeof params)) {
            return (stringValue as string).replace(/{\$a}/g, normaliseParameter(params));
        }

        let result = stringValue;
        Object.entries(params).forEach(([placeholder, value]) => {
            result = (result as string).replace(new RegExp(`{\\$a->${placeholder}}`, 'g'), normaliseParameter(value));
        });

        return result;
    }),
};

// Mock the global functions for mocking AMD modules and strings, making them available in all test files.

jest.mock('@moodle/lms/core/amd');

beforeEach(() => {
    pendingStack.length = 0;
    completeStack.length = 0;
    Object.assign((globalThis as any).M.cfg, defaultCfg);

    (globalThis as any).M.str = {};

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

    (globalThis as any).mockAmdModule = (moduleName: string, module: object) => {
        mockedModules.set(moduleName, module);
    };

    // Register a default core/str mock. Returns '[identifier, component]' for unmocked strings.
    (globalThis as any).mockAmdModule('core/str', {
        // eslint-disable-next-line camelcase
        get_string: jest.fn((identifier: string, component?: string) => {
            const key = `${component}:${identifier}`;
            if (stringMap.has(key)) {
                return Promise.resolve(stringMap.get(key));
            }
            return Promise.resolve(`[${identifier}, ${component}]`);
        }),
    });

    const getRequestedStringsSpy = jest.spyOn(String, 'getRequestedStrings');
    (getRequestedStringsSpy as jest.SpyInstance).mockImplementation((requests: {key: string; component: string; param?: unknown}[]) => {
        return requests.map(({key, component}) => {
            const mapKey = `${component}:${key}`;
            if (pendingStringSet.has(mapKey)) {
                // Return a promise that never resolves to simulate a permanently pending string.
                return new Promise<string>(() => {});
            }
            if (stringMap.has(mapKey)) {
                return Promise.resolve(stringMap.get(mapKey)!);
            }
            return Promise.resolve(`[${key}, ${component}]`);
        });
    });

    /**
     * Provide a value for a mocked string.
     *
     * Populates M.str for the ESM String module, and stringMap for the AMD core/str mock.
     *
     * @param identifier The string identifier (key) to mock.
     * @param component The component the string belongs to.
     * @param resolved The value that should be returned when the string is requested.
     */
    (globalThis as any).mockString = (identifier: string, component: string, resolved: string): void => {
        // For ESM String module (reads M.str directly).
        const mStr = (globalThis as any).M.str;
        if (!mStr[component]) {
            mStr[component] = {};
        }
        mStr[component][identifier] = resolved;

        // For AMD core/str mock (reads stringMap).
        stringMap.set(`${component}:${identifier}`, resolved);
    };

    /**
     * Mock a string so that it remains permanently pending (never resolves).
     * Useful for testing Suspense fallback rendering.
     *
     * @param identifier The string identifier (key) to mock.
     * @param component The component the string belongs to.
     */
    (globalThis as any).mockPendingString = (identifier: string, component: string): void => {
        pendingStringSet.add(`${component}:${identifier}`);
    };

    const performFetchSpy = jest.spyOn(Ajax, 'performFetch');
    performFetchSpy.mockImplementation((requests: AjaxRequest[], options?: AjaxOptions|undefined) => {
        // Reject all requests.
        // Tests can override this with more specific implementations if they want to allow certain requests to succeed.
        return requests.map((request) => Promise.reject(new Error(
            `Unexpected fetch request for method ${request.methodname} with options: ${JSON.stringify(options)}`
        )));
    });

    const fetchManySpy = jest.spyOn(Ajax, 'fetchMany');
    fetchManySpy.mockImplementation((requests: AjaxRequest[], options?: AjaxOptions|undefined) => {
        // Reject all requests.
        return Promise.all(requests.map((request) => Promise.reject(new Error(
            `Unexpected fetch request for method ${request.methodname} with options: ${JSON.stringify(options)}`
        ))));
    });

    const fetchOneSpy = jest.spyOn(Ajax, 'fetchOne');
    fetchOneSpy.mockImplementation((request: AjaxRequest, options?: AjaxOptions|undefined) => {
        return Promise.reject(new Error(`Unexpected fetch request to method: ${request.methodname} with options: ${JSON.stringify(options)}`));
    });
});

afterEach(() => {
    mockedModules.clear();
    stringMap.clear();
    pendingStringSet.clear();
});
