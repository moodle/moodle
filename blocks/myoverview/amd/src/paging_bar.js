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
 * Javascript to load and render the paging bar.
 *
 * @module     block_myoverview/paging_bar
 * @package    block_myoverview
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/custom_interaction_events'],
        function($, CustomEvents) {

    var SELECTORS = {
        ROOT: '[data-region="paging-bar"]',
        PAGE_ITEM: '[data-region="page-item"]',
        ACTIVE_PAGE_ITEM: '[data-region="page-item"].active'
    };

    var EVENTS = {
        PAGE_SELECTED: 'block_myoverview-paging-bar-page-selected',
    };

    /**
     * Get the page element by number.
     *
     * @param {object} root The root element.
     * @param {Number} pageNumber The page number.
     * @returns {*}
     */
    var getPageByNumber = function(root, pageNumber) {
        return root.find(SELECTORS.PAGE_ITEM + '[data-page-number="' + pageNumber + '"]');
    };

    /**
     * Get the page number.
     *
     * @param {object} root The root element.
     * @param {object} page The page.
     * @returns {*} the page number
     */
    var getPageNumber = function(root, page) {
        var pageNumber = page.attr('data-page-number');

        if (pageNumber == 'first') {
            pageNumber = 1;
        } else if (pageNumber == 'last') {
            pageNumber = root.attr('data-page-count');
        }

        return pageNumber;
    };

    /**
     * Register event listeners for the module.
     * @param {object} root The root element.
     */
    var registerEventListeners = function(root) {
        root = $(root);
        CustomEvents.define(root, [
            CustomEvents.events.activate
        ]);

        root.on(CustomEvents.events.activate, SELECTORS.PAGE_ITEM, function(e, data) {
            var page = $(e.target).closest(SELECTORS.PAGE_ITEM);
            var activePage = root.find(SELECTORS.ACTIVE_PAGE_ITEM);
            var pageNumber = getPageNumber(root, page);
            var isSamePage = pageNumber == getPageNumber(root, activePage);

            if (!isSamePage) {
                root.find(SELECTORS.PAGE_ITEM).removeClass('active');
                getPageByNumber(root, pageNumber).addClass('active');
            }

            root.trigger(EVENTS.PAGE_SELECTED, [{
                pageNumber: pageNumber,
                isSamePage: isSamePage,
            }]);

            data.originalEvent.preventDefault();
        });
    };

    return {
        registerEventListeners: registerEventListeners,
        events: EVENTS,
        rootSelector: SELECTORS.ROOT,
    };
});
