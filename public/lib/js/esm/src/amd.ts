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

/* istanbul ignore file */
/**
 * Promise-based AMD module loader.
 *
 * @module     core/amd
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Load a single AMD module and return it as a native Promise.
 *
 * @param moduleId The AMD module identifier, e.g. `'core/ajax'`.
 * @returns A Promise that resolves to the loaded module.
 */
export function requireAsync<T = unknown>(moduleId: string): Promise<T> {
    return new Promise<T>((resolve, reject) => {
        requirejs([moduleId], (mod: unknown) => resolve(mod as T), reject);
    });
}

/**
 * Load multiple AMD modules in one call and return them as a native Promise.
 *
 * @param moduleIds An array of AMD module identifiers.
 * @returns A Promise that resolves to an array of loaded modules in the same order.
 */
export function requireManyAsync(moduleIds: string[]): Promise<unknown[]> {
    return new Promise<unknown[]>((resolve, reject) => {
        requirejs(moduleIds, (...modules: unknown[]) => resolve(modules), reject);
    });
}
