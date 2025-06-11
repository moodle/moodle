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
 * Reactive module for category manager
 *
 * @module qbank_managecategories/categorymanager
 */

import {Reactive} from 'core/reactive';
import {get_string as getString} from 'core/str';
import {mutations} from 'qbank_managecategories/mutations';
import {eventTypes, notifyQbankManagecategoriesStateUpdated} from 'qbank_managecategories/events';
import Ajax from "core/ajax";
import Notification from "core/notification";
import ModalForm from 'core_form/modalform';

const SELECTORS = {
    CATEGORY_LIST: '.qbank_managecategories-categorylist',
    CONTEXT: '.qbank_managecategories-categorylist[data-contextid]',
    CATEGORY_ITEM: '.qbank_managecategories-item[data-categoryid]',
    CATEGORY_ROOT: '#categoryroot',
    SHOWDESCRIPTIONS_TOGGLE: '#showdescriptions-toggle',
    ADD_EDIT_BUTTON: '[data-action="addeditcategory"]',
};

const CLASSES = {
    DRAGHANDLE: 'draghandle',
    DANGER: 'alert-danger',
};

/**
 * Load the initial state.
 *
 * This iterates over the initial tree of category items, and captures the data required for the state from each category.
 * It also captures a count of the number of children in each list.
 *
 * @param {Reactive} reactive
 * @return {Promise<void>}
 */
const loadState = async(reactive) => {
    const rootElement = document.querySelector(SELECTORS.CATEGORY_ROOT);
    const stateData = {
        page: {
            contextid: rootElement.dataset.contextid,
            showdescriptions: document.querySelector(SELECTORS.SHOWDESCRIPTIONS_TOGGLE).checked,
        },
        categories: [],
        categoryLists: [],
    };
    const listItems = document.querySelectorAll(SELECTORS.CATEGORY_ITEM);
    listItems.forEach(item => {
        stateData.categories.push({
            id: item.dataset.categoryid,
            name: item.dataset.categoryname,
            parent: item.dataset.parent,
            contextid: item.dataset.contextid,
            sortorder: item.dataset.sortorder,
            draghandle: item.classList.contains(CLASSES.DRAGHANDLE),
        });
    });
    const categoryLists = document.querySelectorAll(SELECTORS.CATEGORY_LIST);
    categoryLists.forEach(categoryList => {
        stateData.categoryLists.push({
            id: categoryList.dataset.categoryid,
            childCount: categoryList.querySelectorAll(SELECTORS.CATEGORY_ITEM).length,
        });
    });
    reactive.setInitialState(stateData);
};

/**
 * Reactive instance for the category manager.
 */
class CategoryManager extends Reactive {
    /**
     * Move a category to a new position within the given parent.
     *
     * This will call the move_category web service function to re-order the categories, then update
     * the state with the returned updates.
     *
     * @param {Number} categoryId The ID of the category being moved.
     * @param {Number} targetParentId The ID of the destination parent category (this may not have changed).
     * @param {Number} precedingSiblingId The ID of the category to put the moved category after.
     *     This may be null if moving to the top of a list.
     */
    moveCategory(
        categoryId,
        targetParentId,
        precedingSiblingId = null,
    ) {
        const call = {
            methodname: 'qbank_managecategories_move_category',
            args: {
                pagecontextid: this.state.page.contextid,
                categoryid: categoryId,
                targetparentid: targetParentId,
                precedingsiblingid: precedingSiblingId,
            }
        };
        Ajax.call([call])[0]
            .then((stateUpdates) => {
                this.stateManager.processUpdates(stateUpdates);
                return stateUpdates;
            })
            .catch(error => {
                Notification.addNotification({
                    message: error.message,
                    type: 'error',
                });
                document.getElementsByClassName(CLASSES.DANGER)[0]?.scrollIntoView();
            });
    }

    /**
     * Return title for the add/edit modal.
     *
     * @param {boolean} isEdit is 'add' or 'edit' form
     * @returns {String} title string
     */
    getTitle(isEdit) {
        return getString(isEdit ? 'editcategory' : 'addcategory', 'question');
    }

    /**
     * Return save button label for the add/edit modal.
     *
     * @param {boolean} isEdit is 'add' or 'edit' form
     * @returns {String} save string
     */
    getSave(isEdit) {
        return isEdit ? getString('savechanges', 'core') : getString('addcategory', 'question');
    }

    /**
     * Function handling display of modal form.
     *
     * @param {Event} e The click event triggering the modal.
     */
    showEditModal(e) {
        const addEditButton = e.target.closest(SELECTORS.ADD_EDIT_BUTTON);

        // Return if it is not 'addeditcategory' button.
        if (!addEditButton) {
            return;
        }

        // Return if the action type is not specified.
        if (!addEditButton.dataset.actiontype) {
            return;
        }

        e.preventDefault();
        // Data for the modal.
        const title = categorymanager.getTitle(addEditButton.dataset.actiontype === 'edit');
        const save = categorymanager.getSave(addEditButton.dataset.actiontype === 'edit');
        const cmid = addEditButton.dataset.cmid;
        const courseid = addEditButton.dataset.courseid;
        const questioncount = addEditButton.dataset.questioncount;
        let contextid = addEditButton.dataset.contextid;
        let categoryid = null;
        let sortorder = null;
        let parent = null;
        const categoryItem = e.target.closest(SELECTORS.CATEGORY_ITEM);
        if (categoryItem) {
            contextid = categoryItem.dataset.contextid;
            categoryid = categoryItem.dataset.categoryid;
            sortorder = categoryItem.dataset.sortorder;
            const parentContext = categoryItem.closest(SELECTORS.CONTEXT);
            parent = categoryItem.dataset.parent + ',' + parentContext.dataset.contextid;
        }

        // Call the modal.
        const modalForm = new ModalForm({
            formClass: "qbank_managecategories\\form\\question_category_edit_form",
            args: {
                cmid,
                courseid,
                questioncount,
                contextid,
                categoryid,
                sortorder,
                parent,
            },
            modalConfig: {
                title: title,
                large: true,
            },
            saveButtonText: save,
            returnFocus: addEditButton,
        });
        // Once the form has been submitted via the web service, update the state with the new or updated
        // category based on the web service response.
        modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, (response) => {
            categorymanager.stateManager.processUpdates(response.detail);
        });
        // Show the form.
        modalForm.show();
    }
}

export const categorymanager = new CategoryManager({
    name: 'qtype_managecategories_categorymanager',
    eventName: eventTypes.qbankManagecategoriesStateUpdated,
    eventDispatch: notifyQbankManagecategoriesStateUpdated,
    mutations,
});

/**
 * Load the initial state.
 */
export const init = () => {
    loadState(categorymanager);
};
