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
 * Custom fields shared category toggle
 *
 * @module      core_customfield/toggle_shared
 * @copyright   2025 David Carrillo <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";

import 'core/inplace_editable';
import Notification from 'core/notification';
import Pending from 'core/pending';
import {toggleCategory} from 'core_customfield/repository/toggle_shared';

let initialized = false;

/**
 * Initialise toggle
 */
export const init = () => {
    if (initialized) {
        // We already added the event listeners (can be called multiple times by mustache template).
        return;
    }

    document.addEventListener('click', event => {
        // Toggle shared category.
        const sharedToggle = event.target.closest('[data-action="shared-toggle"]');
        if (sharedToggle) {
            const pendingPromise = new Pending('core_customfield/shared:toggle');
            const categoryId = sharedToggle.dataset.id;
            const component = sharedToggle.dataset.component;
            const area = sharedToggle.dataset.area;
            const itemid = sharedToggle.dataset.itemid;
            const sharedStateToggle = +!Number(sharedToggle.dataset.state);

            toggleCategory(categoryId, component, area, itemid, sharedStateToggle)
                .then(() => {
                    sharedToggle.dataset.state = sharedStateToggle;
                    return pendingPromise.resolve();
                })
                .catch(Notification.exception);
        }
    });

    initialized = true;
};
