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
        CHOICE_ELEMENT: '.answer input',
        LINK: 'a',
        RADIO: 'input[type="radio"]'
    };

    /**
     * Mark clear choice radio as enabled and checked.
     *
     * @param {Object} clearChoiceContainer The clear choice option container.
     */
    var checkClearChoiceRadio = function(clearChoiceContainer) {
        clearChoiceContainer.find(SELECTORS.RADIO).prop('disabled', false).prop('checked', true);
    };

    /**
     * Get the clear choice div container.
     *
     * @param {Object} root The question root element.
     * @param {string} fieldPrefix The question outer div prefix.
     * @returns {Object} The clear choice div container.
     */
    var getClearChoiceElement = function(root, fieldPrefix) {
        return root.find('div[id="' + fieldPrefix + '"]');
    };

    /**
     * Hide clear choice option.
     *
     * @param {Object} clearChoiceContainer The clear choice option container.
     */
    var hideClearChoiceOption = function(clearChoiceContainer) {
        // We are using .sr-only and aria-hidden together so while the element is hidden
        // from both the monitor and the screen-reader, it is still tabbable.
        clearChoiceContainer.addClass('sr-only');
        clearChoiceContainer.attr('aria-hidden', true);
        clearChoiceContainer.find(SELECTORS.LINK).attr('tabindex', -1);
    };

    /**
     * Shows clear choice option.
     *
     * @param {Object} clearChoiceContainer The clear choice option container.
     */
    var showClearChoiceOption = function(clearChoiceContainer) {
        clearChoiceContainer.removeClass('sr-only');
        clearChoiceContainer.removeAttr('aria-hidden');
        clearChoiceContainer.find(SELECTORS.LINK).attr('tabindex', 0);
        clearChoiceContainer.find(SELECTORS.RADIO).prop('disabled', true);
    };

    /**
     * Register event listeners for the clear choice module.
     *
     * @param {Object} root The question outer div prefix.
     * @param {string} fieldPrefix The "Clear choice" div prefix.
     */
    var registerEventListeners = function(root, fieldPrefix) {
        var clearChoiceContainer = getClearChoiceElement(root, fieldPrefix);

        clearChoiceContainer.on(CustomEvents.events.activate, SELECTORS.LINK, function(e, data) {

                // Mark the clear choice radio element as checked.
                checkClearChoiceRadio(clearChoiceContainer);
                // Now that the hidden radio has been checked, hide the clear choice option.
                hideClearChoiceOption(clearChoiceContainer);

                data.originalEvent.preventDefault();
        });

        root.on('change', SELECTORS.CHOICE_ELEMENT, function() {
            // If the event has been triggered by any other choice, show the clear choice option.
            showClearChoiceOption(clearChoiceContainer);
        });

        // If the clear choice radio receives focus from using the tab key, return the focus
        // to the first answer option.
        clearChoiceContainer.find(SELECTORS.RADIO).focus(function() {
            var firstChoice = root.find(SELECTORS.CHOICE_ELEMENT).first();
            firstChoice.focus();
        });
    };

    /**
     * Initialise clear choice module.

     * @param {string} root The question outer div prefix.
     * @param {string} fieldPrefix The "Clear choice" div prefix.
     */
    var init = function(root, fieldPrefix) {
        root = $('#' + root);
        registerEventListeners(root, fieldPrefix);
    };

    return {
        init: init
    };
});
