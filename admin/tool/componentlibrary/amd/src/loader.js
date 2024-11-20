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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This initialises the component library JS
 *
 * @module     tool_componentlibrary/loader
 * @copyright  2021 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {mustache} from './mustache';
import {jsRunner} from './jsrunner';
import {clipboardWrapper} from './clipboardwrapper';
import {search} from './search';

/**
 * Load all the component library JavaScript.
 *
 * @param {string} jsonFile Full path to the JSON file with the search DB.
 */
export const init = jsonFile => {
    mustache();
    jsRunner();
    clipboardWrapper();
    search(jsonFile);
};
