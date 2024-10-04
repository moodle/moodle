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
 * Javascript module for reseting all templates.
 *
 * @module      mod_data/resetalltemplates
 * @copyright   2022 Ferran Recio <ferran@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Notification from 'core/notification';
import {prefetchStrings} from 'core/prefetch';
import {getString} from 'core/str';

const selectors = {
    resetAllTemplatesAction: '[data-action="resetalltemplates"]',
};

/**
 * Initialize module
 */
export const init = () => {
    prefetchStrings('mod_data', [
        'resetalltemplatesconfirmtitle',
        'resetalltemplatesconfirm',
    ]);
    prefetchStrings('core', [
        'reset',
    ]);
    registerEventListeners();
};

/**
 * Register events for option in action menu.
 */
const registerEventListeners = () => {
    document.addEventListener('click', (event) => {
        const actionLink = event.target.closest(selectors.resetAllTemplatesAction);
        if (actionLink) {
            event.preventDefault();
            resetAllTemplatesConfirm(actionLink);
        }
    });
};

/**
 * Show the confirmation modal to reset all the templates.
 *
 * @param {HTMLElement} actionLink the element that triggers the action.
 */
const resetAllTemplatesConfirm = async(actionLink) => {
    try {
        await Notification.deleteCancelPromise(
            getString('resetalltemplatesconfirmtitle', 'mod_data'),
            getString('resetalltemplatesconfirm', 'mod_data'),
            getString('reset', 'core'),
        );
        window.location = actionLink.href;
    } catch (error) {
        return;
    }
};
