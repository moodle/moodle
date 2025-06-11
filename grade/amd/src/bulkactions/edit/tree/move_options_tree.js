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
 * Keyboard navigation and aria-tree compatibility for the grade move options.
 *
 * @module     core_grades/bulkactions/edit/tree/move_options_tree
 * @copyright  2023 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Tree from 'core/tree';
import {getList} from 'core/normalise';

/** @constant {Object} The object containing the relevant selectors. */
const Selectors = {
    moveOptionsTree: '#destination-selector [role="tree"]',
    moveOption: '#destination-selector [role="treeitem"]',
    toggleGroupLink: '#destination-selector .collapse-list-link',
};

export default class MoveOptionsTree extends Tree {

    /** @property {function|null} afterSelectMoveOptionCallback Callback function to run after selecting a move option. */
    afterSelectMoveOptionCallback = null;

    /** @property {HTMLElement|null} selectedMoveOption The selected move option. */
    selectedMoveOption = null;

    /**
     * The class constructor.
     *
     * @param {function|null} afterSelectMoveOptionCallback Callback function used to define actions that should be run
     *                                                      after selecting a move option.
     * @returns {void}
     */
    constructor(afterSelectMoveOptionCallback) {
        super(Selectors.moveOptionsTree);
        this.afterSelectMoveOptionCallback = afterSelectMoveOptionCallback;
    }

    /**
     * Handle a key down event.
     *
     * @method handleKeyDown
     * @param {Event} e The event.
     */
    handleKeyDown(e) {
        // If the user presses enter or space, select the item.
        if (e.keyCode === this.keys.enter || e.keyCode === this.keys.space) {
            this.selectMoveOption(e.target);
        } else { // Otherwise, let the default behaviour happen.
            super.handleKeyDown(e);
        }
    }

    /**
     * Handle an item click.
     *
     * @param {Event} event The click event.
     * @param {jQuery} item The item clicked.
     * @returns {void}
     */
    handleItemClick(event, item) {
        const isToggleGroupLink = event.target.closest(Selectors.toggleGroupLink);
        // If the click is on the toggle group (chevron) link, let the default behaviour happen.
        if (isToggleGroupLink) {
            super.handleItemClick(event, item);
            return;
        }
        // If the click is on the item itself, select it.
        this.selectMoveOption(getList(item)[0]);
    }

    /**
     * Select a move option.
     *
     * @method selectMoveOption
     * @param {HTMLElement} moveOption The move option to select.
     */
    selectMoveOption(moveOption) {
        // Create the cache of the visible items.
        this.refreshVisibleItemsCache();
        // Deselect all the move options.
        document.querySelectorAll(Selectors.moveOption).forEach(item => {
            item.dataset.selected = "false";
        });
        // Select and set the focus on the specified move option.
        moveOption.dataset.selected = "true";
        this.selectedMoveOption = moveOption;
        moveOption.focus();
        // Call the callback function if it is defined.
        if (typeof this.afterSelectMoveOptionCallback === 'function') {
            this.afterSelectMoveOptionCallback();
        }
    }
}
