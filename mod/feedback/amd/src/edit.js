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
 * Edit items in feedback module
 *
 * @module     mod_feedback/edit
 * @copyright  2016 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";

import {addIconToContainerRemoveOnCompletion} from 'core/loadingicon';
import Notification from 'core/notification';
import Pending from 'core/pending';
import {prefetchStrings} from 'core/prefetch';
import SortableList from 'core/sortable_list';
import {getString, getStrings} from 'core/str';
import {add as addToast} from 'core/toast';
import {reorderQuestions} from 'mod_feedback/local/repository';

const Selectors = {
    deleteQuestionButton: '[data-action="delete"]',
    sortableListRegion: '[data-region="questions-sortable-list"]',
    sortableElement: '[data-region="questions-sortable-list"] .feedback_itemlist[id]',
    sortableElementTitle: '[data-region="item-title"]',
};

/**
 * Returns the Feedback question item id from the DOM id of an item.
 *
 * @param {String} id The dom id, f.g.: feedback_item_22
 * @return int
 */
const getItemId = (id) => {
    return Number(id.replace(/^.*feedback_item_/i, ''));
};

/**
 * Returns the order of the items in the sortable list.
 *
 * @param {Element} element The element to get the order from.
 * @return string
 */
const getItemOrder = (element) => {
    const sortableList = element.closest(Selectors.sortableListRegion);
    let itemOrder = [];
    sortableList.querySelectorAll(Selectors.sortableElement).forEach((item) => {
        itemOrder.push(getItemId(item.id));
    });
    return itemOrder.toString();
};

let initialized = false;
let moduleId = null;

/**
 * Initialise editor and all it's modules
 *
 * @param {Integer} cmId
 */
export const init = (cmId) => {

    moduleId = cmId;

    // Ensure we only add our listeners once (can be called multiple times).
    if (initialized) {
        return;
    }

    prefetchStrings('core', [
        'yes',
        'no',
    ]);
    prefetchStrings('admin', [
        'confirmation',
    ]);
    prefetchStrings('mod_feedback', [
        'confirmdeleteitem',
        'questionmoved',
    ]);

    // Add event listeners.
    document.addEventListener('click', async event => {

        // Delete question.
        const deleteButton = event.target.closest(Selectors.deleteQuestionButton);
        if (deleteButton) {
            event.preventDefault();
            const confirmationStrings = await getStrings([
                {key: 'confirmation', component: 'admin'},
                {key: 'confirmdeleteitem', component: 'mod_feedback'},
                {key: 'yes', component: 'core'},
                {key: 'no', component: 'core'},
            ]);
            Notification.confirm(...confirmationStrings, () => {
                window.location = deleteButton.getAttribute('href');
            });
            return;
        }
    });

    // Initialize sortable list to handle active conditions moving.
    const sortableList = new SortableList(document.querySelector(Selectors.sortableListRegion));
    sortableList.getElementName = element => Promise.resolve(element[0].querySelector(Selectors.sortableElementTitle)?.textContent);

    document.addEventListener(SortableList.EVENTS.elementDrop, event => {
        if (!event.detail.positionChanged) {
            return;
        }
        const pendingPromise = new Pending('mod_feedback/questions:reorder');
        const itemOrder = getItemOrder(event.detail.element[0]);
        addIconToContainerRemoveOnCompletion(event.detail.element[0], pendingPromise);
        reorderQuestions(moduleId, itemOrder)
            .then(() => getString('questionmoved', 'mod_feedback'))
            .then(addToast)
            .then(() => pendingPromise.resolve())
            .catch(Notification.exception);
    });

    initialized = true;
};
