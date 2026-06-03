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
 * Helper library for copying files from node_modules into the Moodle source tree.
 *
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import chalk from 'chalk';
import fs from 'fs-extra';
import path from 'path';
import { createPackageReadme } from './readme.mjs';
import { getRootDir } from './fs.mjs';
import { updateThirdPartyLibsXml } from './thirdpartylibs.mjs';

/**
 * Copy files from a node_modules package into their bundled locations in the Moodle source tree.
 *
 * Handles removing old files, copying, creating readme_moodle.txt files, and updating
 * thirdpartylibs.xml. Each script using this helper only needs to describe *what* to copy
 * and *where* — the boilerplate is handled here.
 *
 * @param {Object} options
 * @param {string} options.packageName - npm package name (e.g. 'bootstrap', '@moodlehq/design-system')
 * @param {string} options.version - resolved package version string
 * @param {string[]} [options.cleanDirs] - absolute paths of directories to remove before copying
 * @param {Array<{src: string, dest: string, label?: string}>} [options.copies] - copy operations:
 *   src is relative to node_modules/packageName, dest is an absolute path.
 *   An optional label overrides the log line shown for each copy.
 * @param {string[]} [options.readmePaths] - absolute directory paths to write a readme_moodle.txt into
 * @param {Array<{componentPath: string, packageLocation: string, packageName?: string}>} [options.thirdpartylibs]
 *   Entries to update in thirdpartylibs.xml files. componentPath is the directory containing the
 *   XML file; packageLocation is the <location> value to match. packageName defaults to
 *   options.packageName when omitted.
 */
export const copyFromNodeModules = ({
    packageName,
    version,
    cleanDirs = [],
    copies = [],
    readmePaths = [],
    thirdpartylibs = [],
}) => {
    const rootDir = getRootDir();
    const nodeModuleRoot = path.join(rootDir, 'node_modules', packageName);

    console.log(chalk.blue.bold.underline('Updating %s to version %s from Node Modules'), packageName, version);

    if (cleanDirs.length > 0) {
        console.log(chalk.blue('Removing old bundles...'));
        for (const dir of cleanDirs) {
            fs.removeSync(dir, { recursive: true, force: true });
        }
        console.log(chalk.green('Old bundles removed ✓'));
    }

    for (const { src, dest, label } of copies) {
        fs.copySync(path.join(nodeModuleRoot, src), dest);
        console.log(chalk.green(`→ ${label ?? src} ✓`));
    }

    if (readmePaths.length > 0) {
        for (const readmePath of readmePaths) {
            createPackageReadme(readmePath, packageName);
        }
        console.log(chalk.green('→ readme_moodle.txt files ✓'));
    }

    if (thirdpartylibs.length > 0) {
        for (const { componentPath, packageLocation, packageName: pkgName } of thirdpartylibs) {
            updateThirdPartyLibsXml(componentPath, packageLocation, pkgName ?? packageName, version);
        }
        console.log(chalk.green('→ thirdpartylibs.xml files ✓'));
    }

    console.log('\nAll files updated' + chalk.green(' ✓'));
    console.log('Done!');
};
