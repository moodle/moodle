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

export const init = (root) => {
    root = $(root);

    // Hide loading spinner and show report once page is ready.
    // This ensures filters can be applied when sorting by columns.
    $(document).ready(function() {
        $('.loading-icon').hide();
        $('#summaryreport').removeClass('hidden');
    });

    // Generic filter handlers.

    // Event handler to clear filters.
    $(root).on("click", ".filter-clear", function(event) {
        // Clear checkboxes.
        let selected = event.target.parentNode.parentNode.parentElement.querySelectorAll('input[type="checkbox"]:checked');

        selected.forEach(function(checkbox) {
            checkbox.checked = false;
        });
    });

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

    // Select all checkboxes within a filter section.
    var selectAll = (checkboxdiv) => {
        let targetdiv = document.getElementById(checkboxdiv);
        let deselected = targetdiv.querySelectorAll('input[type="checkbox"]:not(:checked)');

        deselected.forEach(function(checkbox) {
            checkbox.checked = true;
        });
    };

    // Groups filter specific handlers.

    // Event to handle select all groups.
    $('#filter-groups-popover .select-all').on('click', function() {
        selectAll('filter-groups-popover');
    });

    // Event handler for showing groups filter popover.
    $('#filter-groups-button').on('click', function() {
        // Create popover.
        var referenceElement = document.querySelector('#filter-groups-button'),
            popperContent = document.querySelector('#filter-groups-popover');

        new Popper(referenceElement, popperContent, {placement: 'bottom'});

        // Show popover.
        $('#filter-groups-popover').removeClass('hidden');
    });

    // Event handler to save groups filter.
    $(root).on("click", "#filter-groups-popover .filter-save", function() {
        // Close the popover.
        $('#filter-groups-popover').addClass('hidden');

        // Submit the filter values and re-generate report.
        generateWithFilters(false);
    });
};
