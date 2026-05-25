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
 * Script to update the react and react-dom bundles.
 *
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import path from 'path';
import { fileURLToPath } from 'url';
import { downloadFromEsmSh, getPackageVersion, getRootDir } from '../lib/util.mjs';

export async function init() {
    const rootDir = getRootDir();
    const reactVersion = getPackageVersion('react');
    const reactDomVersion = getPackageVersion('react-dom');
    const outputDir = path.resolve(rootDir, 'lib', 'js', 'bundles');

    await downloadFromEsmSh({
        bundles: [
            { packageName: 'react', version: reactVersion, fileName: 'react' },
            { packageName: 'react', version: reactVersion, fileName: 'jsx-runtime' },
            { packageName: 'react', version: reactVersion, fileName: 'jsx-dev-runtime', dev: true },
            { packageName: 'react-dom', version: reactDomVersion, fileName: 'react-dom' },
            { packageName: 'react-dom', version: reactDomVersion, fileName: 'client' },
            {
                packageName: 'react-dom',
                version: reactDomVersion,
                fileName: 'profiling',
                outputFileName: 'client.development',
            },
        ],
        outputDir,
        readmePaths: [
            { dir: path.join(outputDir, 'react'), packageName: 'react' },
            { dir: path.join(outputDir, 'react-dom'), packageName: 'react-dom' },
        ],
        thirdpartylibs: [
            {
                componentPath: path.join(rootDir, 'lib'),
                packageLocation: 'js/bundles/react',
                packageName: 'react',
                version: reactVersion,
            },
            {
                componentPath: path.join(rootDir, 'lib'),
                packageLocation: 'js/bundles/react-dom',
                packageName: 'react-dom',
                version: reactDomVersion,
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
