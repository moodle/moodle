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
 * A system for displaying small snackbar notifications to users which disappear shortly after they are shown.
 *
 * @module     core/toast
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import Templates from 'core/templates';
import Notification from 'core/notification';
import Pending from 'core/pending';

const regionSelector = '.toast-wrapper';

/**
 * Add a new region to place toasts in, taking in a parent element.
 *
 * @method
 * @param {HTMLElement} parent
 */
export const addToastRegion = async(parent) => {
    const pendingPromise = new Pending('addToastRegion');

    try {
        const {html, js} = await Templates.renderForPromise('core/local/toast/wrapper', {});
        Templates.prependNodeContents(parent, html, js);
    } catch (e) {
        Notification.exception(e);
    }

    pendingPromise.resolve();
};

/**
 * Add a new toast or snackbar notification to the page.
 *
 * @method
 * @param {String|Promise<string>} message
 * @param {Object} configuration
 * @param {String} [configuration.title]
 * @param {String} [configuration.subtitle]
 * @param {String} [configuration.type=info] Optional type of the toast notification ('success', 'info', 'warning' or 'danger')
 * @param {Boolean} [configuration.autohide=true]
 * @param {Boolean} [configuration.closeButton=false]
 * @param {Number} [configuration.delay=4000]
 *
 * @example
 * import {add as addToast} from 'core/toast';
 * import {getString} from 'core/str';
 *
 * addToast('Example string', {
 *     type: 'warning',
 *     autohide: false,
 *     closeButton: true,
 * });
 *
 * addToast(getString('example', 'mod_myexample'), {
 *     type: 'warning',
 *     autohide: false,
 *     closeButton: true,
 * });
 */
export const add = async(message, configuration) => {
    const pendingPromise = new Pending('addToastRegion');
    configuration = {
        type: 'info',
        closeButton: false,
        autohide: true,
        delay: 4000,
        ...configuration,
    };

    const templateName = `core/local/toast/message`;
    try {
        const {html, js} = await Templates.renderForPromise(templateName, {
            message: await message,
            ...configuration
        });
        const targetNode = await getTargetNode();
        Templates.prependNodeContents(targetNode, html, js);
    } catch (e) {
        Notification.exception(e);
    }

    pendingPromise.resolve();
};

const getTargetNode = async() => {
    const regions = document.querySelectorAll(regionSelector);

    if (regions.length) {
        return regions[regions.length - 1];
    }

    await addToastRegion(document.body, 'fixed-bottom');
    return getTargetNode();
};

/**
 * Remove a parent region.
 *
 * This is useful in cases such as where a dialog is to be removed and the toast region should be moved back to the body.
 *
 * @param {HTMLElement} parent The region that the toast region is currently a child of.
 * @param {HTMLElement} newParent The parent element to move the toast region content to.
 */
export const removeToastRegion = async(parent, newParent = document) => {
    const pendingPromise = new Pending('core/toast:removeToastRegion');
    const getRegionFromParent = (thisParent) => thisParent.querySelector(regionSelector);

    const regionToRemove = getRegionFromParent(parent);
    if (regionToRemove) {
        const targetRegion = getRegionFromParent(newParent);

        regionToRemove.children.forEach((node) => {
            targetRegion.insertBefore(node, targetRegion.firstChild);
        });

        regionToRemove.remove();
    }
    pendingPromise.resolve();
};
