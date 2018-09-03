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
 * Javascript to manage the paging dropdown control.
 *
 * @module     core/paged_content_paging_dropdown
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
    [
        'jquery',
        'core/custom_interaction_events',
        'core/paged_content_events'
    ],
    function(
        $,
        CustomEvents,
        PagedContentEvents
    ) {

    var SELECTORS = {
        ROOT: '[data-region="paging-dropdown-container"]',
        DROPDOWN_ITEM: '[data-region="dropdown-item"]',
        DROPDOWN_TOGGLE: '[data-region="dropdown-toggle"]',
        ACTIVE_DROPDOWN_ITEM: '[data-region="dropdown-item"].active',
        CARET: '[data-region="caret"]'
    };

    /**
     * Get the page number.
     *
     * @param {jquery} item The dropdown item.
     * @returns {int}
     */
    var getPageNumber = function(item) {
        return parseInt(item.attr('data-page-number'), 10);
    };

    /**
     * Get all paging dropdown items.
     *
     * @param {jquery} root The root element.
     * @returns {jquery} A jquery object with all items.
     */
    var getAllItems = function(root) {
        return root.find(SELECTORS.DROPDOWN_ITEM);
    };

    /**
     * Get all paging dropdown items with lower page numbers than the given
     * dropdown item.
     *
     * @param {jquery} root The root element.
     * @param {jquery} item The dropdown item.
     * @returns {jquery} A jquery object with all items.
     */
    var getPreviousItems = function(root, item) {
        var pageNumber = getPageNumber(item);
        return getAllItems(root).filter(function(index, element) {
            return getPageNumber($(element)) < pageNumber;
        });
    };

    /**
     * Get the number of items to be loaded for the dropdown item.
     *
     * @param {jquery} item The dropdown item.
     * @returns {int}
     */
    var getLimit = function(item) {
        return parseInt(item.attr('data-item-count'), 10);
    };

    /**
     * Get the offset of items from the start of the itemset for the given
     * dropdown item.
     *
     * @param {jquery} root The root element.
     * @param {jquery} item The dropdown item.
     * @returns {int}
     */
    var getOffset = function(root, item) {
        if (item.attr('data-offset') != undefined) {
            return parseInt(item.attr('data-offset'), 10);
        }

        var offset = 0;

        getPreviousItems(root, item).each(function(index, prevItem) {
            prevItem = $(prevItem);
            offset += getLimit(prevItem);
        });

        item.attr('data-offset', offset);
        return offset;
    };

    /**
     * Get the active dropdown item.
     *
     * @param {jquery} root The root element.
     * @returns {jquery} The active dropdown item.
     */
    var getActiveItem = function(root) {
        return root.find(SELECTORS.ACTIVE_DROPDOWN_ITEM);
    };

    /**
     * Create the event payload for the list of dropdown items. The event payload
     * is an array of objects with one object per dropdown item.
     *
     * Each payload object contains the page number, limit, and offset for the
     * corresponding dropdown item.
     *
     * For example: If we had 3 dropdown items with incrementing page numbers loading
     * 25 items per page then the generated payload would look like:
     * [
     *      {
     *          pageNumber: 1,
     *          limit: 25,
     *          offset: 0
     *      },
     *      {
     *          pageNumber: 2,
     *          limit: 25,
     *          offset: 25
     *      },
     *      {
     *          pageNumber: 3,
     *          limit: 25,
     *          offset: 50
     *      }
     * ]
     *
     * @param {jquery} root The root element.
     * @param {jquery} items The dropdown items.
     * @returns {object[]} The payload for the event.
     */
    var generateEventPayload = function(root, items) {
        return items.map(function(index, item) {
            item = $(item);
            return {
                pageNumber: getPageNumber(item),
                limit: getLimit(item),
                offset: getOffset(root, item),
            };
        }).get();
    };

    /**
     * Add page number attributes to each of the given items. The page numbers
     * start at 1 and increment by 1 for each item, e.g. 1, 2, 3 etc.
     *
     * @param {jquery} items The dropdown items.
     */
    var generatePageNumbers = function(items) {
        items.each(function(index, item) {
            item = $(item);
            item.attr('data-page-number', index + 1);
        });
    };

    /**
     * Make the given item active by setting the active class on it and firing
     * the SHOW_PAGES event for the paged content to show the appropriate
     * pages.
     *
     * @param {jquery} root The root element.
     * @param {jquery} item The dropdown item.
     */
    var setActiveItem = function(root, item) {
        var prevItems = getPreviousItems(root, item);
        var allItems = prevItems.add(item);
        var eventPayload = generateEventPayload(root, allItems);
        var toggle = root.find(SELECTORS.DROPDOWN_TOGGLE);
        var caret = toggle.find(SELECTORS.CARET);

        getActiveItem(root).removeClass('active');
        item.addClass('active');

        // Update the dropdown toggle to show which item is selected.
        toggle.html(item.text());
        // Bootstrap 2 compatibility.
        toggle.append(caret);
        // Fire the event to tell the content to update.
        root.trigger(PagedContentEvents.SHOW_PAGES, [eventPayload]);
    };

    /**
     * Initialise the module by firing the SHOW_PAGES event for an existing
     * active page found and setting up the event listener for the user to select
     * new pages.
     *
     * @param {object} root The root element.
     */
    var init = function(root) {
        root = $(root);
        var items = getAllItems(root);
        generatePageNumbers(items);

        var activeItem = getActiveItem(root);
        if (activeItem.length) {
            // Fire the first event for the content to make sure it's visible.
            setActiveItem(root, activeItem);
        }

        CustomEvents.define(root, [
            CustomEvents.events.activate
        ]);

        root.on(CustomEvents.events.activate, SELECTORS.DROPDOWN_ITEM, function(e, data) {
            var item = $(e.target).closest(SELECTORS.DROPDOWN_ITEM);
            setActiveItem(root, item);

            data.originalEvent.preventDefault();
        });
    };

    return {
        init: init,
        rootSelector: SELECTORS.ROOT,
    };
});
