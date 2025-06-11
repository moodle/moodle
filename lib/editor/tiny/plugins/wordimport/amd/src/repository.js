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
 * External (web service) service functions for the .docx import plugin for the Moodle TinyMCE 6 editor.
 *
 * @module    tiny_wordimport/repository
 * @copyright 2023 University of Graz
 * @author    André Menrath <andre.menrath@uni-graz.at>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {call as fetchMany} from 'core/ajax';

export const getProcessedDocxContent = (itemid, contextid, filename) => fetchMany([{
    methodname: 'tiny_wordimport_get_processed_content',
    args: {
        itemid,
        contextid,
        filename
    },
}])[0];
