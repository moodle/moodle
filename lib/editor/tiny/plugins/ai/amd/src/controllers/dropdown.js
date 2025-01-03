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
 * Controller for the main selection.
 *
 * This controller is needed to update the "select button"
 *
 * @module      tiny_ai/controllers/dropdown
 * @copyright   2024, ISB Bayern
 * @author      Philipp Memmel
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export const init = (dropDownSelector) => {
    const dropdown = document.querySelector(dropDownSelector);
    dropdown.querySelectorAll('[data-dropdown="option"]').forEach((item) => {
        item.addEventListener('click', () => {
            const dropdownSelect = dropdown.querySelector('[data-dropdown="select"]');
            const dropdownTextElement = dropdown.querySelector('[data-dropdown="selecttext"]');
            dropdownTextElement.innerText = item.innerText;
            dropdownSelect.dataset.value = item.dataset.value;
            const event = new CustomEvent('dropdownSelectionUpdated', {
                detail: {
                    dropdownPreference: dropdown.dataset.preference,
                    newValue: dropdownSelect.dataset.value,
                }
            });
            dropdown.dispatchEvent(event);
        });
    });
};
