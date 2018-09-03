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
    'core/paged_content_pages'
],
function(
    $,
    Templates,
    Notification,
    PagedContent
) {
    var TEMPLATES = {
        PAGED_CONTENT: 'core/paged_content'
    };

    /**
     * Build the context to render the paging bar template with based on the number
     * of pages to show.
     *
     * @param  {int} numberOfPages How many pages to have in the paging bar.
     * @param  {int} itemsPerPage  How many items will be shown per page.
     * @return {object} The template context.
     */
    var buildPagingBarTemplateContext = function(numberOfPages, itemsPerPage) {
        var context = {
            "itemsperpage": itemsPerPage,
            "previous": {},
            "next": {},
            "pages": []
        };

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

        return context;
    };

    /**
     * Build the context to render the paging dropdown template with based on the number
     * of pages to show and items per page.
     *
     * This control is rendered with a gradual increase of the items per page to
     * limit the number of pages in the dropdown. Each page will show twice as much
     * as the previous page (except for the first two pages).
     *
     * For example:
     * Number of pages = 3
     * Items per page = 25
     * Would render a dropdown will 4 options:
     * 25
     * 50
     * 100
     * All
     *
     * @param  {int} numberOfPages How many options to have in the dropdown.
     * @param  {int} itemsPerPage  How many items will be shown per page.
     * @param  {object} config  Configuration options provided by the client.
     * @return {object} The template context.
     */
    var buildPagingDropdownTemplateContext = function(numberOfPages, itemsPerPage, config) {
        var context = {
            options: []
        };

        var totalItems = 0;
        var lastIncrease = 0;
        var maxPages = numberOfPages;

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
     * @param  {int} numberOfPages How many pages to have.
     * @param  {int} itemsPerPage  How many items will be shown per page.
     * @param  {object} config  Configuration options provided by the client.
     * @return {object} The template context.
     */
    var buildTemplateContext = function(numberOfPages, itemsPerPage, config) {
        var context = {
            pagingbar: false,
            pagingdropdown: false,
            skipjs: true
        };

        if (config.hasOwnProperty('dropdown') && config.dropdown) {
            context.pagingdropdown = buildPagingDropdownTemplateContext(numberOfPages, itemsPerPage, config);
        } else {
            context.pagingbar = buildPagingBarTemplateContext(numberOfPages, itemsPerPage);
        }

        return context;
    };

    /**
     * Calculate the number of pages required for the given number of items and
     * how many of each item should appear on a page.
     *
     * @param  {int} numberOfItems How many items in total.
     * @param  {int} itemsPerPage  How many items will be shown per page.
     * @return {int} The number of pages required.
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
     *
     * @param  {int} numberOfItems How many items are there in total.
     * @param  {int} itemsPerPage  How many items will be shown per page.
     * @param  {function} renderPagesContentCallback  Callback for loading and rendering the items.
     * @param  {object} config  Configuration options provided by the client.
     * @return {promise} Resolved with jQuery HTML and string JS.
     */
    var createFromAjax = function(numberOfItems, itemsPerPage, renderPagesContentCallback, config) {
        if (typeof config == 'undefined') {
            config = {};
        }

        var deferred = $.Deferred();
        var numberOfPages = calculateNumberOfPages(numberOfItems, itemsPerPage);
        var templateContext = buildTemplateContext(numberOfPages, itemsPerPage, config);

        Templates.render(TEMPLATES.PAGED_CONTENT, templateContext)
            .then(function(html, js) {
                html = $(html);

                var container = html;
                var pagedContent = html.find(PagedContent.rootSelector);

                PagedContent.init(pagedContent, container, renderPagesContentCallback);

                deferred.resolve(html, js);
                return;
            })
            .fail(function(exception) {
                deferred.reject(exception);
            })
            .fail(Notification.exception);

        return deferred;
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
     *
     * @param  {array} contentItems The list of items to paginate.
     * @param  {int} itemsPerPage  How many items will be shown per page.
     * @param  {function} renderContentCallback  Callback for rendering the items for the page.
     * @param  {object} config  Configuration options provided by the client.
     * @return {promise} Resolved with jQuery HTML and string JS.
     */
    var createFromStaticList = function(contentItems, itemsPerPage, renderContentCallback, config) {
        if (typeof config == 'undefined') {
            config = {};
        }

        var numberOfItems = contentItems.length;
        return createFromAjax(numberOfItems, itemsPerPage, function(pagesData) {
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

    return {
        createFromAjax: createFromAjax,
        createFromStaticList: createFromStaticList
    };
});
