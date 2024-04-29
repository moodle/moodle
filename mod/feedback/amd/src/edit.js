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
 * Edit items in feedback module
 *
 * @module     mod_feedback/edit
 * @copyright  2016 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";

import {prefetchStrings} from 'core/prefetch';
import {getStrings} from 'core/str';
import Notification from 'core/notification';

const Selectors = {
    deleteQuestionButton: '[data-action="delete"]',
};

let initialized = false;

/**
 * Initialise editor and all it's modules
 */
export const init = () => {
    // Ensure we only add our listeners once (can be called multiple times).
    if (initialized) {
        return;
    }

    prefetchStrings('core', [
        'yes',
        'no',
    ]);
    prefetchStrings('admin', [
        'confirmation',
    ]);
    prefetchStrings('mod_feedback', [
        'confirmdeleteitem',
    ]);

    document.addEventListener('click', async event => {

        // Delete question.
        const deleteButton = event.target.closest(Selectors.deleteQuestionButton);
        if (deleteButton) {
            event.preventDefault();
            const confirmationStrings = await getStrings([
                {key: 'confirmation', component: 'admin'},
                {key: 'confirmdeleteitem', component: 'mod_feedback'},
                {key: 'yes', component: 'core'},
                {key: 'no', component: 'core'},
            ]);
            Notification.confirm(...confirmationStrings, () => {
                window.location = deleteButton.getAttribute('href');
            });
            return;
        }
    });

    initialized = true;
};
