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
 * Helper library to assist with updating third party libraries XML files.
 *
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import fs from "fs-extra";
import path from "path";
import { DOMParser, XMLSerializer } from '@xmldom/xmldom';
import xpath from 'xpath';

/**
 * Update the version of the package in thirdpartylibs.xml file.
 *
 * @param {string} componentPath The path to the component that contains the thirdpartylibs.xml file.
 * @param {string} packageLocation The location of the package in the thirdpartylibs.xml file.
 * @param {string} packageName The name of the package to update the version for.
 * @param {string} version The updated package version
 */
export const updateThirdPartyLibsXml = (componentPath, packageLocation, packageName, version) => {
    const xmlPath = path.join(componentPath, 'thirdpartylibs.xml');

    if (!fs.existsSync(xmlPath)) {
        throw new Error(`thirdpartylibs.xml not found at path ${xmlPath}`);
    }

    let xmlContent = fs.readFileSync(xmlPath, 'utf-8');
    const doc = new DOMParser().parseFromString(xmlContent);
    const library = xpath.select(`//libraries/library[location="${packageLocation}"]`, doc);

    if (library.length === 0) {
        throw new Error(`Library with name ${packageName} not found at location ${packageLocation} in thirdpartylibs.xml`);
    }
    const versionNode = xpath.select('version', library[0])[0];
    if (!versionNode) {
        throw new Error(`Version node not found for library ${packageName} in ${xmlPath}`);
    }
    versionNode.textContent = version;
    fs.writeFileSync(xmlPath, new XMLSerializer().serializeToString(doc) + "\n", 'utf-8');
};
