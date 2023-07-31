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
 * Base class for defining a bulk action.
 *
 * @module     core/bulkactions/bulk_action
 * @copyright  2023 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export default class BulkAction {

    /** @property {array} selectedItems The array of selected item elements. */
    selectedItems = [];

    /**
     * Registers the listener events for the bulk actions.
     *
     * @method registerListenerEvents
     * @param {HTMLElement} containerElement The container element for the bulk actions.
     * @returns {void}
     */
    registerListenerEvents(containerElement) {
        // Listen for the click event on the bulk action trigger element.
        containerElement.addEventListener('click', (e) => {
            if (e.target.closest(this.getBulkActionTriggerSelector())) {
                e.preventDefault();
                this.triggerBulkAction();
            }
        });
    }

    /**
     * Setter method for the selectedItems property.
     *
     * @method setSelectedItems
     * @param {Array} selectedItems The array of selected item elements..
     * @returns {void}
     */
    setSelectedItems(selectedItems) {
        this.selectedItems = selectedItems;
    }

    /**
     * Defines the selector of the element that triggers the bulk action.
     *
     * @method getBulkActionTriggerSelector
     * @returns {string}
     */
    getBulkActionTriggerSelector() {
        throw new Error(`getBulkActionTriggerSelector() must be implemented in ${this.constructor.name}`);
    }

    /**
     * Defines the behavior once the bulk action is triggered.
     *
     * @method triggerBulkAction
     */
    triggerBulkAction() {
        throw new Error(`triggerBulkAction() must be implemented in ${this.constructor.name}`);
    }

    /**
     * Renders the bulk action trigger element.
     *
     * @method renderBulkActionTrigger
     * @returns {Promise}
     */
    renderBulkActionTrigger() {
        throw new Error(`renderBulkActionTrigger() must be implemented in ${this.constructor.name}`);
    }
}
