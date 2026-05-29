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
 * Helper library to assist with creating package readme files
 *
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import fs from "fs-extra";
import path from "path";

/**
 * Update the version of the package in thirdpartylibs.xml file.
 *
 * @param {string} packagePath The path to the installed location of the package files.
 * @param {string} packageName The name of the package to update the version for.
 */
export const createPackageReadme = (packagePath, packageName) => {
    const readmeContent = `The content of this directory is automatically managed by the \`npm install\` and \`npm update\` commands.`;
    const readmePath = path.join(packagePath, 'readme_moodle.txt');
    fs.mkdirSync(packagePath, {recursive: true});
    fs.writeFileSync(readmePath, readmeContent, 'utf-8');
};
