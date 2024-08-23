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
 * The category root component.
 *
 * @module     qbank_managecategories/categoryroot
 * @class      qbank_managecategories/categoryroot
 */

import {BaseComponent} from 'core/reactive';
import {categorymanager} from 'qbank_managecategories/categorymanager';

export default class extends BaseComponent {

    create(descriptor) {
        this.name = descriptor.element.id;
        this.classes = {
            SHOWDESCRIPTIONS: 'showdescriptions',
        };
    }

    /**
     * Static method to create a component instance.
     *
     * @param {string} target the DOM main element or its ID
     * @param {object} selectors optional css selector overrides
     * @return {Component}
     */
    static init(target, selectors) {
        return new this({
            element: document.querySelector(target),
            selectors,
            reactive: categorymanager,
        });
    }

    /**
     * Watch for changes to the page state.
     *
     * @return {Array} A list of watchers.
     */
    getWatchers() {
        return [
            // Watch for descriptions being toggled.
            {watch: `page.showdescriptions:updated`, handler: this.toggleDescriptions}
        ];
    }

    /**
     * Show or hide descriptions when the flag in the state is changed.
     *
     * @param {Object} args
     * @param {Object} args.element The updated page state.
     */
    toggleDescriptions({element}) {
        if (element.showdescriptions) {
            this.getElement().classList.add(this.classes.SHOWDESCRIPTIONS);
        } else {
            this.getElement().classList.remove(this.classes.SHOWDESCRIPTIONS);
        }
    }
}
