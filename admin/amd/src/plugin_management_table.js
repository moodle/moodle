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

import {refreshTableContent} from 'core_table/dynamic';
import * as Selectors from 'core_table/local/dynamic/selectors';
import {call as fetchMany} from 'core/ajax';
import Pending from 'core/pending';
import {fetchNotifications} from 'core/notification';

let watching = false;

export default class {
    /**
     * @property {function[]} clickHandlers a list of handlers to call on click.
     */
    clickHandlers = [];

    constructor() {
        this.addClickHandler(this.handleStateToggle);
        this.addClickHandler(this.handleMoveUpDown);
        this.registerEventListeners();
    }

    /**
     * Initialise an instance of the class.
     *
     * This is just a way of making it easier to initialise an instance of the class from PHP.
     */
    static init() {
        if (watching) {
            return;
        }
        watching = true;
        new this();
    }

    /**
     * Add a click handler to the list of handlers.
     *
     * @param {Function} handler A handler to call on a click event
     */
    addClickHandler(handler) {
        this.clickHandlers.push(handler.bind(this));
    }

    /**
     * Register the event listeners for this instance.
     */
    registerEventListeners() {
        document.addEventListener('click', function(e) {
            const tableRoot = this.getTableRoot(e);

            if (!tableRoot) {
                return;
            }

            this.clickHandlers.forEach((handler) => handler(tableRoot, e));
        }.bind(this));
    }

    /**
     * Get the table root from an event.
     *
     * @param {Event} e
     * @returns {HTMLElement|bool}
     */
    getTableRoot(e) {
        const tableRoot = e.target.closest(Selectors.main.region);
        if (!tableRoot) {
            return false;
        }

        return tableRoot;
    }

    /**
     * Set the plugin state (enabled or disabled)
     *
     * @param {string} methodname The web service to call
     * @param {string} plugin The name of the plugin to set the state for
     * @param {number} state The state to set
     * @returns {Promise}
     */
    setPluginState(methodname, plugin, state) {
        return fetchMany([{
            methodname,
            args: {
                plugin,
                state,
            },
        }])[0];
    }

    setPluginOrder(methodname, plugin, direction) {
        return fetchMany([{
            methodname,
            args: {
                plugin,
                direction,
            },
        }])[0];
    }

    /**
     * Handle state toggling.
     *
     * @param {HTMLElement} tableRoot
     * @param {Event} e
     */
    async handleStateToggle(tableRoot, e) {
        const stateToggle = e.target.closest('[data-action="togglestate"][data-toggle-method]');
        if (stateToggle) {
            e.preventDefault();
            const pendingPromise = new Pending('core_table/dynamic:togglestate');

            await this.setPluginState(
                stateToggle.dataset.toggleMethod,
                stateToggle.dataset.plugin,
                stateToggle.dataset.state === '1' ? 0 : 1
            );

            const [updatedRoot] = await Promise.all([
                refreshTableContent(tableRoot),
                fetchNotifications(),
            ]);

            // Refocus on the link that as pressed in the first place.
            updatedRoot.querySelector(`[data-action="togglestate"][data-plugin="${stateToggle.dataset.plugin}"]`).focus();

            // When clicking the toggle and it remains focused, a new tooltip will be generated.
            // Therefore, the old tooltip should be removed.
            this.removeTooltips();
            pendingPromise.resolve();
        }
    }

    async handleMoveUpDown(tableRoot, e) {
        const actionLink = e.target.closest('[data-action="move"][data-method][data-direction]');
        if (!actionLink) {
            return;
        }

        e.preventDefault();

        const pendingPromise = new Pending('core_table/dynamic:processAction');

        await this.setPluginOrder(
            actionLink.dataset.method,
            actionLink.dataset.plugin,
            actionLink.dataset.direction === 'up' ? -1 : 1,
        );

        const [updatedRoot] = await Promise.all([
            refreshTableContent(tableRoot),
            fetchNotifications(),
        ]);

        // Refocus on the link that as pressed in the first place.
        const exactMatch = updatedRoot.querySelector(
            `[data-action="move"][data-plugin="${actionLink.dataset.plugin}"][data-direction="${actionLink.dataset.direction}"]`
        );
        if (exactMatch) {
            exactMatch.focus();
        } else {
            // The move link is not present anymore, so we need to focus on the other one.
            updatedRoot.querySelector(`[data-action="move"][data-plugin="${actionLink.dataset.plugin}"]`)?.focus();
        }

        pendingPromise.resolve();
    }

    /**
     * Remove tooltips.
     */
    removeTooltips() {
        const tooltips = document.querySelectorAll('[id*="tooltip"]');
        if (tooltips.length > 0) {
            tooltips.forEach(tooltip => {
                tooltip.remove();
            });
        }
    }
}
