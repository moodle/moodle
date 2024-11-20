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
 * The category list component.
 *
 * The category list is a drop target, so that a category may be dropped at the top or bottom of the list.
 *
 * @module     qbank_managecategories/categorylist
 * @class      qbank_managecategories/categorylist
 */

import {BaseComponent, DragDrop} from 'core/reactive';
import Templates from 'core/templates';
import {getString} from 'core/str';
import {categorymanager} from 'qbank_managecategories/categorymanager';

export default class extends BaseComponent {

    create(descriptor) {
        this.name = descriptor.element.id;
        this.selectors = {
            CATEGORY_LIST: '.qbank_managecategories-categorylist',
            CATEGORY_ITEM: '.qbank_managecategories-item[data-categoryid]',
            CATEGORY_CONTENTS: '.qbank_managecategories-item > .container',
            CATEGORY_DETAILS: '.qbank_managecategories-details',
            CATEGORY_NO_DRAGHANDLE: '.qbank_managecategories-item[data-categoryid]:not(.draghandle)',
            CATEGORY_ID: id => `#category-${id}`,
        };
        this.classes = {
            DROP_TARGET_BEFORE: 'qbank_managecategories-droptarget-before',
            DROP_TARGET: 'qbank_managecategories-droptarget',
            NO_BOTTOM_PADDING: 'pb-0',
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

    validateDropData() {
        return true;
    }

    /**
     * Highlight the border of the list where the category will be moved.
     *
     * If dropping at the top of the list, highlight the top border.
     * If dropping at the bottom, highlight the bottom border.
     *
     * @param {Object} dropData
     * @param {Event} event
     */
    showDropZone(dropData, event) {
        const dropTarget = this.getElement();
        if (dropTarget.closest(this.selectors.CATEGORY_ID(dropData.id))) {
            // Can't drop onto its own child.
            return false;
        }
        if (this.getInsertBefore(event, dropTarget)) {
            dropTarget.classList.add(this.classes.DROP_TARGET_BEFORE);
            dropTarget.classList.remove(this.classes.DROP_TARGET);
        } else {
            dropTarget.classList.add(this.classes.DROP_TARGET);
            dropTarget.classList.remove(this.classes.DROP_TARGET_BEFORE);
        }
        return true;
    }

    /**
     * Remove highlighting.
     *
     * @param {Object} dropData
     * @param {Event} event
     */
    hideDropZone(dropData, event) {
        const dropTarget = event.target.closest(this.selectors.CATEGORY_LIST);
        dropTarget.classList.remove(this.classes.DROP_TARGET_BEFORE);
        dropTarget.classList.remove(this.classes.DROP_TARGET);
    }

    /**
     * Determine whether we're dragging over the top or bottom half of the list.
     *
     * @param {Event} event
     * @param {Element} dropTarget
     * @return {boolean}
     */
    getInsertBefore(event, dropTarget) {
        // Get the current mouse position within the drop target
        const mouseY = event.clientY - dropTarget.getBoundingClientRect().top;

        // Get the height of the drop target
        const targetHeight = dropTarget.clientHeight;

        // Check if the mouse is over the top half of the drop target
        return mouseY < targetHeight / 2;
    }

    /**
     * Find the new position of the dropped category, and trigger the move.
     *
     * @param {Object} dropData
     * @param {Event} event
     */
    drop(dropData, event) {
        const dropTarget = event.target.closest(this.selectors.CATEGORY_LIST);

        if (!dropTarget) {
            return;
        }

        if (dropTarget.closest(this.selectors.CATEGORY_ID(dropData.id))) {
            // Can't drop onto your own child.
            return;
        }

        const source = document.getElementById(this.ids.CATEGORY(dropData.id));

        if (!source) {
            return;
        }

        const targetParentId = dropTarget.dataset.categoryid;
        let precedingSibling;

        if (this.getInsertBefore(event, dropTarget)) {
            // Dropped at the top of the list.
            precedingSibling = null;
        } else {
            // Dropped at the bottom of the list.
            precedingSibling = dropTarget.lastElementChild;
        }

        // Insert the category after the target category
        categorymanager.moveCategory(dropData.id, targetParentId, precedingSibling?.dataset.categoryid);
    }

    /**
     * Watch for categories moving to a new parent.
     *
     * @return {Array} A list of watchers.
     */
    getWatchers() {
        return [
            // Watch for this category having its child count updated.
            {watch: `categoryLists[${this.element.dataset.categoryid}].childCount:updated`, handler: this.checkEmptyList},
            // Watch for any new category being created.
            {watch: `categories:created`, handler: this.addCategory},
        ];
    }

    /**
     * If this list is now empty, remove it.
     *
     * @param {Object} args
     * @param {Object} args.element The categoryList state element.
     */
    async checkEmptyList({element}) {
        if (element.childCount === 0) {
            // Display a new child drop zone.
            const categoryItem = this.getElement().closest(this.selectors.CATEGORY_ITEM);
            const {html, js} = await Templates.renderForPromise(
                'qbank_managecategories/newchild',
                {
                    categoryid: this.getElement().dataset.categoryid,
                    tooltip: getString('newchild', 'qbank_managecategories', categoryItem.dataset.categoryname)
                }
            );
            const activityNameArea = categoryItem.querySelector(this.selectors.CATEGORY_DETAILS);
            await Templates.appendNodeContents(activityNameArea, html, js);
            // Reinstate padding on the parent element.
            this.element.closest(this.selectors.CATEGORY_CONTENTS).classList.remove(this.classes.NO_BOTTOM_PADDING);
            // Remove this list.
            this.remove();
        }
    }

    /**
     * If a newly-created category has this list's category as its parent, add it to this list.
     *
     * @param {Object} args
     * @param {Object} args.element
     * @return {Promise<void>}
     */
    async addCategory({element}) {
        if (element.parent !== this.getElement().dataset.categoryid) {
            return; // Not for me.
        }
        const {html, js} = await Templates.renderForPromise('qbank_managecategories/category', element.templatecontext);
        Templates.appendNodeContents(this.getElement(), html, js);
        // If one of the children has no draghandle, it should do now it has a sibling.
        const noDragHandle = this.getElement(this.selectors.CATEGORY_NO_DRAGHANDLE);
        if (noDragHandle) {
            this.reactive.dispatch('showDragHandle', noDragHandle.dataset.categoryid);
        }
    }
}
