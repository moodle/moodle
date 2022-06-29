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
import jQuery from 'jquery';
import Tree from 'core/tree';
import {getList} from 'core/normalise';

export default class extends Tree {

    /**
     * Setup the core/tree keyboard navigation.
     *
     * @param {Element|undefined} mainElement an alternative main element in case it is not from the parent component
     * @param {Object|undefined} selectors alternative selectors
     * @param {boolean} preventcache if the elements cache must be disabled.
     */
    constructor(mainElement, selectors, preventcache) {
        // Init this value with the parent DOM element.
        super(mainElement);

        // Get selectors from parent.
        this.selectors = {
            SECTION: selectors.SECTION,
            TOGGLER: selectors.TOGGLER,
            COLLAPSE: selectors.COLLAPSE,
            ENTER: selectors.ENTER ?? selectors.TOGGLER,
        };

        // The core/tree library saves the visible elements cache inside the main tree node.
        // However, in edit mode content can change suddenly so we need to refresh caches when needed.
        if (preventcache) {
            this._getVisibleItems = this.getVisibleItems;
            this.getVisibleItems = () => {
                this.refreshVisibleItemsCache();
                return this._getVisibleItems();
            };
        }
        // All jQuery events can be replaced when MDL-79179 is integrated.
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
            return getList(activeItem)[0];
        }
        return undefined;
    }

    /**
     * Handle enter key on a collpasible node.
     *
     * @param {JQuery} jQueryItem the jQuery object
     */
    enterCallback(jQueryItem) {
        const item = getList(jQueryItem)[0];
        if (this.isGroupItem(jQueryItem)) {
            // Group elements is like clicking a topic but without loosing the focus.
            const enter = item.querySelector(this.selectors.ENTER);
            if (enter.getAttribute('href') !== '#') {
                window.location.href = enter.getAttribute('href');
            }
            enter.click();
        } else {
            // Activity links just follow the link href.
            const link = item.querySelector('a');
            if (link.getAttribute('href') !== '#') {
                window.location.href = link.getAttribute('href');
            } else {
                link.click();
            }
            return;
        }
    }

    /**
     * Handle an item click.
     *
     * @param {Event} event the click event
     * @param {jQuery} jQueryItem the item clicked
     */
    handleItemClick(event, jQueryItem) {
        const isChevron = event.target.closest(this.selectors.COLLAPSE);
        // Only chevron clicks toogle the sections always.
        if (isChevron) {
            super.handleItemClick(event, jQueryItem);
            return;
        }
        // This is a title or activity name click.
        jQueryItem.focus();
        if (this.isGroupItem(jQueryItem)) {
            this.expandGroup(jQueryItem);
        }
    }

    /**
     * Check if a gorup item is collapsed.
     *
     * @param {JQuery} jQueryItem  the jQuery object
     * @returns {boolean} if the element is collapsed
     */
    isGroupCollapsed(jQueryItem) {
        const item = getList(jQueryItem)[0];
        const toggler = item.querySelector(`[aria-expanded]`);
        return toggler.getAttribute('aria-expanded') === 'false';
    }

    /**
     * Toggle a group item.
     *
     * @param {JQuery} item  the jQuery object
     */
    toggleGroup(item) {
        // All jQuery in this segment of code can be replaced when MDL-79179 is integrated.
        const toggler = item.find(this.selectors.COLLAPSE);
        let collapsibleId = toggler.data('target') ?? toggler.attr('href');
        if (!collapsibleId) {
            return;
        }
        collapsibleId = collapsibleId.replace('#', '');

        // Bootstrap 4 uses jQuery to interact with collapsibles.
        const collapsible = jQuery(`#${collapsibleId}`);
        if (collapsible.length) {
            jQuery(`#${collapsibleId}`).collapse('toggle');
        }
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
        const togglers = getList(this.treeRoot)[0].querySelectorAll(this.selectors.SECTION);
        togglers.forEach(item => {
            this.expandGroup(jQuery(item));
        });
    }
}
