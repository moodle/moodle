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

import Notification from 'core/notification';
import {get_string as getString} from 'core/str';

const selectors = {
    selectPresetButton: 'input[name="selectpreset"]',
    selectedPresetRadioButton: 'input[name="fullname"]:checked',
};

/**
 * Initialize module.
 */
export const init = () => {
    const selectPresetButton = document.querySelector(selectors.selectPresetButton);

    selectPresetButton.addEventListener('click', event => {
        event.preventDefault();
        // Validate whether there is a selected preset before submitting the form.
        if (document.querySelectorAll(selectors.selectedPresetRadioButton).length > 0) {
            const presetsForm = event.target.closest('form');
            presetsForm.submit();
        } else {
            // No selected presets. Display an error message to user.
            getString('presetnotselected', 'mod_data').then((str) => {
                return Notification.addNotification({
                    type: 'error',
                    message: str
                });
            }).catch(Notification.exception);
        }
    });
};
