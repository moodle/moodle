
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
 * Javascript to initialise the Recently accessed items block.
 *
 * @module     block_recentlyaccesseditems/main
 * @copyright  2018 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(
    [
        'jquery',
        'block_recentlyaccesseditems/repository',
        'core/templates',
        'core/notification'
    ],
    function(
        $,
        Repository,
        Templates,
        Notification
    ) {

        var NUM_ITEMS = 9;
        // Maximum number of elements to display in the block initially.
        var NUM_ITEMS_INIT = 3;

        var SELECTORS = {
            CARDDECK_CONTAINER: '[data-region="recentlyaccesseditems-view"]',
            CARDDECK: '[data-region="recentlyaccesseditems-view-content"]',
            SHOWMORE_LINK: '[data-region="recentlyaccesseditems-view"] [data-action="more-items"]',
        };

        /**
         * Register event listeners.
         */
        const registerEventListeners = () => {
            const showmoreLink = document.querySelector(SELECTORS.SHOWMORE_LINK);

            // Hide "Show more" link and show additional items.
            showmoreLink.addEventListener('click', () => {
                showmoreLink.classList.add('d-none');

                const hiddenItems = document.querySelector('[data-region="items-list"]').children;
                hiddenItems.forEach(function(hiddenItem) {
                    hiddenItem.style = "display: block";
                });
            });
        };

        /**
         * Get recent items from backend.
         *
         * @method getRecentItems
         * @param {int} limit Only return this many results
         * @return {array} Items user most recently has accessed
         */
        var getRecentItems = function(limit) {
            return Repository.getRecentItems(limit);
        };

        /**
         * Render the block content.
         *
         * @method renderItems
         * @param {object} root The root element for the items view.
         * @param {array} items containing array of returned items.
         * @return {promise} Resolved with HTML and JS strings
         */
        var renderItems = function(root, items) {
            if (items.length > 0) {
                let hasmoreitems = false;
                if (items.length > NUM_ITEMS_INIT) {
                    hasmoreitems = true;
                }
                return Templates.render('block_recentlyaccesseditems/view-cards', {
                    items: items,
                    hasmoreitems: hasmoreitems
                });
            } else {
                var noitemsimgurl = root.attr('data-noitemsimgurl');
                return Templates.render('block_recentlyaccesseditems/no-items', {
                    noitemsimgurl: noitemsimgurl
                });
            }
        };

        /**
         * Get and show the recent items into the block.
         *
         * @param {object} root The root element for the items block.
         */
        var init = function(root) {
            root = $(root);

            var itemsContainer = root.find(SELECTORS.CARDDECK_CONTAINER);
            var itemsContent = root.find(SELECTORS.CARDDECK);

            var itemsPromise = getRecentItems(NUM_ITEMS);

            itemsPromise.then(function(items) {
                var pageContentPromise = renderItems(itemsContainer, items);

                pageContentPromise.then(function(html, js) {
                    Templates.replaceNodeContents(itemsContent, html, js);
                    if (items.length > 3) {
                        registerEventListeners();
                    }
                    return null;
                }).catch(Notification.exception);
            }).catch(Notification.exception);
        };

        return {
            init: init
        };
    });