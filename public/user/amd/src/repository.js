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
 * Module to handle AJAX interactions.
 *
 * @module     core_user/repository
 * @copyright  2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Config from 'core/config';
import {call as fetchMany} from 'core/ajax';
import Fetch from 'core/fetch';

const checkUserId = (userid) => {
    if (Number(userid) === 0) {
        return;
    }
    if (Number(userid) === Config.userId) {
        return;
    }
    throw new Error(
        `Invalid user ID: ${userid}. It is only possible to manage preferences for the current user.`,
    );
};

/**
 * Turn the response object into a Proxy object that will log a warning if the saved property is accessed.
 *
 * @param {Object} response
 * @param {Object} preferences The preferences that might be in the response
 * @return {Promise<Proxy>}
 */
const addLegacySavedProperty = (response, preferences) => {
    const debugLogger = {
        get(target, prop, receiver) {
            if (prop === 'then') {
                // To proxy a Promise we have to return null when the then key is requested.
                return null;
            }
            if (prop === 'saved') {
                window.console.warn(
                    'The saved property is deprecated. Please use the response object directly.',
                );

                return preferences
                    .filter((preference) => target.hasOwnProperty(preference.name))
                    .map((preference) => ({
                        name: preference.name,
                        userid: Config.userid,
                    }));
            }
            return Reflect.get(target, prop, receiver);
        },
    };

    return Promise.resolve(new Proxy(response, debugLogger));
};

/**
 * Get single user preference
 *
 * @param {String} name Name of the preference
 * @param {Number} userid User ID (defaults to current user)
 * @return {Promise}
 */
export const getUserPreference = (name, userid = 0) => getUserPreferences(name, userid)
    .then((response) => response[name]);

/**
 * Get multiple user preferences
 *
 * @param {String|null} name Name of the preference (omit if you want to retrieve all)
 * @param {Number} userid User ID (defaults to current user)
 * @return {Promise<object<string, string>>}
 */
export const getUserPreferences = (name = null, userid = 0) => {
    checkUserId(userid);
    const endpoint = ['current', 'preferences'];

    if (name) {
        endpoint.push(name);
    }

    return Fetch.performGet('core_user', endpoint.join('/')).then((response) => response.json());
};

/**
 * Set single user preference
 *
 * @param {String} name Name of the preference
 * @param {String|null} value Value of the preference (omit if you want to remove the current value)
 * @param {Number} userid User ID (defaults to current user)
 * @return {Promise}
 */
export const setUserPreference = (name, value = null, userid = 0) => {
    checkUserId(userid);
    return Fetch.performPost(
        'core_user',
        `current/preferences/${name}`,
        {
            body: {value},
        },
    )
    // Return the result of the fetch call, and also add in the legacy saved property.
    .then((response) => response.json())
    .then((response) => addLegacySavedProperty(response, [{name}]));
};

/**
 * Set multiple user preferences
 *
 * @param {Object[]} preferences Array of preferences containing name/value/userid attributes
 * @return {Promise}
 */
export const setUserPreferences = (preferences) => {
    preferences.forEach((preference) => checkUserId(preference.userid));
    return Fetch.performPost(
        'core_user',
        'current/preferences',
        {
            body: {
                preferences: Object.fromEntries (preferences.map((preference) => ([preference.name, preference.value]))),
            },
        },
    )
    // Return the result of the fetch call, and also add in the legacy saved property.
    .then((response) => response.json())
    .then((response) => addLegacySavedProperty(response, preferences));
};

/**
 * Unenrol the user with the specified user enrolmentid ID.
 *
 * @param {Number} userEnrolmentId
 * @return {Promise}
 */
export const unenrolUser = userEnrolmentId => {
    return fetchMany([{
        methodname: 'core_enrol_unenrol_user_enrolment',
        args: {
            ueid: userEnrolmentId,
        },
    }])[0];
};

/**
 * Submit the user enrolment form with the specified form data.
 *
 * @param {String} formdata
 * @return {Promise}
 */
export const submitUserEnrolmentForm = formdata => {
    return fetchMany([{
        methodname: 'core_enrol_submit_user_enrolment_form',
        args: {
            formdata,
        },
    }])[0];
};

export const createNotesForUsers = notes => {
    return fetchMany([{
        methodname: 'core_notes_create_notes',
        args: {
            notes
        }
    }])[0];
};

export const sendMessagesToUsers = messages => {
    return fetchMany([{
        methodname: 'core_message_send_instant_messages',
        args: {messages}
    }])[0];
};
