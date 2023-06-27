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
 * Handle clicking on action links of the feedback alert.
 *
 * @module     core/cta_feedback
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';
import Notification from 'core/notification';

const Selectors = {
    regions: {
        root: '[data-region="core/userfeedback"]',
    },
    actions: {},
};
Selectors.actions.give = `${Selectors.regions.root} [data-action="give"]`;
Selectors.actions.remind = `${Selectors.regions.root} [data-action="remind"]`;

/**
 * Attach the necessary event handlers to the action links
 */
export const registerEventListeners = () => {
    document.addEventListener('click', e => {
        const giveAction = e.target.closest(Selectors.actions.give);
        if (giveAction) {
            e.preventDefault();

            if (!window.open(giveAction.href)) {
                throw new Error('Unable to open popup');
            }

            Promise.resolve(giveAction)
                .then(hideRoot)
                .then(recordAction)
                .catch(Notification.exception);
        }

        const remindAction = e.target.closest(Selectors.actions.remind);
        if (remindAction) {
            e.preventDefault();

            Promise.resolve(remindAction)
                .then(hideRoot)
                .then(recordAction)
                .catch(Notification.exception);
        }
    });
};

/**
 * Record the action that the user took.
 *
 * @param {HTMLElement} clickedItem The action element that the user chose.
 * @returns {Promise}
 */
const recordAction = clickedItem => {
    if (clickedItem.dataset.record) {
        return Ajax.call([{
            methodname: 'core_create_userfeedback_action_record',
            args: {
                action: clickedItem.dataset.action,
                contextid: M.cfg.contextid,
            }
        }])[0];
    }

    return Promise.resolve();
};

/**
 * Hide the root node of the CTA notification.
 *
 * @param {HTMLElement} clickedItem The action element that the user chose.
 * @returns {HTMLElement}
 */
const hideRoot = clickedItem => {
    if (clickedItem.dataset.hide) {
        clickedItem.closest(Selectors.regions.root).remove();
    }

    return clickedItem;
};
