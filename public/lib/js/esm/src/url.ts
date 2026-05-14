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
 * URL utility functions.
 *
 * @module     core/url
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */

import config from './config';

declare const M: {
    util: {
        image_url(imagename: string, component: string): string;
    };
};

type QueryParams = Record<string, string | number | boolean | null | undefined>;

/**
 * Construct a file URL.
 *
 * @param relativeScript
 * @param slashArg
 * @returns URL string
 */
export const fileUrl = (relativeScript: string, slashArg: string): string => {
    let url = config.wwwroot + relativeScript;

    // Force a leading slash.
    if (slashArg.charAt(0) !== '/') {
        slashArg = `/${slashArg}`;
    }

    if (config.slasharguments) {
        url += slashArg;
    } else {
        url += `?file=${encodeURIComponent(slashArg)}`;
    }

    return url;
};

/**
 * Take a path relative to the Moodle basedir and apply URL fixes.
 *
 * @param relativePath The path relative to the Moodle basedir.
 * @param params The query parameters for the URL.
 * @param includeSessKey Add the session key to the query params.
 * @returns URL string
 */
export const relativeUrl = (
    relativePath: string,
    params: QueryParams = {},
    includeSessKey = false,
): string => {
    if (
        relativePath.indexOf('http:') === 0
        || relativePath.indexOf('https:') === 0
        || relativePath.indexOf('://') >= 0
    ) {
        throw new Error('relativeUrl function does not accept absolute urls');
    }

    // Fix non-relative paths.
    if (relativePath.charAt(0) !== '/') {
        relativePath = `/${relativePath}`;
    }

    // Fix admin URLs.
    if (config.admin !== 'admin') {
        relativePath = relativePath.replace(/^\/admin\//, `/${config.admin}/`);
    }

    const queryParams: QueryParams = {...params};
    if (includeSessKey) {
        queryParams.sesskey = config.sesskey;
    }

    const queryString = new URLSearchParams(
        Object.entries(queryParams).map(([param, value]) => [param, String(value)]),
    ).toString();

    if (queryString !== '') {
        return `${config.wwwroot}${relativePath}?${queryString}`;
    }

    return config.wwwroot + relativePath;
};

/**
 * Wrapper for image_url function.
 *
 * @param imagename The image name (e.g. t/edit).
 * @param component The component (e.g. mod_feedback).
 * @returns URL string
 */
export const imageUrl = (imagename: string, component: string): string => M.util.image_url(imagename, component);

export default {
    fileUrl,
    relativeUrl,
    imageUrl,
};
