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
 * Textarea handler.
 *
 * @module      tiny_ai/controllers/textarea
 * @copyright   2024, ISB Bayern
 * @author      Philipp Memmel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


export const init = (textareaSelector) => {
    const textarea = document.querySelector(textareaSelector);
    const minHeight = 40;
    const maxHeight = 342;

    const adjustHeight = () => {
        textarea.style.height = minHeight + 'px';
        textarea.style.height = Math.min(textarea.scrollHeight, maxHeight) + 'px';
    };

    if (textarea) {
        adjustHeight();
        textarea.addEventListener('input', adjustHeight);
        window.addEventListener('resize', adjustHeight);
    }
};
