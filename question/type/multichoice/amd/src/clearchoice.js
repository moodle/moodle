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
 * Manages 'Clear my choice' functionality actions.
 *
 * @module     qtype_multichoice/clearchoice
 * @copyright  2019 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.7
 */
define(['jquery', 'core/custom_interaction_events'], function($, CustomEvents) {

    var SELECTORS = {
        ANSWER_RADIOS: '.answer input',
        CLEARRESULTS_LINK: 'a[data-action="clearresults"]'
    };

    var CSSHIDDEN = 'd-none';

    /**
     * Register event listeners for the clear choice module.
     *
     * @param {Object} root The question outer div prefix.
     */
    var registerEventListeners = function(root) {

        var clearChoiceButton = root.find(SELECTORS.CLEARRESULTS_LINK);

        root.on(CustomEvents.events.activate, SELECTORS.CLEARRESULTS_LINK, function(e, data) {
            root.find(SELECTORS.ANSWER_RADIOS).each(function() {
                $(this).prop('checked', false);
            });
            $(e.target).addClass(CSSHIDDEN);
            data.originalEvent.preventDefault();
        });

        root.on(CustomEvents.events.activate, SELECTORS.ANSWER_RADIOS, function() {
            clearChoiceButton.removeClass(CSSHIDDEN);
        });
    };

    /**
     * Initialise clear choice module.

     * @param {string} root The question outer div prefix.
     */
    var init = function(root) {
        root = $('#' + root);
        registerEventListeners(root);
    };

    return {
        init: init
    };
});
