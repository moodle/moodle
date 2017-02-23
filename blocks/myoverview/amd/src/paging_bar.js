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
 * Javascript to load and render the list of calendar events for a
 * given day range.
 *
 * @module     block_myoverview/event_list
 * @package    block_myoverview
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/custom_interaction_events'],
        function($, CustomEvents) {

    var SELECTORS = {
        PAGE_LINK: '[data-region="page-link"]',
        ACTIVE_PAGE_LINK: '.active > [data-region="page-link"]'
    };

    var EVENTS = {
        PAGE_SELECTED: 'block_myoverview-paging-bar-page-selected',
    };

    var registerEventListeners = function(root) {
        root = $(root);
        CustomEvents.define(root, [
            CustomEvents.events.activate
        ]);

        root.one(CustomEvents.events.activate, SELECTORS.PAGE_LINK, function(e, data) {
            var page = $(e.target).closest(SELECTORS.PAGE_LINK);
            var activePage = root.find(SELECTORS.ACTIVE_PAGE_LINK);
            var isSamePage = page.is(activePage);

            if (!isSamePage) {
                root.find(SELECTORS.PAGE_LINK).removeClass('active');
                page.addClass('active');
            }

            root.trigger(EVENTS.PAGE_SELECTED, [{
                pageNumber: page.attr('data-page-number'),
                isSamePage: isSamePage,
            }]);

            data.originalEvent.preventDefault();
        });
    };

    return {
        registerEventListeners: registerEventListeners,
        events: EVENTS,
    };
});
