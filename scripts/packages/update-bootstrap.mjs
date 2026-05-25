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
import { copyFromNodeModules, getPackageVersion, getRootDir } from '../lib/util.mjs';

export async function init() {
    const rootDir = getRootDir();
    const version = getPackageVersion('bootstrap');
    const libDir = path.join(rootDir, 'lib');
    const bundleRoot = path.join(libDir, 'bundles', 'bootstrap');
    const bundleJsRoot = path.join(bundleRoot, 'js');
    const bundleScssRoot = path.join(bundleRoot, 'scss');

    copyFromNodeModules({
        packageName: 'bootstrap',
        version,
        cleanDirs: [bundleRoot],
        copies: [
            { src: path.join('js', 'src', 'dom'), dest: path.join(bundleJsRoot, 'dom'), label: 'bootstrap JS dom' },
            { src: path.join('js', 'src', 'util'), dest: path.join(bundleJsRoot, 'util'), label: 'bootstrap JS util' },
            {
                src: path.join('dist', 'js', 'bootstrap.esm.min.js'),
                dest: path.join(bundleJsRoot, 'bootstrap.esm.min.js'),
                label: 'bootstrap.esm.min.js',
            },
            {
                src: path.join('dist', 'js', 'bootstrap.esm.min.js.map'),
                dest: path.join(bundleJsRoot, 'bootstrap.esm.min.js.map'),
                label: 'bootstrap.esm.min.js.map',
            },
            { src: 'scss', dest: bundleScssRoot, label: 'bootstrap SCSS' },
        ],
        readmePaths: [bundleRoot],
        thirdpartylibs: [
            { componentPath: libDir, packageLocation: 'bundles/bootstrap' },
        ],
    });
}

if (process.argv[1] === fileURLToPath(import.meta.url)) {
    init().catch((err) => {
        console.error(err.message);
        process.exit(1);
    });
}
