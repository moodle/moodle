/**
 * This file is part of Moodle - http://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package   filter_oembed
 * @copyright Guy Thomas / moodlerooms.com 2016
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Oembed main module.
 */
define(['jquery', 'filter_oembed/preloader', 'filter_oembed/responsivecontent'],
    function($, preloader, responsiveContent) {
        return {
            init: function() {
                /**
                 * Apply a mutation observer to track oembed-content being dynamically added to the page.
                 */
                var responsiveContentOnInsert = function() {
                    /**
                     * Does a node have the oembed-content class
                     * @param {opbject} node (dom element)
                     * @returns {boolean}
                     */
                    var hasOembedClass = function(node) {
                        if (!node.className) {
                            return false;
                        }
                        return $(node).is(".oembed-content, .oembed-card-container");
                    };

                    var observer = new MutationObserver(function(mutations) {
                        mutations.forEach(function(mutation) {
                            for (var n in mutation.addedNodes) {
                                var node = mutation.addedNodes[n];
                                if (hasOembedClass(node)) {
                                    // Only apply responsive content to the newly added node for efficiency.
                                    responsiveContent.apply($(node).find('> *:not(video):first-child, .oembed-card'));
                                }
                            }
                        });
                    });

                    var observerConfig = {
                        attributes: true,
                        childList: true,
                        characterData: true,
                        subtree: true
                    };

                    // Note: Currently observing mutations throughout the document body - We might want to limit scope for
                    // observation at some point in the future.
                    var targetNode = document.body;
                    observer.observe(targetNode, observerConfig);
                };

                responsiveContentOnInsert();

                $(document).ready(function() {
                    // Apply preloader listeners.
                    preloader.apply();

                    // Call responsive content on dom ready, to catch things that existed prior to mutation observation.
                    responsiveContent.apply();
                });
            }
        };
    }
);
