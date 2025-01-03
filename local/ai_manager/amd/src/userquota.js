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
 * Module handling the form submission of the statistics tables of local_ai_manager.
 *
 * @module     local_ai_manager/userquota
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {call as fetchMany} from 'core/ajax';
import {getStrings} from 'core/str';
import Templates from 'core/templates';

const constants = {
    MAXUSAGE_UNLIMITED: 999999
};

const queryCountStrings = {
    chat: 'chat requests',
    chatShortened: 'chat',
    feedback: 'feedback requests',
    feedbackShortened: 'feedback',
    imggen: 'image generation generation requests',
    imggenShortened: 'image generation generation',
    singleprompt: 'text requests',
    singlepromptShortened: 'text',
    translate: 'translation requests',
    translateShortened: 'translation',
    tts: 'audio requests',
    ttsShortened: 'audio',
    itt: 'image analyse requests',
    ittShortened: 'image analyse'
};

const fetchUserquotaData = () => fetchMany([{
    methodname: 'local_ai_manager_get_user_quota',
    args: {},
}])[0];

/**
 * Renders the current user usage information into the element identified by the given selector.
 *
 * @param {string} selector the id of the element to insert the infobox
 * @param {string[]} purposes the purposes to show user quota for
 */
export const renderUserQuota = async(selector, purposes) => {
    await localizeQueryCountTexts();

    const targetElement = document.querySelector(selector);
    const userquotaData = await fetchUserquotaData();
    const purposeInfo = [];

    purposes.forEach(purpose => {
        purposeInfo.push(
            {
                purpose,
                'currentusage': userquotaData.usage[purpose].currentusage,
                maxusage: userquotaData.usage[purpose].maxusage,
                'querycounttext': queryCountStrings[purpose + 'Shortened'],
                showmaxusage: userquotaData.usage[purpose].maxusage !== constants.MAXUSAGE_UNLIMITED,
                limitreached: userquotaData.usage[purpose].currentusage === userquotaData.usage[purpose].maxusage,
                islastelement: false
            });
    });
    purposeInfo[purposeInfo.length - 1].islastelement = true;
    purposeInfo[purposeInfo.length - 1].querycounttext = queryCountStrings[purposeInfo[purposeInfo.length - 1].purpose];

    const userquotaContentTemplateContext = {
        purposes: purposeInfo,
        period: userquotaData.period,
        unlimited: userquotaData.role === 'role_unlimited'
    };
    const {html, js} = await Templates.renderForPromise('local_ai_manager/userquota', userquotaContentTemplateContext);
    Templates.appendNodeContents(targetElement, html, js);
};

const localizeQueryCountTexts = async() => {
    const stringsToFetch = [];
    Object.keys(queryCountStrings).filter(key => !key.endsWith('Shortened')).forEach((key) => {
        stringsToFetch.push({key: 'requestcount', component: 'aipurpose_' + key});
        stringsToFetch.push({key: 'requestcount_shortened', component: 'aipurpose_' + key});
    });
    const strings = await getStrings(stringsToFetch);
    let i = 0;
    for (const key in queryCountStrings) {
        queryCountStrings[key] = strings[i];
        i++;
    }
};
