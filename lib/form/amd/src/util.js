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
 * Serialize form values into a string.
 *
 * This must be used instead of URLSearchParams, which does not correctly encode nested values such as arrays.
 *
 * @param {Object} data The form values to serialize
 * @param {string} prefix The prefix to use for key names
 * @returns {string}
 */
export const serialize = (data, prefix = '') => [
    ...Object.entries(data).map(([index, value]) => {
        const key = prefix ? `${prefix}[${index}]` : index;
        return (value !== null && typeof value === "object") ? serialize(value, key) : `${key}=${encodeURIComponent(value)}`;
    })
].join("&");
