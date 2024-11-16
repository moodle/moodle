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
 * Module to manage report insights actions that are executed using AJAX.
 *
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This module manages prediction actions that require AJAX requests.
 *
 * @module report_insights/actions
 */

import {get_string as getString} from 'core/str';
import Ajax from 'core/ajax';
import Notification from 'core/notification';
import Url from 'core/url';
import ModalEvents from 'core/modal_events';
import ModalSaveCancel from 'core/modal_save_cancel';


/**
 * Executes the provided action.
 *
 * @param  {Array}  predictionids
 * @param  {String} actionname
 * @return {Promise}
 */
const markActionExecuted = (predictionids, actionname) => Ajax.call([
    {
        methodname: 'report_insights_action_executed',
        args: {
            actionname,
            predictionids,
        },
    }
])[0];

const getPredictionTable = (predictionContainers) => {
    for (const el of predictionContainers) {
        if (el.closest('table')) {
            return el.closest('table');
        }
    }

    return null;
};

const executeAction = (predictionIds, predictionContainers, actionName) => {
    markActionExecuted(predictionIds, actionName).then(() => {
        // Remove the selected elements from the list.
        const tableNode = getPredictionTable(predictionContainers);
        predictionContainers.forEach((el) => el.remove());

        if (!tableNode.querySelector('tbody > tr')) {
            const params = {
                contextid: tableNode.closest('div.insight-container').dataset.contextId,
                modelid: tableNode.closest('div.insight-container').dataset.modelId,
            };
            window.location.assign(Url.relativeUrl("report/insights/insights.php", params, false));
        }
        return;
    }).catch(Notification.exception);
};

/**
 * Attach on click handlers for bulk actions.
 *
 * @param {String} rootNode
 * @access public
 */
export const initBulk = (rootNode) => {
    document.addEventListener('click', (e) => {
        const action = e.target.closest(`${rootNode} [data-bulk-actionname]`);
        if (!action) {
            return;
        }

        e.preventDefault();
        const actionName = action.dataset.bulkActionname;
        const actionVisibleName = action.textContent.trim();

        const predictionContainers = Array.from(document.querySelectorAll(
            '.insights-list input[data-togglegroup^="insight-bulk-action-"][data-toggle="target"]:checked',
        )).map((checkbox) => checkbox.closest('tr[data-prediction-id]'));
        const predictionIds = predictionContainers.map((el) => el.dataset.predictionId);

        if (predictionIds.length === 0) {
            // No items selected message.
            return;
        }

        const stringParams = {
            action: actionVisibleName,
            nitems: predictionIds.length,
        };

        ModalSaveCancel.create({
            title: actionVisibleName,
            body: getString('confirmbulkaction', 'report_insights', stringParams),
            buttons: {
                save: getString('confirm'),
            },
            show: true,
        }).then((modal) => {
            modal.getRoot().on(ModalEvents.save, function() {
                // The action is now confirmed, sending an action for it.
                return executeAction(predictionIds, predictionContainers, actionName);
            });

            return modal;
        }).catch(Notification.exception);
    });
};
