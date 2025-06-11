// This file is part of Moodle - https://moodle.org/
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
 * Common values helper for the Moodle tiny_wordimport plugin.
 *
 * @module      tiny_wordimport/common
 * @copyright   2023 University of Graz
 * @author      André Menrath <andre.menrath@uni-graz.at>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const component = 'tiny_wordimport';
const DOCX_MIME_TYPE = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

export default {
    component,
    pluginName: `${component}/plugin`,
    icon: component,
    filetype: '.docx',
    wordimportButtonName: `${component}_wordimport`,
    wordimportMenuItemName: `${component}_wordimport`,
    allowedFileType: DOCX_MIME_TYPE
};
