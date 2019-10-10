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
 * Module responsible for handling forum summary report filters.
 *
 * @module     forumreport_summary/filters
 * @package    forumreport_summary
 * @copyright  2019 Michael Hawkins <michaelh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';
import Popper from 'core/popper';
import CustomEvents from 'core/custom_interaction_events';
import Selectors from 'forumreport_summary/selectors';

export const init = (root) => {
    let jqRoot = $(root);

    // Hide loading spinner and show report once page is ready.
    // This ensures filters can be applied when sorting by columns.
    $(document).ready(function() {
        $('.loading-icon').hide();
        $('#summaryreport').removeClass('hidden');
    });

    // Generic filter handlers.

    // Called to override click event to trigger a proper generate request with filtering.
    var generateWithFilters = (event) => {
        var newLink = $('#filtersform').attr('action');

        if (event) {
            event.preventDefault();

            let filterParams = event.target.search.substr(1);
            newLink += '&' + filterParams;
        }

        $('#filtersform').attr('action', newLink);
        $('#filtersform').submit();
    };

    // Override 'reset table preferences' so it generates with filters.
    $('.resettable').on("click", "a", function(event) {
        generateWithFilters(event);
    });

    // Override table heading sort links so they generate with filters.
    $('thead').on("click", "a", function(event) {
        generateWithFilters(event);
    });

    // Override pagination page links so they generate with filters.
    $('.pagination').on("click", "a", function(event) {
        generateWithFilters(event);
    });

    // Submit report via filter
    var submitWithFilter = (containerelement) => {
        // Close the container (eg popover).
        $(containerelement).addClass('hidden');

        // Submit the filter values and re-generate report.
        generateWithFilters(false);
    };

    // Groups filter specific handlers.

    // Event handler for clicking select all groups.
    jqRoot.on(CustomEvents.events.activate, Selectors.filters.group.selectall, function() {
        let deselected = root.querySelectorAll(Selectors.filters.group.checkbox + ':not(:checked)');
        deselected.forEach(function(checkbox) {
            checkbox.checked = true;
        });
    });

    // Event handler for clearing filter by clicking option.
    jqRoot.on(CustomEvents.events.activate, Selectors.filters.group.clear, function() {
        // Clear checkboxes.
        let selected = root.querySelectorAll(Selectors.filters.group.checkbox + ':checked');
        selected.forEach(function(checkbox) {
            checkbox.checked = false;
        });
    });

    // Event handler for showing groups filter popover.
    jqRoot.on(CustomEvents.events.activate, Selectors.filters.group.trigger, function() {
        // Create popover.
        var referenceElement = root.querySelector(Selectors.filters.group.trigger),
            popperContent = root.querySelector(Selectors.filters.group.popover);

        new Popper(referenceElement, popperContent, {placement: 'bottom'});

        // Show popover.
        popperContent.classList.remove('hidden');

        // Change to outlined button.
        referenceElement.classList.add('btn-outline-primary');
        referenceElement.classList.remove('btn-primary');

        // Let screen readers know that it's now expanded.
        referenceElement.setAttribute('aria-expanded', true);
    });

    // Event handler to click save groups filter.
    jqRoot.on(CustomEvents.events.activate, Selectors.filters.group.save, function() {
        submitWithFilter('#filter-groups-popover');
    });
};
