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
 * Manage the timeline view navigation for the overview block.
 *
 * @package    block_myoverview
 * @copyright  2018 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(
[
    'jquery',
    'core/custom_interaction_events',
    'block_myoverview/view'
],
function(
    $,
    CustomEvents,
    View
) {

    var SELECTORS = {
        FILTERS: '[data-region="filter"]',
        FILTER_OPTION: '[data-filter]',
        DISPLAY_OPTION: '[data-display-option]'
    };

    /**
     * Event listener for the Display filter (cards, list).
     * 
     * @param {object} root The root element for the overview block
     * @param {object} viewRoot The root element for displaying courses.
     * @param {object} viewContent content The content element for the courses view.
     */
    var registerSelector = function(root, viewRoot, viewContent) {

        var Selector = root.find(SELECTORS.FILTERS);

        CustomEvents.define(Selector, [CustomEvents.events.activate]);
        Selector.on(
            CustomEvents.events.activate,
            SELECTORS.FILTER_OPTION,
            function(e, data) {
                var option = $(e.target);

                if (option.hasClass('active')) {
                    // If it's already active then we don't need to do anything.
                    return;
                }

                var attributename = 'data-' + option.attr('data-filter');
                viewRoot.attr(attributename, option.attr('data-value'));

                // Reset the views.
                View.init(viewRoot, viewContent);

                data.originalEvent.preventDefault();
            }
        );

        CustomEvents.define(Selector, [CustomEvents.events.activate]);
        Selector.on(
            CustomEvents.events.activate,
            SELECTORS.DISPLAY_OPTION,
            function(e, data) {
                var option = $(e.target);

                if (option.hasClass('active')) {
                    return;
                }

                viewRoot.attr('data-display', option.attr('data-value'));
                View.reset(viewRoot, viewContent);
                data.originalEvent.preventDefault();
            }
        );
    };

    /**
     * Initialise the timeline view navigation by adding event listeners to
     * the navigation elements.
     * 
     * @param {object} root The root element for the myoverview block
     * @param {object} viewRoot The root element for the myoverview block
     * @param {object} viewContent The content element for the myoverview block
     */
    var init = function(root, viewRoot, viewContent) {
        root = $(root);
        registerSelector(root, viewRoot, viewContent);
    };

    return {
        init: init
    };
});
