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
 * Script to update the @floating-ui/* bundles, plus the tabbable package that
 * @floating-ui/react depends on.
 *
 * Each package's own ESM `module` build already uses bare specifiers to reference its
 * cross-package dependencies (e.g. `@floating-ui/dom` importing from `@floating-ui/core`
 * and `@floating-ui/utils`, or `@floating-ui/react` importing `react`/`react-dom`/`tabbable`).
 *
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import path from 'path';
import {fileURLToPath} from 'url';
import {buildBundle, getPackageVersion, getRootDir, updateThirdPartyLibsXml, createPackageReadme} from '../lib/util.mjs';
import fs from 'fs-extra';

/**
 * The set of bundles to build with esbuild.
 *
 * @type {Array<{packageName: string, entryPoint: string, outFile: string, external?: string[]}>}
 */
const BUNDLES = [
    {
        packageName: '@floating-ui/core',
        entryPoint: path.join('@floating-ui', 'core', 'dist', 'floating-ui.core.esm.js'),
        outFile: 'core.js',
        external: ['@floating-ui/utils'],
    },
    {
        packageName: '@floating-ui/dom',
        entryPoint: path.join('@floating-ui', 'dom', 'dist', 'floating-ui.dom.esm.js'),
        outFile: 'dom.js',
        external: ['@floating-ui/core', '@floating-ui/utils', '@floating-ui/utils/dom'],
    },
    {
        packageName: '@floating-ui/utils',
        entryPoint: path.join('@floating-ui', 'utils', 'dist', 'floating-ui.utils.esm.js'),
        outFile: 'utils.js',
    },
    {
        packageName: '@floating-ui/utils',
        entryPoint: path.join('@floating-ui', 'utils', 'dist', 'floating-ui.utils.dom.esm.js'),
        outFile: path.join('utils', 'dom.js'),
    },
    {
        packageName: '@floating-ui/react-dom',
        entryPoint: path.join('@floating-ui', 'react-dom', 'dist', 'floating-ui.react-dom.esm.js'),
        outFile: 'react-dom.js',
        external: ['@floating-ui/dom', 'react', 'react-dom'],
    },
    {
        packageName: '@floating-ui/react',
        entryPoint: path.join('@floating-ui', 'react', 'dist', 'floating-ui.react.esm.js'),
        outFile: 'react.js',
        external: [
            'react',
            'react-dom',
            'react/jsx-runtime',
            '@floating-ui/react-dom',
            '@floating-ui/react/utils',
            '@floating-ui/utils',
            '@floating-ui/utils/dom',
            'tabbable',
        ],
    },
    {
        packageName: '@floating-ui/react',
        entryPoint: path.join('@floating-ui', 'react', 'dist', 'floating-ui.react.utils.esm.js'),
        outFile: path.join('react', 'utils.js'),
        external: ['react', '@floating-ui/utils', '@floating-ui/utils/dom', 'tabbable'],
    },
];

export async function init() {
    const rootDir = getRootDir();
    const nodeModulesDir = path.join(rootDir, 'node_modules');
    const outputDir = path.join(rootDir, 'lib', 'bundles', '@floating-ui');
    const tabbableOutputDir = path.join(rootDir, 'lib', 'bundles', 'tabbable');

    fs.removeSync(outputDir, { recursive: true, force: true });
    fs.removeSync(tabbableOutputDir, { recursive: true, force: true });

    for (const { entryPoint, outFile, external } of BUNDLES) {
        await buildBundle({
            entryPoint: path.join(nodeModulesDir, entryPoint),
            outDir: outputDir,
            outFile,
            external,
        });
    }

    await buildBundle({
        entryPoint: path.join(nodeModulesDir, 'tabbable', 'dist', 'index.esm.js'),
        outDir: tabbableOutputDir,
        outFile: 'tabbable.js',
    });

    createPackageReadme(outputDir, '@floating-ui');
    createPackageReadme(tabbableOutputDir, 'tabbable');

    // Each @floating-ui/* package shares the same bundle directory, so the thirdpartylibs.xml
    // <location> for each one points at its specific output file (rather than the shared
    // directory) to keep the entries unique and accurate.
    const packageOutFiles = new Map();
    for (const { packageName, outFile } of BUNDLES) {
        // Use the first (primary) bundle entry for a package as its thirdpartylibs.xml location;
        // later entries for the same package are secondary subpath exports (e.g. `utils/dom.js`).
        if (!packageOutFiles.has(packageName)) {
            packageOutFiles.set(packageName, outFile);
        }
    }
    for (const [packageName, outFile] of packageOutFiles) {
        updateThirdPartyLibsXml(
            path.join(rootDir, 'lib'),
            path.join('bundles', '@floating-ui', outFile),
            packageName,
            getPackageVersion(packageName),
        );
    }
    updateThirdPartyLibsXml(
        path.join(rootDir, 'lib'),
        'bundles/tabbable',
        'tabbable',
        getPackageVersion('tabbable'),
    );
}

if (process.argv[1] === fileURLToPath(import.meta.url)) {
    init().catch((err) => {
        console.error(err.message);
        process.exit(1);
    });
}
