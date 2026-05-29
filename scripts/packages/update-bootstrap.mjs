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
 * Script to update the bootstrap bundle.
 *
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import path from 'path';
import { fileURLToPath } from 'url';
import { buildBundle, buildDirectory, copyFromNodeModules, getPackageVersion, getRootDir } from '../lib/util.mjs';

/**
 * esbuild plugin that externalises Bootstrap's dom/ and util/ source modules
 * when building the main bundle.
 *
 * When esbuild encounters any import that resolves to a file inside Bootstrap's
 * js/src/dom/ or js/src/util/ directories it marks it as external and rewrites
 * the import path to the sibling ./dom/<file> or ./util/<file> form that the
 * output bundle will actually use.  This keeps dom and util as live, shared
 * module instances so that code importing e.g. `bootstrap/dom/event-handler`
 * via the import map receives the same singleton that Bootstrap's components use
 * internally.
 *
 * @param {string} bootstrapDir Absolute path to the Bootstrap package root.
 * @returns {import('esbuild').Plugin}
 */
function externalBootstrapInternals(bootstrapDir) {
    const srcDir = path.join(bootstrapDir, 'js', 'src');
    const domDir = path.join(srcDir, 'dom');
    const utilDir = path.join(srcDir, 'util');

    return {
        name: 'external-bootstrap-dom-util',
        setup(build) {
            build.onResolve({filter: /\.js$/}, (args) => {
                if (!args.importer) {
                    return null;
                }
                const resolved = path.resolve(path.dirname(args.importer), args.path);
                if (resolved.startsWith(domDir + path.sep) || resolved === domDir) {
                    const rel = path.relative(srcDir, resolved);
                    return {path: `./${rel}`, external: true};
                }
                if (resolved.startsWith(utilDir + path.sep) || resolved === utilDir) {
                    const rel = path.relative(srcDir, resolved);
                    return {path: `./${rel}`, external: true};
                }
                return null;
            });
        },
    };
}

export async function init() {
    const rootDir = getRootDir();
    const version = getPackageVersion('bootstrap');
    const libDir = path.join(rootDir, 'lib');
    const bundleRoot = path.join(libDir, 'bundles', 'bootstrap');
    const bundleJsRoot = path.join(bundleRoot, 'js');
    const bundleScssRoot = path.join(bundleRoot, 'scss');
    const bootstrapDir = path.join(rootDir, 'node_modules', 'bootstrap');

    await copyFromNodeModules({
        packageName: 'bootstrap',
        version,
        cleanDirs: [bundleRoot],
        copies: [
            {src: 'scss', dest: bundleScssRoot, label: 'bootstrap SCSS'},
        ],
        readmePaths: [bundleRoot],
        thirdpartylibs: [
            {componentPath: libDir, packageLocation: 'bundles/bootstrap'},
        ],
        postCopy: async () => {
            await buildBundle({
                entryPoint: path.join(bootstrapDir, 'js', 'index.esm.js'),
                outDir: bundleJsRoot,
                outFile: 'bootstrap.js',
                external: ['@popperjs/core'],
                plugins: [externalBootstrapInternals(bootstrapDir)],
            });

            await buildDirectory({
                srcDir: path.join(bootstrapDir, 'js', 'src', 'dom'),
                outDir: path.join(bundleJsRoot, 'dom'),
            });

            await buildDirectory({
                srcDir: path.join(bootstrapDir, 'js', 'src', 'util'),
                outDir: path.join(bundleJsRoot, 'util'),
            });
        },
    });
}

if (process.argv[1] === fileURLToPath(import.meta.url)) {
    init().catch((err) => {
        console.error(err.message);
        process.exit(1);
    });
}
