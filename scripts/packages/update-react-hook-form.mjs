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
 * Script to update the react-hook-form bundle.
 *
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import path from 'path';
import {fileURLToPath} from 'url';
import {buildBundle, createPackageReadme, getPackageVersion, getRootDir, updateThirdPartyLibsXml} from '../lib/util.mjs';
import fs from 'fs-extra';

export async function init() {
    const rootDir = getRootDir();
    const outputDir = path.join(rootDir, 'lib', 'bundles', 'react-hook-form');

    fs.removeSync(outputDir);

    await buildBundle({
        entryPoint: path.join(rootDir, 'node_modules', 'react-hook-form', 'dist', 'index.esm.mjs'),
        outDir: outputDir,
        outFile: 'react-hook-form.js',
        external: ['react'],
    });

    createPackageReadme(outputDir, 'react-hook-form');
    updateThirdPartyLibsXml(
        path.join(rootDir, 'lib'),
        'bundles/react-hook-form',
        'react-hook-form',
        getPackageVersion('react-hook-form'),
    );
}

if (process.argv[1] === fileURLToPath(import.meta.url)) {
    init().catch((err) => {
        console.error(err.message);
        process.exit(1);
    });
}
