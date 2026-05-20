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
 * Script to update the @popperjs/core bundle.
 *
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import path from 'path';
import { fileURLToPath } from 'url';
import { downloadFromEsmSh, getPackageVersion, getRootDir } from '../lib/util.mjs';

export async function init() {
    const rootDir = getRootDir();
    const version = getPackageVersion('@popperjs/core');
    const outputDir = path.resolve(rootDir, 'lib', 'bundles');

    await downloadFromEsmSh({
        bundles: [
            { packageName: '@popperjs/core', version, fileName: 'core' },
        ],
        outputDir,
        readmePaths: [
            { dir: path.join(outputDir, '@popperjs'), packageName: '@popperjs/core' },
        ],
        thirdpartylibs: [
            {
                componentPath: path.join(rootDir, 'lib'),
                packageLocation: 'bundles/@popperjs',
                packageName: 'popper',
                version,
            },
        ],
    });
}

if (process.argv[1] === fileURLToPath(import.meta.url)) {
    init().catch((err) => {
        console.error(err.message);
        process.exit(1);
    });
}
