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
 * Course index keyboard navigation and aria-tree compatibility.
 *
 * Node tree and bootstrap collapsibles don't use the same HTML structure. However,
 * all keybindings and logic is compatible. This class translate the primitive opetations
 * to a bootstrap collapsible structure.
 *
 * @module     core_courseformat/local/courseindex/keyboardnav
 * @class      core_courseformat/local/courseindex/keyboardnav
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// The core/tree uses jQuery to expand all nodes.
import $ from 'jquery';
import Tree from 'core/tree';

export default class extends Tree {

    /**
     * Setup the core/tree keyboard navigation.
     *
     * @param {CourseIndex} parent the parent component
     */
    constructor(parent) {
        // Init this value with the parent DOM element.
        super(parent.element);

        // Get selectors from parent.
        this.selectors = parent.selectors;

        // The core/tree library saves the visible elements cache inside the main tree node.
        // However, in edit mode content can change suddenly so we need to refresh caches when needed.
        if (parent.reactive.isEditing) {
            this._getVisibleItems = this.getVisibleItems;
            this.getVisibleItems = () => {
                this.refreshVisibleItemsCache();
                return this._getVisibleItems();
            };
        }
        // Add jQuery events to detect boostrap collapse and uncollapse.
        this.treeRoot.on('hidden.bs.collapse shown.bs.collapse', () => {
            this.refreshVisibleItemsCache();
        });
        // Register a custom callback for pressing enter key.
        this.registerEnterCallback(this.enterCallback.bind(this));
    }

    /**
     * Return the current active node.
     *
     * @return {Element|undefined} the active item if any
     */
    getActiveItem() {
        const activeItem = this.treeRoot.data('activeItem');
        if (activeItem) {
            return activeItem.get(0);
        }
        return undefined;
    }

    /**
     * Handle enter key on a collpasible node.
     *
     * @param {JQuery} item the jQuery object
     */
    enterCallback(item) {
        if (this.isGroupItem(item)) {
            // Group elements is like clicking a topic but without loosing the focus.
            window.location.href = item.find(this.selectors.TOGGLER).first().attr('href');
            item.find(this.selectors.TOGGLER).get(0).click();
        } else {
            // Activity links just follow the link href.
            window.location.href = item.find('a').first().attr('href');
            return;
        }
    }

    /**
     * Check if a gorup item is collapsed.
     *
     * @param {JQuery} item  the jQuery object
     * @returns {boolean} if the element is collapsed
     */
    isGroupCollapsed(item) {
        const toggler = item.find(`[aria-expanded]`);
        return toggler.attr('aria-expanded') === 'false';
    }

    /**
     * Toggle a group item.
     *
     * @param {JQuery} item  the jQuery object
     */
    toggleGroup(item) {
        const toggler = item.find(this.selectors.COLLAPSE);
        let collapsibleId = toggler.data('target') ?? toggler.attr('href');
        if (!collapsibleId) {
            return;
        }
        collapsibleId = collapsibleId.replace('#', '');

        // Bootstrap 4 uses jQuery to interact with collapsibles.
        $(`#${collapsibleId}`).collapse('toggle');
    }

    /**
     * Expand a group item.
     *
     * @param {JQuery} item  the jQuery object
     */
    expandGroup(item) {
        if (this.isGroupCollapsed(item)) {
            this.toggleGroup(item);
        }
    }

    /**
     * Collpase a group item.
     *
     * @param {JQuery} item  the jQuery object
     */
    collapseGroup(item) {
        if (!this.isGroupCollapsed(item)) {
            this.toggleGroup(item);
        }
    }

    /**
     * Expand all groups.
     */
    expandAllGroups() {
        const togglers = this.treeRoot.find(this.selectors.SECTION);
        togglers.each((index, item) => {
            this.expandGroup($(item));
        });
    }
}
