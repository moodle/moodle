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
 * Lazy loaded list of items.
 *
 * @module     core_message/message_drawer_lazy_load_list
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
[
    'jquery',
    'core/custom_interaction_events'
],
function(
    $,
    CustomEvents
) {

    var SELECTORS = {
        ROOT: '[data-region="lazy-load-list"]',
        LOADING_ICON_CONTAINER: '[data-region="loading-icon-container"]',
        CONTENT_CONTAINER: '[data-region="content-container"]',
        EMPTY_MESSAGE: '[data-region="empty-message-container"]',
        PLACEHOLDER: '[data-region="placeholder-container"]'
    };

    /**
     * Flag element as loading.
     *
     * @param {Object} root The section container element.
     */
    var startLoading = function(root) {
        root.attr('data-loading', true);
    };

    /**
     * Flag element as not loading.
     *
     * @param {Object} root The section container element.
     */
    var stopLoading = function(root) {
        root.attr('data-loading', false);
    };

    /**
     * Check if the element is loading.
     *
     * @param {Object} root The section container element.
     * @return {Bool}
     */
    var isLoading = function(root) {
        return root.attr('data-loading') === 'true';
    };

    /**
     * Get user id
     *
     * @param  {Object} root The section container element.
     * @return {Number} Logged in user id.
     */
    var getUserId = function(root) {
        return root.attr('data-user-id');
    };

    /**
     * Get the section content container element.
     *
     * @param  {Object} root The section container element.
     * @return {Object} The section content container element.
     */
    var getContentContainer = function(root) {
        return root.find(SELECTORS.CONTENT_CONTAINER);
    };

    /**
     * Get the root element.
     *
     * @param  {Object} containerElement The container element to search in.
     * @return {Object} The list root element.
     */
    var getRoot = function(containerElement) {
        return containerElement.find(SELECTORS.ROOT);
    };

    /**
     * Show the loading icon.
     *
     * @param {Object} root The section container element.
     */
    var showLoadingIcon = function(root) {
        root.find(SELECTORS.LOADING_ICON_CONTAINER).removeClass('hidden');
    };

    /**
     * Hide the loading icon.
     *
     * @param {Object} root The section container element.
     */
    var hideLoadingIcon = function(root) {
        root.find(SELECTORS.LOADING_ICON_CONTAINER).addClass('hidden');
    };

    /**
     * Show the empty message.
     *
     * @param {Object} root The section container element.
     */
    var showEmptyMessage = function(root) {
        root.find(SELECTORS.EMPTY_MESSAGE).removeClass('hidden');
    };

    /**
     * Hide the empty message.
     *
     * @param {Object} root The section container element.
     */
    var hideEmptyMessage = function(root) {
        root.find(SELECTORS.EMPTY_MESSAGE).addClass('hidden');
    };

    /**
     * Show the placeholder element.
     *
     * @param {Object} root The section container element.
     */
    var showPlaceholder = function(root) {
        root.find(SELECTORS.PLACEHOLDER).removeClass('hidden');
    };

    /**
     * Hide the placeholder element.
     *
     * @param {Object} root The section container element.
     */
    var hidePlaceholder = function(root) {
        root.find(SELECTORS.PLACEHOLDER).addClass('hidden');
    };

    /**
     * Show the section content container.
     *
     * @param {Object} root The section container element.
     */
    var showContent = function(root) {
        getContentContainer(root).removeClass('hidden');
    };

    /**
     * Hide the section content container.
     *
     * @param {Object} root The section container element.
     */
    var hideContent = function(root) {
        getContentContainer(root).addClass('hidden');
    };

    /**
     * If the section has loaded all content.
     *
     * @param {Object} root The section container element.
     * @return {Bool}
     */
    var hasLoadedAll = function(root) {
        return root.attr('data-loaded-all') == 'true';
    };

    /**
     * If the section has loaded all content.
     *
     * @param {Object} root The section container element.
     * @param {Bool} value If all items have been loaded.
     */
    var setLoadedAll = function(root, value) {
        root.attr('data-loaded-all', value);
    };

    /**
     * If the section can load more items.
     *
     * @param {Object} root The section container element.
     * @return {Bool}
     */
    var canLoadMore = function(root) {
        return !hasLoadedAll(root) && !isLoading(root);
    };

    /**
     * Load all items in this container from callback and render them.
     *
     * @param {Object} root The section container element.
     * @param {Function} loadCallback The callback to load items.
     * @param {Function} renderCallback The callback to render the results.
     * @return {Object} jQuery promise
     */
    var loadAndRender = function(root, loadCallback, renderCallback) {
        var userId = getUserId(root);
        startLoading(root);

        return loadCallback(root, userId)
            .then(function(items) {
                if (items.length > 0) {
                    var contentContainer = getContentContainer(root);
                    return renderCallback(contentContainer, items, userId)
                        .then(function() {
                            return items;
                        });
                } else {
                    return items;
                }
            })
            .then(function(items) {
                stopLoading(root);
                root.attr('data-seen', true);

                if (!items.length) {
                    setLoadedAll(root, true);
                }

                return items;
            })
            .catch(function() {
                stopLoading(root);
                root.attr('data-seen', true);
                return;
            });
    };

    /**
     * First load of this section.
     *
     * @param {Object} root The section container element.
     * @param {Function} loadCallback The callback to load items.
     * @param {Function} renderCallback The callback to render the results.
     * @return {Object} promise
     */
    var initialLoadAndRender = function(root, loadCallback, renderCallback) {
        getContentContainer(root).empty();
        showPlaceholder(root);
        hideContent(root);
        return loadAndRender(root, loadCallback, renderCallback)
            .then(function(items) {
                hidePlaceholder(root);

                if (!items.length) {
                    showEmptyMessage(root);
                } else {
                    showContent(root);
                }

                return;
            })
            .catch(function() {
                hidePlaceholder(root);
                showContent(root);
                return;
            });
    };

    /**
     * Listen to, and handle events in this section.
     *
     * @param {Object} root The section container element.
     * @param {Function} loadCallback The callback to load items.
     * @param {Function} renderCallback The callback to render the results.
     */
    var registerEventListeners = function(root, loadCallback, renderCallback) {
        CustomEvents.define(root, [
            CustomEvents.events.scrollBottom
        ]);

        root.on(CustomEvents.events.scrollBottom, function() {
            if (canLoadMore(root)) {
                showLoadingIcon(root);
                loadAndRender(root, loadCallback, renderCallback)
                    .then(function() {
                        return hideLoadingIcon(root);
                    })
                    .catch(function() {
                        return hideLoadingIcon(root);
                    });
            }
        });
    };

    /**
     * Setup the section.
     *
     * @param {Object} root The section container element.
     * @param {Function} loadCallback The callback to load items.
     * @param {Function} renderCallback The callback to render the results.
     */
    var show = function(root, loadCallback, renderCallback) {
        root = $(root);

        if (!root.attr('data-init')) {
            registerEventListeners(root, loadCallback, renderCallback);
            initialLoadAndRender(root, loadCallback, renderCallback);
            root.attr('data-init', true);
        }
    };

    return {
        show: show,
        getContentContainer: getContentContainer,
        getRoot: getRoot,
        setLoadedAll: setLoadedAll,
        showEmptyMessage: showEmptyMessage,
        hideEmptyMessage: hideEmptyMessage,
        showContent: showContent,
        hideContent: hideContent
    };
});
