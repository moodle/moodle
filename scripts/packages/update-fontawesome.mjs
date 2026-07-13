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
 * Script to update the @fortawesome/fontawesome-free bundle.
 *
 * Copies the SCSS and webfonts from node_modules into lib/bundles/fontawesome/.
 *
 * The font-face declarations in brands.scss, regular.scss, and solid.scss are patched
 * to replace the upstream `url('#{$fa-font-path}/fa-xxx.ext')` references with the
 * Moodle-specific `url('[[font:core|fa-xxx.ext]]')` syntax, which is resolved at
 * CSS compile time by Moodle's theme_config layer.
 *
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import chalk from 'chalk';
import fs from 'fs-extra';
import path from 'path';
import { fileURLToPath } from 'url';
import { copyFromNodeModules, getPackageVersion, getRootDir } from '../lib/util.mjs';

const PACKAGE_NAME = '@fortawesome/fontawesome-free';

/**
 * Replace upstream `url('#{$fa-font-path}/fa-xxx.ext')` references with
 * Moodle's `url('[[font:core|fa-xxx.ext]]')` syntax.
 *
 * @param {string} filePath Absolute path to the SCSS file to patch.
 */
const applyFontCorePatch = (filePath) => {
    let content = fs.readFileSync(filePath, 'utf-8');
    // Matches: url('#{$fa-font-path}/fa-anything.ext')
    content = content.replace(
        /url\('#\{\$fa-font-path\}\/([^']+)'\)/g,
        "url('[[font:core|$1]]')",
    );
    fs.writeFileSync(filePath, content, 'utf-8');
};

export async function init() {
    const rootDir = getRootDir();
    const version = getPackageVersion(PACKAGE_NAME);
    const bundleRoot = path.join(rootDir, 'lib', 'bundles', 'fontawesome');
    const scssRoot = path.join(bundleRoot, 'scss');

    await copyFromNodeModules({
        packageName: PACKAGE_NAME,
        version,
        cleanDirs: [scssRoot, path.join(bundleRoot, 'webfonts')],
        copies: [
            { src: 'scss', dest: scssRoot, label: `${PACKAGE_NAME}:${version} SCSS` },
            { src: 'webfonts', dest: path.join(bundleRoot, 'webfonts'), label: `${PACKAGE_NAME}:${version} webfonts` },
            { src: 'LICENSE.txt', dest: path.join(bundleRoot, 'LICENSE.txt'), label: 'LICENSE.txt' },
        ],
        readmePaths: [bundleRoot],
        thirdpartylibs: [
            { componentPath: path.join(rootDir, 'lib'), packageLocation: 'bundles/fontawesome' },
        ],
        postCopy: () => {
            for (const filename of ['brands.scss', 'regular.scss', 'solid.scss']) {
                applyFontCorePatch(path.join(scssRoot, filename));
            }
            console.log(chalk.green('→ [[font:core|...]] patches applied ✓'));
        },
    });
}

if (process.argv[1] === fileURLToPath(import.meta.url)) {
    init().catch((err) => {
        console.error(err.message);
        process.exit(1);
    });
}
