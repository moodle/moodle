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
        'core/paged_content_events',
        'core/str',
        'core/pubsub'
    ],
    function(
        $,
        CustomEvents,
        PagedContentEvents,
        Str,
        PubSub
    ) {

    var SELECTORS = {
        ROOT: '[data-region="paging-bar"]',
        PAGE: '[data-page]',
        PAGE_ITEM: '[data-region="page-item"]',
        PAGE_LINK: '[data-region="page-link"]',
        FIRST_BUTTON: '[data-control="first"]',
        LAST_BUTTON: '[data-control="last"]',
        NEXT_BUTTON: '[data-control="next"]',
        PREVIOUS_BUTTON: '[data-control="previous"]'
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
     * Get the next button element.
     *
     * @param {object} root The root element.
     * @return {jQuery}
     */
    var getNextButton = function(root) {
        return root.find(SELECTORS.NEXT_BUTTON);
    };

    /**
     * Set the last page number after which no more pages
     * should be loaded.
     *
     * @param {object} root The root element.
     * @param {Number} number Page number.
     */
    var setLastPageNumber = function(root, number) {
        root.attr('data-last-page-number', number);
    };

    /**
     * Get the last page number.
     *
     * @param {object} root The root element.
     * @return {Number}
     */
    var getLastPageNumber = function(root) {
        return parseInt(root.attr('data-last-page-number'), 10);
    };

    /**
     * Get the active page number.
     *
     * @param {object} root The root element.
     * @returns {Number} The page number
     */
    var getActivePageNumber = function(root) {
        return parseInt(root.attr('data-active-page-number'), 10);
    };

    /**
     * Set the active page number.
     *
     * @param {object} root The root element.
     * @param {Number} number Page number.
     */
    var setActivePageNumber = function(root, number) {
        root.attr('data-active-page-number', number);
    };

    /**
     * Check if there is an active page number.
     *
     * @param {object} root The root element.
     * @returns {bool}
     */
    var hasActivePageNumber = function(root) {
        var number = getActivePageNumber(root);
        return !isNaN(number) && number != 0;
    };

    /**
     * Get the page number for a given page.
     *
     * @param {object} root The root element.
     * @param {object} page The page element.
     * @returns {Number} The page number
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
                if (!lastPage) {
                    pageNumber = activePageNumber + 1;
                } else if (activePageNumber && activePageNumber < lastPage) {
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
     * @returns {Number}
     */
    var getLimit = function(root) {
        return parseInt(root.attr('data-items-per-page'), 10);
    };

    /**
     * Set the limit of items for each page.
     *
     * @param {object} root The root element.
     * @param {Number} limit Items per page limit.
     */
    var setLimit = function(root, limit) {
        root.attr('data-items-per-page', limit);
    };

    /**
     * Show the paging bar.
     *
     * @param {object} root The root element.
     */
    var show = function(root) {
        root.removeClass('hidden');
    };

    /**
     * Hide the paging bar.
     *
     * @param {object} root The root element.
     */
    var hide = function(root) {
        root.addClass('hidden');
    };

    /**
     * Disable the next and last buttons in the paging bar.
     *
     * @param {object} root The root element.
     */
    var disableNextControlButtons = function(root) {
        var nextButton = root.find(SELECTORS.NEXT_BUTTON);
        var lastButton = root.find(SELECTORS.LAST_BUTTON);

        nextButton.addClass('disabled');
        nextButton.attr('aria-disabled', true);
        lastButton.addClass('disabled');
        lastButton.attr('aria-disabled', true);
    };

    /**
     * Enable the next and last buttons in the paging bar.
     *
     * @param {object} root The root element.
     */
    var enableNextControlButtons = function(root) {
        var nextButton = root.find(SELECTORS.NEXT_BUTTON);
        var lastButton = root.find(SELECTORS.LAST_BUTTON);

        nextButton.removeClass('disabled');
        nextButton.removeAttr('aria-disabled');
        lastButton.removeClass('disabled');
        lastButton.removeAttr('aria-disabled');
    };

    /**
     * Disable the previous and first buttons in the paging bar.
     *
     * @param {object} root The root element.
     */
    var disablePreviousControlButtons = function(root) {
        var previousButton = root.find(SELECTORS.PREVIOUS_BUTTON);
        var firstButton = root.find(SELECTORS.FIRST_BUTTON);

        previousButton.addClass('disabled');
        previousButton.attr('aria-disabled', true);
        firstButton.addClass('disabled');
        firstButton.attr('aria-disabled', true);
    };

    /**
     * Enable the previous and first buttons in the paging bar.
     *
     * @param {object} root The root element.
     */
    var enablePreviousControlButtons = function(root) {
        var previousButton = root.find(SELECTORS.PREVIOUS_BUTTON);
        var firstButton = root.find(SELECTORS.FIRST_BUTTON);

        previousButton.removeClass('disabled');
        previousButton.removeAttr('aria-disabled');
        firstButton.removeClass('disabled');
        firstButton.removeAttr('aria-disabled');
    };

    /**
     * Get the components for a get_string request for the aria-label
     * on a page. The value is a comma separated string of key and
     * component.
     *
     * @param {object} root The root element.
     * @return {array} First element is the key, second is the component.
     */
    var getPageAriaLabelComponents = function(root) {
        var componentString = root.attr('data-aria-label-components-pagination-item');
        var components = componentString.split(',').map(function(component) {
            return component.trim();
        });
        return components;
    };

    /**
     * Get the components for a get_string request for the aria-label
     * on an active page. The value is a comma separated string of key and
     * component.
     *
     * @param {object} root The root element.
     * @return {array} First element is the key, second is the component.
     */
    var getActivePageAriaLabelComponents = function(root) {
        var componentString = root.attr('data-aria-label-components-pagination-active-item');
        var components = componentString.split(',').map(function(component) {
            return component.trim();
        });
        return components;
    };

    /**
     * Set page numbers on each of the given items. Page numbers are set
     * from 1..n (where n is the number of items).
     *
     * Sets the active page number to be the last page found with
     * an "active" class (if any).
     *
     * Sets the last page number.
     *
     * @param {object} root The root element.
     * @param {jQuery} items A jQuery list of items.
     */
    var generatePageNumbers = function(root, items) {
        var lastPageNumber = 0;
        setActivePageNumber(root, 0);

        items.each(function(index, item) {
            var pageNumber = index + 1;
            item = $(item);
            item.attr('data-page-number', pageNumber);
            lastPageNumber++;

            if (item.hasClass('active')) {
                setActivePageNumber(root, pageNumber);
            }
        });

        setLastPageNumber(root, lastPageNumber);
    };

    /**
     * Set the aria-labels on each of the page items in the paging bar.
     * This includes the next, previous, first, and last items.
     *
     * @param {object} root The root element.
     */
    var generateAriaLabels = function(root) {
        var pageAriaLabelComponents = getPageAriaLabelComponents(root);
        var activePageAriaLabelComponents = getActivePageAriaLabelComponents(root);
        var activePageNumber = getActivePageNumber(root);
        var pageItems = root.find(SELECTORS.PAGE_ITEM);
        // We want to request all of the strings at once rather than
        // one at a time.
        var stringRequests = pageItems.map(function(index, page) {
            page = $(page);
            var pageNumber = getPageNumber(root, page);

            if (pageNumber === activePageNumber) {
                return {
                    key: activePageAriaLabelComponents[0],
                    component: activePageAriaLabelComponents[1],
                    param: pageNumber
                };
            } else {
                return {
                    key: pageAriaLabelComponents[0],
                    component: pageAriaLabelComponents[1],
                    param: pageNumber
                };
            }
        });

        Str.get_strings(stringRequests).then(function(strings) {
            pageItems.each(function(index, page) {
                page = $(page);
                var string = strings[index];
                page.attr('aria-label', string);
                page.find(SELECTORS.PAGE_LINK).attr('aria-label', string);
            });

            return strings;
        })
        .catch(function() {
            // No need to interrupt the page if we can't load the aria lang strings.
            return;
        });
    };

    /**
     * Make the paging bar item for the given page number visible and fire
     * the SHOW_PAGES paged content event to tell any listening content to
     * update.
     *
     * @param {object} root The root element.
     * @param {Number} pageNumber The number for the page to show.
     * @param {string} id A uniqie id for this instance.
     */
    var showPage = function(root, pageNumber, id) {
        var lastPageNumber = getLastPageNumber(root);
        var isSamePage = pageNumber == getActivePageNumber(root);
        var limit = getLimit(root);
        var offset = (pageNumber - 1) * limit;

        if (!isSamePage) {
            // We only need to toggle the active class if the user didn't click
            // on the already active page.
            root.find(SELECTORS.PAGE_ITEM).removeClass('active').removeAttr('aria-current');
            var page = getPageByNumber(root, pageNumber);
            page.addClass('active');
            page.attr('aria-current', true);
            setActivePageNumber(root, pageNumber);
        }

        // Make sure the control buttons are disabled as the user navigates
        // to either end of the limits.
        if (lastPageNumber && pageNumber >= lastPageNumber) {
            disableNextControlButtons(root);
        } else {
            enableNextControlButtons(root);
        }

        if (pageNumber > 1) {
            enablePreviousControlButtons(root);
        } else {
            disablePreviousControlButtons(root);
        }

        generateAriaLabels(root);

        // This event requires a payload that contains a list of all pages that
        // were activated. In the case of the paging bar we only show one page at
        // a time.
        PubSub.publish(id + PagedContentEvents.SHOW_PAGES, [{
            pageNumber: pageNumber,
            limit: limit,
            offset: offset
        }]);
    };

    /**
     * Add event listeners for interactions with the paging bar as well as listening
     * for custom paged content events.
     *
     * Each event will trigger different logic to update parts of the paging bar's
     * display.
     *
     * @param {object} root The root element.
     * @param {string} id A uniqie id for this instance.
     */
    var registerEventListeners = function(root, id) {
        var ignoreControlWhileLoading = root.attr('data-ignore-control-while-loading');
        var loading = false;

        if (ignoreControlWhileLoading == "") {
            // Default to ignoring control while loading if not specified.
            ignoreControlWhileLoading = true;
        }

        CustomEvents.define(root, [
            CustomEvents.events.activate
        ]);

        root.on(CustomEvents.events.activate, SELECTORS.PAGE_ITEM, function(e, data) {
            data.originalEvent.preventDefault();
            data.originalEvent.stopPropagation();

            if (ignoreControlWhileLoading && loading) {
                // Do nothing if configured to ignore control while loading.
                return;
            }

            var page = $(e.target).closest(SELECTORS.PAGE_ITEM);

            if (!page.hasClass('disabled')) {
                var pageNumber = getPageNumber(root, page);
                showPage(root, pageNumber, id);
                loading = true;
            }
        });

        // This event is fired when all of the items have been loaded. Typically used
        // in an "infinite" pages context when we don't know the exact number of pages
        // ahead of time.
        PubSub.subscribe(id + PagedContentEvents.ALL_ITEMS_LOADED, function(pageNumber) {
            loading = false;
            var currentLastPage = getLastPageNumber(root);

            if (!currentLastPage || pageNumber < currentLastPage) {
                // Somehow the value we've got saved is higher than the new
                // value we just received. Perhaps events came out of order.
                // In any case, save the lowest value.
                setLastPageNumber(root, pageNumber);
            }

            if (pageNumber === 1 && root.attr('data-hide-control-on-single-page')) {
                // If all items were loaded on the first page then we can hide
                // the paging bar because there are no other pages to load.
                hide(root);
                disableNextControlButtons(root);
                disablePreviousControlButtons(root);
            } else {
                show(root);
                disableNextControlButtons(root);
            }
        });

        // This event is fired after all of the requested pages have been rendered.
        PubSub.subscribe(id + PagedContentEvents.PAGES_SHOWN, function() {
            // All pages have been shown so turn off the loading flag.
            loading = false;
        });

        // This is triggered when the paging limit is modified.
        PubSub.subscribe(id + PagedContentEvents.SET_ITEMS_PER_PAGE_LIMIT, function(limit) {
            // Update the limit.
            setLimit(root, limit);
            setLastPageNumber(root, 0);
            setActivePageNumber(root, 0);
            show(root);
            // Reload the data from page 1 again.
            showPage(root, 1, id);
        });
    };

    /**
     * Initialise the paging bar.
     * @param {object} root The root element.
     * @param {string} id A uniqie id for this instance.
     */
    var init = function(root, id) {
        root = $(root);
        var pages = root.find(SELECTORS.PAGE);
        generatePageNumbers(root, pages);
        registerEventListeners(root, id);

        if (hasActivePageNumber(root)) {
            var activePageNumber = getActivePageNumber(root);
            // If the the paging bar was rendered with an active page selected
            // then make sure we fired off the event to tell the content page to
            // show.
            getPageByNumber(root, activePageNumber).click();
            if (activePageNumber == 1) {
                // If the first page is active then disable the previous buttons.
                disablePreviousControlButtons(root);
            }
        } else {
            // There was no active page number so load the first page using
            // the next button. This allows the infinite pagination to work.
            getNextButton(root).click();
        }
    };

    return {
        init: init,
        showPage: showPage,
        rootSelector: SELECTORS.ROOT,
    };
});
