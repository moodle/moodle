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
 * Javascript module to control the form responsible for selecting a preset.
 *
 * @module      mod_data/selectpreset
 * @copyright   2021 Mihail Geshoski <mihail@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const selectors = {
    presetRadioButton: 'input[name="fullname"]',
    selectPresetButton: 'input[name="selectpreset"]',
    selectedPresetRadioButton: 'input[name="fullname"]:checked',
};

/**
 * Initialize module.
 */
export const init = () => {
    const radioButton = document.querySelectorAll(selectors.presetRadioButton);

    // Initialize the "Use a preset" button properly.
    disableUsePresetButton();

    radioButton.forEach((elem) => {
        elem.addEventListener('change', function(event) {
            event.preventDefault();
            // Enable the "Use a preset" button when any of the radio buttons in the presets list is checked.
            disableUsePresetButton();
        });
    });

};

/**
 * Decide whether to disable or not the "Use a preset" button.
 * When there is no preset selected, the button should be displayed disabled; otherwise, it will appear enabled as a primary button.
 *
 * @method
 * @private
 */
const disableUsePresetButton = () => {
    let selectPresetButton = document.querySelector(selectors.selectPresetButton);
    const selectedRadioButton = document.querySelector(selectors.selectedPresetRadioButton);

    if (selectedRadioButton) {
        // There is one preset selected, so the button should be enabled.
        selectPresetButton.removeAttribute('disabled');
        selectPresetButton.classList.remove('btn-secondary');
        selectPresetButton.classList.add('btn-primary');
        selectPresetButton.setAttribute('data-presetname', selectedRadioButton.getAttribute('value'));
        selectPresetButton.setAttribute('data-cmid', selectedRadioButton.getAttribute('data-cmid'));
    } else {
        // There is no any preset selected, so the button should be disabled.
        selectPresetButton.setAttribute('disabled', true);
        selectPresetButton.classList.remove('btn-primary');
        selectPresetButton.classList.add('btn-secondary');
        selectPresetButton.removeAttribute('data-presetname');
        selectPresetButton.removeAttribute('data-cmid');
    }
};
