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
 * The newchild component.
 *
 * This is a drop target for moving a category to an as-yet-nonexistant child list under another category.
 *
 * @module     qbank_managecategories/newchild
 * @class      qbank_managecategories/newchild
 */

import {BaseComponent, DragDrop} from 'core/reactive';
import $ from 'jquery';
import {categorymanager} from 'qbank_managecategories/categorymanager';

export default class extends BaseComponent {
    create(descriptor) {
        this.name = descriptor.element.id;
        this.selectors = {
            NEW_CHILD: '.qbank_managecategories-newchild',
            CATEGORY_ID: id => `#category-${id}`
        };
        this.classes = {
            DROP_TARGET: 'qbank_managecategories-droptarget',
        };
        this.ids = {
            CATEGORY: id => `category-${id}`,
        };
    }

    stateReady() {
        this.dragdrop = new DragDrop(this);
    }

    destroy() {
        // The draggable element must be unregistered.
        if (this.dragdrop !== undefined) {
            this.dragdrop.unregister();
            this.dragdrop = undefined;
        }
    }

    /**
     * Static method to create a component instance form the mustache template.
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
     * Cannot drop a category as a new child of its own descendant.
     *
     * @param {Object} dropData
     * @return {boolean}
     */
    validateDropData(dropData) {
        if (this.getElement().closest(this.selectors.CATEGORY_ID(dropData.id))) {
            return false;
        }
        return true;
    }

    showDropZone(dropData, event) {
        const dropTarget = event.target.closest(this.selectors.NEW_CHILD);
        dropTarget.classList.add(this.classes.DROP_TARGET);
        $(dropTarget).tooltip('show');
    }

    hideDropZone(dropData, event) {
        const dropTarget = event.target.closest(this.selectors.NEW_CHILD);
        dropTarget.classList.remove(this.classes.DROP_TARGET);
        $(dropTarget).tooltip('hide');
    }

    drop(dropData, event) {
        const dropTarget = event.target.closest(this.selectors.NEW_CHILD);

        if (!dropTarget) {
            return;
        }

        const source = document.getElementById(this.ids.CATEGORY(dropData.id));

        if (!source) {
            return;
        }

        const targetParentId = dropTarget.dataset.parent;

        // Insert the category as the first child of the new parent.
        categorymanager.moveCategory(dropData.id, targetParentId);
    }

    /**
     * Watch for categories moving to a new parent.
     *
     * @return {Array} A list of watchers.
     */
    getWatchers() {
        return [
            // Watch for any category having its parent changed.
            {watch: `categories.parent:updated`, handler: this.checkNewChild},
        ];
    }

    /**
     * If an element now has this category as the parent, remove this new child target.
     *
     * @param {Object} args
     * @param {Element} args.element
     */
    checkNewChild({element}) {
        if (element.parent === parseInt(this.element.dataset.parent)) {
            this.remove();
        }
    }
}
