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
 * Helper library for downloading bundles from esm.sh into the Moodle source tree.
 *
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import chalk from 'chalk';
import fs from 'fs-extra';
import path from 'path';
import { createPackageReadme } from './readme.mjs';
import { download } from './download.mjs';
import { updateThirdPartyLibsXml } from './thirdpartylibs.mjs';

const DEFAULT_TARGET = 'es2022';

/**
 * Download bundles from esm.sh and save them to their bundled locations in the Moodle source tree.
 *
 * Handles URL construction, removing old files, downloading, rewriting inter-bundle import paths
 * (so bundles reference each other by bare specifier rather than CDN URL), creating
 * readme_moodle.txt files, and updating thirdpartylibs.xml.
 *
 * Each bundle entry describes one downloadable file. After all bundles are downloaded, every
 * file has its internal imports rewritten: any esm.sh URL that matches another bundle in the
 * set is replaced with the bare local specifier (e.g. `react` or `react/jsx-runtime`).
 *
 * @param {Object} options
 * @param {Array<BundleConfig>} options.bundles - bundles to download. Each entry:
 *   - {string}  packageName    - npm package name (e.g. 'react', '@popperjs/core')
 *   - {string}  version        - resolved package version
 *   - {string}  fileName       - esm.sh file name (e.g. 'react', 'jsx-runtime', 'core')
 *   - {string}  [outputFileName] - overrides the output filename (defaults to fileName)
 *   - {boolean} [dev]          - if true, appends '.development' to the esm.sh filename
 *   - {string}  [target]       - JS target, defaults to 'es2022'
 * @param {string} options.outputDir - absolute base output directory; each bundle is saved to
 *   outputDir/packageName/outputFileName.js
 * @param {Array<{dir: string, packageName: string}>} [options.readmePaths] - directories to
 *   write readme_moodle.txt into, each paired with the package name for the readme content
 * @param {Array<{componentPath: string, packageLocation: string, packageName: string, version: string}>}
 *   [options.thirdpartylibs] - entries to update in thirdpartylibs.xml files
 */
export const downloadFromEsmSh = async ({
    bundles,
    outputDir,
    readmePaths = [],
    thirdpartylibs = [],
}) => {
    const resolvedBundles = bundles.map((bundle) => ({
        ...bundle,
        target: bundle.target ?? DEFAULT_TARGET,
        url: `https://esm.sh/stable/${bundle.packageName}@${bundle.version}/${bundle.target ?? DEFAULT_TARGET}/${bundle.fileName}${bundle.dev ? '.development' : ''}.bundle.mjs`,
    }));

    const firstBundle = resolvedBundles[0];
    console.log(
        chalk.blue.bold.underline('Updating %s bundles to version %s using esm.sh'),
        firstBundle?.packageName ?? 'packages',
        firstBundle?.version ?? 'unknown',
    );

    // Remove all unique output directories for this bundle set.
    const uniqueOutputDirs = [...new Set(resolvedBundles.map((b) => path.join(outputDir, b.packageName)))];
    console.log(chalk.blue('Removing old bundles...'));
    for (const dir of uniqueOutputDirs) {
        fs.removeSync(dir, { recursive: true, force: true });
    }
    console.log(chalk.green('Old bundles removed ✓'));

    for (const bundle of resolvedBundles) {
        const filePath = path.join(outputDir, bundle.packageName, `${bundle.outputFileName ?? bundle.fileName}.js`);
        await download(bundle.url, filePath, (filePath) => {
            // Rewrite all inter-bundle esm.sh import URLs to local bare specifiers so the
            // downloaded bundles reference each other by module name, not CDN URL.
            let content = fs.readFileSync(filePath, 'utf-8');
            for (const b of resolvedBundles) {
                const from = `/stable/${b.packageName}@${b.version}/${b.target}/${b.fileName}.mjs`;
                const to = b.packageName === b.fileName ? b.packageName : `${b.packageName}/${b.fileName}`;
                content = content.replaceAll(from, to);
            }
            fs.writeFileSync(filePath, content);
        });
        console.log(chalk.green(`→ ${bundle.packageName}/${bundle.fileName} ✓`));
    }

    if (readmePaths.length > 0) {
        for (const { dir, packageName } of readmePaths) {
            createPackageReadme(dir, packageName);
        }
        console.log(chalk.green('→ readme_moodle.txt files ✓'));
    }

    if (thirdpartylibs.length > 0) {
        for (const { componentPath, packageLocation, packageName, version } of thirdpartylibs) {
            updateThirdPartyLibsXml(componentPath, packageLocation, packageName, version);
        }
        console.log(chalk.green('→ thirdpartylibs.xml files ✓'));
    }

    console.log('\nAll bundles saved' + chalk.green(' ✓'));
    console.log('Done!');
};
