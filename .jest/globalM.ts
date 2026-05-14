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
 * Early Jest setup that provides the global `M` object.
 *
 * This runs via `setupFiles` (before `setupFilesAfterEnv`) so that `M.cfg`
 * is available when modules like `config.ts` are first imported by hoisted
 * `jest.mock()` / `jest.requireActual()` calls.
 *
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

(globalThis as any).M = {
    cfg: {
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
        batchFetchRequests: false,
    },
    str: {} as Record<string, Record<string, string>>,
    util: {
        js_pending: () => {},
        js_complete: () => {},
        get_string: (key: string, component: string) => {
            return (globalThis as any).M.str[component]?.[key] ?? `[${key}, ${component}]`;
        },
    },
};
