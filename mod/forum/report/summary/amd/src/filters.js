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

    // Called to clear filters.
    var clearAll = (event) => {
        // Clear checkboxes.
        let selected = event.target.parentNode.parentNode.parentElement.querySelectorAll('input[type="checkbox"]:checked');

        selected.forEach(function(checkbox) {
            checkbox.checked = false;
        });
    };

    // Event handler for clearing filter by clicking option.
    $(root).on("click", ".filter-clear", function(event) {
        event.preventDefault();
        clearAll(event);
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

    // Submit report via filter
    var submitWithFilter = (containerelement) => {
        // Close the container (eg popover).
        $(containerelement).addClass('hidden');

        // Submit the filter values and re-generate report.
        generateWithFilters(false);
    };

    // Groups filter specific handlers.

    // Event handler for clicking select all groups.
    $('#filter-groups-popover .select-all').on('click', function(event) {
        event.preventDefault();
        selectAll('filter-groups-popover');
    });

    // Event handler for showing groups filter popover.
    $('#filter-groups-button').on('click', function() {
        // Create popover.
        var referenceElement = document.querySelector('#filter-groups-button'),
            popperContent = document.querySelector('#filter-groups-popover');

        new Popper(referenceElement, popperContent, {placement: 'bottom'});

        // Show popover and switch focus.
        var groupsbutton = document.getElementById('filter-groups-button'),
            groupspopover = document.getElementById('filter-groups-popover');
        groupspopover.classList.remove('hidden');
        groupsbutton.setAttribute('aria-expanded', true);
        groupsbutton.classList.add('btn-outline-primary');
        groupsbutton.classList.remove('btn-primary');
        groupspopover.querySelector('input').focus();
    });

    // Event handler to click save groups filter.
    $(root).on("click", "#filter-groups-popover .filter-save", function(event) {
        event.preventDefault();
        submitWithFilter('#filter-groups-popover');
    });

    // Event handler to support pressing enter/space on groups filter popover actions.
    $('#filter-groups-popover').on("keydown", ".filter-actions", function(event) {
    if ((event.charCode === 13 || event.keyCode === 13 || event.charCode === 32 || event.keyCode === 32)
                && event.target.classList.length > 0) {
            event.preventDefault();

            switch(event.target.classList[0]) {
                case 'select-all':
                    selectAll('filter-groups-popover');
                    break;
                case 'filter-clear':
                    clearAll(event);
                    break;
                case 'filter-save':
                    submitWithFilter('#filter-groups-popover');
                    break;
            }
        }
    });
};
