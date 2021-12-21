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
 * Manager for the accessreview block.
 *
 * @module block_accessreview/module
 * @author      Max Larkin <max@brickfieldlabs.ie>
 * @copyright   2020 Brickfield Education Labs <max@brickfieldlabs.ie>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {call as fetchMany} from 'core/ajax';
import * as Templates from 'core/templates';
import {exception as displayError} from 'core/notification';

/**
 * The number of colours used to represent the heatmap. (Indexed on 0.)
 * @type {number}
 */
const numColours = 2;

/**
 * The toggle state of the heatmap.
 * @type {boolean}
 */
let toggleState = true;

/**
 * Renders the HTML template onto a particular HTML element.
 * @param {HTMLElement} element The element to attach the HTML to.
 * @param {number} errorCount The number of errors on this module/section.
 * @param {number} checkCount The number of checks triggered on this module/section.
 * @param {String} displayFormat
 * @param {Number} minViews
 * @param {Number} viewDelta
 * @returns {Promise}
 */
const renderTemplate = (element, errorCount, checkCount, displayFormat, minViews, viewDelta) => {
    // Calculate a weight?
    const weight = parseInt((errorCount - minViews) / viewDelta * numColours);

    const context = {
        resultPassed: !errorCount,
        classList: '',
        passRate: {
            errorCount,
            checkCount,
            failureRate: Math.round(errorCount / checkCount * 100),
        },
    };

    if (!element) {
        return Promise.resolve();
    }

    const elementClassList = ['block_accessreview'];
    if (context.resultPassed) {
        elementClassList.push('block_accessreview_success');
    } else if (weight) {
        elementClassList.push('block_accessreview_danger');
    } else {
        elementClassList.push('block_accessreview_warning');
    }

    const showIcons = (displayFormat == 'showicons') || (displayFormat == 'showboth');
    const showBackground = (displayFormat == 'showbackground') || (displayFormat == 'showboth');

    if (showBackground && !showIcons) {
        // Only the background is displayed.
        // No need to display the template.
        // Note: The case where both the background and icons are shown is handled later to avoid jankiness.
        element.classList.add(...elementClassList, 'alert');

        return Promise.resolve();
    }

    if (showIcons && !showBackground) {
        context.classList = elementClassList.join(' ');
    }

    // The icons are displayed either with, or without, the background.
    return Templates.renderForPromise('block_accessreview/status', context)
    .then(({html, js}) => {
        Templates.appendNodeContents(element, html, js);

        if (showBackground) {
            element.classList.add(...elementClassList, 'alert');
        }

        return;
    })
    .catch();
};

/**
 * Applies the template to all sections and modules on the course page.
 *
 * @param {Number} courseId
 * @param {String} displayFormat
 * @param {Boolean} updatePreference
 * @returns {Promise}
 */
const showAccessMap = (courseId, displayFormat, updatePreference = false) => {
    // Get error data.
    return Promise.all(fetchReviewData(courseId, updatePreference))
    .then(([sectionData, moduleData]) => {
        // Get total data.
        const {minViews, viewDelta} = getErrorTotals(sectionData, moduleData);

        sectionData.forEach(section => {
            const element = document.querySelector(`#section-${section.section} .summary`);
            if (!element) {
                return;
            }

            renderTemplate(element, section.numerrors, section.numchecks, displayFormat, minViews, viewDelta);
        });

        moduleData.forEach(module => {
            const element = document.getElementById(`module-${module.cmid}`);
            if (!element) {
                return;
            }

            renderTemplate(element, module.numerrors, module.numchecks, displayFormat, minViews, viewDelta);
        });

        // Change the icon display.
        document.querySelector('.icon-accessmap').classList.remove(...['fa-eye-slash']);
        document.querySelector('.icon-accessmap').classList.add(...['fa-eye']);

        return {
            sectionData,
            moduleData,
        };
    })
    .catch(displayError);
};


/**
 * Hides or removes the templates from the HTML of the current page.
 *
 * @param {Boolean} updatePreference
 */
const hideAccessMap = (updatePreference = false) => {
    // Removes the added elements.
    document.querySelectorAll('.block_accessreview_view').forEach(node => node.remove());

    const classList = [
        'block_accessreview',
        'block_accessreview_success',
        'block_accessreview_warning',
        'block_accessreview_danger',
        'block_accessreview_view',
        'alert',
    ];

    // Removes the added classes.
    document.querySelectorAll('.block_accessreview').forEach(node => node.classList.remove(...classList));

    if (updatePreference) {
        setToggleStatePreference(false);
    }

    // Change the icon display.
    document.querySelector('.icon-accessmap').classList.remove(...['fa-eye']);
    document.querySelector('.icon-accessmap').classList.add(...['fa-eye-slash']);
};


/**
 * Toggles the heatmap on/off.
 *
 * @param {Number} courseId
 * @param {String} displayFormat
 */
const toggleAccessMap = (courseId, displayFormat) => {
    toggleState = !toggleState;
    if (!toggleState) {
        hideAccessMap(true);
    } else {
        showAccessMap(courseId, displayFormat, true);
    }
};

/**
 * Parses information on the errors, generating the min, max and totals.
 *
 * @param {Object[]} sectionData The error data for course sections.
 * @param {Object[]} moduleData The error data for course modules.
 * @returns {Object} An object representing the extra error information.
 */
const getErrorTotals = (sectionData, moduleData) => {
    const totals = {
        totalErrors: 0,
        totalUsers: 0,
        minViews: 0,
        maxViews: 0,
        viewDelta: 0,
    };

    [].concat(sectionData, moduleData).forEach(item => {
        totals.totalErrors += item.numerrors;
        if (item.numerrors < totals.minViews) {
            totals.minViews = item.numerrors;
        }

        if (item.numerrors > totals.maxViews) {
            totals.maxViews = item.numerrors;
        }
        totals.totalUsers += item.numchecks;
    });

    totals.viewDelta = totals.maxViews - totals.minViews + 1;

    return totals;
};

const registerEventListeners = (courseId, displayFormat) => {
    document.addEventListener('click', e => {
        if (e.target.closest('#toggle-accessmap')) {
            e.preventDefault();
            toggleAccessMap(courseId, displayFormat);
        }
    });
};

/**
 * Set the user preference for the toggle value.
 *
 * @param   {Boolean} toggleState
 * @returns {Promise}
 */
const getTogglePreferenceParams = toggleState => {
    return {
        methodname: 'core_user_update_user_preferences',
        args: {
            preferences: [{
                type: 'block_accessreviewtogglestate',
                value: toggleState,
            }],
        }
    };
};

const setToggleStatePreference = toggleState => fetchMany([getTogglePreferenceParams(toggleState)]);

/**
 * Fetch the review data.
 *
 * @param   {Number} courseid
 * @param {Boolean} updatePreference
 * @returns {Promise[]}
 */
const fetchReviewData = (courseid, updatePreference = false) => {
    const calls = [
        {
            methodname: 'block_accessreview_get_section_data',
            args: {courseid}
        },
        {
            methodname: 'block_accessreview_get_module_data',
            args: {courseid}
        },
    ];

    if (updatePreference) {
        calls.push(getTogglePreferenceParams(true));
    }

    return fetchMany(calls);
};

/**
 * Setting up the access review module.
 * @param {number} toggled A number represnting the state of the review toggle.
 * @param {string} displayFormat A string representing the display format for icons.
 * @param {number} courseId The course ID.
 */
export const init = (toggled, displayFormat, courseId) => {
    // Settings consts.
    toggleState = toggled == 1;

    if (toggleState) {
        showAccessMap(courseId, displayFormat);
    }

    registerEventListeners(courseId, displayFormat);
};
