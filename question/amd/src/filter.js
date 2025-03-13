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
 * Question bank filter management.
 *
 * @module     core_question/filter
 * @copyright  2021 Tomo Tsuyuki <tomotsuyuki@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import CoreFilter from 'core/datafilter';
import Notification from 'core/notification';
import Selectors from 'core/datafilter/selectors';
import Templates from 'core/templates';
import Fragment from 'core/fragment';
import {getString} from 'core/str';
import {addIconToContainerRemoveOnCompletion} from 'core/loadingicon';

/**
 * Initialise the question bank filter on the element with the given id.
 *
 * @param {String} filterRegionId ID of the HTML element containing the filters.
 * @param {String} defaultcourseid Course ID for the default course to pass back to the view.
 * @param {String} defaultcategoryid Question bank category ID for the default course to pass back to the view.
 * @param {Number} perpage The number of questions to display per page.
 * @param {Number} bankContextId Context ID of the question bank being filtered.
 * @param {Number} quizCmId Course module ID of the quiz as the viewing context.
 * @param {string} component Frankenstyle name of the component for the fragment API callback (e.g. core_question)
 * @param {string} callback Name of the callback for the fragment API (e.g question_data)
 * @param {string} view The class name of the question bank view class used for this page.
 * @param {Number} cmid If we are in an activitiy, the course module ID.
 * @param {Object} pagevars JSON-encoded parameters from passed from the view, including filters and jointype.
 * @param {Object} extraparams JSON-encoded additional parameters specific to this view class, used for re-rendering the view.
 */
export const init = async(
    filterRegionId,
    defaultcourseid,
    defaultcategoryid,
    perpage,
    bankContextId,
    quizCmId,
    component,
    callback,
    view,
    cmid,
    pagevars,
    extraparams,
) => {

    const SELECTORS = {
        QUESTION_CONTAINER_ID: '#questionscontainer',
        QUESTION_TABLE: '#questionscontainer table',
        SORT_LINK: '#questionscontainer div.sorters a',
        PAGINATION_LINK: '#questionscontainer a[href].page-link',
        LASTCHANGED_FIELD: '#questionsubmit input[name=lastchanged]',
        BULK_ACTIONS: '#bulkactionsui-container input',
        MENU_ACTIONS: '.menu-action',
        EDIT_SWITCH: '.editmode-switch-form input[name=setmode]',
        EDIT_SWITCH_URL: '.editmode-switch-form input[name=pageurl]',
        SHOW_ALL_LINK: '[data-filteraction="showall"]',
    };

    const filterSet = document.querySelector(`#${filterRegionId}`);

    const viewData = {
        extraparams: JSON.stringify(extraparams),
        cmid,
        view,
        cat: defaultcategoryid,
        courseid: defaultcourseid,
        filter: {},
        jointype: 0,
        qpage: 0,
        qperpage: perpage,
        sortdata: {},
        lastchanged: document.querySelector(SELECTORS.LASTCHANGED_FIELD)?.value ?? null,
    };

    let sortData = {};
    const defaultSort = document.querySelector(SELECTORS.QUESTION_TABLE)?.dataset?.defaultsort;
    if (defaultSort) {
        sortData = JSON.parse(defaultSort);
    }

    const [
        showAllText,
        showPerPageText,
    ] = await Promise.all([
        getString('showall', 'core', ''),
        getString('showperpage', 'core', extraparams.defaultqperpage),
    ]);

    /**
     * Retrieve table data.
     *
     * @param {Object} filterdata data
     * @param {Promise} pendingPromise pending promise
     */
    const applyFilter = (filterdata, pendingPromise) => {
        // Reload the questions based on the specified filters. If no filters are provided,
        // use the default category filter condition.
        if (filterdata) {
            // Main join types.
            viewData.jointype = parseInt(filterSet.dataset.filterverb, 10);
            delete filterdata.jointype;
            // Retrieve filter info.
            viewData.filter = filterdata;
            if (Object.keys(filterdata).length !== 0) {
                if (!isNaN(viewData.jointype)) {
                    filterdata.jointype = viewData.jointype;
                }
            }
        }
        // Load questions for first page.
        viewData.filter = JSON.stringify(filterdata);
        viewData.sortdata = JSON.stringify(sortData);
        viewData.quizcmid = quizCmId;

        const questionscontainer = document.querySelector(SELECTORS.QUESTION_CONTAINER_ID);
        // Clear the contents of the element, then append the loading icon.
        questionscontainer.innerHTML = '';
        addIconToContainerRemoveOnCompletion(questionscontainer, pendingPromise);

        Fragment.loadFragment(component, callback, bankContextId, viewData)
            // Render questions for first page and pagination.
            .then((questionhtml, jsfooter) => {
                updateUrlParams(filterdata);
                if (questionhtml === undefined) {
                    questionhtml = '';
                }
                if (jsfooter === undefined) {
                    jsfooter = '';
                }
                Templates.replaceNode(questionscontainer, questionhtml, jsfooter);
                // Resolve filter promise.
                if (pendingPromise) {
                    pendingPromise.resolve();
                }
                return {questionhtml, jsfooter};
            })
            .catch(Notification.exception);
    };

    // Init core filter processor with apply callback.
    const coreFilter = new CoreFilter(filterSet, applyFilter);
    coreFilter.activeFilters = {}; // Unset useless courseid filter.
    coreFilter.init();

    /**
     * Update URL Param based upon the current filter.
     *
     * @param {Object} filters Active filters.
     */
    const updateUrlParams = (filters) => {
        const url = new URL(location.href);
        const filterQuery = JSON.stringify(filters);
        url.searchParams.set('filter', filterQuery);
        history.pushState(filters, '', url);
        const editSwitch = document.querySelector(SELECTORS.EDIT_SWITCH);
        if (editSwitch) {
            const editSwitchUrlInput = document.querySelector(SELECTORS.EDIT_SWITCH_URL);
            const editSwitchUrl = new URL(editSwitchUrlInput.value);
            editSwitchUrl.searchParams.set('filter', filterQuery);
            editSwitchUrlInput.value = editSwitchUrl;
            editSwitch.dataset.pageurl = editSwitchUrl;
        }
    };

    /**
     * Cleans URL parameters.
     */
    const cleanUrlParams = () => {
        const queryString = location.search;
        const urlParams = new URLSearchParams(queryString);
        if (urlParams.has('cmid')) {
            const cleanedUrl = new URL(location.href.replace(location.search, ''));
            cleanedUrl.searchParams.set('cmid', urlParams.get('cmid'));
            history.pushState({}, '', cleanedUrl);
        }

        if (urlParams.has('courseid')) {
            const cleanedUrl = new URL(location.href.replace(location.search, ''));
            cleanedUrl.searchParams.set('courseid', urlParams.get('courseid'));
            history.pushState({}, '', cleanedUrl);
        }
    };

    // Add listeners for the sorting, paging and clear actions.
    document.querySelector('.questionbankwindow').addEventListener('click', e => {
        const sortableLink = e.target.closest(SELECTORS.SORT_LINK);
        const paginationLink = e.target.closest(SELECTORS.PAGINATION_LINK);
        const clearLink = e.target.closest(Selectors.filterset.actions.resetFilters);
        const showallLink = e.target.closest(SELECTORS.SHOW_ALL_LINK);
        if (sortableLink) {
            e.preventDefault();
            const oldSort = sortData;
            sortData = {};
            sortData[sortableLink.dataset.sortname] = sortableLink.dataset.sortorder;
            for (const sortname in oldSort) {
                if (sortname !== sortableLink.dataset.sortname) {
                    sortData[sortname] = oldSort[sortname];
                }
            }
            viewData.qpage = 0;
            coreFilter.updateTableFromFilter(false);
        }
        if (paginationLink) {
            e.preventDefault();
            const paginationURL = new URL(paginationLink.getAttribute("href"));
            const qpage = paginationURL.searchParams.get('qpage');
            if (paginationURL.search !== null) {
                viewData.qpage = qpage;
                coreFilter.updateTableFromFilter(false);
            }
        }
        if (clearLink) {
            cleanUrlParams();
        }
        if (showallLink) {

            e.preventDefault();

            // Toggle between showing all and going back to the original qperpage.
            if (Number(showallLink.dataset.status) === 0) {
                viewData.qperpage = extraparams.maxqperpage;
                showallLink.dataset.status = 1;
                showallLink.innerText = showPerPageText;
            } else {
                viewData.qperpage = extraparams.defaultqperpage;
                showallLink.dataset.status = 0;
                showallLink.innerText = showAllText;
            }
            viewData.qpage = 0;
            coreFilter.updateTableFromFilter();
        }
    });

    // Run apply filter at page load.
    let initialFilters;
    let jointype = null;
    if (pagevars.filter) {
        // Load initial filter based on page vars.
        initialFilters = pagevars.filter;
        if (pagevars.jointype) {
            jointype = pagevars.jointype;
        }
    }

    if (Object.entries(initialFilters).length !== 0) {
        // Remove the default empty filter row.
        const emptyFilterRow = filterSet.querySelector(Selectors.filterset.regions.emptyFilterRow);
        if (emptyFilterRow) {
            emptyFilterRow.remove();
        }

        // Add filters.
        let rowcount = 0;
        for (const urlFilter in initialFilters) {
            if (urlFilter === 'jointype') {
                jointype = initialFilters[urlFilter];
                continue;
            }
            // Add each filter row.
            rowcount += 1;
            const filterdata = {
                filtertype: urlFilter,
                values:  initialFilters[urlFilter].values,
                jointype: initialFilters[urlFilter].jointype,
                filteroptions: initialFilters[urlFilter].filteroptions,
                rownum: rowcount
            };
            coreFilter.addFilterRow(filterdata);
        }
        coreFilter.filterSet.dataset.filterverb = jointype;

        // Since we must filter by category, it does not make sense to allow the top-level "match any" or "match none" conditions,
        // as this would exclude the category. Remove those options and disable the select.
        const join = coreFilter.filterSet.querySelector(Selectors.filterset.fields.join);
        join.querySelectorAll(`option:not([value="${jointype}"])`).forEach((option) => option.remove());
        join.disabled = true;
    }
};
