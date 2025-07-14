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
 * The process motnitor's process reactive component.
 *
 * @module     core/local/process_monitor/process
 * @class      core/local/process_monitor/process
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {BaseComponent} from 'core/reactive';
import {manager} from 'core/local/process_monitor/manager';

export default class extends BaseComponent {

    /**
     * Constructor hook.
     */
    create() {
        // Optional component name for debugging.
        this.name = 'process_monitor_process';
        // Default query selectors.
        this.selectors = {
            CLOSE: `[data-action="closeProcess"]`,
            ERROR: `[data-for="error"]`,
            PROGRESSBAR: `progress`,
            NAME: `[data-for="name"]`,
        };
        // Default classes to toggle on refresh.
        this.classes = {
            HIDE: `d-none`,
        };
        this.id = this.element.dataset.id;
    }

    /**
     * Static method to create a component instance form the mustache template.
     *
     * @param {string} query the DOM main element query selector
     * @param {object} selectors optional css selector overrides
     * @return {this}
     */
    static init(query, selectors) {
        return new this({
            element: document.querySelector(query),
            reactive: manager,
            selectors,
        });
    }

    /**
     * Initial state ready method.
     *
     * @param {Object} state the initial state
     */
    stateReady(state) {
        this._refreshItem({state, element: state.queue.get(this.id)});
        this.addEventListener(this.getElement(this.selectors.CLOSE), 'click', this._removeProcess);
    }

    /**
     * Return the component watchers.
     *
     * @returns {Array} of watchers
     */
    getWatchers() {
        return [
            {watch: `queue[${this.id}]:updated`, handler: this._refreshItem},
            {watch: `queue[${this.id}]:deleted`, handler: this.remove},
        ];
    }

    /**
     * Create a monitor item.
     *
     * @param {object} args the watcher arguments
     * @param {object} args.element the item state data
     */
    async _refreshItem({element}) {
        const name = this.getElement(this.selectors.NAME);
        name.innerHTML = element.name;

        const progressbar = this.getElement(this.selectors.PROGRESSBAR);
        progressbar.classList.toggle(this.classes.HIDE, element.finished);
        progressbar.value = element.percentage;

        const close = this.getElement(this.selectors.CLOSE);
        close.classList.toggle(this.classes.HIDE, !element.error);

        const error = this.getElement(this.selectors.ERROR);
        error.innerHTML = element.error;
        error.classList.toggle(this.classes.HIDE, !element.error);
    }

    /**
     * Close the process.
     */
    _removeProcess() {
        this.reactive.dispatch('removeProcess', this.id);
    }
}
