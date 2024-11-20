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
 * Various actions on badges - enabling, disabling, etc.
 *
 * @module      core_badges/actions
 * @copyright   2024 Sara Arjona <sara@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import selectors from 'core_badges/selectors';
import Notification from 'core/notification';
import {prefetchStrings} from 'core/prefetch';
import {getString} from 'core/str';
import Ajax from 'core/ajax';
import Pending from 'core/pending';
import {dispatchEvent} from 'core/event_dispatcher';
import {add as addToast} from 'core/toast';
import * as reportEvents from 'core_reportbuilder/local/events';
import * as reportSelectors from 'core_reportbuilder/local/selectors';

/**
 * Initialize module.
 */
export const init = () => {
    prefetchStrings('core_badges', [
        'reviewconfirm',
        'activatesuccess',
        'deactivatesuccess',
        'awardoncron',
        'numawardstat',
    ]);
    prefetchStrings('core', [
        'confirm',
        'enable',
    ]);

    registerEventListeners();
};

/**
 * Register events for delete preset option in action menu.
 */
const registerEventListeners = () => {
    document.addEventListener('click', (event) => {
        const enableOption = event.target.closest(selectors.actions.enablebadge);

        if (enableOption) {
            event.preventDefault();

            // Use triggerElement to return focus to the action menu toggle.
            const reportElement = event.target.closest(reportSelectors.regions.report);
            const triggerElement = reportElement ? enableOption.closest('.dropdown').querySelector('.dropdown-toggle') : null;
            const badgeId = enableOption.dataset.badgeid;
            const badgeName = enableOption.dataset.badgename;

            Notification.saveCancelPromise(
                getString('confirm', 'core'),
                getString('reviewconfirm', 'core_badges', badgeName),
                getString('enable', 'core'),
                {triggerElement}
            ).then(() => {
                return enableBadge(badgeId, badgeName, reportElement);
            }).catch(() => {
                return;
            });
        }

        const disableOption = event.target.closest(selectors.actions.disablebadge);
        if (disableOption) {
            event.preventDefault();
            const badgeId = disableOption.dataset.badgeid;
            const badgeName = disableOption.dataset.badgename;
            const reportElement = event.target.closest(reportSelectors.regions.report);
            disableBadge(badgeId, badgeName, reportElement);
        }
    });
};

/**
 * Enable the badge.
 *
 * @param {Number} badgeId The id of the badge to enable.
 * @param {String} badgeName The name of the badge to enable.
 * @param {HTMLElement} reportElement the report element.
 */
async function enableBadge(badgeId, badgeName, reportElement) {
    var request = {
        methodname: 'core_badges_enable_badges',
        args: {
            badgeids: [badgeId],
        }
    };

    const pendingPromise = new Pending('core_badges/enable');
    try {
        const result = await Ajax.call([request])[0];
        if (reportElement) {
            showEnableResultToast(badgeName, result);
            // Report element is present, reload the table.
            dispatchEvent(reportEvents.tableReload, {preservePagination: true}, reportElement);
        } else {
            // Report element is not present, add the parameters to the current page to display the message.
            const awards = result.result?.pop().awards;
            document.location = document.location.pathname + `?id=${badgeId}&awards=${awards}`;
        }
    } catch (error) {
        Notification.exception(error);
    }
    pendingPromise.resolve();
}

/**
 * Show the result of enabling a badge.
 *
 * @param {String} badgeName The name of the badge to enable.
 * @param {Object} result The result of enabling a badge.
 */
function showEnableResultToast(badgeName, result) {
    if (result.result?.length > 0) {
        addToast(getString('activatesuccess', 'core_badges', badgeName), {type: 'success'});
        const awards = result.result?.pop().awards;
        if (awards == 'cron') {
            addToast(getString('awardoncron', 'core_badges', {badgename: badgeName}));
        } else if (awards > 0) {
            addToast(getString('numawardstat', 'core_badges', {badgename: badgeName, awards: awards}));
        }
    } else if (result.warnings.length > 0) {
        addToast(result.warnings[0].message, {type: 'danger'});
    }
}

/**
 * Disable the badge.
 *
 * @param {Number} badgeId The id of the badge to disable.
 * @param {String} badgeName The name of the badge to enable.
 * @param {HTMLElement} reportElement the report element.
 */
async function disableBadge(badgeId, badgeName, reportElement) {
    var request = {
        methodname: 'core_badges_disable_badges',
        args: {
            badgeids: [badgeId],
        }
    };

    try {
        const result = await Ajax.call([request])[0];
        if (reportElement) {
            // Report element is present, show the message in a toast and reload the table.
            showDisableResultToast(badgeName, result);
            dispatchEvent(reportEvents.tableReload, {preservePagination: true}, reportElement);
        } else {
            // Report element is not present, the page should be reloaded.
            document.location = document.location.pathname + `?id=${badgeId}`;
        }
    } catch (error) {
        Notification.exception(error);
    }
}

/**
 * Show the result of disabling a badge.
 *
 * @param {String} badgeName The name of the badge to disable.
 * @param {Object} result The result of disabling a badge.
 */
function showDisableResultToast(badgeName, result) {
    if (result.result) {
        addToast(
            getString('deactivatesuccess', 'core_badges', badgeName),
            {type: 'success'}
        );
    } else if (result.warnings.length > 0) {
        addToast(
            result.warnings[0].message,
            {type: 'danger'}
        );
    }
}
