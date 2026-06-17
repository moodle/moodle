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
    const bundleRoot = path.join(rootDir, 'lib', 'bundles', 'design-system');
    const bundleJsRoot = path.join(bundleRoot, 'js');
    const bundleScssRoot = path.join(bundleRoot, 'scss');

    copyFromNodeModules({
        packageName: '@moodlehq/design-system',
        version,
        cleanDirs: [bundleRoot],
        copies: [
            {
                src: path.join('dist', 'components'),
                dest: path.join(bundleJsRoot, 'components'),
                label: '@moodlehq/design-system JS component bundles',
            },
            { src: path.join('dist', 'index.js'), dest: path.join(bundleJsRoot, 'index.js'), label: '@moodlehq/design-system JS index' },
            { src: path.join('dist', 'index.css'), dest: path.join(bundleJsRoot, 'index.css'), label: '@moodlehq/design-system CSS index' },
            { src: path.join('dist', 'index.d.ts'), dest: path.join(bundleJsRoot, 'index.d.ts'), label: '@moodlehq/design-system TypeScript definitions' },
            {
                src: path.join('dist', 'tokens', 'scss'),
                dest: path.join(bundleScssRoot, 'tokens'),
                label: '@moodlehq/design-system SCSS tokens',
            },
        ],
        readmePaths: [bundleRoot],
        thirdpartylibs: [
            { componentPath: path.join(rootDir, 'lib'), packageLocation: 'bundles/design-system' },
        ],
    });

}

if (process.argv[1] === fileURLToPath(import.meta.url)) {
    init().catch((err) => {
        console.error(err.message);
        process.exit(1);
    });
}
