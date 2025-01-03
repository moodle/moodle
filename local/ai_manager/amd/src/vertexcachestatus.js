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
 * Module rendering the warning box to inform the users about misleading AI results.
 *
 * @module     local_ai_manager/vertexcachestatus
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Templates from 'core/templates';
import {call as fetchMany} from 'core/ajax';
import {alert as alertModal, exception as displayException} from 'core/notification';
import {getString} from 'core/str';

/**
 * Fetches the current cache status of the specified service account.
 *
 * @param {string} serviceaccountinfo the stringified JSON with the service account info
 */
const fetchCurrentCacheStatus = (serviceaccountinfo) => fetchMany([{
    methodname: 'local_ai_manager_vertex_cache_status',
    args: {
        serviceaccountinfo
    }
}])[0];

/**
 * Updates the current cache status.
 *
 * @param {string} serviceaccountinfo the stringified JSON with the service account info
 * @param {boolean} newstatus true if the cache should be enabled, false if it should be disabled
 */
const setCurrentCacheStatus = (serviceaccountinfo, newstatus) => fetchMany([{
    methodname: 'local_ai_manager_vertex_cache_status',
    args: {
        serviceaccountinfo,
        newstatus
    }
}])[0];

/**
 * Controls and renders the Google Vertex AI cache status elements.
 *
 * @param {string} selector the CSS selector of the status element to operate on
 */
export const init = async(selector) => {
    const statusElement = document.querySelector(selector);
    const refreshButton = statusElement.querySelector('[data-action="refresh"]');
    const enableCachingButton = statusElement.querySelector('[data-action="enablecaching"]');
    const disableCachingButton = statusElement.querySelector('[data-action="disablecaching"]');
    const serviceaccountinfoTextArea = document.getElementById('id_serviceaccountjson');
    let serviceaccountinfo = serviceaccountinfoTextArea.value;
    // We want to keep track of the current serviceaccountinfo data, also if the user changes it.
    serviceaccountinfoTextArea.addEventListener('input', (event) => {
        serviceaccountinfo = event.target.value;
    });

    refreshButton.addEventListener('click', async(event) => {
        event.preventDefault();
        await updateCachingStatusDisplay(serviceaccountinfo, statusElement);
    });

    if (enableCachingButton) {
        enableCachingButton.addEventListener('click', async(event) => {
            event.preventDefault();
            enableCachingButton.disabled = true;
            await updateCachingStatus(serviceaccountinfo, statusElement, true);
        });
    }
    if (disableCachingButton) {
        disableCachingButton.addEventListener('click', async(event) => {
            event.preventDefault();
            disableCachingButton.disabled = true;
            await updateCachingStatus(serviceaccountinfo, statusElement, false);
        });
    }
};

/**
 * Updates the caching status display.
 *
 * @param {string} serviceaccountinfo the stringified JSON with the service account info
 * @param {string} statusElement the HTML element to operate on
 */
const updateCachingStatusDisplay = async(serviceaccountinfo, statusElement) => {
    let queryResult = null;
    try {
        queryResult = await fetchCurrentCacheStatus(serviceaccountinfo);
    } catch (error) {
        await displayException(error);
        return;
    }
    if (queryResult.code !== 200) {
        const errorTitleString = await getString('vertex_error_cachestatus', 'local_ai_manager');
        await alertModal(errorTitleString, queryResult.error);
    }
    const templateContext = {
        cachingEnabled: queryResult.cachingEnabled,
        noStatus: false
    };

    const {html, js} = await Templates.renderForPromise('local_ai_manager/vertexcachestatus', templateContext);
    Templates.replaceNode(statusElement, html, js);
};

/**
 * Updates the caching status and updates the DOM to reflect the current state.
 *
 * @param {string} serviceaccountinfo the stringified JSON with the service account info
 * @param {string} statusElement the HTML element to operate on
 * @param {boolean} newstatus the status to set the caching configuration to (true or false)
 */
const updateCachingStatus = async(serviceaccountinfo, statusElement, newstatus) => {
    let queryResult = null;
    try {
        queryResult = await setCurrentCacheStatus(serviceaccountinfo, newstatus);
    } catch (error) {
        await displayException(error);
        return;
    }
    if (queryResult.code !== 200) {
        const errorTitleString = await getString('vertex_error_cachestatus', 'local_ai_manager');
        await alertModal(errorTitleString, queryResult.error);
        return;
    }
    await updateCachingStatusDisplay(serviceaccountinfo, statusElement);
};
