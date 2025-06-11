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
 * AMD module to enable users to manage their own data requests.
 *
 * @module     tool_dataprivacy/myrequestactions
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';
import Notification from 'core/notification';
import Pending from 'core/pending';
import {getStrings} from 'core/str';

const SELECTORS = {
    CANCEL_REQUEST: '[data-action="cancel"][data-requestid]',
};

/**
 * Initialize module
 */
export const init = () => {
    document.addEventListener('click', event => {
        const triggerElement = event.target.closest(SELECTORS.CANCEL_REQUEST);
        if (triggerElement === null) {
            return;
        }

        event.preventDefault();

        const requiredStrings = [
            {key: 'cancelrequest', component: 'tool_dataprivacy'},
            {key: 'cancelrequestconfirmation', component: 'tool_dataprivacy'},
        ];

        getStrings(requiredStrings).then(([cancelRequest, cancelConfirm]) => {
            return Notification.confirm(cancelRequest, cancelConfirm, cancelRequest, null, () => {
                const pendingPromise = new Pending('tool/dataprivacy:cancelRequest');
                const request = {
                    methodname: 'tool_dataprivacy_cancel_data_request',
                    args: {requestid: triggerElement.dataset.requestid}
                };

                Ajax.call([request])[0].then(response => {
                    if (response.result) {
                        window.location.reload();
                    } else {
                        Notification.addNotification({
                            type: 'error',
                            message: response.warnings[0].message
                        });
                    }
                    return pendingPromise.resolve();
                }).catch(Notification.exception);
            });
        }).catch();
    });
};
