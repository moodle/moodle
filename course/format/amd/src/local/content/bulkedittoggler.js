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
 * The bulk editor toggler button control.
 *
 * @module     core_courseformat/local/content/bulkedittoggler
 * @class      core_courseformat/local/content/bulkedittoggler
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {BaseComponent} from 'core/reactive';
import {getCurrentCourseEditor} from 'core_courseformat/courseeditor';
import Pending from 'core/pending';
import log from "core/log";

export default class Component extends BaseComponent {

    /**
     * Constructor hook.
     */
    create() {
        // Optional component name for debugging.
        this.name = 'bulk_editor_toogler';
        // Default query selectors.
        this.selectors = {
            BODY: `body`,
            SELECTABLE: `[data-bulkcheckbox][data-is-selectable]`,
        };
        // Component css classes.
        this.classes = {
            HIDDEN: `d-none`,
            BULK: `bulkenabled`,
        };
    }

    /**
     * Static method to create a component instance from the mustache template.
     *
     * @param {string} target optional altentative DOM main element CSS selector
     * @param {object} selectors optional css selector overrides
     * @return {Component}
     */
    static init(target, selectors) {
        let element = document.querySelector(target);
        // TODO Remove this if condition as part of MDL-83851.
        if (!element) {
            log.debug('Init component with id is deprecated, use a query selector instead.');
            element = document.getElementById(target);
        }
        return new this({
            element,
            reactive: getCurrentCourseEditor(),
            selectors
        });
    }

    /**
     * Initial state ready method.
     */
    stateReady() {
        // Capture completion events.
        this.addEventListener(
            this.element,
            'click',
            this._enableBulk
        );
    }

    /**
     * Component watchers.
     *
     * @returns {Array} of watchers
     */
    getWatchers() {
        return [
            {watch: `bulk.enabled:updated`, handler: this._refreshToggler},
        ];
    }

    /**
     * Update a content section using the state information.
     *
     * @param {object} param
     * @param {Object} param.element details the update details (state.bulk in this case).
     */
    _refreshToggler({element}) {
        this.element.classList.toggle(this.classes.HIDDEN, element.enabled ?? false);
        document.querySelector(this.selectors.BODY)?.classList.toggle(this.classes.BULK, element.enabled);
    }

    /**
     * Dispatch the enable bulk mutation.
     *
     * The enable bulk button is outside of the course content main div.
     * Because content/actions captures click events only in the course
     * content, this button needs to trigger the enable bulk mutation
     * by itself.
     */
    _enableBulk() {
        const pendingToggle = new Pending(`courseformat/content:bulktoggle_on`);
        this.reactive.dispatch('bulkEnable', true);
        // Wait for a while and focus on the first checkbox.
        setTimeout(() => {
            document.querySelector(this.selectors.SELECTABLE)?.focus();
            pendingToggle.resolve();
        }, 150);
    }
}
