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
 * Script to update the @moodlehq/design-system bundle.
 *
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import path from 'path';
import { fileURLToPath } from 'url';
import { copyFromNodeModules, getPackageVersion, getRootDir } from '../lib/util.mjs';

export async function init() {
    const rootDir = getRootDir();
    const version = getPackageVersion('@moodlehq/design-system');
    const themeRoot = path.join(rootDir, 'public', 'theme', 'boost');
    const bundleRoot = path.join(rootDir, 'lib', 'js', 'bundles', 'design-system');
    const themeDesignSystemRoot = path.join(themeRoot, 'scss', 'design-system');

    copyFromNodeModules({
        packageName: '@moodlehq/design-system',
        version,
        cleanDirs: [bundleRoot, themeDesignSystemRoot],
        copies: [
            { src: 'dist', dest: bundleRoot, label: '@moodlehq/design-system JS bundles' },
            {
                src: path.join('tokens', 'scss'),
                dest: path.join(themeDesignSystemRoot, 'tokens', 'scss'),
                label: '@moodlehq/design-system tokens',
            },
        ],
        readmePaths: [bundleRoot, themeDesignSystemRoot],
        thirdpartylibs: [
            { componentPath: path.join(rootDir, 'lib'), packageLocation: 'js/bundles/design-system' },
            { componentPath: themeRoot, packageLocation: 'scss/design-system' },
        ],
    });
}

if (process.argv[1] === fileURLToPath(import.meta.url)) {
    init().catch((err) => {
        console.error(err.message);
        process.exit(1);
    });
}
