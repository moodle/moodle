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
 * The file upload monitor component.
 *
 * @module     core/local/process_monitor/monitor
 * @class      core/local/process_monitor/monitor
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Templates from 'core/templates';
import {BaseComponent} from 'core/reactive';
import {manager} from 'core/local/process_monitor/manager';

export default class extends BaseComponent {

    /**
     * Constructor hook.
     */
    create() {
        // Optional component name for debugging.
        this.name = 'process_monitor';
        // Default query selectors.
        this.selectors = {
            QUEUELIST: `[data-for="process-list"]`,
            CLOSE: `[data-action="hide"]`,
        };
        // Default classes to toggle on refresh.
        this.classes = {
            SHOW: 'show',
        };
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
        this._updateMonitor({state, element: state.display});
        this.addEventListener(this.getElement(this.selectors.CLOSE), 'click', this._closeMonitor);
        state.queue.forEach((element) => {
            this._createListItem({state, element});
        });
    }

    /**
     * Return the component watchers.
     *
     * @returns {Array} of watchers
     */
    getWatchers() {
        return [
            // State changes that require to reload some course modules.
            {watch: `queue:created`, handler: this._createListItem},
            {watch: `display:updated`, handler: this._updateMonitor},
        ];
    }

    /**
     * Create a monitor item.
     *
     * @param {object} args the watcher arguments
     * @param {object} args.element the item state data
     */
    async _createListItem({element}) {
        const {html, js} = await Templates.renderForPromise(
            'core/local/process_monitor/process',
            {...element}
        );
        const target = this.getElement(this.selectors.QUEUELIST);
        Templates.appendNodeContents(target, html, js);
    }

    /**
     * Create a monitor item.
     *
     * @param {object} args the watcher arguments
     * @param {object} args.element the display state data
     */
    _updateMonitor({element}) {
        this.element.classList.toggle(this.classes.SHOW, element.show === true);
    }

    /**
     * Close the monitor.
     */
    _closeMonitor() {
        this.reactive.dispatch('setShow', false);
    }
}
