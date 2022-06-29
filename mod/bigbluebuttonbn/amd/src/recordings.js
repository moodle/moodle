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
 * JS for the recordings page on mod_bigbluebuttonbn plugin.
 *
 * @module      mod_bigbluebuttonbn/recordings
 * @copyright   2021 Blindside Networks Inc
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as repository from './repository';
import {exception as displayException} from 'core/notification';
import {prefetchStrings} from 'core/prefetch';
import {get_string as getString, get_strings as getStrings} from 'core/str';
import {addIconToContainerWithPromise} from 'core/loadingicon';
import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';
import Pending from 'core/pending';

const stringsWithKeys = {
    first: 'view_recording_yui_first',
    prev: 'view_recording_yui_prev',
    next: 'view_recording_yui_next',
    last: 'view_recording_yui_last',
    goToLabel: 'view_recording_yui_page',
    goToAction: 'view_recording_yui_go',
    perPage: 'view_recording_yui_rows',
    showAll: 'view_recording_yui_show_all',
};
// Load global strings.
prefetchStrings('bigbluebuttonbn', Object.entries(stringsWithKeys).map((entry) => entry[1]));

const getStringsForYui = () => {
    const stringMap = Object.keys(stringsWithKeys).map(key => {
        return {
            key: stringsWithKeys[key],
            component: 'mod_bigbluebuttonbn',
        };
    });

    // Return an object with the matching string keys (we want an object with {<stringkey>: <stringvalue>...}).
    return getStrings(stringMap)
        .then((stringArray) => Object.assign({}, ...Object.keys(stringsWithKeys).map(
            (key, index) => ({[key]: stringArray[index]})))
        ).catch();
};

const getYuiInstance = lang => new Promise(resolve => {
    // eslint-disable-next-line
    YUI({
        lang,
    }).use('intl', 'datatable', 'datatable-sort', 'datatable-paginator', 'datatype-number', Y => {
        resolve(Y);
    });
});

/**
 * Format the supplied date per the specified locale.
 *
 * @param   {string} locale
 * @param   {number} date
 * @returns {array}
 */
const formatDate = (locale, date) => {
    const realDate = new Date(date);
    return realDate.toLocaleDateString(locale, {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

/**
 * Format response data for the table.
 *
 * @param   {string} response JSON-encoded table data
 * @returns {array}
 */
const getFormattedData = response => {
    const recordingData = response.tabledata;
    return JSON.parse(recordingData.data);
};

const getTableNode = tableSelector => document.querySelector(tableSelector);

const fetchRecordingData = tableSelector => {
    const tableNode = getTableNode(tableSelector);

    if (tableNode.dataset.importMode) {
        return repository.fetchRecordingsToImport(
            tableNode.dataset.bbbid,
            tableNode.dataset.bbbSourceInstanceId,
            tableNode.dataset.bbbSourceCourseId,
            tableNode.dataset.tools,
            tableNode.dataset.groupId
        );
    } else {
        return repository.fetchRecordings(
            tableNode.dataset.bbbid,
            tableNode.dataset.tools,
            tableNode.dataset.groupId
        );
    }
};

/**
 * Fetch the data table functinos for the specified table.
 *
 * @param {String} tableId in which we will display the table
 * @param {String} searchFormId The Id of the relate.
 * @param {Object} dataTable
 * @returns {Object}
 * @private
 */
const getDataTableFunctions = (tableId, searchFormId, dataTable) => {
    const tableNode = getTableNode(tableId);
    const bbbid = tableNode.dataset.bbbid;

    const updateTableFromResponse = response => {
        if (!response || !response.status) {
            // There was no output at all.
            return;
        }

        dataTable.get('data').reset(getFormattedData(response));
        dataTable.set(
            'currentData',
            dataTable.get('data')
        );

        const currentFilter = dataTable.get('currentFilter');
        if (currentFilter) {
            filterByText(currentFilter);
        }
    };

    const refreshTableData = () => fetchRecordingData(tableId).then(updateTableFromResponse);

    const filterByText = value => {
        const dataModel = dataTable.get('currentData');
        dataTable.set('currentFilter', value);

        const escapedRegex = value.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&");
        const rsearch = new RegExp(`<span>.*?${escapedRegex}.*?</span>`, 'i');

        dataTable.set('data', dataModel.filter({asList: true}, item => {
            const name = item.get('recording');
            if (name && rsearch.test(name)) {
                return true;
            }

            const description = item.get('description');
            return description && rsearch.test(description);
        }));
    };

    const requestAction = (element) => {
        const getDataFromAction = (element, dataType) => {
            const dataElement = element.closest(`[data-${dataType}]`);
            if (dataElement) {
                return dataElement.dataset[dataType];
            }

            return null;
        };

        const elementData = element.dataset;
        const payload = {
            bigbluebuttonbnid: bbbid,
            recordingid: getDataFromAction(element, 'recordingid'),
            additionaloptions: getDataFromAction(element, 'additionaloptions'),
            action: elementData.action,
        };
        // Slight change for import, for additional options.
        if (!payload.additionaloptions) {
            payload.additionaloptions = {};
        }
        if (elementData.action === 'import') {
            const bbbsourceid = getDataFromAction(element, 'source-instance-id');
            const bbbcourseid = getDataFromAction(element, 'source-course-id');
            if (!payload.additionaloptions) {
                payload.additionaloptions = {};
            }
            payload.additionaloptions.sourceid = bbbsourceid ? bbbsourceid : 0;
            payload.additionaloptions.bbbcourseid = bbbcourseid ? bbbcourseid : 0;
        }
        // Now additional options should be a json string.
        payload.additionaloptions = JSON.stringify(payload.additionaloptions);
        if (element.dataset.requireConfirmation === "1") {
            // Create the confirmation dialogue.
            return new Promise((resolve) =>
                ModalFactory.create({
                    title: getString('confirm'),
                    body: recordingConfirmationMessage(payload),
                    type: ModalFactory.types.SAVE_CANCEL
                }).then(async(modal) => {
                    modal.setSaveButtonText(await getString('ok', 'moodle'));

                    // Handle save event.
                    modal.getRoot().on(ModalEvents.save, () => {
                        resolve(true);
                    });

                    // Handle hidden event.
                    modal.getRoot().on(ModalEvents.hidden, () => {
                        // Destroy when hidden.
                        modal.destroy();
                        resolve(false);
                    });

                    modal.show();

                    return modal;
                }).catch(Notification.exception)
            ).then((proceed) =>
                proceed ? repository.updateRecording(payload) : () => null
            );
        } else {
            return repository.updateRecording(payload);
        }
    };

    const recordingConfirmationMessage = async(data) => {

        const playbackElement = document.querySelector(`#playbacks-${data.recordingid}`);
        const recordingType = await getString(
            playbackElement.dataset.imported === 'true' ? 'view_recording_link' : 'view_recording',
            'bigbluebuttonbn'
        );

        const confirmation = await getString(`view_recording_${data.action}_confirmation`, 'bigbluebuttonbn', recordingType);

        if (data.action === 'import') {
            return confirmation;
        }

        // If it has associated links imported in a different course/activity, show that in confirmation dialog.
        const associatedLinkCount = document.querySelector(`a#recording-${data.action}-${data.recordingid}`)?.dataset?.links;
        if (!associatedLinkCount || associatedLinkCount === 0) {
            return confirmation;
        }

        const confirmationWarning = await getString(
            associatedLinkCount === 1
                ? `view_recording_${data.action}_confirmation_warning_p`
                : `view_recording_${data.action}_confirmation_warning_s`,
            'bigbluebuttonbn',
            associatedLinkCount
        );

        return confirmationWarning + '\n\n' + confirmation;
    };

    /**
     * Process an action event.
     *
     * @param   {Event} e
     */
    const processAction = e => {
        const popoutLink = e.target.closest('[data-action="play"]');
        if (popoutLink) {
            e.preventDefault();

            const videoPlayer = window.open('', '_blank');
            videoPlayer.opener = null;
            videoPlayer.location.href = popoutLink.href;
            // TODO send a recording viewed event when this event will be implemented.
            return;
        }

        // Fetch any clicked anchor.
        const clickedLink = e.target.closest('a[data-action]');
        if (clickedLink && !clickedLink.classList.contains('disabled')) {
            e.preventDefault();

            // Create a spinning icon on the table.
            const iconPromise = addIconToContainerWithPromise(dataTable.get('boundingBox').getDOMNode());

            requestAction(clickedLink)
                .then(refreshTableData)
                .catch(displayException)
                .then(iconPromise.resolve)
                .catch();
        }
    };

    const processSearchSubmission = e => {
        // Prevent the default action.
        e.preventDefault();
        const parentNode = e.target.closest('div[role=search]');
        const searchInput = parentNode.querySelector('input[name=search]');
        filterByText(searchInput.value);
    };

    const registerEventListeners = () => {
        // Add event listeners to the table boundingBox.
        const boundingBox = dataTable.get('boundingBox').getDOMNode();
        boundingBox.addEventListener('click', processAction);

        // Setup the search from handlers.
        const searchForm = document.querySelector(searchFormId);
        if (searchForm) {
            const searchButton = document.querySelector(searchFormId + ' button');
            searchButton.addEventListener('click', processSearchSubmission);
        }
    };

    return {
        filterByText,
        refreshTableData,
        registerEventListeners,
    };
};

/**
 * Setup the data table for the specified BBB instance.
 *
 * @param {String} tableId in which we will display the table
 * @param {String} searchFormId The Id of the relate.
 * @param   {object} response The response from the data request
 * @returns {Promise}
 */
const setupDatatable = (tableId, searchFormId, response) => {
    if (!response) {
        return Promise.resolve();
    }

    if (!response.status) {
        // Something failed. Continue to show the plain output.
        return Promise.resolve();
    }

    const recordingData = response.tabledata;

    const pendingPromise = new Pending('mod_bigbluebuttonbn/recordings/setupDatatable');
    return Promise.all([getYuiInstance(recordingData.locale), getStringsForYui()])
        .then(([yuiInstance, strings]) => {
            // Here we use a custom formatter for date.
            // See https://clarle.github.io/yui3/yui/docs/api/classes/DataTable.BodyView.Formatters.html
            // Inspired from examples here: https://clarle.github.io/yui3/yui/docs/datatable/
            // Normally formatter have the prototype: (col) => (cell) => <computed value>, see:
            // https://clarle.github.io/yui3/yui/docs/api/files/datatable_js_formatters.js.html#l100 .
            const dateCustomFormatter = () => (cell) => formatDate(recordingData.locale, cell.value);
            // Add the fetched strings to the YUI Instance.
            yuiInstance.Intl.add('datatable-paginator', yuiInstance.config.lang, {...strings});
            yuiInstance.DataTable.BodyView.Formatters.customDate = dateCustomFormatter;
            return yuiInstance;
        })
        .then(yuiInstance => {

            const tableData = getFormattedData(response);
            yuiInstance.RecordsPaginatorView = Y.Base.create('my-paginator-view', yuiInstance.DataTable.Paginator.View, [], {
                _modelChange: function(e) {
                    var changed = e.changed,
                        totalItems = (changed && changed.totalItems);
                    if (totalItems) {
                        this._updateControlsUI(e.target.get('page'));
                    }
                }
            });
            return new yuiInstance.DataTable({
                paginatorView: "RecordsPaginatorView",
                width: "1195px",
                columns: recordingData.columns,
                data: tableData,
                rowsPerPage: 10,
                paginatorLocation: ['header', 'footer'],
                autoSync: true
            });
        })
        .then(dataTable => {
            dataTable.render(tableId);
            const {registerEventListeners} = getDataTableFunctions(
                tableId,
                searchFormId,
                dataTable);
            registerEventListeners();
            return dataTable;
        })
        .then(dataTable => {
            pendingPromise.resolve();
            return dataTable;
        });
};

/**
 * Initialise recordings code.
 *
 * @method init
 * @param {String} tableId in which we will display the table
 * @param {String} searchFormId The Id of the relate.
 */
export const init = (tableId, searchFormId) => {
    fetchRecordingData(tableId)
        .then(response => setupDatatable(tableId, searchFormId, response))
        .catch(displayException);
};
