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
 * Utility functions for the scripts.
 *
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export { copyFromNodeModules } from './util/copyFromNodeModules.mjs';
export { createPackageReadme } from './util/readme.mjs';
export { download } from './util/download.mjs';
export { downloadFromEsmSh } from './util/downloadFromEsmSh.mjs';
export { getPackageVersion } from './util/npmHelper.mjs';
export { getRootDir } from './util/fs.mjs';
export { updateThirdPartyLibsXml } from './util/thirdpartylibs.mjs';
