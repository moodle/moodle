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
 * Javascript module for editing blocks
 *
 * @module      core_block/edit
 * @copyright   2022 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ModalForm from 'core_form/modalform';

const SELECTORS = {
    EDITBLOCK: '[data-action="editblock"][data-blockid][data-blockform]',
};

/**
 * Initialize module
 * @param {String} pagehash
 */
export const init = (pagehash) => {
    document.addEventListener('click', e => {
        const target = e.target.closest(SELECTORS.EDITBLOCK);
        if (!target || !target.getAttribute('data-blockform')) {
            return;
        }
        e.preventDefault();

        const modalForm = new ModalForm({
            modalConfig: {
                title: target.getAttribute('data-header'),
            },
            args: {blockid: target.getAttribute('data-blockid'), pagehash},
            formClass: target.getAttribute('data-blockform'),
            returnFocus: target,
        });

        // Reload the page when the form is submitted, there is no possibility
        // currently to request contents update of just one block on the page.
        modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, () => {
            location.reload();
        });

        modalForm.show();
    });
};
