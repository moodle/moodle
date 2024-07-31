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
 * Tiny media plugin embed helpers.
 *
 * This provides easy access to any classes without instantiating a new object.
 *
 * @module      tiny_media/embed/embedhelpers
 * @copyright   2024 Stevani Andolo <stevani@hotmail.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Return template context for insert media.
 *
 * @param {object} props
 * @returns {object}
 */
export const insertMediaTemplateContext = (props) => {
    return {
        mediaType: props.mediaType,
        showDropzone: props.canShowDropZone,
        showFilePicker: props.canShowFilePicker,
    };
};
