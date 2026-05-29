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
 * esbuild helpers for building vendored packages into the Moodle source tree.
 *
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import esbuild from 'esbuild';
import fs from 'fs-extra';
import path from 'path';

/**
 * Build an ESM bundle from a single entry point, producing both a production
 * (minified) and a development (unminified) build with linked source maps.
 *
 * The development build is written alongside the production file using the
 * `.dev.js` suffix convention (e.g. `bootstrap.js` → `bootstrap.dev.js`).
 *
 * @param {Object} options
 * @param {string} options.entryPoint Absolute path to the entry point file.
 * @param {string} options.outDir Absolute path to the output directory.
 * @param {string} options.outFile Output filename for the production build (e.g. `bootstrap.js`).
 * @param {string[]} [options.external] Bare specifiers to mark as external (e.g. `['@popperjs/core']`).
 * @param {import('esbuild').Plugin[]} [options.plugins] Additional esbuild plugins.
 */
export async function buildBundle({entryPoint, outDir, outFile, external = [], plugins = []}) {
    const commonOptions = {
        entryPoints: [entryPoint],
        bundle: true,
        format: 'esm',
        external,
        plugins,
    };

    await Promise.all([
        // Production build.
        esbuild.build({
            ...commonOptions,
            outfile: path.join(outDir, outFile),
            minify: true,
            sourcemap: false,
        }),

        // Development build.
        esbuild.build({
            ...commonOptions,
            outfile: path.join(outDir, outFile.replace(/\.js$/, '.dev.js')),
            minify: false,
            sourcemap: 'linked',
            sourcesContent: true,
        }),
    ]);
}

/**
 * Transform individual JS files in a source directory without bundling,
 * producing both a production (minified) and development (unminified) build
 * for each file found directly in the directory (non-recursive).
 *
 * Imports within each file are preserved as-is, allowing cross-file references
 * to resolve correctly at runtime via the import map.
 *
 * @param {Object} options
 * @param {string[]} [options.external] Bare specifiers to mark as external.
 * @param {string} options.srcDir Absolute path to the source directory.
 * @param {string} options.outDir Absolute path to the output directory.
 */
export async function buildDirectory({srcDir, outDir, external = []}) {
    const files = fs.readdirSync(srcDir).filter((f) => f.endsWith('.js'));

    await Promise.all(files.flatMap((file) => {
        const src = path.join(srcDir, file);
        const commonOptions = {
            entryPoints: [src],
            bundle: false,
            format: 'esm',
            external,
        };

        return [
            // Production build.
            esbuild.build({
                ...commonOptions,
                outfile: path.join(outDir, file),
                minify: true,
                sourcemap: false,
            }),

            // Development build.
            esbuild.build({
                ...commonOptions,
                outfile: path.join(outDir, file.replace(/\.js$/, '.dev.js')),
                minify: false,
                sourcemap: 'linked',
                sourcesContent: true,
            }),
        ];
    }));
}
