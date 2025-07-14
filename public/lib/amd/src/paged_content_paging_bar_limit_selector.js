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
 * Javascript for dynamically changing the page limits.
 *
 * @module     core/paged_content_paging_bar_limit_selector
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
[
    'jquery',
    'core/custom_interaction_events',
    'core/paged_content_events',
    'core/pubsub'
],
function(
    $,
    CustomEvents,
    PagedContentEvents,
    PubSub
) {

    var SELECTORS = {
        ROOT: '[data-region="paging-control-limit-container"]',
        LIMIT_OPTION: '[data-limit]',
        LIMIT_TOGGLE: '[data-action="limit-toggle"]',
    };

    /**
     * Trigger the SET_ITEMS_PER_PAGE_LIMIT event when the page limit option
     * is modified.
     *
     * @param {object} root The root element.
     * @param {string} id A unique id for this instance.
     */
    var init = function(root, id) {
        root = $(root);

        CustomEvents.define(root, [
            CustomEvents.events.activate
        ]);

        root.on(CustomEvents.events.activate, SELECTORS.LIMIT_OPTION, function(e, data) {
            var optionElement = $(e.target).closest(SELECTORS.LIMIT_OPTION);

            if (optionElement.hasClass('active')) {
                // Don't do anything if it was the active option selected.
                return;
            }

            var limit = parseInt(optionElement.attr('data-limit'), 10);
            // Tell the rest of the pagination components that the limit has changed.
            PubSub.publish(id + PagedContentEvents.SET_ITEMS_PER_PAGE_LIMIT, limit);

            data.originalEvent.preventDefault();
        });
    };

    return {
        init: init,
        rootSelector: SELECTORS.ROOT
    };
});
