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
 * Javascript for showing/hiding pages of content.
 *
 * @module     core/paged_content_pages
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
    [
        'jquery',
        'core/templates',
        'core/notification',
        'core/pubsub',
        'core/paged_content_events'
    ],
    function(
        $,
        Templates,
        Notification,
        PubSub,
        PagedContentEvents
    ) {

    var SELECTORS = {
        ROOT: '[data-region="page-container"]',
        PAGE_REGION: '[data-region="paged-content-page"]',
        ACTIVE_PAGE_REGION: '[data-region="paged-content-page"].active'
    };

    var TEMPLATES = {
        PAGING_CONTENT_ITEM: 'core/paged_content_page',
        LOADING: 'core/overlay_loading'
    };

    var PRELOADING_GRACE_PERIOD = 300;

    /**
     * Find a page by the number.
     *
     * @param {object} root The root element.
     * @param {Number} pageNumber The number of the page to be found.
     * @returns {jQuery} The page.
     */
    var findPage = function(root, pageNumber) {
        return root.find('[data-page="' + pageNumber + '"]');
    };

    /**
     * Show the loading spinner until the returned deferred is resolved by the
     * calling code.
     *
     * The loading spinner is only rendered after a short grace period to avoid
     * having it flash up briefly in the interface.
     *
     * @param {object} root The root element.
     * @returns {promise} The page.
     */
    var startLoading = function(root) {
        var deferred = $.Deferred();
        root.attr('aria-busy', true);

        Templates.render(TEMPLATES.LOADING, {visible: true})
            .then(function(html) {
                var loadingSpinner = $(html);
                // Put this in a timer to give the calling code 300 milliseconds
                // to render the content before we show the loading spinner. This
                // helps prevent a loading icon flicker on close to instant
                // rendering.
                var timerId = setTimeout(function() {
                    root.css('position', 'relative');
                    loadingSpinner.appendTo(root);
                }, PRELOADING_GRACE_PERIOD);

                deferred.always(function() {
                    clearTimeout(timerId);
                    // Remove the loading spinner when our deferred is resolved
                    // by the calling code.
                    loadingSpinner.remove();
                    root.css('position', '');
                    root.removeAttr('aria-busy');
                    return;
                });

                return;
            })
            .fail(Notification.exception);

        return deferred;
    };

    /**
     * Render the result of the page promise in a paged content page.
     *
     * This function returns a promise that is resolved with the new paged content
     * page.
     *
     * @param {object} root The root element.
     * @param {promise} pagePromise The promise resolved with HTML and JS to render in the page.
     * @param {Number} pageNumber The page number.
     * @returns {promise} The page.
     */
    var renderPagePromise = function(root, pagePromise, pageNumber) {
        var deferred = $.Deferred();
        pagePromise.then(function(html, pageJS) {
            pageJS = pageJS || '';
            // When we get the contents to be rendered we can pass it in as the
            // content for a new page.
            Templates.render(TEMPLATES.PAGING_CONTENT_ITEM, {
                page: pageNumber,
                content: html
            })
            .then(function(html) {
                // Make sure the JS we got from the page promise is being added
                // to the page when we render the page.
                Templates.appendNodeContents(root, html, pageJS);
                var page = findPage(root, pageNumber);
                deferred.resolve(page);
                return;
            })
            .fail(function(exception) {
                deferred.reject(exception);
            })
            .fail(Notification.exception);

            return;
        })
        .fail(function(exception) {
            deferred.reject(exception);
            return;
        })
        .fail(Notification.exception);

        return deferred.promise();
    };

    /**
     * Make one or more pages visible based on the SHOW_PAGES event. The show
     * pages event provides data containing which pages should be shown as well
     * as the limit and offset values for loading the items for each of those pages.
     *
     * The renderPagesContentCallback is provided this list of data to know which
     * pages to load. E.g. the data to load 2 pages might look like:
     * [
     *      {
     *          pageNumber: 1,
     *          limit: 5,
     *          offset: 0
     *      },
     *      {
     *          pageNumber: 2,
     *          limit: 5,
     *          offset: 5
     *      }
     * ]
     *
     * The renderPagesContentCallback should return an array of promises, one for
     * each page in the pages data, that is resolved with the HTML and JS for that page.
     *
     * If the renderPagesContentCallback is not provided then it is assumed that
     * all pages have been rendered prior to initialising this module.
     *
     * This function triggers the PAGES_SHOWN event after the pages have been rendered.
     *
     * @param {object} root The root element.
     * @param {Number} pagesData The data for which pages need to be visible.
     * @param {string} id A unique id for this instance.
     * @param {function} renderPagesContentCallback Render pages content.
     */
    var showPages = function(root, pagesData, id, renderPagesContentCallback) {
        var existingPages = [];
        var newPageData = [];
        var newPagesPromise = $.Deferred();
        var shownewpage = true;
        // Check which of the pages being requests have previously been rendered
        // so that we only ask for new pages to be rendered by the callback.
        pagesData.forEach(function(pageData) {
            var pageNumber = pageData.pageNumber;
            var existingPage = findPage(root, pageNumber);
            if (existingPage.length) {
                existingPages.push(existingPage);
            } else {
                newPageData.push(pageData);
            }
        });

        if (newPageData.length && typeof renderPagesContentCallback === 'function') {
            // If we have pages we haven't previously seen then ask the client code
            // to render them for us by calling the callback.
            var promises = renderPagesContentCallback(newPageData, {
                allItemsLoaded: function(lastPageNumber) {
                    PubSub.publish(id + PagedContentEvents.ALL_ITEMS_LOADED, lastPageNumber);
                }
            });
            // After the client has finished rendering each of the pages being asked
            // for then begin our rendering process to put that content into paged
            // content pages.
            var renderPagePromises = promises.map(function(promise, index) {
                // Create our promise for when our rendering will be completed.
                return renderPagePromise(root, promise, newPageData[index].pageNumber);
            });
            // After each of our rendering promises have been completed then we can
            // give all of the new pages to the next bit of code for handling.
            $.when.apply($, renderPagePromises)
                .then(function() {
                    var newPages = Array.prototype.slice.call(arguments);
                    // Resolve the promise with the list of newly rendered pages.
                    newPagesPromise.resolve(newPages);
                    return;
                })
                .fail(function(exception) {
                    newPagesPromise.reject(exception);
                    return;
                })
                .fail(Notification.exception);
        } else {
            // If there aren't any pages to load then immediately resolve the promise.
            newPagesPromise.resolve([]);
        }

        var loadingPromise = startLoading(root);
        newPagesPromise.then(function(newPages) {
            // Once all of the new pages have been created then add them to any
            // existing pages we have.
            var pagesToShow = existingPages.concat(newPages);
            // Hide all existing pages.
            root.find(SELECTORS.PAGE_REGION).addClass('hidden');
            // Show each of the pages that were requested.;
            pagesToShow.forEach(function(page) {
                if (shownewpage) {
                    page.removeClass('hidden');
                }
            });

            return;
        })
        .then(function() {
            // Let everything else know we've displayed the pages.
            PubSub.publish(id + PagedContentEvents.PAGES_SHOWN, pagesData);
            return;
        })
        .fail(Notification.exception)
        .always(function() {
            loadingPromise.resolve();
        });
    };

    /**
     * Initialise the module to listen for SHOW_PAGES events and render the
     * appropriate pages using the provided renderPagesContentCallback function.
     *
     * The renderPagesContentCallback is provided a list of data to know which
     * pages to load.
     * E.g. the data to load 2 pages might look like:
     * [
     *      {
     *          pageNumber: 1,
     *          limit: 5,
     *          offset: 0
     *      },
     *      {
     *          pageNumber: 2,
     *          limit: 5,
     *          offset: 5
     *      }
     * ]
     *
     * The renderPagesContentCallback should return an array of promises, one for
     * each page in the pages data, that is resolved with the HTML and JS for that page.
     *
     * If the renderPagesContentCallback is not provided then it is assumed that
     * all pages have been rendered prior to initialising this module.
     *
     * The event element is the element to listen for the paged content events on.
     *
     * @param {object} root The root element.
     * @param {string} id A unique id for this instance.
     * @param {function} renderPagesContentCallback Render pages content.
     */
    var init = function(root, id, renderPagesContentCallback) {
        root = $(root);

        PubSub.subscribe(id + PagedContentEvents.SHOW_PAGES, function(pagesData) {
            showPages(root, pagesData, id, renderPagesContentCallback);
        });

        PubSub.subscribe(id + PagedContentEvents.SET_ITEMS_PER_PAGE_LIMIT, function() {
            // If the items per page limit was changed then we need to clear our content
            // the load new values based on the new limit.
            root.empty();
        });
    };

    return {
        init: init,
        rootSelector: SELECTORS.ROOT,
    };
});
