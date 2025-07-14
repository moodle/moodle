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
 * The show descriptions toggle component.
 *
 * @module     qbank_managecategories/showdescriptions
 * @class      qbank_managecategories/showdescriptions
 */

import {BaseComponent} from 'core/reactive';
import {categorymanager} from 'qbank_managecategories/categorymanager';

export default class extends BaseComponent {

    create(descriptor) {
        this.name = descriptor.element.id;
        this.selectors = {
            TOGGLE: '#showdescriptions-toggle',
        };
    }

    stateReady() {
        this.addEventListener(this.getElement(this.selectors.TOGGLE), 'change', this.updateShowDescriptions);
    }

    /**
     * Static method to create a component instance.
     *
     * @param {string} target the DOM main element or its ID
     * @param {object} selectors optional css selector overrides
     * @return {Component}
     */
    static init(target, selectors) {
        const targetElement = document.querySelector(target);
        return new this({
            element: targetElement,
            selectors,
            reactive: categorymanager,
        });
    }

    /**
     * Dispatch a mutation to toggle the showDescriptions setting.
     *
     * @param {Event} event The toggle change event.
     * @return {Promise<void>}
     */
    async updateShowDescriptions(event) {
        const checked = event.target.checked;
        this.reactive.dispatch('toggleDescriptions', checked);
    }
}
