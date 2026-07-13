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
 * Aggregates per-plugin swizzle.json files into an in-memory manifest.
 *
 * Each plugin that exposes swizzleable React components ships a swizzle.json
 * file in its js/esm/src/ directory. That file maps module names to their
 * eject/wrap safety levels. generateSwizzleManifest() reads all such files and
 * combines them into a single lookup, recomputed fresh on every call.
 *
 * Plugin authors control their own safety metadata — the aggregate is a derived
 * index, not an authoritative source.
 *
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import fs from 'fs';
import path from 'path';
import {loadComponents, PLUGIN_MANIFEST_FILENAME} from './utils.mjs';

/** Safety level assigned to a component discovered without an explicit swizzle.json entry. */
const DEFAULT_ACTIONS = {eject: 'risky', wrap: 'risky'};

/**
 * Enumerate every component's js/esm/src/ directory, core included, themes excluded.
 *
 * @param {string} rootDir  Absolute path to the Moodle project root.
 * @returns {Array<[componentName: string, srcDir: string]>}
 */
function componentSourceDirs(rootDir) {
    const componentPathMap = loadComponents(rootDir);

    return [
        // Core is not in componentPathMap.
        ['core', path.join(rootDir, 'public', 'lib', 'js', 'esm', 'src')],
        ...Object.entries(componentPathMap)
            .filter(([componentPath]) => !componentPath.startsWith('public/theme/'))
            .map(([componentPath, componentName]) => [
                componentName,
                path.join(rootDir, componentPath, 'js', 'esm', 'src'),
            ]),
    ];
}

/**
 * Read a plugin's swizzle.json and merge its entries into the aggregate.
 *
 * @param {string} componentName  e.g. 'core' or 'local_swizzledemo'
 * @param {string} srcDir         absolute path to the component's js/esm/src/
 * @param {Record<string, object>} aggregate  mutated in place
 */
function mergePluginManifest(componentName, srcDir, aggregate) {
    const manifestPath = path.join(srcDir, PLUGIN_MANIFEST_FILENAME);
    if (!fs.existsSync(manifestPath)) {
        return;
    }
    const entries = JSON.parse(fs.readFileSync(manifestPath, 'utf8'));
    for (const [moduleName, actions] of Object.entries(entries)) {
        aggregate[`@moodle/lms/${componentName}/${moduleName}`] = {actions};
    }
}

/**
 * Read all per-plugin swizzle.json files and return the combined manifest.
 *
 * Recomputed from scratch on every call — there is no persisted aggregate
 * file, so the result always reflects the current state of the codebase.
 *
 * @param {string} rootDir  Absolute path to the Moodle project root.
 * @returns {Record<string, {actions: {eject: string, wrap: string}}>}
 */
export function generateSwizzleManifest(rootDir) {
    const aggregate = {};

    for (const [componentName, srcDir] of componentSourceDirs(rootDir)) {
        mergePluginManifest(componentName, srcDir, aggregate);
    }

    return Object.fromEntries(
        Object.entries(aggregate).sort(([a], [b]) => a.localeCompare(b))
    );
}

/**
 * True when the given source file contains a top-level default export.
 *
 * Used as a lightweight signal that a .tsx module is a swizzleable React
 * component rather than a plain utility file (e.g. ajax.ts, utils.ts).
 *
 * @param {string} filePath
 * @returns {boolean}
 */
function hasDefaultExport(filePath) {
    return /export\s+default\b/.test(fs.readFileSync(filePath, 'utf8'));
}

/**
 * Discover component module names directly under a js/esm/src/ directory.
 *
 * A module is either a top-level .tsx file, or a directory containing an
 * index.tsx/index.ts — in both cases only counted when it has a default
 * export, mirroring how wrap/eject resolve the component's source file.
 *
 * @param {string} srcDir
 * @returns {string[]}
 */
function discoverModuleNames(srcDir) {
    if (!fs.existsSync(srcDir)) {
        return [];
    }

    const names = [];
    for (const entry of fs.readdirSync(srcDir, {withFileTypes: true})) {
        if (entry.isFile() && entry.name.endsWith('.tsx')) {
            const filePath = path.join(srcDir, entry.name);
            if (hasDefaultExport(filePath)) {
                names.push(entry.name.slice(0, -'.tsx'.length));
            }
            continue;
        }

        if (entry.isDirectory()) {
            const indexFile = ['index.tsx', 'index.ts']
                .map(name => path.join(srcDir, entry.name, name))
                .find(candidate => fs.existsSync(candidate));
            if (indexFile && hasDefaultExport(indexFile)) {
                names.push(entry.name);
            }
        }
    }
    return names;
}

/**
 * Ensure every discoverable component module has an explicit swizzle.json entry.
 *
 * Components found in js/esm/src/ with no entry are defaulted to risky/risky —
 * the safest assumption until a maintainer actively opts them into `safe`. This
 * is intended to run as part of the ESM build so an undeclared component shows
 * up as a diff in swizzle.json at commit time, rather than silently having no
 * safety metadata.
 *
 * @param {string} rootDir  Absolute path to the Moodle project root.
 * @returns {string[]} Specifiers (@moodle/lms/<component>/<module>) that were newly defaulted.
 */
export function applyDefaultSwizzleSafety(rootDir) {
    const added = [];

    for (const [componentName, srcDir] of componentSourceDirs(rootDir)) {
        const moduleNames = discoverModuleNames(srcDir);
        if (moduleNames.length === 0) {
            continue;
        }

        const manifestPath = path.join(srcDir, PLUGIN_MANIFEST_FILENAME);
        const existing = fs.existsSync(manifestPath)
            ? JSON.parse(fs.readFileSync(manifestPath, 'utf8'))
            : {};

        let changed = false;
        for (const moduleName of moduleNames) {
            if (!(moduleName in existing)) {
                existing[moduleName] = {...DEFAULT_ACTIONS};
                added.push(`@moodle/lms/${componentName}/${moduleName}`);
                changed = true;
            }
        }

        if (changed) {
            const ordered = Object.fromEntries(
                Object.entries(existing).sort(([a], [b]) => a.localeCompare(b))
            );
            fs.mkdirSync(path.dirname(manifestPath), {recursive: true});
            fs.writeFileSync(manifestPath, JSON.stringify(ordered, null, 2) + '\n');
        }
    }

    return added;
}
