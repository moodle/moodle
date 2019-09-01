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
 * Factory to create a paged content widget.
 *
 * @module     core/paged_content_factory
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
[
    'jquery',
    'core/templates',
    'core/notification',
    'core/paged_content',
    'core/paged_content_events',
    'core/pubsub',
    'core/ajax'
],
function(
    $,
    Templates,
    Notification,
    PagedContent,
    PagedContentEvents,
    PubSub,
    Ajax
) {
    var TEMPLATES = {
        PAGED_CONTENT: 'core/paged_content'
    };

    var DEFAULT = {
        ITEMS_PER_PAGE_SINGLE: 25,
        ITEMS_PER_PAGE_ARRAY: [25, 50, 100, 0],
        MAX_PAGES: 3
    };

    /**
     * Get the default context to render the paged content mustache
     * template.
     *
     * @return {object}
     */
    var getDefaultTemplateContext = function() {
        return {
            pagingbar: false,
            pagingdropdown: false,
            skipjs: true,
            ignorecontrolwhileloading: true,
            controlplacementbottom: false
        };
    };

    /**
     * Get the default context to render the paging bar mustache template.
     *
     * @return {object}
     */
    var getDefaultPagingBarTemplateContext = function() {
        return {
            showitemsperpageselector: false,
            itemsperpage: 35,
            previous: true,
            next: true,
            activepagenumber: 1,
            hidecontrolonsinglepage: true,
            pages: []
        };
    };

    /**
     * Calculate the number of pages required for the given number of items and
     * how many of each item should appear on a page.
     *
     * @param  {Number} numberOfItems How many items in total.
     * @param  {Number} itemsPerPage  How many items will be shown per page.
     * @return {Number} The number of pages required.
     */
    var calculateNumberOfPages = function(numberOfItems, itemsPerPage) {
        var numberOfPages = 1;

        if (numberOfItems > 0) {
            var partial = numberOfItems % itemsPerPage;

            if (partial) {
                numberOfItems -= partial;
                numberOfPages = (numberOfItems / itemsPerPage) + 1;
            } else {
                numberOfPages = numberOfItems / itemsPerPage;
            }
        }

        return numberOfPages;
    };

    /**
     * Build the context for the paging bar template when we have a known number
     * of items.
     *
     * @param {Number} numberOfItems How many items in total.
     * @param {Number} itemsPerPage  How many items will be shown per page.
     * @return {object} Mustache template
     */
    var buildPagingBarTemplateContextKnownLength = function(numberOfItems, itemsPerPage) {
        if (itemsPerPage === null) {
            itemsPerPage = DEFAULT.ITEMS_PER_PAGE_SINGLE;
        }

        if ($.isArray(itemsPerPage)) {
            // If we're given a total number of pages then we don't support a variable
            // set of items per page so just use the first one.
            itemsPerPage = itemsPerPage[0];
        }

        var context = getDefaultPagingBarTemplateContext();
        context.itemsperpage = itemsPerPage;
        var numberOfPages = calculateNumberOfPages(numberOfItems, itemsPerPage);

        for (var i = 1; i <= numberOfPages; i++) {
            var page = {
                number: i,
                page: "" + i,
            };

            // Make the first page active by default.
            if (i === 1) {
                page.active = true;
            }

            context.pages.push(page);
        }

        context.barsize = 10;
        return context;
    };

    /**
     * Convert the itemsPerPage value into a format applicable for the mustache template.
     * The given value can be either a single integer or an array of integers / objects.
     *
     * E.g.
     * In: [5, 10]
     * out: [{value: 5, active: true}, {value: 10, active: false}]
     *
     * In: [5, {value: 10, active: true}]
     * Out: [{value: 5, active: false}, {value: 10, active: true}]
     *
     * In: [{value: 5, active: false}, {value: 10, active: true}]
     * Out: [{value: 5, active: false}, {value: 10, active: true}]
     *
     * @param {int|int[]} itemsPerPage Options for number of items per page.
     * @return {int|array}
     */
    var buildItemsPerPagePagingBarContext = function(itemsPerPage) {
        if ($.isArray(itemsPerPage)) {
            // Convert the array into a format accepted by the template.
            var context = itemsPerPage.map(function(num) {
                if (typeof num === 'number') {
                    // If the item is just a plain number then convert it into
                    // an object with value and active keys.
                    return {
                        value: num,
                        active: false
                    };
                } else {
                    // Otherwise we assume the caller has specified things correctly.
                    return num;
                }
            });

            var activeItems = context.filter(function(item) {
                return item.active;
            });

            // Default the first item to active if one hasn't been specified.
            if (!activeItems.length) {
                context[0].active = true;
            }

            return context;
        } else {
            return itemsPerPage;
        }
    };

    /**
     * Build the context for the paging bar template when we have an unknown
     * number of items.
     *
     * @param {Number} itemsPerPage  How many items will be shown per page.
     * @return {object} Mustache template
     */
    var buildPagingBarTemplateContextUnknownLength = function(itemsPerPage) {
        if (itemsPerPage === null) {
            itemsPerPage = DEFAULT.ITEMS_PER_PAGE_ARRAY;
        }

        var context = getDefaultPagingBarTemplateContext();
        context.itemsperpage = buildItemsPerPagePagingBarContext(itemsPerPage);
        context.showitemsperpageselector = $.isArray(itemsPerPage);

        return context;
    };

    /**
     * Build the context to render the paging bar template with based on the number
     * of pages to show.
     *
     * @param  {int|null} numberOfItems How many items are there total.
     * @param  {int|null} itemsPerPage  How many items will be shown per page.
     * @return {object} The template context.
     */
    var buildPagingBarTemplateContext = function(numberOfItems, itemsPerPage) {
        if (numberOfItems) {
            return buildPagingBarTemplateContextKnownLength(numberOfItems, itemsPerPage);
        } else {
            return buildPagingBarTemplateContextUnknownLength(itemsPerPage);
        }
    };

    /**
     * Build the context to render the paging dropdown template based on the number
     * of pages to show and items per page.
     *
     * This control is rendered with a gradual increase of the items per page to
     * limit the number of pages in the dropdown. Each page will show twice as much
     * as the previous page (except for the first two pages).
     *
     * By default there will only be 4 pages shown (including the "All" option) unless
     * a different number of pages is defined using the maxPages config value.
     *
     * For example:
     * Items per page = 25
     * Would render a dropdown will 4 options:
     * 25
     * 50
     * 100
     * All
     *
     * @param  {Number} itemsPerPage  How many items will be shown per page.
     * @param  {object} config  Configuration options provided by the client.
     * @return {object} The template context.
     */
    var buildPagingDropdownTemplateContext = function(itemsPerPage, config) {
        if (itemsPerPage === null) {
            itemsPerPage = DEFAULT.ITEMS_PER_PAGE_SINGLE;
        }

        if ($.isArray(itemsPerPage)) {
            // If we're given an array for the items per page, rather than a number,
            // then just use that as the options for the dropdown.
            return {
                options: itemsPerPage
            };
        }

        var context = {
            options: []
        };

        var totalItems = 0;
        var lastIncrease = 0;
        var maxPages = DEFAULT.MAX_PAGES;

        if (config.hasOwnProperty('maxPages')) {
            maxPages = config.maxPages;
        }

        for (var i = 1; i <= maxPages; i++) {
            var itemCount = 0;

            if (i <= 2) {
                itemCount = itemsPerPage;
                lastIncrease = itemsPerPage;
            } else {
                lastIncrease = lastIncrease * 2;
                itemCount = lastIncrease;
            }

            totalItems += itemCount;
            var option = {
                itemcount: itemCount,
                content: totalItems
            };

            // Make the first option active by default.
            if (i === 1) {
                option.active = true;
            }

            context.options.push(option);
        }

        return context;
    };

    /**
     * Build the context to render the paged content template with based on the number
     * of pages to show, items per page, and configuration option.
     *
     * By default the code will render a paging bar for the paging controls unless
     * otherwise specified in the provided config.
     *
     * @param  {int|null} numberOfItems Total number of items.
     * @param  {int|null|array} itemsPerPage  How many items will be shown per page.
     * @param  {object} config  Configuration options provided by the client.
     * @return {object} The template context.
     */
    var buildTemplateContext = function(numberOfItems, itemsPerPage, config) {
        var context = getDefaultTemplateContext();

        if (config.hasOwnProperty('ignoreControlWhileLoading')) {
            context.ignorecontrolwhileloading = config.ignoreControlWhileLoading;
        }

        if (config.hasOwnProperty('controlPlacementBottom')) {
            context.controlplacementbottom = config.controlPlacementBottom;
        }

        if (config.hasOwnProperty('hideControlOnSinglePage')) {
            context.hidecontrolonsinglepage = config.hideControlOnSinglePage;
        }

        if (config.hasOwnProperty('ariaLabels')) {
            context.arialabels = config.ariaLabels;
        }

        if (config.hasOwnProperty('dropdown') && config.dropdown) {
            context.pagingdropdown = buildPagingDropdownTemplateContext(itemsPerPage, config);
        } else {
            context.pagingbar = buildPagingBarTemplateContext(numberOfItems, itemsPerPage);
        }

        return context;
    };

    /**
     * Create a paged content widget where the complete list of items is not loaded
     * up front but will instead be loaded by an ajax request (or similar).
     *
     * The client code must provide a callback function which loads and renders the
     * items for each page. See PagedContent.init for more details.
     *
     * The function will return a deferred that is resolved with a jQuery object
     * for the HTML content and a string for the JavaScript.
     *
     * The current list of configuration options available are:
     *      dropdown {bool} True to render the page control as a dropdown (paging bar is default).
     *      maxPages {Number} The maximum number of pages to show in the dropdown (only works with dropdown option)
     *      ignoreControlWhileLoading {bool} Disable the pagination controls while loading a page (default to true)
     *      controlPlacementBottom {bool} Render controls under paged content (default to false)
     *
     * @param  {function} renderPagesContentCallback  Callback for loading and rendering the items.
     * @param  {object} config  Configuration options provided by the client.
     * @return {promise} Resolved with jQuery HTML and string JS.
     */
    var create = function(renderPagesContentCallback, config) {
        return createWithTotalAndLimit(null, null, renderPagesContentCallback, config);
    };

    /**
     * Create a paged content widget where the complete list of items is not loaded
     * up front but will instead be loaded by an ajax request (or similar).
     *
     * The client code must provide a callback function which loads and renders the
     * items for each page. See PagedContent.init for more details.
     *
     * The function will return a deferred that is resolved with a jQuery object
     * for the HTML content and a string for the JavaScript.
     *
     * The current list of configuration options available are:
     *      dropdown {bool} True to render the page control as a dropdown (paging bar is default).
     *      maxPages {Number} The maximum number of pages to show in the dropdown (only works with dropdown option)
     *      ignoreControlWhileLoading {bool} Disable the pagination controls while loading a page (default to true)
     *      controlPlacementBottom {bool} Render controls under paged content (default to false)
     *
     * @param  {int|array|null} itemsPerPage  How many items will be shown per page.
     * @param  {function} renderPagesContentCallback  Callback for loading and rendering the items.
     * @param  {object} config  Configuration options provided by the client.
     * @return {promise} Resolved with jQuery HTML and string JS.
     */
    var createWithLimit = function(itemsPerPage, renderPagesContentCallback, config) {
        return createWithTotalAndLimit(null, itemsPerPage, renderPagesContentCallback, config);
    };

    /**
     * Create a paged content widget where the complete list of items is not loaded
     * up front but will instead be loaded by an ajax request (or similar).
     *
     * The client code must provide a callback function which loads and renders the
     * items for each page. See PagedContent.init for more details.
     *
     * The function will return a deferred that is resolved with a jQuery object
     * for the HTML content and a string for the JavaScript.
     *
     * The current list of configuration options available are:
     *      dropdown {bool} True to render the page control as a dropdown (paging bar is default).
     *      maxPages {Number} The maximum number of pages to show in the dropdown (only works with dropdown option)
     *      ignoreControlWhileLoading {bool} Disable the pagination controls while loading a page (default to true)
     *      controlPlacementBottom {bool} Render controls under paged content (default to false)
     *
     * @param  {int|null} numberOfItems How many items are there in total.
     * @param  {int|array|null} itemsPerPage  How many items will be shown per page.
     * @param  {function} renderPagesContentCallback  Callback for loading and rendering the items.
     * @param  {object} config  Configuration options provided by the client.
     * @return {promise} Resolved with jQuery HTML and string JS.
     */
    var createWithTotalAndLimit = function(numberOfItems, itemsPerPage, renderPagesContentCallback, config) {
        config = config || {};

        var deferred = $.Deferred();
        var templateContext = buildTemplateContext(numberOfItems, itemsPerPage, config);

        Templates.render(TEMPLATES.PAGED_CONTENT, templateContext)
            .then(function(html, js) {
                html = $(html);
                var id = html.attr('id');

                // Set the id to the custom namespace provided
                if (config.hasOwnProperty('eventNamespace')) {
                    id = config.eventNamespace;
                }

                var container = html;

                PagedContent.init(container, renderPagesContentCallback, id);

                registerEvents(id, config);

                deferred.resolve(html, js);
                return;
            })
            .fail(function(exception) {
                deferred.reject(exception);
            })
            .fail(Notification.exception);

        return deferred.promise();
    };

    /**
     * Create a paged content widget where the complete list of items is loaded
     * up front.
     *
     * The client code must provide a callback function which renders the
     * items for each page. The callback will be provided with an array where each
     * value in the array is a the list of items to render for the page.
     *
     * The function will return a deferred that is resolved with a jQuery object
     * for the HTML content and a string for the JavaScript.
     *
     * The current list of configuration options available are:
     *      dropdown {bool} True to render the page control as a dropdown (paging bar is default).
     *      maxPages {Number} The maximum number of pages to show in the dropdown (only works with dropdown option)
     *      ignoreControlWhileLoading {bool} Disable the pagination controls while loading a page (default to true)
     *      controlPlacementBottom {bool} Render controls under paged content (default to false)
     *
     * @param  {array} contentItems The list of items to paginate.
     * @param  {Number} itemsPerPage  How many items will be shown per page.
     * @param  {function} renderContentCallback  Callback for rendering the items for the page.
     * @param  {object} config  Configuration options provided by the client.
     * @return {promise} Resolved with jQuery HTML and string JS.
     */
    var createFromStaticList = function(contentItems, itemsPerPage, renderContentCallback, config) {
        if (typeof config == 'undefined') {
            config = {};
        }

        var numberOfItems = contentItems.length;
        return createWithTotalAndLimit(numberOfItems, itemsPerPage, function(pagesData) {
            var contentToRender = [];
            pagesData.forEach(function(pageData) {
                var begin = pageData.offset;
                var end = pageData.limit ? begin + pageData.limit : numberOfItems;
                var items = contentItems.slice(begin, end);
                contentToRender.push(items);
            });

            return renderContentCallback(contentToRender);
        }, config);
    };

    /**
     * Reset the last page number for the generated paged-content
     * This is used when we need a way to update the last page number outside of the getters callback
     *
     * @param {String} id ID of the paged content container
     * @param {Int} lastPageNumber The last page number
     */
    var resetLastPageNumber = function(id, lastPageNumber) {
        PubSub.publish(id + PagedContentEvents.ALL_ITEMS_LOADED, lastPageNumber);
    };

    /**
     * Generate the callback handler for the page limit persistence functionality
     *
     * @param {String} persistentLimitKey
     * @return {callback}
     */
    var generateLimitHandler = function(persistentLimitKey) {
        var callback = function(limit) {
            var args = {
                preferences: [
                    {
                        type: persistentLimitKey,
                        value: limit
                    }
                ]
            };

            var request = {
                methodname: 'core_user_update_user_preferences',
                args: args
            };

            Ajax.call([request]);
        };

        return callback;
    };

    /**
     * Set up any events based on config key values
     *
     * @param {string} namespace The namespace for this component
     * @param {object} config Config options passed to the factory
     */
    var registerEvents = function(namespace, config) {
        if (config.hasOwnProperty('persistentLimitKey')) {
            PubSub.subscribe(namespace + PagedContentEvents.SET_ITEMS_PER_PAGE_LIMIT,
                generateLimitHandler(config.persistentLimitKey));
        }
    };

    return {
        create: create,
        createWithLimit: createWithLimit,
        createWithTotalAndLimit: createWithTotalAndLimit,
        createFromStaticList: createFromStaticList,
        // Backwards compatibility just in case anyone was using this.
        createFromAjax: createWithTotalAndLimit,
        resetLastPageNumber: resetLastPageNumber
    };
});
