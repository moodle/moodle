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
 * Validate Safe Exam Browser access keys.
 *
 * @module     quizaccess_seb/validate_quiz_access
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';
import Config from 'core/config';
import Notification from "core/notification";
import * as View from 'quizaccess_seb/view';

// SafeExamBrowser object will be automatically initialized if using the SafeExamBrowser application.
window.SafeExamBrowser = window.SafeExamBrowser || null;

/**
 * Once the keys are fetched, action checking access.
 *
 * @param {init} cmid Value of course module id of the quiz.
 * @param {boolean} autoreconfigure Value of Moodle setting: quizaccess_seb/autoreconfigureseb.
 */
const safeExamBrowserKeysUpdated = (cmid, autoreconfigure = false) => {
    // Action opening up the quiz.
    isQuizAccessValid(cmid).then((response) => {
        // Show the alert for an extra second to allow user to see it.
        setTimeout(View.clearLoadingAlert, 1000);

        if (response.configkey && response.browserexamkey) {
            View.allowAccess();
        } else {
            // If autoreconfigureseb is enabled, attempt to reconfigure page with quiz settings.
            if (autoreconfigure === true && response.configkey === false) {
                reconfigureSafeExamBrowser(cmid);
            }
            setTimeout(View.showValidationFailedModal, 1000);
        }

        return response;
    }).catch(err => {
        Notification.exception(err);
    });
};

/**
 * Validate keys in Moodle backend.
 *
 * @param {init} cmid Value of course module id of the quiz.
 * @return {Promise}
 */
const isQuizAccessValid = (cmid) => {
    const request = {
        methodname: 'quizaccess_seb_validate_quiz_keys',
        args: {
            cmid: cmid,
            url: window.location.href,
            configkey: window.SafeExamBrowser.security.configKey,
            browserexamkey: window.SafeExamBrowser.security.browserExamKey
        },
    };

    return Ajax.call([request])[0];
};

/**
 * Check if the key is not yet set.
 *
 * @param {string} key config key or browser exam key.
 * @return {boolean}
 */
const isKeyEmpty = (key) => {
    // If the SafeExamBrowser object is defined, the default 'empty' value of the configKey and browserExamKey is ':'.
    return key === ":";
};

/**
 * Reload Safe Exam Browser with current quiz configuration.
 *
 * @param {init} cmid Value of course module id of the quiz.
 */
const reconfigureSafeExamBrowser = (cmid) => {
    const domain = Config.wwwroot.replace(/^http/i, 'seb');
    const redirecturl = domain + '/mod/quiz/accessrule/seb/config.php?cmid=' + cmid;
    document.location.replace(redirecturl);
};

/**
 * Initialize the process of fetching the keys.
 *
 * @param {init} cmid Value of course module id of the quiz.
 * @param {boolean} autoreconfigure Value of Moodle setting: quizaccess_seb/autoreconfigureseb.
 */
export const init = async(cmid, autoreconfigure = false) => {
    // If the SafeExamBrowser object is instantiated, try and use it to fetch the access keys.
    if (window.SafeExamBrowser !== null) {
        await View.addLoadingAlert();
        // If the SEB keys are already set, we can call our callback directly.

        if (!isKeyEmpty(window.SafeExamBrowser.security.configKey) || !isKeyEmpty(window.SafeExamBrowser.security.browserExamKey)) {
            safeExamBrowserKeysUpdated(cmid, autoreconfigure);
        } else {
            window.SafeExamBrowser.security.updateKeys(safeExamBrowserKeysUpdated);
        }
    }
};
