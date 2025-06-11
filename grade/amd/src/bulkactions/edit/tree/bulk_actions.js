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

import BulkActions from "core/bulkactions/bulk_actions";
import GradebookEditTreeBulkMove from "core_grades/bulkactions/edit/tree/move";

/**
 * Class for defining the bulk actions area in the gradebook setup page.
 *
 * @module     core_grades/bulkactions/edit/tree/bulk_actions
 * @copyright  2023 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const Selectors = {
    selectBulkItemCheckbox: 'input[type="checkbox"].itemselect'
};

export default class GradebookEditTreeBulkActions extends BulkActions {

    /** @property {int|null} courseID The course ID. */
    courseID = null;

    /**
     * Returns the instance of the class.
     *
     * @param {int} courseID
     * @returns {GradebookEditTreeBulkActions}
     */
    static init(courseID) {
        return new this(courseID);
    }

    /**
     * The class constructor.
     *
     * @param {int} courseID The course ID.
     * @returns {void}
     */
    constructor(courseID) {
        super();
        this.courseID = courseID;
    }

    /**
     * Returns the array of the relevant bulk action objects for the gradebook setup page.
     *
     * @method getBulkActions
     * @returns {Array}
     */
    getBulkActions() {
        return [
            new GradebookEditTreeBulkMove(this.courseID)
        ];
    }

    /**
     * Returns the array of selected items.
     *
     * @method getSelectedItems
     * @returns {Array}
     */
    getSelectedItems() {
        return document.querySelectorAll(`${Selectors.selectBulkItemCheckbox}:checked`);
    }

    /**
     * Adds the listener for the item select change event.
     *
     * @method registerItemSelectChangeEvent
     * @param {function} eventHandler The event handler function.
     * @returns {void}
     */
    registerItemSelectChangeEvent(eventHandler) {
        const itemSelectCheckboxes = document.querySelectorAll(Selectors.selectBulkItemCheckbox);
        itemSelectCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', eventHandler.bind(this));
        });
    }

    /**
     * Defines the action for deselecting a selected item.
     *
     * @method deselectItem
     * @param {HTMLElement} selectedItem The selected element.
     * @returns {void}
     */
    deselectItem(selectedItem) {
        selectedItem.checked = false;
    }
}
