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
 * This module is an AMD loader plugin that bridges AMD modules to native ESM modules.
 *
 * This plugin allows AMD modules to synchronously import ESM modules using
 * the RequireJS loader plugin syntax `core/esm!{esm/module/name}`.
 *
 * The plugin resolves the corresponding  ESM module and returns its full namespace
 * object (including `default` and any named exports).
 *
 * This is primarily used in backward-compatibility shims — the original AMD
 * module is replaced with a thin wrapper that delegates to the ESM module
 * via this plugin.
 *
 * If the name is suffixed with `:default`, only the default export of the ESM module will be returned.
 *
 * @module     core/esm
 * @copyright  2026 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @example <caption>Re-export a default export from an ESM module</caption>
 * import Module from 'core/esm!@moodle/lms/core/config:default';
 *
 * export default Module;
 *
 * @example <caption>Re-export default and named exports</caption>
 * import Module from 'core/esm!core/fetch';
 *
 * export default Module.default;
 * export const request = Module.request;
 * export const performGet = Module.performGet;
 */

import nativeImport from 'core/import';

/**
 * The sub-module name after the `:` suffix, if present.
 *
 * For example, in `core/esm!@moodle/lms/core/config:default`, this would return `default`.
 *
 * @param {String} name
 * @returns {string|null}
 */
const getSubmoduleName = (name) => {
    const match = name.match(/:(.*)$/);

    return match?.[1] || null;
};

/**
 * Get the ESM module name by stripping the `:default` suffix if present.
 *
 * @param {String} name The AMD module name passed to the plugin, for example `@moodle/lms/core/config:default`.
 * @returns {String}
 */
const getModuleName = (name) => {
    const submodule = getSubmoduleName(name);
    if (submodule) {
        return name.slice(0, -(`:${submodule}`).length);
    }

    return name;
};

export default {
    // eslint-disable-next-line no-unused-vars
    load: function(name, req, onload, config) {
        // Dynamically require the target module containing the promise.
        nativeImport(getModuleName(name))
            .then(function(resolvedValue) {
                const submodule = getSubmoduleName(name);
                if (submodule) {
                    return onload(resolvedValue[submodule]);
                }
                return onload(resolvedValue);
            })
            .catch(function(error) {
                onload.error(error);
            });
    },
};
