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
 * Report builder editor
 *
 * @module      core_reportbuilder/editor
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";

import 'core/inplace_editable';
import Notification from 'core/notification';
import Pending from 'core/pending';
import Templates from 'core/templates';
import {get_string as getString} from 'core/str';
import {add as addToast} from 'core/toast';
import * as reportSelectors from 'core_reportbuilder/local/selectors';
import {init as columnsEditorInit} from 'core_reportbuilder/local/editor/columns';
import {init as conditionsEditorInit} from 'core_reportbuilder/local/editor/conditions';
import {init as filtersEditorInit} from 'core_reportbuilder/local/editor/filters';
import {init as sortingEditorInit} from 'core_reportbuilder/local/editor/sorting';
import {init as cardviewEditorInit} from 'core_reportbuilder/local/editor/card_view';
import {getReport} from 'core_reportbuilder/local/repository/reports';
import {createReportModal} from 'core_reportbuilder/local/repository/modals';

let initialized = false;

/**
 * Initialise editor and all it's modules
 */
export const init = () => {
    columnsEditorInit(initialized);
    conditionsEditorInit(initialized);
    filtersEditorInit(initialized);
    sortingEditorInit(initialized);
    cardviewEditorInit(initialized);

    // Ensure we only add our listeners once (can be called multiple times by mustache template).
    if (initialized) {
        return;
    }

    // Add event handlers to generic report editor elements.
    document.addEventListener('click', event => {

        // Toggle between edit and preview mode.
        const toggleEditViewMode = event.target.closest(reportSelectors.actions.toggleEditPreview);
        if (toggleEditViewMode) {
            event.preventDefault();

            const reportElement = event.target.closest(reportSelectors.regions.report);
            const pendingPromise = new Pending('core_reportbuilder/reports:get');
            const toggledEditMode = toggleEditViewMode.dataset.editMode !== "1";

            let customjs = '';

            getReport(reportElement.dataset.reportId, toggledEditMode)
                .then(response => {
                    customjs = response.javascript;
                    return Templates.render('core_reportbuilder/local/dynamictabs/editor', response);
                })
                .then((html, js) => {
                    return Templates.replaceNode(reportElement, html, js + customjs);
                })
                .then(() => pendingPromise.resolve())
                .catch(Notification.exception);
        }

        // Edit report details modal.
        const reportEdit = event.target.closest(reportSelectors.actions.reportEdit);
        if (reportEdit) {
            event.preventDefault();

            const reportModal = createReportModal(event.target, getString('editreportdetails', 'core_reportbuilder'),
                reportEdit.dataset.reportId);
            reportModal.addEventListener(reportModal.events.FORM_SUBMITTED, () => {
                getString('reportupdated', 'core_reportbuilder')
                    .then(addToast)
                    .then(() => {
                        return window.location.reload();
                    })
                    .catch(Notification.exception);
            });
            reportModal.show();
        }
    });

    initialized = true;
};
