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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Shared internal utilities for the swizzle library.
 *
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import path from 'path';
import {createRequire} from 'module';
import {fileURLToPath} from 'url';

const _require = createRequire(fileURLToPath(import.meta.url));

/** Filename of a plugin's swizzle declaration file. */
export const PLUGIN_MANIFEST_FILENAME = 'swizzle.json';

/**
 * Parse a @moodle/lms specifier into its component and module parts.
 *
 * @param {string} specifier e.g. @moodle/lms/local_swizzledemo/local_swizzledemo_button
 * @returns {{subpath: string, component: string, module: string}|null} null when the specifier is invalid.
 */
export function parseSpecifier(specifier) {
    const subpath = specifier.replace(/^@moodle\/lms\//, '');
    const slashIdx = subpath.indexOf('/');
    if (slashIdx === -1) {
        return null;
    }
    return {subpath, component: subpath.slice(0, slashIdx), module: subpath.slice(slashIdx + 1)};
}

/**
 * Load Moodle component paths from .grunt/components.js.
 *
 * fetchComponentData() uses process.cwd() to locate lib/components.json, so we
 * temporarily chdir to rootDir to ensure it finds the right files regardless of
 * where the CLI was invoked from.
 *
 * @param {string} rootDir
 * @returns {Record<string, string>}
 */
export function loadComponents(rootDir) {
    const {fetchComponentData} = _require(path.join(rootDir, '.grunt', 'components.js'));
    const savedCwd = process.cwd();
    try {
        process.chdir(rootDir);
        return fetchComponentData().components;
    } finally {
        process.chdir(savedCwd);
    }
}
