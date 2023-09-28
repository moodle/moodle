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
 * A module to handle Share operations of the MoodleNet.
 *
 * @module     core/moodlenet/send_resource
 * @copyright  2023 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      4.2
 */

import Config from 'core/config';
import ModalFactory from 'core/modal_factory';
import {alert as displayAlert, addNotification, exception as displayException} from 'core/notification';
import {get_string as getString} from 'core/str';
import Prefetch from "core/prefetch";
import * as Templates from 'core/templates';
import * as MoodleNetService from 'core/moodlenet/service';
import SendActivityModal from 'core/moodlenet/send_activity_modal';

const TYPE_ACTIVITY = "activity";

let currentModal;
let siteSupportUrl;
let issuerId;
let courseId;
let cmId;
let shareFormat;

/**
 * Handle send to MoodleNet.
 *
 * @param {int} issuerId The OAuth 2 issuer ID.
 * @param {int} cmId The course module ID.
 * @param {int} shareFormat The share format.
 */
const sendToMoodleNet = (issuerId, cmId, shareFormat) => {
    const $modal = currentModal.getModal();
    const modal = $modal[0];
    modal.querySelector('.modal-header').classList.remove('no-border');
    modal.querySelector('.modal-header').classList.add('no-header-text');

    currentModal.setBody(Templates.render('core/moodlenet/send_activity_modal_packaging', {}));
    currentModal.hideFooter();

    MoodleNetService.sendActivity(issuerId, cmId, shareFormat).then(async(data) => {
        const status = data.status;
        const resourceUrl = data.resourceurl;
        return responseFromMoodleNet(status, resourceUrl);
    }).catch(displayException);
};

/**
 * Handle response from MoodleNet.
 *
 * @param {boolean} status Response status. True if successful.
 * @param {String} resourceUrl Resource URL.
 */
const responseFromMoodleNet = (status, resourceUrl = '') => {
    const $modal = currentModal.getModal();
    const modal = $modal[0];
    modal.querySelector('.modal-header').classList.add('no-border');
    currentModal.setBody(Templates.render('core/moodlenet/send_activity_modal_done', {
        success: status,
        sitesupporturl: siteSupportUrl,
    }));

    if (status) {
        currentModal.setFooter(Templates.render('core/moodlenet/send_activity_modal_footer_view', {
            resourceurl: resourceUrl,
        }));
        currentModal.showFooter();
    }
};

/**
 * Handle authorization with MoodleNet server.
 *
 * @param {int} issuerId The OAuth 2 issuer ID.
 * @return {promise}
 */
const handleAuthorization = (issuerId) => {
    const windowsizewidth = 550;
    const windowsizeheight = 550;

    // Check if the user is authorized with MoodleNet or not.
    return MoodleNetService.authorizationCheck(issuerId, courseId).then(async(data) => {
        if (!data.status) {
            // Not yet authorized.
            // Declare moodleNetAuthorize variable, so we can call it later in the callback.
            window.moodleNetAuthorize = (error, errorDescription) => {
                // This will be called by the callback after the authorization is successful.
                if (error == '') {
                    handleAuthorization(issuerId);
                } else if (error !== 'access_denied') {
                    displayAlert(
                        'Authorization error',
                        'Error: ' + error + '<br><br>Error description: ' + errorDescription,
                        'Cancel'
                    );
                }
            };
            // Open the login url of the OAuth 2 issuer for user to login into MoodleNet and authorize.
            return window.open(data.loginurl, 'moodlenet_auth',
                `location=0,status=0,width=${windowsizewidth},height=${windowsizeheight},scrollbars=yes`);
        } else {
            // Already authorized.
            return sendToMoodleNet(issuerId, cmId, shareFormat);
        }
    }).catch(displayException);
};

/**
 * Register events.
 */
const registerEventListeners = () => {
    document.addEventListener('click', e => {
        const shareAction = e.target.closest('[data-action="sendtomoodlenet"]');
        const sendAction = e.target.closest('.moodlenet-action-buttons [data-action="share"]');
        if (shareAction) {
            e.preventDefault();
            const type = shareAction.getAttribute('data-type');
            const shareType = shareAction.getAttribute('data-sharetype');
            const cmId = Config.contextInstanceId;
            if (type == TYPE_ACTIVITY) {
                MoodleNetService.getActivityInformation(cmId).then(async(data) => {
                    if (data.status) {
                        siteSupportUrl = data.supportpageurl;
                        issuerId = data.issuerid;
                        const modalPromise = ModalFactory.create({
                            type: SendActivityModal.TYPE,
                            large: true,
                            templateContext: {
                                'activitytype': data.type,
                                'activityname': data.name,
                                'sharetype': await getString('moodlenet:sharetype' + shareType, 'moodle'),
                                'server': data.server,
                            }
                        });
                        return modalPromise.then(modal => {
                            currentModal = modal;
                            modal.show();
                            return modal;
                        }).catch(displayException);
                    } else {
                        return addNotification({
                            message: data.warnings[0].message,
                            type: 'error'
                        });
                    }
                }).catch(displayException);
            }
        }

        if (sendAction) {
            e.preventDefault();
            courseId = Config.courseId;
            cmId = Config.contextInstanceId;
            shareFormat = 0;
            handleAuthorization(issuerId);
        }
    });
};

/**
 * Initialize.
 */
export const init = () => {
    Prefetch.prefetchTemplates([
        'core/moodlenet/send_activity_modal_base',
        'core/moodlenet/send_activity_modal_packaging',
        'core/moodlenet/send_activity_modal_done',
        'core/moodlenet/send_activity_modal_footer_view',
    ]);
    registerEventListeners();
};
