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
 * Javascript to enhance the paged content paging bar.
 *
 * @module     core/paging_bar
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
        ROOT: '[data-region="paging-bar"]',
        PAGE: '[data-page]',
        PAGE_ITEM: '[data-region="page-item"]',
        ACTIVE_PAGE_ITEM: '[data-region="page-item"].active'
    };

    /**
     * Get the page element by number.
     *
     * @param {object} root The root element.
     * @param {Number} pageNumber The page number.
     * @return {jQuery}
     */
    var getPageByNumber = function(root, pageNumber) {
        return root.find(SELECTORS.PAGE_ITEM + '[data-page-number="' + pageNumber + '"]');
    };

    /**
     * Get the last page number.
     *
     * @param {object} root The root element.
     * @return {int}
     */
    var getLastPageNumber = function(root) {
        var lastPage = root.find(SELECTORS.PAGE).last();
        if (lastPage) {
            return parseInt(lastPage.attr('data-page-number'), 10);
        } else {
            return null;
        }
    };

    /**
     * Get the active page number.
     *
     * @param {object} root The root element.
     * @returns {int} The page number
     */
    var getActivePageNumber = function(root) {
        var activePage = root.find(SELECTORS.ACTIVE_PAGE_ITEM);

        if (activePage.length) {
            return getPageNumber(root, activePage);
        } else {
            return null;
        }
    };

    /**
     * Get the page number.
     *
     * @param {object} root The root element.
     * @param {object} page The page.
     * @returns {int} The page number
     */
    var getPageNumber = function(root, page) {
        if (page.attr('data-page') != undefined) {
            // If it's an actual page then we can just use the page number
            // attribute.
            return parseInt(page.attr('data-page-number'), 10);
        }

        var pageNumber = 1;
        var activePageNumber = null;

        switch (page.attr('data-control')) {
            case 'first':
                pageNumber = 1;
                break;

            case 'last':
                pageNumber = getLastPageNumber(root);
                break;

            case 'next':
                activePageNumber = getActivePageNumber(root);
                var lastPage = getLastPageNumber(root);
                if (activePageNumber && activePageNumber < lastPage) {
                    pageNumber = activePageNumber + 1;
                } else {
                    pageNumber = lastPage;
                }
                break;

            case 'previous':
                activePageNumber = getActivePageNumber(root);
                if (activePageNumber && activePageNumber > 1) {
                    pageNumber = activePageNumber - 1;
                } else {
                    pageNumber = 1;
                }
                break;

            default:
                pageNumber = 1;
                break;
        }

        // Make sure we return an int not a string.
        return parseInt(pageNumber, 10);
    };

    /**
     * Get the limit of items for each page.
     *
     * @param {object} root The root element.
     * @returns {int}
     */
    var getLimit = function(root) {
        return parseInt(root.attr('data-items-per-page'), 10);
    };

    /**
     * Set page numbers on each of the given items. Page numbers are set
     * from 1..n (where n is the number of items).
     *
     * @param {jQuery} items A jQuery list of items.
     */
    var generatePageNumbers = function(items) {
        items.each(function(index, item) {
            item = $(item);
            item.attr('data-page-number', index + 1);
        });
    };

    /**
     * Make the paging bar item for the given page number visible and fire
     * the SHOW_PAGES paged content event to tell any listening content to
     * update.
     *
     * @param {object} root The root element.
     * @param {int} pageNumber The number for the page to show.
     * @param {object} page The page.
     */
    var showPage = function(root, pageNumber) {
        var isSamePage = pageNumber == getActivePageNumber(root);
        var limit = getLimit(root);
        var offset = (pageNumber - 1) * limit;

        if (!isSamePage) {
            // We only need to toggle the active class if the user didn't click
            // on the already active page.
            root.find(SELECTORS.PAGE_ITEM).removeClass('active');
            var page = getPageByNumber(root, pageNumber);
            page.addClass('active');
        }

        // This event requires a payload that contains a list of all pages that
        // were activated. In the case of the paging bar we only show one page at
        // a time.
        root.trigger(PagedContentEvents.SHOW_PAGES, [[{
            pageNumber: pageNumber,
            limit: limit,
            offset: offset
        }]]);
    };

    /**
     * Initialise the paging bar.
     * @param {object} root The root element.
     */
    var init = function(root) {
        root = $(root);
        var pages = root.find(SELECTORS.PAGE);
        generatePageNumbers(pages);

        var activePageNumber = getActivePageNumber(root);
        if (activePageNumber) {
            // If the the paging bar was rendered with an active page selected
            // then make sure we fired off the event to tell the content page to
            // show.
            showPage(root, activePageNumber);
        }

        CustomEvents.define(root, [
            CustomEvents.events.activate
        ]);

        root.on(CustomEvents.events.activate, SELECTORS.PAGE_ITEM, function(e, data) {
            var page = $(e.target).closest(SELECTORS.PAGE_ITEM);
            var pageNumber = getPageNumber(root, page);
            showPage(root, pageNumber);

            data.originalEvent.preventDefault();
            data.originalEvent.stopPropagation();
        });
    };

    return {
        init: init,
        rootSelector: SELECTORS.ROOT,
    };
});
