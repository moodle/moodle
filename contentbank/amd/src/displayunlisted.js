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
 * Module to handle toggling "Display unlisted" preference
 *
 * @module     core_contentbank/displayunlisted
 * @copyright  2023 Daniel Neis Araujo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Notification from 'core/notification';
import Pending from 'core/pending';
import {setUserPreference} from 'core_user/repository';

/**
 * Initialize module, add event listeners
 *
 * @param {String} elementSelector
 */
export const init = elementSelector => {
    document.addEventListener('click', event => {
        const element = event.target.closest(elementSelector);
        if (element) {
            const pendingPromise = new Pending('core_contentbank/displayunlisted');

            setUserPreference('core_contentbank_displayunlisted', !!element.checked)
                .then(() => {
                    pendingPromise.resolve();
                    return document.location.reload();
                })
                .catch(Notification.exception);
        }
    });
};
