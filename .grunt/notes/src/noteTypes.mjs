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
 * The note types.
 *
 * Matches the suggested order in https://keepachangelog.com/en/1.1.0/.
 * @type {Object<string, string>}
 */
const noteTypes = {
    'improved': 'Added',
    'changed': 'Changed',
    'deprecated': 'Deprecated',
    'removed': 'Removed',
    'fixed': 'Fixed',
};

/**
 * The preferred order of note types.
 *
 * @type {string[]}
 */
const preferredOrder = Object.keys(noteTypes);

/**
 * Comparison method to sort note types.
 *
 * @param {String} a
 * @param {String} b
 * @returns {Number}
 */
export const sortNoteTypes = (a, b) => {
    const aIndex = preferredOrder.indexOf(a);
    const bIndex = preferredOrder.indexOf(b);

    if (aIndex === -1) {
        return 1;
    }

    if (bIndex === -1) {
        return -1;
    }

    return aIndex - bIndex;
};

/**
 * Get the note names.
 *
 * @returns {string[]}
 */
export const getNoteNames = () => Object.keys(noteTypes);

/**
 * Get the human-readable note name.
 *
 * @param {string} type
 * @returns {string}
 */
export const getNoteName = (type) => noteTypes[type];

/**
 * Whether the note name is valid.
 *
 * @param {string} type
 * @returns {boolean}
 */
export const isValidNoteName = (type) => noteTypes[type] !== undefined;
