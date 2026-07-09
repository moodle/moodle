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
 * Read/write helpers for per-plugin swizzle.json files.
 *
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import fs from 'fs';
import path from 'path';
import {parseSpecifier, loadComponents, PLUGIN_MANIFEST_FILENAME} from './utils.mjs';

/** Valid safety levels for a swizzle action. */
export const VALID_LEVELS = ['safe', 'risky', 'prohibited'];

/** Valid fields that can have their safety level set independently. */
export const VALID_FIELDS = ['both', 'eject', 'wrap'];

/**
 * Return the absolute path to the per-plugin swizzle.json for a given specifier.
 *
 * @param {string} specifier  e.g. @moodle/lms/local_swizzledemo/Button
 * @param {string} rootDir
 * @returns {string}
 * @throws {Error} When the specifier is invalid or the component is not found in the registry.
 */
function resolvePluginManifestPath(specifier, rootDir) {
    const parsed = parseSpecifier(specifier);
    if (!parsed) {
        throw new Error(`Invalid specifier "${specifier}"`);
    }
    const {component} = parsed;

    if (component === 'core') {
        return path.join(rootDir, 'public', 'lib', 'js', 'esm', 'src', PLUGIN_MANIFEST_FILENAME);
    }

    const components = loadComponents(rootDir);
    const componentPath = Object.entries(components).find(([, name]) => name === component)?.[0];
    if (!componentPath) {
        throw new Error(`Component "${component}" not found in Moodle component registry.`);
    }
    return path.join(rootDir, componentPath, 'js', 'esm', 'src', PLUGIN_MANIFEST_FILENAME);
}

/**
 * Set the safety level for a component in its plugin's own swizzle.json.
 *
 * This writes to the plugin's source swizzle.json — the authoritative per-plugin
 * manifest. The aggregate manifest is not persisted; it is recomputed from these
 * files on every read.
 *
 * @param {string} rootDir   Absolute path to the project root.
 * @param {string} specifier Module specifier (e.g. @moodle/lms/local_swizzledemo/Button).
 * @param {string} level     Safety level: safe, risky, or prohibited.
 * @param {string} field     Which action to update: eject, wrap, or both (default).
 * @param {boolean} log      Whether to print the resulting state. Set to false when the
 *                           caller is about to make another call for the same component
 *                           (e.g. setting eject and wrap independently) and only wants the
 *                           final combined state reported once.
 * @returns {void}
 * @throws {Error} When the level or field is invalid or the component cannot be resolved.
 */
export function setComponentSafety(rootDir, specifier, level, field = 'both', log = true) {
    if (!VALID_LEVELS.includes(level)) {
        throw new Error(`Invalid safety level "${level}". Valid values: ${VALID_LEVELS.join(', ')}`);
    }
    if (!VALID_FIELDS.includes(field)) {
        throw new Error(`Invalid field "${field}". Valid values: ${VALID_FIELDS.join(', ')}`);
    }

    const {module: moduleName} = parseSpecifier(specifier);
    const pluginManifestPath = resolvePluginManifestPath(specifier, rootDir);
    const existing = fs.existsSync(pluginManifestPath)
        ? JSON.parse(fs.readFileSync(pluginManifestPath, 'utf8'))
        : {};

    const current = existing[moduleName] ?? {eject: 'safe', wrap: 'safe'};
    if (field === 'both') {
        existing[moduleName] = {eject: level, wrap: level};
    } else {
        existing[moduleName] = {...current, [field]: level};
    }

    fs.mkdirSync(path.dirname(pluginManifestPath), {recursive: true});
    fs.writeFileSync(pluginManifestPath, JSON.stringify(existing, null, 2) + '\n');

    if (log) {
        const updated = existing[moduleName];
        console.log(`✓ Set ${specifier} → eject: ${updated.eject}, wrap: ${updated.wrap}`);
    }
}
