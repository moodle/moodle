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
 * ESM wrapper around the AMD core/str module for loading Moodle language strings.
 *
 * @module     core/String
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {requireAsync} from '@moodle/lms/core/amd';

type stringParams = Record<string, string | number> | string | number | null;

type stringRequest = {
    key: string;
    component: string;
    lang: string;
    param: stringParams;
};

interface stringModule {
    // eslint-disable-next-line camelcase
    get_string: (identifier: string, component?: string, params?: stringParams) => Promise<string>;
    // eslint-disable-next-line camelcase
    get_strings: (requests: stringRequest[]) => Promise<string>[];
    // eslint-disable-next-line camelcase
    cache_strings: (strings: stringRequest[]) => void;
}

export interface StringProps {
    identifier: string;
    component?: string;
    params?: string | number | Record<string, string | number>;
}

// Ensures the same Promise instance is returned for the same string key across renders.
// use() requires a stable reference — without this, a new Promise is created on every
// render and the component suspends indefinitely.
const stringPromiseCache = new Map<string, Promise<string>>();

export const getString = (
    identifier: string,
    component: string = 'core',
    params?: string | number | Record<string, string | number>,
): Promise<string> => {
    const key = `${component}::${identifier}::${JSON.stringify(params)}`;
    if (!stringPromiseCache.has(key)) {
        stringPromiseCache.set(
            key,
            requireAsync<stringModule>('core/str').then(str => str.get_string(identifier, component, params)),
        );
    }
    return stringPromiseCache.get(key)!;
};

export const cacheStrings = async (
    requests: Array<{key: string; component: string; param?: stringParams; lang?: string}>,
): Promise<void> => {
    const str = await requireAsync<stringModule>('core/str');
    str.cache_strings(requests as stringRequest[]);
};
