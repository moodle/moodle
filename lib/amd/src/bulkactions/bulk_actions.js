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

import Templates from 'core/templates';
import {get_string as getString} from 'core/str';
import {disableStickyFooter, enableStickyFooter} from 'core/sticky-footer';

/**
 * Base class for defining a bulk actions area within a page.
 *
 * @module     core/bulkactions/bulk_actions
 * @copyright  2023 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** @constant {Object} The object containing the relevant selectors. */
const Selectors = {
    stickyFooterContainer: '#sticky-footer',
    selectedItemsCountContainer: '[data-type="bulkactions"] [data-for="bulkcount"]',
    cancelBulkActionModeElement: '[data-type="bulkactions"] [data-action="bulkcancel"]',
    bulkModeContainer: '[data-type="bulkactions"]',
    bulkActionsContainer: '[data-type="bulkactions"] [data-for="bulktools"]'
};

export default class BulkActions {

    /** @property {string|null} initialStickyFooterContent The initial content of the sticky footer. */
    initialStickyFooterContent = null;

    /** @property {Array} selectedItems The array of selected item elements. */
    selectedItems = [];

    /** @property {boolean} isBulkActionsModeEnabled Whether the bulk actions mode is enabled. */
    isBulkActionsModeEnabled = false;

    /**
     * @property {int} maxButtons Sets the maximum number of action buttons to display. If exceeded, additional actions
     *                            are shown in a dropdown menu.
     */
    maxButtons = 5;

    /**
     * The class constructor.
     *
     * @param {int|null} maxButtons Sets the maximum number of action buttons to display. If exceeded, additional actions
     *                              are shown in a dropdown menu.
     * @returns {void}
     */
    constructor(maxButtons = null) {
        if (!this.getStickyFooterContainer()) {
            throw new Error('Sticky footer not found.');
        }
        // Store any pre-existing content in the sticky footer. When bulk actions mode is enabled, this content will be
        // replaced with the bulk actions content and restored when bulk actions mode is disabled.
        this.initialStickyFooterContent = this.getStickyFooterContainer().innerHTML;
        if (maxButtons) {
            this.maxButtons = maxButtons;
        }
        // Register and handle the item select change event.
        this.registerItemSelectChangeEvent(async() => {
            this.selectedItems = this.getSelectedItems();
            if (this.selectedItems.length > 0) { // At least one item is selected.
                // If the bulk actions mode is already enabled only update the selected items count.
                if (this.isBulkActionsModeEnabled) {
                    await this.updateBulkItemSelection();
                } else { // Otherwise, enable the bulk action mode.
                    await this.enableBulkActionsMode();
                }
            } else { // No items are selected, disable the bulk action mode.
                this.disableBulkActionsMode();
            }
        });
    }

    /**
     * Returns the array of the relevant bulk action objects.
     *
     * @method getBulkActions
     * @returns {Array}
     */
    getBulkActions() {
        throw new Error(`getBulkActions() must be implemented in ${this.constructor.name}`);
    }

    /**
     * Returns the array of selected items.
     *
     * @method getSelectedItems
     * @returns {Array}
     */
    getSelectedItems() {
        throw new Error(`getSelectedItems() must be implemented in ${this.constructor.name}`);
    }

    /**
     * Adds the listener for the item select change event.
     * The event handler function that is passed as a parameter should be called right after the event is triggered.
     *
     * @method registerItemSelectChangeEvent
     * @param {function} eventHandler The event handler function.
     * @returns {void}
     */
    registerItemSelectChangeEvent(eventHandler) {
        throw new Error(`registerItemSelectChangeEvent(${eventHandler}) must be implemented in ${this.constructor.name}`);
    }

    /**
     * Defines the action for deselecting a selected item.
     *
     * The base bulk actions class supports deselecting all selected items but does not have knowledge of the type of the
     * selected element. Therefore, each subclass must explicitly define the action of resetting the attributes that
     * indicate a selected state.
     *
     * @method deselectItem
     * @param {HTMLElement} selectedItem The selected element.
     * @returns {void}
     */
    deselectItem(selectedItem) {
        throw new Error(`deselectItem(${selectedItem}) must be implemented in ${this.constructor.name}`);
    }

    /**
     * Returns the sticky footer container.
     *
     * @method getStickyFooterContainer
     * @returns {HTMLElement}
     */
    getStickyFooterContainer() {
        return document.querySelector(Selectors.stickyFooterContainer);
    }

    /**
     * Enables the bulk action mode.
     *
     * @method enableBulkActionsMode
     * @returns {Promise<void>}
     */
    async enableBulkActionsMode() {
        // Make sure that the sticky footer is enabled.
        enableStickyFooter();
        // Render the bulk actions content in the sticky footer container.
        this.getStickyFooterContainer().innerHTML = await this.renderBulkActions();
        const bulkModeContainer = this.getStickyFooterContainer().querySelector(Selectors.bulkModeContainer);
        const bulkActionsContainer = bulkModeContainer.querySelector(Selectors.bulkActionsContainer);
        this.getBulkActions().forEach((bulkAction) => {
            // Register the listener events for each available bulk action.
            bulkAction.registerListenerEvents(bulkActionsContainer);
            // Set the selected items for each available bulk action.
            bulkAction.setSelectedItems(this.selectedItems);
        });
        // Register the click listener event for the cancel bulk mode button.
        bulkModeContainer.addEventListener('click', (e) => {
            if (e.target.closest(Selectors.cancelBulkActionModeElement)) {
                // Deselect all selected items.
                this.selectedItems.forEach((item) => {
                    this.deselectItem(item);
                });
                // Disable the bulk action mode.
                this.disableBulkActionsMode();
            }
        });
        this.isBulkActionsModeEnabled = true;

        // Calling `renderBulkActions()` already renders the item selection count.
        // Because of this, screen readers will not announce the item selection count on first load given that aria-live regions
        // must be present in the DOM and have their contents changed before screen readers can announce their content.
        // So, we call `updateBulkItemSelection()` after a short delay to ensure that screen readers announce the item count.
        setTimeout(async() => {
            await this.updateBulkItemSelection();
        }, 300);
    }

    /**
     * Disables the bulk action mode.
     *
     * @method disableBulkActionsMode
     * @returns {void}
     */
    disableBulkActionsMode() {
        // If there was any previous (initial) content in the sticky footer, restore it.
        if (this.initialStickyFooterContent.length > 0) {
            this.getStickyFooterContainer().innerHTML = this.initialStickyFooterContent;
        } else { // No previous content to restore, disable the sticky footer.
            disableStickyFooter();
        }
        this.isBulkActionsModeEnabled = false;
    }

    /**
     * Renders the bulk actions content.
     *
     * @method renderBulkActions
     * @returns {Promise<string>}
     */
    async renderBulkActions() {
        const data = {
            bulkselectioncount: this.selectedItems.length,
            actions: [],
            moreactions: [],
            hasmoreactions: false,
        };
        const bulkActions = this.getBulkActions();
        const showMoreButton = bulkActions.length > this.maxButtons;

        // Get all bulk actions and render them in order.
        const actions = await Promise.all(
            bulkActions.map((bulkAction, index) =>
                bulkAction.renderBulkActionTrigger(
                    showMoreButton && (index >= this.maxButtons - 1),
                    index
                )
            )
        );

        // Separate rendered actions into data.actions and data.moreactions in the correct order.
        actions.forEach((actionTrigger, index) => {
            if (showMoreButton && (index >= this.maxButtons - 1)) {
                data.moreactions.push({'actiontrigger': actionTrigger});
            } else {
                data.actions.push({'actiontrigger': actionTrigger});
            }
        });

        data.hasmoreactions = data.moreactions.length > 0;

        return Templates.render('core/bulkactions/bulk_actions', data);
    }

    /**
     * Updates the selected items count in the bulk actions content.
     *
     * @method updateBulkItemSelection
     * @returns {void}
     */
    async updateBulkItemSelection() {
        const bulkSelection = await getString('bulkselection', 'core', this.selectedItems.length);
        document.querySelector(Selectors.selectedItemsCountContainer).innerHTML = bulkSelection;
    }
}
