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
 * @copyright  2019 Michael Hawkins <michaelh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';
import {createPopper} from 'core/popper2';
import CustomEvents from 'core/custom_interaction_events';
import Selectors from 'forumreport_summary/selectors';
import Ajax from 'core/ajax';
import KeyCodes from 'core/key_codes';
import * as FormChangeChecker from 'core_form/changechecker';

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
    const generateWithFilters = (event, getparams) => {
        let currentLink = document.forms.filtersform.action,
            newLink;

        if (event) {
            event.preventDefault();

           let currentSplit = currentLink.split('?'),
               currentstring = currentSplit[1],
               newparamsarray = getparams.split('&'),
               paramsstring = '',
               paramkeys = [],
               paramvalues = [];

            // Separate out the existing action GET param string.
            currentstring.split('&').forEach(function(param) {
                let splitparam = param.split('=');
                paramkeys.push(splitparam[0]);
                paramvalues.push(splitparam[1]);
            });

            newparamsarray.forEach(function(paramstring) {
                let newparam = paramstring.split('='),
                    existingkey = paramkeys.indexOf(newparam[0]);

                // Overwrite value if existing, otherwise add new param.
                if (existingkey > -1) {
                    paramvalues[existingkey] = newparam[1];
                } else {
                    paramkeys.push(newparam[0]);
                    paramvalues.push(newparam[1]);
                }
            });

            // Build URL.
            paramkeys.forEach(function(name, key) {
                paramsstring += `&${name}=${paramvalues[key]}`;
            });

            newLink = currentSplit[0] + '?' + paramsstring.substr(1);
        } else {
            newLink = currentLink;
        }

        document.forms.filtersform.action = newLink;
        document.forms.filtersform.submit();
    };

    // Override 'reset table preferences' so it generates with filters.
    $('.resettable').on("click", "a", function(event) {
        generateWithFilters(event, event.target.search.substr(1));
    });

    // Override table heading sort links so they generate with filters.
    $('thead').on("click", "a", function(event) {
        generateWithFilters(event, event.target.search.substr(1));
    });

    // Override pagination page links so they generate with filters.
    $('.pagination').on("click", "a", function(event) {
        generateWithFilters(event, event.target.search.substr(1));
    });

    // Override rows per page submission so it generates with filters.
    if (document.forms.selectperpage) {
        document.forms.selectperpage.onsubmit = (event) => {
            let getparam = 'perpage=' + document.forms.selectperpage.elements.perpage.value;
            generateWithFilters(event, getparam);
        };
    }

    // Override download link so the file is generated with filters.
    const downloadForm = document.getElementById('summaryreport').querySelector('form.dataformatselector');
    if (downloadForm) {
        downloadForm.onsubmit = (event) => {
            const downloadType = downloadForm.querySelector('#downloadtype_download').value;
            const getParams = `download=${downloadType}`;
            const prevAction = document.forms.filtersform.action;

            generateWithFilters(event, getParams);

            // Revert action, so re-submitting the form via filter does not trigger a further download.
            document.forms.filtersform.action = prevAction;
        };
    }

    // Submit report via filter
    const submitWithFilter = (containerelement) => {
        // Disable the dates filter mform checker to prevent any changes triggering a warning to the user.
        FormChangeChecker.unWatchForm(document.querySelector(containerelement).querySelector('form'));
        // Close the container (eg popover).
        $(containerelement).addClass('hidden');

        // Submit the filter values and re-generate report.
        generateWithFilters(false);
    };

    // Use popper to override date mform calendar position.
    const updateCalendarPosition = (referenceid) => {
        let referenceElement = document.querySelector(referenceid),
            popperContent = document.querySelector(Selectors.filters.date.calendar);

        popperContent.style.removeProperty("z-index");
        createPopper(referenceElement, popperContent, {placement: 'bottom-end'});
    };

    // Close the relevant filter.
    const closeOpenFilters = (openFilterButton, openFilter) => {
        openFilter.classList.add('hidden');
        openFilter.setAttribute('data-openfilter', 'false');

        openFilterButton.classList.add('btn-primary');
        openFilterButton.classList.remove('btn-outline-primary');
        openFilterButton.setAttribute('aria-expanded', false);
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
        let referenceElement = root.querySelector(Selectors.filters.group.trigger),
            popperContent = root.querySelector(Selectors.filters.group.popover);

        createPopper(referenceElement, popperContent, {placement: 'bottom-end'});

        // Show popover.
        popperContent.classList.remove('hidden');
        popperContent.setAttribute('data-openfilter', 'true');

        // Change to outlined button.
        referenceElement.classList.add('btn-outline-primary');
        referenceElement.classList.remove('btn-primary');

        // Let screen readers know that it's now expanded.
        referenceElement.setAttribute('aria-expanded', true);

        // Add listeners to handle closing filter.
        const closeListener = e => {
            if (e.target.id !== referenceElement.id && popperContent !== e.target.closest('[data-openfilter="true"]') &&
                    (typeof e.keyCode === 'undefined' || e.keyCode === KeyCodes.enter || e.keyCode === KeyCodes.space)) {
                closeOpenFilters(referenceElement, popperContent);
                document.removeEventListener('click', closeListener);
                document.removeEventListener('keyup', closeListener);
                document.removeEventListener('keyup', escCloseListener);
            }
        };

        document.addEventListener('click', closeListener);
        document.addEventListener('keyup', closeListener);

        const escCloseListener = e => {
            if (e.keyCode === KeyCodes.escape) {
                closeOpenFilters(referenceElement, popperContent);
                document.removeEventListener('keyup', escCloseListener);
                document.removeEventListener('click', closeListener);
            }
        };

        document.addEventListener('keyup', escCloseListener);
    });

    // Event handler to click save groups filter.
    jqRoot.on(CustomEvents.events.activate, Selectors.filters.group.save, function() {
        // Copy the saved values into the form before submitting.
        let popcheckboxes = root.querySelectorAll(Selectors.filters.group.checkbox);

        popcheckboxes.forEach(function(popcheckbox) {
            let filtersform = document.forms.filtersform,
                saveid = popcheckbox.getAttribute('data-saveid');

            filtersform.querySelector(`#${saveid}`).checked = popcheckbox.checked;
        });

        submitWithFilter('#filter-groups-popover');
    });

    // Listeners for export buttons.
    // These allow fetching of the relevant export URL, before submitting the request with
    // any POST data that is common to all of the export links. This allows filters to be
    // applied that contain potentially a lot of data (eg discussion IDs for groups filtering).
    document.querySelectorAll(Selectors.filters.exportlink.link).forEach(function(exportbutton) {
        exportbutton.addEventListener('click', function(event) {
            document.forms.exportlinkform.action = event.target.dataset.url;
            document.forms.exportlinkform.submit();
        });
    });

    // Dates filter specific handlers.

   // Event handler for showing dates filter popover.
    jqRoot.on(CustomEvents.events.activate, Selectors.filters.date.trigger, function() {

        // Create popover.
        let referenceElement = root.querySelector(Selectors.filters.date.trigger),
            popperContent = root.querySelector(Selectors.filters.date.popover);

        createPopper(referenceElement, popperContent, {placement: 'bottom-end'});

        // Show popover and move focus.
        popperContent.classList.remove('hidden');
        popperContent.setAttribute('data-openfilter', 'true');
        popperContent.querySelector('[name="filterdatefrompopover[enabled]"]').focus();

        // Change to outlined button.
        referenceElement.classList.add('btn-outline-primary');
        referenceElement.classList.remove('btn-primary');

        // Let screen readers know that it's now expanded.
        referenceElement.setAttribute('aria-expanded', true);

        // Add listener to handle closing filter.
        const closeListener = e => {
            if (e.target.id !== referenceElement.id && popperContent !== e.target.closest('[data-openfilter="true"]') &&
                    (typeof e.keyCode === 'undefined' || e.keyCode === KeyCodes.enter || e.keyCode === KeyCodes.space)) {
                closeOpenFilters(referenceElement, popperContent);
                document.removeEventListener('click', closeListener);
                document.removeEventListener('keyup', closeListener);
                document.removeEventListener('keyup', escCloseListener);
            }
        };

        document.addEventListener('click', closeListener);
        document.addEventListener('keyup', closeListener);

        const escCloseListener = e => {
            if (e.keyCode === KeyCodes.escape) {
                closeOpenFilters(referenceElement, popperContent);
                document.removeEventListener('keyup', escCloseListener);
                document.removeEventListener('click', closeListener);
            }
        };

        document.addEventListener('keyup', escCloseListener);
    });

    // Event handler to save dates filter.
    jqRoot.on(CustomEvents.events.activate, Selectors.filters.date.save, function() {
        // Populate the hidden form inputs to submit the data.
        let filtersForm = document.forms.filtersform;
        const datesPopover = root.querySelector(Selectors.filters.date.popover);
        const fromEnabled = datesPopover.querySelector('[name="filterdatefrompopover[enabled]"]').checked ? 1 : 0;
        const toEnabled = datesPopover.querySelector('[name="filterdatetopopover[enabled]"]').checked ? 1 : 0;

        if (!fromEnabled && !toEnabled) {
            // Update the elements in the filter form.
            filtersForm.elements['datefrom[timestamp]'].value = 0;
            filtersForm.elements['datefrom[enabled]'].value = fromEnabled;
            filtersForm.elements['dateto[timestamp]'].value = 0;
            filtersForm.elements['dateto[enabled]'].value = toEnabled;

            // Submit the filter values and re-generate report.
            submitWithFilter('#filter-dates-popover');
        } else {
            let args = {data: []};

            if (fromEnabled) {
                args.data.push({
                    'key': 'from',
                    'year': datesPopover.querySelector('[name="filterdatefrompopover[year]"]').value,
                    'month': datesPopover.querySelector('[name="filterdatefrompopover[month]"]').value,
                    'day': datesPopover.querySelector('[name="filterdatefrompopover[day]"]').value,
                    'hour': 0,
                    'minute': 0
                });
            }

            if (toEnabled) {
                args.data.push({
                    'key': 'to',
                    'year': datesPopover.querySelector('[name="filterdatetopopover[year]"]').value,
                    'month': datesPopover.querySelector('[name="filterdatetopopover[month]"]').value,
                    'day': datesPopover.querySelector('[name="filterdatetopopover[day]"]').value,
                    'hour': 23,
                    'minute': 59
                });
            }

            const request = {
                methodname: 'core_calendar_get_timestamps',
                args: args
            };

            Ajax.call([request])[0].done(function(result) {
                let fromTimestamp = 0,
                    toTimestamp = 0;

                result.timestamps.forEach(function(data) {
                    if (data.key === 'from') {
                        fromTimestamp = data.timestamp;
                    } else if (data.key === 'to') {
                        toTimestamp = data.timestamp;
                    }
                });

                // Display an error if the from date is later than the do date.
                if (toTimestamp > 0 && fromTimestamp > toTimestamp) {
                    const warningdiv = document.getElementById('dates-filter-warning');
                    warningdiv.classList.remove('hidden');
                    warningdiv.classList.add('d-block');
                } else {
                    filtersForm.elements['datefrom[timestamp]'].value = fromTimestamp;
                    filtersForm.elements['datefrom[enabled]'].value = fromEnabled;
                    filtersForm.elements['dateto[timestamp]'].value = toTimestamp;
                    filtersForm.elements['dateto[enabled]'].value = toEnabled;

                    // Submit the filter values and re-generate report.
                    submitWithFilter('#filter-dates-popover');
                }
            });
        }
    });

    jqRoot.on(CustomEvents.events.activate, Selectors.filters.date.calendariconfrom, function() {
        updateCalendarPosition(Selectors.filters.date.calendariconfrom);
    });

    jqRoot.on(CustomEvents.events.activate, Selectors.filters.date.calendariconto, function() {
        updateCalendarPosition(Selectors.filters.date.calendariconto);
    });
};
