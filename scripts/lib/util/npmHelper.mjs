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
 * Helper library to assist with npm package operations
 *
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import fs from "fs-extra";
import path from "path";
import { getRootDir } from './fs.mjs';

/**
 * Get the version of a package from npm-shrinkwrap.json file.
 *
 * @param {string} packageName The name of the package to get the version for.
 * @returns {string} The version of the package.
 */
export const getPackageVersion = (packageName) => {
    const rootDir = getRootDir();

    // Load the npm-shrinkwrap.json file to get the version of the package.
    const raw = fs.readFileSync(path.resolve(rootDir, 'npm-shrinkwrap.json'), 'utf-8');
    const pkg = JSON.parse(raw);

    // The package version is stored in the "packages" object with the key "node_modules/{packageName}".
    const version = pkg.packages[`node_modules/${packageName}`]?.version;

    if (!version) {
        throw new Error(
            `The package ${packageName} has not been added as a dependency in package.json.\n` +
            `Please add it with the required version as a dependency in package.json.`
        );
    }
    return version;
};
