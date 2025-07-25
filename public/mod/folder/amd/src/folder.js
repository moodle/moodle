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
 * Javascript helper function for Folder module
 *
 * @module     mod_folder/folder
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Tree from 'core/tree';

/**
 * The init function that does the job.
 * It changes the display of directories and files into a tree.
 *
 * @param {string} elementId The ID of the container element.
 */
export const init = (elementId) => {
    new Tree(`#${elementId} ul.foldertree[role="tree"]`);
};
