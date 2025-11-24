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
 * The category component.
 *
 * @module     qbank_managecategories/category
 * @class      qbank_managecategories/category
 */

import {BaseComponent, DragDrop} from 'core/reactive';
import {categorymanager} from 'qbank_managecategories/categorymanager';
import Templates from 'core/templates';
import Modal from "core/modal";
import {get_string as getString} from "core/str";
import BankSwitcher from 'core_question/bank_switcher';
import Fetch from 'core/fetch';
import Notification from 'core/notification';
import * as CoreUrl from 'core/url';
import {eventTypes as inplaceEditableEventTypes} from 'core/local/inplace_editable/events';

export default class extends BaseComponent {

    create(descriptor) {
        this.name = descriptor.element.id;
        this.selectors = {
            CATEGORY_LIST: '.qbank_managecategories-categorylist',
            CATEGORY_ITEM: '.qbank_managecategories-item[data-categoryid]',
            CATEGORY_CONTENTS: '.qbank_managecategories-item > .container',
            EDIT_BUTTON: '[data-action="addeditcategory"]',
            EDITABLE_CATEGORY_NAME: '.inplaceeditable[data-itemtype="categoryname"]',
            MOVE_BUTTON: '[role="menuitem"][data-actiontype="move"]',
            CONTEXT: '.qbank_managecategories-categorylist[data-contextid]',
            MODAL_CATEGORY_ITEM: '.modal_category_item[data-movingcategoryid]',
            CONTENT_AREA: '.qbank_managecategories-details',
            CATEGORY_ID: id => `#category-${id}`,
            CONTENT_CONTAINER: id => `#category-${id} .qbank_managecategories-childlistcontainer`,
            CHILD_LIST: id => `ul[data-categoryid="${id}"]`,
            PREVIOUS_SIBLING: sortorder => `:scope > [data-sortorder="${sortorder}"]`,
            SWITCH_QUESTION_BANK: '[data-action="switch-question-bank"]',
            MOVE_BANK_HEADER: '.bank-header',
        };
        this.classes = {
            NO_BOTTOM_PADDING: 'pb-0',
            DRAGHANDLE: 'draghandle',
            DROPTARGET: 'qbank_managecategories-droptarget-before',
        };
        this.ids = {
            CATEGORY: id => `category-${id}`,
        };
    }

    stateReady() {
        this.initDragDrop();
        this.addEventListener(this.getElement(this.selectors.EDIT_BUTTON), 'click', categorymanager.showEditModal);
        const moveButton = this.getElement(this.selectors.MOVE_BUTTON);
        this.addEventListener(moveButton, 'click', this.showMoveModal);
        this.addEventListener(this.getElement(), inplaceEditableEventTypes.elementUpdated, (e) => {
            const editable = e.target.closest(this.selectors.EDITABLE_CATEGORY_NAME);
            categorymanager.updateCategoryName(editable.dataset.itemid, editable.dataset.value);
        });
    }

    destroy() {
        // The draggable element must be unregistered.
        this.deInitDragDrop();
    }

    /**
     * Remove any existing DragDrop component, and create a new one.
     */
    initDragDrop() {
        this.deInitDragDrop();
        // If the element is currently draggable, register the getDraggableData method.
        if (this.element.classList.contains(this.classes.DRAGHANDLE)) {
            this.getDraggableData = this._getDraggableData;
        }
        this.dragdrop = new DragDrop(this);
    }

    /**
     * If the DragDrop component is currently registered, unregister it.
     */
    deInitDragDrop() {
        if (this.dragdrop !== undefined) {
            if (this.getDraggableData !== undefined) {
                this.dragdrop.setDraggable(false);
                this.getDraggableData = undefined;
            }
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

    /**
     * Return the category ID from the component's element.
     *
     * This method is referenced as getDraggableData when the component can be dragged.
     *
     * @return {{id: string}}
     * @private
     */
    _getDraggableData() {
        return {
            id: this.getElement().dataset.categoryid
        };
    }

    validateDropData() {
        return true;
    }

    /**
     * Highlight the top border of the category item.
     *
     * @param {Object} dropData
     */
    showDropZone(dropData) {
        if (this.getElement().closest(this.selectors.CATEGORY_ID(dropData.id))) {
            // Can't drop onto itself or its own child.
            return false;
        }
        this.getElement().classList.add(this.classes.DROPTARGET);
        return true;
    }

    /**
     * Remove highlighting.
     */
    hideDropZone() {
        this.getElement().classList.remove(this.classes.DROPTARGET);
    }

    /**
     * Find the new position of the dropped category, and trigger the move.
     *
     * @param {Object} dropData The category being moved.
     * @param {Event} event The drop event.
     */
    drop(dropData, event) {
        const dropTarget = event.target.closest(this.selectors.CATEGORY_ITEM);

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

        const targetParentId = dropTarget.dataset.parent;
        const parentList = dropTarget.closest(this.selectors.CATEGORY_LIST);
        let precedingSibling;

        if (dropTarget === parentList.firstElementChild) {
            // Dropped at the top of the list.
            precedingSibling = null;
        } else {
            precedingSibling = dropTarget.previousElementSibling;
        }

        // Insert the category after the target category
        categorymanager.moveCategory(dropData.id, targetParentId, precedingSibling?.dataset.categoryid);
    }

    getWatchers() {
        return [
            // After any update to this category, move it to the new position.
            {watch: `categories[${this.element.dataset.categoryid}]:updated`, handler: this.updatePosition},
            // When the template context is added or updated, re-render the content.
            {watch: `categories[${this.element.dataset.categoryid}].templatecontext:created`, handler: this.rerender},
            {watch: `categories[${this.element.dataset.categoryid}].templatecontext:updated`, handler: this.rerender},
            // When the name is updated, update it in the element data set.
            {watch: `categories[${this.element.dataset.categoryid}].name:updated`, handler: this.updateName},
            // When a new category is created, check whether we need to add a child list to this category.
            {watch: `categories:created`, handler: this.checkChildList},
        ];
    }

    /**
     * Re-render the category content.
     *
     * @param {Object} args
     * @param {Element} args.element
     * @return {Promise<Array>}
     */
    async rerender({element}) {
        const {html, js} = await Templates.renderForPromise(
            'qbank_managecategories/category_details',
            element.templatecontext
        );
        return Templates.replaceNodeContents(this.getElement(this.selectors.CONTENT_AREA), html, js);
    }

    /**
     * Render and append a new child list.
     *
     * @param {Object} context Template context, must include at least categoryid.
     * @return {Promise<Element>}
     */
    async createChildList(context) {
        const {html, js} = await Templates.renderForPromise(
            'qbank_managecategories/childlist',
            context,
        );
        const parentContainer = document.querySelector(this.selectors.CONTENT_CONTAINER(context.categoryid));
        await Templates.appendNodeContents(parentContainer, html, js);
        const childList = document.querySelector(this.selectors.CHILD_LIST(context.categoryid));
        childList.closest(this.selectors.CATEGORY_CONTENTS).classList.add(this.classes.NO_BOTTOM_PADDING);
        return childList;
    }

    /**
     * Move a category to its new position.
     *
     * A category may change its parent, sortorder and draghandle independently or at the same time. This method will resolve those
     * changes and move the element to the new position. If the parent doesn't already have a child list, one will be created.
     *
     * If the parent has changed, this will also update the state with the new child count of the old and new parents.
     *
     * @param {Object} args
     * @param {Object} args.element
     * @return {Promise<void>}
     */
    async updatePosition({element}) {
        // Move to a new parent category.
        let newParent;
        const originParent = document.querySelector(this.selectors.CHILD_LIST(this.getElement().dataset.parent));
        if (element.contextid === categorymanager.state.page.contextid) {
            if (parseInt(this.getElement().dataset.parent) !== element.parent) {
                newParent = document.querySelector(this.selectors.CHILD_LIST(element.parent));
                if (!newParent) {
                    // The target category doesn't have a child list yet. We'd better create one.
                    newParent = await this.createChildList({categoryid: element.parent});
                }
                this.getElement().dataset.parent = element.parent;
            } else {
                newParent = this.getElement().parentElement;
            }

            // Move to a new position within the parent.
            let previousSibling;
            let nextSibling;
            if (
                newParent.firstElementChild &&
                parseInt(element.sortorder) <= parseInt(newParent.firstElementChild.dataset.sortorder)
            ) {
                // Move to the top of the list.
                nextSibling = newParent.firstElementChild;
            } else {
                // Move later in the list.
                previousSibling = newParent.querySelector(this.selectors.PREVIOUS_SIBLING(element.sortorder - 1));
                nextSibling = previousSibling?.nextElementSibling;
            }

            // Check if this has actually moved, or if it's just having its sortorder updated due to another element moving.
            const moved = (newParent !== this.getElement().parentElement || nextSibling !== this.getElement());

            if (moved) {
                if (nextSibling) {
                    // Move to the specified position in the list.
                    newParent.insertBefore(this.getElement(), nextSibling);
                } else {
                    // Move to the end of the list (may also be the top of the list is empty).
                    newParent.appendChild(this.getElement());
                }
            }
        } else {
            // The category was moved to a different context, it should no longer appear on this page.
            this.getElement().remove();
        }

        if (originParent !== newParent) {
            // Update child count of old and new parent.
            this.reactive.stateManager.processUpdates([
                {
                    name: 'categoryLists',
                    action: 'put',
                    fields: {
                        id: originParent.dataset.categoryid,
                        childCount: originParent.querySelectorAll(this.selectors.CATEGORY_ITEM).length
                    }
                },
                {
                    name: 'categoryLists',
                    action: 'put',
                    fields: {
                        id: newParent.dataset.categoryid,
                        childCount: newParent.querySelectorAll(this.selectors.CATEGORY_ITEM).length
                    }
                }
            ]);
        }

        this.element.dataset.sortorder = element.sortorder;

        // Enable/disable dragging.
        const isDraggable = this.element.classList.contains(this.classes.DRAGHANDLE);
        if (isDraggable && !element.draghandle) {
            this.element.classList.remove(this.classes.DRAGHANDLE);
            this.initDragDrop();
        } else if (!isDraggable && element.draghandle) {
            this.element.classList.add(this.classes.DRAGHANDLE);
            this.initDragDrop();
        }
    }

    /**
     * Create a list of category data from the elements on the page.
     *
     * This will find the category list item elements on the page and extract the category ID, parent ID, and name from the dataset
     * of each element, with any child categories nested underneath. This list can then be passed to createMoveCategoryList to
     * create a tree of move targets.
     *
     * @param {Element} element The category element from the page.
     * @return {Array} List of categories containing categoryId, parentId, categoryName, and a nested array of children.
     */
    getCategoryDataFromElements(element) {
        const categories = [];
        if (element.children) {
            element.children.forEach(category => {
                // Add this category to the list.
                let child = {
                    categoryId: category.dataset.categoryid,
                    parentId: category.dataset.parent,
                    categoryName: category.dataset.categoryname,
                    children: null,
                };
                const childList = category.querySelector(this.selectors.CATEGORY_LIST);
                if (childList) {
                    // If the child has its own children, recursively make a list of those.
                    child.children = this.getCategoryDataFromElements(childList);
                }
                categories.push(child);
            });
        }
        return categories;
    }

    /**
     * Get the category data from the records retrieved from the web service.
     *
     * This will process the list of category records and extract the category ID, parent ID, and name from each,
     * with any child categories nested underneath. This list can then be passed to createMoveCategoryList to
     * create a tree of move targets.
     *
     * @param {Object} record The category record.
     * @return {Array} List of categories containing categoryId, parentId, categoryName, and a nested array of children.
     */
    getCategoryDataFromRecords(record) {
        const categories = [];
        if (record.children) {
            for (const childId in record.children) {
                const category = record.children[childId];
                // Add this category to the list.
                let child = {
                    categoryId: parseInt(category.id),
                    parentId: parseInt(category.parent),
                    categoryName: category.name,
                    children: null,
                };
                if (category.children) {
                    // If the child has its own children, recursively make a list of those.
                    child.children = this.getCategoryDataFromRecords(category);
                }
                categories.push(child);
            }
        }
        return categories;
    }


    /**
     * Recursively create a list of all valid destinations for a current category within a parent category.
     *
     * Each entry in the list represents moving the category "before" another category by default, but may also represent
     * moving "after" another category, or "as a new child of" a parent category.
     *
     * @param {Object} categoryData A list of category data from getCategoryDataFromElements() or getCategoryDataFromRecords().
     * @param {Number} movingCategoryId The ID of the category currently being moved.
     * @return {Array<Object>} A list of objects representing valid move targets for the category. Each object has:
     *  movingcategoryid - The ID of the category we are moving.
     *  precedingsiblingid - The ID of the previous category under the same parent as this target. 0 if this is the first child.
     *  parent - The ID of the target category's parent category. 0 if this is the top category.
     *  categoryname - The name of the target category, to display as part of the destination.
     *  categories - An array of child category targets. If there are no children, this must be null
     *      to prevent infinite recursion in the template.
     *  newchild - If true, this destination is "as a new child of the parent".
     *  lastchild - If true, this destination is after the target category, rather than before.
     */
    createMoveCategoryList(categoryData, movingCategoryId) {
        const categories = [];
        if (categoryData) {
            let precedingSibling = null;
            categoryData.forEach(category => {
                // Don't create a target for the category that's moving.
                if (parseInt(category.categoryId) === movingCategoryId) {
                    return;
                }
                // Create a target to move before this child.
                let child = {
                    movingcategoryid: movingCategoryId,
                    precedingsiblingid: precedingSibling?.categoryId ?? 0,
                    parent: category.parentId,
                    categoryname: category.categoryName,
                    categories: null, // Prevent infinite recursion in the template.
                };
                if (category.children) {
                    // If the child has its own children, recursively make a list of those.
                    child.categories = this.createMoveCategoryList(category.children, movingCategoryId);
                } else {
                    // Otherwise, create a target to move as a new child of this one.
                    child.categories = [
                        {
                            movingcategoryid: movingCategoryId,
                            precedingsiblingid: 0,
                            parent: category.categoryId,
                            categoryname: category.categoryName,
                            categories: null, // Prevent infinite recursion in the template.
                            newchild: true,
                        }
                    ];
                }
                categories.push(child);
                precedingSibling = category;
            });
            if (precedingSibling) {
                if (precedingSibling.categoryId !== movingCategoryId) {
                    // If this is the last child of its parent, also create a target to move the category after this one.
                    categories.push({
                        movingcategoryid: movingCategoryId,
                        precedingsiblingid: precedingSibling.categoryId,
                        parent: precedingSibling.parentId,
                        categoryname: precedingSibling.categoryName,
                        categories: null, // Prevent infinite recursion in the template.
                        lastchild: true,
                    });
                }
            }
        }
        return categories;
    }

    /**
     * Displays a modal containing links to move the category to a new location.
     *
     * @param {Event} e Button click event.
     */
    async showMoveModal(e) {
        // Return if it is not menu item.
        const item = e.target.closest(this.selectors.MOVE_BUTTON);
        if (!item) {
            return;
        }
        // Return if it is disabled.
        if (item.getAttribute('aria-disabled') === 'true') {
            return;
        }

        // Prevent addition click on the item.
        item.setAttribute('aria-disabled', true);

        // Build the list of move links.
        const contextElement = document.querySelector(this.selectors.CONTEXT);
        const categoryData = this.getCategoryDataFromElements(contextElement);

        const moveContext = {
            contextname: contextElement.dataset.contextname,
            contextid: contextElement.dataset.contextid,
            cmid: categorymanager.state.page.cmid,
            categories: [],
            hascategories: false,
        };
        const movingCategoryId = parseInt(item.dataset.categoryid);
        moveContext.categories = this.createMoveCategoryList(categoryData, movingCategoryId);
        moveContext.hascategories = moveContext.categories.length > 0;

        const moveCategory = getString('movecategory', 'qbank_managecategories', item.dataset.categoryname);
        const modal = await Modal.create({
            title: moveCategory,
            body: Templates.render('qbank_managecategories/move_context_list', moveContext),
            footer: '',
            show: true,
            large: true,
        });
        const switcher = new BankSwitcher();
        // Show modal and add click event for list items and bank switcher.
        modal.getBody()[0].addEventListener('click', async(e) => {
            const categoryItem = e.target.closest(this.selectors.MODAL_CATEGORY_ITEM);
            const moveHeader = e.currentTarget.querySelector(this.selectors.MOVE_BANK_HEADER);
            if (categoryItem) {
                categorymanager.moveCategory(
                    categoryItem.dataset.movingcategoryid,
                    categoryItem.dataset.parent,
                    categoryItem.dataset.precedingsiblingid,
                );
                if (moveHeader.dataset.cmid !== categorymanager.state.page.cmid) {
                    const url = CoreUrl.relativeUrl(
                        '/question/bank/managecategories/category.php',
                        {cmid: moveHeader.dataset.cmid}
                    );
                    const message = await getString(
                        'categorymovedto',
                        'qbank_managecategories',
                        {url, name: moveHeader.textContent},
                    );
                    Notification.addNotification({message: message, type: 'info'});
                }
                modal.destroy();
                return;
            }
            const switchButton = e.target.closest(this.selectors.SWITCH_QUESTION_BANK);
            if (switchButton) {
                const pageState = categorymanager.state.page;
                try {
                    const contextId = parseInt(contextElement.dataset.contextid);
                    await switcher.show(modal, pageState.courseid, contextId, parseInt(moveHeader.dataset.cmid), pageState.cmid);
                } catch (ex) {
                    Notification.exception(ex);
                }
            }
        });
        modal.getModal()[0].addEventListener('bankSwitched', async(e) => {
            try {
                const params = {coursemodule: e.detail.cmid};
                const categoriesResponse = await Fetch.performGet(
                    'core_question',
                    'categories',
                    {params},
                );
                const {context, categories} = await categoriesResponse.json();
                // Convert the list of categories into a nested tree.
                for (const id in categories) {
                    const category = categories[id];
                    if (category.parent > 0) {
                        const parentitem = categories[category.parent];
                        if (!parentitem.hasOwnProperty('children')) {
                            parentitem.children = {};
                        }
                        categories[category.parent].children[category.id] = category;
                    }
                }
                // Get the top category with all the others nested below.
                let topCategory;
                for (const id in categories) {
                    if (parseInt(categories[id].parent) === 0) {
                        topCategory = categories[id];
                    }
                }
                const categoryData = this.getCategoryDataFromRecords(topCategory);
                const moveContext = {
                    contextname: context.prefixedname,
                    contextid: context.prefixedname,
                    cmid: e.detail.cmid,
                    categories: [],
                    hascategories: false,
                };
                moveContext.categories = this.createMoveCategoryList(categoryData, movingCategoryId);
                moveContext.hascategories = moveContext.categories.length > 0;
                modal.setBody(
                    Templates.render('qbank_managecategories/move_context_list', moveContext),
                );
                await modal.getBodyPromise();
                modal.setTitle(moveCategory);
                modal.setFooter('');
            } catch (ex) {
                Notification.alert(getString('error', 'error'), ex);
            }
        });
        item.setAttribute('aria-disabled', false);
    }

    /**
     * Check and add a child list if needed.
     *
     * Check whether the category that has just been added has this category as its parent. If it does,
     * check that this category has a child list, and if not, add one.
     *
     * @param {Object} args
     * @param {Element} args.element The new category.
     * @return {Promise<Element>}
     */
    async checkChildList({element}) {
        if (element.parent !== this.getElement().dataset.categoryid) {
            return null; // Not for me.
        }
        let childList = this.getElement(this.selectors.CATEGORY_LIST);
        if (childList) {
            return null; // List already exists, it will handle adding the new category.
        }
        // Render and add a new child list containing the new category.
        return this.createChildList({
            categoryid: element.parent,
            children: [
                element.templatecontext,
            ]
        });
    }

    /**
     * Update the name in the category item element's data, for building move targets.
     *
     * @param {Object} args
     * @param {Object} args.element
     */
    updateName({element}) {
        this.getElement().dataset.categoryname = element.name;
    }
}
