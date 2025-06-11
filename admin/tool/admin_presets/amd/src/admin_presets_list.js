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
 * Admin presets list management
 *
 * @module     tool_admin_presets/admin_presets_list
 * @copyright  2024 David Carrillo <davidmc@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";

import {dispatchEvent} from 'core/event_dispatcher';
import Notification from 'core/notification';
import {prefetchStrings} from 'core/prefetch';
import {getString} from 'core/str';
import Pending from 'core/pending';
import * as reportEvents from 'core_reportbuilder/local/events';
import * as reportSelectors from 'core_reportbuilder/local/selectors';
import {add as addToast} from 'core/toast';
import {deletePreset} from 'tool_admin_presets/repository';

/**
 * Initialise module
 */
export const init = () => {
    prefetchStrings('core', [
        'delete',
    ]);

    prefetchStrings('tool_admin_presets', [
        'deleteshow',
        'deletepreset',
        'eventpresetdeleted',
        'deletepreviouslyapplied'
    ]);

    document.addEventListener('click', event => {
        const presetDelete = event.target.closest('[data-action="admin-preset-delete"]');
        if (presetDelete) {
            event.preventDefault();

            // Use triggerElement to return focus to the action menu toggle.
            const triggerElement = presetDelete.closest('.dropdown').querySelector('.dropdown-toggle');
            const stringid = presetDelete.dataset.presetRollback ? 'deletepreviouslyapplied' : 'deletepreset';

            /* eslint-disable promise/no-nesting */
            Notification.saveCancelPromise(
                getString('deleteshow', 'tool_admin_presets'),
                getString(stringid, 'tool_admin_presets', presetDelete.dataset.presetName),
                getString('delete', 'core'),
                {triggerElement}
            ).then(() => {
                const pendingPromise = new Pending('tool/admin_presets:deletepreset');
                const reportElement = event.target.closest(reportSelectors.regions.report);

                return deletePreset(presetDelete.dataset.presetId)
                    .then(() => addToast(getString('eventpresetdeleted', 'tool_admin_presets')))
                    .then(() => {
                        dispatchEvent(reportEvents.tableReload, {preservePagination: true}, reportElement);
                        return pendingPromise.resolve();
                    })
                    .catch(Notification.exception);
            }).catch(() => {
                return;
            });
        }
    });
};
